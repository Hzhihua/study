<?php

/**
 *   2017年3月30日  星期四
 *   powerBy  黄志华
 */

namespace Code\Controller;
use Think\Controller;

/**
 * 将相对路径换成绝对路径
 * @param  [string] $path [相对路径]
 * @return [string]       [绝对路径]
 */
function fullPath($path){
	return SCRIPT_FILENAME.substr($path, 2);
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

DEFINE('T',"\t");
DEFINE('ENTER',"\r\n");
DEFINE('SCRIPT_FILENAME', substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')).'/');
DEFINE('CODE_PATH', MODULE_PATH);
DEFINE('CODE_CONFIG_PATH',CODE_PATH."Config_create");

class CodeController extends Controller {
	private $table_name = '';
	private $toModule = '';
	private $modelAdmin = '';
	private $modelHome = '';

	public function index(){        

        $s_time = microtime_float();
		$assign = array();

		$GLOBALS['table_name'] = strtolower(I('get.table_name'));		
		$GLOBALS['home_module_name'] = ucfirst(I('get.home_module_name'));
        $GLOBALS['admin_module_name'] = ucfirst(I('get.admin_module_name'));
		
		// $model = new \Code\Model\CodeModel();
        if(I('get.config') || I('get.create')){
            $GLOBALS['table_name'] || trigger_error('表格名称必须填写', E_USER_ERROR);
            ($GLOBALS['home_module_name'] || $GLOBALS['admin_module_name']) || trigger_error('生成文件所对应的模块名称必须填写', E_USER_ERROR);

            // 判断是否点击生成配置文件按钮
            $this->modelHome = D('Home'); 
            $this->modelAdmin = D('Admin'); 
            // $this->modelAdmin = new \Code\Model\AdminModel();;   

            if(I('get.config')){
                $GLOBALS['admin_module_name'] && ($assign['rst']['Admin'] = $this->makeAdminConfig());
                $GLOBALS['home_module_name'] && ($assign['rst']['Home'] = $this->makeHomeConfig());

            }elseif(I('get.create')){
                $GLOBALS['admin_module_name'] && ($assign['rst']['Admin'] = $this->makeAdminCode());
                $GLOBALS['home_module_name'] && ($assign['rst']['Home'] = $this->makeHomeCode());

                $this->addFunction();
            }
        }
        

        $e_time = microtime_float();
        $assign['time'] = $e_time - $s_time . 'seconds';

		$this->assign($assign);
		//显示模板
		$this->display();

	}

    /**
     * 判断文件是否存在且是否为空文件
     * @param string $file 文件全路径
     * @return boolean 存在不为空返回true/否则false
     */
    private function isFileNotEmpty($file){
        return (
            is_file($file) && 
            strlen(trim(file_get_contents($file))) != '0'
        );
    }

    /**
     * 将自定义函数添加到相应模块的函数文件中
     * CC CodeCreate
     */
    private function addFunction(){
        $CCFuncPath = CODE_PATH.'Code_template/'.$GLOBALS['admin_module_name'].'/function.php'; 
        if(!is_file($CCFuncPath)) {
            return false;
        }  

        $modelFuncDir = APP_PATH . $GLOBALS['admin_module_name'] . '/Common/';
        $funcPath = $modelFuncDir . 'function.php';

        if(!is_dir($modelFuncDir)){
            mkdir($modelFuncDir, 0755, true);
        }

        // 判断文件是否存在且是否为空文件
        if($this->isFileNotEmpty($funcPath)){
            include($funcPath);
            $func = require($CCFuncPath);
            file_put_contents($funcPath, $func, FILE_APPEND);
        }else{
            $func = require($CCFuncPath);
            file_put_contents($funcPath, "<?php\r\n".$func);
        }
    }

    /****************后台相关****************/
 	
    /**
     * 后台配置文件生成
     * @return [array] [正确或错误信息]
     */
    private function makeAdminConfig(){
    	$data = array();

    	//1. 根据输入的表格名称获取表格内容
    	$table_cnt = $this->modelAdmin->getTableCnt();
        // dump($table_cnt);

    	//2. 对获取到的表格结构生成对应的配置文件
    	$config_create_full_path = $this->modelAdmin->config_create($table_cnt);
    	if($config_create_full_path['status']){
    		$data['status'] = 1;
    		$data['tip'] = '生成配置文件位于';
    		$data['cnt'] = $config_create_full_path['cnt'];
    	}else{
    		$data['status'] = 0;
    		$data['tip'] = '生成配置文件失败';
    		$data['cnt'] = $config_create_full_path['error'];
    	}

    	return $data;
    }

    /**
     * 后台代码文件生成
     * @return [array] [正确或错误信息]
     */
    private function makeAdminCode(){
    	//3. 根据配置文件生成代码

    	$data = array();
    	$code_create = $this->modelAdmin->code_create();

    	if($code_create['status']){
    		$table_name_uc = ucfirst($GLOBALS['table_name']);
    		$code_full_path = fullPath(APP_PATH);
    		$config_create_path = CODE_CONFIG_PATH.'/'.$GLOBALS['admin_module_name'].'/'.$GLOBALS['table_name'].'.config.php';
    		$config_create = require($config_create_path);

    		$data['status'] = 1;
    		$data['tip'] = '成功生成代码,位于';
    		$data['cnt'] = "
    			{$code_full_path}{$GLOBALS['admin_module_name']}/Controller/{$table_name_uc}Controller.class.php
    			{$code_full_path}{$GLOBALS['admin_module_name']}/Model/{$table_name_uc}Model.class.php
    			{$code_full_path}{$GLOBALS['admin_module_name']}/View/{$table_name_uc}/{$config_create['show']}.html
    			{$code_full_path}{$GLOBALS['admin_module_name']}/View/{$table_name_uc}/{$config_create['add']}.html
    			{$code_full_path}{$GLOBALS['admin_module_name']}/View/{$table_name_uc}/{$config_create['update']}.html
    		";
    	}else{
    		$data['status'] = 0;
    		$data['tip'] = '生成代码失败';
    		$data['cnt'] = $code_create['error'];
    	}
    	
    	return $data;
    }

	



	/****************前台相关****************/

	/**
	 * 后台配置文件生成
	 * @return [array] [正确或错误信息]
	 */
	private function makeHomeConfig(){
		$data['status'] = 1;
		$data['tip'] = '生成配置文件位于';
		$data['cnt'] = 'path';
		return $data;
	}

	/**
     * 后台代码文件生成
     * @return [array] [正确或错误信息]
     */
	private function makeHomeCode(){
		$data['status'] = 1;
		$data['tip'] = '生成配置文件位于';
		$data['cnt'] = 'path';
		return $data;
	}

	
}