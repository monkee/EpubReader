<?php
/**
 *
 * 
 * @author zomboo1(@126.com)
 */

class EpubReader_Ncx
{
	private $xml = null;
	private $catalog = null;
	public function __construct($ncxString){
		$this->xml = @simplexml_load_string($ncxString);
	}

	public function getCatalog(){

		if($this->catalog === null){
			$navMap = $this->xml->navMap;

			foreach($navMap->navPoint as $navPoint){
				$item = $this->parseNavPoint($navPoint);
				$this->catalog[$item['playOrder']] = $item;
			}
		}

		return $this->catalog;
	}

	private function parseNavPoint($navPoint){
		$navPoint = (array)$navPoint;
		$content = (array)$navPoint['content'];
		$item = array(
			'id' => (string)$navPoint['@attributes']['id'],
			'playOrder' => (string)$navPoint['@attributes']['playOrder'],
			'label' => (string)$navPoint['navLabel']->text,
			'src' => (string)$content['@attributes']['src'],
		);

		return $item;
	}
}






/* vim: set ts=4 sw=4 sts=4 tw=2000: */
