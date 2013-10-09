#!/usr/bin/perl
#print $#ARGV;
if($#ARGV != 3)
{
print("Please Specify three arguments"); 
exit(0);
}


$EXPATH=$ARGV[3]."bin";
$ENV{LD_LIBRARY_PATH}.=$ARGV[3]."lib:.:/usr/lib";
$ENV{PFPRO_CERT_PATH}.=$ARGV[3]."certs";
#print $ARGV[0].$ARGV[1].$ARGV[2]  #2>errfile.log;
$val3=$ARGV[2];
#exit(0);
$com="$EXPATH/pfpro ".$ARGV[0]." ".$ARGV[1]." "."\"$val3\" 30";
open(RES,"$com 2>&1 |");

$stat="";
while($_ = <RES>)
{
$stat.=$_;
}

print $stat;


