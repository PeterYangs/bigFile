/**
 * Created by Peter on 2018/1/2 0002.
 */
;(function ($,window,document,undefined) {

    // 默认参数
    var defaluts = {
        fileObj:Object,
        slice_size: 1024*1024,//将文件切割成这个大小
        totalBlobNum: 0,//文件总块数
        blobNum:1,//当前是第几块
        i:0,//计数器
        url:"",
        callback:function () {},
        percent:function () {}
    };



$.fn.extend({

    "bigFile":function (options) {


        var opts = $.extend({}, defaluts, options);//覆盖默认数据


        //文件对象，这个是文件的数据
        // var f=$("#file")[0].files[0];
        opts.fileObj=$(this)[0].files[0];



        //总块数
        // this.totalBlobNum=(Math.ceil(f.size/this.slice_size));
        opts.totalBlobNum=(Math.ceil(opts.fileObj.size/opts.slice_size));



        upload(opts);





    }


});


    /**
     * 文件切割
     * @param blob
     * @param startByte
     * @param length
     * @returns {*}
     */
    function blobSlice (blob,startByte,length) {
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

    /**
     * 文件上传
     * @param opts
     */
    function upload(opts) {



        var data = new FormData();//模拟表单


        //这里第三参数不是长度，是起读点到终点，公式为 start+length
        var s = blobSlice(opts.fileObj, opts.i*opts.slice_size, Number(opts.i*opts.slice_size)+Number(opts.slice_size));


        // console.log(this.i*this.slice_size+"-------------------"+Number(this.i*this.slice_size)+Number(this.slice_size));





        data.append('file_name', opts.fileObj.name);//文件名


        data.append('total_blob_num', opts.totalBlobNum.toString());//总块数

        data.append('blob_num',opts.blobNum.toString());//当前是第几块



        opts.blobNum++;



        data.append('file', s);//当前块的数据





        // var context=this;

//
        $.ajax({
            url:opts.url,
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

                    opts.i++;


                    opts.percent(re);

                    //返回1为还未传完，等待后续文件
                    upload(opts);



                }else if(re.code==2) {

                    //初始化，等待下一个文件继续
                    opts.i=0;
                    opts.blobNum=1;
                    opts.totalBlobNum=0;

                    opts.callback(re);
                    // context.callback(re);

                    // alert('上传成功！');


                }else {

                    console.log(re);

                }



            }


        });



    }
    
    

})(window.jQuery);