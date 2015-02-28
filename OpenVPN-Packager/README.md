OpenVPN Certificate Packager
======

This is a simple application for assembling packaged files for users to connect to an OpenVPN server.  The script will either operate on a single generation via command prompt, or can batch through a database.  It will then create a certificate and paired keys for the clients and package them up with whatever other files you may choose.

The application of this script comes from having a web-based signup page, and needing an automated way to generated packaged OpenVPN SSL client certificates with some helpful files such as the actual client software for major systems.
  1.  User signs up on website, database entry is inserted to produce files, users is added to queue.
  2.  Cron job runs every 5 minutes, selects unprocessed users from same database, generates files, packages, updates database with package filename.
  3.  Another script is run to alert the user that the package has been generated.  Can download via alert email or via control panel.
  
The same script can also generate packaged files on-demand via command prompt.

###How the script works...
It's generally pretty simple really...

1. Sets up variables, database connection, detects if this is a call to generate certificate packages via batch database entries or if a unique client name has been passed directly to it as a command arguement.
2. If batch via database, creates MD5 hash of userID and sets as common name for certificate, generates certificate, packages, and updates database row to show the package having been created.
3. With single or batch operation, the specific generation flow is such...
  1. Create temporary working directory
  2. Enter easy-rsa directory, and generate certificate/keys, then copy them into temporary working directory
  3. Create {WIN,MAC,NIX} subfolders in temporary working directory, copy over files from skeleton/template directory
  4. ZIP files up into package
  5. Remove temporary files once package has been generated

###Installation
1. This assumes you'll be installing to /opt/userCertPackages/keyGenerator/ which can be changed by changing a few lines in the packager.pl file
2. Download and compile the latest OpenVPN server, assumes you'll be compiling to /opt/openvpn-*
3. Ensure that your vars file in the /opt/openvpn-*/easy-rsa/2.0/ has all the variables setup to prevent any further interative prompting while generating certificates.
4. Create base directory (assuming /opt/userCertPackages/keyGenerator/), download files from repo, configure to your specific instance.
5. Once packager.pl has been configured and the database table created, create skeleton directory to include basic files such as client configuration file, a README, ca/ta.key files, and who knows maybe even some client software to make it all super easy for your end-user.  Theses files are standard across all packaged files and will be copied into each one.
6. Populate database, setup packager.pl to run as cronjob, and watch the packaged certificates print out!

###Batch Generation
1. First off, if you'd like to run it via batch, you can setup a CRONTAB script that runs the main packager.pl script every so often as root.  Root is needed to access the special areas of the filesystem and the server key files.
2. Then, produce the corresponding table needed with the included certTable.sql file.  In the scripts the table is called ssSC_user_certs but you may call it whatever you'd like as long as you update the packager.pl file to point to that table.
  * The table is setup with an autoincrementing ID, a column for corresponding userID if you're linking it to your userbase, a column called 'certPackageGenerated' that defaults to 0 indicating the package file has yet to be generated and is switched to 1 when it is, and a 'certPackageFileName' column that contains the randomly generated string for the named package.
3. Once the table is created, edit the corresponding lines in the packager.pl file to allow it to connect to your database.
  * If you altered the table name you'll need to edit packager.pl on lines 43/65
4. Populate database, and watch it roll!

###Single Client Generation
1. Run ./packager.pl clientName
2. ?????
3. PROFIT!
