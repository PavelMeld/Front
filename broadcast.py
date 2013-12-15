#!/usr/bin/python

import cv2
import os
import sys
import signal
import numpy
import re
import pprint

VIDEO_STUB  = "records/record.avi"
CONFIG_FILE = "config/config.txt"
CONFIG_CAM_REGEXP = '^\s*([^:]+)\s*:\s*([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})[:-]?([0-9a-f]{2})\s+(\d+)\s*x\s*(\d+)\s*$';
CONFIG_LINE_REGEXP= '^\s*(.+)\s*\((\d+)\s*,\s*(\d+)\)\s*\((\d+),(\d+)\)\s*$';

make_snapshot = False;
read_config   = False;
config 		  = [];

#
#
#	Signal handler checks for action initiated by remote process
#
#
def signal_handler(sig, frame):
	global make_snapshot;
	global read_config;

	if sig == signal.SIGALRM:
		make_snapshot = True;
	if sig == signal.SIGUSR1:
		read_config = True;

#
#
#	Object tracker main loop
#
#
def load_config():
	global CONFIG_FILE;
	global config;

	new_config = [];
	with open(CONFIG_FILE,"rU") as f:
		for text in f:

			cam = re.match(CONFIG_CAM_REGEXP, text, re.I);
			if cam !=None:
				name = cam.group(1).strip()
				hw_id='';
				for n in range(2,8):					
					hw_id=hw_id+cam.group(n);
				width  = int(cam.group(8));
				height = int(cam.group(9));

				gates    = [];
				new_item = {'name': name, 'hw' : hw_id, 'width': width, 'height': height ,'gates' : gates };
				new_config.append(new_item)
				continue;

			line = re.match(CONFIG_LINE_REGEXP, text, re.I);
			if line !=None:
				p = {
						'name'  : line.group(1).strip(),
						'p1'	: (int(line.group(2)), int(line.group(3))),
						'p2'	: (int(line.group(4)), int(line.group(5)))
					};
				gates.append(p)
	config = new_config;

#
#
#	Object tracker main loop
#
#
def tracker():
	global make_snapshot;
	global read_config;
	global config;
	global VIDEO_STUB;

	#TODO: Remove stub
	cv2.namedWindow("Camera", cv2.WINDOW_AUTOSIZE);
	capture = cv2.VideoCapture(VIDEO_STUB)
	#capture = cv.CreateCameraCapture(0)

	load_config()

	print 'Starting broadcast'
	while True:
		(ret, img) = capture.read()
		if ret == False:
			capture.set(cv2.cv.CV_CAP_PROP_POS_FRAMES, 0);
			continue;

		# TODO: Handle exactly our camera
		cam = config[0];
		for gate in cam["gates"]:
			p1 = gate["p1"];
			p2 = gate["p2"];
			cv2.line(img, p1, p2, 0xFF, 2)
			
		
		cv2.imshow("Camera", img)

		if cv2.waitKey(1) > -1:
			print "Use Ctrl+C to interrupt"
			pass;

		# Handle Events
		if make_snapshot:
			print "Write frame"
			# TODO: save image in cv2 style
			#cv.SaveImage("img/frame.png", img);
			make_snapshot = False;

		if read_config:
			print "Re-loading config"
			load_config()
			read_config = False;

	cv2.destroyWindow("Camera");


signal.signal(signal.SIGALRM, signal_handler);
signal.signal(signal.SIGUSR1, signal_handler);

print 'Writing PID to broadcast.pid'
with open('/tmp/broadcast.pid','w+') as f:
	f.write(str(os.getpid()));

tracker()
