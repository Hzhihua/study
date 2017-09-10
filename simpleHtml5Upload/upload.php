<?php
// sleep(5);
$path = './upload/01.mp3';
// var_dump($_FILES);
if(!file_exists($path)) {  
    move_uploaded_file($_FILES['files']['tmp_name'],$path);
} else {
    file_put_contents($path,file_get_contents($_FILES['files']['tmp_name']),FILE_APPEND);
}