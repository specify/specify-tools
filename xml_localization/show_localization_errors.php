<?php

/*
 * PROGRAMS
 *
 * a - show strings that do not have any characters from their language
 * b - show strings that exist only for english language
 * c - show strings that are in a different case from the one used in the English string
 * d - detect the same language used more than once for the same language
 * e - detect non localized strings that were localized elsewhere
 *
 * */

//CONFIG
$source = file_get_contents(__DIR__ . '/main_schema/schema_localization.xml'); // link to xml file
const LANGUAGES = [
	'uk'    => [
		'language' => 'uk',
		'country'  => 'UA',
		'charset'  => 'а-яіїґ',
		'programs' => ['a' => TRUE,
		               'b' => TRUE,
		               'c' => TRUE,
		               'd' => TRUE,
		               'e' => TRUE,
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
		],
	],
	'pt'    => [
		'language' => 'pt',
		'country'  => '',
		'charset'  => 'a-zµùàçéèç',
		'programs' => ['a' => TRUE,
		               'b' => FALSE,
		               'c' => TRUE,
		               'd' => TRUE,
		               'e' => TRUE,
		],
	],
	'pt_BR' => [
		'language' => 'pt',
		'country'  => 'BR',
		'charset'  => 'a-zµùàçéèç',
		'programs' => ['a' => TRUE,
		               'b' => FALSE,
		               'c' => TRUE,
		               'd' => TRUE,
		               'e' => TRUE,
		],
	],
];                                  //all languages (excluding english)
$programs = [

	/*
	 *
	 *  output_mode:
	 *  0 - raw output
	 *  1 - show distinct (strips line numbers)
	 *  2 - groups results (concat line numbers
	 *
	 * */

	'a' => ['output_mode' => 2],
	'b' => ['output_mode' => 2],
	'c' => ['output_mode' => 2],
	'd' => ['output_mode' => 2],
	'e' => ['output_mode' => 2],
];

//FORMATTING
$multiple_lines_delimiter = '<br>'; //if output_mode is 2
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


$source = str_replace("  ", "", $source);
$source = explode("\n", $source);

$found_not_english = FALSE;
$found_name = FALSE;
$found_english = $found_english2 = FALSE;
$english_string = '';
$is_english_lower_case = FALSE;
$temp_language_data = [];
$line_number = 0;

foreach($programs as $program => $parameters){
	$var_name = 'results_' . $program;
	$$var_name = [];
}

foreach(LANGUAGES as $language => $language_data){
	$temp_language_data[$language]['found'] = FALSE;
	$temp_language_data[$language]['temp_definitions'] = [];
}

foreach($source as $line){

	$line_number++;

	if(strpos($line, '<names>') !== FALSE || strpos($line, '<descs>') !== FALSE){
		$found_name = TRUE;

		foreach($languages as $language => $language_data)
			$temp_language_data[$language]['found'] = FALSE;

	}

	elseif(strpos($line, '</names>') !== FALSE || strpos($line, '</descs>') !== FALSE) {

		foreach(LANGUAGES as $language => &$language_data){

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
				}

				if($language_data['programs']['e']){

					if(!array_key_exists($english_string, $temp_language_data[$language]['definitions']))
						$temp_language_data[$language]['definitions'][$english_string] = [];

					$temp_language_data[$language]['definitions'][$english_string] += $temp_language_data[$language]['temp_definitions'];
					$temp_language_data[$language]['temp_definitions'] = [];

				}

			}

		}

		$found_name = FALSE;
		$found_english = FALSE;

	}

	elseif($found_name) {

		if(strpos($line, '<str language="en"') !== FALSE){

			if($found_english)
				$results_d[] = [$line_number, format_language('en') . ' is defined twice for string: ' . format_string($english_string)];

			$found_english = $found_english2 = TRUE;
		}
		else

			foreach(LANGUAGES as $language => $language_data){

				if(!$language_data['programs']['b'] && !$language_data['programs']['c'] && !$language_data['programs']['d'])
					continue;

				if(strpos($line, '<str language="' . $language_data['language'] . '" country="' . $language_data['country'] . '"') !== FALSE){

					if($temp_language_data[$language]['found'] && $language_data['programs']['d'])
						$results_d[] = [$line_number, format_language($language) . ' is defined twice for string: ' . format_string($temp_language_data[$language]['string']) . ' (' . format_string($english_string) . ')'];

					$temp_language_data[$language]['found'] = $temp_language_data[$language]['found2'] = TRUE;

				}

				elseif(strpos($line, '<text>') !== FALSE) {

					if($found_english2){

						$found_english2 = FALSE;
						$english_string = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
						$first_letter = mb_substr($english_string, 0, 1, "UTF-8");
						$is_english_lower_case = preg_replace('/^[a-z]$/', '', $first_letter) == '';

					}

					elseif($temp_language_data[$language]['found2'] == TRUE) {

						var_dump(htmlspecialchars($line),$language);
						$temp_language_data[$language]['found2'] = FALSE;

						$temp_language_data[$language]['string'] = preg_replace('/<text>(.*?)<\/text>/', '$1', $line);
						$first_letter = mb_substr($temp_language_data[$language]['string'], 0, 1, "UTF-8");
						$temp_language_data[$language]['is_lower_case'] = preg_replace('/^[a-z' . $language_data['charset'] . ']/u', '', $first_letter) == '';


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


foreach($temp_language_data as $language => $language_data){

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

				if($string === FALSE)
					$results_e[] = [format_invalid($line_number) . ' ' . format_valid($exists_with_localization[0]), format_language($language) . ' string does not exist here, yet it does in a different place (' . format_string($exists_with_localization[1]) . ')'];

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
			$final_results_array[] = [implode($multiple_lines_delimiter, $lines), $result];

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