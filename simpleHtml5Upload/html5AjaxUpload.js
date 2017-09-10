function sliceUpload(obj){
   ajax_upload = setInterval(upload(obj),1000);
}
    
function upload(obj){
    //步长
    var step = 10*1024*1024;
    //切割起点
    var begin = 0;
    //切割结束点
    var end = begin + step;
    //上传文件的file对象
    var upfile = null;
    //文件总大小
    var size = 0;
    //数据对象
    var data = null;
    //分割的文件
    var blob = null;
    //XML对象
    var xhr = null;
    //允许下个blob上传
    var go  = true;
    //进度
    var progress = 0;
    return function(){
        if(go == false) return;
        go = false;
        //上传文件的file对象
        upfile = obj.uploadInput.files[0];
        //文件总大小
        size = upfile.size;
        //当起始点超过文件总大小,退出上传.
        if(begin > size){
            clearInterval(ajax_upload);
            //切割起点
            begin = 0;
            //切割结束点
            end = begin + step;
            //允许下个blob上传
            // go  = true;
            //不允许下个blob上传
            go  = false;
            //进度
            progress = 0;
           return;  
        } 
        //XML对象
        xhr = new XMLHttpRequest();
        //建立连接   true异步   false同步
        xhr.open('POST',obj.uploadUrl,true);
        //分割文件
        blob = upfile.slice(begin,end); 
        // console.log(begin);
        // console.log(end);
        //数据对象
        data = new FormData();
        data.append(obj.uploadFileName,blob);
        //发送
        xhr.send(data);
        //ajax异步请求完成
        //更新进度条
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                progress = (end/size)*100;
                if(progress > 100) progress = 100;
                // pg_bar = document.getElementsByTagName('div')[1];
                obj.pg_bar.style.width = progress + '%';
                // pg_num = document.getElementsByTagName('span')[0];
                obj.pg_num.innerHTML = parseInt(progress)+ '%';
                // pg_num.innerHTML = xhr.responseText; 
                //计算下次切割点
                begin = end ;
                end   = begin + step ;
                //允许下个blob上传
                go = true;            
            }
        }
        
        
    };
};