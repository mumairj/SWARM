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
$teamName = $_POST['teamName'];

$sql = <<<EOF
   
   select 
	   src,
	   trgt,
	   src_trgt_tagged_ct 
   from view_tags
		where title='$myQuestion' and team='$teamName'
EOF;

//echo $sql;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$myJSONlinks = '"links": [';
$i=0;
while ($row = pg_fetch_row($ret)) {
    $myJSONlinks = $myJSONlinks . '{"source":"' . $row[0] . '",';
    $myJSONlinks = $myJSONlinks . '"target":"' . $row[1] . '",';
    $myJSONlinks = $myJSONlinks . '"tags":"' . $row[2] . '"},';
	$i=$i+1;
}

if($i==0)
{
	$myJSONlinks = $myJSONlinks . "]";
}
else
{
	$myJSONlinks = substr($myJSONlinks, 0, -1);
	$myJSONlinks = $myJSONlinks . "]";
}
//echo "-------->" . $myJSONlinks . "<---------";

$sqlUsers = <<<EOF
 select
	   trgt,
	   sum(src_trgt_tagged_ct) as ct
   from view_tags
		where title='$myQuestion' and team='$teamName'
    group by trgt

    union

  select distinct src,1 from view_tags
      where title='$myQuestion' and
      src not in (select distinct trgt from view_tags where title='$myQuestion');

EOF;

$retsqlUsers = pg_query($db, $sqlUsers);

if (!$retsqlUsers) {
    echo pg_last_error($db);
    exit;
}

$myarray     = array();
$myJSONnodes = '"nodes": [';
while ($rowsqlUsers = pg_fetch_row($retsqlUsers)) {
    $myJSONnodes = $myJSONnodes . '{"name":"' . $rowsqlUsers[0] . '",';
    $myJSONnodes = $myJSONnodes . '"id":"' . $rowsqlUsers[0] . '",';
	$myJSONnodes = $myJSONnodes . '"weight":"' . $rowsqlUsers[1] . '"},';
    $myarray[] = $rowsqlUsers;
}

$myJSONnodes = substr($myJSONnodes, 0, -1);
$myJSONnodes = $myJSONnodes . "],";
//echo "-------->" . $myJSONnodes . "<---------";

$finalJSON="{".$myJSONnodes.$myJSONlinks."}";

//echo "-------->".$finalJSON."<---------";

pg_close($db);


$name      = $_POST['name'];
$last_name = $_POST['last_name'];
echo $finalJSON;

?>
