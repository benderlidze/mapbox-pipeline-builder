<?
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('log_errors', TRUE);
ini_set('error_log', 'errors.log');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

ob_flush();
ob_start();

$host= getenv('MYSQL_HOST');
$user = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');
$database = getenv('MYSQL_DATABASE');


$db = new mysqli($host, $user, $password, $database);
if ($db -> connect_errno > 0) {
	die('Unable to connect to database [' . $db -> connect_error . ']');
}

if (isset($_GET['isTiledInMapbox'])) {
	$isTiledInMapbox = $_GET['isTiledInMapbox'];
	$res = [];
	$sql = 'SELECT * from pipes 
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


if (isset($_GET['pipe_number']) && !isset($_GET['pipe_segment_number'])) {
	$pipe_number = (int)$_GET['pipe_number'];
	$res = [];
	$sql = 'SELECT * from pipes
				where pipe_number = ' . $pipe_number . '
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

	$sql = 'SELECT pipe_number,pipe_segment_number,pipe_geojson, pipe_geometry_source, pipe_group_name from pipes
			#where  pipe_geometry_source !="Approximate Geometry"
				';
	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		// output data of each row
		while ($row = $result -> fetch_assoc()) {
			if ($row['pipe_geojson'] !== "") {
				$p_g = true;
			} else {
				$p_g = false;
			}

			if ($row['pipe_geometry_source'] == "Approximate Geometry") {
				$completed = false;
			} else {
				$completed = true;
			}

			$res[] = array('number' => $row['pipe_number'], 'segment' => $row['pipe_segment_number'], 'group' => $row['pipe_group_name'], 'geometry' => $p_g, 'completed' => $completed);
		}
	}

	echo json_encode($res);
}
if (isset($_GET['pipe_number']) && isset($_GET['pipe_segment_number'])) {

	$res = [];
	$pipe_number = (int)$_GET['pipe_number'];
	$pipe_segment_number = (int)$_GET['pipe_segment_number'];

	$sql = 'SELECT * from pipes
				where
				pipe_number = ' . $pipe_number . '
				AND pipe_segment_number=' . $pipe_segment_number;
	$result = $db -> query($sql);
	if ($result -> num_rows > 0) {
		// output data of each row
		while ($row = $result -> fetch_assoc()) {
			//echo $row['pipe_number'];
			//echo $row['pipe_geojson'].'<hr>';
			if ($row['pipe_geojson'] !== "" && $row['pipe_geometry_source'] != "Approximate Geometry") {
				$res['pipe_geojson'] = json_decode($row['pipe_geojson']);
			}

			if ($row['pipe_squares'] !== "" && $row['pipe_geometry_source'] == "Approximate Geometry") {
				$res['pipe_squares'] = json_decode($row['pipe_squares']);
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
	$sql = 'SELECT * from pipes
					where
					' . $query . '
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
	file_put_contents('1.txt', ob_get_flush());
}

//$_POST = json_decode(file_get_contents('php://input'), null);

if (isset($_POST['insert_pipe']) && (int)$_POST['insert_pipe'] > 0) {

	$pipe_number = (int)$_POST['insert_pipe'];
	$pipe_segment_number = (int)$_POST['pipe_segment_number'];
	$pipe_score = $_POST['score'];
	$pipe_note = $_POST['note'];
	$pipe_geojson = $_POST['pipe_geojson'];



	$s = json_decode($_POST['pipe_geojson']);
	if(count($s->features) == 0){ // it is empty and we should set Approximate Geometry
		$type = 'Approximate Geometry';
	}else{
		$type = 'PL App';
	}


	$sql = 'INSERT INTO pipes SET
      			pipe_score = "' . $pipe_score . '",
				pipe_note = "' . $pipe_note . '",
				pipe_geojson = \'' . ($pipe_geojson) . '\',
				pipe_number = "' . $pipe_number . '",
				pipe_segment_number = "' . $pipe_segment_number . '",
				pipe_geometry_source = "'.$type.'",
				isTiledInMapox = FALSE
        	ON DUPLICATE KEY UPDATE
        		pipe_score = "' . $pipe_score . '",
				pipe_note = "' . $pipe_note . '",
				pipe_geojson = \'' . ($pipe_geojson) . '\',
				pipe_geometry_source = "'.$type.'",
				isTiledInMapox = FALSE
     		';


	if(isset($_POST['additional_pipe_ids']) && $_POST['additional_pipe_ids']!=''){
	$add = json_decode($_POST['additional_pipe_ids']);
	foreach ($add as $v) {
		$sql = 'INSERT INTO pipes SET
				pipe_score = "' . $pipe_score . '",
			pipe_note = "' . $pipe_note . '",
			pipe_geojson = \'' . ($pipe_geojson) . '\',
			pipe_geometry_source = "'.$type.'",
			isTiledInMapox = FALSE,

			pipe_number = "' . $v->project . '",
			pipe_segment_number = "' . $v->segment . '"
		ON DUPLICATE KEY UPDATE
			pipe_score = "' . $pipe_score . '",
			pipe_note = "' . $pipe_note . '",
			pipe_geojson = \'' . ($pipe_geojson) . '\',
			pipe_geometry_source = "'.$type.'",
			isTiledInMapox = FALSE
			';
		$db -> query($sql);
	}
}


	if ($db -> query($sql) === TRUE) {
		echo $sql;
	}
	file_put_contents('1.txt', ob_get_flush());
}

$db -> close();
exit();
?>
