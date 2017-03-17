<?php
/**
 * Created by PhpStorm.
 * User: teacher
 * Date: 17.03.2017
 * Time: 13:00
 */

if (\Validator::isUuid($source)) {
    $oFile = \FilesModel::findByUuid($source);
    if ($oFile === null) {
        continue;
    }
    $objFile = new \File($oFile->path);

} elseif (is_file(TL_ROOT . '/' . $source)) {
    $objFile = new \File($source);
} else {
    continue;
}