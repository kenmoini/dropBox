#!/usr/bin/python
#Python XML RPC Server - Designed for Salt Smarts
#Description: Bridges between PHP and AlgLib Python base
from array import *
#from Numeric import *
from SimpleXMLRPCServer import SimpleXMLRPCServer
from SimpleXMLRPCServer import SimpleXMLRPCRequestHandler
import os
import sys
import atexit
import xalglib


try:
	#Define atexit function
	def all_done():
		pid = str('saltServer.pid')
		os.remove(pid)

	# Restrict to a particular path.
	# EDIT: Change rpc_paths to meet whatever path you'd like to send requests to
	class RequestHandler(SimpleXMLRPCRequestHandler):
	    rpc_paths = ('/SSRPC2',)

	# Create server
	# EDIT: Replace IP_HERE and PORT_HERE with the IP/Port you'll be serving from
	server = SimpleXMLRPCServer(("IP_HERE", PORT_HERE),requestHandler=RequestHandler)
	server.register_introspection_functions()

	# Register a function under a different name
	def compute_function(lAS,rAS):
		#info, rep, x = xalglib.rmatrixsolvels(a, nrows, ncols, b, threshold)
		#INPUT PARAMETERS
		#    A       -   array[0..NRows-1,0..NCols-1], system matrix
		#    NRows   -   vertical size of A
		#    NCols   -   horizontal size of A
		#    B       -   array[0..NCols-1], right part
		#    Threshold-  a number in [0,1]. Singular values  beyond  Threshold  are
		#                considered  zero.  Set  it to 0.0, if you don't understand
		#                what it means, so the solver will choose good value on its
		#                own.
		#                
		#OUTPUT PARAMETERS
		#    Info    -   return code:
		#                * -4    SVD subroutine failed
		#                * -1    if NRows<=0 or NCols<=0 or Threshold<0 was passed
		#                *  1    if task is solved
		#    Rep     -   solver report, see below for more info
		#    X       -   array[0..N-1,0..M-1], it contains:
		#                * solution of A*X=B if A is non-singular (well-conditioned
		#                  or ill-conditioned, but not very close to singular)
		#                * zeros,  if  A  is  singular  or  VERY  close to singular
		#                  (in this case Info=-3).
		# solve equations using the MatrixSoleLS function
		#IMPORTANT LINE FOUND
		#    RMatrixSolveLS(problem_matrix_left, varcount, arraysize, problem_matrix_right,
		#      0.0, answer,
		#      report, solutions);
		#problem_matrix_left = formulations
		#varcount = 16
		#arraysize = 16
		#problem_matrix_right = intended concentrations
		#Misc Vars...
		threshold = 0.0
		nrows = 16
		ncols = 16
		info, rep, rx = xalglib.rmatrixsolvels(lAS, nrows, ncols, rAS, threshold)
		return [info, rep, rx]


	server.register_function(compute_function, 'computeFertilizer')
	atexit.register(all_done)

	# Setup PID file
	def writePidFile():
	    pid = str(os.getpid())
	    f = open('saltServer.pid', 'w')
	    f.write(pid)
	    f.close()

	writePidFile()

	# Run the server's main loop
	server.serve_forever()

except KeyboardInterrupt:
    sys.exit(0)
