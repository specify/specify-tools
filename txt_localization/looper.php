<?php

$files = scandir(__DIR__.'/properties2');

foreach ($files as $file){

	if(strlen($file) < 3)
		continue;

	$file_name = substr($file, 0, strpos($file, '.'));
	$data = explode("\n", file_get_contents(__DIR__ . '/properties2/'.$file));
	$output_dir = __DIR__.'/properties2/results/'.$file_name.'.txt';
	$output = '';

	foreach($data as $string){

		if($string == '' || $string[0] == '#')
			continue;

		$output .= substr($string, strpos($string, '=') + 1) . "\n";

	}

	file_put_contents($output_dir,$output);

}