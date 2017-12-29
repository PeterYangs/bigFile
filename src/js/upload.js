/**
 * Created by Peter on 2017/12/29 0029.
 */

var bigFile={
    slice_size:1024*1024,//将文件切割成这个大小
    totalBlobNum:0,//文件总块数
    blobNum:1,//当前是第几块
    i:0,//计数器
    init:function (url,callback) {



    },
    upload:function (url,f,callback) {



        var data = new FormData();//模拟表单



        //文件对象，这个是文件的数据
       var f=$("#file")[0].files[0];


        //总块数
        this.totalBlobNum=(Math.ceil(f.size/this.slice_size));



                //这里第三参数不是长度，是起读点到终点，公式为 start+length
        var s = this.blobSlice(f, this.i*this.slice_size, Number(this.i*this.slice_size)+Number(this.slice_size));


        console.log(this.i*this.slice_size+"-------------------"+Number(this.i*this.slice_size)+Number(this.slice_size));





        data.append('file_name', f.name);//文件名


        data.append('total_blob_num', this.totalBlobNum);//总块数

        data.append('blob_num',this.blobNum);//当前是第几块



        this.blobNum++;



        data.append('file', s);//当前块的数据





        var context=this;

//
        $.ajax({
            url:url,
            type:"post",
            data:data,

            cache: false,//不缓存
            processData: false,//processData设置为false。因为data值是FormData对象，不需要对数据做处理。
            contentType: false,//contentType设置为false。因为是由<form>表单构造的FormData对象，且已经声明了属性enctype="multipart/form-data"，所以这里设置为false。
            success:function (re) {

                // console.log(s);

                re=JSON.parse(re);

                // console.log(re);

                if(re.code==1){

                    context.i++;

                    //返回1为还未传完，等待后续文件
                    context.upload(url,f);



                }else if(re.code==2) {

                    //初始化，等待下一个文件继续
                    this.i=0;
                    this.blobNum=1;
                    this.totalBlobNum=0;

                    // callback(re);
                    // context.callback(re);

                    alert('上传成功！');


                }



            }


        });

    },
    callback:function (data) {


    },
    /**
     *
     * @param blob       文件对象
     * @param startByte  开始字节的位置
     * @param length     长度
     * @returns {*}      返回blob对象
     */
    blobSlice:function (blob,startByte,length) {
        if(blob.slice){
            return blob.slice(startByte,length);


        }else if(blob.webitSlice){

            return blob.webkitSlice(startByte,length);

        }else if(blob.mozSlice){

            return blob.mozSlice(startByte,length);

        }else {


            return null;
        }



    }



};

