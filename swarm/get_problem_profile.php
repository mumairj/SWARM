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

$problem = $_POST['problem'];

/* 
SQL check implemented to show only latest processed results on analytics server 
i.e. (c.last_edited/1000)<=(select timestamp from chunk_last_updated where type='sentiment')
since we are intersted in problems whose comments have been processed 
we will display data less than the last post's comment that was processed.
Note: chunk_last_updated will always contain 2 records i.e. one for the last processed post's sentiment,
and one for the last processed post's tag.
*/

$sql = <<<EOF
select
       c.id,
       q.problem_title,
       q.team,
       q.rating rating_team,
       round((ss.score*100/32)::decimal,2) rating_on_platform
from chunk c
left join view_quality_prob_hyp_team_avg q on q.hypothesis_id=c.id
left join swarm_scores ss on ss.team=q.team and ss.problem=q.problem_title
where
    q.rating is not null and ss.score is not null
    and c.id in (select distinct published_hypothesis_id  from view_published_hyp)
	and q.problem_title='$problem' and (c.last_edited/1000)<=(select timestamp from chunk_last_updated where type='sentiment')
order by q.problem_title;
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$teams = '"teams":["';
$teamrating = '"teamrating":[';
$onplatform = '"onplatform":[';

while ($row = pg_fetch_row($ret)) {
	$teams = $teams.$row[2].'","';
	$teamrating = $teamrating.$row[3].',';
	$onplatform = $onplatform.$row[4].',';
}

$teams = substr($teams, 0, -2);
$teams = $teams.']';

$teamrating = substr($teamrating, 0, -1);
$teamrating = $teamrating.']';

$onplatform = substr($onplatform, 0, -1);
$onplatform = $onplatform.']';


$data='{'.$teams.','.$teamrating.','.$onplatform.'}';
echo $data;


?>
