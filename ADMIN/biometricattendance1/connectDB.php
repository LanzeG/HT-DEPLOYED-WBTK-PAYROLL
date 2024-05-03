<?php
/* Database connection settings */


    $host = "localhost";
    $user= "u387373332_masterdb";
    $pass= "WBTKpayrollportal1234@";
    $dbname1="u387373332_masterdb";
    $conn1 = mysqli_connect($host,$user,$pass,$dbname1);


    if ( !$conn1 ) {
    die("Connection failed : " . mysql_error());
    }

?>