<?php
    require_once('functions.php');    
    require_once('langs.php');

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    $msg = '';
    $error = '';
    $success = false;
    $watched_videos = 0;
    $result = [];    

    $data = json_decode(file_get_contents('php://input'), true);

    if(is_array($data))
    {
        $_POST = $data;
    }

    $lang = (!empty($_POST['lang']) ? $_POST['lang'] : 'en_US');

    $langToUse = setLangToUse($lang);

    if(empty($_POST['username']) || empty($_POST['pass']))
    {
        $msg = $langToUse[0];
        $error = 403;
        $success = false;
    }
    else
    {        
        $username = $_POST['username'];                
        $pass = $_POST['pass'];        

        if(!verifyUser($username))
        {
            $msg = $langToUse[1];
            $error = 404;
            $success = false;
        }
        else
        {
            $valueOfLogin = login($username,$pass);

            if(is_array($valueOfLogin))
            {
                    $msg = $langToUse[3];
                    $error = 0;
                    $success = true;
                    $result = $valueOfLogin;
                    $watched_videos = getRewardedVideos($valueOfLogin[0]['id']);
            }
            else
            {
                switch($valueOfLogin)
                {
                    case 'not-verified':
                        $msg = $langToUse[2];
                        $error = 1;
                        $success = false;
                    break;                
                    case 'not-ok':
                        $msg = $langToUse[4];
                        $error = 402;
                        $success = false;
                    break;
                }
            }            
        }
    }

    $response = [
        'msg'=>$msg,
        'error'=>$error,
        'success'=>$success,
        'results'=>$result,
        'videos_watched'=>$watched_videos
    ];

    echo json_encode($response);
?>