	<!doctype html>
<?php 
	session_start();
	require_once('connection.php');
	require_once('common.php');
	//when at default page, any data related to login error and attempt is not needed
	unset($_SESSION['login_attempt']);
	unset($_SESSION['login_error']);
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="description" content="anyone can post any message or comment on ths Wall">
		<title>The Awesome Wall</title>
		<link rel="stylesheet" type="text/css" href="main.css">
		<script type="text/javascript" src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js' > </script>
		<script type="text/javascript" language="javascript">
			function submit_form(){
				console.log("Submitting form"); 
				$('#logout').submit();
			}
		</script>
	</head>
	<body>
		<div class='banner'>
			<h2>Welcome to the Awesome Wall!</h2>
			<div class='login-state'>
			<?php
				if(isset($_SESSION['login']))
				{
					echo '<h3>'. $_SESSION['login']['user_name']. '</h3>';
					echo '<form id='."'logout'". 'action="'. 'process.php' . '" method = "'.'post">';
					echo '<input type="hidden" name="type" value="logout">';  
					echo '<a href="javascript:submit_form()">Log Off</a>';
					echo '</form>';
				}
				else
				{
					echo '<a href="SignIn.php">Sign In</a>';
					echo '<a href="Register.php">Register</a>';  

				}
			?>
			</div>
		</div>
		<div class='forms'>
			<?php
				//always find out if any PhP operation failed prior to loading this page. If yes, print it out here
				if(isset($_SESSION['error']))
				{
					foreach($_SESSION['error'] AS $error)
					{
						echo "<p class='error'>" . $error . "</p>";							
					}
					unset($_SESSION['error']);
				}
			?>
			<h3>Post a message</h3>
			<?php
				//always find out if any PhP operation failed prior to loading this page. If yes, print it out here
				if(isset($_SESSION['post_message_error']))
				{
					echo "<p class='error'>" . $_SESSION['post_message_error'] . "</p>";							
					unset($_SESSION['post_message_error']);
				}
			?>
			<form action="process.php" method="post">
				<input type="hidden" name="type" value="post_message">
				<textarea name="post_message_input" wrap="hard"><?php if(isset($_SESSION['post_message_input']))
						  {
						  	echo $_SESSION['post_message_input']; 
						    unset($_SESSION['post_message_input']);
						  } ?></textarea>
				<input type="submit" value="post a message">
			</form>
		</div>
		<div class='message-groups'>
			<?php
				//get messages that are stored in MySQL. The function getmessages and $connection variable are stored 
				// the other php files included in thie page 
				$result = getmessages($connection);
				if(isset($result['error']))
				{
					echo "Error: " . $result['error'];
				} 
				else
				{
					//retrieve comments for each message as well 
					foreach($result as $aMessage)
					{
						echo '<div class="message">';
						echo '<h3>'. $aMessage['username'] . ' '. $aMessage['updated_at']. '</h3>';
						echo '<p>'. $aMessage['msg']. '</p>';
						echo '<div class="commentdiv">';

						//expect to get a mysqli_result object or false 
						$comments = getComments($connection, $aMessage['msg_id']);
						if($comments == FALSE)
						{
							echo 'Error: Failed to retrieve comments';
						} 
						else
						{
							//use mysqli_result method to fetch each row as associative array
							while($c = mysqli_fetch_assoc($comments))
							{
								//apply anchor to auto-navigate back to appropriate place
								echo "<h3>".$c['first_name']. ' '. $c['last_name'].' '. $c['updated_at']."</h3>
									<p>".$c['comment']."</p>";
							}
						}
							echo '<h4 id="'. $aMessage['msg_id'] .'">Post a comment</h4>';
							//is there an error message to post on here?
							if(isset($_SESSION['post_comment_error'][$aMessage['msg_id']]))
							{
								echo "<p class='error'>" . $_SESSION['post_comment_error'][$aMessage['msg_id']] . "</p>";							
								unset($_SESSION['post_comment_error'][$aMessage['msg_id']]);
							}
							echo '<form class="commentform" action="process.php" method="Post">
									<input type="hidden" name="type" value="post_comment">
									<input type="hidden" name="msg-id" value="'. $aMessage['msg_id'] . '">
									<textarea name="post_comment_input" wrap="hard" rows="3" cols="20">';
							if(isset($_SESSION['post_comment_input'][$aMessage['msg_id']])){
								echo $_SESSION['post_comment_input'][$aMessage['msg_id']]; 
								unset($_SESSION['post_comment_input'][$aMessage['msg_id']]);
							}
							echo '</textarea>
								  <input type="submit" value="post a comment">
								</form>
								</div></div>';
					}
				}
			?>
		</div>
	</body>
</html>