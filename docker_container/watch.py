from common import *


send_notification('Watching...','Watching...')


def run_command():
	p = subprocess.Popen("docker exec -it specify7-docker_specify7_1 bash -c 'cd /usr/local/specify7/specifyweb/frontend/js_src && node_modules/.bin/webpack --w --devtool eval --progress'",
		stdout=subprocess.PIPE,
		stderr=subprocess.STDOUT,
		universal_newlines=False,
		shell=True)

	nice_stdout = open(os.dup(p.stdout.fileno()), newline='')
	for line in nice_stdout:
		yield line, p.poll()

	yield "", p.wait()


for l, rc in run_command():
	if 'Hash' in l:
                send_notification('SUCCESS!!!','Specify 7 Updated!','Glass')
	if 'ERROR' in l:
		send_notification('ERROR!!!','Error occurred while updating Specify 7!','Hero')
	print(l, end="", flush=True)
