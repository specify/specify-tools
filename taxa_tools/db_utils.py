import sqlalchemy as db

# TODO: Move this to docker
get_db_url = lambda schema: f'mysql://%s:%s@%s:%s{f"/{schema}" if schema is not None else ""}?charset=utf8' % (
        'root',
        'root',
        'localhost',
        3310)

_engine = db.create_engine(get_db_url(None))

metadata = db.MetaData()

def clean_and_create_cache_schema(cache_schema, drop=False):
        if drop:
                with _engine.connect() as connection:
                        connection.execute(db.text(f"DROP SCHEMA IF EXISTS {cache_schema}"))
                        connection.execute(db.text(f"CREATE SCHEMA {cache_schema}"))
                        connection.commit()
        return db.create_engine(get_db_url(cache_schema))

def build_table(cache_engine, cache_name, node_columns):
        columns = [db.Column(column_key, column_type,
                             nullable=True # this will be true for incertae sedis
                             )
                   for column_key, column_type in node_columns]
        CacheTable = db.Table(
                cache_name,
                metadata,
                *columns
        )
        metadata.create_all(cache_engine)
        return CacheTable
