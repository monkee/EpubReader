<?php
/**
 *
 * 
 * @author zomboo1(@126.com)
 */

include '../EpubReader.class.php';


$epub = new EpubReader('c.epub');
$r = $epub->getTitle();
$r = $epub->getAuthor();

$r = $epub->getCatalog();

$ct = $epub->getFileContentByPage(0);

var_dump($ct);



/* vim: set ts=4 sw=4 sts=4 tw=2000: */
