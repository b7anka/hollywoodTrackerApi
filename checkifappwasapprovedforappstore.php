<?php
/**
 * Created by PhpStorm.
 * User: blanka
 * Date: 2019-04-06
 * Time: 09:16
 */

require_once("functions.php");

header("Access-Control-Allow-Origin: *");

$wasApprovedForAppStore = (int)getAppApprovedForAppStoreStatus();

$response = [
    'was_approved'=>$wasApprovedForAppStore
];

echo json_encode($response);