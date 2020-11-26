import subprocess
import os
import pync
import time


def send_notification(message, title, sound='Pop'):
        pync.notify(message, title=title, group='specify7_notifications')
        run_process('afplay /System/Library/Sounds/%s.aiff' % sound)

def run_process(command):
        os.system(command)

