<?php if(!defined('CODE_PATH')) exit('非法打开文件Model.template');?>

/**
 *   2017年3月31日  星期五
 *   powerBy  黄志华
 */
namespace <?php echo ucfirst($GLOBALS['admin_module_name']);?>\Model;
use <?php echo $config['use_cm']['Think'];?>\<?php echo $config['use_cm']['Model'];?>;

class <?php echo ucfirst($config_create['table_name']);?>Model extends <?php echo $config['use_cm']['Model'];?>{
	protected $_map = <?php echo $config_create['_map']?>;

	protected $_insertFields = <?php echo $config_create['_insertFields']?>;

	protected $_updateFields = <?php echo $config_create['_updateFields']?>;
	
	protected $_validate = <?php echo $config_create['_validate']?>;
	
	/**	
	 * 字段的值必须存在
	 * @return {bool} 存在 true/ 不存在false
	 */
	protected function _require($code){
		return ($code != '' || $code == '0');
	}

<?php 
	$checkBoxArr = array();
	foreach($table_cnt as $v){
		if(preg_match($config['field_rule_arr']['checkbox'], $v['field'])){
			$comment = $v['comment'];
			$comment_rule_arr = $config['comment_rule_arr'];
			$str_comment = substr($comment, strpos($comment, $comment_rule_arr[0])+1);
			$str_comment = rtrim($str_comment, $comment_rule_arr[1]);
			$arr = explode($comment_rule_arr[2], $str_comment); 
			
			$tmp_str = 'array(';
			foreach ($arr as $value) {
				$ex_arr = explode($comment_rule_arr[3], $value);
				$tmp_str .= "'" . $ex_arr[0] . "'" . ',';
			}
			$tmp_str = rtrim($tmp_str, ',');
			$tmp_str .= ')';

			$checkBoxArr[$v['field']] = $tmp_str;
		}
		
	}

?>
<?php foreach($checkBoxArr as $k=>$v):?>
	/**
	 * checkBox多选多验证规则
	 * @param  {string} code 传递参数
	 * @return {bool} pass true / not pass false
	 */
	protected function checkBoxValivate_<?php echo $k;?>($code, $id = ''){
		$table_arr = <?php echo $v;?>;
		// $_validate 的require验证规则 当传入0值验证不通过  所以在0后面加,来通过验证
		$code_arr = explode(',', rtrim($code, ','));

		foreach($code_arr as $v){
			$bool = false;
			foreach($table_arr as $_v){
				if($v == $_v){
					$bool = true;
					break;
				}
			}
			if(!$bool){
				return false;
			}
		}

		return true;
	}
<?php endforeach;?>

	protected function searchLimit(){
		$where = array();
<?php foreach($config_create['search'] as $k=>$v):?>
<?php if(strtoupper($v['search_method']) === 'BETWEEN'): //范围查询  包含大于、小于、取中?>
		<?php echo '$'.$v['map1'].' = I(\'get.'.$v['map1'].'\');'.ENTER?>
		<?php echo '$'.$v['map2'].' = I(\'get.'.$v['map2'].'\');'.ENTER?>
		<?php echo 'if(!$'.$v['map1'].' && $'.$v['map2'].' !== "")'.ENTER?>
			$where['<?php echo $k?>'] = array('LT', <?php echo 'strtotime($'.$v['map2'].')';?>);<?PHP echo ENTER?>
		<?php echo 'elseif(!$'.$v['map2'].' && $'.$v['map1'].' !== "")'.ENTER?>
			$where['<?php echo $k?>'] = array('GT', <?php echo 'strtotime($'.$v['map1'].')';?>);<?php echo ENTER?>
		<?php echo 'elseif($'.$v['map1'].' !== "" && $'.$v['map2'].' !== "")'.ENTER?>
			$where['<?php echo $k?>'] = array('BETWEEN', array(<?php echo 'strtotime($'.$v['map1'].')';?>, <?php echo 'strtotime($'.$v['map2'].')';?>));	
<?php elseif(strtoupper($v['search_method']) === 'LIKE'):  //模糊查询?>
		if(<?php echo 'I(\'get.'.$v['map'].'\')';?> !== "")
			$where['<?php echo $k?>'] = array('LIKE', '%'.<?php echo 'I(\'get.'.$v['map'].'\')';?>.'%');
<?php else:   //精准查询  EQ IN模式?>
		if(<?php echo 'I(\'get.'.$v['map'].'\')';?> !== "")
			$where['<?php echo $k?>'] = array('<?php echo $v['search_method'];?>', <?php echo '$_GET[\''.$v['map'].'\']';?>);
<?php endif;?>
<?php echo ENTER; //每执行完一次if语句回车换行，分隔开where查询条件?>
<?php endforeach;?>
		return $where;
	}

	public function search(){
		$assign = array();

		$where = $this->searchLimit();
		$total = $this->where($where)->count();
		$listRows = I('get.row', 10);  //默认一页显示10列数据
		// CC_fpage是自动生成函数，位于当前模块下的Common/function.php
		$page = CC_fpage($total, $listRows, $pa="");

		$data = $this->where($where)->limit($page['limit'])->select();
		
		$assign = array(
			'data' => $data,            // 根据查询查询出的数据
			'page' => $page['fpage'],   // 底部分页导航 
		);
		return $assign;
	}
	
	protected function _before_update(&$data, $opt){
<?php foreach($config_create['table_all_fields'] as $k=>$v):?>
<?php if(preg_match($config['field_rule_arr']['time'], $k)):?>
		$data['<?php echo $k;?>'] = $_SERVER['REQUEST_TIME'];
<?php elseif(preg_match($config['field_rule_arr']['file'], $k)):?>
		$data['<?php echo $k;?>'] = implode(',', session('files'));
		session('files', array());
<?php elseif(preg_match($config['field_rule_arr']['pwd'], $k)):?>
		$data['<?php echo $k;?>'] = md5(C('MD5_KEY').$data['<?php echo $k;?>']);
<?php endif;?>
<?php endforeach;?>
	}

	protected function _before_insert(&$data, $opt){
<?php foreach($config_create['table_all_fields'] as $k=>$v):?>
<?php if(preg_match($config['field_rule_arr']['time'], $k)):?>
		$data['<?php echo $k;?>'] = $_SERVER['REQUEST_TIME'];
<?php elseif(preg_match($config['field_rule_arr']['file'], $k)):?>
		$data['<?php echo $k;?>'] = implode(',', session('files'));
		session('files', array());
<?php elseif(preg_match($config['field_rule_arr']['pwd'], $k)):?>
		$data['<?php echo $k;?>'] = md5(C('MD5_KEY').$data['<?php echo $k;?>']);
<?php endif;?>
<?php endforeach;?>
	}

	protected function _before_delete($opt){
<?php foreach($config_create['table_all_fields'] as $k=>$v):?>
<?php if(preg_match($config['field_rule_arr']['file'], $k)):?>
		$file = $this->field('<?php echo $k;?>')->find($opt['where']['<?php echo $config_create['pk_true'];?>']);
		$fileArr = explode(',', $file['<?php echo $k;?>']);
		$rootDir = C('FILESAVEPATH');
		foreach($fileArr as $v){
			@unlink($rootDir.$v);
		}
<?php endif;?>
<?php endforeach;?>
	}
}