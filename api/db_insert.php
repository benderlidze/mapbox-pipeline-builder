<?
	error_reporting(E_ALL|E_STRICT);
	ini_set('display_errors', true);
	 ini_set('memory_limit', '512M');

	$db = new mysqli("localhost","serg_pipe","pipe_pass","serg_pipe");
	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	
	$string = file_get_contents("proj.geojson");
	$json_a = json_decode($string,true);
	
	foreach ($json_a['features'] as $key => $val){
		//var_dump($value); 
		$value =  $val['properties'];
		
		$pipe_order_no = $value['order_no'];
		$squares = $val['geometry']['coordinates'];
		$number =  explode("-", $value['pipeline_id'])[1];
		$segment =  explode("-", $value['pipeline_id'])[2];
		$geometry_source = $value['geometry_source'];
		$pipe_group_name = $value['group name'];
		
		//var_dump($squares);
		/*
		echo $number.'<br>';
		echo $segment.'<br>';
		echo $geometry_source.'<br>';
		*/
		
		$sql = 'INSERT INTO pipes set 
					pipe_number = "'.$number.'",
					pipe_order_no = "'.$pipe_order_no.'",
					pipe_segment_number = "'.$segment.'",
					pipe_group_name = "'.$pipe_group_name.'",
					pipe_geometry_source = "'.$geometry_source.'",
					pipe_geojson = \''.json_encode($val).'\',
					pipe_squares = \''.json_encode($squares).'\'
				';
		
		if ($db->query($sql) === TRUE) {
		  echo "New record created successfully";
		} else {
		  echo "Error: " . $sql . "<br>" . $db->error;
		}
		/**/

		echo '<hr>';
	}
	
	$db->close();
	
?>
