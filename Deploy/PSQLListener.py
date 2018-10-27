import select
import psycopg2
import psycopg2.extensions
import json
import uuid
import sys
import time
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

curs = conn.cursor()
curs.execute("LISTEN chunk_update;")

def checkSentiment(text):
    blob = TextBlob(text)
    sentiment = "neutral"
    if blob.sentiment.polarity < 0:
        sentiment = "negative"
    elif blob.sentiment.polarity == 0:
        sentiment = "neutral"
    else:
        sentiment = "positive"
    return sentiment

print "Waiting for notifications on channel 'chunk_update'"
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
            print("--->",json_data['variant'])
            print("--->",json_data['content'])
            timeNow=int(time.time())
            print('This->',timeNow)
            sentiment = checkSentiment(json_data['content']);
            curInsert = conn.cursor()
            curInsert.execute("delete from chunk_last_updated where type='sentiment'")
            insert_statement = "insert into chunk_last_updated values('"+json_data['id']+"',"+str(timeNow)+",'sentiment')"
            curInsert.execute(insert_statement)
            curInsert = conn.cursor()
            insert_statement = "insert into chunk_sentiment values('"+json_data['id']+"','"+sentiment+"')"
            curInsert.execute(insert_statement)
            #replace(strip_tags(content),'&nbsp;','')
