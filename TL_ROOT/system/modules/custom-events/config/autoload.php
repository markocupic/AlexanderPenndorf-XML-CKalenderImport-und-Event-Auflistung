<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
    'Markocupic',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Modules
    'Markocupic\Customevents\EventRatingForm' => 'system/modules/custom-events/modules/EventRatingForm.php',
    'Markocupic\Customevents\EventRating' => 'system/modules/custom-events/modules/EventRating.php',
    'Markocupic\Customevents\EventGallery' => 'system/modules/custom-events/modules/EventGallery.php',

    // Classes
    'Markocupic\Customevents\Helpers' => 'system/modules/custom-events/classes/Helpers.php',
    'Markocupic\Customevents\Hooks' => 'system/modules/custom-events/classes/Hooks.php',

    // Models
    'Contao\CalendarEventsRatingModel' => 'system/modules/custom-events/models/CalendarEventsRatingModel.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'fileupload_email_notification' => 'system/modules/custom-events/templates/fileupload_email_notification',
    'rating_email_notification' => 'system/modules/custom-events/templates/rating_email_notification',
    'mod_event_gallery' => 'system/modules/custom-events/templates/event_gallery',
    'event_full_custom' => 'system/modules/custom-events/templates/calendar',
    'event_upcoming_custom' => 'system/modules/custom-events/templates/calendar',
    'mod_event_rating' => 'system/modules/custom-events/templates/event_rating',
    'mod_event_rating_form' => 'system/modules/custom-events/templates/event_rating',
));
