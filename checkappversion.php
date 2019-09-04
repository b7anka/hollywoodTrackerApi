<?php
/**
 * Created by PhpStorm.
 * User: blanka
 * Date: 2019-04-06
 * Time: 09:16
 */

require_once("functions.php");

header("Access-Control-Allow-Origin: *");

$version = 0;
$versionCode = 0;

$appInfo = getAppVersion();

$version = (float)$appInfo[0];
$versionCode = (int)$appInfo[1];
$versionIOS = (float)$appInfo[2];
$build = (int)$appInfo[3];

$response = [
    'version'=>$version,
    'versionCode'=>$versionCode,
    'versionIOS'=>$versionIOS,
    'build'=>$build
];

echo json_encode($response);