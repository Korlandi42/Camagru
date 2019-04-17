<?php
session_start();

require 'connexion.php';
if (!isset($_SESSION['username']))
{
	echo '<script type="text/javascript"> window.location.replace("./index.php") </script>';
	die();
}
$req = $db->prepare("SELECT notif FROM users WHERE login = ?");
$req->execute(array($_SESSION['username']));
$req = $req->fetchAll();
$_SESSION['notif'] = $req[0]['notif'];

?>


<!DOCTYPE html>
<html>
<head>
	<title>Camagru</title>
	<meta charset="utf-8">

	<link rel="icon" href="./public/favicon.ico" />

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<link rel="stylesheet" type="text/css" href="./style/stylesheet.css">
	<link rel="stylesheet" type="text/css" href="./style/input_file.css">
	
</head>
<body>

<?php include 'header.php'; ?>

<div class="jumbotron jumbotron-fluid">
	<h1 class="display-4" style="text-align: center;">Edit Your Profil !</h1>
  <div class="container">
  <div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" style="display:none;" id="alert" role="alert" ></div>
  	 <h2 class="display-6" > Change your username </h2>


  	<!-- change username -->
  	<form action="setup_profil.php" method="POST">

  		<div class="form-group row">
		    <label for="username" class="col-sm-2 col-form-label">Enter your new username: </label>
		    <div class="col-sm-10">
		      <input type="text" class="form-control" name="username"  pattern="^[A-Za-z][A-Za-z0-9]{5,30}$" value="<?php echo $_SESSION['username'];?>" required>
	    	</div>
	  </div>

	  <input type="submit" name="changeUsername" class="btn btn-outline-light">


  	</form>

  	<div class="middle_gap"></div>

  		<!-- change passwd -->
  	<h2 class="display-6"> Change your password </h2>


  	<form action="setup_profil.php" method="POST">

  		<div class="form-group row">
		    <label for="username" class="col-sm-2 col-form-label">Enter your old password: </label>
		    <div class="col-sm-10">
		      <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" class="form-control" name="oldpasswd" required>

	    	</div>
	  </div>

	  <div class="form-group row">
		    <label for="username" class="col-sm-2 col-form-label">Enter your new password: </label>
		    <div class="col-sm-10">
		      <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" class="form-control" name="newpass" required>

	    	</div>
	  </div>

	  <div class="form-group row">
		    <label for="username" class="col-sm-2 col-form-label">Re enter your new password: </label>
		    <div class="col-sm-10">
		      <input type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" class="form-control" name="newpass2" required>

	    	</div>
	  </div>

	  <input type="submit" name="changePasswd" class="btn btn-outline-light">


  	</form>


	<div class="middle_gap"></div>
	
  		<!-- change mail -->

  	<h2 class="display-6"> Change your Email</h2>

  	<form action="setup_profil.php" method="POST">

  		<div class="form-group row">
		    <label for="username" class="col-sm-2 col-form-label">Enter your new mail: </label>
		    <div class="col-sm-10">
		      <input type="text" class="form-control" name="mail" required value="<?php echo $_SESSION['mail'];?>">

	    	</div>
	  </div>
	  <input type="submit" name="changeMail" class="btn btn-outline-light">

  	</form>

  	<div class="middle_gap"></div>
	

  	<h2 class="display-6"> Add a profil picture </h2>

  	<form action="setup_profil.php" method="POST" enctype="multipart/form-data">

  		<input type="file" name="file" id="file" class="inputfile" />
		<label for="file">Choose a file</label>

		<input type="submit" name="changePicture" class="btn btn-outline-light">

  	</form>

  	<div class="middle_gap"></div>
	
  		<!-- change mail -->

  	<h2 class="display-6"> Notifications</h2>

  	<form action="setup_profil.php" method="POST">

			<p><input type="checkbox" id="mail" name="mail" <?php echo ($_SESSION['notif'] == 1 ? 'checked' : ''); ?>/>
			<label for="mail">Receive a mail when you got a new like or comment</label></p>
	  		<input type="submit" name="changeNotif" class="btn btn-outline-light">
  	</form>
  	
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

function check_format($src)
{
	$extension_upload = strtolower(substr(strrchr($src['file']['name'], '.'), 1));

	if (!$src['file']['tmp_name']) 
	{
		runJS('alert("Unsupported format or error when uploading the photo")');
		runJS('window.location.replace("setup_profil.php")');

		exit(0);
	}
	if (!list($witdh, $height) = getimagesize($src['file']['tmp_name']))
	{
		runJS('alert("Unsupported format or error when uploading the photo")');
		runJS('window.location.replace("setup_profil.php")');

		exit(0);
	}
	if ($witdh == 0 || $height == 0) 
	{
		runJS('alert("Unsupported format or error when uploading the photo")');
		runJS('window.location.replace("setup_profil.php")');

		exit(0);
	}
}

function check_type($src)
{
	$type = $src['file']['type'];
	if ($type == 'image/jpeg' || $type == 'image/x-icon' || $type == 'image/png')
		return true;
	else
		return false;
}



//Changement de photo de profil

if (isset($_POST['changePicture'])) 
{
	check_format($_FILES);

	if (check_type($_FILES) == true) 
	{
		$src = file_get_contents($_FILES['file']['tmp_name']);
		$src = 'data:image/png;base64,' . base64_encode($src);
		$src = str_replace('data:image/png;base64,', '', $src);
		$src = str_replace(' ', '+', $src);
		$src = base64_decode($src);

		$id = $_SESSION['id'];
		$name_picture = $id . '.jpg';

		file_put_contents("public/photo/users/$id/$name_picture", $src);

		$_SESSION['profil_picture'] = "public/photo/users/$id/$name_picture";
		
		$req = $db->prepare("UPDATE users SET profil_picture = ? WHERE login = ?");
		$req->execute(array("public/photo/users/$id/$name_picture", $name));

		runJS('window.location.replace("profil.php")');

	} 
	else 
	{
		runJS('alert("Please download a  jpeg,icon or png photo !")');
		runJS('window.location.replace("setup_profil.php")');
	}
}


//Changement de username

if (isset($_POST['changeUsername'])) 
{
	if (isset($_POST['username']) && $_POST['username'] !== "") 
	{
		$login = $_POST['username'];

		if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,30}$/', $login)) 
		{
			
				  $msg = "Invalid username! Must be between 5 and 50 characters long. Can contain any letters from a to z and any numbers from 0 through 9.";
				  runJS("div = document.getElementById('alert'); div.style.display= block; div.innerHTML=".$msg.";");
		} 
		else 
		{

			$req = $db->prepare("SELECT * FROM users WHERE login = ?");
			$req->execute(array($login));
			$req = $req->fetchAll();

			if (!empty($req)) 
			{
				$msg = "This username is already taken.";
				runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");
			}
			else 
			{
				$oldlogin = $_SESSION['username'];
				$req2 = $db->prepare('UPDATE users SET login = ? WHERE login = ?');
				$req2->execute(array($login, $oldlogin));

				$_SESSION['username'] = $login;

				runJS('alert("Your username has been successfully changed")');
				runJS('window.location.replace("profil.php")');

			}
		}
	} 
	else 
	{
		$msg = "Please complete all fields!";
				runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");

	}
}



//Changement de mot de passe

if (isset($_POST['changePasswd'])) 
{
	if (isset($_POST['oldpasswd']) && $_POST['oldpasswd'] !== "" && isset($_POST['newpass']) && $_POST['newpass'] !== "" && isset($_POST['newpass2']) && $_POST['newpass2'] !== "") 
	{
		if ($_POST['newpass'] === $_POST['newpass2']) 
		{
			$oldpasswd = $_POST['oldpasswd'];
			$newpass = $_POST['newpass'];

			if (strlen($newpass) < 8 || !preg_match("#[0-9]+#", $newpass || !preg_match("#[a-zA-Z]+#", $newpass))) 
			{

				$msg = "Invalid password! password must be at least 8 characters long including a number and an uppercase letter.";
				runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");

			} 
			else 
			{
				$login = $_SESSION['username'];
				$req = $db->prepare("SELECT passwd FROM users WHERE login = ?");
				$req->execute(array($login));
				$req = $req->fetchAll();

				if (empty($req)) 
				{
					
				$msg = "error !";
				runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");	
				} 
				else 
				{
					$oldpasswd = hash("whirlpool", $oldpasswd);
					$newpass = hash("whirlpool", $newpass);

					if ($req[0]['passwd'] == $oldpasswd) 
					{
						$req2 = $db->prepare('UPDATE users SET passwd = ? WHERE login = ?');
						$req2->execute(array($newpass, $login));

						runJS('alert("Your password has been successfully changed")');
						runJS('window.location.replace("profil.php")');
					} 
					else 
					{
						$msg = "Sorry, your password was incorrect.";
						runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");	

					}
				}
			}
		}
	} 
	else 
	{
		$msg = "Please complete all fields!";
		runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");

	}
}



//Changement de Mail

if (isset($_POST['changeMail'])) 
{
	if (isset($_POST['mail']) && $_POST['mail'] !== "") 
	{
		$mail = $_POST['mail'];

		if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) 
		{
			$msg = "Enter a valid e-mail address. example@mail.com";
			runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");

		} 
		else 
		{
			$req = $db->prepare("SELECT * FROM users WHERE mail = ?");
			$req->execute(array($mail));
			$req = $req->fetchAll();

			if (!empty($req)) 
			{
				$msg = 'Another account is using this mail ' . $mail;
				runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");

			} 
			else 
			{
				$oldmail = $_SESSION['mail'];
				$req2 = $db->prepare('UPDATE users SET mail = ? WHERE mail = ?');
				$req2->execute(array($mail, $oldmail));

				$_SESSION['mail'] = $mail;
				
				runJS('alert("Your mail has been successfully changed")');
				runJS('window.location.replace("profil.php")');

			}
		}
	} 
	else 
	{
		$msg = 'Please complete all fields!';
		runJS("div = document.getElementById('alert'); div.style.display= 'block'; div.innerHTML='".$msg."';");

	}
}

//Gestion des notification

if (isset($_POST['changeNotif'])) 
{
	$req = $db->prepare('SELECT notif FROM users WHERE login = ?');
	$req->execute(array($_SESSION['username']));
	$req = $req->fetch();


	if ($req['notif'][0] == 0) 
	{

		$req = $db->prepare("UPDATE users SET notif = ? WHERE login = ?");
		$req->execute(array(1, $_SESSION['username']));

		$_SESSION['notif'] = 1;

		runJS('alert("Notifications have been enabled")');
		runJS('window.location.replace("profil.php")');
	} 
	else 
	{
		$req = $db->prepare("UPDATE users SET notif = ? WHERE login = ?");
		$req->execute(array(0, $_SESSION['username']));

		$_SESSION['notif'] = 0;

		runJS('alert("Notifications have been disabled")');
		runJS('window.location.replace("profil.php")');
	}
}

?>








































