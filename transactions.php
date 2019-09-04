<?php
/**
 * Created by PhpStorm.
 * User: blanka
 * Date: 2019-04-06
 * Time: 09:16
 */

require_once("functions.php");

header("Access-Control-Allow-Origin: *");

$email = $_GET['email'];

$hasPreviouslyPurchasedPremium = (int)checkIfUserHasPreviouslyPurchasedPremium($email);

$response = [
    'has_previously_purchased'=>$hasPreviouslyPurchasedPremium
];

echo json_encode($response);