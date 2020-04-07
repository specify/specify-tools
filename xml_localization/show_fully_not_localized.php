<?php

//CONFIG
$source = file_get_contents(__DIR__.'/main_schema/schema_localization.xml'); // link to xml file
$show_distinct = true; // whether to remove duplicates from the search results
$delimiter = '<br>'; // what delimiter to use when outputting the search results


$source = str_replace("  ","",$source);
$source = explode("\n",$source);

$found_name = false;
$found_not_english = false;
$found_english = true;
$english_string = '';
$results = [];

foreach($source as $line){

	if(strpos($line,'<names>')!==FALSE)
		$found_name = true;

	elseif($found_name && strpos($line,'<str language="en">')!==FALSE)
		$found_english = true;

	elseif($found_name && strpos($line,'<str language="')!==FALSE)
		$found_not_english = true;

	elseif($found_name && $found_english && strpos($line,'<text>')!==FALSE){

		$found_english = false;

		$result = preg_replace('/<text>(.*?)<\/text>/','$1',$line);
		$english_string = $result;
	}

	if(strpos($line,'</names>')!==FALSE){
		if($found_not_english === false)
			$results[] = $english_string;

		$found_not_english = false;
		$found_name = false;
		$found_english = false;

	}

}

if($show_distinct)
	$results = array_unique($results);

echo implode($delimiter,$results);