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
  $str="PreSharedKey=" . 'YCsWNgxbVMFtLeT1468FLbtmVlredxJFa0qWIdNPq';
  $str=$str . '&MerchantID=' . $_POST["MerchantID"];
  $str=$str . '&Password=' . '6LLHB7W4F2';
  $str=$str . '&StatusCode=' . $_POST["StatusCode"];
  $str=$str . '&Message=' . $_POST["Message"];
  $str=$str . '&PreviousStatusCode=' . $_POST["PreviousStatusCode"];
  $str=$str . '&PreviousMessage=' . $_POST["PreviousMessage"];
  $str=$str . '&CrossReference=' . $_POST["CrossReference"];
  $str=$str . '&Amount=' . $_POST["Amount"];
  $str=$str . '&CurrencyCode=' . $_POST["CurrencyCode"];
  $str=$str . '&OrderID=' . $_POST["OrderID"];
  $str=$str . '&TransactionType=' . $_POST["TransactionType"];
  $str=$str . '&TransactionDateTime=' . $_POST["TransactionDateTime"];
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
  return sha1($str);
}  
?>

<?php 
function checkhash()
{
   $str1 = $_POST["HashDigest"];
   $hashcode = createhash();
   if ($hashcode == $str1) 
   { 
       echo "PASSED"; 
   } 
   else 
   { 
       echo "FAILED"; 
   } 
}  
?>

<p>
				HASH - <?php echo $_POST["HashDigest"] ?> <br /> <br />
				HASHCODE - <?php echo createhash(); ?> <br /><br />
				HASHCHECK - <?php checkhash(); ?> <br />
				StatusCode - <?php echo $_POST["StatusCode"]; ?> <br />
				Response Message - <?php echo $_POST["Message"]; ?> <br />
    				Previous Status Code - <?php echo $_POST["PreviousStatusCode"]; ?> <br />
	  			Previous Message - <?php echo $_POST["PreviousMessage"]; ?> <br />        
				CrossReference - <?php echo $_POST["CrossReference"]; ?> <br />
				MerchantID - <?php echo $_POST["MerchantID"]; ?> <br />
				Amount - <?php echo $_POST["Amount"]; ?> <br />                                                                        
				CurrencyCode - <?php echo $_POST["CurrencyCode"]; ?> <br />
				OrderID - <?php echo $_POST["OrderID"]; ?> <br />
				TransactionType - <?php echo $_POST["TransactionType"]; ?> <br />
				TransactionDateTime - <?php echo $_POST["TransactionDateTime"]; ?> <br />
				CallbackURL - <?php echo $_POST["CallbackURL"]; ?> <br />
				OrderDescription - <?php echo $_POST["OrderDescription"]; ?> <br />
				CustomerName - <?php echo $_POST["CustomerName"]; ?> <br />
				Address1 - <?php echo $_POST["Address1"]; ?> <br />
				Address2 - <?php echo $_POST["Address2"]; ?> <br />
				Address3 - <?php echo $_POST["Address3"]; ?> <br />
				Address4 - <?php echo $_POST["Address4"]; ?> <br />
				City - <?php echo $_POST["City"]; ?> <br /> 
				State - <?php echo $_POST["State"]; ?> <br />
				PostCode - <?php echo $_POST["PostCode"]; ?> <br /> 
				CountryCode - <?php echo $_POST["CountryCode"]; ?> <br />
</p>

</body>

</html>
