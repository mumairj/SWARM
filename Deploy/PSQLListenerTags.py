import select
import psycopg2
import psycopg2.extensions
import psycopg2.extras
import json
import uuid
import sys
from textblob import TextBlob

reload(sys)  
sys.setdefaultencoding('utf8')
#testing commit via unimelb's git: Masters research lab! (M. Umair)
try:
    conn = psycopg2.connect("dbname='swarm' user='postgres' host='127.0.0.1' password='root'")
    print("DB Connected Successfully!")
except:
    print("I am unable to connect to the database")

conn.set_isolation_level(psycopg2.extensions.ISOLATION_LEVEL_AUTOCOMMIT)

curSelect = conn.cursor(cursor_factory=psycopg2.extras.DictCursor)

curs = conn.cursor()
curs.execute("LISTEN author_update;")

print "Waiting for notifications on channel 'author_update'"
while 1:
    if select.select([conn],[],[],5) == ([],[],[]):
        print "Timeout"
    else:
        conn.poll()
        while conn.notifies:
            notify = conn.notifies.pop(0)
            print "Got NOTIFY:", notify.pid, notify.channel, notify.payload
            json_data = json.loads(notify.payload)
            print("--->",json_data['chunk_uuid'])
            print("--->",json_data['user_display_name'])
            print("--->",json_data['content'])
            content = json_data['content']
            user_display_name = json_data['user_display_name']
            chunk_uuid = json_data['chunk_uuid']
            curInsert = conn.cursor()
            insert_statement = "insert into chunk_last_updated values('"+chunk_uuid+"',10005)"
            curInsert.execute(insert_statement)
            curSelect.execute("select distinct display_name from \"user\"")
            rows = curSelect.fetchall()
            for row in rows:
                if(row['display_name'] in content):
                    curInsert = conn.cursor()
                    insert_statement = "insert into swarm_user_tag_chunk values('"+user_display_name+"','"+row['display_name']+"','"+chunk_uuid+"')"
                    curInsert.execute(insert_statement)