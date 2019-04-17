

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

<!-- checked -->

<?php include 'header.php'; ?>

<div class="middle_gap"></div>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
<h1 class="display-4">Create account !</h1>
    <form method="POST" action="register.php" class="myform" id="formRegister">
      <div class="small_gap"></div>
      <div class="form-group col-lg-7">
        <input type="email" class="form-control" name="mail" aria-describedby="emailHelp" placeholder="Enter email" required>
      </div>

      <div class="small_gap"></div>
      <div class="form-group col-lg-7">
        <input type="text" class="form-control" pattern="^[A-Za-z][A-Za-z0-9]{5,30}$"  name="username" onchange="" placeholder="Username" required>
      </div>
      <div class="small_gap"></div>
      <div class="form-group col-lg-7">
        <input type="password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" name="passwd" placeholder="Password" required>
      </div>
      <div class="small_gap"></div>
      <div class="form-group col-lg-7">
        <input type="password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" name="passwd2" placeholder="Re-enter Password" required>
      </div>
      <div class="small_gap"></div>
      <button type="submit" class="btn btn-primary" name="submit" value="OK">Submit</button>
    </br>
      <a href="login.php">Already have an account? Log in</a>

    </form>

     </div>
</div>

<div class="middle_gap"></div>


</body>
<?php include 'footer.php'; ?>
</html>



<?php
require 'connexion.php';

//Fonction utile

function runJS($command) 
{
  echo '<script type="text/javascript">'.$command.'</script>';
}

function echoError($error)
{
  echo '<div class="container" style="position:relative;bottom:550px;"><div class="row"><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
                  '.$error.'
            </div></div></div>';
}

//Check input values

function check_value($login, $passwd, $mail)
{
  if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
  {
    echoError('Enter a valid e-mail address.'); 
    exit(0);
  }
  else if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,30}$/', $login))
  {
    echoError('Invalid username! Must be between 5 and 50 characters long. Can contain any letters from a to z and any numbers from 0 through 9.');
    exit(0);
  }
else if (strlen($passwd) < 8 || !preg_match("#[0-9]+#", $passwd || !preg_match("#[a-zA-Z]+#", $passwd))) 
  {
    echoError('Invalid password! password must be at least 8 characters long including a number and an uppercase letter.');
    exit(0);
  }

}


//Mailer
function SendThisMail($to, $hashmail ,$url, $login, $mail) 
{

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "To: $login <$mail>" . "\r\n";
    $headers .= 'From: Camagru <Camagru@student.43.fr>' . "\r\n";
    $subject = 'Account Verification (Camagru)';
    $message_body = "
           <html>
            <head>
             <title>Lien de validation Camagru</title>
            </head>
            <body>
             Hello $login,<br />
          Thank you for signing up!<br />
          Please click on this link to active your account:<br />
          <a href='$url/confirmation_account.php?token_activation=$hashmail'>Valider</a>
            </body>
           </html>
           ";
    mail($to, $subject, $message_body, $headers);
}

if (isset($_POST['submit']))
{
	if (isset($_POST['mail']) && $_POST['mail'] !== "" &&  isset($_POST['username']) && $_POST['username'] !== "" && isset($_POST['passwd']) && $_POST['passwd'] !== "" &&  isset($_POST['passwd2']) && $_POST['passwd2'] !== "" )
	{

		if ($_POST['passwd'] === $_POST['passwd2'])
		{
      $login = $_POST['username'];
      $passwd = $_POST['passwd'];
      $mail = $_POST['mail'];

      check_value($login, $passwd, $mail);

      $req = $db->prepare("SELECT * FROM users WHERE mail = ? OR login = ?");
      $req->execute(array($mail, $login));
      $req = $req->fetchAll();

      if (!empty($req))
      {
        echoError('This username or mail is already taken.');
      }
      else
      {
        $passwd = hash("whirlpool", $_POST['passwd']);
        $hashmail = password_hash($mail, PASSWORD_BCRYPT);
        $req = $db->prepare('INSERT INTO users (mail, login, passwd, token_activation) VALUES(?, ?, ?, ?)');

        if (!$req->execute(array($mail, $login, $passwd, $hashmail)))
        {
            echo "\PDO::errorInfo():\n";
            die();
        }

        runJS('alert("A validation email has been sent to you!")');
        runJS('window.location.replace("./login.php")');

        SendThisMail($mail, $hashmail , $url, $login, $mail); 

      }
		}
    else
    {
      echoError('Password are not the same!');
    }
	}
  else
  {
    echoError('Please complete all fields!');
  }
}

?>