#!/usr/bin/perl
use strict;
use warnings;
use diagnostics;
use POSIX qw/strftime/;
use FindBin qw($Bin);
#=Simple Auto WordPress BackUp Utility
#=By Ken Moini (ken@kenmoini.com) - www.kenmoini.com
#===Scans relative wp-config.php file and obtains information to backup database and files
#===Takes two arguments: "./sawpb-util WORDPRESS_ROOT BACKUP_ROOT" with the two root directories of course being relative to this path.  It will then store wordpress files in ./BACKUP_ROOT/wp-file-backup and the database backups in ./BACKUP_ROOT/wp-db-backup
#===Depends on: mysql-client, tar, gzip, local write permissions
#===Changelog:
#====v1.0 - (6/14/12) First version.  Has basic error messages, comments, and functions.

my $wordpressRootFolder = $ARGV[0] or die "Command: $0 WORDPRESS_ROOT BACKUP_ROOT\n You forgot to specify the Wordpress root directory!";
my $wordpressBackupFolder = $ARGV[1] or die "Command: $0 WORDPRESS_ROOT BACKUP_ROOT\n You forgot to specify a Backup root directory!";

#===Tests
if (-d $wordpressBackupFolder."/wp-file-backup") {
	#Dir exists, do nothing
}
elsif (-e $wordpressBackupFolder."/wp-file-backup") {
	#Exists as file
	die "Sorry, the WordPress Data Backup directory shares same name as file!";
}
else { 
	mkdir($wordpressBackupFolder."/wp-file-backup", 0775); #make the backup directories if they don't exist
}
if (-d $wordpressBackupFolder."/wp-db-backup") { 
	#Dir exists, do nothing
}
elsif (-e $wordpressBackupFolder."/wp-db-backup") {
	die "Sorry, the WordPress Database Backup directory shares same name as file!";
}
else { 
	mkdir($wordpressBackupFolder."/wp-db-backup", 0775);
}

#Sub-routines...
sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}
sub removeEnds($) {
	my $string = shift;
	$string =~ s/'\);//;
	$string =~ s/';//;
	return $string;
}

my $upperRoot = "\U$wordpressRootFolder"; #Convert the folder names to upper cases for labeling purposes

my ($dbName, $dbUser, $dbPass, $dbHost, $wpPrefix, $selectedTables, $lines, @lookoutLines); #Setup variables and arrays
$dbName = $dbUser = $dbPass = $dbHost = $wpPrefix = '';
@lookoutLines = ("DB_NAME","DB_USER","DB_PASSWORD","DB_HOST","table_prefix");

my $date = strftime('%m-%d-%Y_%s',localtime); #Store the date in a variable
chomp($date); #Remove the new line that gets added at the end

open(INPUT,$wordpressRootFolder."/wp-config.php") or die "Error opening Wordpress Configuration file at ".$wordpressRootFolder."/wp-config.php!";

#Gather data from wp-config.php and store
while(<INPUT>) {
	foreach $lines (@lookoutLines) {
		if (/$lines/) {
			if ($lines eq $lookoutLines[0]) {
				$dbName = s/define\('$lines(.*?)', '//;
				$dbName = trim(removeEnds($_));
			}
			if ($lines eq $lookoutLines[1]) {
				$dbUser = s/define\('$lines(.*?)', '//;
				$dbUser = trim(removeEnds($_));
			}
			if ($lines eq $lookoutLines[2]) {
				$dbPass = s/define\('$lines(.*?)', '//;
				$dbPass = trim(removeEnds($_));
			}
			if ($lines eq $lookoutLines[3]) {
				$dbHost = s/define\('$lines(.*?)', '//;
				$dbHost = trim(removeEnds($_));
			}
			if ($lines eq "table_prefix") {
				$wpPrefix = s/\$$lines(.*?)= '//;
				$wpPrefix = trim(removeEnds($_));
			}
		}
	}
}

#===Connect to mysql-db and store SQL backup
#`mysqldump --host $dbHost --user $dbUser --password=$dbPass $dbName | gzip > $wordpressBackupFolder/wp-db-backup/mysql-$date.gz`; OLD
`mysql -h $dbHost -u $dbUser --password=$dbPass $dbName --silent -e "SHOW TABLES LIKE '$wpPrefix%'" | grep -v Tables_in | xargs mysqldump $dbName --host $dbHost -u $dbUser --password=$dbPass > $wordpressBackupFolder/wp-db-backup/mysql-$date.sql`;

#Compress SQL file
`gzip $wordpressBackupFolder/wp-db-backup/mysql-$date.sql`;

#===Tar up all the Wordpress files
`tar -cvzphf "$wordpressBackupFolder/wp-file-backup/wpFiles-$date.tar.gz" $wordpressRootFolder --atime-preserve --label $upperRoot`;
