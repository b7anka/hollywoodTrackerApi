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

if(empty($_GET['token']))
{
    $msg = $langToUse[12];
    $error = 403;
    $success = false;  
}
else
{
    $token = $_GET['token'];    
    if(verifyToken($token))
    {
        if(updateUserToken($token))
        {
            $msg = $langToUse[13];
            $error = 0;
            $success = true;
        }
        else
        {
            $msg = $langToUse[14]; 
            $error = 403;
            $success = false;                      
        }
    }
    else
    {
        $msg = $langToUse[15];           
        $error = 403;
        $success = false;
    }
}

$response = [
    'msg'=>$msg,
    'error'=>$error,
    'success'=>$success
];

echo json_encode($response);

?>