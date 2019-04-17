
<?php

try 
{

//connexion a la base de donnee
$dbh = new PDO('mysql:host=localhost;dbname:camagru', 'root', 'korlandi', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
//creation de la base de donnee
$req = $dbh->prepare('DROP DATABASE IF EXISTS `camagru`;');
$req->execute();
$req = $dbh->prepare('CREATE DATABASE IF NOT EXISTS `camagru`;');
$req->execute();
//utilisation de la base de donnee camagru
$req = $dbh->prepare('USE `camagru`;');
$req->execute();

//user
$req_users = $dbh->prepare("CREATE TABLE IF NOT EXISTS `camagru`.`users` (`id` int(255) UNSIGNED
        AUTO_INCREMENT PRIMARY KEY, `mail` varchar(255) NOT NULL, `login`
        varchar(255) NOT NULL, `passwd` varchar(255) NOT NULL, `token_activation` varchar(255), `token_resetpasswd` varchar(255), `profil_picture` varchar(255) 
        DEFAULT NULL, `notif` int(255) DEFAULT 1);");
$req_users->execute();

//photo
$req_photo = $dbh->prepare("CREATE TABLE IF NOT EXISTS `camagru`.`photo` (`id` int(255) UNSIGNED
    	AUTO_INCREMENT PRIMARY KEY, `path` varchar(255) NOT NULL, `id_user`
    	int(255) NOT NULL, `creation_date` DATE NOT NULL, `image` varchar(255) NOT NULL);");
$req_photo->execute();

//commentaire
$req_comm = $dbh->prepare("CREATE TABLE IF NOT EXISTS `camagru`.`comment` (`id` int(255) UNSIGNED
    	AUTO_INCREMENT PRIMARY KEY, `id_photo` int(255) NOT NULL, `id_user`
    	int(255) NOT NULL, `text` TEXT);");
$req_comm->execute();

//like
$req_like = $dbh->prepare("CREATE TABLE IF NOT EXISTS `camagru`.`like` (`id` int(255) UNSIGNED
    	AUTO_INCREMENT PRIMARY KEY, `id_photo` int(255) NOT NULL, `id_user`
    	int(255) NOT NULL);");
$req_like->execute();
}

//insertion des miniature

catch(PDOexception $err ) 
{

	print('ERROR: '.$err->getMessage()."\n");

}



echo '<script type="text/javascript">alert("DATABASE CREATED BANGBANG")</script>';
echo '<script type="text/javascript"> window.location.replace("../index.php") </script>';

?>
