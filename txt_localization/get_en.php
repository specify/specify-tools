<?php

if(true){
	header('HTTP/1.1 200 OK');
	header('Content-Type: text/plain; charset=utf-8');
	header('Content-Disposition: attachment; filename="text_en.txt"');
}
else
	echo '<pre>';


$data = explode("\n",file_get_contents(__DIR__.'/properties/stats_en.properties'));

foreach($data as $string){

	if($string == '' || $string[0]=='#')
		continue;

	echo substr($string,strpos($string,'=')+1)."\n";
}