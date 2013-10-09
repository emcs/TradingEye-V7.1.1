#!/usr/bin/perl
##########################

use Config;
$: = $Config{path_sep};

# Set environment
$LIBS = "$:.$:..$:../lib";

$ENV{PATH} .=$LIBS;
$ENV{LD_LIBRARY_PATH} .=$LIBS;
$ENV{SHLIB_PATH} .=$LIBS;
$ENV{LIBPATH} .=$LIBS;

$ENV{CLASSPATH} .= "$:Verisign.jar$:.$:";
$ENV{PFPRO_CERT_PATH} = "../certs";

# Merchant Account values
#   => Change these to your account values
$user	    = "user";
$vendor	    = "vendor";
$partner    = "partner";
$password   = "password";
#   => Change the above to your account values

# Compile the code
print `javac PFProJava.java`; 

# Run the test
$PROXY = "";
print `java PFProJava test-payflow.verisign.com 443 "USER=$user&VENDOR=$vendor&PARTNER=$partner&PWD=$password&TRXTYPE=S&TENDER=C&ACCT=5105105105105100&EXPDATE=1209&AMT=14.42&COMMENT1[3]=123&COMMENT2=Good Customer&INVNUM=1234567890&STREET=5199 JOHNSON&ZIP=94588" 30 $PROXY`;
print "\n";
