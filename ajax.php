<?
	require_once("config/config.php");
	require_once("signal.php");

	define("GET_CONFIG", 	0);
	define("RENAME_CAMERA", 1);
	define("UPDATE_GATE",	2);
	define("ADD_GATE",		3);
	define("DEL_GATE",		4);

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
							"x1"   => $x1,
							"y1"   => $y1,
							"x2"   => $x2,
							"y2"   => $y2,
							"id"   => count($cfg[$cam]["gates"])
						);

		echo json_encode($new_gate);

		array_push($cfg[$cam]["gates"], $new_gate);
		config_save($cfg);
		signal_python(RELOAD_CONFIG);
	}

	// send configuration to user browser
	if ($action == UPDATE_GATE) {
		$cfg = config_load();

		$cam = @$_POST['camera'];
		$id  = @$_POST['id'];
		$name= @$_POST['name'];
		$x1  = @$_POST['x1'];
		$x2  = @$_POST['x2'];
		$y1  = @$_POST['y1'];
		$y2  = @$_POST['y2'];
		if (!isset($cam) || !isset($id) || !isset($name) ||
			!isset($x1)	 || !isset($y1) || 
			!isset($x2)	 ||	!isset($y2)) {
			echo json_encode("Can't change gate - not all parameters received");
			return ;
		}

		// TODO: Skip if '$id' not found in 'gates'

		$cfg[$cam]["gates"][$id]["name"] = $name;
		$cfg[$cam]["gates"][$id]["x1"] 	= $x1;
		$cfg[$cam]["gates"][$id]["x2"] 	= $x2;
		$cfg[$cam]["gates"][$id]["y1"] 	= $y1;
		$cfg[$cam]["gates"][$id]["y2"] 	= $y2;

		config_save($cfg);
		signal_python(RELOAD_CONFIG);

		echo json_encode("OK, Gate updated");
		return;
	}
?>
