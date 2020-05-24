<?php

	include("database_connection.php");
	session_start();
	
	$query = "
		update login_details set is_type = '".$_POST["is_type"]."' where login_details_id = '".$_SESSION["login_details_id"]."'
	";
	$statement = $connect->prepare($query);
	$statement->execute();

?>