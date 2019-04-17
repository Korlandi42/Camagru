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
    <link rel="stylesheet" type="text/css" href="./style/gallery.css">

    <style type="text/css">

        .comments .caption {
            word-break: break-all;
        }

    </style>
	
</head>
<body>

<?php include 'header.php'; ?>

<div class="jumbotron jumbotron-fluid ">

      <h1 class="display-4" style="text-align: center;">Gallery</h1>

<div class="container">

  	

  <?php 

    require 'connexion.php';

    if (!empty($_GET['page']) && !is_numeric($_GET['page']))
    {
         echo '<script type="text/javascript">window.location.replace("./index.php")</script>';

    }

    $page = (!empty($_GET['page']) ? $_GET['page'] : 1);
    $limite = 6;
    $debut = ($page - 1) * $limite;

    $req = $db->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM photo ORDER BY id DESC LIMIT :limite OFFSET :debut");
    $req->bindValue('debut', $debut, PDO::PARAM_INT);
    $req->bindValue('limite', $limite, PDO::PARAM_INT);
    $req->execute();

    $resultFoundRows = $db->prepare('SELECT FOUND_ROWS()');
    $resultFoundRows->execute();
    $nombredElementsTotal = $resultFoundRows->fetchColumn();

    $req = $req->fetchAll();

    echo '<section class="container">
    <div class="row card-display">';
    foreach ($req as $row) 
    {

        $query = $db->prepare('SELECT login FROM users WHERE id = ?');
        $query->execute(array($row['id_user']));
        $query = $query->fetchAll();

        $login = $query[0]['login'];

        $req1 = $db->prepare('SELECT COUNT(*) FROM `like` WHERE id_photo = ?');
        $req1->execute(array($row['id']));
        $req1 = $req1->fetchAll();

        $like = $req1[0];


        echo ' <div class= "insta fade-scroll ">
                    
                    <div class="top-insta">';

                        echo '<p class="user">' . $login . '</p>';
        echo '</div>

                    <div class="post">

                        <div class="overlay">
                        
                        
                        </div>

                        <img src="' . $row['path'] . '" style="max-height: 250px">
                        
                    </div>

                    <div class="footer">
                      
                      <div class="react">
                      <form method="POST" action="index.php" style="display:inline-flex;">
                            <input type="hidden" name="id_photo" value="' . $row['id'] . '">
                            <input type="hidden" name="page" value="'. $page.'">
                            <button type="hidden" name="like" value="OK" style="border:0px;"><span class="love"></button>
                            <p style="margin-left:13px;color:black;">' . $like[0] . '</p>
                        </form>
                      </div>
                      <div class="comments">';

        $req = $db->prepare('SELECT `text`, `id_user` FROM `comment` WHERE id_photo = ?');
        $req->execute(array($row['id']));

        while ($content = $req->fetch()) 
        {
            $query = $db->prepare('SELECT login FROM users WHERE id = ?');
            $query->execute(array($content['id_user']));
            $query = $query->fetchAll();

            $login = $query[0]['login'];

            echo ' <div class="caption">
                            ' . $login . ' : ' . $content['text'] . '
                        </div>';
        }

        echo '         
        </div>
                <form method="POST" action="index.php" style="display:inline-flex;">';

               echo  ' <div class="comment-section">
                                    <input type="hidden" name="id_photo" value="' . $row['id'] . '">
                                    <input type="hidden" name="page" value="'. $page.'">
                                    <input type="text" id="cmnt" name="comment" placeholder="Add a comment...">
                                </div>
                            </div>
                            

                            </form>
                            </div>
          
            ';
    }
    echo " </div>
    </section>";


function runJS($command) 
{
  echo '<script type="text/javascript">'.$command.'</script>';
}


// Ajout de commentaire
if (isset($_POST['comment']) && $_POST['comment'] != "") 
{

    $text = $_POST['comment'];
    $page = $_POST['page'];

    if (isset($_SESSION['username'])) 
    {
        $id_photo = $_POST['id_photo'];
        $id_user = $_SESSION['id'];
        $login        = $_SESSION['username'];



        $req = $db->prepare('INSERT INTO `comment` (id_photo, id_user, `text`) VALUES(?, ?, ?)');
        $req->execute(array($id_photo, $id_user, htmlspecialchars($text)));

        $req2 = $db->prepare('SELECT * FROM `users` JOIN `photo` ON photo.id_user = users.id WHERE photo.id = ?');
        $req2->execute(array($id_photo));
        $req2 = $req2->fetchAll();

        $mail = $req2[0]['mail'];
        $login_photo = $req2[0]['login'];
        $notif = $req2[0]['notif'];



        if ($notif == 1) 
        {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= "To: $login_photo <$mail>" . "\r\n";
            $headers .= 'From: Camagru <Camagru@student.42.fr>' . "\r\n";
            $to = $mail;
            $subject = "$login has commented your photo(Camagru)";
            $message_body = "
			     <html>
			      <head>
			       <title>You have a new comment on your photo</title>
			      </head>
			      <body>
			       Hello $login_photo,
			       $login has commented your photo. Log in on Camagru to check that out!
			      </body>
			     </html>
			     ";


            mail($to, $subject, $message_body, $headers);
        }

        runJS('window.location.replace("./index.php?page='.$page.'")');
    } 
    else 
    {
        runJS('alert("you must be logged in")');
        runJS(' window.location.replace("./index.php?page='.$page.'")');
    }
}



//Ajout et delete de like

if (isset($_POST['like']) && $_POST['like'] == "OK") 
{
    if (isset($_SESSION['username'])) 
    {
        $id_user      = $_SESSION['id'];
        $id_photo     = $_POST['id_photo'];
        $page         = $_POST['page'];
        $login        = $_SESSION['username'];


        $req1 = $db->prepare('SELECT * FROM `like` WHERE id_photo = ? AND id_user = ?');
        $req1->execute(array($id_photo, $id_user));
        $req1 = $req1->fetchAll();

        $req2 = $db->prepare('SELECT * FROM `users` JOIN `photo` ON photo.id_user = users.id WHERE photo.id = ?');
        $req2->execute(array($id_photo));
        $req2 = $req2->fetchAll();

        $mail = $req2[0]['mail'];

        $login_photo = $req2[0]['login'];
        $notif = $req2[0]['notif'];


        if (sizeof($req1) === 0) 
        {
            $req = $db->prepare('INSERT INTO `like` (id_photo, id_user) VALUES(?, ?)');
            $req->execute(array($id_photo, $id_user));



            if ($notif == 1) 
            {
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= "To: $login_photo <$mail>" . "\r\n";
                $headers .= 'From: Camagru <Camagru@student.42.fr>' . "\r\n";
                $to = $mail;
                $subject = "$login has liked your photo(Camagru)";
                $message_body = "
				     <html>
				      <head>
				       <title>You have a new like on your photo</title>
				      </head>
				      <body>
				       Hello $login_photo,
				       $login has liked your photo!
				      </body>
				     </html>
				     ";


                mail($to, $subject, $message_body, $headers);
            }

            runJS(' window.location.replace("./index.php?page='.$page.'")');
        } 
        else 
        {
            $req = $db->prepare('DELETE FROM `like` WHERE id_photo = ? AND id_user = ?');
            $req->execute(array($id_photo, $id_user));

            runJS(' window.location.replace("./index.php?page='.$page.'")');
        }

    } 
    else 
    {
        runJS('alert("you must be logged in")');
        runJS(' window.location.replace("./index.php?page='.$page.'")');
    }
}


    ?>
	<br>
	<?php

$nombreDePages = ceil($nombredElementsTotal / $limite);
if ($page > 1) :
?><a style="color:#5F0C27;" href="/camagruK/index.php?page=<?php echo $page - 1; ?>">Previous</a> - <?php
endif;
for ($i = 1; $i <= $nombreDePages; $i++) :
 ?><a style="color:#5F0C27;" href="/camagruK/index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a> <?php



                                                                                                                                                                                           endfor; if ($page < $nombreDePages) :
                                                                                                                                                                                            ?> - <a style="color:#5F0C27;" href="/camagruK/index.php?page=<?php echo $page + 1; ?>">Next</a><?php
                                                                                                                                                                                        endif;

                                                                                                                                                                                        ?>
  </div>

 </div>


</body>
<?php include 'footer.php'; ?>
</html>