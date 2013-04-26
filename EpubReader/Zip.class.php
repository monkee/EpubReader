<?php
/**
 *
 * 
 * @author zomboo1(@126.com)
 */

require_once(dirname(__FILE__) . DS . 'Zip/pclzip.lib.php');


class EpubReader_Zip
{
	private $zip = null;
	public function __construct($file){
		$this->zip = new PclZip($file);
	}

	/**
	 * 根据文件相对于zip的名称，获取文件内容
	 *
	 * @param string $file 文件名
	 * @return string
	 */
	public function getContentByFilename($file){
		$r = $this->zip->extract(PCLZIP_OPT_BY_NAME, $file, PCLZIP_OPT_EXTRACT_AS_STRING);
		if($r[0]['status'] == 'ok'){
			return $r[0]['content'];
		}
		if(empty($r)){
			throw new Exception("Get zip file content from '{$file}' failed, error=\"file not exists\"");
		}
		throw new Exception("Get zip file content from '{$file}' failed, error={$r[0]['status']}");
	}

	public function __call($method, $argv){
		if(method_exists($this->zip, $method)){
			return call_user_method_array($method, $this->zip, $argv);
		}
		throw new Exception("Method {$method} is not existed!");
	}
}



/* vim: set ts=4 sw=4 sts=4 tw=2000: */
