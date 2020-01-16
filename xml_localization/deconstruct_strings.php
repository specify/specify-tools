<?php

require_once __DIR__ . '/config.php';


$link = $file_path . $xml_file_name.'.xml';

$xml_data = file_get_contents($link);
$xml = simplexml_load_string($xml_data, null, LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json, true);

$output = '';

function explore(&$str){

	global $new_line;
	global $temp_container_name;
	global $en;
	global $output;

	if($str['@attributes']['language'] == 'en'){

		$en = true;

		if($str['text'] == '' || $str['text'] == []){
			//trigger_error('no text : ' . $temp_container_name . $new_line);
			return;
		}

		$output .= $str['text'] . "\n";
	}

	return;

}

function navigate(&$parent){

	global $new_line;
	global $temp_container_name;
	global $en;

	$en = false;

	try {
		if(count($parent) == 0){
			//trigger_error('no translations : ' . $temp_container_name . $new_line);
			return;
		}

		if(!is_array($parent)){
			//trigger_error('not array : ' . $temp_container_name . $new_line);
			return;
		}

		if(array_key_exists('@attributes', $parent)){
			explore($parent);
			return;
		}

		foreach($parent as &$str)
			explore($str);

		if($en == false)
			trigger_error('no english : ' . $temp_container_name . $new_line);
	} catch(Exception $e){
	}

}

function execute_global(&$desc){

	//DESC>NAMES
	navigate($desc['names']['str']);

	//DESC>DESCS
	navigate($desc['descs']['str']);

}

function execute_container(&$container){
	$temp_container_name = $container['@attributes']['name'];

	//ITEMS
	if(array_key_exists('@attributes', $container['items']['desc']))
		execute_global($container['items']['desc']);
	else
		foreach($container['items']['desc'] as &$desc)
			execute_global($desc);

	//NAMES
	navigate($container['names']['str']);

	//DESC>DESCS
	navigate($container['descs']['str']);

	//var_dump($container);
	//echo htmlspecialchars(ArrayToXml::convert($container),null,'',false);
	//exit();
}

if(array_key_exists('@attributes', $array['container']))
	execute_container($array['container']);
else
	foreach($array['container'] as &$container)
		execute_container($container);

//var_dump($array);
//echo utf8_encode(ArrayToXml::convert($array));
//echo htmlspecialchars(ArrayToXml::convert($array),null,'',false);


$output_file = $file_path . 'en_v0.txt';
file_put_contents($output_file, $output);

if($optimize_files){

	$data_array = explode($new_line, $output);
	unset($data_array['']);
	$output = '';

	foreach($data_array as $string)
		$output .= preg_replace('/\d*$/', '', $string) . $new_line;

	$output_file = $file_path . 'en_v1.txt';
	file_put_contents($output_file, $output);
	$data_array = explode("\n", $output);
	unset($data_array['']);


	$output_file = $file_path.'en_v2.txt';
	$unique_array = array_unique($data_array);
	$output = implode("\n",$unique_array);
	file_put_contents($output_file, $output);


	$output_file = $file_path.'en.txt';
	file_put_contents($output_file, $output);

}

foreach($languages as $language_key => $data)
	file_put_contents($file_path.$language_key.'.txt','');

echo 'Done!';