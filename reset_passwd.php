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

 <div class="jumbotron jumbotron-fluid">
    <div class="container">
    	<h1 class="display-4">Reset your password !</h1>
    	<form method="POST" action="reset_passwd.php">

    		<div class="form-group">
    			 <input type="password" class="form-control" name="passwd" placeholder="New password" required>
    		</div>

    		<div class="form-group">
    			 <input type="password" class="form-control" name="passwd2" placeholder="Re enter Password" required>
    		</div>
    		<input type="hidden" name="token" value="<?php echo $_GET['token_resetpasswd']; ?>">
    		<button type="submit" class="btn btn-primary" name="submit" value="OK">Submit</button>
    	</form>

    </div>
   </div>


</body>
<?php include 'footer.php'; ?>
</html>



<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<?php
require 'connexion.php';
	
	if(isset($_POST['submit']))
	{
		if(isset($_POST['passwd']) && $_POST['passwd'] !== "" && isset($_POST['passwd2']) && $_POST['passwd2'] !== "")
		{
			if ($_POST['passwd'] === $_POST['passwd2'])
			{
				$passwd = $_POST['passwd'];
				if (strlen($passwd) >= 8 || preg_match("#[0-9]+#", $passwd || preg_match("#[a-zA-Z]+#", $passwd)))
				{
					$passwd  = hash('whirlpool', $passwd);
					$hashmail = $_POST['token'];

					$req1 = $db->prepare('SELECT token_resetpasswd FROM users WHERE token_resetpasswd = ?');
					$req1->execute(array($hashmail));
					$req1 = $req1->fetchAll();

					if (empty($req1))
					{
						echo '<script type="text/javascript">alert("Invalid Link")</script>';
                    	echo '<script type="text/javascript"> window.location.replace("./index.php") </script>';
					}
					else 
					{
						$req2 = $db->prepare('UPDATE users SET passwd = ?, token_resetpasswd = null WHERE token_resetpasswd = ?');
						$req2->execute(array($passwd, $hashmail));
						
						echo '<script type="text/javascript">alert("Successfully changed your password")</script>';
                    	echo '<script type="text/javascript"> window.location.replace("./login.php") </script>';
					}
				}
				else
				{
					echo '<div class="container"><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
  							Invalid password! password must be at least 8 characters long including a number and an uppercase letter.
						</div></div>';
				}
			}
			else
			{
				echo '<div class="container"><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
  							Passwords do not match.
						</div></div>';
			}	
		}
		else
		{
			echo '<div class="container"><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
  							Please complete all fields!
						</div></div>';
		}
	 }
	
?>
