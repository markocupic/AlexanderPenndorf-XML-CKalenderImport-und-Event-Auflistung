<?php
/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

namespace Markocupic\Customevents;


class Helpers
{
    /**
     * @param $eventId
     * @return string
     */
    public static function getEventListingCSSClasses($eventId)
    {
        $arrCssClasses = array();
        $objEvent = \CalendarEventsModel::findByPk($eventId);
        if ($objEvent !== null)
        {
            // Check if event has uploads
            if (self::countEventUploads($eventId) > 0)
            {
                $arrCssClasses[] = 'event-has-uploads';
            }

            // Check if event is allready history
            if ($objEvent->endDate > 0)
            {
                if ($objEvent->endDate < time())
                {
                    $arrCssClasses[] = 'event-is-history';
                }
                else
                {
                    $arrCssClasses[] = 'future-event';
                }
            }

            // Check if event was rated
            if (\CalendarEventsRatingModel::countRatings($eventId) > 0)
            {
                $arrCssClasses[] = 'event-has-ratings';
            }
        }
        return implode(' ', $arrCssClasses);
    }


    /**
     * @param $eventId
     * @return int
     */
    protected static function countEventUploads($eventId)
    {
        $arrFiles = array();
        $objEvent = \CalendarEventsModel::findByPk($eventId);
        if ($objEvent !== null)
        {
            $strFolder = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['EVENT_FOLDER'] . '/event-' . $objEvent->id . '/images';
            if (is_dir(TL_ROOT . '/' . $strFolder))
            {
                // Scan the upload folder for images
                foreach (scan(TL_ROOT . '/' . $strFolder) as $strFile)
                {
                    if (strncmp($strFile, '.', 1) === 0 || is_dir(TL_ROOT . '/' . $strFolder . '/' . $strFile))
                    {
                        continue;
                    }

                    $objFile = new \File($strFolder . '/' . $strFile);
                    if ($objFile->isGdImage)
                    {
                        $arrFiles[] = $strFolder . '/' . $strFile;
                    }
                }
            }
        }
        return count($arrFiles);
    }

    /**
     * @param $html
     * @param int $loop
     * @return string
     */
    public static function generateRatingIconHtml($html, $loop = 0)
    {
        $strHtml = '';
        for ($i = 0; $i < $loop; $i++)
        {
            $strHtml .= $html;
        }
        return $strHtml;
    }

    /**
     * Helper Class for template "mod_event_gallery"
     * @param $i
     * @param $perRow
     * @return string
     */
    public static function getColClasses($i, $perRow)
    {
        $arrCSSClasses = array();

        // Is col_first
        if ($i % $perRow == 0)
        {
            $arrCSSClasses[] = 'col_first';
        }
        // Is col_last
        if (($i + 1) % $perRow == 0)
        {
            $arrCSSClasses[] = 'col_last';
        }

        // Get col number
        $arrCSSClasses[] = 'col_' . $i % $perRow;

        // Get row number
        $row = floor($i / $perRow);
        $arrCSSClasses[] = 'row_' . $row;

        // Add odd or even
        $arrCSSClasses[] = $row % 2 == 0 ? 'even' : 'odd';


        return implode(' ', $arrCSSClasses);

    }
}