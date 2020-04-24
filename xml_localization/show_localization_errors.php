<?php

/*
 * PROGRAMS
 *
 * a - show strings that do not have any characters from their language
 * b - show strings that exist only for english language
 * c - show strings that are in a different case from the one used in English language
 *
 * */

//CONFIG
$source = file_get_contents(__DIR__ . '/main_schema/schema_localization.xml'); // link to xml file
$languages = [
	['language' => 'uk', 'country' => 'UA', 'charset' => 'а-яіїґ', 'programs' => [
		'a' => TRUE,
		'b' => TRUE,
		'c' => TRUE,
	],
	],
	['language' => 'ru', 'country' => 'RU', 'charset' => 'а-я', 'programs' => [
		'a' => TRUE,
		'b' => TRUE,
		'c' => TRUE,
	],
	],
	['language' => 'pt', 'country' => '', 'charset' => 'a-z0-9µùàçéèç', 'programs' => [
		'a' => TRUE,
		'b' => FALSE,
		'c' => TRUE,
	],
	],
	['language' => 'pt', 'country' => 'BR', 'charset' => 'a-z0-9µùàçéèç', 'programs' => [
		'a' => TRUE,
		'b' => FALSE,
		'c' => TRUE,
	],
	],
];                     //all languages (excluding english)
$programs = [
	'a' => ['show_distinct' => TRUE], // whether to remove duplicates from the search results
	'b' => ['show_distinct' => TRUE],
	'c' => ['show_distinct' => TRUE],
];

$delimiter = '<br>';   // what delimiter to use when outputting the search results


$source = str_replace("  ", "", $source);
$source = explode("\n", $source);

$found_not_english = FALSE;
$found_english = TRUE;
$english_string = '';

foreach($programs as $program => $parameters){
	$var_name = 'results_' . $program;
	$$var_name = [];
}

foreach($languages as $language => &$language_data)
	$language_data['found'] = FALSE;

foreach($source as $line){

	foreach($languages as $language => &$language_data){

		if(!$language_data['programs']['a'])
			continue;

		if(strpos($line,
		          '<str language="' . $language . '" country="' . $language_data['country'] . '">'
		   ) !== FALSE)
			$language_data['found'] = TRUE;

		elseif($language_data['found'] && strpos($line, '<text>') !== FALSE) {

			$language_data['found'] = FALSE;

			$result = preg_replace('/<text>.*?([А-Яа-яіїґҐЇ]+).*?<\/text>/i', '', $line);

			if($result == '')
				continue;

			$result = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
			$results_a[] = 'Not ' . $$language . ' characters: ' . $result;
		}

	}


	if(strpos($line, '<names>') !== FALSE)
		$found_name = TRUE;

	elseif($found_name && strpos($line, '<str language="en">') !== FALSE)
		$found_english = TRUE;

	else {

		foreach($languages as $language => &$language_data){

			if($found_name && strpos($line, '<str language="') !== FALSE)
				$language_data['found'] = TRUE;

			elseif($found_name && $found_english && strpos($line, '<text>') !== FALSE) {

				$found_english = FALSE;
				$english_string = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);

			}

		}

	}

	if(strpos($line, '</names>') !== FALSE){

		foreach($languages as $language => &$language_data){

			if($language_data['found'] === FALSE)
				$results_b[] = 'No value for language: ' . $language . ' for string: ' . $english_string;

			$language_data['found'] = FALSE;

		}

		$found_name = FALSE;
		$found_english = FALSE;

	}

}

$final_results = '';

foreach($programs as $program => $parameters){

	$results = 'results_' . $program;

	if($parameters['show_distinct'])
		$$results = array_unique($$results);

	$final_results .= implode($delimiter, $$results) . str_repeat($delimiter, 4);

}

echo $final_results;