<?
	header("Content-type: image/png");

	$pid = file_get_contents("/tmp/broadcast.pid");
	$pid = intval($pid);
	@posix_kill($pid, SIGALRM );	

	readfile("img/frame.png");
?>
