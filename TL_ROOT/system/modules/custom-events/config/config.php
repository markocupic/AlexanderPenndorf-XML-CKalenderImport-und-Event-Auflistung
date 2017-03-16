<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

// Frontend modules
array_insert
(
	$GLOBALS['FE_MOD'], 0, array
	(
		'custom_events' => array
		(
			'event_gallery' => 'Markocupic\Customevents\EventGallery',
			'event_rating' => 'Markocupic\Customevents\EventRating',
			'event_rating_form' => 'Markocupic\Customevents\EventRatingForm'
		)
	)	
);

// Add event rating to the calendar backend-module
$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_events_rating';

// Hooks
$GLOBALS['TL_HOOKS']['validateFormField'][] = array('Markocupic\Customevents\UploadHooks', 'validateFormFieldHook');
// Notify members of selected groups on fileuploads
$GLOBALS['TL_HOOKS']['processFormData'][] = array('Markocupic\Customevents\UploadHooks', 'notifyMembersOnFileupload');

// Config
$GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['EVENT_FOLDER'] = 'files/events';

// CSS
if(TL_MODE == 'FE')
{
	$GLOBALS['TL_CSS'][] = 'system/modules/custom-events/assets/css/stylesheet.css';
}