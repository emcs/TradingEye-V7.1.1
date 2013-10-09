<!--
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
 -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Untitled 1</title>
</head>

<body>

<?php 
function gatewaydatetime()
{
  $str=date('Y-m-d H:i:s P');
  return $str;
}                 
?>

<form name="contactFormA" id="contactFormA" method="post" action="PaymentFormHostedProcess.php" target="_self">
		
				MerchantID - <input name="MerchantID" value="MyComp-1310044" /><br />
				Amount - <input name="Amount" value="750" /><br />
				CurrencyCode - <input name="CurrencyCode" value="826" /><br />
				OrderID - <input name="OrderID" value="123456" /><br />
				TransactionType - <input name="TransactionType" value="SALE" /><br />
				TransactionDateTime - <input name="TransactionDateTime" value="<? echo gatewaydatetime(); ?>" /><br />
				CallbackURL - <input name="CallbackURL" value="paymentformhostedcallback.php" /><br />
				OrderDescription - <input name="OrderDescription" value="Conference Booking" /><br />
				CustomerName - <input name="CustomerName" value="John Watson" /><br />
				Address1 - <input name="Address1" value="32 Edward Street" /><br />
				Address2 - <input name="Address2" value="" /><br />
				Address3 - <input name="Address3" value="" /><br />
				Address4 - <input name="Address4" value="" /><br />
				City - <input name="City" value="Camborne" /><br /> 
				State - <input name="State" value="Cornwall" /><br />
				PostCode - <input name="PostCode" value="TR14 8PA" /><br /> 
				CountryCode - <input name="CountryCode" value="826" /><br />
				<br />
				CV2Mandatory - <input name="CV2Mandatory" value="true" /><br />
				Address1Mandatory - <input name="Address1Mandatory" value="true" /><br />
				CityMandatory - <input name="CityMandatory" value="true" /><br />
				PostCodeMandatory - <input name="PostCodeMandatory" value="true" /><br />
				StateMandatory - <input name="StateMandatory" value="true" /><br />
				CountryMandatory - <input name="CountryMandatory" value="true" /><br />
				<br /><input type="submit" value="TEST NOW" />
				
			</form>
</body>

</html>
