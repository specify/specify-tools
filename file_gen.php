<?php

if(array_key_exists('count',$_GET))
	$count = $_GET['count'];
else
	$count = 200;

if(array_key_exists('name',$_GET))
	$file_name = $_GET['name'];
else
	$file_name = '*_localities.csv';

if(array_key_exists('type',$_GET) && in_array($_GET['type'],['locality','co']))
	$type = $_GET['type'];
else
	$type = 'default';

if(array_key_exists('to_file',$_GET))
	$TO_FILE = $_GET['to_file']==1 || $_GET['to_file']=='true';
else
	$TO_FILE = false;

//Config
$array = [];
$all_rows_same = !true;
$default_value = '';

switch($type){
	case 'locality': $cols=6; break;
	case 'co': $cols=20; break;
	default: $cols=10; break;
}

if(strlen($file_name)>0 && $file_name[0] == '*')
	$file_name = $count.substr($file_name,1);

//Output setup
if($TO_FILE){
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"". $file_name ."\"");
}





//Declaring array
for ($i = 0; $i < $cols; $i++)
    $array[] = '';

//Declaring functions
function custom_rand($min, $max, $div){
    return (rand() % ($max - $min) + $min) / $div;
}

//Changing values
//[func_name,[param1,param2,...]
//'static_val'
//'' - use def if defined. else use $i
if($type == 'locality'){
	$array[1] = ['custom_rand', [-90000, 90000, 1000]];
	$array[2] = ['custom_rand', [-180000, 180000, 1000]];
	$array[3] = ['custom_rand', [-90000, 90000, 1000]];
	$array[4] = ['custom_rand', [-180000, 180000, 1000]];
	$array[5] = 'Line';
}
elseif($type == 'co')
	$array[0];
else {
	$array[1] = ['custom_rand', [1, 100, 1]];
	$array[2] = ['custom_rand', [1, 100, 1]];
	$array[3] = ['custom_rand', [1, 100, 1]];
	$array[4] = ['custom_rand', [1, 100, 1]];
	$array[5] = ['custom_rand', [1, 100, 1]];
	$array[6] = ['custom_rand', [1, 100, 1]];
	$array[7] = ['custom_rand', [1, 100, 1]];
	$array[8] = ['custom_rand', [1, 100, 1]];
	$array[9] = ['custom_rand', [1, 100, 1]];
}


//Output col names
$i = 0;
for ($i = 0; $i < $cols; $i++) {

    if ($i != 0)
        echo ',';

    echo $i;

}


//Output Data
for ($i = 0, $ii = 0; $i < $count; $i++, $ii = 0) {


    //New lines
    if($TO_FILE)
        echo "\n";
    else
        echo '<br>';


    //Data
    foreach ($array as $value) {

        if ($ii != 0)
            echo ',';


        if (is_array($value))
            echo call_user_func_array($value[0], $value[1]);

        elseif ($value == '') {

            if ($all_rows_same)
                echo $default_value;

            else
                echo $i;
        }

        else
            echo $value;

        $ii = 1;

    }

}
