from common import *


send_notification('Watching...','Watching...','Pop')


def run_command():
	p = subprocess.Popen("docker exec -it specify7-docker_specify7_1 bash -c 'cd /usr/local/specify7/ && make webpack_watch'",
		stdout=subprocess.PIPE,
		stderr=subprocess.STDOUT,
		universal_newlines=False,
		shell=True)

	nice_stdout = open(os.dup(p.stdout.fileno()), newline='')
	for line in nice_stdout:
		yield line, p.poll()

	yield "", p.wait()


for l, rc in run_command():
	if 'WARNING' in l:
		send_notification('SUCCESS!!!','Specify 7 Updated!','Glass')
	if 'ERROR' in l:
		send_notification('ERROR!!!','Error occurred while updating Specify 7!','Sosumi')
	print(l, end="", flush=True)