# txt_localization
The scripts in this folder should be used to localize `.properties` and `.utf8` files

## get_en.txt
This script will strip the key names from the file and output or save the new file with the specified name

## to_upper.txt
This script will capitalize all the strings in the specified file and output or save the changes into the file with the specified name

## glue.php
This file should be used to connect key names and localization values from the new language. It will output or save the result into a file with the specified name

## looper.php
This file is similar to `glue.php`, but automatically scans for all the files in the selected directory and creates new trimmed language files based on each of those

## properties_to_csv.py
Converts all `.properties` files to `.csv` files with each language in a separate column