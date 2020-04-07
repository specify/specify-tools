<?php

//CONFIG
$source = file_get_contents(__DIR__.'/main_schema/schema_localization.xml');
$language = "uk";
$country = "UA";
$show_distinct = true;
$delimiter = '<br>';
//$language = "ru";
//$country = "RU";


$source = str_replace("  ","",$source);
$source = explode("\n",$source);

$needle = '<str language="'.$language.'" country="'.$country.'">';

$found = false;
$results = [];

foreach($source as $line){

	if(strpos($line,$needle)!==FALSE)
		$found = true;

	elseif($found && strpos($line,'<text>')!==FALSE){

		$found = false;

		$result = preg_replace('/<text>.*?([А-Яа-яіїґҐЇ]+).*?<\/text>/','',$line);

		if($result == '')
			continue;

		$result = preg_replace('/<text>(.*?)<\/text>/','$1',$line);
		$results[] = $result;
	}

}

if($show_distinct)
	$results = array_unique($results);

echo implode($delimiter,$results);