<?php
session_start();

require 'connexion.php';

if (!isset($_SESSION['username']))
{
  echo '<script type="text/javascript"> window.location.replace("./index.php") </script>';
  die();
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Camagru</title>
	<meta charset="utf-8">
	<link rel="icon" href="./public/favicon.ico" />

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

	<link rel="stylesheet" type="text/css" href="./style/stylesheet.css">
	<!-- <link rel="stylesheet" type="text/css" href="./style/profil.css"> -->

<style type="text/css">

.image{
 width: 28%;
 border: 1.5px solid #ccc;
 padding: 10px;
 margin: 2% 1.5%;
 flex-grow: 1;
 background-color: #ecf0f1;
 max-width: 350px;

}

.image img{
 max-width: 100%;
 margin: 0 auto;
 max-height: 235px;

}

.image:hover{
 box-shadow: 0 1px 30px rgba(0,0,0,.3);
 transform: scale(1.10);
 border: none;
 transition: box-shadow 0.2s ease, transform 0.2s ease;
 z-index: 1;
}

.image p{
 padding: 10px;
}

 .container{
 	height: 100%;
 	align-content: center;
 }

 .image_outer_container{
 	margin-top: auto;
 	margin-bottom: auto;
 	border-radius: 50%;
 	position: relative;
 }

 .image_inner_container{
 	border-radius: 50%;
 	padding: 5px;
  background: #833ab4; 
  background: -webkit-linear-gradient(to bottom, #fcb045, #fd1d1d, #833ab4); 
  background: linear-gradient(to bottom, #fcb045, #fd1d1d, #833ab4);
 }
 .image_inner_container img{
 	height: 200px;
 	width: 200px;
 	border-radius: 50%;
 	border: 5px solid white;
 }

 .image_outer_container .green_icon{
   background-color: #4cd137;
   position: absolute;
   right: 30px;
   bottom: 10px;
   height: 30px;
   width: 30px;
   border:5px solid white;
   border-radius: 50%;
 }

#photos {
  opacity: .88;
}

#photos img {
  width: 30%;
  margin: 2px;
}

.image{
 width: 28%;
 border: 1.5px solid #ccc;
 padding: 10px;
 margin: 2% 1.5%;
 flex-grow: 1;
 background-color: #ecf0f1;
}

.image img{
 max-width: 100%;
 margin: 0 auto;
}

.image:hover{
 box-shadow: 0 1px 30px rgba(0,0,0,.3);
 transform: scale(1.10);
 border: none;
 transition: box-shadow 0.2s ease, transform 0.2s ease;
 z-index: 1;
}

.image p{
 padding: 10px;
}

	</style>
	
</head>
<body>
<?php 

$page = (!empty($_GET['page']) ? $_GET['page'] : 1);
$limite = 5;
$debut = ($page - 1) * $limite;

$query = $db->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM photo WHERE login = :login ORDER BY date_creation DESC LIMIT :limite OFFSET :debut");
$query->bindValue('login', $_SESSION['username'], PDO::PARAM_INT);
$query->bindValue('debut', $debut, PDO::PARAM_INT);
$query->bindValue('limite', $limite, PDO::PARAM_INT);
$query->execute();

$resultFoundRows = $db->prepare('SELECT FOUND_ROWS()');
$resultFoundRows->execute();
$nombredElementsTotal = $resultFoundRows->fetchColumn();
?>

<?php include 'header.php'; ?>

<div class="jumbotron jumbotron-fluid">
  

  		<!--  Photo de Profil -->

		<div class="d-flex justify-content-center h-100">
			<div class="image_outer_container">
				<div class="image_inner_container">
					<?php if ($_SESSION['profil_picture'] != NULL) {

						$req = $db->prepare("SELECT profil_picture FROM users WHERE login = ? ");
						$req->execute(array($_SESSION['username']));
						$req = $req->fetchAll();
						echo '<img src="'.$_SESSION['profil_picture'].'">';
					 ?>	

					<?php } else { ?>

						<img src="public/default.jpg">
						
					<?php }?>
				</div>
			</div>
		</div>

		<!-- Username -->

		<p style="text-align: center;font-size: 30px;"><?php echo $_SESSION['username'];?></p>

		<div class="row" style="text-align:center;">
			<div class="col-lg-12 col-md-12 col-xs-12">
				<a href="setup_profil.php" ><button type="button" class="btn btn-outline-light">Edit your profil</button></a>
			</div>
		</div>
		
		<div class="container">
		

		<!-- Photo du user -->

		<?php 

			$req = $db->prepare("SELECT `path`, `id` FROM photo WHERE id_user = ? ORDER BY id DESC");
			$req->execute(array($_SESSION['id']));

			while ($content = $req->fetch())
			{
				$path 	= $content["path"];
				$id 	= $content["id"];

				echo ' <form method="POST" action="profil.php" style="display:inline-flex;"> 	
						  <article class="image">
						    <img src="'.$path.'"/>
						    <input type="hidden" name="IDPhoto" value="'.$id.'">
							<input type="hidden" name="path" value="'.$path.'">
							<br>
							<div class="small_gap"></div>
							<button type="submit"  name="delete" class="btn btn-outline-dark" style="text-align:center;">Delete</button>
						    <div class="small_gap"></div>
						  </article>		
						</form>
					';
			}
		?>

	

  </div>
 </div>
</body>
<?php include 'footer.php'; ?>
</html>


<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->



<?php 

	if (isset($_POST['delete']))
	{
		$id = $_POST['IDPhoto'];


		$req = $db->prepare("DELETE FROM photo WHERE id = ? AND id_user = ?");
		$req->execute(array($id, $_SESSION['id']));
		$req = $db->prepare("DELETE FROM `like` WHERE id_photo = ? AND id_user = ?");
		$req->execute(array($id, $_SESSION['id']));
		$req = $db->prepare("DELETE FROM comment WHERE id_photo = ? AND id_user = ?");
		$req->execute(array($id, $_SESSION['id']));

		echo '<script type="text/javascript"> window.location.replace("profil.php") </script>';
	}
?>

