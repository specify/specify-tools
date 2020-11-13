from common import *

run_process("docker exec -it specify7-docker_specify7_1 bash -c 'cd /usr/local/specify7/ && git pull'")
send_notification('Pulling...','Pulling...','Pop')
