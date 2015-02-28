#!/usr/bin/perl -w

#====Modules Used
use strict;
use warnings;
use diagnostics;
use DBI;
use DBD::mysql;
use File::Temp qw(tempdir);
use Digest::MD5  qw(md5_hex);

#====Declare sub-routines
sub trim($);
sub gen($);

#====Declare variables

#Location of OpenVPN compiled easy-rsa package
my $easyRSADir="/opt\/openvpn-2.2.2/easy-rsa/2.0";
#Location of the directory to use to store files post-generation
my $tmpDirBase="/opt/userCertPackages/keyGenerator/";
#Location of template directory, will copy contents of this directory into each generated ZIP file
my $templateDir="/opt/userCertPackages/keyGenerator/templateDir";

#====Database Connection
my $dbHost = "localhost";
my $dbUser = "root";
my $dbPass = "";
my $dbDB = "";
my $dbh = DBI->connect("dbi:mysql:database=$dbDB;host=$dbHost",$dbUser,$dbPass,{AutoCommit=>1,RaiseError=>1,PrintError=>0});

#====Run Checks...
if ($< == 0) {
	print "Running as root...yes\n";
}
else {
	print "Running as root...no...FAILED!\n";
	exit;
}
#See if this is a batch operation with the database or just a single generation
if ($#ARGV == -1) {
	#Setup Query...
	my $dbQuery = "SELECT * FROM ssSC_user_certs WHERE certPackageGenerated=? LIMIT 0, 10";
	my $dbQueryH = $dbh->prepare($dbQuery);
	my @dbResults;
	$dbQueryH->execute("0") 
	or die "SQL ERROR: $DBI::errstr\n";
	my $dbRows = $dbQueryH->rows();
	my $cn;
	my $cnCheck;
	for (my $i = 0; $i < $dbRows; $i++) {
		@dbResults = $dbQueryH->fetchrow_array();
		$cn = substr md5_hex($dbResults[1]), 0, 16;
		print $cn."\n";
		$cnCheck = $easyRSADir;
		$cnCheck .= "/keys/";
		$cnCheck .= $cn;
		$cnCheck .= ".key";
		if (-e $cnCheck) {
			#print "Error! Client already exists!\n";
			#exit;
		}
		else {
			gen($cn);
			my $hashUpdateS = $dbh->prepare("UPDATE ssSC_user_certs SET certPackageGenerated='1', certPackageFileName=? WHERE id=? LIMIT 1");
			$hashUpdateS->execute($cn,$dbResults[0]);
		}
	}
}
#Single generation
if ($#ARGV == 0) {
	my $clientCN=trim($ARGV[0]);
	my $clientKeyCheck = $easyRSADir;
	$clientKeyCheck .= "/keys/";
	$clientKeyCheck .= $clientCN;
	$clientKeyCheck .= ".key";
	if (-e $clientKeyCheck) {
		print "Error! Client already exists!\n";
		exit;
	}
	else {
		gen($clientCN);
	}
}

#====Destroy Database Connection
$dbh->disconnect();

#====Sub-routines...
# Perl trim function to remove whitespace from the start and end of the string
sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}
sub gen($) {
	my $cn = shift;
	my $newTmpDir = tempdir(DIR => $tmpDirBase, CLEANUP => 0);
	chdir($easyRSADir) or die "Cannot chdir to $easyRSADir";
	system(". ./vars && ./pkitoolnew $cn && cp $easyRSADir/keys\/$cn.crt $newTmpDir/client.crt && cp $easyRSADir/keys\/$cn.key $newTmpDir/client.key && mkdir $newTmpDir/MAC && mkdir $newTmpDir/NIX && mkdir $newTmpDir/WIN && cp -aR $templateDir/* $newTmpDir && cp $easyRSADir/keys\/$cn.crt $newTmpDir/WIN/OpenVPNPortable/data/config/client.crt  && cp $easyRSADir/keys\/$cn.key $newTmpDir/WIN/OpenVPNPortable/data/config/client.key");
	chdir($newTmpDir);
	system("zip -lr /opt/userCertPackages/keyGenerator/$cn-openvpn.zip *");	
	chdir($easyRSADir);
	system("rm -rf $newTmpDir");
}
