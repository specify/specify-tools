# -*- coding: utf-8 -*-
"""The watcher script."""

from common import send_notification
import subprocess
import os
import re
import time


send_notification("Waiting...", "Waiting for containers to start...")

polling_interval = 10  # 10 seconds
max_polling_duration = 10 * 60  # 10 minutes
duration = 0
while True:
    subprocess.check_output
    duration += polling_interval
    running_containers = subprocess.run(
        ["docker", "compose", "top"], stdout=subprocess.PIPE
    )
    print(running_containers.stdout)
    if len(running_containers.stdout) > 10:
        break

    if duration > max_polling_duration:
        send_notification(
            "Failed...", "Container failed to start on time", "Hero"
        )
        exit(1)

    print("Waiting...")
    time.sleep(polling_interval)


send_notification("Watching...", "Watching for rebuilds")


watchers = [
    {
        "container_name": "webpack_1",
        "matches": lambda line: "ERROR" in line,
        "notification": [
            "ERROR: Webpack",
            "Error occurred while rebuilding Specify 7 front-end",
            "HERO",
        ],
    },
    {
        "container_name": "webpack_1",
        "matches": lambda line: "compiled successfully" in line,
        "notification": ["SUCCESS", "Specify 7 Updated", "Glass"],
    },
    {
        "container_name": "specify7_1",
        "matches": lambda line: "Watching for file changes" in line,
        "notification": ["SUCCESS", "Specify 7 Updated", "Glass"],
    },
]

ansi_escape = re.compile(r"\x1B(?:[@-Z\\-_]|\[[0-?]*[ -/]*[@-~])")


def run_command():
    """Run docker compose log and yields the output.

    Yields:
        The output string line by line
    """
    command = "docker compose logs --no-color --tail 0 --follow"
    print(command)
    p = subprocess.Popen(
        command,
        stdout=subprocess.PIPE,
        stderr=subprocess.STDOUT,
        universal_newlines=False,
        shell=True,
    )

    nice_stdout = open(os.dup(p.stdout.fileno()), newline="")
    for line in nice_stdout:
        yield line, p.poll()

    yield "", p.wait()


for line, _rc in run_command():
    for watcher in watchers:
        if line.startswith(watcher["container_name"]):
            cut_line = line[line.find("|") + 2 :]
            # Strip escape sequences (and colors)
            stripped_line = ansi_escape.sub("", cut_line)
            if watcher["matches"](stripped_line):
                print(line)
                send_notification(*watcher["notification"])

send_notification("Stopped watching", "Stopped watching for rebuilds")
