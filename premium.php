<?php

require_once("functions.php");
require_once("langs.php");

define("PUBLIC_KEY","YOUR_ANDROID_APP_PUBLIC_KEY");
define("DEVICE_ANDROID","android");

$msg = '';
$error = '';
$success = false;

$data = json_decode(file_get_contents('php://input'), true);

if(is_array($data))
{
    $_POST = $data;
}

$lang = (!empty($_POST['lang']) ? $_POST['lang'] : "en_US");

$langToUse = setLangToUse($lang);

if(empty($_POST['idUser']) || empty($_POST['device']))
{
    $msg = $langToUse[66];
    $error = 403;
    $success = false;
}
else
{  
    $id = $_POST['idUser'];
    $device = $_POST['device'];
    $signature = (!empty($_POST['signature']) ? $_POST['signature'] : null);
    $timeStamp = $_POST['time'];
    updateLastActivity($id, $timeStamp);

    if($device == DEVICE_ANDROID)
    {
        if(empty($_POST['originalData']) || empty($_POST['signature']))
        {
            $msg = $langToUse[77];
            $error = 403;
            $success = false;
        }
        else
        {
            $originalData = $_POST['originalData'];
            $signature = $_POST['signature'];
            if(verifyInAppPlayStore($originalData, $signature, PUBLIC_KEY))
            { 
                $userInfo = fetchInfo($id);
                $email = $userInfo[3];

                if(updatePremium($id))
                {
                    if(!CheckPreviousTransaction($email)){
                    savePremiumTransaction($email);
                }

                    $msg = $langToUse[67];
                    $error = 0;
                    $success = true;
                }
                else
                {
                    $msg = $langToUse[68];
                    $error = 403;
                    $success = false;
                }
            }
            else
            {
                $msg = $langToUse[75];
                $error = 403;
                $success = false;
            }
        }
    }
    else
    {
        $userInfo = fetchInfo($id);
        $email = $userInfo[3];
        if(updatePremium($id))
        {
            if(!CheckPreviousTransaction($email)){
                savePremiumTransaction($email);
            }
            $msg = $langToUse[67];
            $error = 0;
            $success = true;
        }
        else
        {
            $msg = $langToUse[68];
            $error = 403;
            $success = false;
        }
    }

}

$response = [
    'msg'=>$msg,
    'error'=>$error,
    'success'=>$success
];

echo json_encode($response);

?>