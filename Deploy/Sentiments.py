import psycopg2
import psycopg2.extras
from textblob import TextBlob
import sys

reload(sys)  
sys.setdefaultencoding('utf8')

try:
    conn = psycopg2.connect("dbname='swarm' user='postgres' host='127.0.0.1' password='root'")
    print("DB Connected Successfully!")
except:
    print("I am unable to connect to the database")

cur = conn.cursor(cursor_factory=psycopg2.extras.DictCursor)

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

#First mark all hypothesis' parents!
try:
    cur.execute("select id,replace(strip_tags(content),'&nbsp;','') cleaned_content from chunk")
    rows = cur.fetchall()
    print("Rows: \n")
    i=0
    curInsert = conn.cursor()
    curInsert.execute("delete from chunk_sentiment")
    for row in rows:
        #print(row['cleaned_content'])
        sentiment = checkSentiment(row['cleaned_content'])
        print(i)
        i=i+1
        curInsert.execute("insert into chunk_sentiment(chunk_id,sentiment) values('"+row['id']+"','"+sentiment+"')")
        conn.commit()
except Exception as e:
    print("Command execution failed.",str(e))
    conn.rollback()
finally:
    if conn:
        conn.close()

