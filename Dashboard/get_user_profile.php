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

$displayName = $_POST['displayName'];

$sql = <<<EOF
select title,display_name,team,hypothesis_posts,hypothesis_child_posts,total_posts from view_user_posts_frequency
where display_name='$displayName'
order by title;
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$data = '{"2007":[';

while ($row = pg_fetch_row($ret)) {
$data = $data.'{"name":"'.$row[0].'","values":{"#ofHypothesis":'.$row[3].',"#ofCommDesc":'.$row[4].',"#TotalPosts":'.$row[5].'}},';
	//$myarray = $row;
}

$data = substr($data, 0, -1);
$data = $data.'';
echo $data.']}';

//$users = '{"2007":[{"name":"Black Site Surveillance","values":{"#ofHypothesis":3,"#ofCommDesc":4,"#TotalPosts":5}},{"name":"Drug Interdiction","values":{"#ofHypothesis":2,"#ofCommDesc":5,"#TotalPosts":4}},{"name":"How Did Arthur Allen Die?","values":{"#ofHypothesis":5,"#ofCommDesc":3,"#TotalPosts":2}},{"name":"Kalukistan","values":{"#ofHypothesis":1,"#ofCommDesc":6,"#TotalPosts":7}},{"name":"Three Nations","values":{"#ofHypothesis":5,"#ofCommDesc":3,"#TotalPosts":2}},{"name":"Who Is the Spy?","values":{"#ofHypothesis":5,"#ofCommDesc":3,"#TotalPosts":2}}]}';

echo $users ;

?>
