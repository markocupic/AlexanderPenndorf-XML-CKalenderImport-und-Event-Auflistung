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
        if (FE_USER_LOGGED_IN && $_POST['FORM_SUBMIT'] == 'formEventRating' && is_numeric($_POST['rating']) && $_POST['rating'] > 0 && $_POST['rating'] < 6 && isset($_GET['auto_item']) && $_GET['events'] != '')
        {
            $objEvents = \CalendarEventsModel::findByIdOrAlias($_GET['events']);
            if ($objEvents !== null)
            {
                $objUser = \FrontendUser::getInstance();
                $objRating = $this->Database->prepare('SELECT * FROM tl_calendar_events_rating WHERE memberId=? AND pid=?')->execute($objUser->id, $objEvents->id);
                if (!$objRating->numRows)
                {
                    $set = array(
                        'memberId' => $objUser->id,
                        'pid'      => $objEvents->id,
                        'tstamp'   => time(),
                        'rating'   => (int)$_POST['rating'],
                    );
                    $this->Database->prepare('INSERT INTO tl_calendar_events_rating %s')->set($set)->execute();
                    $this->reload();
                }
            }
        }

        // Show Form to Logged in users only!
        if (!FE_USER_LOGGED_IN)
        {
            return '';
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
        $this->Template->userRating = $this->getUserRating();
        $this->Template->objEvent = $this->objEvent;
        $this->Template->objUser = $this->objUser;
        $this->Template->averageRating = $this->getAverageRating(2);
        $this->Template->countRatings = $this->countRatings();
    }

    /**
     * @return mixed
     */
    protected function getUserRating()
    {
        return \CalendarEventsRatingModel::getUserRating($this->objUser->id, $this->objEvent->id);
    }

    /**
     * @return mixed|null
     */
    protected function getAverageRating($precision = 2)
    {
        return \CalendarEventsRatingModel::getAverageRating($this->objEvent->id, $precision = 2);
    }

    /**
     * @return int
     */
    protected function countRatings()
    {
        return \CalendarEventsRatingModel::countRatings($this->objEvent->id);
    }
}
