import subprocess
import os
import pync
import time
import glob
import subprocess


container_name = "specify7_webpack_1"


def send_notification(message, title, sound="Pop", silent=False):
    pync.notify(message, title=title, group="specify7_notifications")
    run_process("afplay /System/Library/Sounds/%s.aiff" % sound, silent)


def run_process(command, silent=False, run_async=True):
    if not silent:
        print(command)
    if run_async:
        subprocess.Popen(command, shell=True)
    else:
        os.system(command)
