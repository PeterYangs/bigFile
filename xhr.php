<?php
require "vendor/autoload.php";

//print_r($_POST);
//
//print_r($_FILES);


//echo __DIR__;
$up=new \bigFile\upload\Upload(__DIR__."/temp",__DIR__."/upload",$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name'],12);

$up->apiReturn();




