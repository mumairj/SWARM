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
$team = $_POST['team'];


if($team=='null')
{
$sql = <<<EOF
select
       X.comment_due_date,
       X.problem_title,
       coalesce(Y.sentiment_count,0) pos_ct,
       coalesce(Z.sentiment_count,0) neu_ct,
       coalesce(K.sentiment_count,0) neg_ct,
	   coalesce(coalesce(Y.sentiment_count,0)+coalesce(Z.sentiment_count,0)+coalesce(K.sentiment_count,0)) totalComments
from (select
       distinct
       comment_due_date,
       problem_title
from view_comments_sentiments_problemwise
order by problem_title,comment_due_date)X
left join view_comments_sentiments_problemwise Y
on Y.comment_due_date=X.comment_due_date and X.problem_title=Y.problem_title and Y.sentiment='positive'
left join view_comments_sentiments_problemwise Z
on Z.comment_due_date=X.comment_due_date and Z.problem_title=Y.problem_title and Z.sentiment='neutral'
left join view_comments_sentiments_problemwise K
on K.comment_due_date=X.comment_due_date and K.problem_title=Y.problem_title and K.sentiment='negative'
where X.problem_title='$problem'
order by X.problem_title,X.comment_due_date;
EOF;
}
else
{
$sql = <<<EOF
select distinct
       X.comment_due_date,
       X.problem_title,
       coalesce(Y.sentiment_count,0) pos_ct,
       coalesce(Z.sentiment_count,0) neu_ct,
       coalesce(K.sentiment_count,0) neg_ct,
	   coalesce(coalesce(Y.sentiment_count,0)+coalesce(Z.sentiment_count,0)+coalesce(K.sentiment_count,0)) totalComments
from (select
       distinct
       comment_due_date,
       problem_title,
       team
from view_comments_sentiments
order by problem_title,comment_due_date)X
left join view_comments_sentiments Y
on Y.comment_due_date=X.comment_due_date and X.problem_title=Y.problem_title and X.team=Y.team and Y.sentiment='positive'
left join view_comments_sentiments Z
on Z.comment_due_date=X.comment_due_date and Z.problem_title=Y.problem_title and X.team=Y.team and Z.sentiment='neutral'
left join view_comments_sentiments K
on K.comment_due_date=X.comment_due_date and K.problem_title=Y.problem_title and X.team=Y.team and K.sentiment='negative'
where X.problem_title='$problem' and X.team='$team'
order by X.problem_title,X.comment_due_date;
EOF;
}

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
	if($i==0)
	{
		$dateRange = $dateRange.$row[0].'","';
		$positiveComments = $positiveComments.$row[2].',';
		$negativeComments = $negativeComments.$row[4].',';
		$neutralComments = $neutralComments.$row[3].',';
		$totalComments = $totalComments.$row[5].',';
	}
	else
	{
		$dateRange = $dateRange.$row[0].'","';
		$positiveComments = $positiveComments.$row[2].',';
		$negativeComments = $negativeComments.$row[4].',';
		$neutralComments = $neutralComments.$row[3].',';
		$totalComments = $totalComments.$row[5].',';
	}
	$i=$i+1;
}

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

$finalData='{'.$dateRange.','.$positiveComments.','.$negativeComments.','.$neutralComments.','.$totalComments.'}';

echo $finalData;


?>
