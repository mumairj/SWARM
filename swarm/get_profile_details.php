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

$user = $_POST['user'];
$problem = $_POST['problem'];

$sql = <<<EOF
select
       userprob.problem,
       userprob.team,
       userprob.member,
       coalesce(userhyp.sum,0) hyp_count,
       coalesce(usercomm.sum,0) comm_count,
       coalesce(userdesc.sum,0) desc_count,
       (coalesce(userhyp.sum,0)+coalesce(usercomm.sum,0)+coalesce(userdesc.sum,0)) total_posts,
       coalesce(usersentp.count,0) pos_comm,
       coalesce(usersentneg.count,0) neg_comm,
       coalesce(usersentneu.count,0) peu_comm,
       coalesce(userincoming.sum,0) incoming_pings,
       coalesce(userinoutgoing.count,0) outgoing_pings
from
(select c.title problem,t.title as team,t.display_name as member,c.id as problem_id,t.team_id,t.user_id from team_members t inner join chunk_user_group_relation cugr on t.team_id=cugr.user_group_id inner join chunk c on c.id=cugr.chunk_id where c.deleted=false and
display_name='$user'
)userprob
left join
(select coalesce(cpr1.parent_id,X.parent_id),c.title as problem_title,X.variant,sum(X.count) from( select parent_id,variant,count(*) from chunk_swarm c
where display_name='$user'
group by parent_id,variant)X left join chunk_parent_relation cpr1 on cpr1.child_id=X.parent_id inner join chunk c on c.id=coalesce(cpr1.parent_id,X.parent_id) group by coalesce(cpr1.parent_id,X.parent_id),c.title ,X.variant
)userhyp on userprob.problem=userhyp.problem_title and userhyp.variant='hypothesis'
left join
(select coalesce(cpr1.parent_id,X.parent_id),c.title as problem_title,X.variant,sum(X.count) from( select parent_id,variant,count(*) from chunk_swarm c
where display_name='$user'
group by parent_id,variant)X left join chunk_parent_relation cpr1 on cpr1.child_id=X.parent_id inner join chunk c on c.id=coalesce(cpr1.parent_id,X.parent_id) group by coalesce(cpr1.parent_id,X.parent_id),c.title ,X.variant
)usercomm on userprob.problem=usercomm.problem_title and usercomm.variant='comment'
left join
(select coalesce(cpr1.parent_id,X.parent_id),c.title as problem_title,X.variant,sum(X.count) from( select parent_id,variant,count(*) from chunk_swarm c
where display_name='$user'
group by parent_id,variant)X left join chunk_parent_relation cpr1 on cpr1.child_id=X.parent_id inner join chunk c on c.id=coalesce(cpr1.parent_id,X.parent_id) group by coalesce(cpr1.parent_id,X.parent_id),c.title ,X.variant
)userdesc on userprob.problem=userdesc.problem_title and userdesc.variant='text'
left join (select  problem_title,sentiment,  count(*) from view_comments
where  comment_author='$user' group by sentiment,problem_title
)usersentp on usersentp.problem_title=userprob.problem and usersentp.sentiment='positive'
left join (select  problem_title,sentiment,  count(*) from view_comments
where  comment_author='$user' group by sentiment,problem_title
)usersentneg on usersentneg.problem_title=userprob.problem and usersentneg.sentiment='negative'
left join (select  problem_title,sentiment,  count(*) from view_comments
where  comment_author='$user' group by sentiment,problem_title
)usersentneu on usersentneu.problem_title=userprob.problem and usersentneu.sentiment='neutral'
left join (select title,sum(src_trgt_tagged_ct) from view_tags
where trgt='$user'
group by title)userincoming on userincoming.title=userprob.problem
left join (select title,count(*) from view_tags
where src='$user'
group by title)userinoutgoing on userinoutgoing.title=userprob.problem
where userprob.problem='$problem';
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$posts = '"posts":[{"name":"Hyp.","y":';

while ($row = pg_fetch_row($ret)) {
	$posts = $posts.$row[3].',"sliced": true,"selected": true},["Comm.",'.$row[4].'],["Desc.",'.$row[5].']],"sentiments":[{"name":"Pos.","y":'.$row[7].',"sliced": true,"selected": true},["Neg.",'.$row[8].'],["Neu.",'.$row[9].']],"pings":[{"name":"Inc.","y":'.$row[10].',"sliced": true,"selected": true},["Out.",'.$row[11].']]';
}



$sql = <<<EOF
select
       X.problem_title,
       X.team,
       X.comment_author,
       X.comment_due_date,
       coalesce(Ypos.count,0)  pos_ct,
       coalesce(Yneg.count,0)  neg_ct,
       coalesce(Yneu.count,0)  neu_ct,
       (coalesce(Ypos.count,0)+coalesce(Yneg.count,0)+coalesce(Yneu.count,0)) tot_ct
from
(select problem_title, team,comment_author, comment_due_date :: date
 from view_comments
where comment_due_date is not null
group by problem_title, team, comment_author, comment_due_date :: date
)X
left join (select problem_title, team,comment_author, sentiment, comment_due_date :: date, count(*) from view_comments group by problem_title, team, comment_author,sentiment, comment_due_date :: date order by problem_title, comment_author, comment_due_date :: date
)Ypos on X.problem_title=Ypos.problem_title and X.team=Ypos.team and X.comment_author=Ypos.comment_author and X.comment_due_date=Ypos.comment_due_date and Ypos.sentiment='positive'
left join (select problem_title, team,comment_author, sentiment, comment_due_date :: date, count(*) from view_comments group by problem_title, team, comment_author,sentiment, comment_due_date :: date order by problem_title, comment_author, comment_due_date :: date
)Yneg on X.problem_title=Yneg.problem_title and X.team=Yneg.team and X.comment_author=Yneg.comment_author and X.comment_due_date=Yneg.comment_due_date and Yneg.sentiment='negative'
left join (select problem_title, team,comment_author, sentiment, comment_due_date :: date, count(*) from view_comments group by problem_title, team, comment_author,sentiment, comment_due_date :: date order by problem_title, comment_author, comment_due_date :: date
)Yneu on X.problem_title=Yneu.problem_title and X.team=Yneu.team and X.comment_author=Yneu.comment_author and X.comment_due_date=Yneu.comment_due_date and Yneu.sentiment='neutral'
where X.problem_title='$problem' and X.comment_author='$user';
EOF;


$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

$dateRange = '"dateRange":["';
$positiveComments = '"positiveComments":[';
$negativeComments = '"negativeComments":[';
$neutralComments = '"neutralComments":[';
$totalComments = '"totalComments":[';
$i=0;
while ($row = pg_fetch_row($ret)) {
	$dateRange = $dateRange.$row[3].'","';
	$positiveComments = $positiveComments.$row[4].',';
	$negativeComments = $negativeComments.$row[5].',';
	$neutralComments = $neutralComments.$row[6].',';
	$totalComments = $totalComments.$row[7].',';
	$i=$i+1;

}

if($i==0)
{
$finalData='"dateRange": [],"positiveComments": [],"negativeComments": [],"neutralComments": [],"totalComments": []';
}
else{
$dateRange = substr($dateRange, 0, -2);
$dateRange = $dateRange.']';

$positiveComments = substr($positiveComments, 0, -1);
$positiveComments = $positiveComments.']';

$negativeComments = substr($negativeComments, 0, -1);
$negativeComments = $negativeComments.']';

$neutralComments = substr($neutralComments, 0, -1);
$neutralComments = $neutralComments.']';

$totalComments = substr($totalComments, 0, -1);
$totalComments = $totalComments.']';

$finalData=$dateRange.','.$positiveComments.','.$negativeComments.','.$neutralComments.','.$totalComments;

}



$data='{'.$posts.','.$finalData.'}';
echo $data;


?>
