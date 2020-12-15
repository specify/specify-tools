from common import *

local_base_dir = '/Users/mambo/site/py_charm/specify7/'
remote_base_dir = '/usr/local/specify7/'

for file in get_files_in_directory(local_base_dir + 'specifyweb/frontend/js_src/*'):
    remote_path = '/'.join(file.split('/')[1:-1]).replace(local_base_dir[1:],remote_base_dir)
    run_process("docker cp %s %s:%s" % (file,container_name,remote_path))

send_notification('Updating...','Updating...')

