<?php
    require_once('functions.php');    
    require_once('langs.php');

    header("Access-Control-Allow-Origin: *");

    $msg = '';
    $error = '';
    $success = false;
    $results = [];
    
    $lang = (!empty($_GET['lang']) ? $_GET['lang'] : 'en_US');

    $langToUse = setLangToUse($lang);

    if(empty($_GET['idUser']) || empty($_GET['type']))
    {
        $msg= $langToUse[21];
        $error = 403;
        $success = false;
    }
    else
    {        
        $idUser = $_GET['idUser'];                     
        $type = $_GET['type'];
        $timeStamp = $_GET['time'];
        updateLastActivity($idUser, $timeStamp);
        $results = getUserData($idUser,$type);
        $msg= $langToUse[22];
        $error = 0;
        $success = true;
    }

    $response = [
        'msg'=>$msg,
        'error'=>$error,
        'success'=>$success,
        'results'=>$results
    ];

    echo json_encode($response);
?>