<?
	define("GET_CONFIG", 0);
	define("CONFIG_PATH", "config/lines.cfg");

	/*
		TODO: Add user authentication
	*/

	$action = @$_POST['action'];

	if (!isset($action)) {
		echo json_encode(array());
		return;
	}
	
	// Send configuration to user browser

	if ($action == GET_CONFIG) {
		$config_camera = '/^\s*([^:]+)\s*:\s*([0-9a-f]{2})[:-]([0-9a-f]{2})[:-]([0-9a-f]{2})[:-]([0-9a-f]{2})[:-]([0-9a-f]{2})[:-]([0-9a-f]{2})\s+(\d+)\s*x\s*(\d+)\s*$/iu';
		$config_gate   = '/^\s*(.+)\s*\((\d+)\s*,\s*(\d+)\)\s*\((\d+),(\d+)\)\s*$/u';

		$data = file(CONFIG_PATH);
		if ($data === NULL) {
			echo json_encode(NULL);
			return;
		}

		$config = array();

		$status = 0;
		for ($n=0; $n<sizeof($data); $n++) {
			$str = $data[$n];
		
			if (preg_match($config_camera, $str, $matches)) {
				$name  = $matches[1];
				$hw_id = $matches[2].$matches[3].$matches[4].$matches[5].$matches[6].$matches[7];
				$width = $matches[8];
				$height= $matches[9];

				$gates = array();
				array_push($config, 
						   array("hw" => $hw_id, 
						         "name" => $name, 
								 "gates" => &$gates,
								 "width" => $width,
								 "height"=> $height));
				$status = 1;
				continue;
			}

			if ($status == 1 && preg_match($config_gate, $str, $matches)) {
				array_push($gates, array("name"=>$matches[1],
										 "x1"  =>$matches[2],
										 "y1"  =>$matches[3],
										 "x2"  =>$matches[4],
										 "y2"  =>$matches[5]));
				continue;
			}
		}
		echo json_encode($config);
	}
?>
