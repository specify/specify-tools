<?php

if(array_key_exists('count',$_GET))
	$count = $_GET['count'];
else
	$count = 200;

if(array_key_exists('to_file',$_GET))
	$TO_FILE = $_GET['to_file']==1 || $_GET['to_file']=='true';
else
	$TO_FILE = false;

//Config
$FILE_NAME = $count.'_localities.csv';
$cols = 5;
$array = [];
$all_rows_same = !true;
$default_value = 1;


//Output setup
if($TO_FILE){
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"". $FILE_NAME ."\"");
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
$array[1] = ['custom_rand', [-90000, 90000, 1000]];
$array[2] = ['custom_rand', [-180000, 180000, 1000]];
$array[3] = ['custom_rand', [-90000, 90000, 1000]];
$array[4] = ['custom_rand', [-180000, 180000, 1000]];


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
