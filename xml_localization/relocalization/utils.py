import os
from xml_localization.relocalization import config
import xmltodict
import json
from termcolor import colored
import re
import copy


defined_languages = config.languages.keys()


def loop(function):

    base_dir = os.path.join(
        os.getcwd(),
        config.working_directory
    )

    for directory in os.listdir(base_dir):

        full_path = os.path.join(
            base_dir,
            directory
        )

        if not os.path.isdir(full_path):
            continue

        print(colored(full_path.split('/')[-1],'blue'))
        function(full_path)


def parse_xml(working_location):
    with open(
            os.path.join(
               working_location,
               config.source_file_name
            )
    ) as source_file:
       return xmltodict.parse(source_file.read())



def dump_to_json(working_location, source, file_name):
    dump_to_file(
        working_location,
        json.dumps(
            source,
            indent=4,
            sort_keys=False,
            ensure_ascii=False
        ),
        file_name
    )


def dump_to_file(working_location, source:str, file_name):
    with open(
        os.path.join(
            working_location,
            file_name
        ),
        'w'
    ) as file:
        file.write(source)


def extract_string(list_of_str, path, error_callback):
    extracted_line = {}

    if not isinstance(list_of_str, list):
        list_of_str = [list_of_str]

    for string in list_of_str:
        if 'text' in string:
            extracted_line[string['@language']] = \
                string['text']

    if not len(extracted_line.keys()):
        return []

    if 'en' not in extracted_line and error_callback:
        response = error_callback(
            'missing_en_key',
            '%s Strings for English language are missing %s. Location: %s' % (
                colored('ERROR: ', 'red'),
                json.dumps(extracted_line, indent=4, sort_keys=False),
                path
            )
        )

        if isinstance(response, str):
            extracted_line['en'] = response

    for language in defined_languages:
        if language not in extracted_line:
            extracted_line[language] = ''

    return [extracted_line]


def extract_strings(structure, path, error_callback):
    extracted_strings = []
    for branch in ['names', 'descs']:
        if branch in structure and structure[branch] is not None:
            extracted_strings += extract_string(
                structure[branch]['str'],
                '%s > %s' % (
                    path,
                    branch
                ),
                error_callback
            )
    return extracted_strings


def parse_structure(structure, error_callback):
    extracted_strings = []

    containers = structure['vector']['container']

    if not isinstance(containers, list):
        containers = [containers]

    for container in containers:

        extracted_strings += extract_strings(
            container,
            container['@name'],
            error_callback
        )

        if 'items' not in container or container['items'] is None:
            continue

        list_of_desc = container['items']['desc']

        if not isinstance(list_of_desc, list):
            list_of_desc = [list_of_desc]

        for desc in list_of_desc:
            extracted_strings += extract_strings(
                desc,
                '%s > %s' % (
                    container['@name'],
                    desc['@name']
                ),
                error_callback
            )

    return extracted_strings


def dict_to_spreadsheet(structure)->str:
    languages_to_include = config.languages.keys()
    spreadsheet = [languages_to_include]

    for line in structure:
        spreadsheet_row = []
        for language in languages_to_include:
            spreadsheet_row.append(
                line[language] if language in line
                else ''
            )
        spreadsheet.append(spreadsheet_row)

    return config.line_separator.join([
        config.column_separator.join(line) for line in spreadsheet
    ])


def prompt(question):
    response = input('%s ' % question)
    return response.lower() in ['y','yes', 'true', 't', '1']


substr = lambda string, position: string[position[0]:position[1]]


def re_search(regex_pattern, string):
   match = re.search(regex_pattern, string, re.IGNORECASE)

   if not match:
       return ''

   else:
       return match.group()


def strip_strings(extracted_strings):

    stripped_strings = copy.deepcopy(extracted_strings)
    regex = r'[^%s]+' % config.full_charset

    for index, line in enumerate(stripped_strings):

        for position in ['start', 'end']:
            regex_pattern = ('^%s' if position=='start'
                else '%s$') % regex

            # match = test(line['en'])
            #
            # if not match:
            #     continue

            for language_code, \
                language_config in config.languages.items():

                if language_code not in line:
                    continue

                local_match = re_search(
                    regex_pattern,
                    line[language_code]
                )

                if local_match:
                    stripped_strings[index][language_code] = substr(
                        stripped_strings[index][language_code],
                        [len(local_match), None] if position == 'start'
                        else [0, -len(local_match)]
                    )

                # if local_match != match:
                #     response = error_callback(
                #         'unmatched_symbol_group',
                #         (
                #             '%s Symbol group of `%s` (%s) differs' +
                #             'from that of `%s` (en): %s'
                #         ) % (
                #             colored('ERROR: ', 'red'),
                #             line[language_code],
                #             language_code,
                #             line['en']
                #         )
                #     )

    return stripped_strings


def unique_strings(striped_strings):
    distinct_strings_dict = {}

    for line in striped_strings:
        if line['en'] in distinct_strings_dict:
            continue
        else:
            distinct_strings_dict[line['en']] = line

    return list(distinct_strings_dict.values())
