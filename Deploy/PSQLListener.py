import select
import psycopg2
import psycopg2.extensions
import json
import uuid
import sys

reload(sys)  
sys.setdefaultencoding('utf8')

try:
    conn = psycopg2.connect("dbname='swarm' user='postgres' host='127.0.0.1' password='root'")
    print("DB Connected Successfully!")
except:
    print("I am unable to connect to the database")
	
conn.set_isolation_level(psycopg2.extensions.ISOLATION_LEVEL_AUTOCOMMIT)

curs = conn.cursor()
curs.execute("LISTEN table_update;")

print "Waiting for notifications on channel 'table_update'"
while 1:
    if select.select([conn],[],[],5) == ([],[],[]):
        print "Timeout"
    else:
        conn.poll()
        while conn.notifies:
            notify = conn.notifies.pop(0)
            print "Got NOTIFY:", notify.pid, notify.channel, notify.payload
            json_data = json.loads(notify.payload)
            print("--->",json_data['id'])
            curInsert = conn.cursor()
            insert_statement = "insert into chunk_last_updated values('"+json_data['id']+"',10005)"
            print("--->",insert_statement)
            curInsert.execute(insert_statement)
/*
INSERT INTO public.chunk
        (
		id,
		public_visibility,
		created_at,
		last_edited,
		title,
		summary,
		content,
		content_type,
		description,
		parent_id,
		parent_relation,
		ext,
		state,
		due_date,
		variant,
		level,
		deleted,
		delete_at,
		published_date,
		start_date)
VALUES (
 uuid_generate_v4(),
true,
1530436803461,
1530599658488,
'uj',
'',
'',
'text/html',
'',
'd505c5b6-2bd3-4444-b761-4fa50ab612a1',
'hypothesis',
'',
'deleted',
1530434539870,
'hypothesis',
'0',
true,
1530599658488,
1530436807885,
null);

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
select uuid_generate_v4();

select * from chunk where title='uj';

insert into chunk_last_updated();
select timestamp;

CREATE OR REPLACE FUNCTION table_update_notify() RETURNS trigger AS $$
DECLARE
  id uuid;
BEGIN
  IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
    id = NEW.id;
  ELSE
    id = OLD.id;
  END IF;
  PERFORM pg_notify('table_update', json_build_object('table', TG_TABLE_NAME, 'id', id, 'type', TG_OP)::text);
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

select * from chunk_last_updated;
*/