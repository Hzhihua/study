<?php
/**
 * 2017年4月4日  星期二
 * powerBy 黄志华
 * Description: 总的配置文件   关于【生成配置文件】和【生成代码】的所有配置都在这配置
 */
return array(
	#controller方法名称  view/*.html html文件名称   c(Controller)h(Html)
	'ch_arr' => array(
		'show' => 'index', #显示数据方法名称
		'add' => 'add',  #添加数据方法名称
		'update' => 'update',  #更改数据方法名称
		'del' => 'del',   #删除数据方法名称
	),

	# 操作成功或失败带参数进行跳转 
	# 值带不带单引号不影响
	# 'url_param' => array('page'),  //不可这样写
	# 'url_param' => array('page'=>C('URL_HTML_SUFFIX'), 'row'=>10),
	# 'url_param' => array('page'=>1, 'row'=>''),
	# U('', array('page'=>I('get.page', 1), 'row'=>I('get.row', ''))
	'url_param' => array('page'=>1, 'row'=>'10'),

	# <textarea cols="30" rows="3"></textarea>
	'textarea_config' => array('cols'=>'30', 'rows'=>'3'),   # '30'与30都可以

	# use Think\Controller;
	# use Think\Model;
	'use_cm' => array('Think'=>'Think', 'Controller'=>'Controller', 'Model'=>'Model'),

	#  art_id@id,art_content@content  array('id'=>'art_id', 'content'=>'art_content')
	'map_rule_arr' => array(',', '@'),        #  URL字段映射字符处理配置

	#  性别(1-男,2-女)  ====>    array("1"=>"男", "2"=>"女")
	'comment_rule_arr' => array('(', ')', ',', '-'),  #  表字段注释字符处理配置

	#  表字段关键字匹配
	'field_rule_arr' => array(
		'username'=>'/username/', # 用户名
		'pwd'=>'/password/', # 密码
		'image'=>'/(_small|_middle|_big)/', # 图片
		'file'=>'/file/',    # 文件
		'radio'=>'/select/',  # 多选一功能  <input type="radio"/>
		'checkbox'=>'/more/',  # 多选多功能 <input type="chechbox"/>
		'time'=>'/time/',   # 时间 更新时间
		'textarea'=>'/content/', # 文章内容 <textarea></textarea>
		'email'=>'/email/', # 邮箱
		'url'=>'/url/', # 链接地址
		'money'=>'/money/', # 金钱
	),
);

