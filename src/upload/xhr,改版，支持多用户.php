<?php

/**
 * 上传类，支持多用户上传
 * Create By Peter
 * Class Upload
 */
class Upload{
    private $filepath = './Public/upload/temp/'; //上传目录
    private $sava="./Public/upload/file/";        //保存地址
    private $tmpPath;  //PHP文件临时目录
    private $blobNum; //第几个文件块
    private $totalBlobNum; //文件块总数
    private $fileName; //文件名
    private $uid;     //用户id

    public function __construct($tmpPath,$blobNum,$totalBlobNum,$fileName,$uid){
        $this->tmpPath =  $tmpPath;
        $this->blobNum =  $blobNum;
        $this->totalBlobNum =  $totalBlobNum;
        $this->fileName =  $fileName;
        $this->uid=isset($uid)?($uid."/"):"";


//        echo $this->uid;

        $this->moveFile();
//        echo "11111111111111111";
//        $this->fileMerge();
    }

    //判断是否是最后一块，如果是则进行文件合成并且删除文件块
    private function fileMerge(){
        //如果当前是最后一块就开始拼接
        if($this->blobNum == $this->totalBlobNum){


            $blob = '';

            //如果服务器是Linux的就不需要转码
            //文件名要转码一下，不然乱码，文件数据不需要转码，会乱码可能是编辑器的默认编码的问题，正常打开文件不会乱码
//            $this->fileName=mb_convert_encoding($this->fileName,"gb2312",'utf-8');//文件名转一下码

            //因为考虑到传的文件会重名，后面的文件的类型又是追加，应该先把之前的文件删除，重新覆盖
            if(file_exists($this->sava.'/'. $this->fileName)){

                @unlink($this->sava.'/'. $this->fileName);

            }

            //打开目标文件对象，类型为追加，不能一次性写入，不然内存会炸，深有体会
            $f=  fopen($this->sava.'/'. $this->fileName,"a+");

            for($i=1; $i<= $this->totalBlobNum; $i++){


                //将临时文件夹里的文件拼接起来
                $filename=$this->filepath."/".$this->uid."temp_".$i;

                //获取其中一块
                $blob = file_get_contents($filename);
                //追加写入
                fwrite($f,$blob);


            }
            //关闭文件流
            fclose($f);






            //删除这些切割文件
            $this->deleteFileBlob();
        }
    }

    //删除文件块
    private function deleteFileBlob(){
        for($i=1; $i<= $this->totalBlobNum; $i++){

            $filename=$filename=$this->filepath."/".$this->uid."temp_".$i;
            @unlink($filename);
//            @unlink($this->filepath.'/'. $this->fileName.'__'.$i);
        }
    }

    //移动文件
    private function moveFile(){

        //没有这个文件夹的话就新建
        $this->touchDir();
//        $filename = $this->filepath.'/'. $this->fileName.'__'.$this->blobNum;

        $filename=$this->filepath."/".$this->uid."temp_".$this->blobNum;

//        echo $filename;
        move_uploaded_file($this->tmpPath,$filename);

        //判断是否是最后一块文件
        $this->fileMerge();



    }

    //API返回数据（这里根据自己的需要做调整，可以写出进度条的效果）
    public function apiReturn(){

        $data="";
        if($this->blobNum == $this->totalBlobNum){

//            $filename=$this->filepath."/"."temp_".$this->blobNum;
            if(file_exists($this->sava.'/'. $this->fileName)){
                $data['now']=$this->blobNum;
                $data['num']=$this->totalBlobNum;
                $data['percent']=number_format($this->blobNum/$this->totalBlobNum,2,'.','')*100;
                $data['code'] = 2;
                $data['msg'] = 'success';
                $data['file_path'] = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['DOCUMENT_URI']).str_replace('.','',$this->sava).'/'. $this->fileName;
                $data['fileName']=basename($data['file_path']);
//                echo 2;

                echo json_encode($data);
            }
        }else{

            $filename=$this->filepath."/".$this->uid."temp_".$this->blobNum;
//            echo $filename;
            if(file_exists($filename)){
                $data['code'] = 1;
                $data['now']=$this->blobNum;
                $data['num']=$this->totalBlobNum;
                $data['percent']=number_format($this->blobNum/$this->totalBlobNum,2,'.','')*100;
                $data['msg'] = 'waiting for all';
                $data['file_path'] = '';

                echo json_encode($data);

//                echo 1;
            }
        }


//        header('Content-type: application/json');

    }

    //建立上传文件夹
    private function touchDir(){
        if(!file_exists($this->filepath."/".$this->uid)){
            return mkdir($this->filepath."/".$this->uid);
        }
    }
}

////实例化并获取系统变量传参
//$upload = new Upload($_FILES['file']['tmp_name'],$_POST['blob_num'],$_POST['total_blob_num'],$_POST['file_name']);
////调用方法，返回结果
//$upload->apiReturn();
