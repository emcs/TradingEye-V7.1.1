<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
#COMPANY SETTINGS
define("MSG_CNAME_EMPTY","<div class=\"popWarning\"><p>Please enter your company name</p></div>");
define("MSG_CADDRESS_EMPTY","<div class=\"popWarning\"><p>Please enter an address</p></div>");
define("MSG_CPHONE_EMPTY","<div class=\"popWarning\"><p>Please enter a phone number</p></div>");
define("MSG_CZIP_EMPTY","<div class=\"popWarning\"><p>Please enter a postcode/zip</p></div>");
define("MSG_CCITY_EMPTY","<div class=\"popWarning\"><p>Please enter a city</p></div>");
define("MSG_CDETAILS_UPDATED","<div class=\"popPositive\"><p>Your company details have been updated</p></div>");
define("MSG_ORDERDETAILS_UPDATED","<div class=\"popPositive\"><p>Your order settings have been updated</p></div>");
define("MSG_FEATURESETTINGS_UPDATED","<div class=\"popPositive\"><p>Your feature settings have been updated</p></div>");
define("MSG_DESIGNSETTINGS_UPDATED","<div class=\"popPositive\"><p>Your design settings have been updated</p></div>");
define("MSG_METASETTINGS_UPDATED","<div class=\"popPositive\"><p>Your meta tags have been updated</p></div>");

#COUNTRY MESSAGES
define("MSG_COUNTRY_INSERTED","<div class=\"popPositive\"><p>A new country has been added</p></div>");
define("MSG_COUNTRY_UPDATED","<div class=\"popPositive\"><p>This country record has been updated</p></div>");
define("MSG_COUNTRYNAME_EXIST","<div class=\"popWarning\"><p>This country name already exists</p></div>");
define("MSG_COUNTRYNAME_EMPTY","<div class=\"popWarning\"><p>Please enter a country name</p></div>");
define("MSG_SHORTCOUNTRY_EMPTY","<div class=\"popWarning\"><p>Please enter a short name</p></div>");
define("MSG_COUNTRY_DELETED","<div class=\"popPositive\"><p>County/states for this country have been deleted</p></div>");
define("MSG_NOCOUNTRY_DELETED","<div class=\"popWarning\"><p>Please select a country to delete</p></div>");

#STATE MESSAGES
define("MSG_STATE_INSERTED","<div class=\"popPositive\"><p>A new county/state has been added</p></div>");
define("MSG_STATE_UPDATED","<div class=\"popPositive\"><p>This county/state record has been updated</p></div>");
define("MSG_STATENAME_EXIST","<div class=\"popWarning\"><p>State name already exists</p></div>");
define("MSG_STATENAME_EMPTY","<div class=\"popWarning\"><p>Please enter a state name</p></div>");
define("MSG_SHORTSTATE_EMPTY","<div class=\"popWarning\"><p>Please enter a short name</p></div>");
define("MSG_STATE_DELETED","<div class=\"popPositive\"><p>County/state has been deleted</p></div>");
define("MSG_NOSTATE_DELETED","<div class=\"popWarning\"><p>Please select a couty/state to delete</p></div>");

#FILE MANAGER
define("MSG_FILE_DELETED","<div class=\"popPositive\"><p>File has been deleted</p></div>");
define("MSG_NOFILE_DELETED","<div class=\"popWarning\"><p>No file deleted</p></div>");
define("MSG_FILE_UPLOADED","<div class=\"popPositive\"><p>File has been uploaded</p></div>");
define("MSG_NOFILE_UPLOADED","<div class=\"popWarning\"><p>No file uploaded</p></div>");
define("MSG_NODIRECTORY_EXIST","<div class=\"popWarning\"><p>Directory does not exist</p></div>");
define("MSG_NODIRECTORY_PERMIT","<div class=\"popWarning\"><p>The required permissions do not exist</p></div>");
define("MSG_NOFILE_EXIST","<div class=\"popWarning\"><p>No files exist in the selected directory</p></div>");

#SYSTEM SETTING
define("MSG_FILE_OPEN","<div class=\"popWarning\"><p>Unable to open the database configuration file</p></div>");
define("MSG_FILE_NOWRITE","<div class=\"popWarning\"><p>Unable to write to the database configuration file</p></div>");
define("MSG_FILE_NOWRITABLE","<div class=\"popWarning\"><p>The database configuration file is not writable</p></div>");
define("MSG_SYSTEMSETTING_UPDATED","<div class=\"popPositive\"><p>System settings have been updated</p></div>");
define("MSG_SITEURL_EMPTY","<div class=\"popWarning\"><p>Please enter the root URL</p></div>");
define("MSG_SITEPATH_EMPTY","<div class=\"popWarning\"><p>Please enter the directory path</p></div>");
define("MSG_SITETITLE_EMPTY","<div class=\"popWarning\"><p>Please enter the website title</p></div>");
define("MSG_SITENAME_EMPTY","<div class=\"popWarning\"><p>Please enter the site name</p></div>");
define("MSG_ADMINEMAIL_EMPTY","<div class=\"popWarning\"><p>Please enter the admin email address</p></div>");
define("MSG_CURRENCY_EMPTY","<div class=\"popWarning\"><p>Please enter the site currency</p></div>");
define("MSG_NOTDIR","<div class=\"popWarning\"><p>Please enter a valid directory path</p></div>");
define("MSG_SEOFRIENDLY","<div class=\"popWarning\"><p>Confirm the SEO friendly urls working by clicking the checkbox</p></div>");

define("MSG_DBSERVER_EMPTY","<div class=\"popWarning\"><p>Please enter a database server</p></div>");
define("MSG_USERNAME_EMPTY","<div class=\"popWarning\"><p>Please enter the database username</p></div>");
define("MSG_PASSWORD_EMPTY","<div class=\"popWarning\"><p>Please enter the database password</p></div>");
define("MSG_DBNAME_EMPTY","<div class=\"popWarning\"><p>Please enter a database name</p></div>");
define("MSG_SMTP_USERNAME_EMPTY","<div class=\"popWarning\"><p>Please enter an SMTP username</p></div>");
define("MSG_SMTP_PASSWORD_EMPTY","<div class=\"popWarning\"><p>Please enter an SMTP password</p></div>");
define("MSG_SMTP_HOST_EMPTY","<div class=\"popWarning\"><p>Please enter an SMTP host</p></div>");

define("MSG_APPLYAVSCV2_EMPTY","<h3>Please enter a valid Protx AVSCV2 flag.</h3><p>Use this flag to fine-tune the AVS/CV2 checks and ruleset you have defined, at a transaction level.</p><p>0 = If AVS/CV2 enabled then check them. If rules apply, use rules. (default)<br /><br />
1 = Force AVS/CV2 checks even if not enabled for the account. If rules apply, use rules.<br />2 = Force NO AVS/CV2 checks even if enabled on account.<br />3 = Force AVS/CV2 checks even if not enabled for the account but DON'T apply any rules.</p>");

define("MSG_3DSECURESTATUS_EMPTY","<h3>Please enter a valid Protx 3-D Secure flag.</h3><p>Use this flag to fine-tune the 3-D Secure checks and ruleset you've defined, at a transaction level.</p><p>0 = <strong>Default</strong> If 3-D Secure checks are possible and rules allow, perform the checks and apply the authorisation rules.<br />1 = Force 3-D Secure checks for this transaction only, (if your account is 3D-enabled) and apply rules for authorisation.<br />2 = Do not perform 3D-Secure checks for this transaction only, and always authorise.<br />3 = Force 3D-Secure checks for this transaction (if your account is 3-D Secure enabled) but ALWAYS obtain an auth code, irrespective of the current ruleset in use.</p>");

#CSV IMPORTER

define("MSG_CSV_NOTUPLOADED","<div class=\"popWarning\"><p>The CSV has not been uploaded, and no data has been imported</p></div>");
define("MSG_CSV_REQUIRED","<div class=\"popWarning\"><p>Some required fields are missing</p></div>");
define("MSG_CSV_MANDATORY","<div class=\"popWarning\"><p>You are missing one or more required fields</p></div>");
define("MSG_INVALID_CSV","<div class=\"popWarning\"><p>The file you have tried to upload is invalid</p></div>");


#PLUGIN APPLICATIONS
define("LBL_EDITPLUGIN_BTN","Update application");
define("LBL_ADDPLUGIN_BTN","Add application");
define("MSG_PLUGINNAME_EMPTY","<div class=\"popWarning\"><p>Please enter an application name</p></div>");
define("MSG_TEMPLATE_EMPTY","<div class=\"popWarning\"><p>Please enter a template</p></div>");
define("MSG_VERSION_EMPTY","<div class=\"popWarning\"><p>Please enter a version number for the plugin</p></div>");
define("MSG_PLUGIN_INSERTED","<div class=\"popPositive\"><p>Plugin has been inserted successfully</p></div>");
define("MSG_PLUGIN_UPDATED","<div class=\"popPositive\"><p>Plugin has been updated successfully</p></div>");
define("MSG_NO_PLUGIN","<div class=\"popWarning\"><p>No plugins available</p></div>");

#TEXTREA
define("TPL_VAR_UPDATESUCCESS","<div class=\"popWarning\"><p>Text has been updated successfully</p></div>");

#PAYMENT DETAILS
define("MSG_PAYMENTDETAILS_UPDATED","<div class=\"popPositive\"><p>Payment settings have been updated successfully</p></div>");
?>