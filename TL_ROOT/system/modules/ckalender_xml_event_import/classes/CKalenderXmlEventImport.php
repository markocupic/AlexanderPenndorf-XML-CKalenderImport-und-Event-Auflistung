<?php
/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

namespace Markocupic;


class CKalenderXmlEventImport extends \System
{
    /**
     * @var string
     */
    protected $ckal_url = null;

    /**
     * @var null
     */
    protected $tmpFile = null;

    /**
     * @var null
     */
    protected $calendarId = null;

    /**
     * onSubmitCallback for tl_calendar
     * Load events from ckalender
     * @param \DataContainer $dc
     * @throws \Exception
     */
    public function updateCalendarsFromXMLOnSubmit(\DataContainer $dc)
    {
        $objCalendar = \Database::getInstance()->prepare('SELECT * FROM tl_calendar WHERE id=?')->execute($dc->id);
        if ($objCalendar->numRows)
        {
            if ($objCalendar->ckal_source && $objCalendar->ckal_url != '')
            {
                $oCalendar = \CalendarModel::findByPk($dc->id);
                $oCalendar->ckal_last_reload = time();
                $oCalendar->save();
                $this->ckal_url = $oCalendar->ckal_url;
                $this->calendarId = $oCalendar->id;

                // Launch import
                $this->importXmlFromUrl();
            }
        }
    }


    /**
     * Generate page Hook
     */
    public function updateCalendarsFromXML()
    {
        $objCalendar = \CalendarModel::findAll();
        if ($objCalendar !== null)
        {
            while ($objCalendar->next())
            {
                if ($objCalendar->ckal_source && $objCalendar->ckal_url != '')
                {

                    if (($objCalendar->ckal_last_reload + $objCalendar->ckal_cache) < time() || $objCalendar->ckal_cache < 1)
                    {
                        $objCalendar->ckal_last_reload = time();
                        $objCalendar->save();
                        $this->ckal_url = $objCalendar->ckal_url;
                        $this->calendarId = $objCalendar->id;

                        // Launch import
                        $this->importXmlFromUrl();
                        if ($objCalendar->ckal_cache > 30 && TL_MODE == 'FE')
                        {
                            \Controller::reload();
                        }
                    }
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    protected function importXmlFromUrl()
    {
        $url = html_entity_decode($this->ckal_url);

        $this->tmpFile = $this->downloadURLToTempFile($url);
        $strContent = $this->tmpFile->getContent();

        // Create SimpleXML object from string
        $xml = simplexml_load_string($strContent, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        //$xml = simplexml_load_string($strContent, 'SimpleXMLElement');

        if (!$xml)
        {
            $this->log('Cannot create Simple XML Object from string!', __METHOD__, TL_ERROR);
            throw new \Exception('Cannot create Simple XML Object from string!');
        }

        // Hide all Events of the selected calendar
        \Database::getInstance()->prepare('UPDATE tl_calendar_events SET published=? WHERE pid=?')->execute('', $this->calendarId);

        $items = 0;
        foreach ($xml->children() as $child)
        {
            $set = $this->getDatarecordFromXML($child, $xml, $strContent);
            if ($set['uuid'] > 0)
            {
                $items++;
                $set['pid'] = $this->calendarId;
                $set['published'] = '1';
                $objEvent = \Database::getInstance()->prepare('SELECT * FROM tl_calendar_events WHERE uuid=? LIMIT 0,1')->execute($set['uuid']);
                if ($objEvent->numRows)
                {
                    \Database::getInstance()->prepare('UPDATE tl_calendar_events %s WHERE id=?')->set($set)->execute($objEvent->id);
                }
                else
                {
                    \Database::getInstance()->prepare('INSERT INTO tl_calendar_events %s')->set($set)->execute();
                }
            }
        }
        if ($items > 0)
        {
            $this->log('Reloaded ' . $items . ' Events from ckalender to tl_calendar with ID: ' . $this->calendarId, __METHOD__, TL_GENERAL);
            if (TL_MODE == 'FE')
            {
                \Controller::reload();
            }
        }
        else
        {
            $this->log('Reloaded ' . $items . ' Events from ckalender to tl_calendar with ID: ' . $this->calendarId . '. Check for proper XML-handling', __METHOD__, TL_ERROR);
        }
    }

    /**
     * @param $child
     * @param $xml
     * @param $strContent
     * @return array
     */
    protected function getDatarecordFromXML($child, $xml, $strContent)
    {
        $startDate = $this->getTimestamp($child->Von, 'd.m.Y');
        $endDate = $this->getTimestamp($child->Bis, 'd.m.Y');

        $set = array();
        $set['uuid'] = (int)$child->ID;
        $set['title'] = (string)$child->Titel;
        $set['alias'] = $this->generateAlias($child->Titel, $child->ID);
        $set['location'] = (string)$child->Ort;
        $set['location'] = (string)$child->Ort;
        $set['startDate'] = (int)$startDate;
        $set['endDate'] = (int)$endDate;
        $set['startTime'] = (int)$startDate;
        $set['endTime'] = (int)$endDate;
        $set['addTime'] = '';
        $set['source'] = 'default';
        $set['author'] = 0;
        $set['tstamp'] = time();
        $set['notiz'] = $this->getCorrectStringValue($child->Notiz);
        $set['verantwortlich'] = $this->getCorrectStringValue($child->Verantwortlich);
        $set['benutzergruppe'] = $this->getCorrectStringValue($child->Benutzergruppe);
        $set['text'] = $this->getCorrectStringValue($child->Text);


        if ($child->Zeitangabe != '')
        {
            $arrTime = explode('-', $child->Zeitangabe);
            if (isset($arrTime[0]))
            {
                if ($arrTime[0] > 0 && $arrTime[0] <= 24)
                {
                    $set['startTime'] = (int)($startDate + $arrTime[0] * 3600);
                    $set['addTime'] = '1';
                }
            }
            if (isset($arrTime[1]))
            {
                if ($arrTime[1] > 0 && $arrTime[1] <= 24)
                {
                    $set['endTime'] = (int)($endDate + $arrTime[1] * 3600);
                    $set['addTime'] = '1';
                }
            }
        }
        if ($set['uuid'] > 0)
        {
            return $set;

        }
        return array();

    }

    /**
     * @param $strDate
     * @param string $format
     * @return mixed|null
     */
    protected function getTimestamp($strDate, $format = 'd.m.Y')
    {
        $dateobj = new \Date((string)$strDate, $format);
        return $dateobj->timestamp;
    }

    /**
     * @param string $title
     * @param $uuid
     * @return string
     */
    protected function generateAlias($title = '', $uuid)
    {
        $alias = (standardize($title) != '') ? standardize($title) . '-' : '';
        return $alias . $uuid;
    }

    /**
     * @param $url
     * @return \File
     * @throws \Exception
     */
    protected function downloadURLToTempFile($url)
    {
        $url = html_entity_decode($url);
        if ($this->isCurlInstalled())
        {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if (preg_match("/^https/", $url))
            {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $content = curl_exec($ch);
            curl_close($ch);
        }
        else
        {
            $this->log('The CURL extension is not installed!', __METHOD__, TL_ERROR);
            throw new \Exception('The CURL extension is not installed!');
            $content = file_get_contents($url);
        }
        $filename = md5(time());
        $objFile = new \File('system/tmp/' . $filename);
        $objFile->write($content);
        $objFile->close();
        return $objFile;
    }

    /**
     * @return bool
     */
    private function isCurlInstalled()
    {
        if (in_array('curl', get_loaded_extensions()))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @param $value
     * @return string
     */
    private function getCorrectStringValue($value)
    {
        if (!isset($value))
        {
            return '';
        }
        elseif ($value != '')
        {
            return (string)$value;
        }
        else
        {
            return '';
        }
    }

    /**
     * @return int
     */
    public static function generateUuid()
    {
        $arrFields = \Database::getInstance()->listFields('tl_calendar_events');
        if(!in_array('uuid', $arrFields))
        {
            return null;
        }

        // Generate uuid
        $uuid = 9000000000;
        $skip = false;
        do
        {
            $uuid++;
            $objCal = \CalendarEventsModel::findByUuid($uuid);
            if ($objCal === null)
            {
                $skip = true;
            }
        } while ($skip !== true);
        return $uuid;
    }
}