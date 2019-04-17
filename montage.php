<?php

require 'connexion.php';

session_start();

if (!isset($_SESSION['username']))
{
    echo '<script type="text/javascript"> window.location.replace("./login.php") </script>';
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

	<link rel="stylesheet" type="text/css" href="./style/montage.css">

    <style type="text/css">

    .navbar-custom {
    background-color: #9e6483 !important;
    /*#9e6483*/ /*#b4747c*/
}

body {

    background-image: url(public/back.jpg);
    background-attachment:fixed;
    background-position:center;
    background-size:cover;
    background-repeat:no-repeat;

}


.jumbotron {
      background-color: rgba(102, 38, 60, 0.5);
      min-height: 80vh;
      margin-top: 20px;
      margin-left: 40px;
      margin-right: 40px;
      color: white;
      /*opacity: 0.5;*/
    }
</style>
	
</head>
<body>

<?php include 'header.php'; ?>

 <div class="jumbotron jumbotron-fluid">
  <div class="container">
  <div class="forheader"></div>
  <div class="main">
    <div class="page">
        <div class="wrapper">
            
            <!-- 
            <div class="gallery">
                <div class="accordion">
                    d
                        
                    </ul>
                </div>
            </div> -->

            <div class="row">

                <div class="col-lg-12 col-md-12 col-xs-12 " >
                        <div class="t-galery" style="max-width: 1000px;">
                        <?php 
                            $id = $_SESSION['id'];
                            $req = $db->prepare('SELECT `path` FROM photo WHERE id_user = ? ORDER BY id DESC');
                            $req->execute(array($id));
                            while ($content = $req->fetch()) {
                                echo "<div><img class='t-canvas' src='$content[path]'></div>";
                            }
                        ?>
                    </div>
                </div>
            </div>


            <div class="camera" >

                <div class="cv" id="camera">
                    <video id="video"></video>
                    <canvas id="canvas" hidden></canvas>
                </div>


               <div id="divStick">
                    <input type="image" id="crown" src="public/img-montage/crown.png" style="display:none;"/>
                    <input type="image" id="soleil" src="public/img-montage/soleil.png" style="display:none"/>
                    <input type="image" id="coeur" src="public/img-montage/coeur.png" style="display:none"/>
                </div>


                <div class="tools" id="mini" style="background-color: #66263C;">

                    <div class="mask tap"  onclick="check_camera('crown')">
                            <img onclick="show_img_select('crown');"  id="cr" src="public/img-montage/crown.png" >
                    </div>

                    <div class="mask tap"  onclick="check_camera('soleil')">
                        <img onclick="show_img_select('soleil');" id="so" src="public/img-montage/soleil.png">
                    </div>

                    <div class="mask tap"  onclick="check_camera('coeur')">
                        <img onclick="show_img_select('coeur');" id="co" src="public/img-montage/coeur.png">
                    </div>

                </div>

            </div>

            <div id="divImg">
                <img id="output_image" style="height: auto;width: 320px;"/>
            </div>

                <div class="actions">
                    <form method="POST" action="montage.php" name="uploadphoto" enctype="multipart/form-data">
                        <input name="image" id="photo" hidden/>
                            <div>
                                <button id="startbutton" disabled>Prendre une photo</button>
                            </div>
                
                   
               
                
                 
                 <input type="file" name="photoUpload" id="downloadPhoto" value="upload_photo" onClick="display_picture_upload()" onChange="preview_image(event);" style="display:none;" accept="image/png"/>



                <input type="radio" name="filter" value="crown" hidden id="crown_radio">
                <input type="radio" name="filter" value="soleil"  hidden id="soleil_radio">
                <input type="radio" name="filter" value="coeur" hidden id="coeur_radio">

                </form>

                <div >
                    <button onclick="document.getElementById('downloadPhoto').click();undisplay_stickers(); ">Download</button>
                    <p class='text-center'>only png file allowed</p>
                </div>
            </div>
            <script type="text/javascript">




            </script>

        </div>
    </div>
</div>
</div>
</div>

<script type="text/javascript" src="webcam.js"></script>

</body>
<?php include 'footer.php'; ?>
</html>


<!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->

<?php
// session_start();


function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
    $cut = imagecreatetruecolor($src_w, $src_h);
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}



function check_format($src)
{
    $extension_upload = strtolower(substr(strrchr($src['photoUpload']['name'], '.'), 1));
    if ($extension_upload != "png") {
        echo '<script type="text/javascript"> alert("Cette photo est pas du bon format")</script>';
        echo '<script type="text/javascript"> window.location.replace("montage.php") </script>';
        exit(0);
    }
    if (!$src['photoUpload']['tmp_name']) {
        echo '<script type="text/javascript"> alert("Format non supporte ou erreur lors de lupload de la photo")</script>';
        echo '<script type="text/javascript"> window.location.replace("montage.php") </script>';
        exit(0);
    }
    if (!list($witdh, $height) = getimagesize($src['photoUpload']['tmp_name'])) {
        echo '<script type="text/javascript"> alert("Format non supporte ou erreur lors de lupload de la photo")</script>';
        echo '<script type="text/javascript"> window.location.replace("montage.php") </script>';
        exit(0);
    }
    $test = file_get_contents($src['photoUpload']['tmp_name']);
    if ($witdh == 0 || $height == 0) {
        echo '<script type="text/javascript"> alert("Format non supporte ou erreur lors de lupload de la photo")</script>';
        echo '<script type="text/javascript"> window.location.replace("montage.php") </script>';
        exit(0);
    }
}


function is_post_file($file)
{
    if (!empty($file['photoUpload']['tmp_name'] || !empty($file['photoUpload']['name'])))
        return (true);
    else
        return (false);
}

if (isset($_POST['filter'])) 
{

    if (is_post_file($_FILES) == false) 
    {
        $src = $_POST['image'];
        if ($src === "data:,")
        {
            exit();
        }
    } 
    else 
    {
        check_format($_FILES);
        $src = file_get_contents($_FILES['photoUpload']['tmp_name']);
        $src = 'data:image/png;base64,' . base64_encode($src);
    }
        $sticker = $_POST['filter'];
        $src = str_replace('data:image/png;base64,', '', $src);
        $src = str_replace(' ', '+', $src);
        $src = base64_decode($src);
        file_put_contents("public/photo/tmp.png", $src);
        $src1 = imagecreatefrompng("public/img-montage/$sticker.png");
        $dest = imagecreatefrompng("public/photo/tmp.png");
        imagealphablending($src1, false);
        imagesavealpha($src1, true);
        $largeur_source = imagesx($src1);
        $hauteur_source = imagesy($src1);
        $largeur_destination = imagesx($dest);
        $hauteur_destination = imagesy($dest);
        $destination_x = $largeur_destination - $largeur_source;
        $destination_y = $hauteur_destination - $hauteur_source;
        imagecopymerge_alpha($dest, $src1, 0, 0, 0, 0, imagesx($src1), imagesy($src1), 90);
        ob_start();
        imagepng($dest);

        $image_data = ob_get_contents();
        ob_end_clean();

        $id = $_SESSION['id'];
        $picture = md5(rand());
        $picture .= ".png";
        
        file_put_contents("public/photo/users/$id/$picture", $image_data);
        

        $req = $db->prepare("INSERT INTO photo (path, id_user, creation_date, image) VALUES (?, ?, ?, ?)");
        $req->execute(array("public/photo/users/$id/$picture", $_SESSION['id'], date("Y-m-d H:i:s"), $picture));

        echo '<script type="text/javascript"> window.location.replace("montage.php") </script>';

        imagedestroy($dest);
        imagedestroy($src1);
}

?>




