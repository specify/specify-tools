
import json

from db_utils import *
from specify_ranks import specify_ranks, required_ranks

KINGDOM = 'F'

def load_kingdom_ranks(kingdom_name):
    file = open('./col/ranks.json')
    ranks = json.load(file)
    raw_kingdom_ranks = [(int(_id), dat[0]) for (_id, dat) in ranks[kingdom_name].items()]
    sorted_ranks = sorted(raw_kingdom_ranks, key=lambda x: x[0])
    return list([name for (_, name) in sorted_ranks])

kingdom_ranks = [rank.lower() for rank in
                 load_kingdom_ranks(KINGDOM) ]

node_columns = [
    ('name', db.String(100)), # Just for potential indexing
    ('common_name', db.Text),
    ('author', db.Text),
    ('source', db.Text),
    ('guid', db.Text)
]

INCERTAE_SEDIS = ['incertae sedis', '', '', '', '']

EMPTY = ['', '', '', '', '']

def load_data(kingdom_name):
    # TODO: Allow custom paths
    file = open(f'./col/rows/{kingdom_name}.json')
    data = json.load(file)
    file.close()
    return data



TOP_ID = 3

def get_missing_ranks(parent, current):
    missing_ranks = [INCERTAE_SEDIS if rank in required_ranks else EMPTY
                     for rank in specify_ranks[parent:current]
                     if rank.lower() in kingdom_ranks]
    return missing_ranks

def make_rows(guid, kingdom_data, insert,
              parent_rank_id = TOP_ID, parent_rows=[]):
    current_node = kingdom_data[guid]
    #print(guid, current_node)
    current_data = current_node[0]
    rank_id = current_node[1]
    children = current_node[2]
    row_with_self = [*parent_rows,
                     *get_missing_ranks(parent_rank_id, rank_id-1),
                     (*current_data, guid)]
    if len(children) == 0:
        insert(row_with_self)
    for child in children:
        make_rows(child, kingdom_data, insert, rank_id, row_with_self)


CHUNK_COUNT = 1000

_rows = []

mapped_columns = [
    (f"{rank.lower()}_{column}", _type) for rank in kingdom_ranks
    for column, _type in node_columns
]


def insert_rows(engine, table):
    mapped_rows_to_columns = [{col_name: _row[idx]
                               for idx, col_name, _ in enumerate(mapped_columns)}
                              for _row in _rows]

    with engine.connect() as conn:
        result = conn.execute(
            db.insert(table),
            mapped_rows_to_columns
        )
        conn.commit()
    _rows.clear()


def insert_into_cache(engine, table, row):
    incoming_length = len(row)
    flat = [col_val for block in row for col_val in block]
    _rows.append([*flat, *((len(kingdom_ranks) - incoming_length)*EMPTY)])

    assert len(_rows[-1]) == len(mapped_columns), "Got bad rows"
    if len(_rows) == CHUNK_COUNT:
        insert_rows(engine, table)



def main():

    kingdom_data = load_data(KINGDOM)
    cache_engine = clean_and_create_cache_schema('col_cache_4', drop=True)
    table = build_table(cache_engine, f'{KINGDOM}_cache', mapped_columns)

    make_rows(KINGDOM, kingdom_data,
              lambda row: insert_into_cache(cache_engine, table, row))
    if len(_rows) > 0: # catch rows which were inserted at the end
        insert_rows(cache_engine, table)


if __name__ == '__main__':
    print(mapped_columns)
    main()





















































