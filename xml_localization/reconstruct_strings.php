<?php

require_once __DIR__ . '/config.php';
require_once 'ArrayToXml.php';


//CAPITALIZE TRANSLATED TEXT
function mb_ucfirst($string){
	return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
}

function capitalized_prefix($language_key){
	global $languages;
	global $overwrite_non_capitalized;
	global $capitalized_files_prefix;

	if($overwrite_non_capitalized)
		return '';
	if($language_key == '')
		return '';
	if(array_key_exists($language_key,$languages))
		return $capitalized_files_prefix;
	return '';
}

foreach($languages as $language_key => $data){

	if(!$data['capitalize_all'])
		return;

	$link = $file_path . $language_key . '.txt';
	$data = file_get_contents($link);
	$data = explode($new_line, $data);
	$data_count = count($data);
	if($data[$data_count-1] == '')
		unset($data[$data_count-1]);
	$output = '';

	foreach($data as $value)
		$output .= mb_ucfirst($value) . $new_line;

	if(!$overwrite_non_capitalized)
		$link = $file_path . $language_key . capitalized_prefix($language_key) . '.txt';
	file_put_contents($link, $output);

}

//creating language list
$language_list = [];
foreach($languages as $language_key => $data)
	$language_list[] = $language_key;
$language_list_with_en = array_merge(['en'],$language_list);


//COMPILE TEXT FILES
if($optimize_files){

	$en_files = ['en_v0', 'en_v1', 'en_v2'];
	$all_files = array_merge($en_files, $language_list_with_en);

	$translated = [];
	$compiled_file_name = [];

	foreach($all_files as $file_name)
		$translated[$file_name] = explode($new_line, file_get_contents($file_path . $file_name . capitalized_prefix($file_name). '.txt'));

	foreach($language_list_with_en as $language_key)
		$compiled_file_name[$language_key] = $file_path . $language_key . $optimized_files_prefix . '.txt';

	$count = count($translated['en_v0']);
	unset($translated['en_v0'][$count--]);
	$connection_pos = 0;
	$compiled = [];

	foreach($language_list_with_en as $language_key)
		$compiled[$language_key] = '';

	for($i = 0; $i < $count; $i++){

		$connection_pos = array_search($translated['en_v1'][$i], $translated['en_v2']);
		if($connection_pos === false){
			var_dump($i, '<br>');
			exit();
		} else
			foreach($language_list_with_en as $language_key)
				$compiled[$language_key] .= str_replace($translated['en_v1'][$i], $translated[$language_key][$connection_pos], $translated['en_v0'][$i]) . $new_line;


	}

	foreach($language_list_with_en as $language_key)
		file_put_contents($compiled_file_name[$language_key], $compiled[$language_key]);

}

//CREATE FINAL XML
$position_global = 0;

foreach($language_list_with_en as $language_key)
	$translated[$language_key] = explode($new_line, file_get_contents($file_path . $language_key . $optimized_files_prefix . '.txt'));

$link = $file_path . $xml_file_name.'.xml';
$xml_data = file_get_contents($link);
$xml = simplexml_load_string($xml_data, null, LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json, true);

function execute($node){

	global $languages;
	global $translated;
	global $position_global;

	try {

		if($node == null)
			throw new Exception();

		if(array_key_exists('@attributes', $node['str']))
			$node['str'] = [$node['str']];

		if(count($node['str']) == 0)
			throw new Exception();

		$en = null;

		foreach($node['str'] as &$str){

			if($str['@attributes']['language'] == 'en'){

				if($str['text'] == '' || $str['text'] == [])
					throw new Exception();

				$en = &$str;
				$en['text'] = $translated['en'][$position_global];

			}
		}

		if($en == null)
			throw new Exception();

		foreach($languages as $language_key => $language_data){

			$ln = &$node['str'][];
			$ln = $en;
			$ln['@attributes'] = $language_data['xml_attributes'];
			$ln['text'] = $translated[$language_key][$position_global];

		}

		$position_global++;

	} catch(Exception $e){
	}

	return $node;

}

function execute_global(&$node){

	//DESC>NAMES
	$node['names'] = execute($node['names']);

	//DESC>DESCS
	$node['descs'] = execute($node['descs']);

}

function execute_container(&$container){
	//ITEMS

	if(array_key_exists('@attributes', $container['items']['desc']))
		execute_global($container['items']['desc']);
	else
		foreach($container['items']['desc'] as &$desc)
			execute_global($desc);

	//NAMES
	$container['names'] = execute($container['names']);

	//DESCS
	$container['descs'] = execute($container['descs']);


	//var_dump($container);
	//echo htmlspecialchars(ArrayToXml::convert($container),null,'',false);
	//exit();
}



if(array_key_exists('@attributes', $array['container']))
	execute_container($array['container']);
else
	foreach($array['container'] as &$container)
		execute_container($container);


$result = ArrayToXml::convert($array);
$result = explode("\n",$result);
$result[0] = '';
$result[1] = '<vector>';
$result[count($result)-2] = '</vector>';
$result = implode("\n",$result);
$result = substr($result,1,-1);

//automatic file saving does not work because of encoding issues with non latin characters
$link = $file_path . $xml_file_name.'_final.xml';
//file_put_contents($link,$result);exit();
file_put_contents($link,'');

//var_dump($array);
//echo utf8_encode(ArrayToXml::convert($array));
echo '<pre>'.htmlspecialchars($result, null, '', false);