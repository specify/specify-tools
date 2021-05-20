"""Misc utility functions used by other script."""

import subprocess
import os
import pync


container_name = "specify7_webpack_1"


def send_notification(title, message, sound="Pop", silent=False):
    """Send a notification using the pync module.

    Args:
        title: Title of the message
        message: Message
        sound:
            The name of the aiff sound file
            (from /System/Library/Sounds/)
        silent: Whether to print the output
    """
    pync.notify(message, title=title, group="specify7_notifications")
    run_process("afplay /System/Library/Sounds/%s.aiff" % sound, silent)


def run_process(command, silent=False, run_async=True):
    """Run a command without listening for output.

    Args:
        command:
            The command to run
            List or str
        silent: Whether to print the command that would be run
        run_async:
            Whether to run asynchronously
    """
    if not silent:
        print(command)
    if run_async:
        subprocess.Popen(command, shell=True)
    else:
        os.system(command)
