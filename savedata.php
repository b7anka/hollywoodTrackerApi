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

    if(empty($_POST['idUser']) || empty($_POST['type']) || empty($_POST['title']) || empty($_POST['watchedtime']))
    {
        $msg= $langToUse[5];
        $error = 403;
        $success = false;
    }
    else
    {                
        $idUser = $_POST['idUser'];
        $timeStamp = $_POST['time'];
        $type = $_POST['type'];
        $title = $_POST['title'];
        $watchedtime = $_POST['watchedtime'];
        updateLastActivity($idUser, $timeStamp);
        $page = (!empty($_POST['page']) ? $_POST['page'] : null);
        $season = (!empty($_POST['season']) ? $_POST['season'] : null);
        $episode = (!empty($_POST['episode']) ? $_POST['episode'] : null);
        $completed = (!empty($_POST['completed']) ? $_POST['completed'] : 0);

        if(!checkForPremium($idUser, $type))
        {
            switch($type)
            {
                case MOVIES:
                    $msg= $langToUse[69];
                break;
                case TVSHOWS:
                    $msg= $langToUse[70];
                break;
                case BOOKS:
                    $msg= $langToUse[71];
                break;                    
            }
            $error = 403;
            $success = false;
        }    
        else
        {
            $result = saveUserData($idUser,$type,$title,$watchedtime,$page,$season,$episode,$completed,$langToUse);

            if($result)
            {
                switch($type)
                {
                    case MOVIES:
                        $msg= $langToUse[23];
                    break;
                    case TVSHOWS:
                        $msg= $langToUse[25];
                    break;
                    case BOOKS:
                        $msg= $langToUse[27];
                    break;                    
                }
                $error = 0;
                $success = true;
            } 
            else
            {
                switch($type)
                {
                    case MOVIES:
                        $msg= $langToUse[24];
                    break;
                    case TVSHOWS:
                        $msg= $langToUse[26];
                    break;
                    case BOOKS:
                        $msg= $langToUse[28];
                    break;                    
                }
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