#!/bin/sh
echo  "----------------------------------------------------"
echo  " ***> Performing 'pfpro' binary test transaction....."

USER=user
VENDOR=vendor
PARTNER=partner
PASSWORD=password

echo
echo "You will need to change the USER, VENDOR, PARTNER and PASSWORD to your"
echo "User, Vendor, Partner and Password as specified when you signed up with"
echo "the Payflow Pro service."
echo
echo "To change these values, edit the variables USER, VENDOR, PARTNER and"
echo "PASSWORD in this file, test.sh."
echo

libpath=.:../lib
LD_LIBRARY_PATH=$libpath:${LD_LIBRARY_PATH:-};export LD_LIBRARY_PATH

PFPRO_CERT_PATH=../certs;export PFPRO_CERT_PATH
./pfpro test-payflow.verisign.com 443 "USER=$USER&VENDOR=$VENDOR&PARTNER=$PARTNER&PWD=$PASSWORD&TRXTYPE=S&TENDER=C&ACCT=5105105105105100&EXPDATE=1209&AMT=27.23" 10 
echo
echo  "Done with 'pfpro' binary test transaction..."
echo  "---------------------------------------------------"
