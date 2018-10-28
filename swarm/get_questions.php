<?php

$configs = include('config.php');

$host        = "host=".$configs['host'];
$port        = "port=".$configs['port'];
$dbname      = "dbname=".$configs['dbname'];
$credentials = "user=".$configs['user']." password=".$configs['password'];

$db = pg_connect("$host $port $dbname $credentials");

if (!$db) 
{
    //echo "Error : Unable to open database\n";
} 
else 
{
    //echo "Opened database successfully\n";
}

/* 
SQL check implemented to show only latest processed results on analytics server 
i.e. (last_edited/1000)<=(select max(timestamp) from chunk_last_updated)
Getting the max datetime of processed chunks.
*/

$sql = <<<EOF
select distinct title from chunk where parent_id is null and deleted=false and (last_edited/1000)<=(select max(timestamp) from chunk_last_updated);
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$questions = '["';

$myarray = array();

while ($row = pg_fetch_row($ret)) {
    $questions = $questions.$row[0] . '","';
	//$myarray = $row;
}

$questions = substr($questions, 0, -2);
$questions = $questions."]";
//$questions='abcd';
pg_close($db);

echo $questions ;

?>
