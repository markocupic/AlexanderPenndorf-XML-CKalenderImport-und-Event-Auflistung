<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

namespace Markocupic\Customevents;

/**
 * EventGallery
 *
 * @author Marko Cupic <m.cupic@gmx.ch>
 */
class EventRatingForm extends \Module
{

    /**
     * template
     * @var string
     */
    protected $strTemplate = 'mod_event_rating_form';

    /**
     * @var null
     */
    protected $objUser = null;

    /**
     * @var null
     */
    protected $objEvent = null;


    /**
     * @var bool
     */
    protected $blnError = false;


    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['event_rating_form'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // Overwrite default template
        if ($this->eventRatingFormTpl != '')
        {
            $this->strTemplate = $this->eventRatingFormTpl;
        }

        // Insert Rating
        if (FE_USER_LOGGED_IN && $_POST['FORM_SUBMIT'] == 'formEventRating' && isset($_GET['auto_item']) && $_GET['events'] != '')
        {

            $set = array();
            $arrFormInputStarRatings = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['ratings'];
            $arrFormInputVertraege = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['vertraege'];
            $arrFormInputHardware = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['hardware'];


            foreach ($arrFormInputStarRatings as $fieldname)
            {
                if (!is_numeric(\Input::post($fieldname)) || \Input::post($fieldname) < 1 || \Input::post($fieldname) > 6)
                {
                    $this->blnError = true;
                }
                else
                {
                    $set[$fieldname] = (int)\Input::post($fieldname);
                }
            }

            foreach ($arrFormInputVertraege as $fieldname)
            {
                if (!is_numeric(\Input::post($fieldname)) && (int)\Input::post($fieldname) != 0)
                {
                    $this->blnError = true;
                }
                else
                {
                    $set[$fieldname] = (int)\Input::post($fieldname);
                }
            }

            foreach ($arrFormInputHardware as $fieldname)
            {
                if (!is_numeric(\Input::post($fieldname)) && (int)\Input::post($fieldname) != 0)
                {
                    $this->blnError = true;
                }
                else
                {
                    $set[$fieldname] = (int)\Input::post($fieldname);
                }
            }

            $set['weitereInfo'] = \Input::post('weitereInfo');
            $set['notifyUsers'] = \Input::post('notifyUsers');


            if ($this->blnError !== true)
            {
                $objEvents = \CalendarEventsModel::findByIdOrAlias($_GET['events']);
                if ($objEvents !== null)
                {
                    $objUser = \FrontendUser::getInstance();
                    $objRating = $this->Database->prepare('SELECT * FROM tl_calendar_events_rating WHERE memberId=? AND pid=?')->execute($objUser->id, $objEvents->id);
                    if (!$objRating->numRows)
                    {
                        $set['memberId'] = $objUser->id;
                        $set['pid'] = $objEvents->id;
                        $set['tstamp'] = time();
                        $this->Database->prepare('INSERT INTO tl_calendar_events_rating %s')->set($set)->execute();
                        $this->notifyUsers($set);
                        $this->reload();
                    }
                }
            }

        }

        // Show Form to Logged in users only!
        if (!FE_USER_LOGGED_IN)
        {
            return '';
        }

        $this->objUser = \FrontendUser::getInstance();

        // Dont make form available until event has stopped
        $objEvent = \CalendarEventsModel::findByIdOrAlias($_GET['events']);
        if ($objEvent !== null)
        {
            $this->objEvent = $objEvent;
        }
        else
        {
            return '';
        }

        // Do not show rating html, if event hasn't finished
        if ($this->objEvent->endDate > time())
        {
            return '';
        }

        // Allow only 1 rating per event
        if (\CalendarEventsRatingModel::countRatings($this->objEvent->id) > 0)
        {
            return '';
        }


        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->loadLanguageFile('tl_calendar_events_rating');
        $this->Template->labels = $GLOBALS['TL_LANG']['tl_calendar_events_rating'];
    }


    /**
     * @param $set
     */
    protected function notifyUsers($set)
    {
        $strRecipients = str_replace(' ', '', $set['notifyUsers']);
        $strRecipients = trim($strRecipients, ',');

        if ($strRecipients == '')
        {
            return;
        }
        // Sort out duplicate E-Mail-Adresses
        $arrRecipients = explode(',', $strRecipients);
        $arrRecipients = array_unique($arrRecipients);
        if (count($arrRecipients) < 1)
        {
            return;
        }

        $strRecipients = implode(',', $arrRecipients);
        while (strpos($strRecipients, ',,') !== false)
        {
            $strRecipients = str_replace(',,', ',', $strRecipients);
        }


        $objEvent = \CalendarEventsModel::findByPk($set['pid']);
        if ($objEvent === null)
        {
            new \Exception('Event mit id ' . $set['pid'] . ' wurde nicht gefunden.');
        }


        $objTemplate = new \FrontendTemplate('rating_email_notification');

        $objUser = \FrontendUser::getInstance();
        if ($objUser !== null)
        {
            $objTemplate->logged_in_user_firstname = $objUser->firstname;
            $objTemplate->logged_in_user_lastname = $objUser->lastname;
        }

        $objTemplate->eventname = $objEvent->title;

        // The url to the newsreader
        $objTemplate->href = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];

        // Load language for the labels
        $this->loadLanguageFile('tl_calendar_events_rating');

        // Add ratings to template
        $arrRatings = array();
        foreach ($GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['ratings'] as $key)
        {
            $arrRatings[] = array(
                'label' => $GLOBALS['TL_LANG']['tl_calendar_events_rating'][$key][0],
                'value' => $set[$key]
            );
        }
        $objTemplate->ratings = $arrRatings;

        // Add vertraege to template
        $arrVertraege = array();
        foreach ($GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['vertraege'] as $key)
        {
            $arrVertraege[] = array(
                'label' => $GLOBALS['TL_LANG']['tl_calendar_events_rating'][$key][0],
                'value' => $set[$key]
            );
        }
        $objTemplate->vertraege = $arrVertraege;

        // Add hardware to template
        $arrHardware = array();
        foreach ($GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['hardware'] as $key)
        {
            $arrVertraege[] = array(
                'label' => $GLOBALS['TL_LANG']['tl_calendar_events_rating'][$key][0],
                'value' => $set[$key]
            );
        }
        $objTemplate->hardware = $arrVertraege;

        // Weitere Info
        $objTemplate->weitereInfo = $set['weitereInfo'] != '' ? $set['weitereInfo'] : null;


        // Email
        $email = new \Email();
        $email->subject = 'Neues Rating fuer Event: ' . $objEvent->title;
        $email->text = $objTemplate->parse();
        $email->sendTo($strRecipients);
    }

}
