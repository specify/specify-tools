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
$languages = ['uk' => ['country' => 'UA', 'charset' => 'а-яіїґ', 'programs' => ['a' => TRUE, 'b' => TRUE, 'c' => TRUE,
],
], 'ru'            => ['language' => 'ru', 'country' => 'RU', 'charset' => 'а-я', 'programs' => ['a' => TRUE, 'b' => TRUE, 'c' => TRUE,
],
], 'pt'            => ['language' => 'pt', 'country' => '', 'charset' => 'a-z0-9µùàçéèç', 'programs' => ['a' => TRUE, 'b' => FALSE, 'c' => TRUE,
],
], 'pt_BR'         => ['language' => 'pt', 'country' => 'BR', 'charset' => 'a-z0-9µùàçéèç', 'programs' => ['a' => TRUE, 'b' => FALSE, 'c' => TRUE,
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
$found_name = FALSE;
$found_english = TRUE;
$english_string = '';

foreach($programs as $program => $parameters){
	$var_name = 'results_' . $program;
	$$var_name = [];
}

foreach($languages as $language => &$language_data)
	$language_data['found'] = FALSE;

foreach($source as $line){

	foreach($languages as $language => &$language_data){ //a

		if(!$language_data['programs']['a'])
			continue;

		if(strpos($line, '<str language="' . $language . '" country="' . $language_data['country'] . '">') !== FALSE){
			$languages[$language]['found'] = TRUE;
			var_dump(htmlspecialchars($line));
		} elseif($language_data['found'] && strpos($line, '<text>') !== FALSE) {

			if($language == 'pt'){
				var_dump(htmlspecialchars($line));
				exit();
			}
			$language_data['found'] = FALSE;

			$result = preg_replace('/<text>.*?([' . $language_data['charset'] . ']+).*?<\/text>/i', '', $line);

			if($result == '')
				continue;

			$result = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
			$results_a[] = 'Not ' . $language . ' characters: ' . $result;
			break;
		}

	}

	//b
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