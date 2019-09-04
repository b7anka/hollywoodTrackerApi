<?php

require_once("functions.php");
require_once("langs.php");

$msg = '';
$error = '';
$success = false;

$data = json_decode(file_get_contents('php://input'), true);

if(is_array($data))
{
    $_POST = $data;
}

$lang = 'en_US';

$lang = (!empty($_POST['lang']) ? $_POST['lang'] : $_GET['lang']);

if(empty($lang))
{
    $lang = "en_US";
}

$langToUse = setLangToUse($lang);

if(empty($_GET['token']) && empty($_POST['id']))
{
    $msg = $langToUse[12];
    $error = 403;
    $success = false;
}
else
{
    $token = (!empty($_GET['token']) ? $_GET['token'] : "");   
    $id = (!empty($_POST['id']) ? $_POST['id'] : null);

    if(!empty($token))
    {
        if(verifyToken($token))
        {        
            if(removeToken($token))
            {       
                $msg = $langToUse[16];
                $error = 403;
                $success = false;       
            }
            else
            {
                $msg = $langToUse[17];
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
    else
    {
        if(deleteUserAccount($id))
        {
            $msg = $langToUse[55];
            $error = 0;
            $success = true;
        }
        else
        {
            $msg = $langToUse[56];
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