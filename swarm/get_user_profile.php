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

$displayName = $_POST['user'];

$sql = <<<EOF
select
       userprob.problem,
       userprob.team,
       userprob.member,
       coalesce(userhyp.sum,0) hyp_count,
       coalesce(usercomm.sum,0) comm_count,
       coalesce(userdesc.sum,0) desc_count,
       (coalesce(userhyp.sum,0)+coalesce(usercomm.sum,0)+coalesce(userdesc.sum,0)) total_posts
from
(select c.title problem,t.title as team,t.display_name as member,c.id as problem_id,t.team_id,t.user_id from team_members t inner join chunk_user_group_relation cugr on t.team_id=cugr.user_group_id inner join chunk c on c.id=cugr.chunk_id where c.deleted=false and
display_name='$displayName'
)userprob
left join
(select coalesce(cpr1.parent_id,X.parent_id),c.title as problem_title,X.variant,sum(X.count) from( select parent_id,variant,count(*) from chunk_swarm c
where display_name='$displayName'
group by parent_id,variant)X left join chunk_parent_relation cpr1 on cpr1.child_id=X.parent_id inner join chunk c on c.id=coalesce(cpr1.parent_id,X.parent_id) group by coalesce(cpr1.parent_id,X.parent_id),c.title ,X.variant
)userhyp on userprob.problem=userhyp.problem_title and userhyp.variant='hypothesis'
left join
(select coalesce(cpr1.parent_id,X.parent_id),c.title as problem_title,X.variant,sum(X.count) from( select parent_id,variant,count(*) from chunk_swarm c
where display_name='$displayName'
group by parent_id,variant)X left join chunk_parent_relation cpr1 on cpr1.child_id=X.parent_id inner join chunk c on c.id=coalesce(cpr1.parent_id,X.parent_id) group by coalesce(cpr1.parent_id,X.parent_id),c.title ,X.variant
)usercomm on userprob.problem=usercomm.problem_title and usercomm.variant='comment'
left join
(select coalesce(cpr1.parent_id,X.parent_id),c.title as problem_title,X.variant,sum(X.count) from( select parent_id,variant,count(*) from chunk_swarm c
where display_name='$displayName'
group by parent_id,variant)X left join chunk_parent_relation cpr1 on cpr1.child_id=X.parent_id inner join chunk c on c.id=coalesce(cpr1.parent_id,X.parent_id) group by coalesce(cpr1.parent_id,X.parent_id),c.title ,X.variant
)userdesc on userprob.problem=userdesc.problem_title and userdesc.variant='text';
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$prob = '"problems":["';
$hyp = '"hyp":[';
$comm = '"comm":[';
$desc = '"desc":[';
$total = '"total":[';

while ($row = pg_fetch_row($ret)) {
	$prob = $prob.$row[0].' : '.$row[1].'","';
	$hyp = $hyp.$row[3].',';
	$comm = $comm.$row[4].',';
	$desc = $desc.$row[5].',';
	$total = $total.$row[6].',';
}

$prob = substr($prob, 0, -2);
$prob = $prob.']';

$hyp = substr($hyp, 0, -1);
$hyp = $hyp.']';

$comm = substr($comm, 0, -1);
$comm = $comm.']';

$desc = substr($desc, 0, -1);
$desc = $desc.']';

$total = substr($total, 0, -1);
$total = $total.']';

$data='{'.$prob.','.$hyp.','.$comm.','.$desc.','.$total.'}';
echo $data;


?>
