<?php
function OpenCon()
 {
 $dbhost = "localhost";
 $dbuser = "root";
 $dbpass = "kishore1234";
 $db = "ssl_check";
 $conn = mysqli_connect($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: \n". $conn -> error);
 
 return $conn;
 }
 
function CloseCon($conn)
 {
 $conn -> close();
 }
   
?>