# docker_container
A collection of Python scripts for simplifying development with specify 7's Docker Container.

**NOTE: These scripts are designed to be run from a macOS host machine. Otherwise, some modifications would be required**

### pull.py
Runs `git pull` inside of container

### update.py
Copies modified js scripts from local directory to docker container.

### update_full.py
Same as update.py, but copies entire specifyweb folder

### watch.py
Runs webpack watch in the container waiting for any files to get modified. E.x running `update.py` would cause webpack to rebuild while `watch.py` is running in the background assuming any files were modified.

`watch.py` would also send you a notifications when the build process is done.