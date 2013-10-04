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
function createhash()
{ 
  $str="PreSharedKey=" . 'YCsWNgxbVMFtLeT1468FLbtmVlredxJFa0qWIdNPq/FHTdXN2OV8Hr70iQU=';
  $str=$str . '&MerchantID=' . $_POST["MerchantID"];
  $str=$str . '&Password=' . '6LLHB7W4F2';
  $str=$str . '&Amount=' . $_POST["Amount"];
  $str=$str . '&CurrencyCode=' . $_POST["CurrencyCode"];
  $str=$str . '&OrderID=' . $_POST["OrderID"];
  $str=$str . '&TransactionType=' . $_POST["TransactionType"];
  $str=$str . '&TransactionDateTime=' . $_POST["TransactionDateTime"];
  $str=$str . '&CallbackURL=' . $_POST["CallbackURL"];
  $str=$str . '&OrderDescription=' . $_POST["OrderDescription"];
  $str=$str . '&CustomerName=' . $_POST["CustomerName"];
  $str=$str . '&Address1=' . $_POST["Address1"];
  $str=$str . '&Address2=' . $_POST["Address2"];
  $str=$str . '&Address3=' . $_POST["Address3"];
  $str=$str . '&Address4=' . $_POST["Address4"];
  $str=$str . '&City=' . $_POST["City"];
  $str=$str . '&State=' . $_POST["State"];
  $str=$str . '&PostCode=' . $_POST["PostCode"];
  $str=$str . '&CountryCode=' . $_POST["CountryCode"];
  $str=$str . "&CV2Mandatory=" . $_POST["CV2Mandatory"];
  $str=$str . "&Address1Mandatory=" . $_POST["Address1Mandatory"];
  $str=$str . "&CityMandatory=" . $_POST["CityMandatory"];
  $str=$str . "&PostCodeMandatory=" . $_POST["PostCodeMandatory"];
  $str=$str . "&StateMandatory=" . $_POST["StateMandatory"];
  $str=$str . "&CountryMandatory=" . $_POST["CountryMandatory"];
  $str=$str . "&ResultDeliveryMethod=" . 'POST';
  $str=$str . "&ServerResultURL=" . '';
  $str=$str . "&PaymentFormDisplaysResult=" . 'false';
  echo (sha1($str));
}     
     
?>

<?php 
function createhashstring()
{ 
  $str="PreSharedKey=" . '[ENTER YOUR PRESHARED KEY HERE]';
  $str=$str . '&MerchantID=' . $_POST["MerchantID"];
  $str=$str . '&Password=' . '[ENTER YOUR MERCHANT PASSWORD HERE]';
  $str=$str . '&Amount=' . $_POST["Amount"];
  $str=$str . '&CurrencyCode=' . $_POST["CurrencyCode"];
  $str=$str . '&OrderID=' . $_POST["OrderID"];
  $str=$str . '&TransactionType=' . $_POST["TransactionType"];
  $str=$str . '&TransactionDateTime=' . $_POST["TransactionDateTime"];
  $str=$str . '&CallbackURL=' . $_POST["CallbackURL"];
  $str=$str . '&OrderDescription=' . $_POST["OrderDescription"];
  $str=$str . '&CustomerName=' . $_POST["CustomerName"];
  $str=$str . '&Address1=' . $_POST["Address1"];
  $str=$str . '&Address2=' . $_POST["Address2"];
  $str=$str . '&Address3=' . $_POST["Address3"];
  $str=$str . '&Address4=' . $_POST["Address4"];
  $str=$str . '&City=' . $_POST["City"];
  $str=$str . '&State=' . $_POST["State"];
  $str=$str . '&PostCode=' . $_POST["PostCode"];
  $str=$str . '&CountryCode=' . $_POST["CountryCode"];
  $str=$str . "&CV2Mandatory=" . $_POST["CV2Mandatory"];
  $str=$str . "&Address1Mandatory=" . $_POST["Address1Mandatory"];
  $str=$str . "&CityMandatory=" . $_POST["CityMandatory"];
  $str=$str . "&PostCodeMandatory=" . $_POST["PostCodeMandatory"];
  $str=$str . "&StateMandatory=" . $_POST["StateMandatory"];
  $str=$str . "&CountryMandatory=" . $_POST["CountryMandatory"];
  $str=$str . "&ResultDeliveryMethod=" . 'POST';
  $str=$str . "&ServerResultURL=" . '';
  $str=$str . "&PaymentFormDisplaysResult=" . 'false';
   echo $str;
}     
?>

<p>
				HashDigest - <?php createhash(); ?><br />
				MerchantID - <?php echo $_POST["MerchantID"]; ?><br />
				Hash Digest - <?php createhashstring(); ?><br />
</p>

<form name="contactForm" id="contactForm" method="post" action="https://mms.cardsaveonlinepayments.com/Pages/PublicPages/PaymentForm.aspx" target="_self">
		
				<input type="hidden" name="HashDigest" value="<?php createhash(); ?>" />
				<input type="hidden" name="MerchantID" value="<?php echo $_POST["MerchantID"]; ?>" />
				<input type="hidden" name="Amount" value="<?php echo $_POST["Amount"]; ?>" />                                       
				<input type="hidden" name="CurrencyCode" value="<?php echo $_POST["CurrencyCode"]; ?>" />
				<input type="hidden" name="OrderID" value="<?php echo $_POST["OrderID"]; ?>" />
				<input type="hidden" name="TransactionType" value="<?php echo $_POST["TransactionType"]; ?>" />
				<input type="hidden" name="TransactionDateTime" value="<?php echo $_POST["TransactionDateTime"]; ?>" />
				<input type="hidden" name="CallbackURL" value="<?php echo $_POST["CallbackURL"]; ?>" />
				<input type="hidden" name="OrderDescription" value="<?php echo $_POST["OrderDescription"]; ?>" />
				<input type="hidden" name="CustomerName" value="<?php echo $_POST["CustomerName"]; ?>" />
				<input type="hidden" name="Address1" value="<?php echo $_POST["Address1"]; ?>" />
				<input type="hidden" name="Address2" value="<?php echo $_POST["Address2"]; ?>" />
				<input type="hidden" name="Address3" value="<?php echo $_POST["Address3"]; ?>" />
				<input type="hidden" name="Address4" value="<?php echo $_POST["Address4"]; ?>" />
				<input type="hidden" name="City" value="<?php echo $_POST["City"]; ?>" /> 
				<input type="hidden" name="State" value="<?php echo $_POST["State"]; ?>" />
				<input type="hidden" name="PostCode" value="<?php echo $_POST["PostCode"]; ?>" />
				<input type="hidden" name="CountryCode" value="<?php echo $_POST["CountryCode"]; ?>" />
				<input type="hidden" name="CV2Mandatory" value="<?php echo $_POST["CV2Mandatory"]; ?>" />
				<input type="hidden" name="Address1Mandatory" value="<?php echo $_POST["Address1Mandatory"]; ?>" />
				<input type="hidden" name="CityMandatory" value="<?php echo $_POST["CityMandatory"]; ?>" />
				<input type="hidden" name="PostCodeMandatory" value="<?php echo $_POST["PostCodeMandatory"]; ?>" />
				<input type="hidden" name="StateMandatory" value="<?php echo $_POST["StateMandatory"]; ?>" />
				<input type="hidden" name="CountryMandatory" value="<?php echo $_POST["CountryMandatory"]; ?>" />
				<input type="hidden" name="ResultDeliveryMethod" value="POST" />
				<input type="hidden" name="ServerResultURL" value="" />
				<input type="hidden" name="PaymentFormDisplaysResult" value="false" />
  			<br /><input type="submit" value="TEST NOW" />
				
			</form>
</body>

</html>
