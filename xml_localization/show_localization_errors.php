<?php

/*
 * PROGRAMS
 *
 * a - show strings that do not have any characters from their language
 * b - show strings that exist only for the English language
 * c - show strings that are in a different case from the one used in the English string
 * d - detect the same language used more than once for the same language
 * e - detect non localized strings that were localized elsewhere
 * f - strings start with different numbers/special characters
 * g - strings end   with different numbers/special characters
 *
 * */


//CONFIG
$source = file_get_contents(__DIR__ . '/birds_schema/schema_localization.xml'); // link to xml file

const LANGUAGES = [ //all languages (excluding english)
	'uk'    => [
		'language' => 'uk',
		'country'  => 'UA',
		'charset'  => 'а-яіїґє', //only specify lowercase variant of the characters that may exist in both lower and upper cases (no numbers or symbols)
		'programs' => ['a' => TRUE,
		               'b' => TRUE,
		               'c' => TRUE,
		               'd' => TRUE,
		               'e' => TRUE,
		               'f' => TRUE,
		               'g' => TRUE,
		],
	],
	'ru'    => [
		'language' => 'ru',
		'country'  => 'RU',
		'charset'  => 'а-я',
		'programs' => ['a' => TRUE,
		               'b' => TRUE,
		               'c' => TRUE,
		               'd' => TRUE,
		               'e' => TRUE,
		               'f' => TRUE,
		               'g' => TRUE,
		],
	],
	'pt'    => [
		'language' => 'pt',
		'country'  => '',
		'charset'  => 'a-zµùàçéèçúâó',
		'programs' => ['a' => TRUE,
		               'b' => FALSE,
		               'c' => TRUE,
		               'd' => TRUE,
		               'e' => TRUE,
		               'f' => TRUE,
		               'g' => TRUE,
		],
	],
	'pt_BR' => [
		'language' => 'pt',
		'country'  => 'BR',
		'charset'  => 'a-zµùàçéèçúâó',
		'programs' => ['a' => TRUE,
		               'b' => FALSE,
		               'c' => TRUE,
		               'd' => TRUE,
		               'e' => TRUE,
		               'f' => TRUE,
		               'g' => TRUE,
		],
	],
];

$programs = [

	/*
	 *
	 *  output_mode:
	 *  0 - raw output
	 *  1 - show distinct (strips line numbers)
	 *  2 - groups results (concat line numbers)
	 *
	 * */

	'a' => ['output_mode' => 2],
	'b' => ['output_mode' => 2],
	'c' => ['output_mode' => 2],
	'd' => ['output_mode' => 2],
	'e' => ['output_mode' => 2],
	'f' => ['output_mode' => 2],
	'g' => ['output_mode' => 2],
];

const SHOW_E_OUTPUT_AS_JSON = TRUE;


//FORMATTING
const MULTIPLE_LINES_DELIMITER = '<br>'; //if output_mode is 2
echo '<style>
	table {
		width: 100%;
	}
	thead {
		background: #aaa; color: #000;
	}
	td {
		border: 4px solid #aaa; padding: 10px;
	}
</style>';
const E_OUTPUT_TEXTAREA_STYLES = '
	width: 90vw;
	height: 20vw;
';

function format_language($language){

	return '<span style="color:blue">' . strtoupper($language) . '</span>';
}

function format_invalid($line_number){

	return '<span style="color:red">' . $line_number . '</span>';
}

function format_valid($line_number){

	return '<span style="color:green">' . $line_number . '</span>';
}

function format_string($line_number){

	return '<i>' . $line_number . '</i>';
}


//LOGIC
$source = str_replace("  ", "", $source);
$source = explode("\n", $source);

$found_not_english = FALSE;
$found_name = FALSE;
$found_english = $found_english2 = FALSE;
$english_string = '';
$is_english_lower_case = FALSE;
$temp_language_data = [];
$line_number = 0;
$english_first_letter = '';
$english_last_letter = '';

foreach($programs as $program => $parameters){ //init results array
	$var_name = 'results_' . $program;
	$$var_name = [];
}

foreach(LANGUAGES as $language => $language_data){ //init default values
	$temp_language_data[$language]['found'] = FALSE;
	$temp_language_data[$language]['temp_definitions'] = [];
}

foreach($source as $line){

	$line_number++;

	if(strpos($line, '<names>') !== FALSE || strpos($line, '<descs>') !== FALSE){ // <names>
		$found_name = TRUE;

		foreach(LANGUAGES as $language => $language_data)
			$temp_language_data[$language]['found'] = FALSE;

	}

	elseif(strpos($line, '</names>') !== FALSE || strpos($line, '</descs>') !== FALSE) { // </names>

		foreach(LANGUAGES as $language => &$language_data){

			if(!$found_english)
				continue;

			if($temp_language_data[$language]['found'] === FALSE){

				if($language_data['programs']['b'])
					$results_b[] = [$line_number, 'No value for ' . format_language($language) . ' language for string: ' . format_string($english_string)];

				if($language_data['programs']['e'])
					$temp_language_data[$language]['definitions'][$english_string][$line_number] = FALSE;

			}
			else {

				if($language_data['programs']['c'] && $is_english_lower_case != $temp_language_data[$language]['is_lower_case']){

					$temp_string = format_language('en') . ' string (' . format_string($english_string) . ') starts with ';

					if($is_english_lower_case)
						$temp_string .= 'lower case';
					else
						$temp_string .= 'upper case';

					$temp_string .= ' but ' . format_language($language) . ' (' . format_string($temp_language_data[$language]['string']) . ') starts with ';

					if($temp_language_data[$language]['is_lower_case'])
						$temp_string .= 'lower case';
					else
						$temp_string .= 'upper case';

					$results_c[] = [$line_number, $temp_string];

					$temp_language_data[$language]['is_lower_case'] = FALSE;

				}

				if($language_data['programs']['e']){

					if(!array_key_exists($english_string, $temp_language_data[$language]['definitions']))
						$temp_language_data[$language]['definitions'][$english_string] = [];

					$temp_language_data[$language]['definitions'][$english_string] += $temp_language_data[$language]['temp_definitions'];
					$temp_language_data[$language]['temp_definitions'] = [];

				}

				//var_dump($language,$english_first_letter,$temp_language_data[$language]['first_letter'],'<br>');

				if($language_data['programs']['f'] && preg_replace('/[a-z]/ui','',$english_first_letter) !== preg_replace('/^[a-z' . $language_data['charset'] . ']/ui', '', $temp_language_data[$language]['first_letter']))
					$results_f[] = [$line_number, format_language('en').' begins with '.format_string($english_first_letter).', yet '.format_language($language).' begins with '.format_string($temp_language_data[$language]['first_letter'])];

				if($language_data['programs']['g'] && preg_replace('/[a-z]/ui','',$english_last_letter) !== preg_replace('/^[a-z' . $language_data['charset'] . ']/ui', '', $temp_language_data[$language]['last_letter']))
					$results_g[] = [$line_number, format_language('en').' ends with '.format_string($english_last_letter).', yet '.format_language($language).' ends with '.format_string($temp_language_data[$language]['last_letter'])];

			}

		}

		$found_name = FALSE;
		$found_english = FALSE;

	}

	elseif($found_name) { // anything else

		if(strpos($line, '<str language="en"') !== FALSE){ // eng <str>

			if($found_english)
				$results_d[] = [$line_number, format_language('en') . ' is defined twice for string: ' . format_string($english_string)];

			$found_english = $found_english2 = TRUE;
		}
		else // anything else

			foreach(LANGUAGES as $language => $language_data){

				if(!$language_data['programs']['b'] && !$language_data['programs']['c'] && !$language_data['programs']['d'])
					continue;

				if( // str
					strpos($line, '<str language="' . $language_data['language'] . '" country="' . $language_data['country'] . '"') !== FALSE
					||
				    (
					    $language_data['country']==''
					    &&
				        strpos($line, '<str language="' . $language_data['language'] . '">') !== FALSE
				    )
				){

					if($temp_language_data[$language]['found'] && $language_data['programs']['d'])
						$results_d[] = [$line_number, format_language($language) . ' is defined twice for string: ' . format_string($temp_language_data[$language]['string']) . ' (' . format_string($english_string) . ')'];

					$temp_language_data[$language]['found'] = $temp_language_data[$language]['found2'] = TRUE;

				}

				elseif(strpos($line, '<text>') !== FALSE) { // <text>

					if($found_english2){

						$found_english2 = FALSE;
						$english_string = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
						$english_first_letter = mb_substr($english_string, 0, 1, "UTF-8");
						$english_last_letter = mb_substr($english_string, -1, 1, "UTF-8");
						$is_english_lower_case = preg_replace('/^[a-z]$/', '', $english_first_letter) == '';

					}

					elseif($temp_language_data[$language]['found2'] == TRUE) {

						$temp_language_data[$language]['found2'] = FALSE;

						$temp_language_data[$language]['string'] = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
						$temp_language_data[$language]['first_letter'] = mb_substr($temp_language_data[$language]['string'], 0, 1, "UTF-8");
						$temp_language_data[$language]['last_letter'] = mb_substr($temp_language_data[$language]['string'], -1, 1, "UTF-8");
						$temp_language_data[$language]['is_lower_case'] = preg_replace('/^[a-z' . $language_data['charset'] . ']/u', '', $temp_language_data[$language]['first_letter']) == '';


						if($language_data['programs']['a']){

							$result = preg_replace('/<text>.*?([' . $language_data['charset'] . ']+).*?<\/text>/iu', '', $line);
							if($result != ''){
								$result = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
								$results_a[] = [$line_number, 'Not ' . format_language($language) . ' characters: ' . format_string($result)];
							}

						}

						if($language_data['programs']['e'])
							$temp_language_data[$language]['temp_definitions'][$line_number] = $temp_language_data[$language]['string'];

					}

				}

			}

	}

}


$e_results_array = [];
$is_e_array_empty = TRUE;
foreach($temp_language_data as $language => $language_data){

	$e_results_array[$language] = [];

	foreach($language_data['definitions'] as $english_string => $localization_strings){

		$exists_with_localization = FALSE;
		$exists_without_localization = FALSE;

		foreach($localization_strings as $line_number => $string){

			if($string !== FALSE)
				$exists_with_localization = [$line_number, $string];

			else
				$exists_without_localization = TRUE;

		}

		if($exists_with_localization !== FALSE && $exists_without_localization)
			foreach($localization_strings as $line_number => $string){

				if($string === FALSE){
					$results_e[] = [format_invalid($line_number) . ' ' . format_valid($exists_with_localization[0]), format_language($language) . ' string does not exist here, yet it does in a different place (' . format_string($exists_with_localization[1]) . ')'];

					if(SHOW_E_OUTPUT_AS_JSON){
						$e_results_array[$language][$line_number] = $exists_with_localization[0];
						$is_e_array_empty = FALSE;
					}

				}

			}

	}

}


$final_results = '';

foreach($programs as $program => $parameters){

	$results = 'results_' . $program;

	if($parameters['output_mode'] == 1){

		foreach($$results as &$result)
			$result = $result[1];

		$$results = array_unique($$results);

	}
	elseif($parameters['output_mode'] == 2) {

		$lines = [];

		$new_results = [];

		foreach($$results as $result){

			$line_number = $result[0];
			$result_string = $result[1];

			if(strpos($line_number, '<') === FALSE)
				$line_number = format_invalid($line_number);

			if(array_key_exists($result_string, $new_results))
				$new_results[$result_string][] = $line_number;
			else
				$new_results[$result_string] = [$line_number];

		}

		$final_results_array = [];

		foreach($new_results as $result => $lines)
			$final_results_array[] = [implode(MULTIPLE_LINES_DELIMITER, $lines), $result];

		$$results = $final_results_array;

	}

	if(count($$results) != 0){

		echo '<table>
			<thead>
				<tr>
					<th colspan="2" class="collapse" data-alt_text="Click here to show this table">Click here to collapse this table</th>
				</tr>
			</thead>
			
			<tbody>';

		foreach($$results as $result){

			$line_number = $result[0];

			if(strpos($line_number, '<') === FALSE)
				$line_number = format_invalid($line_number);

			echo '<tr><td>' . $line_number . '</td><td>' . $result[1] . '</td></tr>';

		}

		echo '</tbody>
		</table>';
	}

}

if(SHOW_E_OUTPUT_AS_JSON && !$is_e_array_empty){

	$json_data = [
		'languages' => LANGUAGES,
		'e_output' => $e_results_array
	];

	$json_result = json_encode($json_data);

	echo '<textarea style="' . E_OUTPUT_TEXTAREA_STYLES . '">' . $json_result . '</textarea>';
}

echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js" integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>';
echo "<script>

$('.collapse').click(function(){
	
	let el = $(this);
	
	el.closest('table').find('tbody').toggle();
	
	let data_alt_text = el.attr('data-alt_text');
	el.attr('data-alt_text', el.text());
	el.text(data_alt_text);
	
});

</script>";