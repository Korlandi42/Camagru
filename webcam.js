var streaming = false,
    video        = document.querySelector('#video'),
    cover        = document.querySelector('#cover'),
    canvas       = document.querySelector('#canvas'),
    photo        = document.querySelector('#photo'),
    startbutton  = document.querySelector('#startbutton'),
    width = 320,
    height = 0;


if (navigator.mediaDevices.getUserMedia) {       
  navigator.mediaDevices.getUserMedia({video: true})
  .then(function(stream) {
    video.srcObject = stream;
    video.play();
  })
  .catch(function(error) {      
    startbutton.disabled = true;
    return;
  });
}

  video.addEventListener('canplay', function(ev){
    if (!streaming) {
      height = video.videoHeight / (video.videoWidth/width);
      video.setAttribute('width', width);
      video.setAttribute('height', height);
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
      streaming = true;
    }
  }, false);


  function takepicture() {
    canvas.width = width;
    canvas.height = height;
    canvas.getContext('2d').drawImage(video, 0, 0, width, height);
    var data = canvas.toDataURL('image/png');
    photo.setAttribute('src', data);
    document.getElementById('photo').value = data;
    document.forms['uploadphoto'].submit();
    var currentSticker = startbutton.dataset.currentSticker;
  }

  startbutton.addEventListener('click', function(ev) {
    takepicture();
    ev.preventDefault();
  }, false);


function display_picture_upload()
{
    document.getElementById('camera').style         = 'display:none;background-color:white;'; 
    document.getElementById('output_image').style   = 'position:relative;bottom:230px;height: auto;width: 320px;';
    document.getElementById('mini').style           = 'position:relative;background-color: #66263C;';
    document.getElementById('divStick').style       = 'z-index: 2;';
    document.getElementById('divImg').style         = 'z-index: 1;';
}

function undisplay_stickers() 
{
    document.getElementById('crown').style = 'display:none;'
    document.getElementById('soleil').style = 'display:none;'
    document.getElementById('coeur').style = 'display:none;'
}

function preview_image(event) 
{
    var fileList = this.files;
     var reader = new FileReader();
     reader.onload = function()
     {
      var output = document.getElementById('output_image');
      output.src = reader.result;
     }
     reader.readAsDataURL(event.target.files[0]);
}



function check_camera(sticker_name)
{
    if (navigator.mediaDevices.getUserMedia) {       
        navigator.mediaDevices.getUserMedia({video: true})
      .then(function(stream) {

        select_sticker(sticker_name);

        })
      .catch(function(error) {

        var src = document.getElementById('output_image').src;

        if (src)
        {
            select_sticker(sticker_name);
            document.getElementById('startbutton').disabled=false;
        }

        });
    }
}

function select_sticker(sticker_name)
{
     if (sticker_name === "crown")
    {
        if (document.getElementById('output_image').src)
        {  
            document.getElementById('crown').style = 'display:none;'
        }
        else
        {
            document.getElementById('crown').style='display:absolute;position:relative;width:100px;right:325px;';
        }

        document.getElementById('startbutton').disabled=false;
        document.getElementById('soleil').style='display:none';
        document.getElementById('coeur').style='display:none';
        document.getElementById('crown_radio').click();
    }
    if (sticker_name === "soleil")
    {
        if (document.getElementById('output_image').src)
        {  
            document.getElementById('soleil').style = 'display:none;'
        }
        else
        {
            document.getElementById('soleil').style='display:absolute;position:relative;width:100px;right:325px;';
        }
        document.getElementById('startbutton').disabled=false;
        document.getElementById('crown').style='display:none';
        document.getElementById('coeur').style='display:none';
        
        document.getElementById('soleil_radio').click();
    }
    if (sticker_name === "coeur")
    {
        if (document.getElementById('output_image').src)
        {  
            document.getElementById('coeur').style = 'display:none;'
        }
        else
        {
            document.getElementById('coeur').style='display:absolute;position:relative;width:100px;right:325px;';
        }
        document.getElementById('startbutton').disabled=false;
        document.getElementById('crown').style='display:none';
        document.getElementById('soleil').style='display:none';
        document.getElementById('coeur_radio').click();
    }
}

function show_img_select(sticker_name)
{

    if (sticker_name === "crown")
    {
        document.getElementById('cr').style = "border: 2px solid #d3d3d3; border-radius: 1.2em;";
        document.getElementById('so').style = "border: 0px;"
        document.getElementById('co').style = "border: 0px;"
    }
    if (sticker_name === "soleil")
    {
        document.getElementById('so').style = "border: 2px solid #d3d3d3; border-radius: 1.2em;";
        document.getElementById('cr').style = "border: 0px;"
        document.getElementById('co').style = "border: 0px;"
    }
    if (sticker_name === "coeur")
    {
        document.getElementById('co').style = "border: 2px solid #d3d3d3; border-radius: 1.2em;";
        document.getElementById('cr').style = "border: 0px;"
        document.getElementById('so').style = "border: 0px;"
    }
   
} 