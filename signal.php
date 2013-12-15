<?
	define("RELOAD_CONFIG", 	10);		// SIGUSR1
	define("MAKE_SCREENSHOT", 	14);		// SIGALRM

	/*
		Signal	 Python action
		-----------------------------
		SIGALRM  save screenshots
		SIGUSR1  reload configuration
	*/

	function signal_python($signal) {
		$pid = file_get_contents("/tmp/broadcast.pid");
		$pid = intval($pid);
		@posix_kill($pid, $signal);	
	}

	//signal_python(RELOAD_CONFIG);
?>
