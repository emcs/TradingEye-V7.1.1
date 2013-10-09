

/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

	1) Unzip both your Tradingeye and license files

	2) Create a mySQL database

	3) Rename the htaccess.txt file to .htaccess

	4) Rename your tradingeye.txt license file to tradingeye
	
	5) Upload all your files to the root of your site but your license files must be uploaded in binary mode
	
	6) CHMOD the following folders to have 777 permissions:
		/config/
		/images/
		/graphics/
		/UserFiles/
		/rss/
	
	7) Browse to http://www.yoursite.com/installs/ and run through the installation process

		Items you will need to know:

		a. Database Server e.g. localhost
		b. Database Name e.g. tradingeye
		c. Database Username
		d. Database Password
		e. Database Prefix e.g. te6_
		f. SSL URL e.g. https://www.yoursite.com/
		g. Tradingeye Admin Email Address e.g. info@yoursite.com
		h. Tradingeye Admin Username
		i. Tradingeye Admin Password
	
	8) Once complete, follow the links to the admin and login using the Admin Username and Password created during the install
	
	9) Once this has been done please remove the /installs/ directory from your web server!



	Further reading:

	http://forum.tradingeye.com/showthread.php?t=1931