<?php

/*
 * TODO
 *
 * 
 *
 * */

$xml_file_name = 'schema_localization';
$file_path = __DIR__.'/main_schema/';
$new_line = "\n"; // <br>
$optimize_files = true;
$optimized_files_prefix = $optimize_files ? '_optimized' : '';
$overwrite_non_capitalized = false;
$capitalized_files_prefix = $overwrite_non_capitalized ? '' : '_capitalized';
$languages = [

	'uk' => [
		'xml_attributes' => [
			'language' => 'uk',
			'country' => 'UA',
			'variant' => '',
		],
		'capitalize_all' => 'true',
	],

	'ru' => [
		'xml_attributes' => [
			'language' => 'ru',
			'country' => 'RU',
			'variant' => '',
		],
		'capitalize_all' => 'true',
	],

];
