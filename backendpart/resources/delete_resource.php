<?php
sesiion_start();
header('Content-Type: application/json');
header('Access-control-Allow-Orign: *');
include('../config/db.php");
$input=json_decode(file_get_contents('php://input'),true);
$id=$input['id'] ?? 0;
//continue here..
//the input has been a comment ask that

