<?php
namespace bigFile\upload;

class Upload {
    private $sava="./Public/upload/file/";        //保存地址
    private $tmpPath;  //PHP文件临时目录
    private $blobNum; //第几个文件块
    private $totalBlobNum; //文件块总数
    private $fileName; //文件名
    private $tag;     //用户id

    /**
     *
     * Upload constructor.
     * @param $tmpPath         上传目录
     * @param $save            保存目录
     * @param $blobNum         第几块
     * @param $totalBlobNum    总块数
     * @param $fileName        文件名称
     * @param $tag             文件标记（用来区别临时文件）
     */
    public function __construct($tmpPath,$save,$blobNum,$totalBlobNum,$fileName,$tag){
        $this->tmpPath =  $tmpPath;
        $this->blobNum =  $blobNum;
        $this->totalBlobNum =  $totalBlobNum;
        $this->fileName =  $fileName;
        $this->tag=isset($tag)?($tag."/"):"";
        $this->sava=$save;

        //写入权限检查
        if($this->blobNum==1){

            if(!is_writable($this->tmpPath)) exit(json_encode(['code'=>3,'msg'=>$this->tmpPath.":此文件夹无写入权限，请检查"]));
            if(!is_writable($this->sava)) exit(json_encode(['code'=>3,'msg'=>$this->sava.":此文件夹无写入权限，请检查"]));



        }
//        echo json_encode($this->fileName);

//        print_r($_F);

        $this->moveFile();
//        echo "11111111111111111";
//        $this->fileMerge();
    }

    //判断是否是最后一块，如果是则进行文件合成并且删除文件块
    private function fileMerge(){
        //如果当前是最后一块就开始拼接
        if($this->blobNum == $this->totalBlobNum){


            $blob = '';

            $fileName=$this->fileName;
            //如果服务器是Linux的就不需要转码
            //文件名要转码一下，不然乱码，文件数据不需要转码，会乱码可能是编辑器的默认编码的问题，正常打开文件不会乱码
         if(PHP_OS!="Linux")  $fileName=mb_convert_encoding($this->fileName,"gb2312",'utf-8');//文件名转一下码

//            echo $this->fileName;


            //因为考虑到传的文件会重名，后面的文件的类型又是追加，应该先把之前的文件删除，重新覆盖
            if(file_exists($this->sava.'/'. $fileName)){

                @unlink($this->sava.'/'. $fileName);

            }

            //打开目标文件对象，类型为追加，不能一次性写入，不然内存会炸，深有体会
            $f=  fopen($this->sava.'/'. $fileName,"a+");

            for($i=1; $i<= $this->totalBlobNum; $i++){


                //将临时文件夹里的文件拼接起来
                $filename_=$this->tmpPath."/".$this->tag."temp_".$i;

                //获取其中一块
                $blob = file_get_contents($filename_);
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

            $filename=$this->tmpPath."/".$this->tag."temp_".$i;

            //删除文件
            @unlink($filename);

        }
        //删除文件夹
        @rmdir($this->tmpPath."/".$this->tag);
    }

    //移动文件
    private function moveFile(){

        //没有这个文件夹的话就新建
        $this->touchDir();
//        $filename = $this->filepath.'/'. $this->fileName.'__'.$this->blobNum;

        $filename=$this->tmpPath."/".$this->tag."temp_".$this->blobNum;

//        echo $;
        move_uploaded_file($_FILES['file']['tmp_name'],$filename);

        //判断是否是最后一块文件
        $this->fileMerge();



    }

    //API返回数据（这里根据自己的需要做调整，可以写出进度条的效果）
    public function apiReturn(){

//        echo "111111111111111111";

        $data="";
        if($this->blobNum == $this->totalBlobNum){

//            $filename=$this->filepath."/"."temp_".$this->blobNum;

            $filename=$this->fileName;

            if(PHP_OS!="Linux")  $filename=mb_convert_encoding($this->fileName,"gb2312",'utf-8');

            if(file_exists($this->sava.'/'.$filename)){
                $data['now']=$this->blobNum;
                $data['num']=$this->totalBlobNum;
                $data['percent']=number_format($this->blobNum/$this->totalBlobNum,2,'.','')*100;
                $data['code'] = 2;
                $data['msg'] = 'success';
                $data['file_name']=$this->fileName;
//                $data['file_path'] = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['DOCUMENT_URI']).str_replace('.','',$this->sava).'/'. $this->fileName;
//                $data['fileName']=basename($data['file_path']);
//                echo 2;

                echo json_encode($data);
            }
        }else{

//            echo "1111111111111";


            $filename=$this->tmpPath."/".$this->tag."temp_".$this->blobNum;
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

    //建立临时上传文件夹
    private function touchDir(){
        if(!file_exists($this->tmpPath."/".$this->tag)){


            return mkdir($this->tmpPath."/".$this->tag);
        }
    }





}

