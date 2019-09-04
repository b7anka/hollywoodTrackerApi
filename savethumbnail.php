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

    if(empty($_POST['title']) || empty($_POST['thumb']))
    {
        $msg = $langToUse[57];
        $error = 403;
        $success = false;
    }
    else
    {        
        $title = testInput($_POST['title']);
        $thumbnail = $_POST['thumb'];  
        
        if(!verifyThumbnail($title))
        {
            $fileName = decodeAndStoreImage($thumbnail,"images");

            if(empty($fileName))
            {
                $msg = $langToUse[58];
                $error = 403;
                $success = false;
            }
            else
            {
                $thumbnail = "$defaultURL/images/$fileName";
                if(setDataThumbnail($title,$thumbnail))
                {
                    $msg = $langToUse[59];
                    $error = 0;
                    $success = true;
                }
                else
                {
                    $msg = $langToUse[60];
                    $error = 403;
                    $success = false;
                }
            }
        }
        else
        {
            $msg = $langToUse[76];
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