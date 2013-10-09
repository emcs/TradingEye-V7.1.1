Copyright (c) 1998-2003 Verisign, Inc. All Rights Reserved


CONTACT INFORMATION
-------------------

{!+
Verisign, Inc. 
http://www.verisign.com

See contact.txt for additional contact information.
-!}


{!+
In addition to this readme file, full product documentation is
available. Download the "Payflow Pro Developer's Guide" from the
"Downloads" page of the VeriSign Manager website:
https://manager.verisign.com
-!}


PAYFLOW PRO
-----------
This directory tree contains the Payflow Pro client program and examples.


CONTENTS
--------
pfpro:              Executable client
pfpro_file:         Executable client that accepts a file containing transaction parameters
test.sh:            Example script
test-xml.sh:        Example script for XML
transaction.xml:    Sample XML document
readme.txt:         This file


EXAMPLES
--------
* Run test.sh.
* You should receive a response similar to the following:
    response = RESULT=0&PNREF=VXYZ00912465&ERRCODE=00&AUTHCODE=09TEST&AVSADDR=Y&AVSZIP=Y& 


NOTE
----
* You must set the environment variable PFPRO_CERT_PATH to point to the directory that 
    contains the file f73e89fd.0 in the certs subdirectory.
