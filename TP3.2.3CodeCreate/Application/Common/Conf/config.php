<?php
return array(
	//'配置项'=>'配置值'

    //url地址大小写不敏感设置
    'URL_CASE_INSENSITIVE'  =>  false,

     //URL 模式
    'URL_MODEL'=>3,  //对U函数(生成路劲起效)     要在index.php中开启开发模式

    'URL_HTML_SUFFIX'=>'',

    /******** 修改I函数底层过滤时使用的函数(逗号之间不能加空格) ********/
    'DEFAULT_FILTER' => 'trim,htmlspecialchars',
    // 'SHOW_PAGE_TRACE'=>true, 
    
    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'code',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '123123',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'code_',    // 数据库表前缀

    // 完全使用PHP本身作为模板引擎
    'TMPL_ENGINE_TYPE' =>'PHP',
    // 'TMPL_PARSE_STRING'  =>array(
    //      '__PUBLIC__' => '/Common', // 更改默认的/Public 替换规则
    //      '__JS__'     => '/Public/JS/', // 增加新的JS类库路径替换规则
    //      '__UPLOAD__' => '/Uploads', // 增加新的上传路径替换规则
    // )
    'HTML' => array(
        'p_css' => './Public/Public/css/',
        'p_js' => './Public/Public/js/',
        'p_images' => './Public/Public/images/',
    ),

    //上传参数设置
    'UPLOADPATH'  => './Public/Upload/',  // 设置附件上传根目录
    //图片参数
    'IMGMAXSIZE'   => 3*1024*1024,  // 设置附件上传大小
    'IMGSAVEPATH'  => 'Images/',        // 设置附件上传（子）目录
    'IMGEXTS'      => array('jpg', 'gif', 'png', 'jpeg'),  // 设置附件上传类型
    
    //文件
    'FILEMAXSIZE'   => 300*1024*1024,  // 设置附件上传大小
    'FILESAVEPATH'  => './Public/Upload/Files/',        // 设置附件上传（子）目录
    'FILEEXTS'      => array('doc', 'docx', 'pdf', 'word'),  // 设置附件上传类型

    // md5加密杂质
    'MD5_KEY'   => '2138d!@##DNF^&FD!@!@#&*R!@$*!DN!@G',
);