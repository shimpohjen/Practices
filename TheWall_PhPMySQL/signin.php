<!doctype html>
<?php session_start();
//if you can get to this page even though there is a signed in session, 
//something is wrong. To be safe, we'll destroy login variables. 
unset($_SESSION['login']);	
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="SignIn.css">
		<meta charset="utf-8">
		<title>The Awesome Wall</title>
		<script type="text/javascript">
		</script>
	</head>
	<body>
		<form action="process.php" method="post">
			<input type="hidden" name="type" value="sign-in">
			Email:&emsp;&emsp; <input type="text" name="email" value=
			<?php if(isset($_SESSION['login_error'])){echo '"'.$_SESSION['login_attempt']['email'].'"';} ?>
			>
			<br/>
			Password:&ensp; <input type="password" name="password"><br/>
			<div>
				<input type="submit" value="log in">
				<input type="button" value="cancel" onClick="location.href='index.php'"> 
			</div>
		</form>
		<div class='errors'>
			<?php 
				if(isset($_SESSION['login_error']['email'])){
					echo $_SESSION['login_error']['email'].'<br/>';
					unset($_SESSION['login_error']['email']);
				}
				if(isset($_SESSION['login_error']['password'])){
					echo $_SESSION['login_error']['password'].'<br/>';
					unset($_SESSION['login_error']['password']);
					} 
				?>
		</div>
	</body>
</html>