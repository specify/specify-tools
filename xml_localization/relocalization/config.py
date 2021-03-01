#
# MAIN CONFIGURATION OPTIONS
#

# needed if you want to use `populate_workdir.py` script
specify6_location = '/Users/mambo/site/git/specify6'

# location of schema_localization.xml files
working_directory = 'xml_localization/relocalization/workdir'

# Specify, which languages you are working with now
# Languages not present in this list would not be modified
# For each language, specify its HTML attributes
languages = {
    'en': {
        # xml_attributes for each <str> key of that language
        'xml_attributes': {
            'language': 'en',
        },
        'error_fixers': {
           # no error fixers are applicable to the 'en' language
        },
        # charset of that language
        # used in regular expressions
        # case-insensitive
        'charset': 'a-z',
    },
    # 'uk': {
    #     'xml_attributes': {
    #         'language': 'uk',
    #         'country': 'UA',
    #         'variant': '',
    #     },
    #     'error_fixers': {
    #         # Most fixes can be set to the following values
    #         # 'off' - ignore the issue, if possible
    #         # 'automatic' - fix, and don't prompt, if possible
    #         # 'prompt' - ask on each occurrence of this error
    #
    #         # Makes sure that the string has the same case as the `en`
    #         # variant
    #         'wrong_case': 'automatic',  # 'automatic' / 'prompt' / 'off'
    #
    #         # Makes sure that strings have the same prefix and suffix,
    #         # if any, as the `en` variant. Non letter characters at the
    #         # start or end of the string are considered a prefix or
    #         # suffix respectively
    #         'unmatched_symbol_group': 'automatic',
    #     },
    #     'charset': 'іа-яїґь',
    # },
    'ru': {
        'xml_attributes': {
            'language': 'ru',
            'country': 'RU',
            'variant': '',
        },
        'error_fixers': {
            'wrong_case': 'automatic',
            'unmatched_symbol_group': 'automatic',
        },
        'charset': 'а-я',
    },
}


# Like the `error_fixers` structure inside the `languages` dictionary,
# but includes global error fixes
error_fixers = {
    # Fix strings that don't have the `en` key
    # This can't be turned off, but can be set to 'prompt'
    'missing_en_key': 'automatic',
}


#
# OPTIONAL CONFIGURATION OPTIONS
#

# Language that is very likely to be present in the schema_localization file
# Used when English key is missing
stable_key = 'ru'

# symbols that are considered to be letters
# case-insensitive
full_charset = 'a-zіа-яїґь'

source_file_name = 'schema_localization.xml'
original_en_file_name = '_original.json'
striped_en_file_name = '_striped.json'
distinct_en_file_name = '_distinct.json'
editable_file_name = 'editable.tsv'
schema_editable_file_name = 'schema_editable.tsv'

line_separator = '\n'
column_separator = '\t'
schema_editable_file_separator = '%s~~~\/\/\/~~~%s' % (
    line_separator,
    line_separator
)