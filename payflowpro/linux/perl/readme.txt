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



NAME
    PFProAPI - Perl support for Payflow Pro calls


SYNOPSIS
      Perl client with convenience subroutines for Perl and CGI use.

INSTALLATION
      The Perl client has been designed to run on both Unix and NT platforms.

      You must set the environment variable PFPRO_CERT_PATH to point to the directory 
      that contains the file f73e89fd.0 in the certs subdirectory.

      NOTE: Before beginning, edit the file PFProAPI.pm, and modify the 
      variable settings for $USER, $VENDOR, $PARTNER and $PWD near the top
      of the file to the correct values for your account.  If you do not
      do this, the test steps will result in User Authentication failures.
      For example, if your account values are Sandy, Acme, SuperPartner, and
      a1b2c3, then the resulting section would be as shown here:

            # Set the following variables to your account values:
            $USER         = "Sandy";	#{!+ You must register with Verisign to get -!}
            $VENDOR       = "Acme";	# your USER/VENDOR/PARTNER/PWD
            $PARTNER      = "SuperPartner";
            $PWD          = "a1b2c3";

      UNIX Platform: 

        You *must* have a working Perl installation installed to use this module.
        If you do not, please contact your ISP to have them set this module up for you.

        Assuming your perl is setup correctly and the Payflow Pro Client
        is installed, do:
                    perl Makefile.PL
                    make;
                    make test;
                    make install;

        If you receive the following message, it means that the Payflow Pro Client
        could not be copied from the ../lib directory to a new lib subdirectory:

                Warning: the following files are missing in your kit:
                    lib/libpfpro.a
                    lib/pfpro.h

        You may need root permissions in install the module.  If this is impossible,
        you may be able to install the module in your local account by using:

                    perl Makefile.PL PREFIX=YOUR_LOCAL_PERL_FILES

            (replace YOUR_LOCAL_PERL_FILES with a path to which you would like to
             place your local perl files. If you are unsure, try ~/perllib )

        For more information, try perldoc ExtUtils::MakeMaker.


      NT Platform:

        If you do not have perl installed, you can obtain it from:

            http://www.activestate.com

        Note: You must obtain the latest version of ActivePerl for IIS 4.0 
        test and not just Perl for Win32. 

        There is an issue with ActivePerl that allows scripts to be executed via the 
        command line, however they will not execute properly from Internet Information 
        Server. The resolution to this issue appears in the following Knowledge Base 
        article:

            http://support.microsoft.com/support/kb/articles/q186/8/01.asp?FR=0

        Source Installation:

        If you have nmake and a compiler installed on your system, follow the
        Source Installation instructions below.  If you do not, then perform the Binary
        Installation.

        Assuming your perl is setup correctly and the Payflow Pro Client
        is installed, do:
                    perl Makefile.PL
                    nmake;
                    nmake test;
                    nmake install;

        If you receive the following message, it means the Payflow Pro Client
        could not be copied from the ..\lib directory to a new lib subdirectory:

                Warning: the following files are missing in your kit:
                    lib/PFPro.lib
                    lib/pfpro.h


        Binary Installation:

        If you have nmake and a compiler installed on your system, follow
        the Source Installation instructions listed above.

        If you do not have nmake and a compiler installed on your system, then
        run the following script (performs a binary installation):

                    perl install.pl



DOCUMENTATION
      After a successful installation, you can run perldoc PFProAPI at any time to
      display the Perl documentation page for the installed Payflow Pro Perl
      Client module.

