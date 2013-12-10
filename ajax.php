<?
	require_once("config/config.php");

	define("GET_CONFIG", 	0);
	define("RENAME_CAMERA", 1);
	define("RENAME_GATE",	2);
	define("MOVE_GATE",		3);
	define("ADD_GATE",		4);
	define("DEL_GATE",		5);

	/*
		TODO: Add user authentication
	*/

	$action = @$_POST['action'];
	//$action = ADD_GATE;

	if (!isset($action)) {
		echo json_encode(array());
		return;
	}
	
	// send configuration to user browser
	if ($action == GET_CONFIG) {
		$cfg = config_load();
		echo json_encode($cfg);
	}

	// send configuration to user browser
	if ($action == ADD_GATE) {
		$cfg = config_load();

		$cam = @$_POST['camera'];
		if (!isset($cam))
			$cam = 0;
		$width  = $cfg[$cam]["width"];
		$height = $cfg[$cam]["height"];

		$name = "label".(count($cfg[$cam]["gates"]) + 1);
		$x1 = rand(15, $width-15);
		$x2 = rand(15, $width-15);
		$y1 = rand(15, $height-15);
		$y2 = rand(15, $height-15);

		$new_gate = array(
							"name" => $name,
							"x1" => $x1,
							"y1" => $y1,
							"x2" => $x2,
							"y2" => $y2
						);

		echo json_encode($new_gate);

		array_push($cfg[$cam]["gates"], $new_gate);
		config_save($cfg);
	}
?>
