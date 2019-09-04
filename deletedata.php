<?php
    require_once('functions.php');    
    require_once('langs.php');

    header("Access-Control-Allow-Origin: *");

    $msg = '';
    $error = '';
    $success = false;

    $data = json_decode(file_get_contents('php://input'), true);

    if(is_array($data))
    {
        $_POST = $data;
    }
    
    $lang = (!empty($_POST['lang']) ? $_POST['lang'] : 'en_US');

    $langToUse = setLangToUse($lang);

    if(empty($_POST['id']) || empty($_POST['type']))
    {
        $msg= $langToUse[5];
        $error = 403;
        $success = false;
    }
    else
    {        
        $id = $_POST['id'];
        $idUser = $_POST['idUser'];
        $type = $_POST['type'];
        $timeStamp = $_POST['time'];
        updateLastActivity($idUser, $timeStamp);
        $result = deleteData($id,$type);

        if($result)
        {
            $msg= $langToUse[31];
            $error = 0;
            $success = true;
        } 
        else
        {
            $msg= $langToUse[32];
            $error = 0;
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