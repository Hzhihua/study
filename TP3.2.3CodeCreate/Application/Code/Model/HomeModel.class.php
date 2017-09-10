<?php

namespace Code\Model;
use Think\Model;

class HomeModel extends Model{
	private $config = '';
	private $config_create = '';
	protected $tableName = '';  //继承父类的tableName

	public function __construct(){
		$this->tableName = $GLOBALS['table_name'];
		parent::__construct();
		// $config_path = CODE_PATH.'Code_template/Home/config.php';
		// $this->config = require($config_path);
	}
}