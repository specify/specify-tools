from glob import glob
import os
import json

workdir = '/Users/mambo/site/git/specify6/src'  # location of `.properties` files
outputdir = '/Users/mambo/Downloads/properties_to_csv/output'  # the output location for the `.csv` files
extension = 'properties'  # the extension to search for inside of workdir
languages = ['en', 'ru']  # the languages to work with
column_separator = '\t'  # the column separator in the final `.csv` files

files = glob(os.path.join(workdir, ('*.%s' % extension)))
basenames = [os.path.splitext(os.path.basename(file))[0] for file in files]
target_files = [file_name for file_name in basenames if '_' in file_name]

language_files = []
for file_name in target_files:
    base_name, language, *rest = file_name.split('_')

    if language not in languages:
        continue

    language_files.append(base_name)


def strip_key(line_data, key, i, language, languages):
    local_key = line_data[language].split('=')[0]
    if key != local_key:
        raise Exception(
            '%s key (%s) is not the same as %s key (%s) at line %d' % (language, local_key, languages[0], key, i)
        )
    return line_data[language][line_data[language].find('=') + 1:]


for base_name in language_files:
    file_data = None
    print(base_name)
    for language in languages:
        with open('%s/%s_%s.%s' % (workdir, base_name, language, extension)) as file:
            lines = file.read().split('\n')

            if file_data is None:
                file_data = []
                for i in range(0, len(lines)):
                    file_data.append(dict.fromkeys(languages, False))

            for i, line in enumerate(lines):
                file_data[i][language] = line

    columns = [['Field Name'] + languages]

    for i, line_data in enumerate(file_data, 1):

        for language in languages:
            if line_data[language] is False:
                raise Exception('%s is not defined at %s' % (language, i))

        if line_data[languages[0]] == '':
            for language in languages:
                if line_data[language] != '':
                    raise Exception(
                        '%s is "" at %d, but %s has value "%s"' % (languages[0], i, language, line_data[language]))
            columns.append([])
        elif line_data[languages[0]][0] == '#':
            for language in languages:
                if line_data[language] == '' or line_data[language][0] != '#':
                    raise Exception('%s begins with "#" at %d, but %s has value' % (languages[0], i, language))
            columns.append([line_data[languages[0]]])
        else:
            key = line_data[languages[0]].split('=')[0]
            columns.append(
                [key] +
                list(map(
                    lambda language: strip_key(line_data, key, i, language, languages),
                    languages
                ))
            )

    columns_csv = '\n'.join([column_separator.join(fields) for fields in columns])
    with open(os.path.join(outputdir, '%s.csv' % base_name), 'w') as file:
        file.write(columns_csv)
