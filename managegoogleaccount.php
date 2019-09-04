<?php
    require_once('functions.php');    
    require_once('langs.php');

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

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

    if(empty($_POST['email']) || empty($_POST['value']))
    {
        $msg = $langToUse[84];
        $error = 403;
        $success = false;
    }
    else
    {        
        $mail = $_POST['email'];
        $linkState = filter_var($_POST['value'], FILTER_VALIDATE_BOOLEAN);
        $value = ($linkState ? 1 : 0);

        if(verifyUser($mail))
        {
            if(linkGoogleAccount($mail, $value))
            {
                $msg = $langToUse[82];
                $error = 0;
                $success = true;
            }
            else
            {
                $msg = $langToUse[83];
                $error = 403;
                $success = false;
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
        'success'=>$success
    ];

    echo json_encode($response);
?>