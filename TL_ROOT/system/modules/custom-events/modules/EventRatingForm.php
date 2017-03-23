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
            $arrFormInputStarRatings = array('ratingTechnikImShop', 'ratingAufbauAbbauVorOrt', 'ratingKundenfrequenzImShop', 'ratingZustandShop', 'ratingUnterstuetzungImShop', 'ratingWetter');
            $arrFormInputNumerics = array('anzahlDurchgefuehrteBeratungen', 'anzahlAbgeschlosseneVertraege', 'festnetz', 'kreditMobile', 'kommentarZumEinsatz');
            $arrFormInputText = array('kommentarZumEinsatz');
            foreach($arrFormInputStarRatings as $fieldname)
            {
                if(!is_numeric(\Input::post($fieldname)) || \Input::post($fieldname) < 1 || \Input::post($fieldname) > 6)
                {
                    $this->blnError = true;
                }else{
                    $set[$fieldname] = (int) \Input::post($fieldname);
                }
            }

            foreach($arrFormInputNumerics as $fieldname)
            {
                if(!is_numeric(\Input::post($fieldname)) && (int) \Input::post($fieldname) != 0)
                {
                    $this->blnError = true;
                }
                else{
                    $set[$fieldname] = (int) \Input::post($fieldname);
                }
            }

            $set['kommentarZumEinsatz'] = \Input::post('kommentarZumEinsatz');

            if($this->blnError !== true)
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

    }

}
