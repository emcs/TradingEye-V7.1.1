<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
session_start();
  include_once('../configuration.php');
  $callback_url=SITE_SAFEURL."ecom/index.php";
	echo "<SCRIPT LANGUAGE=\"Javascript\"> function OnLoadEvent() {document.form.submit(); }</SCRIPT>";  
	echo "<html><head><title>Redirecting...</title></head>" .    "<body OnLoad=\"OnLoadEvent();\">" .    "<FORM name=\"form\" action=\"" . $callback_url . "\"method=\"POST\">"; 
	
function getToken($thisString) 
           {
          // List the possible tokens
            $Tokens = array(
            "Status",
            "StatusDetail",
            "VendorTxCode",
            "VPSTxId",
            "TxAuthNo",
            "Amount",
            "AVSCV2", 
            "AddressResult", 
            "PostCodeResult", 
            "CV2Result", 
            "GiftAid", 
            "3DSecureStatus", 
            "CAVV",
            "AddressStatus",
            "CardType",
            "Last4Digits",
            "PayerStatus","CardType");

          // Initialise arrays
          $output = array();
          $resultArray = array();
          
          // Get the next token in the sequence
          for ($i = count($Tokens)-1; $i >= 0 ; $i--){
            // Find the position in the string
            $start = strpos($thisString, $Tokens[$i]);
            // If it's present
            if ($start !== false){
              // Record position and token name
              $resultArray[$i]->start = $start;
              $resultArray[$i]->token = $Tokens[$i];
            }
          }
          
          // Sort in order of position
          sort($resultArray);
            // Go through the result array, getting the token values
          for ($i = 0; $i<count($resultArray); $i++){
            // Get the start point of the value
            $valueStart = $resultArray[$i]->start + strlen($resultArray[$i]->token) + 1;
            // Get the length of the value
            if ($i==(count($resultArray)-1)) {
              $output[$resultArray[$i]->token] = substr($thisString, $valueStart);
            } else {
              $valueLength = $resultArray[$i+1]->start - $resultArray[$i]->start - strlen($resultArray[$i]->token) - 2;
              $output[$resultArray[$i]->token] = substr($thisString, $valueStart, $valueLength);
            }      

          }
          // Return the ouput array
          return $output;
        }
    
    function simpleXor($InString, $Key) 
    {
          // Initialise key array
          $KeyList = array();
          // Initialise out variable
          $output = "";
            // Convert $Key into array of ASCII values
          for($i = 0; $i < strlen($Key); $i++){
            $KeyList[$i] = ord(substr($Key, $i, 1));
          }
          // Step through string a character at a time
          for($i = 0; $i < strlen($InString); $i++) {
            // Get ASCII code from string, get ASCII code from key (loop through with MOD), XOR the two, get the character from the result
            // % is MOD (modulus), ^ is XOR
            $output.= chr(ord(substr($InString, $i, 1)) ^ ($KeyList[$i % strlen($Key)]));
          }
          // Return the result
          return $output;
    }
    
    function base64Decode($scrambled) 
    {
      // Initialise output variable
      $output = "";
      // Fix plus to space conversion issue
      $scrambled = str_replace(" ","+",$scrambled);
      // Do encoding
      $output = base64_decode($scrambled);
      // Return the result
      return $output;
    }
    
    $strCrypt=$_REQUEST["crypt"];
   	if (strlen($strCrypt)==0) 
    {
		ob_end_flush();
		header('Location: '.SITE_URL);
		exit;
	}else{	
        $strDecoded=simpleXor(Base64Decode($strCrypt),SAGE_ENCRYPTEDPASSWORD);
        $values = getToken($strDecoded);
        $strVendorTxCode=$values["VendorTxCode"];
        $strStatus=$values["Status"];
              
        $validate = $_REQUEST["validate"];  
        if (strlen($validate)==0)
        {
            // failed return
            $failureflag=1;
            if ($strStatus=="NOTAUTHED")
                $strReason="Your payment was declined by the bank.  This could be due to insufficient funds, or incorrect card details.";
            else if ($strStatus=="ABORT")
                $strReason="You chose to Cancel your order on the payment pages.  If you wish to resubmit your order you can do so here. If you have questions or concerns about ordering online, please contact us at ".SITE_PHONE.".";
            else if ($strStatus=="REJECTED") 
                $strReason="Your order did not meet our minimum fraud screening requirements. If you have questions about our fraud screening rules, or wish to contact us to discuss this, please call ".SITE_PHONE.".";
            else if ($strStatus=="INVALID" or strStatus=="MALFORMED")
                $strReason="We could not process your order because we have been unable to register your transaction with our Payment Gateway. You can place the order over the telephone instead by calling ".SITE_PHONE.".";
            else if ($strStatus=="ERROR")
                $strReason="We could not process your order because our Payment Gateway service was experiencing difficulties. You can place the order over the telephone instead by calling ".SITE_PHONE.".";
            else
                $strReason="The transaction process failed. Please contact us on ".SITE_PHONE." with the date and time of your order and we will investigate.";
		} else {
			// success return
            $failureflag=0;
            $validate = simpleXor(Base64Decode($validate),SAGE_ENCRYPTEDPASSWORD);
            $string = explode("_",$validate);
			//die($validate);
            $orderid = $string[0];
            $session = $string[1];
			// success reported but session mismatch
			if ($session != session_id()) {
                $failureflag=1;
                $strReason="There was a problem with your order session on returning to our site preventing us from automatically supplying your invoice, but you may have been charged.  Please contact us on ".SITE_PHONE." with the date and time of your order and we will investigate.";
			}
        }
    }
    if ($failureflag==0)
    {
        echo "<input type=\"hidden\" name=\"mode\" value=\"" .$orderid. "\" />";
		$_SESSION['order_id'] = $orderid;
        echo "<input type=\"hidden\" name=\"action\" value=\"checkout.process\" />"; 
    } else {
        echo "<input type=\"hidden\" name=\"sagepaystatus\" value=\"".$strReason."\" />"; 
        echo "<input type=\"hidden\" name=\"sagepayerr\" value=\"1\" />"; 
        echo "<input type=\"hidden\" name=\"action\" value=\"checkout.review\" />"; 
	}
	echo "<NOSCRIPT><center><p>Please click the button below to go back to your site</p><input type=\"submit\" value=\"Go\"/></p></center></NOSCRIPT>".    "</form></body></html>"; 
	exit;
?>
