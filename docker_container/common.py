import subprocess
import os
import time
from playsound import playsound



def send_notification(message, title, sound):
	process = subprocess.Popen("osascript -e 'display notification \"%s\" with title \"%s\"'" % (message, title), shell=True)
	process.wait()
	playsound('/System/Library/Sounds/'+sound+'.aiff')


def run_process(command):
	process = subprocess.Popen(command, shell=True)
	process.wait()
