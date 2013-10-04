CardSave Hosted Payment Page Solution
PHP Example

1) Please ask your merchant for your his TEST Gateway Account and Merchant Management System login's.

If you do not have these details or you do not have a merchant yet then you can register a test account here...

https://mms.cardsaveonlinepayments.com/Pages/PublicPages/RegisterMerchant.aspx 

You will receive a verification email which you must respond to complete the registration process. You will then receive two separate emails with login details to the back office management system and payment gateway. When you login to the back office system, Merchant Management System for the first time, (you will be asked to change your password on first time entry). 


2) You will need the TEST Gateway email merchantID and password to plug into your code. Please make a note. 


3) You will also need the pre-shared key. Login to the Merchant Management System and select merchant information from the left menu. At the bottom of that page you will see the security information and pre-shared key.


4) Copy the code into your web space and make the following changes...

In PaymentFormHosted.php

Replace [ENTER YOUR MERCHANTID HERE] including the "[]" brackets with the TEST Gateway merchantID, (format letters-numbers)
Replace [ENTER YOUR CALLBACK URL HERE] including the "[]" brackets with the full qualifying path to the call back page.

If the code is not in the root folder of the website add the full qualifying path to the post action... 
<form name="contactFormA" id="contactFormA" method="post" action="PaymentFormHostedProcess.php" target="_self">


In PaymentFromHostedProcess.php

In BOTH the createhash function and createhashstring function...
Replace [ENTER YOUR PRESHARED KEY HERE] including the "[]" brackets with the preshared key you from the Merchant Management System.
Replace [ENTER YOUR MERCHANT PASSWORD HERE] including the "[]" brackets with the TEST gateway merchant password.


In PaymentFromHostedcallback.php

Replace [ENTER YOUR PRESHARED KEY HERE] including the "[]" brackets with the preshared key you from the Merchant Management System.
Replace [ENTER YOUR MERCHANT PASSWORD HERE] including the "[]" brackets with the TEST gateway merchant password.


The Test Pages are now good to go!
Just browse to the PaymentFormHosted.php page in your browser and test.

You can download some test cards to try here, they have varying responses...
https://mms.cardsaveonlinepayments.com/SiteFiles/VirtualFiles/TEST_CARD_DETAILS/TestCardDetails.zip