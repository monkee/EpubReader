<?php
/**
 * 一个可以读取epub文件并解析的类
 *
 * @file Epub.class.php
 * @author zomboo1(@126.com)
 * @date 2013/04/15 20:59:16
 */

if(!defined("DS")){
	define("DS", DIRECTORY_SEPARATOR); //使用DS作为DIRECTORY_SEPARATOR的缩写，已经成为了一种共识
}

//autoload，可以合并到其它的自动载入规则里去
spl_autoload_register("__phpepubReader_autoload"); //注册自动载入函数，使得类的载入规则化

/**
 * autoload
 * 
 * 规则如下：
 * 1. 根目录下，每个类包的拥有单独的命名空间，与该目录的名称一致
 * 2. 类包可拥有与自己命名空间一致的类，称之为默认类或者主类
 * 3. 如包：Sample下，class Sample是主类
 * 4. new Sample_SubClass() 载入的类为：Sample/SubClass
 * 5. 尚未使用PHP5.3的命名空间，未保证代码向前有一定的兼容性
 * 6. 文件名与类名一致，将"/"换成"_"即可；文件使用".class.php"作为文件后缀
 * 
 * @param string $class
 * @throws SDException
 */
function __phpepubReader_autoload($class){
	$classPath = str_replace('_', DS, $class);
	$classPath = dirname(__FILE__) . DS . $classPath . '.class.php';
	if(is_file($classPath)){
		include_once $classPath;
	}
}

class EpubReader
{
	const CONTAINER = 'META-INF/container.xml';	//容器

	private $zip = null;
	private $opf = null;

	public function __construct($epub){
		$this->zip = new EpubReader_Zip($epub);

		//初始化
		$this->init();
	}

	/**
	 * 获取目录
	 */
	public function getCatalog(){
		$ncx = $this->getNcx();
		return $ncx->getCatalog();
	}

	/**
	 * 根据页码获取页内容
	 *
	 * 这个页是相对于文件的
	 *
	 * @param int $page 页码从0开始，封面
	 *
	 * @return string
	 */
	public function getFileContentByPage($page){
		$filename = $this->opf->getFileNameByPage($page);

		return $this->zip->getContentByFilename($filename);
	}

	public function __call($method, $argv){
		if(strpos($method, 'get') === 0){
			return $this->opf->getMeta(strtolower(substr($method, 3)));
		}
	}
	private function getNcx(){
		if(empty($this->ncx)){
			$ncx = $this->opf->getNcxFilename();
			$this->ncx = new EpubReader_Ncx($this->zip->getContentByFilename($ncx));
		}

		return $this->ncx;
	}

	private function init(){
		//初始化opf
		$opfFile = $this->zip->getContentByFilename(self::CONTAINER);
		$container = simplexml_load_string($opfFile);
		$ret = (array)$container->rootfiles->rootfile;
		$opfPath = $ret['@attributes']['full-path'];

		$this->opf = new EpubReader_Opf($this->zip->getContentByFilename($opfPath), $opfPath);
	}
}


