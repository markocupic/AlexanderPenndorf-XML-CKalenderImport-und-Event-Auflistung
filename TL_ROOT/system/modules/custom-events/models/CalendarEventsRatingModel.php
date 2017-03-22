<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

namespace Contao;


/**
 * Class CalendarEventsRatingModel
 * @package Contao
 */
class CalendarEventsRatingModel extends \Model
{

    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_calendar_events_rating';


    /**
     * @param $userId
     * @param $eventId
     * @return mixed|null
     */
    public static function getUserRating($fieldname, $userId, $eventId)
    {
        $objRating = \Database::getInstance()->prepare('SELECT * FROM tl_calendar_events_rating WHERE memberId=? AND pid=? LIMIT 0,1')->execute($userId, $eventId);
        if ($objRating->numRows)
        {
            return $objRating->{$fieldname};
        }
        return null;
    }

    /**
     * @param $eventId
     * @param int $precision
     * @return float
     */
    public static function getAverageRating($fieldname, $eventId, $precision = 2)
    {
        $objRating = \Database::getInstance()->prepare('SELECT AVG(' . $fieldname . ') AS averageRating FROM tl_calendar_events_rating WHERE tl_calendar_events_rating.pid=? AND tl_calendar_events_rating.memberId IN (SELECT id FROM tl_member)')->execute($eventId);
        return round($objRating->averageRating, $precision);
    }

    /**
     * @param $eventId
     * @return int
     */
    public static function countRatings($eventId)
    {
        $objRating = \Database::getInstance()->prepare('SELECT id FROM tl_calendar_events_rating WHERE tl_calendar_events_rating.pid=? AND tl_calendar_events_rating.memberId IN (SELECT id FROM tl_member)')->execute($eventId);
        return $objRating->numRows;
    }

    /**
     * @param $eventId
     * @param $userId
     * @return int
     */
    public static function countRatingsFromUser($eventId, $userId)
    {
        $objRating = \Database::getInstance()->prepare('SELECT id FROM tl_calendar_events_rating WHERE tl_calendar_events_rating.pid=? AND tl_calendar_events_rating.memberId=? AND tl_calendar_events_rating.memberId IN (SELECT id FROM tl_member)')->execute($eventId, $userId);
        return $objRating->numRows;
    }

}
