import os
import sys
from common import *


send_notification("Starting...", "Starting...", silent=True)

compose_file_name = "docker-compose.yml"

directory = os.getcwd()
while True:
    if os.path.isfile(os.path.join(directory, compose_file_name)):
        break
    if directory == "/":
        exit("Failed to find the compose file")
    directory = os.path.dirname(directory)

print(f"(cd {directory} && docker compose up {' '.join(sys.argv[1:])})")
