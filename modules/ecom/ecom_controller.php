<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
include_once($pluginInterface->plugincheck(SITE_PATH."ecom/messages.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/shop_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/shop_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/enquiry_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/enquiry_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/wishlist_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/wishlist_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/compare_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/compare_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/billShipInfo.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/payment.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/paymentClass.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/review.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/saveOrder.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/receipt.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/search.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/shopByBrand.php")); 

include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/main/cardSave.php")); 

class c_ecomController
{

	# Class Constructor
	function c_ecomController($obDatabase,&$obTemplate,$attributes)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->templatePath=THEMEPATH."ecom/templates/main/";
		$this->libFunc=new c_libFunctions();
		$this->comFunc=new c_commonFunctions();
		$this->comFunc->obDb=$obDatabase;
		$this->m_eventHandler();
	}


	# Function to handle events
	function m_eventHandler()
	{
		if(!isset($this->request['action']))
		{
			$this->request['action']="";
		}
		$action=explode(".",$this->request['action']);
		$member = explode("=",$this->request['action']);
		
		if(!isset($this->request['mode']))
		{
			$this->request['mode']="";
		}
		
		if(!isset($action[1]))
		{
			$action[1]="";
		}
		switch($action[0])
		{
			#HANDLING VIEW(FRONTEND-SHOP BUILDER)
			case "ecom":
			$obShopInterface=new c_shopInterface();
			$obShopInterface->obTpl=$this->obTpl;
			$obShopInterface->obDb=$this->obDb;
			$obShopInterface->request=$this->request;
			$obShopInterface->imageUrl=SITE_URL."images/";
			$obShopInterface->imagePath=SITE_PATH."images/";

			$obShopDb=new c_shopDb();
			$obShopDb->obTpl=$this->obTpl;
			$obShopDb->obDb=$this->obDb;
			$obShopDb->request=$this->request;

			$obBill=new c_billShipInfo();
			$obBill->obTpl=$this->obTpl;
			$obBill->obDb=$this->obDb;
			$obBill->request=$this->request;

			$obSearch=new c_search();
			$obSearch->obTpl=$this->obTpl;
			$obSearch->obDb=$this->obDb;
			$obSearch->request=$this->request;
			
			$obBrand=new c_brand();
			$obBrand->obTpl=$this->obTpl;
			$obBrand->obDb=$this->obDb;
			$obBrand->request=$this->request;
			
			switch($action[1])
			{
				case "search":
					$obSearch->searchTemplate=$this->templatePath."searchPage.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo; <a href='#'>Search Results</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obSearch->m_searchResults());
				break;
				
				case "brand":
					$obBrand->brandTemplate=$this->templatePath."brandPage.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo; <a href='#'>Brand Results</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obBrand->m_brandResults());
				break;
				case "details":
					
					if($obShopInterface->m_checkMemberPage())
					{
						$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$this->request['mode']);
						$_SESSION['referer']=$retUrl;
						$this->libFunc->authenticate();
						unset($_SESSION['referer']);
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS",$obShopInterface->m_topNavigation('department'));
						$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showDeptDetails());	
					}else{
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS",$obShopInterface->m_topNavigation('department'));
						$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showDeptDetails());
					}
				break;
				
				case "deptattribute":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS",$obShopInterface->m_topNavigation('department'));
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showDeptAttributeSort());
				break;
				
				case "pdetails":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS",$obShopInterface->m_topNavigation('product'));
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showProductDetails());
				break;
				
				case "pfinder":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo; <a href='#'>Product Finder</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_ProductFinder());
				break;
				case "cdetails":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS",$obShopInterface->m_topNavigation('content'));
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showContentDetails());
				break;
				case "deletereview":
				$obShopDb->m_deleteReview();
				break;
				case "reviewForm":
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.reviewForm&mode=".$this->request['mode']);
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);	$this->obTpl->set_var("TPL_VAR_BREDCRUMBS",$obShopInterface->m_topNavigation('product'));
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showProductDetails());
				break;
			
				case "largeImg":
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspLargeImg());
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS",$obShopInterface->breadcrumb);
				break;
				case "reviewAdd":
					$this->libFunc->authenticate();
					$obShopDb->m_reviewAdd();
				break;
				case "help":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.help");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);
					$obShopDb->m_reviewHelp();
				break;
				case "noHelp":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.noHelp");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);

					$obShopDb->m_reviewNoHelp();
				break;
				case "addtocart":
				if(!$obShopDb->m_addTocart())
				{
					$obShopInterface->template=$this->templatePath."viewcart.tpl.htm";
					$obShopInterface->m_viewCart();
					$obShopDb->stockTemplate=$this->templatePath."stockControl.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Stock control</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopDb->m_dspStockMessage());
				}
				break;

				case "addmulticart":
				if(!$obShopDb->m_addToMulticart())
				{
					$obShopInterface->template=$this->templatePath."viewcart.tpl.htm";
					$obShopInterface->m_viewCart();
					$obShopDb->stockTemplate=$this->templatePath."stockControl.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Stock control</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopDb->m_dspStockMessage());
				}
				break;
			
				case "remove":
					$obShopDb->m_deleteCart();
				break;

				case "updateCart":
				
				if($this->request['mode']== LANG_EMPTYBASKET)
				{
					$obShopDb->m_emptyCart();
				}
				elseif($this->request['mode']== LANG_UPDATEBASKET)
				{
					if(!$obShopDb->m_updateCart())
					{
						$obShopDb->stockTemplate=$this->templatePath."stockControl.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Stock control</a>");
						$this->obTpl->set_var("TPL_VAR_BODY",$obShopDb->m_dspStockMessage());
					}
				}
				else
				{	
					$obShopDb->templatePath=$this->templatePath;
					$obShopDb->Interface=$obShopInterface;
					
					if(!$obShopDb->m_updateCart('1'))
					{
						$obShopDb->stockTemplate=$this->templatePath."stockControl.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Stock control</a>");
						$this->obTpl->set_var("TPL_VAR_BODY",$obShopDb->m_dspStockMessage());
					}
				}
				break;
				case "calcShip":
			
					if($this->request['mode']=="Get Quote")
					{
						$_SESSION['calcShip']=$this->request['mode'];
					}	
				
					$obShopInterface->template=$this->templatePath."viewcart.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS"," &nbsp;&raquo;&nbsp;<a href='#'>Shopping basket</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_viewCart());
				
				break;
				case "viewcart":
					$_SESSION['referer'] = SITE_SAFEURL."ecom/index.php?action=ecom.viewcart";
					$obShopInterface->template=$this->templatePath."viewcart.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS"," &nbsp;&raquo;&nbsp;<a href='#'>Shopping basket</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_viewCart());
				break;
				
				case "changepostage":
                    if(DEFAULT_POSTAGE_METHOD=='zones'){
                       $this->comFunc->m_postageZonePrice($_SESSION['cartweight'],$this->request['countryid'],$_SESSION['grandTotal'],1,$_SESSION['subtotal'],$_SESSION['grandsubTotal'],$_SESSION['VAT']);
                    } elseif(DEFAULT_POSTAGE_METHOD=='cities'){
                        $this->comFunc->m_postageCityPrice($_SESSION['cartweight'],$this->request['countryid'],$_SESSION['grandTotal'],1,$_SESSION['subtotal'],$_SESSION['grandsubTotal'],$_SESSION['VAT'],$this->request['stateid']);
                    }
                
                break;
				
				case "updateviewcart":
                    
                       $this->comFunc->m_UpdateViewCart();
                break;
                
                case "changecountry":
                    $this->comFunc->m_postageCityCountry($this->request['countryid']);
                break;
				
				case "giftwrap":
				$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Gift wrap</a>");
				$obShopInterface->giftTemplate=$this->templatePath."giftwrap.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspGiftWrap());
				break;
				case "giftAdd":
					$obShopDb->m_addGiftWrap();
				break;
				case "removeGift":
					$obShopDb->m_removeGift();
				break;
				case "backitem":
					$obShopDb->m_backOrderSeperate();
				break;
				case "backremove":
					$obShopDb->m_deleteCart();
				break;
				case "instructions":
				$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Backorder instructions</a>");
				$obShopInterface->giftTemplate=$this->templatePath."backorder.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspBackOrderInstructions());
				break;
				default:
					$obShopInterface->template=$this->templatePath."viewcart.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Shopping basket</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_viewCart());
				break;
			}
			break;

			case "checkout":
			
			$obBill=new c_billShipInfo();
			$obBill->obTpl=$this->obTpl;
			$obBill->obDb=$this->obDb;
			$obBill->request=$this->request;

			$obPayment=new c_payment();
			$obPayment->obTpl=$this->obTpl;
			$obPayment->obDb=$this->obDb;
			$obPayment->request=$this->request;
				
			$user=new c_userInterface();
			$user->obTpl=$this->obTpl;
			$user->obDb=$this->obDb;
			$user->request=$this->request;

			$obReview=new c_review();
			$obReview->obTpl=$this->obTpl;
			$obReview->obDb=$this->obDb;
			$obReview->request=$this->request;

			$obSaveOrder=new c_saveOrder();
			$obSaveOrder->obTpl=$this->obTpl;
			$obSaveOrder->obDb=$this->obDb;
			$obSaveOrder->request=$this->request;

			$obreceipt=new c_receipt();
			$obreceipt->obTpl=$this->obTpl;
			$obreceipt->obDb=$this->obDb;
			$obreceipt->request=$this->request;
			$this->libFunc=new c_libFunctions();
			
			$cardSave = new c_cardSave();
			$cardSave->obDb=$this->obDb;
			$cardSave->obTpl=$this->obTpl;
			$cardSave->request=$this->request;
			$cardSave->libFunc=$this->libFunc;
			
			switch($action[1])
			{

				case "login":
					$obBill->m_checkLogin();
					$obBill->loginTemplate=$this->templatePath."checkoutLogin.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Login</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_checkoutLoginForm());
				break;
				case "lost":
					$user->cart=1;
					$user->m_sendPassword();
					$obBill->loginTemplate=$this->templatePath."checkoutLogin.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Login</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_checkoutLoginForm());
				break;
				case "loginForm":
					$obBill->loginTemplate=$this->templatePath."checkoutLogin.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Login</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_checkoutLoginForm());
				break;
				case "logout":
				session_destroy();
				$retUrl1=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
				$_SESSION['referer']=$retUrl1;
				$this->libFunc->authenticate();
				unset($_SESSION['referer']);
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.login");
				header("Location:".$retUrl);
				break;
				case "billing":
					$this->comFunc->m_checkShoppingCart();
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					//$_SESSION['referer']=$retUrl;
					#IF WITHOUT LOGIN CHECKUT SELECTED
					//if(!isset($this->request['email']) && empty($this->request['email'])){
						#IF EMAIL NOT SPECIFIED THEN CHECK LOGIN DETAILS
						//$this->libFunc->m_cartAuthenticate();
						//session_unregister('referer');
					//}
					$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Billing &amp; delivery address</a>");
					unset($_SESSION['referer']);
					$obBill->billShipTemplate=$this->templatePath."ConfirmOrderAndBillShip.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_billShipInfoForm());
				break;
				case "billingerr":
					$this->comFunc->m_checkShoppingCart();
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					//$_SESSION['referer']=$retUrl;
					#IF WITHOUT LOGIN CHECKUT SELECTED
					//if(!isset($this->request['email']) && empty($this->request['email'])){
						#IF EMAIL NOT SPECIFIED THEN CHECK LOGIN DETAILS
						//$this->libFunc->m_cartAuthenticate();
						//session_unregister('referer');
					//}
					$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Billing &amp; delivery address</a>");
					unset($_SESSION['referer']);
					$obBill->billShipTemplate=$this->templatePath."ConfirmOrderAndBillShip.tpl.htm";
					$obBill->err=1;
					$obBill->errMsg='There was a problem with your payment details.';
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_billShipInfoForm());
				break;
				case "addBillShipInfo":
				$this->comFunc->m_checkShoppingCart();
                $result=$obBill->m_verifyBillShipAdd();
                if($result) {
                    echo "||ERROR||1||" . $result . "||";
				}
				else{
					$obBill->m_saveBillShipInfo();
				}
				break;

				case "shipping":
				$this->comFunc->m_checkShoppingCart();	
				$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->m_cartAuthenticate();
					unset($_SESSION['referer']);
					$obBill->postageTemplate=$this->templatePath."ConfirmOrderAndBillShip.tpl.htm";
					$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Postage information</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_postageSelect());
				break;
				case "updatePostage":
				$this->comFunc->m_checkShoppingCart();
					$obBill->m_updatePostage();
				break;
				case "payment":
					$this->comFunc->m_checkShoppingCart();
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->m_cartAuthenticate();
					unset($_SESSION['referer']);
					$obPayment->paymentTemplate=$this->templatePath."payment.tpl.htm";
					$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Choose a payment method</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obPayment->m_paymentMethods());
				break;

				
				case "review":
				$siteUrl=SITE_URL."ecom/index.php?action=checkout.billing";
					$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($siteUrl));
				break;
	
				case "reviewit":
					$this->comFunc->m_checkShoppingCart();
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->m_cartAuthenticate();
					unset($_SESSION['referer']);
					if($obPayment->m_verifyPaymentGateway())
					{
						$obPayment->paymentTemplate=$this->templatePath."payment.tpl.htm";
						//$paymentUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.payment");
						$paymentUrl =$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.billing");
						$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href=\"".$paymentUrl."\">Billing & delivery address</a>&nbsp;&raquo;&nbsp;<a href='#'>Review your order</a>");
						$this->obTpl->set_var("TPL_VAR_BODY",$obPayment->m_paymentMethods());
						
					}
					else
					{
						$obReview->libFunc=$this->libFunc;
						$obReview->reviewTemplate=$this->templatePath."review.tpl.htm";
						//$paymentUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.payment");
						$paymentUrl =$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.billing");
						$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href=\"".$paymentUrl."\">Billing & delivery address</a>&nbsp;&raquo;&nbsp;<a href='#'>Review your order</a>");
						$this->obTpl->set_var("TPL_VAR_BODY",$obReview->m_reviewCheckout());
					}
				break;
		
				case "saveorder":
					$obSaveOrder->cardsaveTemplate=$this->templatePath."cardsave.tpl.htm";
					$this->comFunc->m_checkShoppingCart();
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->m_cartAuthenticate();
					unset($_SESSION['referer']);
					$obSaveOrder->worldpayTemplate=$this->templatePath."worldpay.tpl.htm";
					$obSaveOrder->secpayTemplate=$this->templatePath."secpay.tpl.htm";
					$obSaveOrder->hsbcTemplate=$this->templatePath."hsbc.tpl.htm";
					$obSaveOrder->barclayTemplate=$this->templatePath."barclay.tpl.htm";
					$obSaveOrder->paypalTemplate=$this->templatePath."paypal.tpl.htm";
					$obSaveOrder->offSTTemplate=$this->templatePath."offst.tpl.htm";
					#(BEGIN) SAGEPAY INTEGRATION
                    $obSaveOrder->sagepayTemplate=$this->templatePath."sageform.tpl.htm";
                    #(END) SAGEPAY INTEGRATION
                    $this->comFunc->m_checkShoppingCart();
					$obBill->errMsg=$obSaveOrder->m_saveOrderData();
					//$obSaveOrder->m_saveOrderData();
					$obBill->billShipTemplate=$this->templatePath."ConfirmOrderAndBillShip.tpl.htm";
					$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Choose a payment method</a>");
                    
					unset($_SESSION['userid']);
                    unset($_SESSION['username']);
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_billShipInfoForm());
				break;
				case "return":
					//PAYPAL AND OTHER RETURN PAGE DISPLAY
					$obreceipt->template=$this->templatePath."return.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"#\">Order Confirmation</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obreceipt->m_return());
				break;
				case "IPN":
					//PAYPAL INSTANT PAYMENT NOTIFICATION
			//error_log("\nPaypal IPN received.".time()."\n",3,SITE_PATH."paypal_ipn.log");
					$result = $obreceipt->m_Paypal_IPN_Notification();
			//error_log("\nPaypal IPN result:".$result[0]."|".time()."\n",3,SITE_PATH."paypal_ipn.log");
					if($result[0] == "1")
					{
						$obreceipt->m_sendOrderDetails($result[1]);
					}
				break;
				case "sage3d":
					$obreceipt->m_Sagepay_3D1();
				break;
				case "sage3d2":
					$obreceipt->m_Sagepay_3D2();
				break;
				case "sage3dr":
					$obreceipt->m_Sagepay_3DR();
				break;
				case "cs3d":
					$cardSave->m_cardSave_3D1();
				break;
				case "cs3d2":
					$cardSave->m_cardSave_3D2();
				break;
				case "cs3dr":
					$cardSave->m_cardSave_3DR();
				break;
				case "cshcb":
					$cardSave->m_cardSave_Hosted_Callback("0");
				break;
				case "cshcb2":
					$cardSave->m_cardSave_Hosted_Callback("1");
				break;
                
				case "process":
				$authCode=$_SESSION['vAuthCode'];
				$orderId=$_SESSION['order_id'];
				if($authCode&&$orderId){
					$this->obDb->query="UPDATE ".ORDERS." SET vAuthCode='$authCode' WHERE iOrderid_PK='$orderId'";
					$this->obDb->updateQuery();
				}
					$obreceipt->worldpay=0;
					$obreceipt->processTemplate=$this->templatePath."orderProcessed.tpl.htm";
					$obPayment->errMsg=$obreceipt->m_sendOrderDetails();
					#IF not worldpay
					if($obreceipt->worldpay !=1){
						$obPayment->paymentTemplate=$this->templatePath."payment.tpl.htm";
						$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href=\"".$cartUrl."\">Shopping basket</a>&nbsp;&raquo;&nbsp;<a href='#'>Choose a payment method</a>");
						$this->obTpl->set_var("TPL_VAR_BODY",$obPayment->m_paymentMethods());
					}
				break;
				case "status":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.status&mode=".$this->request['mode']);
					$_SESSION['referer']=$retUrl;
					if((!isset($_SESSION['userid']) || !isset($_SESSION['username']) || $_SESSION['userid']=="")  &&  !isset($_SESSION['customer']) && !isset($_SESSION['email']))
					{
						$siteUrl=SITE_URL."ecom/index.php?action=checkout.loginForm";
						$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($siteUrl));
					}
					unset($_SESSION['referer']);
					$obreceipt->template=$this->templatePath."orderProcessed.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Order Processed</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obreceipt->m_orderProcessed());
				break;
				case "receipt":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.receipt&mode=".$this->request['mode']);
					$_SESSION['referer']=$retUrl;
					if((!isset($_SESSION['userid']) || !isset($_SESSION['username']) || $_SESSION['userid']=="")  &&  !isset($_SESSION['customer']) && !isset($_SESSION['email']))
						{
							$siteUrl=SITE_URL."ecom/index.php?action=checkout.loginForm";
							$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($siteUrl));
						}
					//$this->libFunc->m_cartAuthenticate();
					unset($_SESSION['referer']);
					$obreceipt->receiptTemplate=$this->templatePath."receipt.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>View Receipt</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obreceipt->m_dspreceipt());
				break;
				
				case "editOrder":          
		          if((!isset($_SESSION['userid']) || !isset($_SESSION['username']) || $_SESSION['userid']=="")  &&  !isset($_SESSION['customer']) && !isset($_SESSION['email']))
		          {
		            $siteUrl=SITE_URL."ecom/index.php?action=checkout.loginForm";
		            $this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($siteUrl));
		          }                
				  $obShopDb=new c_shopDb();
			      $obShopDb->obTpl=$this->obTpl;
			      $obShopDb->obDb=$this->obDb;
			      $obShopDb->request=$this->request;
		      	  $obShopDb->m_addInvoiceToCart();  
			      $cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
			      $this->libFunc->m_mosRedirect($cartUrl);   	                            
		        break;
				
				case "cardsave_success":
				 	if($_REQUEST['StatusCode']!='0'){
						$_SESSION['Message'] = $_REQUEST['Message'];
						$retUrl=SITE_URL."ecom/index.php?action=checkout.billing";
						$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($retUrl));
						exit;
					}
					
					$vAuthCode=$_SESSION['vAuthCode']=str_replace("AuthCode: ","",$_REQUEST['Message']);
					if($vAuthCode) {
					$this->obDb->query ="update ".ORDERS." set vAuthCode='$vAuthCode' where iOrderid_PK=".$_REQUEST['OrderID'];
					$this->obDb->updateQuery();
					}
										
					$obCSR->receiptTemplate=$this->templatePath."cardsave_success.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Cardsave Success</a>");
					$retUrl=SITE_URL."ecom/index.php?action=checkout.process&mode=".$_REQUEST['OrderID'];
					$this->obDb->query ="update ".ORDERS." set iOrderStatus=1 where iOrderid_PK=".$_REQUEST['OrderID'];
					$this->obDb->updateQuery();
					$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($retUrl));
				break;
				case "backorder":
					$obreceipt->m_processBackorder();
				break;

				case "supplierConf":
					$obreceipt->receiptTemplate=$this->templatePath."supplier_conf.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Confirmation</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obreceipt->m_supplierOrderConf());
				break;

				default:
					$this->comFunc->m_checkShoppingCart();
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					$_SESSION['referer']=$retUrl;

					if(!isset($this->request['email']) && empty($this->request['email']))
					{
						$this->libFunc->m_cartAuthenticate();
						unset($_SESSION['referer']);
					}
					elseif($obBill->m_valiadateEmail()==1)
					{
						$obBill->loginTemplate=$this->templatePath."checkoutLogin.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Login</a>");
						$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_checkoutLoginForm());
						break;
					}
					$obBill->billShipTemplate=$this->templatePath."ConfirmOrderAndBillShip.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Shopping basket</a>");
					$this->obTpl->set_var("TPL_VAR_BODY",$obBill->m_billShipInfoForm());			
				break;
			}
			break;

			case "wishlist":
				if(USEWISHLIST!=1)
				{
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=error&mode=content");
					header("Location:".$retUrl);
					exit;
				}  
				$obWishInterface=new c_wishInterface();
				$obWishInterface->obTpl=$this->obTpl;
				$obWishInterface->obDb=$this->obDb;
				$obWishInterface->request=$this->request;
				$obWishlistDb=new c_wishlistDb();
				$obWishlistDb->obTpl=$this->obTpl;
				$obWishlistDb->obDb=$this->obDb;
				$obWishlistDb->request=$this->request;
				$this->libFunc=new c_libFunctions();
			switch($action[1])
			{
				case "display":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>My Wish List</a>");
					$obWishInterface->template=$this->templatePath."wishlist.tpl.htm";
					$obWishInterface->libFunc=$this->libFunc;
					$this->obTpl->set_var("TPL_VAR_BODY",$obWishInterface->m_showWishlist());
				break;
				case "add":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.add&mode=".$this->request['mode']);
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);
					$this->libFunc->authenticate();
					$obWishlistDb->m_insertWishlist();
				break;
				case "emailadd":
					$this->libFunc->authenticate();
					$obWishlistDb->m_addWishEmail();
				break;
				case "emailsend":
					$this->libFunc->authenticate();
					$obWishInterface->m_sendEmail();
				break;
				case "emailremove":
					$this->libFunc->authenticate();
					$obWishlistDb->m_removeWishEmail();
				break;
				
				case "modify":
					$this->libFunc->authenticate();
					$obWishlistDb->m_modifyWishlist();
				break;
				default:
					$this->libFunc->authenticate();
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>My wishlist</a>");
					$obWishInterface->template=$this->templatePath."wishlist.tpl.htm";
					$obWishInterface->libFunc=$this->libFunc;
					$this->obTpl->set_var("TPL_VAR_BODY",$obWishInterface->m_showWishlist());
				break;

			}
			break;
			case "compare":
				if(USECOMPARE!=1)
				{
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php");
					header("Location:".$retUrl);
					exit;
				}  
				$obCompareInterface=new c_compareInterface();
				$obCompareInterface->obTpl=$this->obTpl;
				$obCompareInterface->obDb=$this->obDb;
				$obCompareInterface->request=$this->request;
				$obCompareDb=new c_comparelistDb();
				$obCompareDb->obTpl=$this->obTpl;
				$obCompareDb->obDb=$this->obDb;
				$obCompareDb->request=$this->request;
				$this->libFunc=new c_libFunctions();
			switch($action[1])
			{
				case "display":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=compare.display");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>My Comparison List</a>");
					$obCompareInterface->template=$this->templatePath."comparelist.tpl.htm";
					$obCompareInterface->libFunc=$this->libFunc;
					$this->obTpl->set_var("TPL_VAR_BODY",$obCompareInterface->m_showComparelist());
				break;
				case "add":
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=compare.add&mode=".$this->request['mode']);
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);
					$this->libFunc->authenticate();
					$obCompareDb->m_insertComparelist();
				break;
				case "modify":
					$this->libFunc->authenticate();
					$obCompareDb->m_modifyComparelist();
				break;
				default:
					$this->libFunc->authenticate();
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>My Comaparison list</a>");
					$obCompareInterface->template=$this->templatePath."comparelist.tpl.htm";
					$obCompareInterface->libFunc=$this->libFunc;
					$this->obTpl->set_var("TPL_VAR_BODY",$obCompareInterface->m_showComparelist());
				break;

			}
			break;

			case "enquiry":
				case "wishlist":
				$obEnquiryInterface=new c_enquiryInterface();
				$obEnquiryInterface->obTpl=$this->obTpl;
				$obEnquiryInterface->obDb=$this->obDb;
				$obEnquiryInterface->request=$this->request;
				$obEnquiryDb=new c_enquiryDb();
				$obEnquiryDb->obTpl=$this->obTpl;
				$obEnquiryDb->obDb=$this->obDb;
				$obEnquiryDb->request=$this->request;

				$this->libFunc=new c_libFunctions();
			switch($action[1])
			{
				case "dspForm":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Product Enquiry</a>");
					$obEnquiryInterface->enquiryTemplate =$this->templatePath."enquiry.tpl.htm";
					$obEnquiryInterface->libFunc=$this->libFunc;
					$this->obTpl->set_var("TPL_VAR_BODY",$obEnquiryInterface->m_showEnquiryForm());
				break;
				case "post":
					$obEnquiryInterface->m_sendEmail();
				break;
				case "status":
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Product Enquiry</a>");
					$obEnquiryInterface->enquiryTemplate =$this->templatePath."enquirySubmit.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obEnquiryInterface->m_showStatus());
					
				break;
				
				default:
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;<a href='#'>Product Enquiry</a>");
					$obEnquiryInterface->enquiryTemplate =$this->templatePath."enquiry.tpl.htm";
					$obEnquiryInterface->libFunc=$this->libFunc;
					$this->obTpl->set_var("TPL_VAR_BODY",$obEnquiryInterface->m_showEnquiryForm());
				break;

			}
			break;
			default:
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php");
				header("Location:".$retUrl);
				exit;
			break;

		}

	}

}

?>