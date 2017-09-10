<?php if(!defined('CODE_PATH')) exit('非法打开文件Controller.template');?>

/**
 *   2017年3月31日  星期五
 *   powerBy  黄志华
 */
<?php 
	function U_param($url_param, $name, $extend = 0){
		$param = 'array(';
		foreach($url_param as $k=>$v){
			$param .= "'";
			$param .= $k;
			$param .= "'";
			$param .= "=>";
			$param .= 'I(\'get.'.$k.'\',\''.$v.'\')';
			$param .= ",";
		}
		if(!$extend){
			$param = rtrim($param, ',');
			$param .= ')';
			$url = 'U(\''.ucfirst($GLOBALS['admin_module_name']).'/'.ucfirst($GLOBALS['table_name']).'/'.$name.'\', '.$param.')';
		}else{
			$url = 'U(\''.ucfirst($GLOBALS['admin_module_name']).'/'.ucfirst($GLOBALS['table_name']).'/'.$name.'\', '.$param;
		}
		
		return $url;
	}
?>

namespace <?php echo ucfirst($GLOBALS['admin_module_name']);?>\Controller;
use <?php echo $config['use_cm']['Think'];?>\<?php echo $config['use_cm']['Controller'];?>;

class <?php echo ucfirst($config_create['table_name']);?>Controller extends <?php echo $config['use_cm']['Controller'];?>{
	public function <?php echo $config_create['show']?>(){
		$assign = array();

		$<?php echo $config_create['table_name']?>Model = D('<?php echo ucfirst($config_create['table_name']);?>');

		$search = $<?php echo $config_create['table_name']?>Model->search();

		$assign = array(
			'data' => $search['data'],
			'page' => $search['page'],
		);
		$this->assign($assign);
		$this->display();
	}

	public function <?php echo $config_create['update']?>(){
		$<?php echo $config_create['table_name']?>Model = D('<?php echo ucfirst($config_create['table_name']);?>');
		<?php
			$pk = "'";
			$pk .= $config_create['pk'];
			$pk .= "'";
			$pk .= "=>";
			$pk .= 'I(\'post.'.$config_create['pk'].'\')';

			$pk .= '))'; // 将U函数补充完

			$checkbox = array();
			foreach($config_create['table_all_fields'] as $k=>$v){
				if(preg_match($config['field_rule_arr']['checkbox'], $k)){
					$checkbox_tmp = array_search($k, $config_create['_map_true_arr']);
					$checkbox[] = $checkbox_tmp ? $checkbox_tmp : $k;
				}
			}
		?>
		
		if(IS_POST){
<?php foreach($checkbox as $v):?>
			$_POST['<?php echo $v;?>'] = implode(',', $_POST['<?php echo $v;?>']);
<?php endforeach;?>

			$create = $<?php echo $config_create['table_name']?>Model->create(I('post.'), $<?php echo $config_create['table_name']?>Model::MODEL_UPDATE);
			if($create){
				$n = $<?php echo $config_create['table_name']?>Model->save();
				if($n){
					redirect(<?php echo U_param($config['url_param'], $config_create['update']);?>);
				}else{
					$this->error($<?php echo $config_create['table_name']?>Model->getError(), <?php echo U_param($config['url_param'], $config_create['update'], 1).$pk;?>);
				}
			}else{
				$this->error($<?php echo $config_create['table_name']?>Model->getError(), <?php echo U_param($config['url_param'], $config_create['update'], 1).$pk;?>);
			}
		}else{

			$data = $<?php echo $config_create['table_name']?>Model->find(I('get.<?php echo $config_create['pk'];?>'));
			// 字段映射处理 $data要求是一维数组
			// $data = $<?php echo $config_create['table_name']?>Model->parseFieldsMap($data);
			$this->assign($data);
<?php foreach($config_create['table_all_fields'] as $k=>$v):?>
<?php if(preg_match($config['field_rule_arr']['file'], $k)):?>
			CC_sessionFilesEmpty();			
<?php endif;?>
<?php endforeach;?>
			$this->display();
		}
	}

	public function <?php echo $config_create['add']?>(){
		if(IS_POST){
<?php foreach($checkbox as $v):?>
			$_POST['<?php echo $v;?>'] = implode(',', $_POST['<?php echo $v;?>']);
<?php endforeach;?>

			$<?php echo $config_create['table_name']?>Model = D('<?php echo ucfirst($config_create['table_name']);?>');
			$create = $<?php echo $config_create['table_name']?>Model->create(I('post.'), $<?php echo $config_create['table_name']?>Model::MODEL_INSERT);
			if($create){
				$id = $<?php echo $config_create['table_name']?>Model->add();
				if($id){
					if(I('post.jump_show'))
						redirect(<?php echo U_param($config['url_param'], $config_create['show']);?>);
					else
						redirect(<?php echo U_param($config['url_param'], $config_create['add']);?>, 0);
				}else{
					$this->error($<?php echo $config_create['table_name']?>Model->getError(), <?php echo U_param($config['url_param'], $config_create['add']);?>);
				}
			}else{
				$this->error($<?php echo $config_create['table_name']?>Model->getError(), <?php echo U_param($config['url_param'], $config_create['add']);?>);
			}
		}else{
<?php foreach($config_create['table_all_fields'] as $k=>$v):?>
<?php if(preg_match($config['field_rule_arr']['file'], $k)):?>
			CC_sessionFilesEmpty();			
<?php endif;?>
<?php endforeach;?>
			$this->display();
		}

	}

	/**
	 * ajax字段验证
	 * @return {string} [失败返回错误信息]
	 */
	public function ajaxValidate(){
<?php foreach($checkbox as $v):?>
		$_POST['<?php echo $v;?>'] && ($_POST['<?php echo $v;?>'] = implode(',', $_POST['<?php echo $v;?>']));
<?php endforeach;?>

		$<?php echo $config_create['table_name']?>Model = D('<?php echo ucfirst($config_create['table_name']);?>');
		$create = $<?php echo $config_create['table_name']?>Model->create(I('post.'));
		if(!$create){
			echo $<?php echo $config_create['table_name']?>Model->getError();
		}
	}

	public function <?php echo $config_create['del']?>(){
		$<?php echo $config_create['table_name']?>Model = D('<?php echo ucfirst($config_create['table_name']);?>');
		$rst = $<?php echo $config_create['table_name']?>Model->delete(I('get.<?php echo $config_create['pk'];?>'));
		if($rst){
			redirect(<?php echo U_param($config['url_param'], $config_create['show']);?>, 0);
		}else{
			$this->error($<?php echo $config_create['table_name']?>Model->getError(), <?php echo U_param($config['url_param'], $config_create['show']);?>);
		}
	}
}
