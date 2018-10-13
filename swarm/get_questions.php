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

$sql = <<<EOF
select distinct title from chunk where parent_id is null and deleted=false;
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
