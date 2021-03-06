<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

namespace Markocupic\Customevents;


class Hooks
{
    /**
     * @var string
     */
    private static $strEventFolder = 'files/events';

    /**
     * ProcessFormDataHook
     * @param Widget $objWidget
     * @param $intId
     * @param $arrForm
     * @return Widget
     */
    public function moveFilesToEventDirectory($arrSubmitted, $arrData, $arrFiles, $arrLabels, $objForm)
    {

        if ($_POST['FORM_SUBMIT'] && isset($_GET['auto_item']) && $_GET['events'] != '')
        {
            // Support for contao core fileupload, Dropzone and Fineuplaoder
            if (isset($arrFiles['event_image_uploader']) || isset($_SESSION['FORM_DATA']['event_image_uploader']) || isset($_SESSION['FORM_DATA']['event_image_uploader_fineuploader']))
            {
                if (count($arrFiles) > 0)
                {
                    foreach ($arrFiles as $arrFile)
                    {
                        $src = str_replace(TL_ROOT . '/', '', $arrFile['tmp_name']);
                        $objEvents = \CalendarEventsModel::findByIdOrAlias($_GET['events']);
                        if ($objEvents !== null)
                        {
                            // Create new event folder if it doesn't already exist
                            $strFolder = $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['EVENT_FOLDER'] . '/event-' . $objEvents->id . '/images';
                            new \Folder($strFolder);
                            if (is_file(TL_ROOT . '/' . $src))
                            {
                                $objFile = new \File($src);
                            }
                            else
                            {
                                continue;
                            }

                            // Check for valide filetype
                            if (!$objFile->isImage)
                            {
                                continue;
                            }

                            // Move file to the event folder
                            $blnMoved = false;
                            $i = 0;
                            while ($blnMoved === false)
                            {
                                $i++;
                                $filename = 'file-' . str_pad($i, 6, '0', STR_PAD_LEFT) . '.' . strtolower($objFile->extension);
                                if (!is_file(TL_ROOT . '/' . $strFolder . '/' . $filename))
                                {
                                    \Files::getInstance()->copy($objFile->path, $strFolder . '/' . $filename);
                                    $blnMoved = true;
                                    $objOldFile = new \File($objFile->path, false);
                                    $objOldFile->delete();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * ProcessFormDataHook
     * @param $arrSubmitted
     * @param $arrData
     * @param $arrFiles
     * @param $arrLabels
     * @param $objForm
     */
    public function notifyMembersOnFileupload($arrSubmitted, $arrData, $arrFiles, $arrLabels, $objForm)
    {
        if ($_POST['FORM_SUBMIT'] && $objForm->notifyOnUpload && count($arrFiles) > 0)
        {
            $arrGroupsToNotify = deserialize($objForm->notifyOnUploadGroups, true);
            if (count($arrGroupsToNotify) > 0)
            {
                $arrMembersToNotify = array();
                $objMember = \Database::getInstance()->prepare('SELECT * FROM tl_member WHERE disable=?')->execute('');
                while ($objMember->next())
                {
                    if ($objMember->email != '')
                    {
                        $arrMemberGroups = deserialize($objMember->groups, true);
                        if (array_intersect($arrGroupsToNotify, $arrMemberGroups))
                        {
                            $arrMembersToNotify[] = $objMember->id;
                        }
                    }
                }

                $arrMembersToNotify = array_unique($arrMembersToNotify);


                // notify members
                foreach ($arrMembersToNotify as $memberId)
                {
                    $objMember = \MemberModel::findByPk($memberId);
                    if ($objMember === null)
                    {
                        continue;
                    }
                    if ($objMember->email == '')
                    {
                        continue;
                    }

                    $objTemplate = new \FrontendTemplate('fileupload_email_notification');
                    $objTemplate->firstname = $objMember->firstname;

                    $objUser = \FrontendUser::getInstance();
                    if ($objUser !== null)
                    {
                        $objTemplate->logged_in_user_firstname = $objUser->firstname;
                        $objTemplate->logged_in_user_lastname = $objUser->lastname;
                    }

                    $eventname = 'no eventname found';
                    $objEvent = $this->getEventFromUrl();
                    if ($objEvent !== null)
                    {
                        $eventname = $objEvent->title;
                        $objTemplate->eventname = $eventname;
                    }

                    $objTemplate->href = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER[HTTP_HOST] . $_SERVER[REQUEST_URI];

                    // Email
                    $email = new \Email();
                    $email->subject = 'Neue Bilder fuer Event: ' . $eventname;
                    $email->text = $objTemplate->parse();
                    $email->sendTo($objMember->email);
                }
            }
        }
    }

    /**
     * @return \CalendarEventsModel|null
     */
    protected function getEventFromUrl()
    {

        $objEvent = \CalendarEventsModel::findByIdOrAlias($_GET['events']);
        return $objEvent;
    }

}


