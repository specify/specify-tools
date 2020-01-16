<?php

$to_file = true;

if($to_file){
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="stats_ru.properties"');
}
else
	echo '<pre>';


$data_original = explode("\n", file_get_contents(__DIR__.'/properties/stats_en.properties'));
//$result       = explode("\n", file_get_contents(__DIR__.'/properties/text_en.txt'));
$result       = explode("\n", file_get_contents(__DIR__.'/properties/text_ru.txt'));
//$result       = explode("\n", file_get_contents(__DIR__.'/properties/text_uk.txt'));

$global_index = 0;

foreach($data_original as $string)
	if($string == '' || $string[0] == '#')
		echo $string."\n";
	else
		echo substr($string, 0, strpos($string, '=')+1).$result[$global_index++]."\n";