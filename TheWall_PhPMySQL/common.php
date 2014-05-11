<?php
function getUser($connection, $email, $pass)
{
	$query='SELECT id, first_name, last_name FROM users WHERE email ="' . $email .'" AND password ="'. $pass .'"'; 
	return(mysqli_query($connection, $query));
}
function getmessages($connection){
	$query = "SELECT first_name, last_name, message, messages.updated_at, messages.id 
	FROM thewall.users JOIN thewall.messages ON 
	users.id = messages.user_id ORDER BY messages.updated_at DESC";
	$temp = array();
	$result = mysqli_query($connection, $query);
	if(isset($result))
	{
		while($row = mysqli_fetch_assoc($result))
		{
			$temp[] = 
				array('username' => ''. $row['first_name'] .' ' . $row['last_name'] 
					, 'updated_at' => ''.$row['updated_at']
					, 'msg' => $row['message']
					, 'msg_id' => $row['id']);
		}
	}
	else
	{
		$temp = array('error' => 'Failed to retrieve messages');
	}
	return $temp;
}

function addMessage($connection, $message)
{
	$insert ="INSERT INTO messages (user_id, message, created_at, updated_at) 
			VALUE (1,'". $message ."',Now(), Now())";
	return mysqli_query($connection, $insert);
}

function getComments($connection, $messageID)
{
	$query = "SELECT first_name, last_name, comment, comments.updated_at 
	FROM thewall.users JOIN thewall.comments ON comments.user_id = users.id 
	WHERE comments.message_id = ". $messageID . 
	" ORDER BY comments.updated_at ASC";
	return mysqli_query($connection, $query);
}

function addComment($connection, $message)
{
	$insert ="INSERT INTO comments (message_id, user_id, comment, created_at, updated_at) 
			VALUE (". $_POST['msgID'] . " ,1,'". $message ."',Now(), Now())";
		var_dump($insert);
	return mysqli_query($connection, $insert);
}
?>