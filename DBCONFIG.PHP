 
<?php

// this will avoid mysql_connect() deprecation error.
error_reporting( ~E_DEPRECATED & ~E_NOTICE );
// but I strongly suggest you to use PDO or MySQLi.

$host = "localhost:3307";
$user= "root";
$pass= "";
$dbname="masterdb";
$conn = mysqli_connect($host,$user,$pass,$dbname);


if ( !$conn ) {
 die("Connection failed : " . mysql_error());
}



?>