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
class EventGallery extends \Module
{

    /**
     * template
     * @var string
     */
    protected $strTemplate = 'mod_event_gallery';

    /**
     * @var null
     */
    protected $arrFiles = null;

    /**
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');
            if (version_compare(VERSION . '.' . BUILD, '4.0.0', '<'))
            {
                $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['events_gallery'][0]) . ' ###';
            }
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        if (\Environment::get('isAjaxRequest') && \Input::get('file') != '' && \Input::get('delete_file') && FE_USER_LOGGED_IN)
        {
            if (file_exists(TL_ROOT . '/' . \Input::get('file')))
            {
                $objFiles = new \File(\Input::get('file'));
                $objFiles->delete();
                echo json_encode(array('status' => 'success'));
                exit();
            }
        }


        // Overwrite default template
        if ($this->eventGalleryTpl != '')
        {
            $this->strTemplate = $this->eventGalleryTpl;
        }

        $this->arrFiles = $this->getEventImagesFromUrl();
        if (count($this->arrFiles) < 1)
        {
            // return 'Noch keine Bilder';
            return '';
        }

        return parent::generate();
    }


    /**
     * Generate the module
     */
    protected function compile()
    {
        $cols = array();
        foreach ($this->arrFiles as $i => $file)
        {
            $cols[] = array(
                'href'     => $file,
                'name'     => basename($file),
                'colClass' => $this->getColClass($i, $this->perRow, $this->arrFiles),
            );
        }

        if (count($cols))
        {
            $this->Template->cols = $cols;
        }
    }

    /**
     * @return array
     */
    public function getEventImagesFromUrl()
    {
        $arrFiles = array();
        if (isset($_GET['auto_item']) && $_GET['events'] != '')
        {
            $objEvents = \CalendarEventsModel::findByIdOrAlias($_GET['events']);
            if ($objEvents !== null)
            {
                $strFolder = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['EVENT_FOLDER'] . '/event-' . $objEvents->id . '/images';
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
        }
        return $arrFiles;
    }

    /**
     * Helper Class
     * @param $i
     * @param $perRow
     * @return string
     */
    protected function getColClass($i, $perRow = 0, $files)
    {
        if ($perRow < 1)
        {
            $perRow = count($files);
        }

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
