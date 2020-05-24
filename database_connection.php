<html>
	<body>
	<audio id="myAudio">
		<source src="notification1.mp3" type="audio/ogg">
		<source src="notification1.mp3" type="audio/mpeg">
	</audio>
	</body>
</html>
<?php

	$connect = new PDO("mysql:host=localhost;dbname=livechat;charset=utf8mb4;","root","");

	date_default_timezone_set('Asia/Kolkata');

	function fetch_user_last_activity($user_id,$connect)
	{
		$query = "
			select * from login_details where user_id = '$user_id' order by last_activity desc limit 1
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		
		foreach($result as $row)
		{
			return $row["last_activity"];
		}
	}
	
	function fetch_user_chat_history($from_user_id,$to_user_id,$connect)
	{
		$query = "
			select * from chat_message where (from_user_id = '".$from_user_id."' and to_user_id = '".$to_user_id."')
			OR (from_user_id = '".$to_user_id."' and to_user_id = '".$from_user_id."') order by timestamp desc
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '<ul class = "list-unstyled">';
		foreach($result as $row)
		{
			$username = '';
			$chat_message = '';
			if($row["from_user_id"] == $from_user_id)
			{
				if($row["status"] == '2')
				{
					$chat_message = '<em>This message has been deleted...!!!</em>';
					$username = '<b class = "text-success">You</b>';
				}
				else
				{
					$chat_message = $row["chat_message"];
					$username = '<button type="button" class="btn btn-danger btn-xs remove_chat" id="'.$row['chat_message_id'].'">x</button>&nbsp;<b class="text-success">You</b>';
				}
			}
			else
			{
				if($row["status"] == '2')
				{
					$chat_message = '<em>This message has been removed</em>';
				}
				else
				{
					$chat_message = $row["chat_message"];
				}
				$username = '<b class = "text-danger">'.get_user_name($row["from_user_id"],$connect).'</b>';
			}
			$output .= '
				<li style="border-bottom:1px dotted #ccc;padding-top:8px; padding-left:8px; padding-right:8px;">
					<p>'.$username.' - '.$chat_message.'
						<div align="right">
							- <small><em>'.$row['timestamp'].'</em></small>
						</div>
					</p>
				</li>
			';
		}
		$output .= '</ul>';
		$query = "
			update chat_message set status = '0' where from_user_id = '".$to_user_id."' and to_user_id = '".$from_user_id."' and status = '1'
		";
		$statement = $connect->prepare($query);
		$statement->execute(); 
		$sub_query = "
			update chat_message set notification = '0' where from_user_id = '".$to_user_id."' and to_user_id = '".$from_user_id."' and notification = '1'
		";
		$sub_statement = $connect->prepare($sub_query);
		$sub_statement->execute();
		return $output;
	}
	
	function get_user_name($user_id,$connect)
	{
		$query = "select username from login where user_id = '$user_id'";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			return $row["username"];
		}
	}
	
	function count_unseen_message($from_user_id,$to_user_id,$connect)
	{
		$query = "
			select * from chat_message where from_user_id = '$from_user_id' and to_user_id = '$to_user_id' and status = '1'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$count = $statement->rowCount();
			
		$sub_query = "
			select * from chat_message where from_user_id = '$from_user_id' and to_user_id = '$to_user_id' and notification = '1'
		";
		$sub_statement = $connect->prepare($sub_query);
		$sub_statement->execute();
		$count_notify = $sub_statement->rowCount();
		
		$output = '';
		if($count > 0)
		{
			$output = '<span class = "label label-success">'.$count.'</span>';
			if($count_notify > 0)
			{
				echo '<script type="text/javascript">
						  makenotification();
				      </script>';
			}
			$subsub_query = "
				update chat_message set notification = '0' where from_user_id = '$from_user_id' and to_user_id = '$to_user_id' and notification = '1'
			";
			$subsub_statement = $connect->prepare($subsub_query);
			$subsub_statement->execute();
		}
		return $output;
	}
	
	function fetch_is_type_status($user_id,$connect)
	{
		$query = "
			select is_type from login_details where user_id = '".$user_id."' order by last_activity desc limit 1
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '';
		foreach($result as $row)
		{
			if($row["is_type"] == "yes")
			{
				$output = '
					- <small><em><span class = "text-muted">Typing...</span></em></small>
				';
			}
		}
		return $output;
	}

?>

<script>

	var x = document.getElementById("myAudio");
	function makenotification()
	{
		x.play();
	}

</script>