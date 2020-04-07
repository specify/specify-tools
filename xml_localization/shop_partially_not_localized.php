<?php

//CONFIG
$source = file_get_contents(__DIR__.'/main_schema/schema_localization.xml'); // link to xml file
$language = "uk"; // language to search for (e.x. "ru")
$country = "UA"; // country to search for (e.x. "RU")
$show_distinct = true; // whether to remove duplicates from the search results
$delimiter = '<br>'; // what delimiter to use when outputting the search results


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