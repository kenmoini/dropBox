dropBox
=======

A collection of random but useful scripts not large, complex, or otherwise worthy of their own repo.  Most will also have their own branch so the whole repository is not needed to be synced.

######Sort by Primary Language
[PHP](#php)
[Python](#python)

###PHP
[DreamZilla](#dreamzilla)

###Python
[SaltServer](#saltServer)

DreamZilla
========
DreamZilla is a simple PHP application that uses an API Key that the Dreamhost user provides and it requests the list of FTP/SFTP/SSH users that Dreamhost user has in their account and generates an XML file to download and import into FileZilla's Site Manager for easy connection to your various users/sites.

A live version is able to be seen here: http://www.kenmoini.com/dropBox/dreamzilla/

SaltServer
========
SaltServer is a simple set of scripts that will bridge data passed from applications to the AlgLib computation engine.  This is how [SaltSmarts](www.saltsmarts.com) computes fertilizer profiles.  It spins up a simple XML-RPC server, formats the data slightly, passes it to the AlgLib library, and returns the processed data.
To install, you'll need to install the AlgLib CPython library first.  With that being complete, edit the lines 23 and 27 in saltServer.py to match where you'll be serving from, and lines 4 and 12 in serverCheck.sh if you'd like to use the script to keep the SaltServer running via CRON.
Server expexts two arrays of the same size, and will return either a 3 item array when successfully processed, or a 2 item array if computation has failed.
