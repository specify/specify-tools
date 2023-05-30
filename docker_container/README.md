# docker_container

A collection of Python scripts for simplifying development with Specify 7's
Docker Container.

**NOTE: These scripts are designed to be run from a macOS host machine.
Otherwise, some modifications would be required**

## watch.py

Watches for front-end and back-end rebuilds and sends a notification on any
errors.

## Installation

Create a virtual environment:

```zsh
python -m venv venv
```

Install the dependencies:

```zsh
./venv/bin/pip install -r requirements.txt
```

For convenience, you wound need to add the following bash function to your
`~/.zshrc` file (you would have to make a few modifications):

```zsh
dcu(){
  scripts_location="~/site/git/specify_tools/docker_container/"
  compose_location=`python3 ~/site/git/code_share/Python/finder/finder.py docker-compose.yml`
  if [ $? -ne 0 ]; then
    echo "Unable to find 'docker-compose.yml"
    return 1
  fi
  cd $compose_location
  echo "" > nohup.out
  watcher=(bash -c "${scripts_location}venv/bin/python ${scripts_location}watch.py")
  nohup $watcher &
  docker compose up $@
  cd -
}
```

In the above, change `script_location` to the location of this folder
(`docker_compose`) and `compose_location` to the directory that contains the
`docker-compose.yml` file. Or you can use
[this script](https://github.com/maxpatiiuk/code_share/tree/main/Python/finder)
to do that automatically.

Now, just run `dcu` in the terminal, the container would start in the foreground
and the watcher would start in the background.

If you need, you can also provide additional arguments to the
`docker compose up` command:

```zsh
dcu --build
```
