#!/bin/sh

PFPRO_FILE=transaction.xml

echo  "--------------------------------------------------------------"
echo  " ***> Performing 'pfpro' binary test transaction using '$PFPRO_FILE' file ....." 
echo

echo "You will need to change the USER, VENDOR, PARTNER and PASSWORD to your"
echo "User, Vendor, Partner and Password as specified when you signed up with" 
echo "the Payflow Pro service."
echo
echo "To change these values, edit the elements '<User>', '<Vendor>', '<Partner>'
echo "and '<Password>' in the file '$PFPRO_FILE'."
echo

libpath=.:../lib
LD_LIBRARY_PATH=$libpath:${LD_LIBRARY_PATH:-};export LD_LIBRARY_PATH

PFPRO_CERT_PATH=../certs;export PFPRO_CERT_PATH

./pfpro-file test-payflow.verisign.com 443 transaction.xml 10 
echo
echo
echo  "Done with 'pfpro' binary test transaction..."
echo  "---------------------------------------------------"
