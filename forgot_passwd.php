<!DOCTYPE html>

<?php include_once("header.php"); ?>

<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <title>Camagru</title>

  <link rel="icon" href="./public/favicon.ico" />

  <link rel="stylesheet" type="text/css" href="./style/stylesheet.css">

</head>
<body>

  <div class="jumbotron jumbotron-fluid">
    <div class="container">
        <form method="post" id="forgot" action="forgot_passwd.php">
           <div class="form-group">
              <h1 class="display-4">Reset password</h1>
              <div class="middle_gap"></div>
              <label for="mail">Email address : </label>
              <input type="text" class="form-control" name="mail" id="mail" aria-describedby="emailHelp" placeholder="Enter email" required>
            </div>
           
            <button type="submit" class="btn btn-primary" name="submit" value="OK">Submit</button>
          
          </div>
        </form>
    </div>
  </div>


</body>
<?php include 'footer.php'; ?>
</html>

<?php

require_once 'connexion.php';

// useful functions to make the logic more readable.
function runJS($command) {
  echo '<script type="text/javascript">'.$command.'</script>';
}

function SendThisMail($to, $hashmail ,$url) {
  $headers  = 'MIME-Version: 1.0' . "\r\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
  $headers .= "To: <$to>" . "\r\n";
  $headers .= 'From: Camagru <camagru@student.42.fr>' . "\r\n";

  $subject = 'Reset your password (Camagru)';

  $message_body = "
    <html>
    <head><title>Reset your password</title></head>
    <body>
      Hello $to,<br />
      You recently requested to reset your password. Just click the button below to set a new password:<br />
      <a href='$url/reset_passwd.php?token_resetpasswd=$hashmail'>Valider</a><br />
      If you didn't request a password reset, you can ignore this email.<br />
    </body>
    </html>
  ";

  mail($to, $subject, $message_body, $headers);
}

// back-end POST params
if (isset($_POST['submit']) && $_POST['submit'] === "OK")
{
  if (isset($_POST['mail']) && $_POST['mail'] !== "")
  {

    $to = $_POST['mail']; 

    $req = $db->prepare('SELECT * FROM users WHERE mail = ?');
    $req->execute(array($to));
    $req = $req->fetchAll();

    if (!empty($req))
    {
      $hashmail = password_hash($to, PASSWORD_BCRYPT);
      $req = $db->prepare('UPDATE users SET token_resetpasswd = ? WHERE mail = ?');

      if (!$req->execute(array($hashmail, $to)))
      {
          echo "\PDO::errorInfo():\n";
          print_r($db->errorInfo());
          die();
      }

      runJS('alert("an email has been sent to you to reset your password")');
      runJS('window.location.replace("./index.php")');

      SendThisMail($to, $hashmail , $url);
    } else {
      echo '
        <div class="container"><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
          No account exists for   '.$to.'.
        </div></div>
      ';
    }
  } else {
    echo '
      <div class="container"><div class="alert alert-danger col-lg-12 col-md-12 col-xs-12" role="alert">
        Email is required.
      </div></div>
    ';
  }
}
?>