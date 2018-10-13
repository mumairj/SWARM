<?php
$host        = "host=115.146.92.239";
$port        = "port=5432";
$dbname      = "dbname=swarm";
$credentials = "user=postgres password=root";

$db = pg_connect("$host $port $dbname $credentials");

if (!$db) 
{
    //echo "Error : Unable to open database\n";
} 
else 
{
    //echo "Opened database successfully\n";
}

$myQuestion = $_POST['myQuestion'];

$sql =<<<EOF
    select
    distinct pug.id,pug.title
	from chunk c
	inner join chunk_user_group_relation cugr on c.id = cugr.chunk_id
	inner join perm_user_group pug on pug.id=cugr.user_group_id
	where c.title='$myQuestion'
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$questions = '["';

$myarray = array();
$i=0;
while ($row = pg_fetch_row($ret)) {
    //$questions = $questions.$row[0] . '","';
	$myarray[] = $row;
	//$i=$i+1;
}

$questions = substr($questions, 0, -2);
$questions = $questions."]";

pg_close($db);

//echo $questions;
echo json_encode($myarray);

?>
