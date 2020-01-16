<?php

if(true){
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="text_uk.txt"');
}
else
	echo '<pre>';

$link = './source/text_uk.txt';

$data = file_get_contents($link);
$data = explode("\n",$data);

function mb_ucfirst($string){
	return mb_strtoupper(mb_substr($string, 0, 1)).mb_substr($string, 1);
}

foreach($data as $value)
	echo mb_ucfirst($value)."\n";