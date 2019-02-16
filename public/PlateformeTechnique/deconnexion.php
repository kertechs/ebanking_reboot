<?php
session_start();
session_destroy();
header('location: connexionPFT.php');
exit;
?>