<?php

/*
 * PROGRAMS
 *
 * a - show strings that do not have any characters from their language
 * b - show strings that exist only for english language
 * c - show strings that are in a different case from the one used in the English string
 *
 * */

//CONFIG
$source = file_get_contents(__DIR__ . '/main_schema/schema_localization.xml'); // link to xml file
const LANGUAGES = ['uk' => ['country' => 'UA', 'charset' => 'а-яіїґ', 'programs' => ['a' => TRUE, 'b' => TRUE, 'c' => TRUE,
],
], 'ru'                 => ['language' => 'ru', 'country' => 'RU', 'charset' => 'а-я', 'programs' => ['a' => TRUE, 'b' => TRUE, 'c' => TRUE,
],
], 'pt'                 => ['language' => 'pt', 'country' => '', 'charset' => 'a-z0-9µùàçéèç', 'programs' => ['a' => TRUE, 'b' => FALSE, 'c' => TRUE,
],
], 'pt_BR'              => ['language' => 'pt', 'country' => 'BR', 'charset' => 'a-z0-9µùàçéèç', 'programs' => ['a' => TRUE, 'b' => FALSE, 'c' => TRUE,
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
$is_english_lower_case = FALSE;
$temp_language_data = [];

foreach($programs as $program => $parameters){
	$var_name = 'results_' . $program;
	$$var_name = [];
}

foreach(LANGUAGES as $language => $language_data)
	$temp_language_data[$language]['found'] = FALSE;

foreach($source as $line){

	foreach(LANGUAGES as $language => $language_data){ //a

		if(!$language_data['programs']['a'])
			continue;

		if(strpos($line, '<str language="' . $language . '" country="' . $language_data['country'] . '">') !== FALSE)
			$found_not_english = $language;

		elseif($found_not_english == $language && strpos($line, '<text>') !== FALSE) {

			$found_not_english = FALSE;

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

	elseif(strpos($line, '</names>') !== FALSE) {

		foreach(LANGUAGES as $language => &$language_data){

			if($temp_language_data[$language]['found'] === FALSE && $language_data['programs']['b'])
				$results_b[] = 'No value for language: ' . $language . ' for string: ' . $english_string;

			if($language_data['programs']['c'] && $is_english_lower_case != $temp_language_data[$language]['is_lower_case']){

				$temp_string = 'EN string (' . $english_string . ') starts with ';

				if($is_english_lower_case)
					$temp_string .= 'lower case'; else
					$temp_string .= 'upper case';

				$temp_string .= ' but ' . $language . ' (' . $temp_language_data[$language]['string'] . ') starts with ';

				if($temp_language_data[$language]['is_lower_case'])
					$temp_string .= 'lower case'; else
					$temp_string .= 'upper case';

				$results_c[] = $temp_string;

			}

			$temp_language_data[$language]['found'] = FALSE;

		}

		$found_name = FALSE;
		$found_english = FALSE;

	} elseif($found_name) {
		if(strpos($line, '<str language="en">') !== FALSE)
			$found_english = TRUE; else

			foreach(LANGUAGES as $language => $language_data){

				if(!$language_data['programs']['b'] && !$language_data['programs']['c'])
					continue;

				if(strpos($line, '<str language="' . $language . '" country="' . $language_data['country'] . '">') !== FALSE)
					$temp_language_data[$language]['found'] = TRUE;

				elseif(strpos($line, '<text>') !== FALSE) {

					if($found_english){

						$found_english = FALSE;
						$english_string = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
						$first_letter = mb_substr($english_string, 0, 1, "UTF-8");
						$is_english_lower_case = preg_replace('/^[' . $language_data['charset'] . ']$/', '', $first_letter) == '';

					} else {

						$temp_language_data[$language]['string'] = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
						$first_letter = mb_substr($temp_language_data[$language]['string'], 0, 1, "UTF-8");
						$temp_language_data[$language]['is_lower_case'] = preg_replace('/^[' . $language_data['charset'] . ']$/', '', $first_letter) == '';

//						if($temp_language_data[$language]['string']=='Записи дерева'){
//							var_dump($first_letter,
//							         preg_replace('/^[' . $language_data['charset'] . ']$/', '', $first_letter));
//						}
						//var_dump();
						//var_dump($first_letter,'/[' . $language_data['charset'] . ']/',$temp_language_data[$language]['is_lower_case']);
					}

				} elseif($found_english && strpos($line, '<text>') !== FALSE) {

					$found_english = FALSE;
					$english_string = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
					$first_letter = mb_substr($english_string, 0, 1, "UTF-8");
					$is_english_lower_case = preg_replace('/[' . $language_data['charset'] . ']/', '', $first_letter) == '';
					//var_dump($first_letter,$is_english_lower_case);

				}

			}

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