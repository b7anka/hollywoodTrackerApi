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

    if(empty($_POST['email']))
    {
        $msg = $langToUse[80];
        $error = 402;
        $success = false;
    }
    else
    {        
        $mail = $_POST['email'];

        if(verifyUser($mail))
        {
            $user = fetchInfo(0,$mail);

            if($user['google_account'] == 0)
            {
                $msg = $langToUse[81];
                $error = 403;
                $success = false;
            }
            else
            {
                $valueOfLogin = getUserProfile($user['id_user']);
                $msg = $langToUse[3];
                $error = 0;
                $success = true;
                $result = $valueOfLogin;
                $watched_videos = getRewardedVideos($valueOfLogin[0]['id']);
            }
        }
        else
        {
            $msg = $langToUse[1];
            $error = 401;
            $success = false;
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