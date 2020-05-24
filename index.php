<?php

include('database_connection.php');
session_start();

if(!isset($_SESSION['user_id']))
{
 header('location:login.php');
}

?>

<!DOCTYPE html>
<html>  

    <head>  
        <title>Chat Application using PHP Ajax Jquery</title>  
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
    </head>
	
    <body>  
		
		<div class="container">
		<br />
	   
		<h3 align="center">Chat Application using PHP Ajax Jquery</a></h3><br />
		<br />
	   
		<div class="table-responsive">
			<h4 align="center">Online User</h4>
			<p align="right">Hi - <?php echo $_SESSION['username']; ?> - <a href="logout.php">Logout</a></p>
			<div id = "user_details"></div>
			<div id = "user_model_details"></div>
		</div>
		
		</div>
	
    </body>  
</html>

<script>

	$(document).ready(function(){
		fetch_user();
		
		setInterval(function(){
			update_last_activity();
			fetch_user();
			update_chat_history_data();
		},5000);
		
		function fetch_user()
		{
			$.ajax({
				url:"fetch_user.php",
				method:"post",
				success : function(data){
					$('#user_details').html(data);
				}
			})
		}
		function update_last_activity()
		{
			$.ajax({
				url:"update_last_activity.php",
				success : function()
				{
					
				}
			})
		}
		
		function make_chat_dialog_box(to_user_id, to_user_name)
		{
		  var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
		  modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
		  modal_content += fetch_user_chat_history(to_user_id);
		  modal_content += '</div>';
		  modal_content += '<div class="form-group">';
		  modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message"></textarea>';
		  modal_content += '</div><div class="form-group" align="right">';
		  modal_content+= '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Send</button></div></div>';
		  $('#user_model_details').html(modal_content);
		}

		$(document).on('click','.start_chat',function(){
			var to_user_id = $(this).data('touserid');
			var to_user_name = $(this).data('tousername');
			make_chat_dialog_box(to_user_id,to_user_name);
			$("#user_dialog_"+to_user_id).dialog({
				autoOpen:false,
				width:400
			});
			$('#user_dialog_'+to_user_id).dialog('open');
			$('#chat_message_'+to_user_id).emojioneArea({
				pickerPosition : "top",
				toneStyle : "bullet"
			});
		});
		
		$(document).on('click','.send_chat',function(){
			var to_user_id = $(this).attr('id');
			var chat_message = $('#chat_message_'+to_user_id).val();
			$.ajax({
				url : "insert_chat.php",
				method : "post",
				data : {to_user_id:to_user_id,chat_message:chat_message},
				success : function(data)
				{
					//$('#chat_message_'+to_user_id).val('');
					var element = $('#chat_message_'+to_user_id).emojioneArea();
					element[0].emojioneArea.setText('');
					$('#chat_history_'+to_user_id).html(data);
				}
			});
		});
		
		function fetch_user_chat_history(to_user_id)
		{
			$.ajax({
				url : "fetch_user_chat_history.php",
				method : "post",
				data : {to_user_id:to_user_id},
				success : function(data)
				{
					$('#chat_history_'+to_user_id).html(data);
				}
			});
		}
		
		function update_chat_history_data()
		{
			$('.chat_history').each(function(){
				var to_user_id = $(this).data('touserid');
				fetch_user_chat_history(to_user_id);
			});
		}
		
		$(document).on('focus','.chat_message',function(){
			var is_type = "yes";
			$.ajax({
				url : "update_is_type_status.php",
				method : "post",
				data : {is_type:is_type},
				success : function()
				{
					
				}
			});
		});
		
		$(document).on('blur','.chat_message',function(){
			var is_type = "no";
			$.ajax({
				url : "update_is_type_status.php",
				method : "post",
				data : {is_type:is_type},
				success : function()
				{
					
				}
			});
		});
		
		$(document).on('click','.remove_chat',function(){
			var chat_message_id = $(this).attr('id');
			if(confirm("Are you sure you want to remove this chat...???"))
			{
				$.ajax({
					url : "remove_chat.php",
					method : "post",
					data : {chat_message_id:chat_message_id},
					success : function(data)
					{
						update_chat_history_data();
					}
				});
			}
		});
	});

</script>