# specify_tools

This repository has a collection of tools for testing and translating
Specify 6, 7 and Web Portal

## Makefile

This is a tool that helps quickly build and run any version of Specify

## release_log

This app will provide a list of issues from a repository and will give
you an ability to filter the results

## dataset_generator

This script generates `.csv` data set files full of data according to
different parameters

## xml_localization

The scripts in this folder are used for localizing the
`schema_localization.xml` files

## txt_localization

The scripts in this folder should be used to localize `.properties`
and `.utf8` files

## docker_container

A collection of Python scripts for simplifying development with
Specify 7's Docker Container.

### `up.py`

Finds the `docker-compose.yml` file in the current directory or one of
it's parent directories and print the command needed to start the
container.

This is most useful when called from inside of shell function, like
this:

```bash
dcu () {
  python3 ~/path/to/specify_tools/docker_container/up.py $@ | /bin/zsh
}
```

Then, you can use it like this to simply start the containers:

```bash
dcu
```

Or initialize a rebuild like this:

```bash
dcu --build
```
