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

$user = $_POST['user'];

$sql = <<<EOF
select
    distinct title
from chunk
where deleted=false and id in (select distinct chunk_id from chunk_user_group_relation
where user_group_id in (select team_id from team_members
where display_name='$user'));
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$users = '["';

$myarray = array();

while ($row = pg_fetch_row($ret)) {
    $users = $users.$row[0] . '","';
	//$myarray = $row;
}

$users = substr($users, 0, -2);
$users = $users."]";
//$questions='abcd';
pg_close($db);

echo $users ;

?>
