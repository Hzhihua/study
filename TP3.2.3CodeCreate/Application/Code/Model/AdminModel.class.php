<?php
/**
 *   2017年3月31日  星期五
 *   powerBy  黄志华
 */

// Code模块下的Model文件夹
namespace Code\Model;
use Think\Model;

/**
 * 将相对路径换成绝对路径
 * @param  [string] $path [相对路径]
 * @return [string]       [绝对路径]
 */
function fullPath($path){
	return SCRIPT_FILENAME.substr($path, 2);
}

class AdminModel extends Model{
	private $config = '';
	private $config_create = '';
	protected $tableName = '';  //继承父类的tableName
	protected $table_name_prefix = '';

	public function __construct(){
		// 修改数据库连接表
		$this->tableName = $GLOBALS['table_name'];
		parent::__construct();

		$config_path = CODE_PATH.'Code_template/Admin/config.php';
		$this->config = require($config_path);
	}

	/**
	 * 获取表结构内容并返回
	 * @param  string $table_name_prefix 表名称(带前缀)
	 * @return array                     表结构内容
	 */
	public function getTableCnt($table_name_prefix = ''){
		$this->table_name_prefix = $table_name_prefix ? $table_name_prefix : C('DB_PREFIX').$GLOBALS['table_name'];
		return $this->query("show full fields from %s", $this->table_name_prefix);
	}

	/**
	 * 处理表格数据，生成新的配置文件
	 * @return array [description]
	 */
	protected function handle_table($table_cnt){
		$config = $this->config;
		$return = array();

		// 处理包含的php文件  处理成字符串
		$path = CODE_PATH.'Code_template/Admin/Config.template.php';
		ob_start();
		require($path);
		$config_template_cnt = ob_get_clean();

		// echo '处理表格数据：<br />';
		// dump($config_template_cnt);

		if($config_template_cnt){
			$return = array(
				'status' => true,
				'cnt' => $config_template_cnt,
			);
		}else{
			$return = array(
				'status' => false,
				'cnt' => '处理表格数据失败',
			);
		}
		
		return $return;
	}

	/**
	 * 根据表结构生成配置文件
	 * @param  array $table_cnt 表结构
	 * @return array           生成配置文件的全路径/false
	 */
	public function config_create($table_cnt){
		$return = array();
		$handle_table = $this->handle_table($table_cnt);

		if($handle_table['status']){

			$dir = CODE_CONFIG_PATH.'/'.ucfirst($GLOBALS['admin_module_name']).'/';
			if(!is_dir($dir)){
				mkdir($dir, 0755, true);
			}
			$config_full_path = $dir.$GLOBALS['table_name'].'.config.php';
			$rst = file_put_contents($config_full_path, "<?php".ENTER.$handle_table['cnt']);

			if($rst){
				$return['status'] = true;
				$return['cnt'] = fullPath($config_full_path);
			}else{
				$return['status'] = false;
				$return['error'] = '生成配置文件失败['.fullPath($config_full_path).']';
			}
		}else{
			$return['status'] = false;
			$return['error'] = $handle_table['error'];
		}

		return $return;
	}

	/**
	 * 生成controller、model文件函数(c->controller m->model)
	 * @param  array $config_create 配置文件内容
	 * @param  string $type         controller/model
	 */
	protected function cm_create($type){
		$table_cnt = $this->getTableCnt();
		$config = $this->config;
		$config_create = $this->config_create;

		$ucfirst_type = ucfirst($type);
		$template_path = CODE_PATH . 'Code_template/Admin/';
		$path = $template_path . $ucfirst_type. '.template.php';

		ob_start();
		require($path);
		$data = ob_get_clean();

		$dir = APP_PATH . $GLOBALS['admin_module_name'] . '/'. $ucfirst_type .'/';
		if(!is_dir($dir)){
			mkdir($dir, 0755, true);
		}
		$create_path = $dir . ucfirst($GLOBALS['table_name']) . $ucfirst_type .'.class.php';

		file_put_contents($create_path, "<?php".ENTER.$data);
	}

	/**
	 * 生成模板文件(add/update/index).html
	 * @param  array $config_create 生成配置文件内容
	 */
	protected function html_create($type){
		$config = $this->config;
		$config_create = $this->config_create;
		$template_path = CODE_PATH . 'Code_template/Admin/';

		$add_path = $template_path . $type . '.html.php';
		ob_start();
		require($add_path);
		$data = ob_get_clean();

		$dir_path = APP_PATH . $GLOBALS['admin_module_name'] . '/View/' . ucfirst($GLOBALS['table_name']);
		if(!is_dir($dir_path)){
			mkdir($dir_path, 0755, true);
		}
		$create_path = $dir_path . '/' . $config_create[$type] . '.html';

		file_put_contents($create_path, $data);
	}

	/**
	 * 根据配置文件生成代码
	 * @return array true/false
	 */
	public function code_create(){
		// 包含生成的配置文件
		$config_create_path = CODE_CONFIG_PATH.'/'.ucfirst($GLOBALS['admin_module_name']).'/'.$GLOBALS['table_name'].'.config.php';
		$this->config_create = require($config_create_path);

		//  controller文件生成
		$this->cm_create('controller');
		
		// model文件生成
		$this->cm_create('model');

		// html文件生成
		$this->html_create('show');
		$this->html_create('add');
		$this->html_create('update');

		return array(
			'status' => true,
		);
	}
}