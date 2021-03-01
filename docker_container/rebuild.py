from common import *


send_notification('Rebuilding...','Rebuilding...')

def run_command():
        command = "(cd /Users/mambo/site/py_charm/specify7 && docker-compose up --build -d)"
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
        #if 'Running' in l:
        #        send_notification('SUCCESS!!!','Specify 7 Updated!','Glass')
	#if 'ERROR' in l:
	#	send_notification('ERROR!!!','Error occurred while updating Specify 7!','Hero')
	print(l, end="", flush=True)

send_notification('Rebuilt...','Rebuilt...', 'Glass')
run_process('(cd /Users/mambo/site/py_charm/specify7/specifyweb/frontend/static && docker cp ./img specify7_specify7_1:/volumes/static-files/frontend-static/)')
