from common import *

run_process("docker exec -it --workdir "+base_dir+"specify7/specifyweb/frontend/js_src "+container_name+" /bin/sh; exit")
