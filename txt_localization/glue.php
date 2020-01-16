<?php

if(true){
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="localization_uk_final.utf8"');
}
else
	echo '<pre>';


$data_original = explode("\n", file_get_contents('./source/localization_en.utf8'));
//$result       = explode("\n", file_get_contents('./source/text_en.txt'));
//$result       = explode("\n", file_get_contents('./source/text_ru.txt'));
$result       = explode("\n", file_get_contents('./source/text_uk.txt'));

$global_index = 0;

foreach($data_original as $string)
	if($string == '' || $string[0] == '#')
		echo $string."\n";
	else
		echo substr($string, 0, strpos($string, '=')+1).$result[$global_index++]."\n";