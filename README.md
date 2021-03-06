dropBox
=======

A collection of random but useful scripts not large, complex, or otherwise worthy of their own repo.

######Sort by Primary Language
[Perl](#perl) &bull; [PHP](#php) &bull; [Python](#python)

###Perl
[OpenVPN Client Packager](#openvpn-client-packager)

[Simple Automatic WordPress Backup Utility](#simple-automatic-wordpress-backup-utility)

###PHP
[DreamZilla](#dreamzilla)

###Python
[SaltServer](#saltServer)

OpenVPN Client Packager
========
I wrote a set of scripts to provide more administrative functions over VPN technology, especially with tieing into a web panel/application.  This one that I've released is part of that library of scripts, it takes queued database entries for clients that need a certificate generated, generates it, packages it together with a few other needed and helpful files, and updates the database to indicate the generated ZIP file being available.

You can find it under the OpenVPN-Packager directory.

Simple Automatic WordPress Backup Utility
========
This is a script I've deployed to many of my WordPress installations.  The utility backs up the WordPress database as well as the files into separate packages and directories.  It pulls database connection information live from the wp-config.php file and it even creates the backup directories if needed.

You run it by calling...

`./sawpb-util WORDPRESS_ROOT BACKUP_ROOT`

Where WORDPRESS_ROOT is the relative path to the base of the WordPress directory, and BACKUP_ROOT is the relative path to where you'd like to save that particular WordPress installation's backup files to.  I set up Cronjobs for my WordPress installations, and have a main backup directory with subdirectories for every domain I have WordPress loaded.  They're then SCP'd to an off-site backup server.

DreamZilla
========
DreamZilla is a simple PHP application that uses an API Key that the Dreamhost user provides and it requests the list of FTP/SFTP/SSH users that Dreamhost user has in their account and generates an XML file to download and import into FileZilla's Site Manager for easy connection to your various users/sites.

A live version is able to be seen here: http://www.kenmoini.com/dropBox/dreamzilla/

SaltServer
========
SaltServer is a simple set of scripts that will bridge data passed from applications to the AlgLib computation engine.  This is how [SaltSmarts](http://www.saltsmarts.com) computes fertilizer profiles.  It spins up a simple XML-RPC server, formats the data slightly, passes it to the AlgLib library, and returns the processed data.

To install, you'll need to install the AlgLib CPython library first.  With that being complete, edit the lines 23 and 27 in saltServer.py to match where you'll be serving from, and lines 4 and 12 in serverCheck.sh if you'd like to use the script to keep the SaltServer running via CRON.

Server expexts two arrays of the same size, and will return either a 3 item array when successfully processed, or a 2 item array if computation has failed.
