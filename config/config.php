<?
	define("CONFIG_PATH", "config/config.txt");

////////////////////////////////////////////////////////////////////////////////
//	
//	
//	Load configuration
//
//
////////////////////////////////////////////////////////////////////////////////
	function config_load() {
		$config_camera = '/^\s*([^:]+)\s*:\s*([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})\s+(\d+)\s*x\s*(\d+)\s*$/iu';
		$config_gate   = '/^\s*(.+)\s*\((\d+)\s*,\s*(\d+)\)\s*\((\d+),(\d+)\)\s*$/u';

		$data = file(CONFIG_PATH);
		if ($data === NULL) {
			return array();
		}

		$config = array();

		$status = 0;
		for ($n=0; $n<sizeof($data); $n++) {
			$str = $data[$n];
		
			if (preg_match($config_camera, $str, $matches)) {
				$name  = trim($matches[1]);
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
				array_push($gates, array("name"=>trim($matches[1]),
										 "x1"  =>$matches[2],
										 "y1"  =>$matches[3],
										 "x2"  =>$matches[4],
										 "y2"  =>$matches[5]));
				continue;
			}
		}

		return $config;
	}

////////////////////////////////////////////////////////////////////////////////
//	
//	
//	Save configuration
//
//
////////////////////////////////////////////////////////////////////////////////
	function config_save($cfg) {
		$h = fopen(CONFIG_PATH,"w+");
		foreach ($cfg as $cam) {
			$cam_info = $cam["name"]." : ".$cam["hw"]."\t".$cam["width"]."x".$cam["height"]."\n";
			fwrite($h, $cam_info);
			foreach ($cam["gates"] as $gate) {
				$gate_info = "\t".$gate["name"]."\t(".$gate["x1"].",".$gate["y1"].")\t(".$gate["x2"].",".$gate["y2"].")\n";
				fwrite($h, $gate_info);
			}
		}
		fclose($h);
	}

?>
