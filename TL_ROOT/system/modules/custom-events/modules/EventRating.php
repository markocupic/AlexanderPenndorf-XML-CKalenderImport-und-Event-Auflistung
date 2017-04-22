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
        // $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['ratings'] was set in config.php
        $arrFormInputStarRatings = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['ratings'];
        $averageRatings = array();
        $userRatings = array();
        foreach ($arrFormInputStarRatings as $fieldname)
        {
            $averageRatings[$fieldname] = $this->getAverageRating($fieldname, 2);
            if (FE_USER_LOGGED_IN)
            {
                $userRatings[$fieldname] = $this->getUserRating($fieldname);
            }
        }
        $this->Template->starRatings = $averageRatings;


        // Other numeric values
        // $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['vertraege'] was set in config.php
        $arrFormInputVertraege = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['vertraege'];
        $numericValues = array();
        foreach ($arrFormInputVertraege as $fieldname)
        {
            $numericValues[$fieldname] = $this->getAverageRating($fieldname, 2);
        }
        $this->Template->vertraege = $numericValues;


        // Other numeric values
        // $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['hardware'] was set in config.php
        $arrFormInputHardware = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['hardware'];
        $numericValues = array();
        foreach ($arrFormInputHardware as $fieldname)
        {
            $numericValues[$fieldname] = $this->getAverageRating($fieldname, 2);
        }
        $this->Template->hardware = $numericValues;


        // Weitere Info
        $this->Template->weitereInfo = $this->getWeitereInfo();


        // Logged in user
        $this->Template->objEvent = $this->objEvent;
        if (FE_USER_LOGGED_IN)
        {
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
     * @return array
     */
    protected function getWeitereInfo()
    {
        $arrText = array();
        $objEventsRating = \CalendarEventsRatingModel::findByPid($this->objEvent->id);
        if($objEventsRating !== null)
        {
            while($objEventsRating->next())
            {
                $arrText[] = array(
                    'authorId' => $objEventsRating->memberId,
                    'author' => \MemberModel::findByPk($objEventsRating->memberId)->firstname . ' ' . \MemberModel::findByPk($objEventsRating->memberId)->lastname,
                    'text' => $objEventsRating->weitereInfo,
                    'tstamp' => $objEventsRating->tstamp
                );
            }
        }


        return $arrText;
    }

    /**
     * @return int
     */
    protected function countRatings()
    {
        return \CalendarEventsRatingModel::countRatings($this->objEvent->id);
    }


}
