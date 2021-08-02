<?php
session_start();
// Destroy 
session_destroy(); 
header('Location: index.html');
?>