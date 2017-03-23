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
class EventRating extends \Module
{

    /**
     * template
     * @var string
     */
    protected $strTemplate = 'mod_event_rating';

    /**
     * @var null
     */
    protected $objUser = null;

    /**
     * @var null
     */
    protected $objEvent = null;


    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');
            if (version_compare(VERSION . '.' . BUILD, '4.0.0', '<'))
            {
                $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['event_rating'][0]) . ' ###';
            }
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        // Overwrite default template
        if ($this->eventRatingTpl != '')
        {
            $this->strTemplate = $this->eventRatingTpl;
        }


        // Show to logged in users only!
        if (!FE_USER_LOGGED_IN)
        {
            //return '';
        }

        $this->objUser = \FrontendUser::getInstance();

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


        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {

        $this->Template->countRatings = $this->countRatings();

        // Star-Rating Values
        $arrFormInputStarRatings = array('ratingTechnikImShop', 'ratingAufbauAbbauVorOrt', 'ratingKundenfrequenzImShop', 'ratingZustandShop', 'ratingUnterstuetzungImShop', 'ratingWetter');
        $averageRatings = array();
        $userRatings = array();
        foreach($arrFormInputStarRatings as $fieldname)
        {
            $averageRatings[$fieldname] = $this->getAverageRating($fieldname, 2);
            if(FE_USER_LOGGED_IN)
            {
                $userRatings[$fieldname] = $this->getUserRating($fieldname);
            }
        }
        $this->Template->starRatings = $averageRatings;

        // Other numeric values
        $arrFormInputNumerics = array('anzahlDurchgefuehrteBeratungen', 'anzahlAbgeschlosseneVertraege', 'festnetz', 'kreditMobile');
        $numericValues = array();
        foreach($arrFormInputNumerics as $fieldname)
        {
            $numericValues[$fieldname] = $this->getAverageRating($fieldname, 2);
        }
        $this->Template->numericValues = $numericValues;



        // Comments
        $comments = array();
        $fieldname = 'kommentarZumEinsatz';
        $objRatings = \CalendarEventsRatingModel::findByPid($this->objEvent->id);
        if($objRatings !== null)
        {
            while($objRatings->next())
            {
                if($objRatings->{$fieldname} != '')
                {
                    $comments[] = nl2br($objRatings->{$fieldname});
                }
            }
        }
        $this->Template->comments = $comments;



        // Logged in user
        $this->Template->objEvent = $this->objEvent;
        if(FE_USER_LOGGED_IN) {
            $this->Template->objUser = $this->objUser;
        }



        // Labels
        \Controller::loadLanguageFile('tl_calendar_events_rating');
        $this->Template->labels = $GLOBALS['TL_LANG']['tl_calendar_events_rating'];

    }


    /**
     * @return mixed
     */
    protected function getUserRating($fieldname)
    {
        return \CalendarEventsRatingModel::getUserRating($fieldname, $this->objUser->id, $this->objEvent->id);
    }

    /**
     * @return mixed|null
     */
    protected function getAverageRating($fieldname, $precision = 2)
    {
        return \CalendarEventsRatingModel::getAverageRating($fieldname, $this->objEvent->id, $precision = 2);
    }

    /**
     * @return int
     */
    protected function countRatings()
    {
        return \CalendarEventsRatingModel::countRatings($this->objEvent->id);
    }


}
