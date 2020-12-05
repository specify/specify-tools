import subprocess
import os
import pync
import time
import glob


dirname = os.path.dirname(__file__)


def send_notification(message, title, sound='Pop'):
        pync.notify(message, title=title, group='specify7_notifications')
        run_process('afplay /System/Library/Sounds/%s.aiff' % sound)


def run_process(command):
        os.system(command)


def string_has_substring(string,array_of_substrings):
    for substring in array_of_substrings:
        if substring in string:
            return True
    return False


def get_ignore_list():
    with open(os.path.join(dirname,'ignore_list.txt')) as ignore_list:
        return  ignore_list.read().strip().split('\n')


def get_files_in_directory(directory):
    ignore_list = get_ignore_list()
    files_to_upload = glob.glob(directory)
    return [file for file in files_to_upload if not string_has_substring(file,ignore_list)]

