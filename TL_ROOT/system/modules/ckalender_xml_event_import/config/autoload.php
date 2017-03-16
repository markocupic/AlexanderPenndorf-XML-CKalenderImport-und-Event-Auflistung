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
	// Classes
	'Markocupic\CKalenderXmlEventImport' => 'system/modules/ckalender_xml_event_import/classes/CKalenderXmlEventImport.php',
));
