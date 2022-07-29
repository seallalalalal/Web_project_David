<?php
// change this file when you change the environment (mySQL password & ....)
function quoteStr($sql) {
  global $db;
  return "'".$db->real_escape_string($sql)."'";
}
date_default_timezone_set('Asia/Taipei');
$db=new mysqli('p:127.0.0.1','web_project','eatcat','web_project');
if($db->connect_errno) die("-CONNECT_DB");
$db->set_charset('utf8');
//session_start();
?>
