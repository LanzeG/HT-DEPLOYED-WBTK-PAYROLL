<?php
/* Database connection settings */
	$servername = "localhost:3307";
    $username = "root";		//put your phpmyadmin username.(default is "root")
    $password = "";			//if your phpmyadmin has a password put it here.(default is "root")
    $dbname = "biometricattendace";
    
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	
	if ($conn->connect_error) {
        die("Database Connection failed: " . $conn->connect_error);
    }

    $host = "localhost:3307";
    $user= "root";
    $pass= "";
    $dbname1="masterdb";
    $conn1 = mysqli_connect($host,$user,$pass,$dbname1);


    if ( !$conn1 ) {
    die("Connection failed : " . mysql_error());
    }

?>