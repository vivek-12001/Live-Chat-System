<?php

	include("database_connection.php");
	
	if(isset($_POST["chat_message_id"]))
	{
		$query = "
			update chat_message set status = '2' where chat_message_id = '".$_POST["chat_message_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
	}

?>