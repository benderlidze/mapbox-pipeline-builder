<?
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('log_errors', TRUE);
ini_set('error_log', 'errors.log');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

ob_flush();
ob_start();

$db = new mysqli("localhost", "serg_pipe", "pipe_pass", "serg_pipe");
if ($db -> connect_errno > 0) {
	die('Unable to connect to database [' . $db -> connect_error . ']');
}


//SELECT * FROM `pipes` WHERE pipe_id = (select MAX(pipe_id) from pipes where `pipe_number` = 14921 and `pipe_segment_number`= 1)
//SELECT pipe_id, pipe_number, pipe_segment_number, count(pipe_id) as c FROM pipes group by pipe_number, pipe_segment_number 
//HAVING c > 1
//ORDER BY c DESC

/*
if (isset($_GET['isTiledInMapbox'])) {
	$isTiledInMapbox = $_GET['isTiledInMapbox'];
	$res = [];
	$sql = 'SELECT * from pipe_v3 
				where `isTiledInMapox` = '.$isTiledInMapbox.' 
				and pipe_geometry_source = "PL App"
			';
	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		while ($row = $result -> fetch_assoc()) {
			if ($row['pipe_geojson'] !== "") {
				$res[] = array('number' => $row['pipe_number'], 'segment' => $row['pipe_segment_number'], 'geometry'=>$row['pipe_geojson']);
			}
		}
	}

	echo json_encode($res);
}
*/

if (isset($_GET['pipe_number']) && !isset($_GET['pipe_segment_number'])) {
	$pipe_number = (int)$_GET['pipe_number'];
	$res = [];
	$sql = 'SELECT * from pipe_v3 
				where pipe_pipeline_id = ' . $pipe_number . '
				#and pipe_geometry_source =="Approximate Geometry"
				';
	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		$res = Array('number' => $result -> num_rows);
	}
	echo json_encode($result -> num_rows);
}

if (isset($_GET['all_pipes'])) {

	$res = [];

	$sql = 'SELECT * from pipe_v3				';
	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		// output data of each row
		while ($row = $result -> fetch_assoc()) {
			if ($row['geometry'] !== "") {
				$p_g = true;
			} else {
				$p_g = false;
			}

			$res[] = array('number' => $row['pipe_pipeline_id'],  'geometry' => $p_g);
		}
	}

	echo json_encode($res);
}
if (isset($_GET['pipe_pipeline_id']) ) {

	$res = [];
	$pipe_number = $_GET['pipe_pipeline_id'];

	$sql = 'SELECT * from pipe_v3 
				where 
				pipe_pipeline_id = "' . $pipe_number . '" 
				limit 1 
				';
	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		// output data of each row
		while ($row = $result -> fetch_assoc()) {
			//echo $row['pipe_number'];
			//echo $row['pipe_geojson'].'<hr>';
			if ($row['geometry'] !== "" ) {
				$res['geometry'] = json_decode($row['geometry']);
			}

		}
	}

	echo json_encode($res);
}

if (isset($_POST['query_lines'])) {

	$lines = json_decode($_POST['query_lines']);
	$elements = [];
	foreach ($lines as $value) {
		//$elements[] = '(`pipe_number` = ' . $value[0] . ' and `pipe_segment_number` = ' . $value[1] . ')';
		//$elements[] = '(`pipe_number` = ' . $value[0] .' and pipe_geometry_source =="Approximate Geometry" )';
		$elements[] = '(`pipe_number` = ' . $value[0] . ' and pipe_geometry_source !="Approximate Geometry")';
	}
	$query = implode(" OR ", $elements);

	$res = [];
	$sql = 'SELECT * from pipe_v3 
					where
					' . $query . '
				';

	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		while ($row = $result -> fetch_assoc()) {
			if ($row['pipe_geojson'] !== "") {
				$res[] = array('number' => $row['pipe_pipeline_id'],  'geometry'=>$row['geometry']);
			}
		}
	}

	echo json_encode($res);
	file_put_contents('1.txt', ob_get_flush());
}

//$_POST = json_decode(file_get_contents('php://input'), null);

if (isset($_POST['insert_pipe'])) {

	$pipe_number = $_POST['insert_pipe'];
	$pipe_geojson = $_POST['pipe_geojson'];
	$pipe_score = $_POST['score'];
	$pipe_note = $_POST['note'];
	
	$sql = 'INSERT INTO pipe_v3 SET
				pipe_score = "' . $pipe_score . '",
				pipe_note = "' . $pipe_note . '",
				pipe_pipeline_id = "'.$pipe_number.'",
				geometry = \'' . ($pipe_geojson) . '\'
        	ON DUPLICATE KEY UPDATE
        		pipe_score = "' . $pipe_score . '",
				pipe_note = "' . $pipe_note . '",
        		geometry = \'' . ($pipe_geojson) . '\'
     		';
	
	if ($db -> query($sql) === TRUE) {
		echo $sql;
	}
	
}

$db -> close();
exit();
?>