<?php
session_start();
?>


<!DOCTYPE html>
<html>
<head>
	<title>Camagru</title>
	<meta charset="utf-8">

	<link rel="icon" href="./public/favicon.ico" />

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<link rel="stylesheet" type="text/css" href="./style/stylesheet.css">
	
</head>
<body>

<?php include 'header.php'; ?>

<div class="middle_gap"></div>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <form method="POST" action="login.php">
	  <div class="form-group">
	  	<h1 class="display-4">Log In !</h1>
	  	<div class="middle_gap"></div>
	    <label for="exampleInputEmail1">Username</label>
	    <input type="text" class="form-control" name="username" aria-describedby="emailHelp" placeholder="Enter username" required>
	  </div>
	  <div class="form-group">
	    <label for="exampleInputPassword1">Password</label>
	    <input type="password" class="form-control" name="passwd" placeholder="Password" required>
	  </div>
	  <button type="submit" class="btn btn-primary" name="submit" value="OK">Log In</button></br>
	  <a href="forgot_passwd.php"> Forgot password ?</a><br/>
	  <a href="register.php"> Don’t have an account? Sign Up</a>
	  
	</form>

	 <div class="image"> 

	 	
	 </div>
  </div>
</div>

 
</body>
<?php include 'footer.php'; ?>
</html>

<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<?php

require 'connexion.php';

function runJS($command) 
{
  echo '<script type="text/javascript">'.$command.'</script>';
}

function echoError($error)
{
  echo '<div class="container error"<div class="row"><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
                  '.$error.'
            </div></div></div>';
}

if (isset($_POST['submit']) && $_POST['submit'] == 'OK')
{
	if (isset($_POST['username']) && $_POST['username'] !== "" && isset($_POST['passwd']) && $_POST['passwd'] !== "")
	{
		$login 			= $_POST['username']; 
		$passwd 		= $_POST['passwd'];

		$req = $db->prepare('SELECT * FROM users WHERE login = ?');
        $req->execute(array($login));
        $req = $req->fetchAll();

        if (!empty($req))
        {
        	if(!$req[0]['token_activation'])
        	{
        		$passwd = hash('whirlpool', $passwd);


        		if ($req[0]['passwd'] === $passwd)
        		{
        			$_SESSION['username'] 		= $login;
        			$_SESSION['mail']     		= $req[0]['mail'];
        			$_SESSION['id'] 	  		= $req[0]['id'];
        			$_SESSION['profil_picture'] = $req[0]['profil_picture'];
        			$_SESSION['notif'] 			= $req[0]['notif'];

        			runJS('window.location.replace("./index.php")');
        		}
        		else
        		{
                    echoError('Sorry, your password was incorrect.');
      //   			echo '<div class="row" ><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
  							 
						// </div></div>';
        		}
        	}
        	else
        	{
        		echoError('You must activate your account ...!');
        	}
        }
        else
        {
        	echoError('The username you entered doesn\'t belong to an account. Please check your username and try again.');
        }
	}
	else
	{
		echoError('Empty fields!');
	}
}

?>