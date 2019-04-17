<?php

session_start();

require 'connexion.php';

function runJS($command) 
{
  echo '<script type="text/javascript">'.$command.'</script>';
}

$hash = $_GET['token_activation'];

if (!is_string($hash)) {
	runJS('alert("Invalid Link")');
	runJS('window.location.replace("./index.php")');
}

$req1 = $db->prepare('SELECT token_activation, id FROM users WHERE token_activation = ?');
$req1->execute(array($hash));
$req1 = $req1->fetchAll();

if (empty($req1))
{
	runJS('alert("Invalid Link")');
	runJS('window.location.replace("./index.php")');
}
else
{
	$req2 = $db->prepare('UPDATE users SET token_activation = null WHERE token_activation = ?');
	$req2->execute(array($hash));

	$id = $req1[0]['id'];

	mkdir("public/photo/users/$id");

	runJS('alert("Account activated")');
	runJS('window.location.replace("./login.php")');

}

?>