<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */


/**
 * Add a palette to tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_gallery'] = '{title_legend},name,headline,type;{image_legend},perRow;{template_legend:hide},eventGalleryTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_rating'] = '{title_legend},name,headline,type;{template_legend:hide},eventRatingTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['event_rating_form'] = '{title_legend},name,headline,type;{template_legend:hide},eventRatingFormTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

/**
 * Table tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['perRow'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['perRow'],
    'default'                 => 4,
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'                 => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "smallint(5) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['eventGalleryTpl'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['eventGalleryTpl'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_event_gallery', 'getEventGalleryTemplates'),
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['eventRatingTpl'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['eventRatingTpl'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_event_gallery', 'getEventRatingTemplates'),
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['eventRatingFormTpl'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['eventRatingFormTpl'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_event_gallery', 'getEventRatingFormTemplates'),
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);

  /**
   * Provide miscellaneous methods that are used by the data configuration array.
   *
   * @author Leo Feyer <https://github.com/leofeyer>
   */
class tl_event_gallery extends Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Return all templates as array
     *
     * @return array
     */
    public function getEventGalleryTemplates()
    {
        return $this->getTemplateGroup('mod_event_gallery');
    }

    /**
     * Return all templates as array
     *
     * @return array
     */
    public function getEventRatingTemplates()
    {
        return $this->getTemplateGroup('mod_event_rating');
    }

    /**
     * Return all templates as array
     *
     * @return array
     */
    public function getEventRatingFormTemplates()
    {
        return $this->getTemplateGroup('mod_event_rating_form');
    }

}