<?php

require_once("functions.php");
require_once("langs.php");

header("Access-Control-Allow-Origin: *");

$msg = '';
$error = '';
$success = false;

$lang = 'en_US';

if(!empty($_GET['lang']))
{
    $lang = $_GET['lang'];
}

$langToUse = setLangToUse($lang);

if(empty($_GET['username']))
{
    $msg = $langToUse[78];
    $error = 403;
    $success = false;  
}
else
{
    $username = $_GET['username'];    
    if(verifyUser($username))
    {
        $lastActivity = getLastActivity($username);
        $msg = $langToUse[79];
        $error = 0;
        $success = true;
    }
    else
    {
        $lastActivity = 0;
        $msg = $langToUse[1];           
        $error = 403;
        $success = false;
    }
}

$response = [
    'msg'=>$msg,
    'error'=>$error,
    'success'=>$success,
    'last_activity'=>$lastActivity
];

echo json_encode($response);

?>