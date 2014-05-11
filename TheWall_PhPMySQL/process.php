<?php
session_start();
require_once('connection.php');
require_once('common.php');

//if is a sign in submission
if($_POST['type'] == 'sign-in')
{
	//at this point, if someone attempt to sign in. We assume that the previous sign-in 
	//session is no longer valid and should be destroyed if it exists. 
	session_destroy();
	//have to start session again to use the session variables
	session_start();

	//validate if the input is valid
	$_SESSION['login_attempt']['email'] = $_POST['email'];
	$pass = $_POST['password'];
	if(filter_var($_SESSION['login_attempt']['email'], FILTER_VALIDATE_EMAIL) == FALSE)
	{
		$_SESSION['login_error']['email'] = "This not a proper email address";
	}
	if(empty($pass))
	{
		$_SESSION['login_error']['password'] = "Provided password is empty";		
	}
	if(isset($_SESSION['login_error']))
	{
		header('location:signin.php');
		exit();
	}
	else
	{
		//at this point, the login validations completes and both value should be valid.
		//getUser returns mysqli_result object 		
		$result = getUser($connection, $_SESSION['login_attempt']['email'], $pass);
		if($result == false){
			$_SESSION['login_error']['result'] = "Invalid login";
			header('location:signin.php');
			exit();
		}
		elseif(mysqli_num_rows($result) == 0){
			$_SESSION['login_error']['result'] = "Something is wrong, more than one similar account existed."; 
			header('location:signin.php');
			exit();
		}
		else{
			$row = mysqli_fetch_assoc($result);
			$_SESSION['login']['User_id'] = $row['id'];
			$_SESSION['login']['user_name'] = $row['first_name']; 
			header('location:index.php');
			exit();
		}
	}

}
elseif($_POST['type'] == 'post_message')
{
	//first check if user has logged in
	if(empty($_SESSION['login']))
	{
		//then log an error to inform user that login is required before posting is allowed
		$_SESSION['post_message_error'] = "Need to login before posting a new message";
		$_SESSION['post_message_input'] = $_POST['post_message_input'];
		header('location:index.php');
		exit();
	}
	if(empty($_POST['post_message_input']))
	{
		//do nothing
		$_SESSION['post_message_error']='Post is empty';
		header('location:index.php');
		exit();
	}
	else
	{
		if(addMessage($connection, $_POST['post_message_input']))
		{
			header('location:index.php');
			exit();
		}
		else
		{
			$_SESSION['post_message_error'] = 'failed to add message to the list';
			header('location:index.php');
			exit();
		}
	}
}
elseif($_POST['type']== 'post_comment')
{
	//first check if user has logged in
	if(empty($_SESSION['login']))
	{
		//then log an error to inform user that login is required before posting is allowed
		$_SESSION['post_comment_error'][$_POST['msg-id']] = "Need to login before posting a comment";
		$_SESSION['post_comment_input'][$_POST['msg-id']] = $_POST['post_comment_input'];
		header('location:index.php#'. $_POST['msg-id']);
		exit();
	}
	if(empty($_POST['post_comment_input']))
	{
		//do nothing
		$_SESSION['post_comment_error'][$_POST['msg-id']]='Comment is empty';
		header('location:index.php');
		exit();
	}
	else
	{
		if(addComment($connection, $_POST['post_comment_input']))
		{
			header('location:index.php');
			exit();
		}
		else
		{
			$_SESSION['error'][] = 'failed to add comment to the list';
			header('location:index.php');
			exit();
		}
	}
}
elseif($_POST['type']['logout']){
	//logging out
	unset($_SESSION); 
	session_destroy();
	header('location:index.php');
	exit();
}
//unidentified submission. Don't do anything an return 
else
{
	$_SESSION['error'][] = 'Unrecognized posting';
	header('location:index.php');
	exit();	
}
?>