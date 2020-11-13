from common import *

run_process("docker cp ~/site/py_charm/specify7/specifyweb specify7-docker_specify7_1:/usr/local/specify7/")
send_notification('(Full) Updating...','(Full) Updating...','Pop')