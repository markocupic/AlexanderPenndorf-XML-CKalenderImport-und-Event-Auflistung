<?php
/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */


// Palettes
$GLOBALS['TL_DCA']['tl_form']['palettes']['default'] = str_replace('{expert_legend', '{fileupload_legend:hide},notifyOnUpload;{expert_legend', $GLOBALS['TL_DCA']['tl_form']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'][] = 'notifyOnUpload';

// Subpalettes
$GLOBALS['TL_DCA']['tl_form']['subpalettes']['notifyOnUpload'] = 'notifyOnUploadGroups';

// Fields
$GLOBALS['TL_DCA']['tl_form']['fields']['notifyOnUpload'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_form']['notifyOnUpload'],
    'exclude' => true,
    'filter' => true,
    'inputType' => 'checkbox',
    'eval' => array('submitOnChange' => true, 'tl_class' => 'w50'),
    'sql' => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_form']['fields']['notifyOnUploadGroups'] = array
(
    'label' => &$GLOBALS['TL_LANG']['tl_form']['notifyOnUploadGroups'],
    'exclude' => true,
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_member_group.name',
    'eval' => array('tl_class' => 'clr', 'mandatory' => true, 'multiple' => true),
    'sql' => "blob NULL",
    'relation' => array('type' => 'hasMany', 'load' => 'lazy')
);
