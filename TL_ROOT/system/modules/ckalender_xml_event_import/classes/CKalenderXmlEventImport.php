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
     * stored in tl_calendar.ckal_url
     */
    protected $ckal_url = null;


    /**
     * @var null
     */
    protected $objTmpFile = null;


    /**
     * @var null
     * tl_calendar.id
     */
    protected $calendarId = null;


    /**
     * Trigger the eventImport from ckalender.de in the backend
     * onSubmitCallback for tl_calendar
     * @param \DataContainer $dc
     * @throws \Exception
     */
    public function backendTrigger(\DataContainer $dc)
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
     * Trigger the eventImport from ckalender.de in the frontend
     * GeneratePage Hook
     */
    public function frontendTrigger()
    {
        if (TL_MODE != 'FE')
        {
            return;
        }

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
        // Prepare url
        $url = html_entity_decode($this->ckal_url);
        $url = $this->replaceWildcardsInUrl($url);

        $this->objTmpFile = $this->downloadURLToTempFile($url);
        $strContent = $this->objTmpFile->getContent();

        // Create SimpleXML object from string
        libxml_use_internal_errors(true);

        $xml = simplexml_load_string($strContent, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        // XML error handling
        if ($xml === false)
        {
            $errors = libxml_get_errors();
            foreach ($errors as $error)
            {
                echo $this->displayXmlError($error, $xml);
            }

            libxml_clear_errors();
            $this->log('Cannot create Simple XML Object from string!', __METHOD__, TL_ERROR);
            throw new \Exception('Cannot create Simple XML Object from string!');
        }

        // Hide all Events of the selected calendar
        \Database::getInstance()->prepare('UPDATE tl_calendar_events SET published=? WHERE pid=?')->execute('', $this->calendarId);

        $items = 0;
        foreach ($xml->children() as $child)
        {
            // Set the insert array
            $set = $this->getDatarecordFromXML($child, $xml, $strContent);

            // Call CKalenderXMLEventImportBeforeUpdateHook
            if (isset($GLOBALS['TL_HOOKS']['CKalenderXMLEventImportBeforeUpdateHook']) && is_array($GLOBALS['TL_HOOKS']['CKalenderXMLEventImportBeforeUpdateHook']))
            {
                foreach ($GLOBALS['TL_HOOKS']['CKalenderXMLEventImportBeforeUpdateHook'] as $callback)
                {
                    $this->import($callback[0]);
                    $set = $this->{$callback[0]}->{$callback[1]}();
                }
            }

            if (isset($set) && is_array($set) && $set['uuid'] > 0)
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

        // Delete the temporary XML-file from the server
        $objFile = new \File($this->objTmpFile->path, false);
        $objFile->delete();
    }


    /**
     * @param $error
     * @param $xml
     * @return string
     */
    protected function displayXmlError($error, $xml)
    {
        $return = $xml[$error->line - 1] . "\n";
        $return .= str_repeat('-', $error->column) . "^\n";

        switch ($error->level)
        {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }

        $return .= trim($error->message) . "\n  Line: $error->line" . "\n  Column: $error->column";

        if ($error->file)
        {
            $return .= "\n  File: $error->file";
        }

        return "$return\n\n--------------------------------------------\n\n";
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
        $set['author'] = $this->getAuthor((int)$child->ID);
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
     * Events that are created in contao habe
     * @return int
     */
    public static function generateUuid()
    {

        $objDb = \Database::getInstance();
        if (!$objDb->fieldExists('uuid', 'tl_calendar_events'))
        {
            return null;
        }

        // Generate uuid
        $uuid = 1000000000;
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
     * @param $url
     * @return mixed
     */
    protected function replaceWildcardsInUrl($strUrl)
    {
        $pattern = '/\#\#\#([+|-])(.*\d)days\#\#\#/iU';
        if (preg_match($pattern, $strUrl))
        {
            $strUrl = preg_replace_callback($pattern, function ($match)
            {
                //date('d.m.Y', strtotime("+30 days"));
                return \Date::parse('d.m.Y', strtotime($match[1] . $match[2] . ' days'));
            }, $strUrl);
        }

        return $strUrl;
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
     * Get the event-author
     * @param int $uuid
     * @return int
     */
    protected function getAuthor($uuid = 0)
    {
        // Do not overwrite author, if it is already set
        $objEvent = \CalendarEventsModel::findByUuid($uuid);
        if ($objEvent !== null)
        {
            if ($objEvent->author > 0)
            {
                return $objEvent->author;
            }
        }

        // Use the id of the logged in backend user
        if (TL_MODE == 'BE')
        {
            $objUser = \BackendUser::getInstance();
            return $objUser->id;
        }

        // Set backend-administrator as author
        $objUser = \Database::getInstance()->prepare('SELECT * FROM tl_user WHERE admin=?')->limit(1)->execute('1');
        if ($objUser->numRows)
        {
            return $objUser->id;
        }

        // default
        return 0;
    }
}
