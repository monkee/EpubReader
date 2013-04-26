<?php
/**
 *
 * 
 * @author zomboo1(@126.com)
 */

class EpubReader_Opf
{
	const META_TAG = '';
	const APP_NCX = 'application/x-dtbncx+xml';
	private $meta = null;
	private $spine = null;

	private $xml = null;
	private $ncx = null;

	private $relPath = '';

	public function __construct($opfString, $relPath = ''){
		$this->xml = @simplexml_load_string($opfString);
		$this->relPath = dirname($relPath);
		empty($this->relPath) || $this->relPath .= '/';
	}

	public function getMeta($meta){
		$this->xml->registerXPathNamespace('dc', "http://www.idpf.org/2007/opf");
		$r = $this->xml->metadata->xpath('dc:' . $meta);
		
		return (string)$r[0];
	}

	public function getFileNameByPage($page){
		$spine = $this->getSpine();
		$html = $spine[$page];

		if(empty($html)){
			throw new Exception("Page {$page} is not exist");
		}

		return $this->relPath . $html['manifest']['href'];
	}

	public function getSpine(){
		if(empty($this->spine)){
			foreach($this->xml->spine->itemref as $item){
				$item = (array)$item;
				$item = $item['@attributes'];
				$item['manifest'] = $this->getManifestById($item['idref']);
				$this->spine[] = $item;
			}
		}

		return $this->spine;
	}

	public function getManifestById($id){
		$mf = $this->getManifest();

		return $mf[$id];
	}

	private function getManifest(){
		if(empty($this->manifest)){
			foreach($this->xml->manifest->item as $item){
				$item = (array)$item;
				$item = $item['@attributes'];
				$this->manifest[$item['id']] = $item;
			}
		}

		return $this->manifest;
	}

	/**
	 * 获取ncx的文件
	 *
	 * @return string
	 */
	public function getNcxFilename(){
		$mainfest = $this->xml->manifest;
		foreach($mainfest->children() as $item){
			if($item['id'] == 'ncx' || $item['media-type'] == self::APP_NCX){
				return $this->relPath . (string)$item['href'];
			}
		}
	}
}


/* vim: set ts=4 sw=4 sts=4 tw=2000: */
