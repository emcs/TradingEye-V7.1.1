<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_saveOrder
{

#CONSTRUCTOR
	function c_saveOrder()
	{
		$this->err=0;
		$this->fileLink="";
		$this->libFunc=new c_libFunctions();
	}
	#FFUNCTION TO SAVE ORDER DETAILS
	function m_saveOrderData()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$timestamp=time();
		$obPayGateway= new c_paymentGateways();
		# This condition due order not complete by protx 
		if(count($_SESSION)==0)	{
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);
		}
	//	$this->invoice=
		#INTAILIZING
		$this->payMethod=$this->libFunc->ifSet($_SESSION,'payMethod',' ');
		
		$this->grandTotal=$this->libFunc->ifSet($_SESSION,'grandTotal',' ');
		$_SESSION['ship_state_id']=$this->libFunc->ifSet($_SESSION,'ship_state_id',' ');
		$_SESSION['bill_state_id']=$this->libFunc->ifSet($_SESSION,'bill_state_id',' ');
		$_SESSION['bill_state']=$this->libFunc->ifSet($_SESSION,'bill_state',' ');
		$_SESSION['ship_state']=$this->libFunc->ifSet($_SESSION,'ship_state',' ');
		$_SESSION['VAT']=$this->libFunc->ifSet($_SESSION,'VAT',' ');
		$_SESSION['comments']=$this->libFunc->ifSet($_SESSION,'comments',' ');
		$_SESSION['giftCertPrice']=$this->libFunc->ifSet($_SESSION,'giftCertPrice','0');
		$_SESSION['giftCertCode']=$this->libFunc->ifSet($_SESSION,'giftCertCode',' ');
		$_SESSION['discountPrice']=$this->libFunc->ifSet($_SESSION,'discountPrice','0');
		$_SESSION['discountCode']=$this->libFunc->ifSet($_SESSION,'discountCode',' ');

		$_SESSION['memberPointsEarned']=$this->libFunc->ifSet($_SESSION,'memberPointsEarned','0');
		$_SESSION['promotionDiscountPrice']=$this->libFunc->ifSet($_SESSION,'promotionDiscountPrice','0');

		if($_SESSION['useMemberPoints']=='yes')
		{
			$_SESSION['usedMemberPoints']=$this->libFunc->ifSet($_SESSION,'usedMemberPoints','0');	
            $_SESSION['memberPointsUsedAmount']=$this->libFunc->ifSet($_SESSION,'memberPointsUsedAmount','0');
		}
		else
		{
			$_SESSION['usedMemberPoints']=0;
			$_SESSION['memberPointsUsedAmount']=0;
		}
		$_SESSION['cartWeight']=$this->libFunc->ifSet($_SESSION,'cartWeight','0');
		$_SESSION['cartWeightPrice']=$this->libFunc->ifSet($_SESSION,'cartWeightPrice','0');
		$_SESSION['alt_ship']=$this->libFunc->ifSet($_SESSION,"alt_ship",0);

		$_COOKIE['sourceid']=$this->libFunc->ifSet($_COOKIE,"sourceid","");
		if(isset($_SESSION['userid']) && empty($_SESSION['userid']))
		{
			unset($_SESSION['userid']);
		}
		if(!isset($_SESSION['userid']))
		{
			if(isset($_SESSION['txtpassword']))
			{
				$uniqID=$_SESSION['txtpassword'];
			}
			else
			{
				$uniqID=uniqid (3);
			}

			#ADDING NOT REGISTERED CUSTOMER  
			$this->obDb->query= "select iCustmerid_PK,iRegistered FROM ".CUSTOMERS." WHERE vEmail = '".$_SESSION['email']."'";
			$qryResult = $this->obDb->fetchQuery();
			$rCount=$this->obDb->record_count;
			if($rCount > 0 and $qryResult[0]->iRegistered=='0')
			{
				$this->obDb->query="UPDATE ".CUSTOMERS." SET 
				vFirstName		='".$this->libFunc->m_addToDB($_SESSION['first_name'])."',
				vLastName		='".$this->libFunc->m_addToDB($_SESSION['last_name'])."',
				vPassword		= PASSWORD('".$uniqID."'),
				vAddress1		='".$this->libFunc->m_addToDB($_SESSION['address1'])."',
				vAddress2		='".$this->libFunc->m_addToDB($_SESSION['address2'])."',
				vCity				='".$this->libFunc->m_addToDB($_SESSION['city'])."',
				vState			='".$this->libFunc->m_addToDB($_SESSION['bill_state_id'])."',
				vStateName	='".$this->libFunc->m_addToDB($_SESSION['bill_state'])."',
				vCountry		='".$this->libFunc->m_addToDB($_SESSION['bill_country_id'])."',
				vZip				='".$this->libFunc->m_addToDB($_SESSION['zip'])."',
				vCompany		='".$this->libFunc->m_addToDB($_SESSION['company'])."',
				vPhone			='".$this->libFunc->m_addToDB($_SESSION['phone'])."',
				iMailList			='".$_SESSION['mail_list']."',
				tmSignupDate	='".$timestamp."',
				fMemberPoints ='0',";
				
				if(isset($_SESSION['txtpassword']) && !empty($_SESSION['txtpassword']))
				{
					$this->obDb->query= $this->obDb->query."iRegistered ='1',";
				}
				else
				{
					$this->obDb->query= $this->obDb->query." iRegistered ='0',";
				}
				$this->obDb->query= $this->obDb->query."vHomePage='".$this->libFunc->m_addToDB($_SESSION['homepage'])."' WHERE vEmail='".$this->libFunc->m_addToDB($_SESSION['email'])."' AND iRegistered ='0'";
				//die($this->obDb->query);
			}
			elseif(isset($qryResult[0]->iRegistered) && $qryResult[0]->iRegistered=='1')
			{
				$_SESSION['cardsave_error'] = 'Email address already in use. Please login to continue with this email address.<br>';
				$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
				header("Location: ".$retUrl);
				$this->libFunc->m_mosRedirect($retUrl);
				die('Please login to continue');
			}
			else
			{
				$this->obDb->query="INSERT INTO ".CUSTOMERS." SET 
				vFirstName		='".$this->libFunc->m_addToDB($_SESSION['first_name'])."',
				vLastName		='".$this->libFunc->m_addToDB($_SESSION['last_name'])."',
				vEmail			='".$this->libFunc->m_addToDB($_SESSION['email'])."',
				vPassword		= PASSWORD('".$uniqID."'),
				vAddress1		='".$this->libFunc->m_addToDB($_SESSION['address1'])."',
				vAddress2		='".$this->libFunc->m_addToDB($_SESSION['address2'])."',
				vCity				='".$this->libFunc->m_addToDB($_SESSION['city'])."',
				vState			='".$this->libFunc->m_addToDB($_SESSION['bill_state_id'])."',
				vStateName	='".$this->libFunc->m_addToDB($_SESSION['bill_state'])."',
				vCountry		='".$this->libFunc->m_addToDB($_SESSION['bill_country_id'])."',
				vZip				='".$this->libFunc->m_addToDB($_SESSION['zip'])."',
				vCompany		='".$this->libFunc->m_addToDB($_SESSION['company'])."',
				vPhone			='".$this->libFunc->m_addToDB($_SESSION['phone'])."',
				iMailList			='".$_SESSION['mail_list']."',
				tmSignupDate	='".$timestamp."',
				fMemberPoints ='0',
				iStatus ='1',
				vHomePage		='".$this->libFunc->m_addToDB($_SESSION['homepage'])."'";
				if(isset($_SESSION['txtpassword']) && !empty($_SESSION['txtpassword']))
				{
					$this->obDb->query= $this->obDb->query.",iRegistered ='1'";
				}
				else
				{
					$this->obDb->query= $this->obDb->query.",iRegistered ='0'";
				}
			}
			$this->obDb->updateQuery();
			$_SESSION['userid']=$this->obDb->last_insert_id;
			//Guest checkout broke this stuff. Removed below line as the new solution is to create a inaccessible account rather than not add anything at all. Obviously the customer isnt creating a account and should be able to still do so.
			//$comFunc->m_sendDetails($_SESSION['email']);
			#SETTING SESSION ID
		}
		//die("fail");
		if($_SESSION['username'] != "" && $_SESSION['mail_list'] != ""){
			$this->obDb->query ="UPDATE ".CUSTOMERS." SET iMailList=".$_SESSION['mail_list']." WHERE vEmail='".$_SESSION['email']."'";
            $this->obDb->updateQuery();
		}
		if(isset($_SESSION['withoutlogin'])  && $_SESSION['withoutlogin']==1)
		{
			$_SESSION['userid']=0;
		}

		#UPDATING GIFTCERTIFICATES IF THEY ARE USED
		if($_SESSION['giftCertPrice']>0)
		{
			$this->obDb->query ="UPDATE ".GIFTCERTIFICATES." SET fRemaining=fRemaining-".$_SESSION['giftCertPrice']." WHERE vGiftcode='".$_SESSION['giftCertCode']."'";
			$this->obDb->updateQuery();
		}

		#DB QUERY
		$this->obDb->query ="SELECT MAX(iInvoice) as maxInvoice FROM  ".ORDERS;
		$rsInvoice=$this->obDb->fetchQuery();
		$this->invoice=$rsInvoice[0]->maxInvoice;
		if(empty($this->invoice))
		{
			$this->invoice=CONST_INVOICE;
		}
		else
		{
			$this->invoice++;
		}

		if(isset($_SESSION['freeShip']) && $_SESSION['freeShip']==1)
		{
			$_SESSION['postagePrice']=0;
			$_SESSION['postageMethod']=LBL_FREEPP;
		}

		$time=time();
		if(SELECTED_PAYMENTGATEWAY ==='protx'){
			$protx_apply_avs_cv2=PROTX_APPLY_AVS_CV2;
			$protx_3d_secure_status=PROTX_3D_SECURE_STATUS;
		}
		else{
			$protx_apply_avs_cv2=0;
			$protx_3d_secure_status=0;

		}
		$this->VendorTxCode=uniqid(rand(), true);
		#QUERY TO INSERT ORDER DETAILS TO MAIN TABLE
 		$this->obDb->query="INSERT INTO ".ORDERS." SET 
		iInvoice				='".$this->invoice."',
		iCustomerid_FK	='".$_SESSION['userid']."',
		tmOrderDate		='$time',
		vPayMethod		='".$this->libFunc->m_addToDB($_SESSION['payMethod'])."',
		vShipDescription	='".$this->libFunc->m_addToDB($_SESSION['postageMethod'])."',
		vShipMethod_Id	='".$this->libFunc->m_addToDB($_SESSION['postageId'])."',
		fShipTotal			='".$this->libFunc->m_addToDB($_SESSION['postagePrice'])."',
		vFirstName			='".$this->libFunc->m_addToDB($_SESSION['first_name'])."',
		vLastName			='".$this->libFunc->m_addToDB($_SESSION['last_name'])."',
		vEmail				='".$this->libFunc->m_addToDB($_SESSION['email'])."',
		vAddress1			='".$this->libFunc->m_addToDB($_SESSION['address1'])."',
		vAddress2			='".$this->libFunc->m_addToDB($_SESSION['address2'])."',
		vCity					='".$this->libFunc->m_addToDB($_SESSION['city'])."',
		vState				='".$this->libFunc->m_addToDB($_SESSION['bill_state_id'])."',
		vStateName		='".$this->libFunc->m_addToDB($_SESSION['bill_state'])."',
		vCountry			='".$this->libFunc->m_addToDB($_SESSION['bill_country_id'])."',
		vZip					='".$this->libFunc->m_addToDB($_SESSION['zip'])."',
		vCompany			='".$this->libFunc->m_addToDB($_SESSION['company'])."',
		vPhone				='".$this->libFunc->m_addToDB($_SESSION['phone'])."',
		iSameAsBilling 		='".$this->libFunc->m_addToDB($_SESSION['alt_ship'])."',
		vAltCompany			='".$this->libFunc->m_addToDB($_SESSION['alt_company'])."',
		vAltName			='".$this->libFunc->m_addToDB($_SESSION['alt_name'])."',
		vAltAddress1		='".$this->libFunc->m_addToDB($_SESSION['alt_address1'])."',
		vAltAddress2		='".$this->libFunc->m_addToDB($_SESSION['alt_address2'])."',
		vAltCity				='".$this->libFunc->m_addToDB($_SESSION['alt_city'])."',
		vAltState			='".$this->libFunc->m_addToDB($_SESSION['ship_state_id'])."',
		vAltStateName	 	='".$this->libFunc->m_addToDB($_SESSION['ship_state'])."',
		vAltZip				='".$this->libFunc->m_addToDB($_SESSION['alt_zip'])."',
		vAltPhone	 		='".$this->libFunc->m_addToDB($_SESSION['alt_phone'])."',
		vAltCountry			='".$this->libFunc->m_addToDB($_SESSION['ship_country_id'])."',
		vHomePage			='".$this->libFunc->m_addToDB($_SESSION['homepage'])."',
		vDiscountCode	 	='".$this->libFunc->m_addToDB($_SESSION['discountCode'])."',	
		fDiscount 			='".$this->libFunc->m_addToDB($_SESSION['discountPrice'])."',	
		iGiftcert_FK 		='".$this->libFunc->m_addToDB($_SESSION['giftCertCode'])."',	
		fGiftcertTotal		='".$this->libFunc->m_addToDB($_SESSION['giftCertPrice'])."',	
		fPromoValue 		='".$this->libFunc->m_addToDB($_SESSION['promotionDiscountPrice'])."',
		fTaxRate				='".$this->libFunc->m_addToDB($_SESSION['VAT'])."',	
		fTaxPrice			='".$this->libFunc->m_addToDB($_SESSION['vatTotal'])."',	
		tComments			='".$this->libFunc->m_addToDB($_SESSION['comments'])."',	
		vCustomerIP			='".$_SERVER['REMOTE_ADDR']."',
		fShipByWeightPrice='".$_SESSION['cartWeightPrice']."',	
		fShipByWeightKg	='".$_SESSION['cartWeight']."',	
		fCodCharge  		='".$_SESSION['codPrice']."',	
		fMemberPoints		='".$_SESSION['memberPointsUsedAmount']."',	
		vSid 					='".$_COOKIE['sourceid']."',
		iPayStatus  		='0',	
		fTotalPrice  		='".$this->libFunc->m_addToDB($_SESSION['grandTotal'])."',
		iTransactionId		='".$this->VendorTxCode."',
		vRemote_address  	='',
		vProtx_apply_avs_cv2  	='".$protx_apply_avs_cv2."',
		vProtx_3d_secure_status  	='".$protx_3d_secure_status."',
		iEarnedPoints ='".$this->libFunc->m_addToDB(ceil($_SESSION['memberPointsEarned']))."'";	
	
			
		$this->obDb->updateQuery();
		#ORDER ID
		$this->orderId=$this->obDb->last_insert_id;
		$_SESSION['order_id']=$this->orderId;
		$_SESSION['invoicenumber']=$this->invoice;

		if($this->request['IssueNumber']=='0')
		{
			$this->request['IssueNumber']="";
		}
		if($this->request['cc_type']==='SOLO' && $this->request['cc_type']==='SWITCH'){
			$this->request['cc_type']='MAESTRO';
		}

		if(($_SESSION['payMethod']=='cc' || $_SESSION['payMethod']=='eft') && SELECTED_PAYMENTGATEWAY!='protx')
		{
			#INSERTING CREDIT CARD DETAILS
			$this->obDb->query="INSERT INTO ".CREDITCARDS." SET 
			iOrderid_FK			='".$this->orderId."',
			vCCnumber	 		='".$this->libFunc->m_addToDB($_SESSION['cc_number'])."',
			vCCtype 			='".$this->libFunc->m_addToDB($_SESSION['cc_type'])."',
			vCCyear 			='".$this->libFunc->m_addToDB($_SESSION['cc_year'])."',
			vCCmonth 			='".$this->libFunc->m_addToDB($_SESSION['cc_month'])."',
			vCCstart_year 	='".$this->libFunc->m_addToDB($_SESSION['cc_start_year'])."',
			vCCstart_month 	='".$this->libFunc->m_addToDB($_SESSION['cc_start_month'])."',
			vAba 					='".$this->libFunc->m_addToDB($_SESSION['aba'])."',
			vAcct 				='".$this->libFunc->m_addToDB($_SESSION['acct'])."',
			vCCissuenumber 	='".$this->libFunc->m_addToDB($_SESSION['issuenumber'])."'";
			$this->obDb->updateQuery();
		}

		#QUERY TO INSERT PRODUCT DETAILS  
		#GETTING DATA FROM SESSION (TEMPORARY DATA) 
		$this->obDb->query = "SELECT vTitle,vSeoTitle,fPrice,fRetailPrice,iVendorid_FK,vSku,iQty,iTmpCartId_PK,iProdId_FK,vShipCode,iKit,iGiftWrap,tShortDescription,iTaxable,fVolDiscount,vDownloadablefile,iFreeShip,vShipNotes FROM ".TEMPCART." T,".PRODUCTS." P WHERE iProdId_FK=iProdId_PK AND  vSessionId='".SESSIONID."'";
	
		#FLAG TO INDICATE SEPERATE BACKORDER AND NORMAL ORDER
		$_SESSION['backOrderSeperate']=$this->libFunc->ifSet($_SESSION,'backOrderSeperate','0');

		#FLAG TO INDICATE WHETHER PROCESSING BACKORDER OR NOT
		$_SESSION['backOrderProcess']=$this->libFunc->ifSet($_SESSION,'backOrderProcess','0');

		if($_SESSION['backOrderSeperate']==1 && $_SESSION['backOrderProcess']==1)
		{
			$this->obDb->query .=" AND T.iBackOrder='1'";
		}
		elseif($_SESSION['backOrderSeperate']==1)
		{
			$this->obDb->query .=" AND T.iBackOrder<>'1'";
		}



		$rowCart=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		if($rsCount>0)
		{
			for($i=0;$i<$rsCount;$i++)
			{#FOR LOOP PRODUCT BEGIN
					#MARGIN CALCULATOR
				switch (MARGINSTATUS)
				{
					case "increase":
						$rowCart[$i]->fPrice= ($rowCart[$i]->fPrice * MARGINPERCENT/100 ) + $rowCart[$i]->fPrice;
					break;
					case "decrease":
						$rowCart[$i]->fPrice=  $rowCart[$i]->fPrice - ($rowCart[$i]->fPrice * MARGINPERCENT/100 );
					break;
					default:
						$rowCart[$i]->fPrice = $rowCart[$i]->fPrice;
					break;	
				}
				#END MARGIN CALCULATOR
				//--- Switch to retail price if Retail customer
				if ($comFunc->m_checkCustomerType()==1 && ENABLE_WHOLESALE==1 && $rowCart[$i]->fRetailPrice>0){
				$rowCart[$i]->fPrice=$rowCart[$i]->fRetailPrice;
				}
				//----End switch price
				//$this->obDb->query ="SELECT vSeoTitle FROM ".PRODUCTS." WHERE iProdId_FK=iProdid_PK ='".$rowCart[$i]->iProdId_FK."'";
				//$SeoReturn=$this->obDb->fetchQuery();
				$qty=$rowCart[$i]->iQty;
				#INSERTING PRODUCTS TO ORDERED PRODUCT TABLE
				$this->obDb->query ="INSERT INTO ".ORDERPRODUCTS." SET ";
				$this->obDb->query.="iOrderid_FK		='".$this->orderId."',";
				$this->obDb->query.="iProductid_FK	='".$rowCart[$i]->iProdId_FK."',";
				$this->obDb->query.="iVendorid_FK	='".$rowCart[$i]->iVendorid_FK."',";
				$this->obDb->query.="iQty				='".$rowCart[$i]->iQty."',";
				$this->obDb->query.="iGiftwrapFK		='".$rowCart[$i]->iGiftWrap."',";
				$this->obDb->query.="fPrice				='".$rowCart[$i]->fPrice."',";
				$this->obDb->query.="vTitle				='".$this->libFunc->m_addToDB($rowCart[$i]->vTitle)."',";
				$this->obDb->query.="seo_title			='".$this->libFunc->m_addToDB($rowCart[$i]->vSeoTitle)."',";
				$this->obDb->query.="vSku				='".$this->libFunc->m_addToDB($rowCart[$i]->vSku)."',";
				$this->obDb->query.="iKit					='".$rowCart[$i]->iKit."',";
				$this->obDb->query.="fDiscount			='".$rowCart[$i]->fVolDiscount."',";
				$this->obDb->query.="tShortDescription='".$this->libFunc->m_addToDB($rowCart[$i]->tShortDescription)."',";
				$this->obDb->query.="iTaxable			='".$rowCart[$i]->iTaxable."',";
				$this->obDb->query.="iFreeship			='".$rowCart[$i]->iFreeShip."',";
				$this->obDb->query.="vPostageNotes	='".$this->libFunc->m_addToDB($rowCart[$i]->vShipNotes)."'";
				$this->obDb->updateQuery();
				$prodOrderId=$this->obDb->last_insert_id;
	 			
				if($rowCart[$i]->iKit==1)
				{
					 $this->obDb->query ="SELECT PK.*,P.vTitle,P.vSku FROM ".PRODUCTKITS." PK,".PRODUCTS." P WHERE iProdId_FK=iProdId_PK AND   iKitId='".$rowCart[$i]->iProdId_FK."'";
					$rsKits=$this->obDb->fetchQuery();
					$kitCount=$this->obDb->record_count;
					#INSERTING KITS*********************************************
					if($kitCount>0)
					{
						for($k=0;$k<$kitCount;$k++)
						{
							$this->obDb->query="INSERT INTO ".ORDERKITS." SET 
							iOrderid_FK 				='".$this->orderId."',
							iProductid_FK			='".$rsKits[$k]->iProdId_FK."',
							iProductOrderid_FK	='".$prodOrderId."',
							iKitid	 					='".$rsKits[$k]->iKitId."',
							iKitItem_id				='".$rsKits[$k]->iKitId_PK."',
							iKitgroup 				='".$rsKits[$k]->iKitId."',
							iKitItem_title			='".$this->libFunc->m_addToDB($rsKits[$k]->vTitle." (".$rsKits[$k]->vSku.")")."'";
							$this->obDb->updateQuery();
						$this->m_updateOptions($rowCart[$i]->iTmpCartId_PK,$rsKits[$k]->iProdId_FK,$prodOrderId);
						}
					}
				}
				else
				{
					$this->m_updateOptions($rowCart[$i]->iTmpCartId_PK,$rowCart[$i]->iProdId_FK,$prodOrderId);	
				}


				#INSERTING CHOICES*********************************************
				 $this->obDb->query ="SELECT vDescription,vChoiceVal,fPrice,iChoiceid_PK,vType,iQty  FROM ".CHOICES.", ".TEMPCHOICES." WHERE iTmpChoiceId_FK=iChoiceid_PK AND iTmpCartId_FK='".$rowCart[$i]->iTmpCartId_PK."'";
				$rsChoices=$this->obDb->fetchQuery();
				$rsChoiceCount=$this->obDb->record_count;
				if($rsChoiceCount>0)
				{
					for($j=0;$j<$rsChoiceCount;$j++)
					{
						#IF QUANTITY IS SELECTED
						if($rsChoices[$j]->iQty==1)
						{
							$rsChoices[$j]->fPrice=$rsChoices[$j]->fPrice*intval($rsChoices[$j]->vChoiceVal);
						}
						$this->obDb->query="INSERT INTO ".ORDERCHOICES." SET 
						iOrderid_FK 				='".$this->orderId."',
						iProductid_FK			='".$rowCart[$i]->iProdId_FK."',
						iProductOrderid_FK	='".$prodOrderId."',
						iChoiceid_FK			='".$rsChoices[$j]->iChoiceid_PK."',
						vChoiceValue			='".$this->libFunc->m_addToDB($rsChoices[$j]->vChoiceVal)."',
						vDescription 			='".$this->libFunc->m_addToDB($rsChoices[$j]->vDescription)."',
						fPrice						='".$rsChoices[$j]->fPrice."',
						vType 					='".$this->libFunc->m_addToDB($rsChoices[$j]->vType)."'";
						$this->obDb->updateQuery();
					}#END FOR CHOICE
				}#ENF IF CHOICE
			}#FOR LOOP PRODUCT END
			$this->request = array_merge($this->request,$_SESSION); 
		}#IF END
	   
		//$obPayGateway=new c_paymentGateways();	
		$obPayGateway->payStatus=1;
		$this->payTotal=number_format($this->grandTotal,2,'.','');
		//die($this->payMethod);
		if($this->payTotal>0) {
			switch($this->payMethod) {		
				#CARDSAVE
				case "cs_redirect":
					$cardSave = new c_cardSave($this->orderId);
					$cardSave->obDb=$this->obDb;
					$cardSave->obTpl=$this->obTpl;
					$cardSave->request=$this->request;
					$cardSave->libFunc=$this->libFunc;
					$cardSave->m_CardSave_Hosted();
					exit;
				break;
				#FORM BASED PAYPAL
				case "paypal":
				$this->ObTpl=new template();
				$this->ObTpl->set_file("TPL_PAYPAL_FILE",$this->paypalTemplate);
				$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);
				$this->ObTpl->set_var("TPL_VAR_SESSIONID",SESSIONID);

				$this->ObTpl->set_var("TPL_VAR_PAYACTION",PAYPAL_URL);
				$this->ObTpl->set_var("TPL_VAR_PAYPALID",PAYPAL_ID);
				$this->ObTpl->set_var("TPL_VAR_PAYCURRENCY",PAYMENT_CURRENCY);
				$this->ObTpl->set_var("TPL_VAR_ITEMNAME",SITE_NAME);
				$this->ObTpl->set_var("TPL_VAR_ITEMNUMBER",$this->orderId);
				$this->ObTpl->set_var("TPL_VAR_INVOICENUMBER",$this->invoice);
				$this->ObTpl->set_var("TPL_VAR_GRANDTOTAL",$this->payTotal);
				$this->ObTpl->pparse("return","TPL_PAYPAL_FILE");
				exit;
				break;
								
				case "secpay":
					$this->secpaySubmit();
					exit;
				break;				
				case "securetrading":
					$this->offSTSubmit();
					exit;
				break;

				case "paypaldirect":
					$this->m_submitPaypalDirect();
				break;
				
                #(BEGIN) SAGEPAY INTERGRATION 
				case "sagepayform":
					$this->m_sagepayHostedSubmit();
					exit;
				break;
                #(END) SAGEPAY INTERGRATION 
                
				case "cc":
			 
				#IMPLEMENTING PAYMENT GATEWAYS
				$obPayGateway->payMethod=SELECTED_PAYMENTGATEWAY;
				switch(SELECTED_PAYMENTGATEWAY){
					case "Cardsave":
						$cardSave = new c_cardSave($this->orderId);
						$cardSave->obDb=$this->obDb;
						$cardSave->obTpl=$this->obTpl;
						$cardSave->request=$this->request;
						$cardSave->libFunc=$this->libFunc;
						$cardSave->m_CardSave_Direct();
						exit;
					break;
					case "protx":	
						$this->m_sagepaySubmit();
						exit;
						break;
						case "authorizenet":
						$authnet_values	= array(
							"x_login"					=> AUTHORIZEPAYMENT_LOGIN,
							"x_version"				=> "3.1",
							"x_delim_char"			=> "|",
							"x_delim_data"			=> "TRUE",
							"x_url"					=> "FALSE",
							"x_type"					=> AUTHORIZEPAYMENT_TYPE,
							"x_method"				=> "CC",
							"x_tran_key"			=> AUTHORIZEPAYMENT_KEY,
							"x_relay_response"	=> "FALSE",
							"x_invoice_num"		=>	 $this->orderId,
							"x_card_num"			=> $this->request['CCNumber'],
							"x_card_code"			=> $this->request["cv2"],
							"x_exp_date"			=>	  $this->request['CCMonth'].$this->request['CCYear'],
							"x_description"			=> SITE_NAME." products",
							"x_amount"				=> $this->payTotal,
							"x_first_name"			=> $this->request["first_name"],
							"x_last_name"			=> $this->request["last_name"],
							"x_Company"			=> $this->request["company"],
							"x_address"				=> $this->request["address1"]." ".$this->request["address2"],
							"x_city"					=> $this->request["city"],
							"x_state"				=> $this->m_stateName($this->request["bill_state_id"],$this->request["bill_state"]),
							"x_country"	=>$this->m_countryName($this->request['bill_country_id']),
							"x_zip"					=> $this->request["zip"],
							"x_email"					=> $this->request["email"]	,
							"x_phone"				=> $this->request["phone"],
							"x_Ship_To_First_Name"	=> $this->request["alt_name"],
							"x_Ship_To_Address"	=> $this->request["alt_address1"],
							"x_Ship_To_City"	=> $this->request["alt_city"],
							"x_Ship_To_State"	=> $this->m_stateName($this->request["ship_state_id"],''),
							"x_Ship_To_Zip"	=> $this->request["alt_zip"],
							"x_country"	=>$this->m_countryName($this->request['ship_country_id'])
							);
						$fields = "";
						foreach( $authnet_values as $key => $value ) 
						{
							$fields .= "$key=" . urlencode( $value ) . "&";
						}
						$requestBody=rtrim( $fields, "&");			
					 	$result=$obPayGateway->sendHttpRequest($requestBody,AUTHORIZENET_URL);
						$obPayGateway->fnRetStatus($result);
						break;
						case VERISIGN:
							if(VERISIGN_USER==""){
								$verisignUser=VERISIGN_LOGIN;
							}else{
								$verisignUser=VERISIGN_USER;
							}
							#SETTING SHIP STATENAME
							$billStateId		=$this->libFunc->ifSet($_SESSION,'bill_state_id','0');
							$shipStateId		=$this->libFunc->ifSet($_SESSION,'ship_state_id','0');
							
							$this->request['CCYear']=substr($this->request['CCYear'],2);

							$verisignStr ="USER=".$verisignUser."&";
							$verisignStr.="VENDOR=".VERISIGN_LOGIN."&";
							$verisignStr.="PARTNER=".VERISIGN_PARTNER."&";
							$verisignStr.="PWD=".VERISIGN_PASSWORD."&";
							$verisignStr.="TRXTYPE=S&";
							$verisignStr.="TENDER=C&";
							$verisignStr.="ACCT=".$this->request['CCNumber']."&";
							$verisignStr.="EXPDATE=".$this->request['CCMonth'].$this->request['CCYear']."&";
							$verisignStr.="AMT=".$this->payTotal."&";
							$verisignStr.="PONUM=".$this->invoice."&";
							$verisignStr.="CUSTREF=".$_SESSION['userid']."&";
							$verisignStr.="CVV2=".$this->request['cv2']."&";
							$verisignStr.="FIRSTNAME=".$_SESSION['first_name']."&";
							$verisignStr.="LASTNAME=".$_SESSION['last_name']."&";
							$verisignStr.="EMAIL=".$_SESSION['email']."&";
							$verisignStr.="CITY=".$_SESSION['city']."&";
							$verisignStr.="ZIP=".$_SESSION['zip']."&";
							$verisignStr.="TAXAMT=".$_SESSION['vatTotal']."&";
							$verisignStr.="PHONENUM=".$_SESSION['phone']."&";
							$verisignStr.="STREET=".$_SESSION['address1']." ".$_SESSION['address2']."&";
							$verisignStr.="STATE=".$this->m_stateName($billStateId,$_SESSION['bill_state'])."&";
							$verisignStr.="BILLTOCOUNTRY=".$this->m_countryName($_SESSION['bill_country_id'])."&";
							$verisignStr.="SHIPTOFIRSTNAME=".$_SESSION['first_name']."&";
							$verisignStr.="SHIPTOLASTNAME=".$_SESSION['last_name']."&";
							$verisignStr.="SHIPTOSTREET=".$_SESSION['address1']." ".$_SESSION['address2']."&";
							$verisignStr.="SHIPTOCITY=".$_SESSION['city']."&";
							$verisignStr.="SHIPTOSTATE=".$this->m_stateName($shipStateId,$_SESSION['bill_state'])."&";
							$verisignStr.="SHIPTOZIP=".$_SESSION['zip']."&";
							$verisignStr.="SHIPTOCOUNTRY=".$this->m_countryName($_SESSION['bill_country_id'])."&";
							$verisignStr.="VERBOSITY=MEDIUM";

							$libPath=SITE_PATH."payflowpro/linux/";
			
							exec("perl ".$libPath."execute.pl ".VERISIGN_URL." ".VERISIGN_PORT." '$verisignStr' '$libPath'",$varans);
							//exec("perl ".SITE_PATH."payflowpro/linux/execute.pl ".VERISIGN_URL." ".VERISIGN_PORT." '$verisignStr'",$varans);
						
						
							$arr = explode("&",$varans[0]);
							$cntArr=count($arr);
							if($cntArr>0)
							{
								for($i=0; $i<$cntArr; $i++)
								{
									if($arr[$i] != "" && strstr($arr[$i],'=')){
										list($key, $value)=split("=", $arr[$i], 2);
										$resultArray[$key]=$value;
									}
								}
							}
							if(!isset($resultArray['RESULT'])){
								$obPayGateway->payStatus=0;
								$obPayGateway->errMsg="Payment gateway is not setup properly";
							}else{
								if($resultArray['RESULT']!=0){
									$obPayGateway->payStatus=0;
								}
								$obPayGateway->errMsg=$resultArray['RESPMSG'];
								$obPayGateway->transactionId=$resultArray['PNREF'];
							}
					break;
					case "securetrading":
						  $this->securetradingSubmit();
					break;
					case "propay":
						//Propay Gateway Integration:Starts
						$url = PROPAY_URL;
						$this->obDb->query= "select max(iInvoice) as iInvoiceId FROM ".ORDERS;
						$invoice = $this->obDb->fetchQuery();
						$invoice_id = $invoice[0]->iInvoiceId;
						if($_SESSION['alt_address1'] != ""){
							$address = $_SESSION['alt_address1'];
						}else{
							$address = $_SESSION['address1'];
						}
						if($_SESSION['alt_zip'] != ""){
							$zip_code = $_SESSION['alt_zip'];
						}else{
							$zip_code = $_SESSION['zip'];
						}
						$exp_year = substr($_SESSION['cc_year'], -2);
						$grand_total = $_SESSION['grandTotal']*100;
						$post_string = "<?xml version='1.0'?>
							<!DOCTYPE Request.dtd>
							<XMLRequest>
							<certStr>".PROPAY_CERTSTRING."</certStr>
							<class>partner</class>
									<XMLTrans>
									<transType>04</transType>
									<accountNum>".PROPAY_ACCNUMBER."</accountNum>
									<amount>".$grand_total."</amount>
									<addr>".$address."</addr>
									<zip>".$zip_code."</zip>
									<sourceEmail>".$_SESSION['email']."</sourceEmail>
									<ccNum>".$_SESSION['cc_number']."</ccNum>
									<expDate>".$_SESSION['cc_month']."".$exp_year."</expDate>";
									$post_string .= "<AVS>Y</AVS>";	
									$post_string .= "<CVV2>".$_SESSION['cv2']."</CVV2>	
									<cardholderName>".$_SESSION['cardholder_name']."</cardholderName>
									<invNum>".$invoice_id."</invNum>
									</XMLTrans>	
							</XMLRequest>";
						$xml_response = $obPayGateway->sendHttpRequest($post_string,$url);
						$xmlparse = $obPayGateway->xml2array($xml_response);
						$obPayGateway->propay_response($xmlparse);

						if( $obPayGateway->flag != "SUCCESS"){
							$_SESSION['pro'] = $obPayGateway->errMsg;
							$this->errMsg = $obPayGateway->errMsg;
							$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
							$this->libFunc->m_mosRedirect($retUrl);
						}else{
							$_SESSION['pro'] = "";							
							$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$_SESSION['order_id']);
							$this->libFunc->m_mosRedirect($retUrl);
						}
						//Propay Gateway Integration:Ends
					break;
				} # END of online paymentgateways
				break;
			} #End of offline switch
		}# End of if paymethod

		if($obPayGateway->payStatus!=1)	{
			return $obPayGateway->errMsg;
		}

		if(isset($obPayGateway->transactionId))
		{
			$this->obDb->query= "UPDATE ".ORDERS." SET iPayStatus='1',iTransactionId='".$obPayGateway->transactionId."'   WHERE iOrderid_PK = '".$this->orderId."'";
			$rs = $this->obDb->updateQuery();
		}
		$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$this->orderId);
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END SAVE ORDER

	function offSTSubmit()
	{
	$this->ObTpl=new template();
	$this->ObTpl->set_file("TPL_OFFST_FILE",$this->offSTTemplate);
	$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);
	$this->ObTpl->set_var("TPL_VAR_PAYACTION",OFFST_URL);	
	$this->ObTpl->set_var("TPL_VAR_ST_AMOUNT",100*$this->payTotal);
	$this->ObTpl->set_var("ST_SITEREFERENCE", STREFERENCE);
	
	$this->ObTpl->set_var("TPL_VAR_MERCHANTEMAIL",ORDER_EMAIL);
	$this->ObTpl->set_var("TPL_VAR_MERCHANT",SECPAY_MERCHANT);
	$this->ObTpl->set_var("TPL_VAR_CURRENCY",'gbp');
	$this->ObTpl->set_var("TPL_VAR_ORDERINFOR", ORDERINFOR);
	
	$this->ObTpl->set_var("TPL_VAR_NAME", $this->libFunc->m_displayContent($_SESSION['first_name'])." ".$this->libFunc->m_displayContent($_SESSION['last_name']));
	$this->ObTpl->set_var("TPL_VAR_ADDRESS",$this->libFunc->m_displayContent($_SESSION['address1']));
	
	$this->ObTpl->set_var("ST_ORDERREF",$this->invoice);
	$this->ObTpl->set_var("ST_TOWN",$this->libFunc->m_displayContent($_SESSION['city']));
	
	//--------state
	$stateId=$this->libFunc->ifSet($_SESSION,'ship_state_id','0');
	$stateName=$_SESSION['ship_state'];
	$this->ObTpl->set_var("ST_COUNTY",$this->m_stateName($stateId,$stateName));
	//--------	
	$Callback_URL=$this->libFunc->m_safeUrl(SITE_URL."securetrading/callback.php");
	$this->ObTpl->set_var("CALLBACK_URL",SITE_URL."securetrading/callback.php");
	
	$this->ObTpl->set_var("ST_COUNTRY",$this->m_countryName($_SESSION['ship_country_id']));
	$this->ObTpl->set_var("ST_POSTCODE",$this->libFunc->m_displayContent($_SESSION['zip']));
	$this->ObTpl->set_var("ST_EMAIL",$this->libFunc->m_displayContent($_SESSION['email']));
	$this->ObTpl->set_var("ST_TELEPHONE",$this->libFunc->m_displayContent($_SESSION['phone']));
	$this->ObTpl->set_var("TPL_VAR_MODE",$this->orderId);
	$this->ObTpl->set_var("TPL_VAR_SESSIONID",session_id());	
	$this->ObTpl->set_var("TPL_CALLBACK_URL",$Callback_URL);
	$this->ObTpl->pparse("return","TPL_OFFST_FILE");
	
	
	}

	function m_updateOptions($cartId,$prodId,$prodOrderId)
	{
		#**NOTE -OPTIONVALUEID IS SAVED AS WE CAN GET OPTION ID FROM OPTION VALUE ID
		#INSERTING OPTIONS
		$this->obDb->query ="SELECT vName,vDescription,vOptVal,iOptionid_PK FROM ".OPTIONS.", ".TEMPOPTIONS." WHERE iOptId_FK=iOptionid_PK AND iTmpCartId_FK='".$cartId."' AND iProdId_Fk='".$prodId."'";
		
		$rsOptions=$this->obDb->fetchQuery();
		$optCount=$this->obDb->record_count;

		if($optCount>0)
		{

			for($k=0;$k<$optCount;$k++)
			{
				 $this->obDb->query ="SELECT vItem,fPrice FROM ".OPTIONVALUES." WHERE iOptionid_FK='".$rsOptions[$k]->iOptionid_PK."' AND iOptionValueid_PK='".$rsOptions[$k]->vOptVal."'";
				 
				$rsOptionValue=$this->obDb->fetchQuery();

				$this->obDb->query="INSERT INTO ".ORDEROPTIONS." SET 
				iOrderid_FK 				='".$this->orderId."',
				iProductid_FK			='".$prodId."',
				iProductOrderid_FK	='".$prodOrderId."',
				iOptionid					='".$rsOptions[$k]->vOptVal."',
				vName 					='".$this->libFunc->m_addToDB($rsOptions[$k]->vDescription)."',
				vItem  					='".$this->libFunc->m_addToDB($rsOptionValue[0]->vItem)."',
				fPrice						='".$rsOptionValue[0]->fPrice."'";
				$this->obDb->updateQuery();
			}#END FOR
		}#END IF OPTION
	}#ef

	#FUNCTION TO RETURN COUNTRY NAME
	function m_countryName($countryId,$code=0){
		$this->obDb->query = "SELECT vCountryName,vCountryCode,vShortName FROM ".COUNTRY." where iCountryId_PK  = '".$countryId."'";
		$row_country = $this->obDb->fetchQuery();
		if($code==1){
			return $row_country[0]->vCountryCode;
		}elseif($code==2){
			return $row_country[0]->vShortName;
		}else{
			return $row_country[0]->vCountryName;
		}
	}

	#FUNCTION TO RETURN STATE NAME
	function m_stateName($stateId,$stateName){
		if($stateId){
			$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$stateId."'";
			$row_state = $this->obDb->fetchQuery();
			return $row_state[0]->vStateName;
		}else{
			return $stateName;
		}
	}

	#FUNCTION TO SET WORLDPAY TEMPLATE TO SUBMIT TO PYMENT GATEWAY
	function worldpaySubmit(){
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_WORLDPAY_FILE",$this->worldpayTemplate);
		$MC_callback=SITE_SAFEURL.'ecom/index.php';
		$this->ObTpl->set_var("TPL_CALLBACK_URL",$MC_callback);
		$this->ObTpl->set_var("TPL_VAR_SESSIONID",session_id());
		$this->ObTpl->set_var("TPL_VAR_PAYACTION",WORLDPAY_URL);
		$this->ObTpl->set_var("TPL_VAR_INSTID",WORLDPAY_INSTID);
		$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",SITE_NAME);
		$this->ObTpl->set_var("TPL_VAR_PAYCURRENCY",PAYMENT_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_GRANDTOTAL",$this->payTotal);
		$this->ObTpl->set_var("TPL_VAR_INVOICE",$this->invoice);
		$this->ObTpl->set_var("TPL_VAR_ORDERID",$this->orderId);
		$this->ObTpl->set_var("WORLDPAY_TEST_MODE",WORLDPAY_TEST_MODE);
		$this->ObTpl->set_var("TPL_VAR_FIRSTNAME",$this->libFunc->m_displayContent($_SESSION['first_name']));
		$this->ObTpl->set_var("TPL_VAR_LASTNAME",$this->libFunc->m_displayContent($_SESSION['last_name']));
		$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($_SESSION['email']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($_SESSION['address1']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($_SESSION['address2']));
		$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($_SESSION['city']));
		$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($_SESSION['zip']));
		$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($_SESSION['phone']));
		#SETTING SHIP STATENAME
		$stateId		=$this->libFunc->ifSet($_SESSION,'ship_state_id','0');
		$stateName=$_SESSION['ship_state'];
		$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$this->m_stateName($stateId,$stateName));
		#SETTING BILL COUNTRY NAME- CODE
		$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",
		$this->libFunc->m_displayContent($this->m_countryName($_SESSION['bill_country_id'],2,'.','')));

		$this->ObTpl->pparse("return","TPL_WORLDPAY_FILE");
	}#EF

	#FUNCTION TO SET SECPAY TEMPLATE TO SUBMIT TO PYMENT GATEWAY
	function secpaySubmit(){
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SECPAY_FILE",$this->secpayTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);
		$this->ObTpl->set_var("TPL_VAR_SESSIONID",session_id());
		$this->ObTpl->set_var("TPL_VAR_PAYACTION",SECPAY_URL);
		$this->ObTpl->set_var("TPL_VAR_MERCHANT",SECPAY_MERCHANT);
		$this->ObTpl->set_var("TPL_VAR_PAYCURRENCY",PAYMENT_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_INVOICE",$this->invoice);
		$this->ObTpl->set_var("TPL_VAR_ITEMNUMBER",$this->orderId);
		$this->ObTpl->set_var("TPL_VAR_GRANDTOTAL",$this->payTotal);
		$this->ObTpl->set_var("SECPAY_MODE",SECPAY_MODE);
		$this->ObTpl->set_var("TPL_VAR_MERCHANTEMAIL",ORDER_EMAIL);
		//$Callback_URL=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$this->orderId."&phpsessid=".session_id());

		$Callback_URL=$this->libFunc->m_safeUrl(SITE_URL."SECpay/callback.php");
		$this->ObTpl->set_var("CALLBACK_URL",SITE_URL."SECpay/callback.php");

		$Backcallback_url=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.review&mode1=cancel");
		$this->ObTpl->set_var("BACKCALLBACK_URL",$Backcallback_url);

		#CALCULATE DIGEST KEY
		$digestKey=md5($this->invoice.$this->payTotal.SECPAY_REMOTEPASSWORD);
		$this->ObTpl->set_var("TPL_VAR_DIGEST_CALCULATED",$digestKey);


		#SETTING BILL STATENAME
		$stateId		=$this->libFunc->ifSet($_SESSION,'bill_state_id','0');
		$stateName=$_SESSION['bill_state'];
		$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$this->m_stateName($stateId,$stateName));

		
		#SETTING BILL COUNTRY NAME
		$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",
		$this->libFunc->m_displayContent($this->m_countryName($_SESSION['bill_country_id'])));

		#SETTING SHIP STATENAME
		$stateId		=$this->libFunc->ifSet($_SESSION,'ship_state_id','0');
		$stateName=$_SESSION['ship_state'];
		$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$this->m_stateName($stateId,$stateName));

		#SETTING SHIP COUNTRYNAME
		$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
		$this->libFunc->m_displayContent($this->m_countryName($_SESSION['ship_country_id'])));

		#SETTING BILLLING INFO
		$this->ObTpl->set_var("TPL_VAR_FIRSTNAME",$this->libFunc->m_displayContent($_SESSION['first_name']));
		$this->ObTpl->set_var("TPL_VAR_LASTNAME",$this->libFunc->m_displayContent($_SESSION['last_name']));
		$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($_SESSION['email']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($_SESSION['address1']));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($_SESSION['address2']));
		$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($_SESSION['city']));
		$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($_SESSION['zip']));
		$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($_SESSION['company']));
		$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($_SESSION['phone']));
	
		#SETTING SHIPPING INFO
		$this->ObTpl->set_var("TPL_VAR_ALTNAME",$this->libFunc->m_displayContent($_SESSION['alt_name']));
		$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($_SESSION['alt_address1']));
		$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($_SESSION['alt_address2']));
		$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($_SESSION['alt_city']));
		
		$this->ObTpl->set_var("SHIP_STATE","");
		
		#SETTING SHIP STATENAME
		if($this->libFunc->ifSet($_SESSION,'ship_state_id','0'))
		{
			$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$_SESSION['ship_state_id']."'";
			$row_state = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$this->libFunc->m_displayContent($row_state[0]->vStateName));
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$_SESSION['ship_state']);
		}
		#SETTING SHIP COUNTRYNAME
		$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['ship_country_id']."'";
		$row_country = $this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
		$this->libFunc->m_displayContent($row_country[0]->vCountryName));


		$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($_SESSION['alt_zip']));
		$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($_SESSION['alt_phone']));

		$this->ObTpl->pparse("return","TPL_SECPAY_FILE");
	}#EF
	
	
	#start of SecureTrading Payment gateway
	function securetradingSubmit(){
		$CpiDirectResultUrl = SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$this->orderId."&phpsessid=".session_id();

		#define the remote cgi in readiness to call pullpage function 
		//DPI - RKT 30082007  

		if(GATEWAY_TESTMODE == 1) {
			$securetradingurl = SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$this->orderId;
		}
		else {
			$securetradingurl = SECURETRADING_URL;
		}

	
		#SETTING BILL STATENAME
		if($this->libFunc->ifSet($_SESSION,'bill_state_id','0'))
		{
			$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$_SESSION['bill_state_id']."'";
			$row_state = $this->obDb->fetchQuery();
			$billstatename = $this->libFunc-> m_displayContent_sec($this->libFunc-> m_displayContent_sec($row_state[0]->vStateName));
		}
		else
		{
			$billstatename = $this->libFunc-> m_displayContent_sec($_SESSION['bill_state']);
		}
		
		#SETTING BILL COUNTRY NAME
		$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['bill_country_id']."'";
		$row_country = $this->obDb->fetchQuery();
		$billcountryname = $this->libFunc-> m_displayContent_sec($row_country[0]->vCountryName);
		#SETTING SHIP STATENAME
		if($this->libFunc->ifSet($_SESSION,'ship_state_id','0'))
		{
			$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$_SESSION['ship_state_id']."'";
			$row_state = $this->obDb->fetchQuery();
			$shipstatename = $this->libFunc-> m_displayContent_sec($row_state[0]->vStateName);
		}
		else
		{
			$shipstatename = $this->libFunc-> m_displayContent_sec($_SESSION['ship_state']);
		}

		#SETTING SHIP COUNTRYNAME
		$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['ship_country_id']."'";
		$row_country = $this->obDb->fetchQuery();
	//	$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
	//	$this->libFunc-> m_displayContent_sec($row_country[0]->vCountryName));
		$shipcountryname = $this->libFunc-> m_displayContent_sec($row_country[0]->vCountryName);

		$billingname   = $this->libFunc->m_displayContent_sec($_SESSION['first_name']) . " " . $this->libFunc->m_displayContent_sec($_SESSION['last_name']);

		$merchant		= SECURETRADING_MERCHANTID;
		$orderref		= $this->orderId;
		$orderinfo		= "Order from ".SITE_NAME;
		$amount			= number_format($_SESSION['grandTotal'],2,'.','');
		$amount			= $amount * 100;
		$currency		= SECURETRADING_CURRENCY;
		$merchantemail	= ORDER_EMAIL;
		$customeremail	= $this->libFunc->m_displayContent_sec($_SESSION['email']);
		$callbackurl	= 1;
		$failureurl		= 1;
		$settlementday	= "";
		$formref		= "";
		$name			= $billingname;
		$address		= $this->libFunc->m_displayContent_sec($_SESSION['address1']) ." ". $this->libFunc->m_displayContent($_SESSION['address2']);
		$town			= $this->libFunc->m_displayContent_sec($_SESSION['city']);
		$county			= $this->libFunc->m_displayContent_sec($billstatename);
		$country		= $this->libFunc->m_displayContent_sec($billcountryname);
		$postcode		= $this->libFunc->m_displayContent_sec($_SESSION['zip']);
		$telephone		= $this->libFunc->m_displayContent_sec($_SESSION['phone']);
		$email			= $this->libFunc->m_displayContent_sec($_SESSION['email']);
		$trans_id		= $this->invoice;
		$options		= "";
		$sessionFld			= $this->libFunc->m_displayContent_sec(session_id());	
		$bill_name		= $billingname;
		$bill_company	= $this->libFunc->m_displayContent_sec($_SESSION['company']);
		$bill_addr_1	= $this->libFunc->m_displayContent_sec($_SESSION['address1']);
		$bill_addr_2	= $this->libFunc->m_displayContent_sec($_SESSION['address2']);
		$bill_city		= $this->libFunc->m_displayContent_sec($_SESSION['city']);
		$bill_state		= $this->libFunc->m_displayContent_sec($billstatename);
		$bill_country	= $this->libFunc->m_displayContent_sec($billcountryname);
		$bill_post_code	= $this->libFunc->m_displayContent_sec($_SESSION['zip']);
		$bill_tel		= $this->libFunc->m_displayContent_sec($_SESSION['phone']);
		$bill_addr_2	= $this->libFunc->m_displayContent_sec($bill_addr_2);
		$bill_email		= $this->libFunc->m_displayContent_sec($_SESSION['email']);
		$ship_name		= $this->libFunc->m_displayContent_sec($_SESSION['alt_name']);
		$ship_addr_1	= "";
		$ship_addr_2	= "";
		$ship_city		= $this->libFunc->m_displayContent_sec($_SESSION['alt_city']);
		$ship_state		= $this->libFunc->m_displayContent_sec($shipstatename);
		$ship_country   = $this->libFunc->m_displayContent_sec($shipcountryname);
		$ship_post_code	= $this->libFunc->m_displayContent_sec($_SESSION['alt_zip']);
		$ship_tel		= $this->libFunc->m_displayContent_sec($_SESSION['alt_phone']);
		$ship_email		= "";

		$cc_number		= $this->libFunc->m_displayContent_sec($_SESSION['cc_number']);
		$cc_type		= $this->libFunc->m_displayContent_sec($_SESSION['cc_type']);
		$cv2   = $this->libFunc->m_displayContent_sec($_SESSION['cv2']);
		$cc_year	= $this->libFunc->m_displayContent_sec($_SESSION['cc_year']);
		$cc_month	= $this->libFunc->m_displayContent_sec($_SESSION['cc_month']);
		$cc_start_year		= $this->libFunc->m_displayContent_sec($_SESSION['cc_start_year']);
		$cc_start_month		= $this->libFunc->m_displayContent_sec($_SESSION['cc_start_month']);
		$ccissue		= $this->libFunc->m_displayContent_sec($_SESSION['issuenumber']);

		switch($cc_type){
				case "VISA":
				$card_type = "Visa";
				break;
				case "MC":
				$card_type = "Mastercard";
				break;
				case "MASTRO":
				$card_type = "Maestro";
				break;
				case "SOLO":
				$card_type = "Solo";
				break;
				case "DELTA":
				$card_type = "Delta";
				break;
				case "SWITCH":
                $card_type = "Maestro";
                break;
			}

		$Mode = "P";
		$OrderDesc ="Order from ".SITE_NAME;
		$UserId = $_SESSION['userid']; //Optional, unique identifier for the user
		$OrderId = $this->orderId; //unique order number

		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html>
		<head>
		<title>Payment Page</title>
		</head>
		<body>
		<form method=\"post\" action=\"$securetradingurl\" id=\"paymentpage\" name=\"paymentpage\" onsubmit=\"return submitpayment();\">
		<input type=\"hidden\" name=\"auth_type\" value=\"test\"/>
		<input type=\"hidden\" name=\"merchant\" value=\"$merchant\"/>
		<input type=\"hidden\" name=\"orderref\" value=\"$orderref\"/>
		<input type=\"hidden\" name=\"orderinfo\" value=\"$orderinfo\"/>
		<input type=\"hidden\" name=\"amount\" value=\"$amount\"/>
		<input type=\"hidden\" name=\"currency\" value=\"$currency\"/>
		<input type=\"hidden\" name=\"merchantemail\" value=\"$merchantemail\"/>
		<input type=\"hidden\" name=\"customeremail\" value=\"$customeremail\"/>
		<input type=\"hidden\" name=\"cctype\" value=\"$card_type\"/>
		<input type=\"hidden\" name=\"ccnumber\" value=\"$cc_number\"/>
		<input type=\"hidden\" name=\"st_expiryyears\" value=\"$cc_year\"/>
		<input type=\"hidden\" name=\"month\" value=\"$cc_month\"/>
		<input type=\"hidden\" name=\"year\" value=\"$cc_year\"/>	 
		<input type=\"hidden\" name=\"st_startyears\" value=\"$cc_start_year\"/>
		<input type=\"hidden\" name=\"securitycode\" value=\"$cv2\"/>
		<input type=\"hidden\" name=\"ccissue\" value=\"$ccissue\"/>
		<input type=\"hidden\" name=\"callbackurl\" value=\"$callbackurl\"/>
		<input type=\"hidden\" name=\"failureurl\" value=\"$failureurl\"/>
		<input type=\"hidden\" name=\"settlementday\" value=\"$settlementday\"/>
		<input type=\"hidden\" name=\"formref\" value=\"$formref\"/>		
		<input type=\"hidden\" name=\"name\" value=\"$name\">
		<input type=\"hidden\" name=\"address\" value=\"$address\">
		<input type=\"hidden\" name=\"town\" value=\"$town\">
		<input type=\"hidden\" name=\"county\" value=\"$county\">
		<input type=\"hidden\" name=\"country\" value=\"$country\">
		<input type=\"hidden\" name=\"postcode\" value=\"$postcode\">
		<input type=\"hidden\" name=\"telephone\" value=\"$telephone\">
		<input type=\"hidden\" name=\"email\" value=\"$email\">
		<input type=\"hidden\" name=\"trans_id\" value=\"$trans_id\">
		<input type=\"hidden\" name=\"options\" value=\"$options\">
		<input type=\"hidden\" name=\"sessionFld\" value=\"$sessionFld\">
		<input type=\"hidden\" name=\"bill_name\" value=\"$bill_name\">
		<input type=\"hidden\" name=\"bill_company\" value=\"$bill_company\">
		<input type=\"hidden\" name=\"bill_addr_1\" value=\"$bill_addr_1\">
		<input type=\"hidden\" name=\"bill_addr_2\" value=\"$bill_addr_2\">
		<input type=\"hidden\" name=\"bill_city\" value=\"$bill_city\">
		<input type=\"hidden\" name=\"bill_state\" value=\"$bill_state\">
		<input type=\"hidden\" name=\"bill_country\" value=\"$bill_country\">
		<input type=\"hidden\" name=\"bill_post_code\" value=\"$bill_post_code\">
		<input type=\"hidden\" name=\"bill_tel\" value=\"$bill_tel\">
		<input type=\"hidden\" name=\"bill_email\" value=\"$bill_email\">
		<input type=\"hidden\" name=\"ship_name\" value=\"$ship_name\">
		<input type=\"hidden\" name=\"ship_addr_1\" value=\"$ship_addr_1\">
		<input type=\"hidden\" name=\"ship_addr_2\" value=\"$ship_addr_2\">
		<input type=\"hidden\" name=\"ship_city\" value=\"$ship_city\">
		<input type=\"hidden\" name=\"ship_state\" value=\"$ship_state\">
		<input type=\"hidden\" name=\"ship_country\" value=\"$ship_country\">
		<input type=\"hidden\" name=\"ship_post_code\" value=\"$ship_post_code\">
		<input type=\"hidden\" name=\"ship_tel\" value=\"$ship_tel\">
		<input type=\"hidden\" name=\"ship_email\" value=\"$ship_email\">
		<input type=\"hidden\" name=\"Mode\" value=\"$Mode\">
		<input type=\"hidden\" name=\"OrderId\" value=\"$OrderId\">
		<input type=\"hidden\" name=\"UserId\" value=\"$UserId\">
		<input type=\"hidden\" name=\"OrderDesc\" value=\"$OrderDesc\">	";

	
		echo "<input type=\"submit\" value=\"Click here if you do not get automatically redirected in 10 seconds...\"></form><script>document.paymentpage.submit();</script>
		</body></html>";
		die;
	}#End of SecureTrading Payment gateway
    
 # (BEGIN) SAGEPAY INTERGRATION 
	function m_sagepaySubmit(){
			// Now to build the Form crypt field.  For more details see the Form Protocol 2.23 
            
			if($this->request['cc_type']!='SOLO' && $this->request['cc_type']!='SWITCH'){
				$this->request['IssueNumber']="";
			}
			if($this->request['cc_type']==='SOLO' && $this->request['cc_type']==='SWITCH'){
				$this->request['cc_type']='MAESTRO';
			}
							
			$this->request['StartYear']=substr($_SESSION['cc_start_year'],2);
			$this->request['CCYear']=substr($_SESSION['cc_year'],2);
			$strPost="VPSProtocol=2.23";
			$strPost=$strPost . "&TxType=PAYMENT";
			$strPost=$strPost . "&VendorTxCode=".$this->VendorTxCode;
			$strPost=$strPost . "&Vendor=" . PROTX_VENDOR;
			$strPost=$strPost . "&Amount=" . number_format($_SESSION['grandTotal'],2); // Formatted to 2 decimal places with leading digit
			$strPost=$strPost . "&Currency=" . PAYMENT_CURRENCY;
			// Up to 100 chars of free format description
			$strPost=$strPost . "&Description=Order from " . SITE_NAME;
			$strPost=$strPost . "&ClientIPAddress=" . $_SERVER['REMOTE_ADDR'];
			$billingname   = $this->libFunc->m_displayContent_sec($_SESSION['first_name']) . " " . $this->libFunc->m_displayContent_sec($_SESSION['last_name']);
			// This is an Optional setting. Here we are just using the Billing names given.
			$strPost=$strPost . "&CustomerName=" . $billingname;
			$strPost=$strPost . "&VendorEMail=".ORDER_EMAIL;  // This is an Optional setting
			
			// Billing Details:
			$strPost=$strPost . "&BillingFirstnames=" . $_SESSION['first_name'];
			$strPost=$strPost . "&BillingSurname=" . $_SESSION['last_name'];
			$strPost=$strPost . "&BillingAddress1=" . $this->libFunc->m_displayContent_sec($_SESSION['address1']);
			$strPost=$strPost . "&BillingAddress2=" . $this->libFunc->m_displayContent_sec($_SESSION['address2']);
			$strPost=$strPost . "&BillingCity=" . $_SESSION['city'];
			$strPost=$strPost . "&BillingPostCode=" . $this->libFunc->m_displayContent_sec($_SESSION['zip']);
			
			$this->obDb->query = "SELECT vShortname FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['bill_country_id']."'";
			$row_country = $this->obDb->fetchQuery();
			$billcountryname = $this->libFunc-> m_displayContent_sec($row_country[0]->vShortname);
			
			$strPost=$strPost . "&BillingCountry=".$billcountryname;
			$strPost=$strPost . "&BillingPhone=" . $this->libFunc->m_displayContent_sec($_SESSION['phone']);
			
			if($_SESSION['alt_fName'] == ""){
				$_SESSION['alt_fName'] = $_SESSION['first_name'];
			}
			if($_SESSION['alt_lName'] == ""){
				$_SESSION['alt_lName'] = $_SESSION['last_name'];
			}
			if($_SESSION['alt_address1'] == ""){
				$_SESSION['alt_address1'] = $_SESSION['address1'];
			}
			if($_SESSION['alt_city'] == ""){
				$_SESSION['alt_city'] = $_SESSION['city'];
			}
			if($_SESSION['alt_zip'] == ""){
				$_SESSION['alt_zip'] = $_SESSION['zip'];
			}
			$strPost=$strPost . "&DeliveryFirstnames=" . $_SESSION['alt_fName'];
						$strPost=$strPost . "&CardType=" . $this->request['cc_type'];
						$strPost=$strPost . "&CardHolder=" . $_SESSION['cardholder_name'];
						$strPost=$strPost . "&CardNumber=" . $_SESSION['cc_number'];
						if(isset($_SESSION['cc_start_month']) && $_SESSION['cc_start_month'] !='')
						{
						$strPost=$strPost . "&StartDate=" . $_SESSION['cc_start_month'].$this->request['StartYear'];
						}
						$strPost=$strPost . "&ExpiryDate=" . $_SESSION['cc_month'].$this->request['CCYear'];
						if(isset($this->request['IssueNumber']) && $this->request['IssueNumber'] !='')
						{
						$strPost=$strPost . "&IssueNumber=" . $_SESSION['issuenumber'];
						}
						if(isset($_SESSION['cv2']) && $_SESSION['cv2'] !='')
						{
						$strPost=$strPost . "&CV2=" . $_SESSION['cv2'];
						}
						$strPost=$strPost . "&CardType=" . $_SESSION['cc_type'];
						$strPost=$strPost . "&CustomerEMail=" . $_SESSION['email'];
			// Delivery Details:
			$strPost=$strPost . "&DeliveryFirstnames=" . $_SESSION['alt_fName'];
			$strPost=$strPost . "&DeliverySurname=" . $_SESSION['alt_lName'];
			$strPost=$strPost . "&DeliveryAddress1=" . $this->libFunc->m_displayContent($_SESSION['alt_address1']);
			$strPost=$strPost . "&DeliveryAddress2=" . "";
			$strPost=$strPost . "&DeliveryCity=" . $this->libFunc->m_displayContent_sec($_SESSION['alt_city']);
			$strPost=$strPost . "&DeliveryPostCode=" . $this->libFunc->m_displayContent_sec($_SESSION['alt_zip']);
			
			#SETTING SHIP COUNTRYNAME
			$this->obDb->query = "SELECT vShortname FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['ship_country_id']."'";
			$row_country = $this->obDb->fetchQuery();
			$shipcountryname = $this->libFunc-> m_displayContent_sec($row_country[0]->vShortname);
		
			$strPost=$strPost . "&DeliveryCountry=" . $this->libFunc->m_displayContent_sec($shipcountryname);
			$strPost=$strPost . "&DeliveryPhone=" . $this->libFunc->m_displayContent($_SESSION['alt_phone']);
			$strPost=$strPost . "&Apply3DSecure=".PROTX_3D_SECURE_STATUS;
			$strPost=$strPost . "&ApplyAVSCV2=".PROTX_APPLY_AVS_CV2;
			//die($strPost);
			if (GATEWAY_TESTMODE == 0)
				{
				  $strAbortURL="https://live.sagepay.com/gateway/service/abort.vsp";
				  $strAuthoriseURL="https://live.sagepay.com/gateway/service/authorise.vsp";
				  $strCancelURL="https://live.sagepay.com/gateway/service/cancel.vsp";
				  $strPurchaseURL="https://live.sagepay.com/gateway/service/vspdirect-register.vsp";
				  $strRefundURL="https://live.sagepay.com/gateway/service/refund.vsp";
				  $strReleaseURL="https://live.sagepay.com/gateway/service/release.vsp";
				  $strRepeatURL="https://live.sagepay.com/gateway/service/repeat.vsp";
				  $strVoidURL="https://live.sagepay.com/gateway/service/void.vsp";
				  $_SESSION['str3DCallbackPage']="https://live.sagepay.com/gateway/service/direct3dcallback.vsp";
				  $strPayPalCompletionURL="https://live.sagepay.com/gateway/service/complete.vsp";
				}
			elseif (GATEWAY_TESTMODE == 1)
				{
				  $strAbortURL="https://test.sagepay.com/gateway/service/abort.vsp";
				  $strAuthoriseURL="https://test.sagepay.com/gateway/service/authorise.vsp";
				  $strCancelURL="https://test.sagepay.com/gateway/service/cancel.vsp";
				  $strPurchaseURL="https://test.sagepay.com/gateway/service/vspdirect-register.vsp";
				  $strRefundURL="https://test.sagepay.com/gateway/service/refund.vsp";
				  $strReleaseURL="https://test.sagepay.com/gateway/service/release.vsp";
				  $strRepeatURL="https://test.sagepay.com/gateway/service/repeat.vsp";
				  $strVoidURL="https://test.sagepay.com/gateway/service/void.vsp";
				  $_SESSION['str3DCallbackPage']="https://test.sagepay.com/gateway/service/direct3dcallback.vsp";
				  $strPayPalCompletionURL="https://test.sagepay.com/gateway/service/complete.vsp";
				}
			else
				{
				  $strAbortURL="https://test.sagepay.com/simulator/VSPServerGateway.asp?Service=VendorAbortTx";
				  $strAuthoriseURL="https://test.sagepay.com/simulator/VSPServerGateway.asp?Service=VendorAuthoriseTx";
				  $strCancelURL="https://test.sagepay.com/simulator/VSPServerGateway.asp?Service=VendorCancelTx";
				  $strPurchaseURL="https://test.sagepay.com/simulator/VSPDirectGateway.asp";
				  $strRefundURL="https://test.sagepay.com/simulator/VSPServerGateway.asp?Service=VendorRefundTx";
				  $strReleaseURL="https://test.sagepay.com/simulator/VSPServerGateway.asp?Service=VendorReleaseTx";
				  $strRepeatURL="https://test.sagepay.com/simulator/VSPServerGateway.asp?Service=VendorRepeatTx";
				  $strVoidURL="https://test.sagepay.com/simulator/VSPServerGateway.asp?Service=VendorVoidTx";
				  $_SESSION['str3DCallbackPage']="https://test.sagepay.com/simulator/VSPDirectCallback.asp";
				  $strPayPalCompletionURL="https://test.sagepay.com/simulator/paypalcomplete.asp";
				}	

			$arrResponse = $this->requestPost($strPurchaseURL, $strPost);
			$strStatus=$arrResponse["Status"];
			$strStatusDetail=$arrResponse["StatusDetail"];
			if ($strStatus=="3DAUTH") 
			{
				/* This is a 3D-Secure transaction, so we need to redirect the customer to their bank
				** for authentication.  First get the pertinent information from the response */
				$strMD=$arrResponse["MD"];
				$strACSURL=$arrResponse["ACSURL"];
				$strPAReq=$arrResponse["PAReq"];
				/*echo '<html><body><table class="formTable">
            	<tr>
					<td><div class="subheader">3D-Secure Authentication with your Bank</div></td>
              	</tr>
              	<tr>
                	<td>
						<table class="formTable">
							<tr>
								<td width="80%">
									<p>To increase the security of Internet transactions Visa and Mastercard have introduced 3D-Secure (like an online version of Chip and PIN). <br>
							  			<br>
						    			You have chosen to use a card that is part of the 3D-Secure scheme, so you will need to authenticate yourself with your bank in the section below.
						    		</p>
						    	</td>
								<td width="20%" align="center"><img src="images/vbv_logo_small.gif" alt="Verified by Visa"><BR><BR><img src="images/mcsc_logo.gif" alt="MasterCard SecureCode"></td>
							</tr>
						</table>
						<div class="greyHzShadeBar">&nbsp;</div>
					</td>
              	</tr>
			  	<tr>
                	<td valign="top">';*/
				$_SESSION["MD"]=$strMD;
				$_SESSION["PAReq"]=$strPAReq;
				$_SESSION["ACSURL"]=$strACSURL;
				$_SESSION["VendorTxCode"]=$this->VendorTxCode;
				/*echo '<IFRAME SRC="'. SITE_SAFEURL.'ecom/index.php?action=checkout.sage3d&VendorTxCode=' . $this->VendorTxCode .'" NAME="3DIFrame" WIDTH="100%" HEIGHT="500" FRAMEBORDER="0">
					  </IFRAME>
					</td>
			  	</tr>
			</table>
            <div class="greyHzShadeBar"></body></html>';*/
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL.'ecom/index.php?action=checkout.sage3d&VendorTxCode=' . $this->VendorTxCode);
					$this->libFunc->m_mosRedirect($retUrl);
			} 
			else
			{
				/* If this isn't 3D-Auth, then this is an authorisation result (either successful or otherwise) **
				** Get the results form the POST if they are there */
				$strVPSTxId=$arrResponse["VPSTxId"];
				$strSecurityKey=$arrResponse["SecurityKey"];
				$strTxAuthNo=$arrResponse["TxAuthNo"];
				$strAVSCV2=$arrResponse["AVSCV2"];
				$strAddressResult=$arrResponse["AddressResult"];
				$strPostCodeResult=$arrResponse["PostCodeResult"];
				$strCV2Result=$arrResponse["CV2Result"];
				$str3DSecureStatus=$arrResponse["3DSecureStatus"];
				$strCAVV=$arrResponse["CAVV"];
						
				// Update the database and redirect the user appropriately
				if ($strStatus=="OK")
					$strDBStatus="AUTHORISED - The transaction was successfully authorised with the bank.";
				elseif ($strStatus=="MALFORMED")
					$strDBStatus="MALFORMED - The StatusDetail was:" . mysql_real_escape_string(substr($strStatusDetail,0,255));
				elseif ($strStatus=="INVALID")
					$strDBStatus="INVALID - The StatusDetail was:" . mysql_real_escape_string(substr($strStatusDetail,0,255));
				elseif ($strStatus=="NOTAUTHED")
					$strDBStatus="DECLINED - The transaction was not authorised by the bank.";
				elseif ($strStatus=="REJECTED")
					$strDBStatus="REJECTED - The transaction was failed by your 3D-Secure or AVS/CV2 rule-bases.";
				elseif ($strStatus=="AUTHENTICATED")
					$strDBStatus="AUTHENTICATED - The transaction was successfully 3D-Secure Authenticated and can now be Authorised.";
				elseif ($strStatus=="REGISTERED")
					$strDBStatus="REGISTERED - The transaction was could not be 3D-Secure Authenticated, but has been registered to be Authorised.";
				elseif ($strStatus=="ERROR")
					$strDBStatus="ERROR - There was an error during the payment process.  The error details are: " . mysql_real_escape_string($strStatusDetail);
				else
					$strDBStatus="UNKNOWN - An unknown status was returned from Sage Pay.  The Status was: " . mysql_real_escape_string($strStatus) . ", with StatusDetail:" . mysql_real_escape_string($strStatusDetail);
				$this->obDb->query= "UPDATE ".ORDERS." SET v3DSecureStatus='".$strDBStatus."' WHERE iOrderid_PK = '".$this->orderId."'";
	    			$rs = $this->obDb->updateQuery();
				$_SESSION["VendorTxCode"]=$this->VendorTxCode;
				if (($strStatus=="OK")||($strStatus=="AUTHENTICATED")||($strStatus=="REGISTERED"))
				{
					$this->obDb->query= "UPDATE ".ORDERS." SET iOrderStatus=1,iPayStatus=1 WHERE iOrderid_PK = '".$this->orderId."'";
	    			$rs = $this->obDb->updateQuery();
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$this->orderId);
					$this->libFunc->m_mosRedirect($retUrl);
				}
				else {
					$strPageError=$strDBStatus;
					$_SESSION['cardsave_error']=$strPageError;
					$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
					$this->libFunc->m_mosRedirect($retUrl);
					}
			}
			return false;















	}#EF
 # (BEGIN) SAGEPAY INTERGRATION 
	function m_sagepayHostedSubmit(){

			$this->ObTpl=new template();
			$this->ObTpl->set_file("TPL_SAGEPAY_FILE",$this->sagepayTemplate);
			$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);
			$this->ObTpl->set_var("TPL_VAR_SESSIONID",session_id());
			$this->ObTpl->set_var("TPL_VAR_PAYACTION",SAGEFORM_URL);
			$this->ObTpl->set_var("TPL_VAR_VENDOR",SAGE_VENDORNAME);
            $this->ObTpl->set_var("TPL_VAR_TRANSACTIONTYPE",SAGE_TRANSACTIONTYPE);
            
            			
            $strEncryptionPassword= SAGE_ENCRYPTEDPASSWORD;
			
			           
			// Now to build the Form crypt field.  For more details see the Form Protocol 2.23 
            
			$strPost="VendorTxCode=".$this->invoice; /** As generated above **/
					
			$strPost=$strPost . "&Amount=" . number_format($_SESSION['grandTotal'],2); // Formatted to 2 decimal places with leading digit
			$strPost=$strPost . "&Currency=" . SAGE_CURRENCY;
			// Up to 100 chars of free format description
			$strPost=$strPost . "&Description=Order from " . SITE_NAME;

			
			/* The SuccessURL is the page to which Form returns the customer if the transaction is successful 
			** You can change this for each transaction, perhaps passing a session ID or state flag if you wish */

			$validate = $this->orderId."_".session_id();
            $validate = $this->base64Encode($this->SimpleXor($validate,$strEncryptionPassword));	
            $strPost=$strPost . "&SuccessURL=".SITE_SAFEURL."sagepay/callback.php?validate=".$validate;
			   
            
			/* The FailureURL is the page to which Form returns the customer if the transaction is unsuccessful
			** You can change this for each transaction, perhaps passing a session ID or state flag if you wish */
			$strPost=$strPost . "&FailureURL=" . SITE_SAFEURL. "sagepay/callback.php";
			
            
			$billingname   = $this->libFunc->m_displayContent_sec($_SESSION['first_name']) . " " . $this->libFunc->m_displayContent_sec($_SESSION['last_name']);
			// This is an Optional setting. Here we are just using the Billing names given.
			$strPost=$strPost . "&CustomerName=" . $billingname;
			
			/* Email settings:
			** Flag 'SendEMail' is an Optional setting. 
			** 0 = Do not send either customer or vendor e-mails, 
			** 1 = Send customer and vendor e-mails if address(es) are provided(DEFAULT). 
			** 2 = Send Vendor Email but not Customer Email. If you do not supply this field, 1 is assumed and e-mails are sent if addresses are provided. **/
			$strPost=$strPost . "&SendEMail=1";
			$strCustomerEMail = $this->libFunc->m_displayContent_sec($_SESSION['email']);
			if (strlen($strCustomerEMail) > 0)
			        $strPost=$strPost . "&CustomerEMail=" . $strCustomerEMail;  // This is an Optional setting
			  
			$strPost=$strPost . "&VendorEMail=".ORDER_EMAIL;  // This is an Optional setting
			
			// You can specify any custom message to send to your customers in their confirmation e-mail here
			// The field can contain HTML if you wish, and be different for each order.  This field is optional
			$strPost=$strPost . "&eMailMessage=Thank you so very much for your order.";
			
			// Billing Details:
			$strPost=$strPost . "&BillingFirstnames=" . $_SESSION['first_name'];
			$strPost=$strPost . "&BillingSurname=" . $_SESSION['last_name'];
			$strPost=$strPost . "&BillingAddress1=" . $this->libFunc->m_displayContent_sec($_SESSION['address1']);
			$strPost=$strPost . "&BillingAddress2=" . $this->libFunc->m_displayContent_sec($_SESSION['address2']);
			$strPost=$strPost . "&BillingCity=" . $_SESSION['city'];
			$strPost=$strPost . "&BillingPostCode=" . $this->libFunc->m_displayContent_sec($_SESSION['zip']);
			
			$this->obDb->query = "SELECT vShortname FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['bill_country_id']."'";
			$row_country = $this->obDb->fetchQuery();
			$billcountryname = $this->libFunc-> m_displayContent_sec($row_country[0]->vShortname);
			
			$strPost=$strPost . "&BillingCountry=".$billcountryname;
			
			#SETTING BILL STATENAME
		/*	if($this->libFunc->ifSet($_SESSION,'bill_state_id','0'))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$_SESSION['bill_state_id']."'";
				$row_state = $this->obDb->fetchQuery();
				$billstatename = $this->libFunc-> m_displayContent_sec($this->libFunc-> m_displayContent_sec($row_state[0]->vStateName));
			}
			else
			{
				$billstatename = $this->libFunc-> m_displayContent_sec($_SESSION['bill_state']);
			}
		*/	
		//	$strPost=$strPost . "&BillingState=" . $_SESSION['bill_state_id'];
			$strPost=$strPost . "&BillingPhone=" . $this->libFunc->m_displayContent_sec($_SESSION['phone']);
			
			if($_SESSION['alt_fName'] == ""){
				$_SESSION['alt_fName'] = $_SESSION['first_name'];
			}
			if($_SESSION['alt_lName'] == ""){
				$_SESSION['alt_lName'] = $_SESSION['last_name'];
			}
			if($_SESSION['alt_address1'] == ""){
				$_SESSION['alt_address1'] = $_SESSION['address1'];
			}
			if($_SESSION['alt_city'] == ""){
				$_SESSION['alt_city'] = $_SESSION['city'];
			}
			if($_SESSION['alt_zip'] == ""){
				$_SESSION['alt_zip'] = $_SESSION['zip'];
			}
			// Delivery Details:
			$strPost=$strPost . "&DeliveryFirstnames=" . $_SESSION['alt_fName'];
			$strPost=$strPost . "&DeliverySurname=" . $_SESSION['alt_lName'];
			$strPost=$strPost . "&DeliveryAddress1=" . $this->libFunc->m_displayContent($_SESSION['alt_address1']);
			$strPost=$strPost . "&DeliveryAddress2=" . "";
			$strPost=$strPost . "&DeliveryCity=" . $this->libFunc->m_displayContent_sec($_SESSION['alt_city']);
			$strPost=$strPost . "&DeliveryPostCode=" . $this->libFunc->m_displayContent_sec($_SESSION['alt_zip']);
			
			#SETTING SHIP COUNTRYNAME
			$this->obDb->query = "SELECT vShortname FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['ship_country_id']."'";
			$row_country = $this->obDb->fetchQuery();
			$shipcountryname = $this->libFunc-> m_displayContent_sec($row_country[0]->vShortname);
		
			$strPost=$strPost . "&DeliveryCountry=" . $this->libFunc->m_displayContent_sec($shipcountryname);
					
			#SETTING SHIP STATENAME
		/*	if($this->libFunc->ifSet($_SESSION,'ship_state_id','0'))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$_SESSION['ship_state_id']."'";
				$row_state = $this->obDb->fetchQuery();
				$shipstatename = $this->libFunc-> m_displayContent_sec($row_state[0]->vStateName);
			}
			else
			{
				$shipstatename = $this->libFunc-> m_displayContent_sec($_SESSION['ship_state']);
			}
		*/	
			//$strPost=$strPost . "&DeliveryState=" .$_SESSION['ship_state_id'];
			$strPost=$strPost . "&DeliveryPhone=" . $this->libFunc->m_displayContent($_SESSION['alt_phone']);
	
			/* Allow fine control over AVS/CV2 checks and rules by changing this value. 0 is Default 
			** It can be changed dynamically, per transaction, if you wish.  See the Server Protocol document */
			$strPost=$strPost . "&ApplyAVSCV2=0";
				
			/* Allow fine control over 3D-Secure checks and rules by changing this value. 0 is Default 
			** It can be changed dynamically, per transaction, if you wish.  See the Form Protocol document */
			$strPost=$strPost . "&Apply3DSecure=0";
			            
			// Encrypt the plaintext string for inclusion in the hidden field
			
			$strCrypt = $this->base64Encode($this->SimpleXor($strPost,$strEncryptionPassword));	
			$this->ObTpl->set_var("TPL_VAR_CRYPT",$strCrypt);
			
			$this->ObTpl->pparse("return","TPL_SAGEPAY_FILE");
	}#EF
# For sagepay

	function requestPost($url, $data){
	// Set a one-minute timeout for this script
	set_time_limit(60);

	// Initialise output variable
	$output = array();

	// Open the cURL session
	$curlSession = curl_init();

	// Set the URL
	curl_setopt ($curlSession, CURLOPT_URL, $url);
	// No headers, please
	curl_setopt ($curlSession, CURLOPT_HEADER, 0);
	// It's a POST request
	curl_setopt ($curlSession, CURLOPT_POST, 1);
	// Set the fields for the POST
	curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $data);
	// Return it direct, don't print it out
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1); 
	// This connection will timeout in 30 seconds
	curl_setopt($curlSession, CURLOPT_TIMEOUT,30); 
	//The next two lines must be present for the kit to work with newer version of cURL
	//You should remove them if you have any problems in earlier versions of cURL
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

	//Send the request and store the result in an array
	
	$rawresponse = curl_exec($curlSession);
	//Store the raw response for later as it's useful to see for integration and understanding 
	$_SESSION["rawresponse"]=$rawresponse;
	//Split response into name=value pairs
	$response = split(chr(10), $rawresponse);
	// Check that a connection was made
	if (curl_error($curlSession)){
		// If it wasn't...
		$output['Status'] = "FAIL";
		$output['StatusDetail'] = curl_error($curlSession);
	}

	// Close the cURL session
	curl_close ($curlSession);

	// Tokenise the response
	for ($i=0; $i<count($response); $i++){
		// Find position of first "=" character
		$splitAt = strpos($response[$i], "=");
		// Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
		$output[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
	} // END for ($i=0; $i<count($response); $i++)

	// Return the output
	return $output;
	

} // END function requestPost()
	

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
	

	function base64Encode($plain) 
	{
	  // Initialise output variable
	  $output = "";
	  // Do encoding
	  $output = base64_encode($plain);
	  // Return the result
	  return $output;
	}
# (END) SAGEPAY INTERGRATION 

	#FUNCTION TO PARSED HTTP BODY
	function PPHttpPost($methodName_, $nvpStr_)
	{
			// Set up your API credentials, PayPal end point, and API version.
			$API_UserName = urlencode(PAYPALAPI_USERNAME);
			$API_Password = urlencode(PAYPALAPI_PASSWORD);
			$API_Signature = urlencode(PAYPALAPI_SIGNATURE);
            $API_Endpoint = PAYPALAPI_ENDPOINT;
			
            $version = urlencode('51.0');
				 
			// Set the curl parameters.
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
			curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
			// Turn off the server and peer verification (TrustManager Concept).
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
		
			// Set the API operation, version, and API signature in the request.
			$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
			 
			// Set the request as a POST FIELD for curl.
			curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
			// Get response from the server.
			$httpResponse = curl_exec($ch);
		
			if(!$httpResponse) {
				exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
			}
		
			// Extract the response details.
			$httpResponseAr = explode("&", $httpResponse);
		
			$httpParsedResponseAr = array();
			foreach ($httpResponseAr as $i => $value) {
				$tmpAr = explode("=", $value);
				if(sizeof($tmpAr) > 1) {
					$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
				}
			}
		
			if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
				exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
			}
			return $httpParsedResponseAr;
	}#END FUNCTION 
	
	function m_submitPaypalDirect(){
		$obPayGateway= new c_paymentGateways();
		$paymentType = urlencode('Sale');				// or 'Sale'
		$firstName = urlencode($_SESSION['first_name']);
		$lastName = urlencode($_SESSION['last_name']);

		if ($_SESSION['cc_type']!=="SWITCH")
		{
			$_SESSION['cc_type'] = "Maestro";
		}elseif($_SESSION['cc_type']=="VISA" || $_SESSION['cc_type']=="DELTA" || $_SESSION['cc_type']=='UKE'){
			$_SESSION['cc_type'] = "Visa";			
		}elseif($_SESSION['cc_type']=="MC"){
			$_SESSION['cc_type']="MasterCard";
		} 

		$creditCardType = urlencode($_SESSION['cc_type']);
		$creditCardNumber = urlencode($_SESSION['cc_number']);
		$expDateMonth = $_SESSION['cc_month'];
		// Month must be padded with leading zero
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
		
		$expDateYear = urlencode($_SESSION['cc_year']);
		$cvv2Number = urlencode($_SESSION['cv2']);
		$address1 = urlencode($_SESSION['address1']);
		$address2 = urlencode($_SESSION['address2']);
		$city = urlencode($_SESSION['city']);
		$shipStateId=$this->libFunc->ifSet($_SESSION,'ship_state_id','0');
		$state = urlencode($this->m_stateName($shipStateId,$_SESSION['bill_state']));
		$zip = urlencode($_SESSION['zip']);
		
		$this->obDb->query = "SELECT vShortName FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['bill_country_id']."'";
		$bill_country = $this->obDb->fetchQuery();
				
		$country = urlencode($bill_country[0]->vShortName);				// US or other valid country code
		$amount = urlencode($this->payTotal);
		$currencyID = urlencode(PAYMENT_CURRENCY);							// or other currency ('GBP', 'EUR', 'JPY', 'CAD', 'AUD')
		
		// Add request-specific fields to the request string.
		$nvpStr =	"&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber".
					"&EXPDATE=$padDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName".
					"&STREET=$address1&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID";

		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $nvpStr);

		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{
			$obPayGateway->payStatus =1;
			$obPayGateway->transactionId = $httpParsedResponseAr["TRANSACTIONID"];
			//--
			if(isset($obPayGateway->transactionId))
	    		{
	    			$this->obDb->query= "UPDATE ".ORDERS." SET  	iOrderStatus=1,iPayStatus=1,iTransactionId='".$obPayGateway->transactionId."'   WHERE iOrderid_PK = '".$this->orderId."'";
	    			$rs = $this->obDb->updateQuery();

				}
	    		$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$this->orderId);
	    		$this->libFunc->m_mosRedirect($retUrl);
				}
			//--
		 else  {
                $_SESSION['paypaldirecterr']=1;
                $_SESSION['paypaldirectMsg']= $httpParsedResponseAr["L_SHORTMESSAGE0"].": ".$httpParsedResponseAr["L_LONGMESSAGE0"] ;
                $retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
	    		$this->libFunc->m_mosRedirect($retUrl);
	    }
		
	}

}#END CLASS
?>