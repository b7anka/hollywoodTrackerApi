<?php

require_once("functions.php");
require_once("langs.php");

header("Access-Control-Allow-Origin: *");

$msg = '';
$error = '';
$success = false;
$videosWatched = 0;

$lang = 'en_US';

if(!empty($_GET['lang']))
{
    $lang = $_GET['lang'];
}

$langToUse = setLangToUse($lang);

if(empty($_GET['idUser']))
{
    $msg = $langToUse[78];
    $error = 403;
    $success = false;  
}
else
{
    $id = $_GET['idUser'];
    $isSaving = filter_var($_GET['isSaving'], FILTER_VALIDATE_BOOLEAN);
    $value = $_GET['value'];

    if($isSaving)
    {
        if(updateRewardedVideos($id, $value))
        {
            $videosWatched = getRewardedVideos($id);
            $msg = $langToUse[79];
            $error = 200;
            if($videosWatched == TOTAL_VIDEOS_TO_WIN)
            {
                updatePremium($id);
                $success = true;
            }
            else
            {
                $success = false;
            }
        }
        else
        {
            $msg = $langToUse[1];
            $error = 403;
            $success = false;
        }
    }
    else
    {
        $videosWatched = getRewardedVideos($id);
        $msg = $langToUse[79];
        $error = 100;
        $success = false;
    }
}

$response = [
    'msg'=>$msg,
    'error'=>$error,
    'success'=>$success,
    'videos_watched'=>$videosWatched
];

echo json_encode($response);

?>