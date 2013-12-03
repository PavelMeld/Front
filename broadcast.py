#!/usr/bin/python

import cv
import os
import signal

make_snapshot = False;

def alarm_hanlder(sig, frame):
	global make_snapshot;
	make_snapshot = True;

cv.NamedWindow("Camera", 1);
capture = cv.CreateCameraCapture(0)
signal.signal(signal.SIGALRM, alarm_hanlder);

print 'Writing PID to broadcast.pid'
with open('/tmp/broadcast.pid','w+') as f:
	f.write(str(os.getpid()));
print 'Starting broadcast'
while True:
	img = cv.QueryFrame(capture)
	cv.ShowImage("Camera", img)
	if cv.WaitKey(1) > -1:
		break;
	if make_snapshot:
		print "Write frame"
		cv.SaveImage("img/frame.png", img);
		make_snapshot = False;

cv.DestroyWindow("Camera");
