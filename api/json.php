<?
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('log_errors', TRUE);
ini_set('error_log', 'errors.log');
ini_set('memory_limit', '512M'); // 4 GBs minus 1 MB

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.time().'.json"');

$host= 'petro-ninja-mysql.csekrpixufxm.us-east-1.rds.amazonaws.com';
$user = 'admin';
$password = 'baNZyj2t7c';
$database = 'v2_petroninja_pipelines';


$db = new mysqli($host, $user, $password, $database);


if ($db -> connect_errno > 0) {
	die('Unable to connect to database [' . $db -> connect_error . ']');
}


	$res = [];
	$sql = 'SELECT * from pipes
				where
				pipe_geometry_source !="Approximate Geometry"

				';
	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		while ($row = $result -> fetch_assoc()) {
			// $res[] = array('number' => $row['pipe_number'], 'segment' => $row['pipe_segment_number'], 'geometry'=>$row['pipe_geojson']);
			$temp = json_decode($row['pipe_geojson']);
			if($temp->type == "FeatureCollection"){
				$res[] = json_encode($temp->features);
			}else{
				$res[] = json_encode($temp);
			}

		}
	}

	$json = implode(',',$res);

	echo '{
	  "type": "FeatureCollection",
	  "features": ['.$json.']
	}';


$db -> close();
exit();
?>
