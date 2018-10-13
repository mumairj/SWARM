<?php
$configs = include('config.php');

$host        = "host=".$configs['host'];
$port        = "port=".$configs['port'];
$dbname      = "dbname=".$configs['dbname'];
$credentials = "user=".$configs['user']." password=".$configs['password'];

   $db = pg_connect( "$host $port $dbname $credentials"  );
   if(!$db) {
      //echo "Error : Unable to open database\n";
   } else {
      //echo "Opened database successfully\n";
   }

   $sql =<<<EOF
      select c.variant,X.* from
		(
		select
			sutc.user_display_name,
			sutc.tagged_user_display_name,
			c.base_parent_id,
			c.variant thread_type,
			count(tagged_user_display_name)
		from swarm_user_tag_chunk sutc
		inner join chunk c on c.id=sutc.chunk_uuid
		group by
		sutc.user_display_name,
		sutc.tagged_user_display_name,
		c.base_parent_id,
		c.variant
		)X
		inner join chunk c on c.id=X.base_parent_id
EOF;

   $ret = pg_query($db, $sql);
   
   if(!$ret) {
      echo pg_last_error($db);
      exit;
   } 
   $myarray = array();
   while($row = pg_fetch_row($ret)) {
      //echo "ID = ". $row[0] . "\n";
	  $myarray[] = $row;
   }
   
   //echo json_encode($myarray);
   
   //echo "Operation done successfully\n";
   pg_close($db);


	$name = $_POST['name'];
    $last_name = $_POST['last_name'];
    echo '{"nodes":[{"name":"Pan","label":"Person","id":1},{"name":"Michael","label":"Person","id":2},{"name":"Neo4j","label":"Database","id":3},{"name":"Graph Database","label":"Database","id":10}],"links":[{"source":1,"target":2,"type":"KNOWS","since":2010},{"source":1,"target":3,"type":"FOUNDED"},{"source":2,"target":3,"type":"WORKS_ON"},{"source":3,"target":10,"type":"IS_A"}]}';

?>