<?php
include 'db_conf.php';
$conn = OpenCon();

if($conn){
    $uname = mysqli_real_escape_string($conn,$_POST['username']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

if ($uname != "" && $password != ""){

    $sql_query = "select * from users where username='".$uname."' and password='".$password."'";
    $result = mysqli_query($conn,$sql_query);
    $row = mysqli_fetch_array($result);

    $count = $row['username'];

    if($count){
        $_SESSION['uname'] = $uname;
        echo 1;
    }else{
        echo 0;
    }

}

}
?>