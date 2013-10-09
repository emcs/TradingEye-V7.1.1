<?php
session_start();

include"Config.php";
require_once ("ThePaymentGateway/PaymentSystem.php");

$rgeplRequestGatewayEntryPointList = new RequestGatewayEntryPointList(); 
$rgeplRequestGatewayEntryPointList->add("https://gw1.".$PaymentProcessorFullDomain, 100, 2);
$rgeplRequestGatewayEntryPointList->add("https://gw2.".$PaymentProcessorFullDomain, 200, 2);
$rgeplRequestGatewayEntryPointList->add("https://gw3.".$PaymentProcessorFullDomain, 300, 2);

$mdMerchantDetails = new MerchantDetails($MerchantID, $Password);

$tdsidThreeDSecureInputData = new ThreeDSecureInputData($_POST['CrossReference'],$_POST['PaRES']);

$tdsaThreeDSecureAuthentication = new ThreeDSecureAuthentication($rgeplRequestGatewayEntryPointList, 1, null, $mdMerchantDetails, $tdsidThreeDSecureInputData, "");
$boTransactionProcessed = $tdsaThreeDSecureAuthentication->processTransaction($goGatewayOutput, $tomTransactionOutputMessage);

if ($boTransactionProcessed == false)
{
	// could not communicate with the payment gateway
	$NextFormMode = "RESULTS";
	$Message = "Couldn't communicate with payment gateway";
	$TransactionSuccessful = false;
}
else
{
	switch ($goGatewayOutput->getStatusCode())
	{
	case 0:
		// status code of 0 - means transaction successful
		$_SESSION['vAuthCode']=str_replace("AuthCode: ","",$goGatewayOutput->getMessage());
		$NextFormMode = "RESULTS";
		$Message = $goGatewayOutput->getMessage();
		$TransactionSuccessful = true;
		$loc=$SiteSecureBaseURL."ecom/index.php?action=checkout.process&mode=".$_SESSION['order_id'];
		header("location:$loc");
		
	die;
		break;
		
					case 4:
				// status code of 4 - means transaction referred
  
		$NextFormMode = "RESULTS";
		$Message = $goGatewayOutput->getMessage();
		$TransactionSuccessful = false;
				break;
				
				
	case 5:
		// status code of 5 - means transaction declined
		$NextFormMode = "RESULTS";
		$Message = $goGatewayOutput->getMessage();
		$TransactionSuccessful = false;
		break;
	case 20:
		// status code of 20 - means duplicate transaction 
		$NextFormMode = "RESULTS";
		$Message = $goGatewayOutput->getMessage();
		if ($goGatewayOutput->getPreviousTransactionResult()->getStatusCode()->getValue() == 0)
		{
			$TransactionSuccessful = true;
		}
		else
		{
			$TransactionSuccessful = false;
		}
		$PreviousTransactionMessage = $goGatewayOutput->getPreviousTransactionResult()->getMessage();
		$DuplicateTransaction = true;
		break;
	case 30:
		// status code of 30 - means an error occurred 
		$NextFormMode = "RESULTS";
		$Message = $goGatewayOutput->getMessage();
		if ($goGatewayOutput->getErrorMessages()->getCount() > 0)
		{
			$Message = $Message."<br /><ul>";

			for ($LoopIndex = 0; $LoopIndex < $goGatewayOutput->getErrorMessages()->getCount(); $LoopIndex++)
			{
				$Message = $Message."<li>".$goGatewayOutput->getErrorMessages()->getAt($LoopIndex)."</li>";
			}
			$Message = $Message."</ul>";
			$TransactionSuccessful = false;
		}
		break;
	default:
		// unhandled status code  
		$NextFormMode = "RESULTS";
		$Message=$goGatewayOutput->getMessage();
		$TransactionSuccessful = false;
		break;
	}
} 


	$_SESSION['cardsave_error'] = $Message;	
	$url=$SiteSecureBaseURL."ecom/index.php?action=checkout.billing";
	header("location:$url");
	
?>
