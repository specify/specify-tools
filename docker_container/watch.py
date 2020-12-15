from common import *


send_notification('Watching...','Watching...')


def run_command():
        command = "docker exec -it --workdir "+base_dir+"specify7/specifyweb/frontend/js_src "+container_name+" bash -c 'node_modules/.bin/webpack --w --devtool eval --progress'"
        print(command)
        p = subprocess.Popen(command,
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
