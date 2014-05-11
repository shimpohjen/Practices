<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_DATABASE', 'TheWall');

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
if(mysqli_connect_errno())
{
	echo "Connection failed with error " . mysqli_connect_errno(); 
}
?>