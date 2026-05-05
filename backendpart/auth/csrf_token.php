<?php
session_start();
header('Conternt-Type: application/json');
function gnerateCSRFToken(){
  if(empty($_SESSSION['csrf_token']) ){
    $_SESSION[csrf_token'] = bin2hex(randin_bytes(32));
  }
  return $_SESSION['csrf_token'];
}
function verifyCSRFToken($token){
   return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
echo json_encode(['csrf_token' => generateCSRFToken()]);
?>
