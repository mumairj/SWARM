import psycopg2
import psycopg2.extras
import sys

reload(sys)  
sys.setdefaultencoding('utf8')

try:
    conn = psycopg2.connect("dbname='swarm' user='postgres' host='127.0.0.1' password='root'")
    print("DB Connected Successfully!")
except:
    print("I am unable to connect to the database")

cur = conn.cursor(cursor_factory=psycopg2.extras.DictCursor)

#First mark all hypothesis' parents!
try:
    cur.execute("select distinct display_name from \"user\"")
    rows = cur.fetchall()
    print("Rows: \n")
    i=0
    curInsert = conn.cursor()
    for row in rows:
        i+=1
        #print("Checking for",row['display_name'],"...")
        cur2 = conn.cursor(cursor_factory=psycopg2.extras.DictCursor)
        query="select c.id,c.content,c.display_name,c.variant from chunk_swarm c where c.parent_relation='comment' and c.content like '%"+row['display_name']+"%'";
        cur2.execute(query);
        #print(query)
        taggedRows = cur2.fetchall()
        for tagRow in taggedRows:
            print(row['display_name']+" tagged "+tagRow['display_name'])
            #ignore selfTags
            if(row['display_name']==tagRow['display_name']):
                continue;
            curInsert.execute("insert into swarm_user_tag_chunk(chunk_uuid,user_display_name,tagged_user_display_name) "
                              " values('"+tagRow['id']+"', '"+tagRow['display_name']+"', '"+row['display_name']+"')")
            conn.commit()
except Exception as e:
    print("Command execution failed.",str(e))
    conn.rollback()
finally:
    if conn:
        conn.close()


