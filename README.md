# bigFile
**大文件分割拼接上传功能**

**例子：**

html
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>
        <script  src="http://libs.baidu.com/jquery/1.7.2/jquery.min.js"></script>
        <!--<script type="text/javascript" src="__PUBLIC__/home/jquery.js"></script>-->
        <script src="__ROOT__/vendor/peteryang/big-file/src/js/uploads.js"></script>
    
    </head>
    <body>

        <input  name="file" type="file"  id="file" onchange="upload()"/>
    
    
        <script>
    
            function upload() {
           $("#file").bigFile({ 
               url:"xhr.php",
               callback:function (re) {  console.log(re); },
               percent:function (re) {  console.log(re); }
           });
            }
        </script>
    
    
    
    </body>
    </html>
    
    


----------


    php
    
    
    
    
   

            require "autoload.php"
    
            $up=new \bigFile\upload\Upload("./temp","./upload",$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name'],12);
    
            $up->apiReturn();
        
    
