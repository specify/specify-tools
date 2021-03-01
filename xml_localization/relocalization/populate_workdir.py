from xml_localization.relocalization import config
import os
from shutil import copyfile
from termcolor import colored
from pathlib import Path

for dir_path, _, filenames in os.walk(
    os.path.join(
        os.getcwd(),
        config.specify6_location
    )
):
    for filename in [
        f for f in filenames if f == config.source_file_name
    ]:
        directory = dir_path.split('/')[-1]

        if directory == 'config':
            directory = 'main'

        source = os.path.join(dir_path, filename)
        destination = os.path.join(
                os.getcwd(),
                config.working_directory,
                directory,
                filename
            )

        print('%s %s %s' % (
            source,
            colored(' >> ', 'green'),
            destination
        ))

        Path(os.path.dirname(destination)).mkdir(
            parents=True,
            exist_ok=True
        )

        copyfile(source, destination)
