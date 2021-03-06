<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

$GLOBALS['TL_DCA']['tl_calendar_events']['config']['ondelete_callback'][] = array('tl_calendar_events_custom_events', 'ondeleteCallback');

/**
 * Table tl_calendar_events
 */
$GLOBALS['TL_DCA']['tl_calendar_events']['list']['operations']['ratings'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['ratings'],
    'href'  => 'table=tl_calendar_events_rating',
    'icon'  => 'system/modules/custom-events/assets/images/star.png',
    'button_callback' => array('tl_calendar_events_custom_events', 'buttonCallbackRatings')
);





class tl_calendar_events_custom_events extends Backend
{
    public function ondeleteCallback(DataContainer $dc)
    {
        $path = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['EVENT_FOLDER'] . '/event-' . $dc->id;
        $pathNew = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['EVENT_FOLDER'] . '/old-event-' . $dc->id;
        $objFolder = new Folder($path);
        $objFolder->renameTo($pathNew);

        // Delete from tl_comments
        Database::getInstance()->prepare('DELETE FROM tl_comments WHERE source=? AND parent=?')->execute('tl_calendar_events', $dc->id);

        // Delete from tl_calendar_events_rating
        Database::getInstance()->prepare('DELETE FROM tl_calendar_events_rating WHERE pid=?')->execute($dc->id);
    }

    /**
     * @param $row
     * @param $href
     * @param $label
     * @param $title
     * @param $icon
     * @param $attributes
     * @return string
     */
    public function buttonCallbackRatings($row, $href, $label, $title, $icon, $attributes)
    {
        $rating = false;
        $objRating = Database::getInstance()->prepare('SELECT * FROM tl_calendar_events_rating WHERE pid=?')->execute($row['id']);
        if($objRating->numRows)
        {
            $rating = true;
        }

        if($rating)
        {
            $href = $href . '&id=' . $row['id'];
            return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
        }else{
            return '';
        }

    }
}
