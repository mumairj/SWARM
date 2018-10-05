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
//
$sql = <<<EOF
select
       X.comment_due_date,
       X.problem_title,
       coalesce(Y.sentiment_count,0) pos_ct,
       coalesce(Z.sentiment_count,0) neu_ct,
       coalesce(K.sentiment_count,0) neg_ct
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
where X.problem_title='Drug Interdiction'
order by X.problem_title,X.comment_due_date;
EOF;

$ret = pg_query($db, $sql);

if (!$ret) {
    echo pg_last_error($db);
    exit;
}

//{"dateRange":[]}

$dateRange = '"dateRange":["';
$positiveComments = '"positiveComments":[';
$negativeComments = '"negativeComments":[';
$i=0;
while ($row = pg_fetch_row($ret)) {
	
	if($i==0)
	{
		$dateRange = $dateRange.$row[0].'","';
		$positiveComments = $positiveComments.$row[2].',';
		$negativeComments = $negativeComments.$row[4].',';
	}
	else
	{
		$dateRange = $dateRange.$row[0].'","';
		$positiveComments = $positiveComments.$row[2].',';
		$negativeComments = $negativeComments.$row[4].',';
	}
	
	$i=$i+1;
	//$myarray = $row;
}

$dateRange = substr($dateRange, 0, -2);
$dateRange = $dateRange.']';
$positiveComments = substr($positiveComments, 0, -1);
$positiveComments = $positiveComments.']';
$negativeComments = substr($negativeComments, 0, -1);
$negativeComments = $negativeComments.']';
$finalData='{'.$dateRange.','.$positiveComments.','.$negativeComments.'}';
echo $finalData;

//$users = '{"2007":[{"name":"Black Site Surveillance","values":{"#ofHypothesis":3,"#ofCommDesc":4,"#TotalPosts":5}},{"name":"Drug Interdiction","values":{"#ofHypothesis":2,"#ofCommDesc":5,"#TotalPosts":4}},{"name":"How Did Arthur Allen Die?","values":{"#ofHypothesis":5,"#ofCommDesc":3,"#TotalPosts":2}},{"name":"Kalukistan","values":{"#ofHypothesis":1,"#ofCommDesc":6,"#TotalPosts":7}},{"name":"Three Nations","values":{"#ofHypothesis":5,"#ofCommDesc":3,"#TotalPosts":2}},{"name":"Who Is the Spy?","values":{"#ofHypothesis":5,"#ofCommDesc":3,"#TotalPosts":2}}]}';

echo $users ;

?>
