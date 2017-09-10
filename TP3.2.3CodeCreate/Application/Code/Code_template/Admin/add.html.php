<!-- 2017年4月2日  星期天  powerBy  黄志华 -->
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $config_create['add'];?></title>
	<?php echo '<?php $html = C(\'HTML\');?>';?>

	<script src="<?php echo '<?php echo $html[\'p_js\'];?>';?>jquery.js"></script>
<?php foreach($config_create['html'] as $k=>$v):?>
<?php if(preg_match($config['field_rule_arr']['file'], $k)):?>

	<link rel="stylesheet" href="<?php echo '<?php echo $html[\'p_css\'];?>'?>html5uploader.css">
	<script src="<?php echo '<?php echo $html[\'p_js\'];?>';?>jQ.html5SliceUpload.js"></script>
	<script type="text/javascript">
	$(function(){
		$('#upload').html5SliceUpload({
			url:'<?php echo U('Pub/Pub/upload');?>',
			onNumEnough: function(num){
				alert('超过文件上传数量限制: '+num);
			}
		});
	});
	</script>
<?php endif;?>
<?php endforeach;?>
</head>
<body>
	<form action="" method="post" enctype="multipart/form-data">
		<table>
<?php foreach($config_create['html'] as $k=>$v):?>
			<tr>
				<td>
					<?php switch($v['html_lab']): case 'input':  // <input></input>?>
<?php if($v['select_item']):   //<input type="checkbox/radio" />?>
<?php echo $v['text'];?>：
				<?php foreach($v['select_item'] as $_k=>$_v):?>
	<?php echo $_v;?>:<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" value="<?php echo $_k;?>" name="<?php echo $v['map'];if(strtolower($v['type']) == 'checkbox') echo '[]'; // []用于多选多 type="checkbox" ?>"/>&nbsp;
				<?php endforeach;?>

<?php elseif(preg_match($config['field_rule_arr']['file'], $k)): //<input type="file" />?>
<?php echo $v['text'];?>：<div id="dropFile"><div id="upload"></div></div><ol id="showFileList"></ol>

<?php elseif(preg_match($config['field_rule_arr']['pwd'], $k))://<input type="password" />?>
<?php echo $v['text'];?>：<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" placeholder="<?php echo $v['tip'];?>" name="<?php echo $v['map'];?>"/>
					<br />
<?php echo $v['text'];?>确认：<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" placeholder="<?php echo $v['tip'];?>" name="re<?php echo $v['map'];?>"/>

<?php else:  // <input type="text/email/url" />?>
<?php echo $v['text'];?>：<<?php echo $v['html_lab'];?> type="<?php echo $v['type']?>" placeholder="<?php echo $v['tip'];?>" name="<?php echo $v['map'];?>"/>
<?php endif;?><?php echo ENTER;break;?>

					<?php case 'textarea':  // <textarea></textarea>?>
<?php echo $v['text'];?>：<<?php echo $v['html_lab']?> placeholder="<?php echo $v['tip'];?>" name="<?php echo $v['map'];?>" cols="<?php echo $v['cols'];?>" rows="<?php echo $v['rows'];?>"></<?php echo $v['html_lab']?>>
<?php break;?>
<?php endswitch;?>
				</td>
				
			</tr>
<?php endforeach;?>
			<tr>
				<td>
					提交后跳转到显示页面？
					是<input type="radio" name="jump_show" value="1">
					否<input type="radio" name="jump_show" value="0" checked="checked">
				</td>
			</tr>
			<tr><td colspan="2"><input type="submit" value="添加" name="sub"></td></tr>
		</table>
	</form>
</body>
</html>
<?php 
	foreach($config_create['table_all_fields'] as $k=>$v){
		if(preg_match($config['field_rule_arr']['pwd'], $k)){
			$pwd = $k;
		}elseif(preg_match($config['field_rule_arr']['checkbox'], $k)){
			$checkbox = $k;
		}
	}
?>
<script type="text/javascript">
	var inputSubmit = $('input[type="submit"]');
	var resultArr = [];
<?php if($checkbox):?>
	function getCheckBoxValue(checkBox){
		var value = '';
		for(var i=0; i<checkBox.length; i++){
			if(checkBox[i].checked){
				value += checkBox[i].value + ',';
			}
		}
		// validate require 验证0不通过 所以加,
		return value;
	}
<?php endif;?>

	function sendValidate(obj, _this, __this){
		$.post("<?php echo '<?php echo U(\''.$GLOBALS['admin_module_name'].'/'.ucfirst($GLOBALS['table_name']).'/ajaxValidate\');?>';?>",obj,function(result){
		    if(result){
		    	_this.next('div').remove();
		    	_this.parent().append('<div>'+result+'</div>');
		    	
		    	inputSubmit.attr('disabled', 'disabled');
		    	_this.parent().css('color', 'red');
		    	if(__this){
		    		__this.parent().css('color', 'red');
		    	}
		    	for( var key in obj ){
				    resultArr[key] = false;
				}
		    }else{
		    	for( var key in obj ){
				    resultArr[key] = true;
				}
		    	_this.parent().css('color', '#000');
		    	if(__this){
		    		__this.parent().css('color', '#000');
		    	}
		    	// submit button
		    	var bool = true;
		    	for( var key in resultArr ){
				    if(!resultArr[key]){
						bool = false;
						break;
				    }
				}
		    	
		    	if(bool){
		    		inputSubmit.removeAttr('disabled');
		    		_this.siblings('div').remove();
		    	}
		    }
		    return false;
		});
	}
	$('input[type="password"]').unbind('blur').bind('blur', function(){
		var _this = $('input[name="art_password"]');
		var __this = $('input[name="reart_password"]');
		var name = [];
		var value = [];

		name[0] = _this[0].name;
		name[1] = __this[0].name;
		value[0] = _this.val();
		value[1] = __this.val();
		
		var obj = {};
		obj[name[0]] = value[0];
		obj[name[1]] = value[1];

		sendValidate(obj, _this);
		return false;
	});
	$('input[type!="password"]').unbind('blur').bind('blur', function(){
		var obj = {};
		var _this = $(this);
		var name = _this[0].name;
		var value = '';
<?php if($checkbox):?>
		var checkBox = $('input[name=\''+name+'\']');
		if(checkBox[0].type == 'checkbox'){
			value = getCheckBoxValue(checkBox);
		}
<?php endif;?>
		else{
			value = _this.val();
		}	
		obj[name] = value;

		sendValidate(obj, _this);
		return false;
	});
	$('textarea').unbind('blur').bind('blur', function(){
		var _this = $(this);
		var value = _this.val();
		var name = _this[0].name;
		var obj = {};
		obj[name] = value;
		
		sendValidate(obj, _this);
		return false;
	});
</script>
