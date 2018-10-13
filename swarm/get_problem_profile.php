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

$problem = $_POST['problem'];

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
	and q.problem_title='$problem'
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
