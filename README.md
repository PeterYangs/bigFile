# bigFile
**大文件分割拼接上传功能**

**例子：**

以tp5框架为例

html
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Title</title>
        <script  src="http://libs.baidu.com/jquery/1.7.2/jquery.min.js"></script>
        <!--<script type="text/javascript" src="__PUBLIC__/home/jquery.js"></script>-->
        <script src="__ROOT__/vendor/peteryang/big-file/src/js/upload.js"></script>
    
    </head>
    <body>

        <input  name="file" type="file"  id="file" onchange="upload()"/>
    
    
        <script>
    
            function upload() {
                bigFile.upload('__URL__/xhr',function (data) {
    
    
                    console.log(data);
    
                });
            }
        </script>
    
    
    
    </body>
    </html>
    
    


----------


    php
    
    
   

     function xhr(){
    
            $post=input();
    
    
            $up=new \bigFile\upload\Upload("./temp","./upload",$post['blob_num'],$post['total_blob_num'],$post['file_name'],12);
    
            $up->apiReturn();
        }
    