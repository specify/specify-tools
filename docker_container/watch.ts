/** The watcher script */

import {send_notification} from './common';

send_notification("Waiting...", "Waiting for containers to start...");


my_env = os.environ.copy()

const pollingInterval = 10
const maxPollingDuration = 10 * 60;
let duration = 0
while(true) {
    duration += pollingInterval
    const runningContainers = subprocess.run(
        ["docker", "compose", "top"],
        stdout=subprocess.PIPE,
        env=my_env
    )
    if runningContainers.stdout.length > 10:
        break

    if duration > maxPollingDuration:
        send_notification(
            "Failed...", "Container failed to start on time", "Hero"
        )
        exit(1)

    print("Waiting...")
    time.sleep(pollingInterval)
}


send_notification("Watching...", "Watching for rebuilds")


watchers = [
    {
        "name": 'webpack',
        "matches": (line)=> line.includes("ERROR"),
        "notification": [
            "ERROR: Webpack",
            "Error occurred while rebuilding Specify 7 front-end",
            "HERO",
        ],
    },
    {
        "name": 'webpack',
        "matches": (line) => line.includes('successfully'),
        "notification": ["SUCCESS", "Specify 7 Updated", "Glass"],
    },
]

const ansiEscape = /\x1B(?:[@-Z\\-_]|\[[0-?]*[ -/]*[@-~])"/;


def run_command():
    """Run docker compose log and yield the output.

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
        env=my_env
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
