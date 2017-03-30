<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

/** Config **/
// Set the event image folder
$GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['EVENT_FOLDER'] = 'files/events';


/** Frontend modules **/
array_insert($GLOBALS['FE_MOD'], 0, array(
        'custom_events' => array(
            'event_gallery'     => 'Markocupic\Customevents\EventGallery',
            'event_rating'      => 'Markocupic\Customevents\EventRating',
            'event_rating_form' => 'Markocupic\Customevents\EventRatingForm',
        ),
    ));


/** Backend modules **/
// Add event rating to the calendar backend-module
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_rating';


/** Hooks **/
// Move files to the event-directory
$GLOBALS['TL_HOOKS']['processFormData'][] = array('Markocupic\Customevents\Hooks', 'moveFilesToEventDirectory');
// Notify members of selected groups on fileuploads
$GLOBALS['TL_HOOKS']['processFormData'][] = array('Markocupic\Customevents\Hooks', 'notifyMembersOnFileupload');


/** Assets **/
if (TL_MODE == 'FE')
{
    $GLOBALS['TL_CSS'][] = 'system/modules/custom-events/assets/css/stylesheet.css';
    $GLOBALS['TL_CSS'][] = 'system/modules/custom-events/assets/fonts/star-rating/css/star-rating.css';
    $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/custom-events/assets/js/custom-events.js';
}