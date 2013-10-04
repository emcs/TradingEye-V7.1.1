<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

ini_set('display_errors', "0");
error_reporting();
class c_receipt
{
	#CONSTRUCTOR
	function c_receipt()
	{
		$this->err				=0;
		$this->credit			=0;
		$this->solo				=0;
		$this->subTotal		=0;
		$this->volDiscount	=0;
		$this->grandTotal		=0;
		$this->postagePrice	=0;
		$this->totalQty		=0;
		$this->fileLink			="";
		$this->libFunc			=new c_libFunctions();
		$this->sessionId      =SESSIONID;
	}
	
	#FUNCTION TO DISPLAY ORDER PROCESS MESSAGE
	function m_orderProcessed()
	{
		if(!isset($this->request['mode']) || empty($this->request['mode']))
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}

		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->ObTpl=new template();

		$this->ObTpl->set_file("TPL_ORDER_FILE",$this->template);
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_ORDERSTATUS_BLK","orderstatus_blk");
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_BACKORDER_BLK","backorder_blk");

		$this->ObTpl->set_var("backorder_blk","");
		$this->ObTpl->set_var("orderstatus_blk","");
		#FLAG TO INDICATE SEPERATE BACKORDER AND NORMAL ORDER
		$_SESSION['backOrderSeperate']=$this->libFunc->ifSet($_SESSION,'backOrderSeperate','0');
		$this->obDb->query = "SELECT count(*) as cnt  FROM ".TEMPCART." WHERE  vSessionId='".$this->sessionId."'";
		$rowCount=$this->obDb->fetchQuery();

		if($_SESSION['backOrderSeperate']==1 && $rowCount[0]->cnt>0)
		{
			$this->ObTpl->parse("backorder_blk","TPL_BACKORDER_BLK");
		}
		else
		{			
			$this->ObTpl->parse("orderstatus_blk","TPL_ORDERSTATUS_BLK");
		}
		$backOrderUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.backorder");
		$this->ObTpl->set_var("TPL_VAR_BACKORDERURL",$backOrderUrl);

		$receiptUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.receipt&mode=".$this->request['mode']);
		$this->ObTpl->set_var("TPL_VAR_RECIEPTURL",$receiptUrl);
		return $this->ObTpl->parse("return","TPL_ORDER_FILE");
	}

	
	#FUNCTION TO RESEND INVOICES TO SUPPLIERS	
	function m_sendAutoOrder($invoice_no, $supplier_id)
	{
		
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->ObTpl=new template();
		
		
		$this->ObTpl->set_file("TPL_ORDERMAIL_SUPPLIER_FILE",MODULES_PATH."ecom/templates/main/supplierAutoMail.tpl.htm");
				

		#SETTING BLOCKS
		$this->ObTpl->set_block("TPL_ORDERMAIL_SUPPLIER_FILE","TPL_CART_BLK","cart_blk");
		$this->ObTpl->set_block("TPL_ORDERMAIL_SUPPLIER_FILE","TPL_DELIVERY_BLK","delivery_blk");
		
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAR_CARTPRODUCTS","cartproduct_blk");	
		$this->ObTpl->set_block("TPL_VAR_CARTPRODUCTS","TPL_KIT_BLK","kit_blk");		

		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
	
		
		#TPL_VAR_SUPPLIEREMAIL
		
		#INTAILAIZING
		$this->ObTpl->set_var("delivery_blk","");				
		$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING","");
		
		// [DRK][MODIFIED BY NSI]
		$this->ObTpl->set_var("TPL_VAR_COMPANY_DETAILS",$comFunc->m_mailFooter());
		// [/DRK]
		
		$receiptUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.receipt&mode=".$this->request['mode']);
		$this->ObTpl->set_var("TPL_VAR_RECIEPTURL",$receiptUrl);


		
		
		#QUERY ORDER TABLE
		$this->obDb->query = "SELECT iOrderid_PK,tmOrderDate,vPayMethod,vShipDescription,fShipTotal,";
		$this->obDb->query.= "vFirstName,vLastName,vEmail,vAddress1,vAddress2,vCity,iInvoice,";
		$this->obDb->query.= "vState,vStateName,vCountry,vZip,vCompany,vPhone,vHomepage,";
		$this->obDb->query.= "vAltName,vAltAddress1,vAltAddress2,vAltCity,vAltState,vAltCountry,";
		$this->obDb->query.= "vAltStateName,vAltZip,vAltPhone,fCodCharge,fPromoValue,";
		$this->obDb->query.= "vDiscountCode,fDiscount,iGiftcert_FK,fGiftcertTotal,fMemberPoints,";
		$this->obDb->query.= "fShipByWeightPrice,fShipByWeightKg,iSameAsBilling,vAuthCode,";
		$this->obDb->query.= "fTaxRate,fTaxPrice,tComments,vStatus,iPayStatus,fTotalPrice,iEarnedPoints,iCustomerid_FK";
		$this->obDb->query .= " FROM ".ORDERS." WHERE iInvoice= ".$invoice_no;
		
		
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		if($rCount!=1)
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
	

		$Name=$this->libFunc->m_displayContent($qryResult[0]->vFirstName)." ".$this->libFunc->m_displayContent($qryResult[0]->vLastName);
		
		
		if($rCount>0)
		{				
			$notPaid=array('mail','cod','cc_phone');	 #NOT PAID PAYMENT STATUS
			if(in_array($qryResult[0]->vPayMethod,$notPaid)){
				$payStatus='0';
			}else{
				$payStatus='1';
			}
			
			
			if($rCount>0)
			{
				$this->ObTpl->set_var("TPL_VAR_INVOICE",$qryResult[0]->iInvoice);	
				$this->ObTpl->set_var("TPL_VAR_ORDERDATE",$this->libFunc->dateFormat2($qryResult[0]->tmOrderDate));
							
				$this->ObTpl->set_var("TPL_VAR_ORDERSTATUS",ucfirst($qryResult[0]->vStatus));
				if(empty($qryResult[0]->tComments))
				{
					$this->ObTpl->set_var("TPL_VAR_COMMENTS","None");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_COMMENTS",$qryResult[0]->tComments);
				}

				if(isset($qryResult[0]->vState) && !empty($qryResult[0]->vState))
				{
					$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$qryResult[0]->vState."'";
					$row_state = $this->obDb->fetchQuery();
					$this->ObTpl->set_var("TPL_VAR_BILLSTATE",					$this->libFunc->m_displayContent($row_state[0]->vStateName));
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$qryResult[0]->vStateName);
				}
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$qryResult[0]->vCountry."'";
				$row_country = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));

				if(isset($qryResult[0]->vAltState) && !empty($qryResult[0]->vAltState))
				{
					$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$qryResult[0]->vAltState."'";
					$row_state = $this->obDb->fetchQuery();
					$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$this->libFunc->m_displayContent($row_state[0]->vStateName));
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$qryResult[0]->vAltStateName);
				}
			
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$qryResult[0]->vAltCountry."'";
				$row_country = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));
	
	
				$this->ObTpl->set_var("TPL_VAR_FIRSTNAME",$this->libFunc->m_displayContent($qryResult[0]->vFirstName));
				$this->ObTpl->set_var("TPL_VAR_LASTNAME",$this->libFunc->m_displayContent($qryResult[0]->vLastName));
				$this->ObTpl->set_var("TPL_VAR_COMPANY","(".$this->libFunc->m_displayContent($qryResult[0]->vCompany).")");
				$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($qryResult[0]->vEmail));
				$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($qryResult[0]->vAddress1));
				$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($qryResult[0]->vAddress2));
				$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($qryResult[0]->vCity));
				$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($qryResult[0]->vZip));
				$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($qryResult[0]->vCompany));
				$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($qryResult[0]->vPhone));
				$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",$this->libFunc->m_displayContent($qryResult[0]->vHomepage));
				$this->ObTpl->set_var("TPL_VAR_MPOINTS","");
	
								
				if($qryResult[0]->iSameAsBilling==1)
				{
					$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING",MSG_SAMEASBILLING);
				}
				else
				{
					$this->ObTpl->parse("delivery_blk","TPL_DELIVERY_BLK");
				}
				$this->ObTpl->set_var("TPL_VAR_ALTNAME",$this->libFunc->m_displayContent($qryResult[0]->vAltName));
				$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress1));
				$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress2));
				$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($qryResult[0]->vAltCity));
				
				$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($qryResult[0]->vAltZip));
				$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($qryResult[0]->vAltPhone));
			
				# Retrieve products list from order  : 
											
				$this->obDb->query = "SELECT iOrderProductid_PK,iProductid_FK,iVendorid_FK,iQty,iGiftwrapFK,fPrice,";
				$this->obDb->query.= "fDiscount,vTitle,vSku,vSupplierSku,iKit,tShortDescription,tSupplierDescription,seo_title,iTaxable,iFreeship,vPostageNotes ";
				$this->obDb->query .= " FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$qryResult[0]->iOrderid_PK."'";
				$rsOrderProduct=$this->obDb->fetchQuery();
				$rsOrderProductCount=$this->obDb->record_count;
								
				# Retrieve active suppliers list from DB			
				$this->obDb->query = "SELECT iVendorid_PK,vEmail,vCompany FROM ".SUPPLIERS." WHERE iStatus=1 AND iVendorid_PK = ".$supplier_id;
				$rowSupplier=$this->obDb->fetchQuery();
				$rowSupplierCount = $this->obDb->record_count;
							
				if($rsOrderProductCount>0 && $rowSupplierCount>0)
				{	
					
					for ($iSup=0; $iSup<$rowSupplierCount; $iSup++)			
					{												
						# INITIALIZING FOR BLOCKS						
						$this->ObTpl->set_var("cart_blk","");							
						$this->ObTpl->set_var("cartproduct_blk","");	
						//$this->ObTpl->set_var("TPL_VAR_SUPPLIEREMAIL",$rowSupplier[$iSup]->vEmail);	
						$this->ObTpl->set_var("TPL_VAR_SUPPLIER_NAME",$rowSupplier[$iSup]->vCompany);
																							
						$totalCount = 0;			
						for($i=0;$i<$rsOrderProductCount;$i++)
						{			
							
							if ($rowSupplier[$iSup]->iVendorid_PK == $rsOrderProduct[$i]->iVendorid_FK)		
							{
							$totalCount++;						
							# DIPSLAY THIS PRODUCT
							$this->ObTpl->set_var("TPL_VAR_OPTIONS","");
							$this->ObTpl->set_var("TPL_VAR_INC_VAT_PRICE","");
							$this->ObTpl->set_var("TPL_VAR_CHOICES","");
							$this->ObTpl->set_var("kit_blk","");	
							$this->ObTpl->set_var("TPL_VAR_NOTES","");	
							$this->price=0;
							$this->total=0;
						
							$comFunc->orderProductId=$rsOrderProduct[$i]->iOrderProductid_PK;
							$comFunc->qty=$rsOrderProduct[$i]->iQty;
							$comFunc->price=$this->price;
							$this->ObTpl->set_var("TPL_VAR_GIFTWRAP","");
							##GIFTWRAP URL
							if($rsOrderProduct[$i]->iGiftwrapFK!=0){
								$this->ObTpl->set_var("TPL_VAR_GIFTWRAP",$comFunc->m_dspGiftWrap($rsOrderProduct[$i]->iGiftwrapFK));
							}
					
							if($rsOrderProduct[$i]->iKit==1)
							{
								$this->obDb->query = "SELECT iKitItem_title,iProductid_FK FROM ".ORDERKITS." WHERE  iKitId='".$rsOrderProduct[$i]->iProductid_FK."' AND iProductOrderid_FK='".$rsOrderProduct[$i]->iOrderProductid_PK."'";
								$rsKit=$this->obDb->fetchQuery();
								$rsKitCount=$this->obDb->record_count;
		
								for($j=0;$j<$rsKitCount;$j++)
								{
									$comFunc->kitProductId=$rsKit[$j]->iProductid_FK;
									#GET CART OPTIONS
									$kitOptions=$comFunc->m_orderKitProductOptions();
									if($kitOptions==' ')
									{
										$this->ObTpl->set_var("TPL_VAR_KITOPTIONS","");
									}
									else
									{
										$this->ObTpl->set_var("TPL_VAR_KITOPTIONS",$kitOptions);
									}
									$this->ObTpl->set_var("TPL_VAR_KITTITLE",$this->libFunc->m_displayContent($rsKit[$j]->iKitItem_title));
									$this->ObTpl->parse("kit_blk","TPL_KIT_BLK",true);	
								}
							}
							else
							{
								#GET ORDERED PRODUCT OPTIONS
								$this->ObTpl->set_var("TPL_VAR_OPTIONS",$comFunc->m_orderProductOptions());
								#GET ORDERED PRODUCT CHOICES
								$this->ObTpl->set_var("TPL_VAR_CHOICES",$comFunc->m_orderProductChoices());
							}
		
							
							# ADDING POSTAGE AND PACKAGING PRICE FOR EACH PRODUCT 							
							if($rsOrderProduct[$i]->iFreeship == 1)
							{	
								$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","<em>".LBL_FREEPP."</em><br />");
							} else {								
								
								# QUERY TO GET vShipCode from PRODUCT TABLE
								$this->obDb->query = " SELECT vShipCode FROM ".PRODUCTS." WHERE iProdid_PK = ".$rsOrderProduct[$i]->iProductid_FK;
								$rowShipCode 	   =  $this->obDb->fetchQuery();
								$rowShipCodeCount  = $this->obDb->record_count;
								
								if ($rowShipCodeCount > 0 ){																		
									
									$this->obDb->query ="SELECT vField1,vField2,iPostDescId_PK  FROM  ".POSTAGEDETAILS." WHERE iPostId_FK=4 AND iPostDescId_PK = ".$rowShipCode[0]->vShipCode;									
									$rsPostage=$this->obDb->fetchQuery();
									$postageCnt=$this->obDb->record_count;
									if ($postageCnt>0){
										$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","<em> P&P ".CONST_CURRENCY.$rsPostage[0]->vField2."</em><br />");	
									}
															
								}								
							}
						
							# (OPTION And choice effected amount)
							$this->price=$comFunc->price;	
		
							#VOLUME DISCOUNT
							#DISCOUNT ACCORDING TO QTY
							$vDiscountPerCartElement=number_format(($rsOrderProduct[$i]->fDiscount),2);
							if($vDiscountPerCartElement>0)
							{
								$totalDiscountItem=$vDiscountPerCartElement*$rsOrderProduct[$i]->iQty;							
								$this->volDiscount=$this->volDiscount+$totalDiscountItem;
							}
							$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rsOrderProduct[$i]->seo_title;
							$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
		
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSku));
							$this->ObTpl->set_var("TPL_VAR_SUPPLIER_DES",$this->libFunc->m_displayContent($rsOrderProduct[$i]->tSupplierDescription));
							$this->ObTpl->set_var("TPL_VAR_SUPPLIER_SKU",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSupplierSku));
							
							$this->price=$this->price+$rsOrderProduct[$i]->fPrice;
							$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($this->price,2));
		
							#CHECK IF PRODUCT IS TAXABLE
							if ($rsOrderProduct[$i]->iTaxable == 1){
								
								$vatPercent=$this->libFunc->m_vatCalculate();
								$vatPriceNew=($vatPercent*$this->price)/100+$this->price;	
								$this->ObTpl->set_var("TPL_VAR_INC_VAT_PRICE", " ( ".CONST_CURRENCY.number_format($vatPriceNew,2)." Inc V.A.T ) ");
							} else {
								$this->ObTpl->set_var("TPL_VAR_INC_VAT_PRICE", "");
							}
		
							$this->ObTpl->set_var("TPL_VAR_QTY",$rsOrderProduct[$i]->iQty);
							$this->totalQty+=$rsOrderProduct[$i]->iQty;
							
							$this->ObTpl->parse("cartproduct_blk","TPL_VAR_CARTPRODUCTS",true);	
							} # END IF PRODUCTS' SUPPLIER_PK IS THE ONE NEED TO BE DISPLAYED		
																									
						} # END FOR $i - product list
						
					
						#END PRODUCT DISPLAY					
						$this->ObTpl->parse("cart_blk","TPL_CART_BLK");						
						
						#08/01/2008 
						#CREATE CONFIRMATION LINK FOR EACH SUPPLIER													
						$confirmation_url = SITE_URL."ecom/index.php?action=checkout.supplierConf&invoice=".$qryResult[0]->iInvoice."&supplier=".$rowSupplier[$iSup]->iVendorid_PK;						
						$this->ObTpl->set_var("TPL_VAR_CONFIRMATION_LINK",$confirmation_url);
						
						if ($totalCount > 0)
						{						
							$obMail = new htmlMimeMail();
							$obMail->setReturnPath(ADMIN_EMAIL);
							$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
												
							# CREATE ORDER EMAIL AND SEND TO SUPPLIER
							$obMail->setSubject("REMINDER FOR ORDER NUMBER : ".$qryResult[0]->iInvoice." -- ".$rowSupplier[$iSup]->vCompany);
							$obMail->setCrlf("\n"); //to handle mails in Outlook Express						
							$message=$this->ObTpl->parse("return","TPL_ORDERMAIL_SUPPLIER_FILE");
							$htmlcontent=$message; 						
							$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
							$obMail->setHtml($htmlcontent,$txtcontent);
				
							$obMail->buildMessage();
							$result = $obMail->send(array($rowSupplier[$iSup]->vEmail));		
													
							$timestamp=time();						
							# UPDATE SENT TIME 
							$this->obDb->query = " UPDATE ".CONFIRMATIONORDERS." SET tmLastSendDate = '".$timestamp."' WHERE iInvoice = ".$invoice_no." AND iVendorid_FK = ".$supplier_id;
							
							$this->obDb->updateQuery();							 
					
																								
						}
						$this->ObTpl->clear_var("cartproduct_blk");	
						$this->ObTpl->clear_var("cart_blk");
						
					}# END FOR $iSup - supplier list	
				
				}# END IF $rsOrderProductCount>0 && $rowSupplierCount>0		
		   }#END ORDERS IF $rCount>0														
		} # END OF IF $rCount>0 && $qryResult[0]->iPayStatus==0		
		
			
	} # FUNCTION ENDED
	
	
	
	#FUNCTION TO RESEND INVOICES TO SUPPLIERS
	function m_supplierResendOrder()
	{
				
		if(!isset($this->request['invoice']) || empty($this->request['invoice']))
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}	
		
		if(!isset($this->request['supplier']) || empty($this->request['supplier']))
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}	
								
		$this->m_sendAutoOrder($this->request['invoice'],$this->request['supplier']);

		$this->ObTpl=new template();			
		$this->ObTpl->set_file("TPL_ORDERMAIL_SUPPLIER_FILE",MODULES_PATH."ecom/templates/main/supplierMailSent.tpl.htm");				
		return $this->ObTpl->parse("return","TPL_ORDERMAIL_SUPPLIER_FILE");
	}
	
		
	/* Method: m_sendSupplierOrderDetails
	 * This method is to send email to all supplier based on product ordered.(Drop Feature)
	 * his function should only be used once "Drop ship" feature is enable from Admin.
	 * 
	 * @return: Nothing
	 * @param:  Nothing
	 * @author: Dave Bui
	 */
		
	function m_sendSupplierOrderDetails($row='')
	{
			
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->ObTpl=new template();
			
		$this->ObTpl->set_file("TPL_ORDERMAIL_SUPPLIER_FILE",MODULES_PATH."ecom/templates/main/supplierMail.tpl.htm");
				

		#SETTING BLOCKS
		$this->ObTpl->set_block("TPL_ORDERMAIL_SUPPLIER_FILE","TPL_CART_BLK","cart_blk");
		$this->ObTpl->set_block("TPL_ORDERMAIL_SUPPLIER_FILE","TPL_DELIVERY_BLK","delivery_blk");
		
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAR_CARTPRODUCTS","cartproduct_blk");	
		$this->ObTpl->set_block("TPL_VAR_CARTPRODUCTS","TPL_KIT_BLK","kit_blk");		

		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
	
	
		
		#INTAILAIZING
		$this->ObTpl->set_var("delivery_blk","");				
		$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING","");
		
		$this->ObTpl->set_var("TPL_VAR_COMPANY_DETAILS",$comFunc->m_mailFooter());
	
		
		$receiptUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.receipt&mode=".$this->request['mode']);
		$this->ObTpl->set_var("TPL_VAR_RECIEPTURL",$receiptUrl);

		#QUERY ORDER TABLE
		$this->obDb->query = "SELECT tmOrderDate,vPayMethod,vShipDescription,fShipTotal,";
		$this->obDb->query.= "vFirstName,vLastName,vEmail,vAddress1,vAddress2,vCity,iInvoice,";
		$this->obDb->query.= "vState,vStateName,vCountry,vZip,vCompany,vPhone,vHomepage,";
		$this->obDb->query.= "vAltName,vAltAddress1,vAltAddress2,vAltCity,vAltState,vAltCountry,";
		$this->obDb->query.= "vAltStateName,vAltZip,vAltPhone,fCodCharge,fPromoValue,";
		$this->obDb->query.= "vDiscountCode,fDiscount,iGiftcert_FK,fGiftcertTotal,fMemberPoints,";
		$this->obDb->query.= "fShipByWeightPrice,fShipByWeightKg,iSameAsBilling,vAuthCode,";
		$this->obDb->query.= "fTaxRate,fTaxPrice,tComments,vStatus,iPayStatus,fTotalPrice,iEarnedPoints,iCustomerid_FK";
		$this->obDb->query .= " FROM ".ORDERS." WHERE iOrderid_PK='".$this->request['mode']."'";
		
		if(isset($_SESSION['userid']) && !empty($_SESSION['userid'])){
			$this->obDb->query .= " AND iCustomerid_FK='".$_SESSION['userid']."'";
		}else{
			$this->obDb->query .= " AND vEmail='".$_SESSION['email']."'";
		}
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;

		$timestamp = time();
		#INSERTING RECORDS TO CONFIRMORDER TABLE
		foreach($row as $k=>$v){
			$this->obDb->query ="INSERT INTO ".CONFIRMATIONORDERS." SET ";
			$this->obDb->query.="iInvoice		='".$qryResult[0]->iInvoice."',";
			$this->obDb->query.="tmLastSendDate	='".$timestamp."',";
			$this->obDb->query.="iVendorid_FK	='".$row[$k]->iVendorid_FK."'";
			$this->obDb->updateQuery();
		}
		
		if($rCount!=1)
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
	

		$Name=$this->libFunc->m_displayContent($qryResult[0]->vFirstName)." ".$this->libFunc->m_displayContent($qryResult[0]->vLastName);
	
		if($rCount>0) 
		{
				
				
			$this->ObTpl->set_var("TPL_VAR_INVOICE",$qryResult[0]->iInvoice);	
			$this->ObTpl->set_var("TPL_VAR_ORDERDATE",$this->libFunc->dateFormat2($qryResult[0]->tmOrderDate));
						
			$this->ObTpl->set_var("TPL_VAR_ORDERSTATUS",ucfirst($qryResult[0]->vStatus));
			if(empty($qryResult[0]->tComments))
			{
				$this->ObTpl->set_var("TPL_VAR_COMMENTS","None");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_COMMENTS",$qryResult[0]->tComments);
			}

			if(isset($qryResult[0]->vState) && !empty($qryResult[0]->vState))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$qryResult[0]->vState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$qryResult[0]->vStateName);
			}
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$qryResult[0]->vCountry."'";
			$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",
			$this->libFunc->m_displayContent($row_country[0]->vCountryName));

			if(isset($qryResult[0]->vAltState) && !empty($qryResult[0]->vAltState))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$qryResult[0]->vAltState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",	$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$qryResult[0]->vAltStateName);
			}
		
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$qryResult[0]->vAltCountry."'";
			$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
			$this->libFunc->m_displayContent($row_country[0]->vCountryName));	

			$this->ObTpl->set_var("TPL_VAR_FIRSTNAME",$this->libFunc->m_displayContent($qryResult[0]->vFirstName));
			$this->ObTpl->set_var("TPL_VAR_LASTNAME",$this->libFunc->m_displayContent($qryResult[0]->vLastName));
			$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($qryResult[0]->vEmail));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($qryResult[0]->vAddress1));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($qryResult[0]->vAddress2));
			$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($qryResult[0]->vCity));
			$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($qryResult[0]->vZip));
			$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($qryResult[0]->vCompany));
			$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($qryResult[0]->vPhone));
			$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",$this->libFunc->m_displayContent($qryResult[0]->vHomepage));
			$this->ObTpl->set_var("TPL_VAR_MPOINTS","");

							
			if($qryResult[0]->iSameAsBilling==1)
			{
				$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING",MSG_SAMEASBILLING);
			}
			else
			{
				$this->ObTpl->parse("delivery_blk","TPL_DELIVERY_BLK");
			}
			$this->ObTpl->set_var("TPL_VAR_ALTNAME",$this->libFunc->m_displayContent($qryResult[0]->vAltName));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress1));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress2));
			$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($qryResult[0]->vAltCity));
			
			$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($qryResult[0]->vAltZip));
			$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($qryResult[0]->vAltPhone));
		
			# Retrieve products list from order  :
			$this->obDb->query = "SELECT iOrderProductid_PK,iProductid_FK,iVendorid_FK,iQty,iGiftwrapFK,fPrice,";
			$this->obDb->query.= "fDiscount,vTitle,vSku,vSupplierSku,iKit,tShortDescription,tSupplierDescription,seo_title,iTaxable,iFreeship,vPostageNotes ";
			$this->obDb->query .= " FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['mode']."'";
			 
			$rsOrderProduct=$this->obDb->fetchQuery();
			$rsOrderProductCount=$this->obDb->record_count;
			
			
			# Retrieve active suppliers list from DB			
			$this->obDb->query = "SELECT iVendorid_PK,vEmail,vCompany FROM ".SUPPLIERS." WHERE iStatus=1";
			$rowSupplier=$this->obDb->fetchQuery();
			$rowSupplierCount = $this->obDb->record_count;
						
								
			if($rsOrderProductCount>0 && $rowSupplierCount>0)
			{	
				$comFunc->orderId=$this->request['mode'];
			
				for ($iSup=0; $iSup<$rowSupplierCount; $iSup++)			
				{												
					# INITIALIZING FOR BLOCKS						
					$this->ObTpl->set_var("cart_blk","");							
					$this->ObTpl->set_var("cartproduct_blk","");	
					$this->ObTpl->set_var("TPL_VAR_SUPPLIEREMAIL",$rowSupplier[$iSup]->vEmail);												
					
					# INITIALIZING FOR SUPPLIER COUNT
					$totalCount = 0;			
					
					for($i=0;$i<$rsOrderProductCount;$i++)
					{			
						
						if ($rowSupplier[$iSup]->iVendorid_PK == $rsOrderProduct[$i]->iVendorid_FK)		
						{						
						$totalCount++;						
						# DIPSLAY THIS PRODUCT
						$this->ObTpl->set_var("TPL_VAR_OPTIONS","");
						$this->ObTpl->set_var("TPL_VAR_CHOICES","");
						$this->ObTpl->set_var("kit_blk","");	
						$this->ObTpl->set_var("TPL_VAR_NOTES","");	
						$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","");
						$this->ObTpl->set_var("TPL_VAR_INC_VAT_PRICE", "");
						
						$this->price=0;
						$this->total=0;
					
						$comFunc->orderProductId=$rsOrderProduct[$i]->iOrderProductid_PK;
						$comFunc->qty=$rsOrderProduct[$i]->iQty;
						$comFunc->price=$this->price;
						$this->ObTpl->set_var("TPL_VAR_GIFTWRAP","");
						##GIFTWRAP URL
						if($rsOrderProduct[$i]->iGiftwrapFK!=0){
							$this->ObTpl->set_var("TPL_VAR_GIFTWRAP",$comFunc->m_dspGiftWrap($rsOrderProduct[$i]->iGiftwrapFK));
						}
				
						if($rsOrderProduct[$i]->iKit==1)
						{
							$this->obDb->query = "SELECT iKitItem_title,iProductid_FK FROM ".ORDERKITS." WHERE  iKitId='".$rsOrderProduct[$i]->iProductid_FK."' AND iProductOrderid_FK='".$rsOrderProduct[$i]->iOrderProductid_PK."'";
							$rsKit=$this->obDb->fetchQuery();
							$rsKitCount=$this->obDb->record_count;
	
							for($j=0;$j<$rsKitCount;$j++)
							{
								$comFunc->kitProductId=$rsKit[$j]->iProductid_FK;
								#GET CART OPTIONS
								$kitOptions=$comFunc->m_orderKitProductOptions();
								if($kitOptions==' ')
								{
									$this->ObTpl->set_var("TPL_VAR_KITOPTIONS","");
								}
								else
								{
									$this->ObTpl->set_var("TPL_VAR_KITOPTIONS",$kitOptions);
								}
								$this->ObTpl->set_var("TPL_VAR_KITTITLE",$this->libFunc->m_displayContent($rsKit[$j]->iKitItem_title));
								$this->ObTpl->parse("kit_blk","TPL_KIT_BLK",true);	
							}
						}
						else
						{
							#GET ORDERED PRODUCT OPTIONS
							$this->ObTpl->set_var("TPL_VAR_OPTIONS",$comFunc->m_orderProductOptions());
							#GET ORDERED PRODUCT CHOICES
							$this->ObTpl->set_var("TPL_VAR_CHOICES",$comFunc->m_orderProductChoices());
						}
													
					
						if($rsOrderProduct[$i]->iFreeship == 1)
						{	
							$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","<em>".LBL_FREEPP."</em><br />");
						} 
						
						
						# (OPTION And choice effected amount)
						$this->price=$comFunc->price;	
	
						#VOLUME DISCOUNT
						#DISCOUNT ACCORDING TO QTY
						$vDiscountPerCartElement=number_format(($rsOrderProduct[$i]->fDiscount),2);
						if($vDiscountPerCartElement>0)
						{
							$totalDiscountItem=$vDiscountPerCartElement*$rsOrderProduct[$i]->iQty;							
							$this->volDiscount=$this->volDiscount+$totalDiscountItem;
						}
						$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rsOrderProduct[$i]->seo_title;
						$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
	
						$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vTitle));
						$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSku));
						$this->ObTpl->set_var("TPL_VAR_SUPPLIER_DES",$this->libFunc->m_displayContent($rsOrderProduct[$i]->tSupplierDescription));
						$this->ObTpl->set_var("TPL_VAR_SUPPLIER_SKU",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSupplierSku));
						
						$this->price=$this->price+$rsOrderProduct[$i]->fPrice;
						$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($this->price,2));
													
						#CHECK IF PRODUCT IS TAXABLE
						if ($rsOrderProduct[$i]->iTaxable == 1){
							
							$vatPercent=$this->libFunc->m_vatCalculate();
							$vatPriceNew=($vatPercent*$this->price)/100+$this->price;
							$this->ObTpl->set_var("TPL_VAR_INC_VAT_PRICE", " ( ".CONST_CURRENCY.number_format($vatPriceNew,2)." Inc V.A.T ) ");
						} else {
							$this->ObTpl->set_var("TPL_VAR_INC_VAT_PRICE", "");
						}
								
						$this->ObTpl->set_var("TPL_VAR_QTY",$rsOrderProduct[$i]->iQty);
						$this->totalQty+=$rsOrderProduct[$i]->iQty;
						
						$this->ObTpl->parse("cartproduct_blk","TPL_VAR_CARTPRODUCTS",true);	
						} # END IF PRODUCTS' SUPPLIER_PK IS THE ONE NEED TO BE DISPLAYED		
						
																	
					} # END FOR $i - product list
					
					#END PRODUCT DISPLAY					
					$this->ObTpl->parse("cart_blk","TPL_CART_BLK");						
					
					 
					#CREATE CONFIRMATION LINK FOR EACH SUPPLIER																		
					$confirmation_url = SITE_URL."ecom/index.php?action=checkout.supplierConf&invoice=".$qryResult[0]->iInvoice."&supplier=".$rowSupplier[$iSup]->iVendorid_PK;						
					$this->ObTpl->set_var("TPL_VAR_CONFIRMATION_LINK",$confirmation_url);
			
					if ($totalCount > 0)
					{
						
						$obMail = new htmlMimeMail();					
						$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
											
						# CREATE ORDER EMAIL AND SEND TO SUPPLIER
						$obMail->setSubject("ORDER NUMBER : ".$qryResult[0]->iInvoice." FOR ".$rowSupplier[$iSup]->vCompany);
						$obMail->setCrlf("\n"); //to handle mails in Outlook Express						
						$message=$this->ObTpl->parse("return","TPL_ORDERMAIL_SUPPLIER_FILE");
						$htmlcontent=$message; 						
						$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
						$obMail->setHtml($htmlcontent,$txtcontent);
											
						# YOU COULD ADD ATTACHEMENT TO THE EMAIL INVOICE IF YOU KNOW HOW TO MODIFY THE CODE
												
						$obMail->buildMessage();					
						$result = $obMail->send(array($rowSupplier[$iSup]->vEmail));																													
					}
					$this->ObTpl->clear_var("cartproduct_blk");	
					$this->ObTpl->clear_var("cart_blk");					
				}# END FOR $iSup - supplier list	
			
			}# END IF $rsOrderProductCount>0 && $rowSupplierCount>0
							   														
		} # END OF IF $rCount>0 && $qryResult[0]->iPayStatus==0		
	
	}# END FUNCTION
	
		
	
	
	
	#FUNCTION TO CONFIRM THAT SUPPLIER ALREADY RECEIVE THE ORDER
	/*
	 * This function should only be used once "Drop ship" feature is enable from Admin.
	 */ 
	 
	function m_supplierOrderConf()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->ObTpl=new template();
		
		if(!isset($this->request['invoice']) || empty($this->request['invoice']))
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
		if(!isset($this->request['supplier']) || empty($this->request['supplier']))
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
		
		
		$this->ObTpl->set_file("TPL_SUPL_CONF_FILE",$this->receiptTemplate);
		$this->ObTpl->set_block("TPL_SUPL_CONF_FILE","TPL_THANKS_BLK","thanks_blk");		
		$this->ObTpl->set_var("thanks_blk","");
		
		$this->ObTpl->set_var("TPL_VAR_INVOICE",$this->request['invoice']);
		

		$this->obDb->query = " SELECT * FROM ".CONFIRMATIONORDERS." WHERE iInvoice = ".$this->request['invoice']." 
							   AND iVendorid_FK = ".$this->request['supplier'];
		$rows = $this->obDb->fetchQuery();
		
		$total_record = $this->obDb->record_count;
		
		if ($total_record == 0){
			$this->ObTpl->set_var("TPL_VAR_ERROR_MESSAGE","Sorry ! Invoice (".$this->request['invoice'].") or Supplier (".$this->request['supplier'].") has not been recognised.");
		}
		elseif ($rows[0]->status == "Confirmed"){		
			$this->ObTpl->set_var("TPL_VAR_ERROR_MESSAGE"," This invoice number ".$this->request['invoice']." was already confirmed by you.");			
		}else {
			$this->ObTpl->set_var("TPL_VAR_ERROR_MESSAGE","");
			$this->ObTpl->parse("thanks_blk","TPL_THANKS_BLK");
		}
		
		
		$timestamp = time();
		$this->obDb->query = " UPDATE ".CONFIRMATIONORDERS." SET 
							   status = 'Confirmed', tmLastSendDate = '".$timestamp."' WHERE iInvoice = '".$this->request['invoice']."' 
							   AND iVendorid_FK = ".$this->request['supplier'];
		$this->obDb->updateQuery();
		
		return $this->ObTpl->parse("return","TPL_SUPL_CONF_FILE");		
	}
	
	#FUNCTION TO SEND ORDER DETAILS TO ADMIN AND CUSTOMER
	function m_sendOrderDetails($themode=0)
	{   //die('yahoo');
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->ObTpl=new template();
		if(!empty($themode))
		{
			$mode = $themode;
		}elseif(isset($this->request['mode']) || !empty($this->request['mode']))
		{
			$mode = $this->request['mode'];
		}
		else
		{
			trigger_error("Mode check failed" . $mode);
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}

		$_SESSION['payMethod']=$this->libFunc->ifSet($_SESSION,"payMethod");
		$this->ObTpl->set_file("TPL_ORDERMAIL_FILE",MODULES_PATH."ecom/templates/main/orderMail.tpl.htm");
	

		#SETTING BLOCKS
		$this->ObTpl->set_block("TPL_ORDERMAIL_FILE","TPL_CART_BLK","cart_blk");
		$this->ObTpl->set_block("TPL_ORDERMAIL_FILE","TPL_DELIVERY_BLK","delivery_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAR_CARTPRODUCTS","cartproduct_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_GIFTCERT_BLK","giftcert_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_DISCOUNT_BLK","discount_blk");
		$this->ObTpl->set_block("TPL_VAR_CARTPRODUCTS","TPL_KIT_BLK","kit_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_COD_BLK","cod_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_PROMODISCOUNTS_BLK","promodiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VOLDISCOUNTS_BLK","volDiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_POSTAGE_BLK","postage_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_MPOINTS_BLK","memberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_EMPOINTS_BLK","earnedmemberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_TMPOINTS_BLK","totalmemberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_CARTWEIGHT_BLK","cartWeight_blk");	
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAT_BLK","vat_blk");

		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT","");
		
		#INTAILAIZING
		$this->ObTpl->set_var("delivery_blk","");	
		$this->ObTpl->set_var("cart_blk","");	
		$this->ObTpl->set_var("cartWeight_blk","");	
		$this->ObTpl->set_var("giftcert_blk","");	
		$this->ObTpl->set_var("discount_blk","");	
		$this->ObTpl->set_var("cartproduct_blk","");	
		$this->ObTpl->set_var("kit_blk","");	
		$this->ObTpl->set_var("promodiscounts_blk","");	
		$this->ObTpl->set_var("volDiscounts_blk","");	
		$this->ObTpl->set_var("postage_blk","");		
		$this->ObTpl->set_var("cod_blk","");	
		$this->ObTpl->set_var("memberpoint_blk","");
		$this->ObTpl->set_var("earnedmemberpoint_blk","");
		$this->ObTpl->set_var("totalmemberpoint_blk","");
		$this->ObTpl->set_var("vat_blk","");

		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING","");
				
		$this->ObTpl->set_var("TPL_VAR_COMPANY_DETAILS",$comFunc->m_mailFooter());
		

		$receiptUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.receipt&mode=".$mode);
		$this->ObTpl->set_var("TPL_VAR_RECIEPTURL",$receiptUrl);

		#QUERY ORDER TABLE
		$this->obDb->query = "SELECT tmOrderDate,vPayMethod,vShipDescription,fShipTotal,";
		$this->obDb->query.= "vFirstName,vLastName,vEmail,vAddress1,vAddress2,vCity,iInvoice,";
		$this->obDb->query.= "vState,vStateName,vCountry,vZip,vCompany,vPhone,vHomepage,";
		$this->obDb->query.= "vAltCompany,vAltName,vAltAddress1,vAltAddress2,vAltCity,vAltState,vAltCountry,";
		$this->obDb->query.= "vAltStateName,vAltZip,vAltPhone,fCodCharge,fPromoValue,";
		$this->obDb->query.= "vDiscountCode,fDiscount,iGiftcert_FK,fGiftcertTotal,fMemberPoints,";
		$this->obDb->query.= "fShipByWeightPrice,fShipByWeightKg,iSameAsBilling,vAuthCode,";
		$this->obDb->query.= "fTaxRate,fTaxPrice,tComments,vStatus,iPayStatus,fTotalPrice,iEarnedPoints,vSessionid,iCustomerid_FK";
		$this->obDb->query .= " FROM ".ORDERS." WHERE iOrderid_PK='".$mode."'";
		
		if(isset($_SESSION['userid']) && !empty($_SESSION['userid'])){
			$this->obDb->query .= " AND iCustomerid_FK='".$_SESSION['userid']."'";
		}
		
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
			
		if($rCount!=1)
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}

		#TO VALIDATE SECPAY
		if($qryResult[0]->vPayMethod=='secpay'){
			$this->sessionId=$this->request['sessionFld'];
			if(!$this->m_validateSecpay()){
				return "SECPay Message: ".$this->request['message'];
			}
		}elseif($qryResult[0]->vPayMethod=='worldpay'){
			$this->sessionId=$this->request['M_sessionid'];
			if(!$this->m_validateWorldpay()){
				return "Worldpay Message: ".$this->request['message'];
			}
		}elseif(isset($qryResult[0]->vSessionid) && !empty($qryResult[0]->vSessionid)){
			$this->sessionId=$qryResult[0]->vSessionid;
		}
		//error_log($this->sessionId."\n",3,SITE_PATH."paypal.log");
	   
		$Name=$this->libFunc->m_displayContent($qryResult[0]->vFirstName)." ".$this->libFunc->m_displayContent($qryResult[0]->vLastName);
		
		//echo "<pre>";print_r($qryResult);
		//echo 'Factor='.($rCount>0 && ($qryResult[0]->iPayStatus==0 || (($qryResult[0]->vPayMethod=='barclay' || $qryResult[0]->vPayMethod=='cs_redirect') && $qryResult[0]->iPayStatus==1)));
		//echo $qryResult[0]->vEmail;
		//exit;
		if($rCount>0)
		{
			$payStatus=(string) $qryResult[0]->iPayStatus;
			$notPaid=array('mail','cod','cc_phone');	 #NOT PAID PAYMENT STATUS
			if(in_array($qryResult[0]->vPayMethod,$notPaid)){
				$payStatus='0';
			}
			if($qryResult[0]->vPayMethod == "paypal_exp" && $qryResult[0]->iPayStatus == 1){
				$this->sessionId = SESSIONID;
			}
			//Temp fix to disable the ability of someone simply visiting this url and setting order as paid (paypal only, as each pay method is updated, add it here)
			//elseif($qryResult[0]->vPayMethod != 'paypal' && $qryResult[0]->vPayMethod != 'sagepayform' && $qryResult[0]->vPayMethod != 'paypaldirect' && $qryResult[0]->vPayMethod != 'Cardsave' && $qryResult[0]->vPayMethod != 'barclay')
			//{
			//	$payStatus='1';
			//}
			$memberPointsEarned=$qryResult[0]->iEarnedPoints;
			$usedMemberPoints=$qryResult[0]->fMemberPoints/MPOINTVALUE;
			//if($qryResult[0]->iEarnedPoints > 0 &//& $memberPointsEarned == 0 && $qryResult[0]->iPayStatus == 1)
			//{
			//	$memberPointsEarned = $qryResult[0]->iEarnedPoints;
			//}
			$this->obDb->query ="SELECT fMemberPoints FROM ".CUSTOMERS." WHERE iCustmerid_PK='".$qryResult[0]->iCustomerid_FK."'";
			$result = $this->obDb->fetchQuery();
			$currentpoints = $result[0]->fMemberPoints;
			#UPDATING MEMBER POINTS-ADDING EARNED AND SUBTRACTING USED
			$mPoints=$currentpoints + $memberPointsEarned-ceil($usedMemberPoints);
			//error_log("End result:".$mPoints."=".$currentpoints." + ".$memberPointsEarned." - ".$usedMemberPoints,3,SITE_PATH."mpoint.log");
			$this->obDb->query ="UPDATE ".CUSTOMERS." SET fMemberPoints='".$mPoints."' WHERE iCustmerid_PK='".$qryResult[0]->iCustomerid_FK."'";
			
			
			$this->obDb->updateQuery();

			#DELETEING TEMPERARY DATA************************************
			$this->m_deleteTemp();
			#**********************************************************

			

			#MODIFIED BY NSI - 17-04-2007(ADDED NEW FIELD in query vSessionId)
			$this->obDb->query ="UPDATE ".ORDERS." SET vSessionId='".$this->sessionId."',";
			$this->obDb->query.="iPayStatus='".$payStatus."',iOrderStatus='1' ";
			
			if($qryResult[0]->vPayMethod=='paypal')
			{
				$this->obDb->query.=",iTransactionId='".$this->request['txn_id']."' ";
			}elseif($qryResult[0]->vPayMethod=='worldpay'){
				#07-05-07
				$this->obDb->query.=",iTransactionId='".$this->request['transId']."' ";
			}
			$this->obDb->query.=" WHERE iOrderid_PK = '".$mode."'";
			$rs = $this->obDb->updateQuery();
			$time=time();
			$receiptUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.receipt&mode=".$mode);
			$adminUrl=SITE_URL."admin/";
			$this->ObTpl->set_var("TPL_VAR_NAME",$Name);
		//echo $rCount;
		//die('in function m_sendOrderDetails');
		if($rCount>0)
		{	
			$this->ObTpl->set_var("TPL_VAR_INVOICE",$qryResult[0]->iInvoice);
			$_SESSION['google']['id'] = $qryResult[0]->iInvoice;	
			$this->ObTpl->set_var("TPL_VAR_ORDERDATE",$this->libFunc->dateFormat2($qryResult[0]->tmOrderDate));
			if($qryResult[0]->vPayMethod=='cod')
			{
				$vPayMethod=	$comFunc->m_paymentMethod($qryResult[0]->vPayMethod,$qryResult[0]->fCodCharge);
			}
			else
			{
				$vPayMethod=	$comFunc->m_paymentMethod($qryResult[0]->vPayMethod);
			}
			
			$this->ObTpl->set_var("TPL_VAR_PAYMENTMETHOD",$vPayMethod);
			$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$qryResult[0]->vShipDescription);
			$this->ObTpl->set_var("TPL_VAR_ORDERSTATUS",ucfirst($qryResult[0]->vStatus));
			if(empty($qryResult[0]->tComments))
			{
				$this->ObTpl->set_var("TPL_VAR_COMMENTS","None");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_COMMENTS",$qryResult[0]->tComments);
			}

			if(isset($qryResult[0]->vState) && !empty($qryResult[0]->vState))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$qryResult[0]->vState."'";
				$row_state = $this->obDb->fetchQuery();
				$_SESSION['google']['state'] = $this->libFunc->m_displayContent($row_state[0]->vStateName);
				$this->ObTpl->set_var("TPL_VAR_BILLSTATE",					$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$qryResult[0]->vStateName);
				$_SESSION['google']['state'] = $qryResult[0]->vStateName;
			}
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$qryResult[0]->vCountry."'";
			$row_country = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",$this->libFunc->m_displayContent($row_country[0]->vCountryName));
			$_SESSION['google']['country'] = $this->libFunc->m_displayContent($row_country[0]->vCountryName);

			if(isset($qryResult[0]->vAltState) && !empty($qryResult[0]->vAltState))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$qryResult[0]->vAltState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",					$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$qryResult[0]->vAltStateName);
			}
			
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$qryResult[0]->vAltCountry."'";
			$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
			$this->libFunc->m_displayContent($row_country[0]->vCountryName));


			$this->ObTpl->set_var("TPL_VAR_FIRSTNAME",$this->libFunc->m_displayContent($qryResult[0]->vFirstName));
			$this->ObTpl->set_var("TPL_VAR_LASTNAME",$this->libFunc->m_displayContent($qryResult[0]->vLastName));
			$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($qryResult[0]->vEmail));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($qryResult[0]->vAddress1));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($qryResult[0]->vAddress2));
			$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($qryResult[0]->vCity));
			$_SESSION['google']['city'] = $this->libFunc->m_displayContent($qryResult[0]->vCity);
			$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($qryResult[0]->vZip));
			$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($qryResult[0]->vCompany));
			$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($qryResult[0]->vPhone));
			$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",$this->libFunc->m_displayContent($qryResult[0]->vHomepage));
			$this->ObTpl->set_var("TPL_VAR_MPOINTS","");

			

			if($this->libFunc->ifSet($_SESSION,"cssSelectedFile",""))
			{
				$this->ObTpl->set_var("TPL_VAR_CSSFILE",trim($_SESSION['cssSelectedFile']));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_CSSFILE",trim(DEFAULT_CSS));
			}


			$this->ObTpl->set_var("TPL_VAR_CSSFILE","");
		
			if($qryResult[0]->iSameAsBilling==1)
			{
				$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING",MSG_SAMEASBILLING);
			}
			else
			{
				$this->ObTpl->parse("delivery_blk","TPL_DELIVERY_BLK");
			}
			
			$this->ObTpl->set_var("TPL_VAR_ALTCOMPANY",$this->libFunc->m_displayContent($qryResult[0]->vAltCompany));
			$this->ObTpl->set_var("TPL_VAR_ALTNAME",$this->libFunc->m_displayContent($qryResult[0]->vAltName));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress1));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress2));
			$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($qryResult[0]->vAltCity));
			
			$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($qryResult[0]->vAltZip));
			$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($qryResult[0]->vAltPhone));
		
			$this->obDb->query = "SELECT iOrderProductid_PK,iProductid_FK,iQty,iGiftwrapFK,fPrice,";
			$this->obDb->query.= "fDiscount,vTitle,vSku,iKit,tShortDescription,seo_title,iTaxable,iFreeship,vPostageNotes ";
			$this->obDb->query .= " FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$mode."'";
			$rsOrderProduct=$this->obDb->fetchQuery();
			$rsOrderProductCount=$this->obDb->record_count;
			foreach($rsOrderProduct as $key=>$value){
				$this->obDb->query = "SELECT vDownloadablefile FROM ".PRODUCTS." WHERE iProdid_PK = '".$rsOrderProduct[$key]->iProductid_FK."'";
				$downloadProduct=$this->obDb->fetchQuery();
				if(isset($downloadProduct[0]->vDownloadablefile)){
					$rsOrderProduct[$key]->vDownloadablefile = $rsOrderProduct[$key]->iProductid_FK;
				}
			}
			if($rsOrderProductCount>0)
				{
					
					$id_rows = array();
					for ($iSup=0; $iSup<$rsOrderProductCount; $iSup++ )
					{
						$id_rows[$iSup] = $rsOrderProduct[$iSup]->iProductid_FK;
					}
					
					
					
					#GETTING SUPPLIERS FROM PRODUCT TABLE
					$this->obDb->query = " SELECT distinct iVendorid_FK FROM ".PRODUCTS.
										 " WHERE iVendorid_FK>0 AND iProdid_PK IN (" . implode(",", $id_rows). ")";
				
					$row = $this->obDb->fetchQuery();			
					$totalVendor = $this->obDb->record_count;
					
					if ($totalVendor > 0){
						$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER",$totalVendor);
					} else {
						$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER","");
					}	
					
				$comFunc->orderId=$mode;
				for($i=0;$i<$rsOrderProductCount;$i++)
				{
					$this->ObTpl->set_var("TPL_VAR_OPTIONS","");
					$this->ObTpl->set_var("TPL_VAR_CHOICES","");
					$this->ObTpl->set_var("kit_blk","");	
					$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","");
					$this->ObTpl->set_var("TPL_VAR_TAXABLE","");
					$this->ObTpl->set_var("TPL_VAR_NOTES","");	
					$this->price=0;#INTIALIZING
					$this->total=0;
				
					$comFunc->orderProductId=$rsOrderProduct[$i]->iOrderProductid_PK;
					$comFunc->qty=$rsOrderProduct[$i]->iQty;
					$comFunc->price=$this->price;
					$this->ObTpl->set_var("TPL_VAR_GIFTWRAP","");
					##GIFTWRAP URL
					if($rsOrderProduct[$i]->iGiftwrapFK!=0){
						$this->ObTpl->set_var("TPL_VAR_GIFTWRAP",$comFunc->m_dspGiftWrap($rsOrderProduct[$i]->iGiftwrapFK));
					}
			
					if($rsOrderProduct[$i]->iKit==1)
					{
						$this->obDb->query = "SELECT iKitItem_title,iProductid_FK FROM ".ORDERKITS." WHERE  iKitId='".$rsOrderProduct[$i]->iProductid_FK."' AND iProductOrderid_FK='".$rsOrderProduct[$i]->iOrderProductid_PK."'";
						$rsKit=$this->obDb->fetchQuery();
						$rsKitCount=$this->obDb->record_count;

						for($j=0;$j<$rsKitCount;$j++)
						{
							$comFunc->kitProductId=$rsKit[$j]->iProductid_FK;
							#GET CART OPTIONS
							$kitOptions=$comFunc->m_orderKitProductOptions();
							if($kitOptions==' ')
							{
								$this->ObTpl->set_var("TPL_VAR_KITOPTIONS","");
							}
							else
							{
								$this->ObTpl->set_var("TPL_VAR_KITOPTIONS",$kitOptions);
							}
							$this->ObTpl->set_var("TPL_VAR_KITTITLE",$this->libFunc->m_displayContent($rsKit[$j]->iKitItem_title));
							$this->ObTpl->parse("kit_blk","TPL_KIT_BLK",true);	
						}
					}
					else
					{
						#GET ORDERED PRODUCT OPTIONS
						$this->ObTpl->set_var("TPL_VAR_OPTIONS",$comFunc->m_orderProductOptions());
						#GET ORDERED PRODUCT CHOICES
						$this->ObTpl->set_var("TPL_VAR_CHOICES",$comFunc->m_orderProductChoices());
					}

					# (OPTION And choice effected amount)
					$this->price=$comFunc->price;	

					#CHECK FOR DOWNLOADABLE FILE
					if($qryResult[0]->vPayMethod == "mail" || $qryResult[0]->vPayMethod == "cod"){
						$this->ObTpl->set_var("TPL_VAR_FILELINK","");
						$this->ObTpl->parse("download_blk","TPL_DOWNLOAD_BLK");
					}else{
						if(!empty($rsOrderProduct[$i]->vDownloadablefile))
						{
							$this->libFunc->m_checkFileExist($rsOrderProduct[$i]->vDownloadablefile,"files");
							if($this->libFunc->m_checkFileExist($rsOrderProduct[$i]->vDownloadablefile,"files"))
							{
								$downloadUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=download&mode=".$rsOrderProduct[$i]->iProductid_FK);
								$this->fileLink="<a href='".$downloadUrl."'>Click here to download</a>";
								$this->ObTpl->set_var("TPL_VAR_FILELINK",$this->fileLink);
								$this->ObTpl->parse("download_blk","TPL_DOWNLOAD_BLK");
							}
						}else{
							$this->ObTpl->set_var("TPL_VAR_FILELINK","");
							$this->ObTpl->parse("download_blk","TPL_DOWNLOAD_BLK");
						}
					}

					#VOLUME DISCOUNT
					#DISCOUNT ACCORDING TO QTY
					$vDiscountPerCartElement=number_format(($rsOrderProduct[$i]->fDiscount),2,'.','');
					if($vDiscountPerCartElement>0)
					{
						$totalDiscountItem=$vDiscountPerCartElement*$rsOrderProduct[$i]->iQty;
						$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT",
							"Volume Discount: ".CONST_CURRENCY.$vDiscountPerCartElement." each Total: ".CONST_CURRENCY.$totalDiscountItem."<br />");
						$this->volDiscount=$this->volDiscount+$totalDiscountItem;
					}
					$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rsOrderProduct[$i]->seo_title;
					$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	

					$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSku));

					$this->price=$this->price+$rsOrderProduct[$i]->fPrice;
					$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($this->price,2,'.',''));

					$this->ObTpl->set_var("TPL_VAR_QTY",$rsOrderProduct[$i]->iQty);
					$this->totalQty+=$rsOrderProduct[$i]->iQty;

					$this->total+=$rsOrderProduct[$i]->iQty*$this->price;
					$this->ObTpl->set_var("TPL_VAR_TOTAL",number_format($this->total,2,'.',''));
					$this->subTotal=$this->subTotal+$this->total;

					if($rsOrderProduct[$i]->iFreeship ==1)
					{
						$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","<em>".LBL_FREEPP."</em><br />");
					}
					if($rsOrderProduct[$i]->iTaxable !=1)
					{
						if (HIDENOVAT != 1) {
							$this->ObTpl->set_var("TPL_VAR_TAXABLE","<em>".LBL_NOTAX."</em><br />");
						} else {
							$this->ObTpl->set_var("TPL_VAR_TAXABLE","");
						}
					}
					if(!empty($rsOrderProduct[$i]->vPostageNotes))
					{
						$this->ObTpl->set_var("TPL_VAR_NOTES","Notes: ".$this->libFunc->m_displayContent($rsOrderProduct[$i]->vPostageNotes)."<br />");
					}
				
					$this->ObTpl->parse("cartproduct_blk","TPL_VAR_CARTPRODUCTS",true);
					$_SESSION['google']['products'] = Array();
					$_SESSION['google']['products'][] = "_gaq.push(['_addItem',
					  '".$_SESSION['google']['id']."',
					  '".$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSku)."',
					  '".$this->libFunc->m_displayContent($rsOrderProduct[$i]->vTitle)."',
					  '".$comFunc->m_orderProductOptions()." ".$comFunc->m_orderProductChoices()."',
					  '".$this->price."',
					  '".$rsOrderProduct[$i]->iQty."'
				   ]);";
				}
				#END PRODUCT DISPLAY

				#******************** SUB TOTAL ****************	*************		
				$this->ObTpl->set_var("TPL_VAR_SUBTOTAL",number_format($this->subTotal,2,'.',''));
				$this->grandTotal=$this->subTotal;

				#******************** PROMOTION CODE ************************
				if($qryResult[0]->fPromoValue>0)
				{
					$this->ObTpl->set_var("TPL_VAR_PDISCOUNTS",number_format($qryResult[0]->fPromoValue,2,'.',''));
					$this->grandTotal-=number_format($qryResult[0]->fPromoValue,2,'.','');
					$this->ObTpl->parse("promodiscounts_blk","TPL_PROMODISCOUNTS_BLK");
				}

				
				#******************** VOLUME DISCOUNT ************************
				if($this->volDiscount>0)
				{
					$this->ObTpl->set_var("TPL_VAR_VOLDISCOUNT",number_format($this->volDiscount,2,'.',''));
					$this->grandTotal-=$this->volDiscount;
					$this->ObTpl->parse("volDiscounts_blk","TPL_VOLDISCOUNTS_BLK");
				}
						#CART WEIGHT *******
				if($qryResult[0]->fShipByWeightPrice>0  && ISACTIVE_ITEMWEIGHT==1)
				{
					
					$this->ObTpl->set_var("TPL_VAR_WEIGHT",$qryResult[0]->fShipByWeightKg);
					$this->ObTpl->set_var("TPL_VAR_WEIGHTPRICE",number_format($qryResult[0]->fShipByWeightPrice,2,'.',''));
					
					$this->grandTotal+=$qryResult[0]->fShipByWeightPrice;
					$this->ObTpl->parse("cartWeight_blk","TPL_CARTWEIGHT_BLK");
				}
				if($qryResult[0]->fMemberPoints>0)
				{
					$this->ObTpl->set_var("TPL_VAR_MPOINTS",number_format($qryResult[0]->fMemberPoints,2,'.',''));
					$this->grandTotal-=number_format($qryResult[0]->fMemberPoints,2,'.','');
					$this->ObTpl->parse("memberpoint_blk","TPL_MPOINTS_BLK");
				}

				# code added for getting total earned points

				if($qryResult[0]->iEarnedPoints>0)
				{
					$this->ObTpl->set_var("TPL_VAR_EMPOINTS",number_format($qryResult[0]->iEarnedPoints));
					$this->ObTpl->parse("earnedmemberpoint_blk","TPL_EMPOINTS_BLK");
				}

					# code added for getting total points
		
					$this->obDb->query = "SELECT fMemberPoints FROM ".CUSTOMERS." WHERE  iCustmerid_PK=".$qryResult[0]->iCustomerid_FK;
					$rsCust=$this->obDb->fetchQuery();
					
					if($rsCust[0]->fMemberPoints>0){
						$this->ObTpl->set_var("TPL_VAR_TMPOINTS",number_format($rsCust[0]->fMemberPoints,0));
						$memberpoint_price=MPOINTVALUE*$rsCust[0]->fMemberPoints;
						$this->ObTpl->set_var("TPL_VAR_TMPOINTS_PRICE",number_format($memberpoint_price,2,'.',''));
						$this->ObTpl->parse("totalmemberpoint_blk","TPL_TMPOINTS_BLK");
					}				


					$temptotal = $this->grandTotal;
					#POSTAGE CALCULATION**************************

				if($qryResult[0]->fShipTotal>0)
				{
					
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Postage method (".$qryResult[0]->vShipDescription.")");
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",CONST_CURRENCY.number_format($qryResult[0]->fShipTotal,2,'.',''));
					$this->grandTotal+=number_format($qryResult[0]->fShipTotal,2,'.','');
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
					$_SESSION['google']['shipping'] = $qryResult[0]->fShipTotal;
				}
				elseif($qryResult[0]->vShipDescription=="Free P&P")
				{
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$qryResult[0]->vShipDescription);
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE","No Charge");
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
					$_SESSION['google']['shipping'] = 0;
				}

					#COD PRICE(PAYMENT GATEWAY ADDITIONAL PRICE)
				if($qryResult[0]->fCodCharge>0)
				{
					$this->ObTpl->set_var("TPL_VAR_CODPRICE",number_format($qryResult[0]->fCodCharge ,2,'.',''));
					$this->grandTotal+=number_format($qryResult[0]->fCodCharge ,2,'.','');
					$_SESSION['google']['shipping'] = $_SESSION['google']['shipping'] + $qryResult[0]->fCodCharge;
					$this->ObTpl->parse("cod_blk","TPL_COD_BLK");
				}
				#CHECK FOR DISCOUNTS
				if($qryResult[0]->fDiscount!=0)
				{
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE",number_format($qryResult[0]->fDiscount,2,'.',''));
					$this->grandTotal-=number_format($qryResult[0]->fDiscount,2,'.','');
					$temptotal = $temptotal - $qryResult[0]->fDiscount;
					$_SESSION['google']['subtotal'] = $temptotal;
					$this->ObTpl->parse("discount_blk","TPL_DISCOUNT_BLK");
					
					$curTime=time();
					$this->obDb->query ="UPDATE ".DISCOUNTS." SET iState=0 WHERE vCode='".$qryResult[0]->vDiscountCode."' AND tmStartDate<$curTime AND tmEndDate>$curTime AND iUseonce=1";
					$this->obDb->updateQuery();		
				}
				#CHECK FOR GIFTCERTIFICATES
				if($qryResult[0]->fGiftcertTotal!=0)
				{
					$this->grandTotal-=number_format($qryResult[0]->fGiftcertTotal,2,'.','');
					$this->ObTpl->set_var("TPL_VAR_GIFTCERTPRICE",number_format($qryResult[0]->fGiftcertTotal,2,'.',''));
					$this->ObTpl->parse("giftcert_blk","TPL_GIFTCERT_BLK");	
				}
				if($qryResult[0]->fTaxPrice>0)
				{
					$this->ObTpl->set_var("TPL_VAR_TAXNAME", VAT_TAX_TEXT);					$this->ObTpl->set_var("TPL_VAR_VAT",number_format($qryResult[0]->fTaxRate,2,'.',''));
					$this->ObTpl->set_var("TPL_VAR_VATPRICE",number_format($qryResult[0]->fTaxPrice,2,'.',''));
					$_SESSION['google']['tax'] = $qryResult[0]->fTaxPrice;
					$this->ObTpl->parse("vat_blk","TPL_VAT_BLK");
				}

				$this->grandTotal+=number_format($qryResult[0]->fDiscount,2,'.','');
				$this->ObTpl->set_var("TPL_VAR_CURRENTTOTAL",number_format($qryResult[0]->fTotalPrice,2,'.',''));
				$_SESSION['google']['total'] = $qryResult[0]->fTotalPrice;
				$_SESSION['google']['paid'] = 1;
				$this->ObTpl->parse("cart_blk","TPL_CART_BLK");	
			}
		
		}#END ORDERS IF CONDITION

			
			$message=$this->ObTpl->parse("return","TPL_ORDERMAIL_FILE");
			
			$obMail = new htmlMimeMail();
			$obMail->setReturnPath(ADMIN_EMAIL);
			$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");

			$obMail->setSubject("Thank You for your order at ".SITE_NAME);
			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);

			$obMail->buildMessage();
			//if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", ADMIN_EMAIL)) { 
			
				$result = $obMail->send(array($qryResult[0]->vEmail));
				//$result = $obMail->send(array('soniduke@gmail.com'));
			//}
			$obMail->setSubject("Thank You for your order at ".SITE_NAME);
			
			$this->ObTpl->set_var("customer_blk","");
			$this->ObTpl->parse("admin_blk","TPL_ADMIN_BLK");

			$obMail->setSubject(SITE_NAME." Invoice ".$qryResult[0]->iInvoice);
			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$message=$this->ObTpl->parse("return","TPL_ORDERMAIL_FILE");
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);

			$obMail->buildMessage();
			//if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", ORDER_EMAIL)) { 
				$result = $obMail->send(array(ORDER_EMAIL));
			//}
			
			# SEND ORDER MAIL TO SUPPLIER
			$this->m_sendSupplierOrderDetails($row);
			# END ORDER MAIL TO SUPPLIER
			
			
			#WIRELESS EMAIL
			$obMail->setSubject(SITE_NAME." Invoice ".$qryResult[0]->iInvoice);
			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$wirelesscontent="Invoice: ".$qryResult[0]->iInvoice."<br />";
			$wirelesscontent.="Customer: ".$Name."<br />";
			$wirelesscontent.="Total: ".CONST_CURRENCY.number_format($qryResult[0]->fTotalPrice,2,'.','');
			 
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$wirelesscontent));
			$obMail->setHtml($wirelesscontent,$txtcontent);
			$obMail->buildMessage();
			if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", WIRELESS_EMAIL)) {
				$result = $obMail->send(array(WIRELESS_EMAIL));
			}
			if($qryResult[0]->vPayMethod != "paypal")
			{
			$processUrl=SITE_SAFEURL."ecom/index.php?action=checkout.status&mode=".$mode;
			}
			if($qryResult[0]->vPayMethod=='worldpay'){
			
				?>
				 <script language="JavaScript" type="text/javascript">
					location.href="<? echo $this->libFunc->m_safeUrl($processUrl) ?>";
					</script>
				<?php
				$this->template=$this->processTemplate;
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Order Processed");
				$this->obTpl->set_var("TPL_VAR_BODY",$this->m_orderProcessed());
				$this->worldpay=1;
				//$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($processUrl));
			}else{
			
				$this->worldpay=0;
				$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($processUrl));
			}
		}	
		elseif(isset($mode) && !empty($mode))
		{
		
			$processUrl=SITE_SAFEURL."ecom/index.php?action=checkout.status&mode=".$mode;
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($processUrl));
		}
		else
		{
		
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
	}

	#FUNCTION TO VALIDATE SECPAY
	function m_validateSecpay(){	
		
		if($this->request['code']!='A'){
			return false;
		}
		 $hashKey=md5("trans_id=".$_REQUEST['trans_id']."&amount=".$_REQUEST['amount']."&callback=".SITE_URL."SECpay/callback.php&".SECPAY_DIGESTKEY);
		
		if($this->request['hash']==$hashKey){
			return true;
		}else{
			$this->request['message']='Fraud entry';
		}
		return false;
	}# END OF m_validateSecpay

	#FUNCTION TO VALIDATE WORLD PAY
	function m_validateWorldpay(){
		if($this->request['transStatus']=='C'){
			$this->request['message']="Transaction has been cancelled";
			return false;
		}
		if($this->request['transStatus']=='N'){
			$this->request['message']="Transaction has been rejected";
			return false;
		}
		return true;
	}

	#FUNCTION TO DELETE TEMPERARY DATA
	function m_deleteTemp()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->obDb->query = "SELECT count(*) as cnt  FROM ".TEMPCART." WHERE  vSessionId='".$this->sessionId."'";
		$rowCount=$this->obDb->fetchQuery();

		$this->obDb->query = "SELECT iTmpCartId_PK,iQty,iProdId_FK,iUseinventory,P.iBackorder,vDownloadablefile  FROM ".TEMPCART." AS T,".PRODUCTS." AS P WHERE  vSessionId='".$this->sessionId."' AND iProdId_FK=iProdId_PK";
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
			{
				#CHECK FOR DOWNLOADABLE FILE
				if(!empty($rowCart[$i]->vDownloadablefile))
				{
					if($this->libFunc->m_checkFileExist($rowCart[$i]->vDownloadablefile,"files"))
					{
						$downloadUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=download&mode=".$rowCart[$i]->vDownloadablefile);
						$this->fileLink.="<a href='".$downloadUrl."'>".$downloadUrl."</a><br /><br />";
					}
				}
				#UPDATING QUANTITY
				if(STOCK_CHECK==1 && ($rowCart[$i]->iUseinventory==1 && $rowCart[$i]->iBackorder!=1))
				{
					$this->obDb->query="UPDATE ".PRODUCTS." SET iInventory=iInventory - ".$rowCart[$i]->iQty." WHERE  iProdId_PK	='".$rowCart[$i]->iProdId_FK."'";
					$this->obDb->updateQuery();
					
				}
				#SEND STOCK EMAIL 
				$comFunc->productId=$rowCart[$i]->iProdId_FK;
				$comFunc->m_sendStockMail();
				
				$this->obDb->query = "SELECT vOptVal,iProdId_Fk FROM ".TEMPOPTIONS." WHERE  iTmpCartId_FK='".$rowCart[$i]->iTmpCartId_PK."'";
				$rowOpt=$this->obDb->fetchQuery();
				$rsOptCount=$this->obDb->record_count;
				
				if($rsOptCount>0)
				{
					for($j=0;$j<$rsOptCount;$j++)
					{
						//Neetika Patch: Product Package
						if($rowOpt[$j]->vOptVal == "" && $rowOpt[$j]->iProdId_Fk != ""){
							$this->obDb->query="UPDATE ".PRODUCTS." SET iInventory=iInventory - ".$rowCart[$i]->iQty." WHERE  iProdId_PK	='".$rowOpt[$j]->iProdId_Fk."'";
                            #TELL MOBI TO UPDATE INV
                            if ( MOBICARTEN == "enabled" ){
                                $this->obDb->query="SELECT * FROM ".PRODUCTS." WHERE iProdId_PK = '".$rowOpt[$j]->iProdId_FK."'";
                                $prodInfo = $this->obDb->fetchQuery();
                                mobi_upd_prod($prodInfo[0]->vTitle, $prodInfo[0]->vTitle, '', $prodInfo[0]->fprice, '', '', $prodInfo[0]->iInventory, $use_inventory);
                            }
						}else{
						#UPDATING QUANTITY
							$this->obDb->query="UPDATE ".OPTIONVALUES." SET iInventory=iInventory - ".$rowCart[$i]->iQty." WHERE iOptionValueid_PK='".$rowOpt[$j]->vOptVal."'";
						}
						$this->obDb->updateQuery();
					}
				}	
				#DELETE TEMP OPTIONS ENTRIES FROM DATABASE
				$this->obDb->query="DELETE FROM ".TEMPOPTIONS." WHERE  iTmpCartId_FK='".$rowCart[$i]->iTmpCartId_PK."'";
				$this->obDb->updateQuery();

				$this->obDb->query = "SELECT iTmpChoiceId_FK FROM ".TEMPCHOICES." WHERE  iTmpCartId_FK='".$rowCart[$i]->iTmpCartId_PK."'";
				$rowChoice=$this->obDb->fetchQuery();
				$rsChoiceCount=$this->obDb->record_count;
				
				if($rsChoiceCount>0)
				{
					for($k=0;$k<$rsChoiceCount;$k++)
					{
						#UPDATING QUANTITY
						$this->obDb->query="UPDATE ".CHOICES." SET iInventory=iInventory - ".$rowCart[$i]->iQty." WHERE  iChoiceid_PK='".$rowChoice[$k]->iTmpChoiceId_FK."'";
						$this->obDb->updateQuery();
					}
				}	

				#DELETE TEMP CHOICES FROM DATABASE
				$this->obDb->query="DELETE FROM ".TEMPCHOICES." WHERE  iTmpCartId_FK='".$rowCart[$i]->iTmpCartId_PK."'";
				$this->obDb->updateQuery();

				#DELEET TEMP CART
				$this->obDb->query="DELETE FROM ".TEMPCART." WHERE vSessionId='".$this->sessionId."'";
				#FLAG TO INDICATE WHETHER PROCESSING BACKORDER OR NOT
				$_SESSION['backOrderProcess']=$this->libFunc->ifSet($_SESSION,'backOrderProcess','0');

				if($_SESSION['backOrderSeperate']==1 && $_SESSION['backOrderProcess']==1)
				{
					$this->obDb->query .=" AND iBackOrder='1'";
				}
				elseif($_SESSION['backOrderSeperate']==1)
				{
					$this->obDb->query .=" AND iBackOrder<>'1'";
				}
				$this->obDb->updateQuery();
			}#end for
		}#end if

		if($rowCount[0]->cnt==$rsCount || $rowCount[0]->cnt=="0") 
		{
			#DELETE FROM SESSION
			foreach($_SESSION as $sid=>$svalue)
			{
				$notDestroyArrray=array('cssSelectedFile','userid','uid','username','uname','email');
				if(!in_array($sid,$notDestroyArrray))
				{
					unset($_SESSION[$sid]);
				}
			}
		}
	}

	
	#FUNCTION TO DISPLAY receipt
	function m_dspreceipt()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		if(!isset($this->request['mode']) || empty($this->request['mode']))
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_REVIEW_FILE",$this->receiptTemplate);

		#SETTING BLOCKS
		$this->ObTpl->set_block("TPL_REVIEW_FILE","TPL_CART_BLK","cart_blk");
		$this->ObTpl->set_block("TPL_REVIEW_FILE","TPL_DELIVERY_BLK","delivery_blk");
		$this->ObTpl->set_block("TPL_REVIEW_FILE","TPL_VAR_CARTPRODUCTS","cartproduct_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_GIFTCERT_BLK","giftcert_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_DISCOUNT_BLK","discount_blk");
		$this->ObTpl->set_block("TPL_VAR_CARTPRODUCTS","TPL_KIT_BLK","kit_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_COD_BLK","cod_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_PROMODISCOUNTS_BLK","promodiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VOLDISCOUNTS_BLK","volDiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_POSTAGE_BLK","postage_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_MPOINTS_BLK","memberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_EMPOINTS_BLK","earnedmemberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_TMPOINTS_BLK","totalmemberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_CARTWEIGHT_BLK","cartWeight_blk");	
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAT_BLK","vat_blk");

		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT","");
		$this->ObTpl->set_var("TPL_VAR_VAUTHCODE","-");
		
		#INTAILAIZING
		$this->ObTpl->set_var("delivery_blk","");	
		$this->ObTpl->set_var("cart_blk","");	
		$this->ObTpl->set_var("cartWeight_blk","");	
		$this->ObTpl->set_var("giftcert_blk","");	
		$this->ObTpl->set_var("discount_blk","");	
		$this->ObTpl->set_var("cartproduct_blk","");	
		$this->ObTpl->set_var("kit_blk","");	
		$this->ObTpl->set_var("promodiscounts_blk","");	
		$this->ObTpl->set_var("volDiscounts_blk","");	
		$this->ObTpl->set_var("postage_blk","");		
		$this->ObTpl->set_var("cod_blk","");	
		$this->ObTpl->set_var("memberpoint_blk","");
		$this->ObTpl->set_var("earnedmemberpoint_blk","");
		$this->ObTpl->set_var("totalmemberpoint_blk","");
		$this->ObTpl->set_var("vat_blk","");

		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING","");
		
		// [DRK][MODIFIED BY NSI]
		$this->ObTpl->set_var("TPL_VAR_COMPANY_DETAILS",$comFunc->m_mailFooter());
		// [/DRK]

		

		#QUERY ORDER TABLE
		$this->obDb->query = "SELECT tmOrderDate,vPayMethod,vShipDescription,fShipTotal,";
		$this->obDb->query.= "vFirstName,vLastName,vEmail,vAddress1,vAddress2,vCity,iInvoice,";
		$this->obDb->query.= "vState,vStateName,vCountry,vZip,vCompany,vPhone,vHomepage,";
		$this->obDb->query.= "vAltCompany,vAltName,vAltAddress1,vAltAddress2,vAltCity,vAltState,vAltCountry,";
		$this->obDb->query.= "vAltStateName,vAltZip,vAltPhone,fCodCharge,fPromoValue,";
		$this->obDb->query.= "vDiscountCode,fDiscount,iGiftcert_FK,fGiftcertTotal,fMemberPoints,";
		$this->obDb->query.= "fShipByWeightPrice,fShipByWeightKg,iSameAsBilling,vAuthCode,";
		$this->obDb->query.= "fTaxRate,fTaxPrice,tComments,vStatus,iPayStatus,fTotalPrice,iEarnedPoints,iCustomerid_FK";
		$this->obDb->query .= " FROM ".ORDERS." WHERE iOrderid_PK='".$this->request['mode']."'";
		if(isset($_SESSION['userid']) && !empty($_SESSION['userid'])){
			$this->obDb->query .= " AND iCustomerid_FK='".$_SESSION['userid']."'";
		}else{
			$this->obDb->query .= " AND vEmail='".$_SESSION['email']."'";
		} 
		$rsOrder=$this->obDb->fetchQuery();
		$rsOrderCount=$this->obDb->record_count;
				  
			$this->ObTpl->set_var("TPL_VAR_VAUTHCODE", $rsOrder[0]->vAuthCode);
				
		if($rsOrderCount!=1)
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}

 
		if($rsOrderCount>0)
		{
			$this->ObTpl->set_var("TPL_VAR_INVOICE",$rsOrder[0]->iInvoice);	
			$this->ObTpl->set_var("TPL_VAR_ORDERDATE",$this->libFunc->dateFormat2($rsOrder[0]->tmOrderDate));
			if($rsOrder[0]->vPayMethod=='cod')
			{
				$vPayMethod=	$comFunc->m_paymentMethod($rsOrder[0]->vPayMethod,$rsOrder[0]->fCodCharge);
			}
			else
			{
				$vPayMethod=	$comFunc->m_paymentMethod($rsOrder[0]->vPayMethod);
			}
			$this->ObTpl->set_var("TPL_VAR_PAYMENTMETHOD",$vPayMethod);
			$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$rsOrder[0]->vShipDescription);
			$this->ObTpl->set_var("TPL_VAR_ORDERSTATUS",ucfirst($rsOrder[0]->vStatus));
			if(empty($rsOrder[0]->tComments))
			{
				$this->ObTpl->set_var("TPL_VAR_COMMENTS","None");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_COMMENTS",$rsOrder[0]->tComments);
			}

			if(isset($rsOrder[0]->vState) && !empty($rsOrder[0]->vState))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$rsOrder[0]->vState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_BILLSTATE",					$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$rsOrder[0]->vStateName);
			}
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$rsOrder[0]->vCountry."'";
			$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",
			$this->libFunc->m_displayContent($row_country[0]->vCountryName));

			if(isset($rsOrder[0]->vAltState) && !empty($rsOrder[0]->vAltState))
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$rsOrder[0]->vAltState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",					$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$rsOrder[0]->vAltStateName);
			}
			
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$rsOrder[0]->vAltCountry."'";
			$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
			$this->libFunc->m_displayContent($row_country[0]->vCountryName));


			$this->ObTpl->set_var("TPL_VAR_FIRSTNAME",$this->libFunc->m_displayContent($rsOrder[0]->vFirstName));
			$this->ObTpl->set_var("TPL_VAR_LASTNAME",$this->libFunc->m_displayContent($rsOrder[0]->vLastName));
			$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($rsOrder[0]->vEmail));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($rsOrder[0]->vAddress1));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($rsOrder[0]->vAddress2));
			$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($rsOrder[0]->vCity));
			$this->ObTpl->set_var("TPL_VAR_ZIP",$this->libFunc->m_displayContent($rsOrder[0]->vZip));
			$this->ObTpl->set_var("TPL_VAR_COMPANY",$this->libFunc->m_displayContent($rsOrder[0]->vCompany));
			$this->ObTpl->set_var("TPL_VAR_PHONE",$this->libFunc->m_displayContent($rsOrder[0]->vPhone));
			$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",$this->libFunc->m_displayContent($rsOrder[0]->vHomepage));
			$this->ObTpl->set_var("TPL_VAR_MPOINTS","");
		
			# ADD EDIT AND MY ACCOUNT LINK	  
			$url_editOrder = SITE_URL."ecom/index.php?action=checkout.editOrder&mode=".$this->request['mode'];
			$this->ObTpl->set_var("TPL_VAR_MYACCOUNT_URL",SITE_URL."user/index.php?action=user.home");
						
			$this->ObTpl->set_var("TPL_VAR_EDITORDER_URL",$url_editOrder);
	  
		
			if($rsOrder[0]->iSameAsBilling==1)
			{
				$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING",MSG_SAMEASBILLING);
			}
			else
			{
				$this->ObTpl->parse("delivery_blk","TPL_DELIVERY_BLK");
			}
			if(isset($rsOrder[0]->vAltCompany) && !empty($rsOrder[0]->vAltCompany))
			{
			$this->ObTpl->set_var("TPL_VAR_ALTCOMPANY",$this->libFunc->m_displayContent($rsOrder[0]->vAltCompany));
			}
			else
			{
			$this->ObTpl->set_var("TPL_VAR_ALTCOMPANY","");
			}
			$this->ObTpl->set_var("TPL_VAR_ALTNAME",$this->libFunc->m_displayContent($rsOrder[0]->vAltName));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($rsOrder[0]->vAltAddress1));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($rsOrder[0]->vAltAddress2));
			$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($rsOrder[0]->vAltCity));
			
			$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($rsOrder[0]->vAltZip));
			$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($rsOrder[0]->vAltPhone));
		
			$this->obDb->query = "SELECT iOrderProductid_PK,iProductid_FK,iQty,iGiftwrapFK,fPrice,";
			$this->obDb->query.= "fDiscount,vTitle,vSku,iKit,tShortDescription,vSeoTitle,iTaxable,iFreeship,vPostageNotes,seo_title ";
			$this->obDb->query .= " FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['mode']."'";
			 
			$rsOrderProduct=$this->obDb->fetchQuery();
			$rsOrderProductCount=$this->obDb->record_count;
			if($rsOrderProductCount>0)
			{
				$id_rows = array();
				for ($iSup=0; $iSup<$rsOrderProductCount; $iSup++ )
				{
					$id_rows[$iSup] = $rsOrderProduct[$iSup]->iProductid_FK;
				}
				
				$this->obDb->query = " SELECT distinct iVendorid_FK FROM ".PRODUCTS.
									 " WHERE iVendorid_FK>0 AND iProdid_PK IN (" . implode(",", $id_rows). ")";
			
				$row = $this->obDb->fetchQuery();			
				$totalVendor = $this->obDb->record_count;
				
				if ($totalVendor > 0){
					$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER",$totalVendor);
				} else {
					$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER","");
				}					
				
				$comFunc->orderId=$this->request['mode'];
				for($i=0;$i<$rsOrderProductCount;$i++)
				{
					$this->ObTpl->set_var("TPL_VAR_OPTIONS","");
					$this->ObTpl->set_var("TPL_VAR_CHOICES","");
					$this->ObTpl->set_var("kit_blk","");	
					$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","");
					$this->ObTpl->set_var("TPL_VAR_TAXABLE","");
					$this->ObTpl->set_var("TPL_VAR_NOTES","");	
					$this->price=0;#INTIALIZING
					$this->total=0;
				
					$comFunc->orderProductId=$rsOrderProduct[$i]->iOrderProductid_PK;
					$comFunc->qty=$rsOrderProduct[$i]->iQty;
					$comFunc->price=$this->price;
					$this->ObTpl->set_var("TPL_VAR_GIFTWRAP","");
					##GIFTWRAP URL
					if($rsOrderProduct[$i]->iGiftwrapFK!=0){
						$this->ObTpl->set_var("TPL_VAR_GIFTWRAP",$comFunc->m_dspGiftWrap($rsOrderProduct[$i]->iGiftwrapFK));
					}
			
					if($rsOrderProduct[$i]->iKit==1)
					{
						$this->obDb->query = "SELECT iKitItem_title,iProductid_FK FROM ".ORDERKITS." WHERE  iKitId='".$rsOrderProduct[$i]->iProductid_FK."' AND iProductOrderid_FK='".$rsOrderProduct[$i]->iOrderProductid_PK."'";
						$rsKit=$this->obDb->fetchQuery();
						$rsKitCount=$this->obDb->record_count;

						for($j=0;$j<$rsKitCount;$j++)
						{
							$comFunc->kitProductId=$rsKit[$j]->iProductid_FK;
							#GET CART OPTIONS
							$kitOptions=$comFunc->m_orderKitProductOptions();
							if($kitOptions==' ')
							{
								$this->ObTpl->set_var("TPL_VAR_KITOPTIONS","");
							}
							else
							{
								$this->ObTpl->set_var("TPL_VAR_KITOPTIONS",$kitOptions);
							}
							$this->ObTpl->set_var("TPL_VAR_KITTITLE",$this->libFunc->m_displayContent($rsKit[$j]->iKitItem_title));
							$this->ObTpl->parse("kit_blk","TPL_KIT_BLK",true);	
						}
					}
					else
					{
						#GET ORDERED PRODUCT OPTIONS
						$this->ObTpl->set_var("TPL_VAR_OPTIONS",$comFunc->m_orderProductOptions());
						#GET ORDERED PRODUCT CHOICES
						$this->ObTpl->set_var("TPL_VAR_CHOICES",$comFunc->m_orderProductChoices());
					}

					# (OPTION And choice effected amount)
					$this->price=$comFunc->price;	

				#VOLUME DISCOUNT
					#DISCOUNT ACCORDING TO QTY
					$vDiscountPerCartElement=number_format(($rsOrderProduct[$i]->fDiscount),2,'.','');
					if($vDiscountPerCartElement>0)
					{
						$totalDiscountItem=$vDiscountPerCartElement*$rsOrderProduct[$i]->iQty;
						$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT",
							"Volume Discount: ".CONST_CURRENCY.$vDiscountPerCartElement." each Total: ".CONST_CURRENCY.$totalDiscountItem."<br />");
						$this->volDiscount=$this->volDiscount+$totalDiscountItem;
					}
					$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rsOrderProduct[$i]->seo_title;
					$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	

					$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSku));

					$this->price=$this->price+$rsOrderProduct[$i]->fPrice;
					$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($this->price,2,'.',''));

					$this->ObTpl->set_var("TPL_VAR_QTY",$rsOrderProduct[$i]->iQty);
					$this->totalQty+=$rsOrderProduct[$i]->iQty;

					$this->total+=$rsOrderProduct[$i]->iQty*$this->price;
					$this->ObTpl->set_var("TPL_VAR_TOTAL",number_format($this->total,2,'.',''));
					$this->subTotal=$this->subTotal+$this->total;

					if($rsOrderProduct[$i]->iFreeship ==1)
					{
						$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","<em>".LBL_FREEPP."</em><br />");
					}
					if($rsOrderProduct[$i]->iTaxable !=1)
					{
						if (HIDENOVAT != 1) {
							$this->ObTpl->set_var("TPL_VAR_TAXABLE","<em>".LBL_NOTAX."</em><br />");
						} else {
							$this->ObTpl->set_var("TPL_VAR_TAXABLE","");
						}
					}
					if(!empty($rsOrderProduct[$i]->vPostageNotes))
					{
						$this->ObTpl->set_var("TPL_VAR_NOTES","Notes: ".$this->libFunc->m_displayContent($rsOrderProduct[$i]->vPostageNotes)."<br />");
					}
				
					$this->ObTpl->parse("cartproduct_blk","TPL_VAR_CARTPRODUCTS",true);	
				}
				#END PRODUCT DISPLAY

				#******************** SUB TOTAL ****************	*************		
				$this->ObTpl->set_var("TPL_VAR_SUBTOTAL",number_format($this->subTotal,2,'.',''));
				$this->grandTotal=$this->subTotal;

				#******************** PROMOTION CODE ************************
				if($rsOrder[0]->fPromoValue>0)
				{
					$this->ObTpl->set_var("TPL_VAR_PDISCOUNTS",number_format($rsOrder[0]->fPromoValue,2,'.',''));
					$this->grandTotal-=number_format($rsOrder[0]->fPromoValue,2,'.','');
					$this->ObTpl->parse("promodiscounts_blk","TPL_PROMODISCOUNTS_BLK");
				}

				
				#******************** VOLUME DISCOUNT ************************
				if($this->volDiscount>0)
				{
					$this->ObTpl->set_var("TPL_VAR_VOLDISCOUNT",number_format($this->volDiscount,2,'.',''));
					$this->grandTotal-=$this->volDiscount;
					$this->ObTpl->parse("volDiscounts_blk","TPL_VOLDISCOUNTS_BLK");
				}
						#CART WEIGHT *******
				if($rsOrder[0]->fShipByWeightPrice>0  && ISACTIVE_ITEMWEIGHT==1)
				{
					
					$this->ObTpl->set_var("TPL_VAR_WEIGHT",$rsOrder[0]->fShipByWeightKg);
					$this->ObTpl->set_var("TPL_VAR_WEIGHTPRICE",number_format($rsOrder[0]->fShipByWeightPrice,2,'.',''));
					
					$this->grandTotal+=$rsOrder[0]->fShipByWeightPrice;
					$this->ObTpl->parse("cartWeight_blk","TPL_CARTWEIGHT_BLK");
				}
				if($rsOrder[0]->fMemberPoints>0)
				{
					$this->ObTpl->set_var("TPL_VAR_MPOINTS",number_format($rsOrder[0]->fMemberPoints,2,'.',''));
					$this->grandTotal-=number_format($rsOrder[0]->fMemberPoints,2,'.','');
					$this->ObTpl->parse("memberpoint_blk","TPL_MPOINTS_BLK");
				}

		# code added for getting total earned points

				if($rsOrder[0]->iEarnedPoints>0)
				{
					$this->ObTpl->set_var("TPL_VAR_EMPOINTS",$rsOrder[0]->iEarnedPoints);
					$this->ObTpl->parse("earnedmemberpoint_blk","TPL_EMPOINTS_BLK");
				}

		# code added for getting total points
		
					$this->obDb->query = "SELECT fMemberPoints FROM ".CUSTOMERS." WHERE  iCustmerid_PK=".$rsOrder[0]->iCustomerid_FK;
					$rsCust=$this->obDb->fetchQuery();
					
					if($rsCust[0]->fMemberPoints>0){
					$this->ObTpl->set_var("TPL_VAR_TMPOINTS",number_format($rsCust[0]->fMemberPoints,0));
					$memberpoint_price=MPOINTVALUE*$rsCust[0]->fMemberPoints;
					$this->ObTpl->set_var("TPL_VAR_TMPOINTS_PRICE",number_format($memberpoint_price,2,'.',''));
					$this->ObTpl->parse("totalmemberpoint_blk","TPL_TMPOINTS_BLK");
					}				



					#POSTAGE CALCULATION**************************

				if($rsOrder[0]->fShipTotal>0) {
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Postage method (".$rsOrder[0]->vShipDescription.")");
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",CONST_CURRENCY.number_format($rsOrder[0]->fShipTotal,2,'.',''));
					$this->grandTotal+=number_format($rsOrder[0]->fShipTotal,2,'.','');
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
				}
				elseif($rsOrder[0]->vShipDescription=="Free P&amp;P")
				{
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$rsOrder[0]->vShipDescription);
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE","No Charge");
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
				}

				#COD PRICE(PAYMENT GATEWAY ADDITIONAL PRICE)
				if($rsOrder[0]->fCodCharge>0)
				{
					$this->ObTpl->set_var("TPL_VAR_CODPRICE",number_format($rsOrder[0]->fCodCharge ,2,'.',''));
					$this->grandTotal+=number_format($rsOrder[0]->fCodCharge ,2,'.','');
					$this->ObTpl->parse("cod_blk","TPL_COD_BLK");
				}
				#CHECK FOR DISCOUNTS
				if($rsOrder[0]->fDiscount!=0)
				{
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE",number_format($rsOrder[0]->fDiscount,2,'.',''));
					$this->grandTotal-=number_format($rsOrder[0]->fDiscount,2,'.','');
					$this->ObTpl->parse("discount_blk","TPL_DISCOUNT_BLK");	
				}
				#CHECK FOR GIFTCERTIFICATES
				if($rsOrder[0]->fGiftcertTotal!=0)
				{
					$this->grandTotal-=number_format($rsOrder[0]->fGiftcertTotal,2,'.','');
					$this->ObTpl->set_var("TPL_VAR_GIFTCERTPRICE",number_format($rsOrder[0]->fGiftcertTotal,2,'.',''));
					$this->ObTpl->parse("giftcert_blk","TPL_GIFTCERT_BLK");	
				}
				if($rsOrder[0]->fTaxPrice>0)
				{
					$this->ObTpl->set_var("TPL_VAR_TAXNAME", VAT_TAX_TEXT);					$this->ObTpl->set_var("TPL_VAR_VAT",number_format($rsOrder[0]->fTaxRate,2,'.',''));
					$this->ObTpl->set_var("TPL_VAR_VATPRICE",number_format($rsOrder[0]->fTaxPrice,2,'.',''));
					$this->ObTpl->parse("vat_blk","TPL_VAT_BLK");
				}

				$this->grandTotal+=number_format($rsOrder[0]->fDiscount,2,'.','');
				$this->ObTpl->set_var("TPL_VAR_CURRENTTOTAL",number_format($rsOrder[0]->fTotalPrice,2,'.',''));
				$this->ObTpl->parse("cart_blk","TPL_CART_BLK");	
			}
		
		}#END ORDERS IF CONDITION

		return $this->ObTpl->parse("return","TPL_REVIEW_FILE");
	}#END FUNCTION

	#FUNCTION START BACKORDER PROCESSING
	function m_processBackorder()
	{
		$_SESSION['backOrderProcess']=1;
		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);	
	}
	
	#FUNCTION TO RECEIVE PAYPAL IPN
	function m_Paypal_IPN_Notification()
	{
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=' . urlencode('_notify-validate');
		$orderid = $this->request['id'];
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
		
		 
		$ch = curl_init();
		if(GATEWAY_TESTMODE == 1)
		{
		curl_setopt($ch, CURLOPT_URL, 'https://www.sandbox.paypal.com/cgi-bin/webscr');
		}
		else
		{
		curl_setopt($ch, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr');
		}
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: www.paypal.com'));
		$res = curl_exec($ch);
		curl_close($ch);
		 
		// assign posted variables to local variables
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];
		$sessionid = $_GET['phpsessid'];
		$this->sessionId = $sessionid; 
		//error_log("got info from paypal\n",3,SITE_PATH."paypal_ipn.log");
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			
			$comFunc=new c_commonFunctions();
			$comFunc->obDb=$this->obDb;
			$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE iOrderid_PK=".$orderid;
			$row=$this->obDb->fetchQuery();
			//error_log("Status:".$payment_status."|totalprice:".$row[0]->fTotalPrice."|paidprice:".$payment_amount."\n",3,SITE_PATH."paypal_ipn.log");
			if($payment_status == "Completed" && $receiver_email == PAYPAL_ID && $row[0]->fTotalPrice == $payment_amount)
			{
				//UPDATE ORDER IN DATABASE
				$this->obDb->query = "UPDATE ".ORDERS." SET iPayStatus=1,vSessionId='".$sessionid."',iOrderStatus=1 WHERE iOrderid_PK=".$orderid;
				$row=$this->obDb->updateQuery();
				//SEND TO NOTIFICATION FUNCTION
				//domain/ecom/checkout.process&mode=orderid
			//error_log("Marked Order as Paid.\n",3,SITE_PATH."paypal_ipn.log");
				return "1, ". $orderid . "," . $sessionid;
			}
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			//error_log("Invalid Response from PAYPAL IPN.",3,SITE_PATH."paypal_ipn.log");
			return "0,";
		}
	}
	
	#Creates the page the user views when the return from a paypal order (no receipt until IPN delivers results)
	function m_return()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_RETURN_FILE",$this->template);
		return $this->ObTpl->parse("return","TPL_RETURN_FILE");
	}
	
	#SagePay 3D Page 1
	function m_Sagepay_3D1()
	{
		$strACSURL=$_SESSION["ACSURL"];
		$strPAReq=$_SESSION["PAReq"];
		$strMD=$_SESSION["MD"];
		$strVendorTxCode=$_SESSION["VendorTxCode"];
		$_SESSION["PAReq"]="";
		//error_log("[".time() . "] ".SESSIONID." has reached sagepay3d1. Transaction: ".$_SESSION["VendorTxCode"]."actionurl:".$strACSURL."\n",3,SITE_PATH."sagepay.log");
		$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;3d Secure");
		$this->obTpl->set_var("TPL_VAR_BODY",'
		<p>To increase the security of Internet transactions Visa and Mastercard have introduced 3D-Secure (like an online version of Chip and PIN). <br>
			<br>
		You have chosen to use a card that is part of the 3D-Secure scheme, so you will need to authenticate yourself with your bank in the section below.
						    		</p>
		<form name="form" id="sagepay3d1" action="'.$strACSURL.'" method="POST">
				<input type="hidden" name="PaReq" value="'.$strPAReq.'" />
				<input type="hidden" name="TermUrl" value="'. SITE_SAFEURL . 'ecom/index.php?action=checkout.sage3d2&VendorTxCode=' . $strVendorTxCode.'" />
				<input type="hidden" name="MD" value="'.$strMD.'" />
				<input type="submit" value="Proceed to 3D secure authentication" />
			  </form>
			  <script type="text/javascript">jQuery(document).ready(function() { jQuery("#sagepay3d1").submit();});</script>');
	}
	
	#SagePay 3D Page 2
	function m_Sagepay_3D2()
	{
		$strPaRes=$_REQUEST["PaRes"];
		$strMD=$_REQUEST["MD"];
		$strVendorTxCode=$this->cleaninput($_REQUEST["VendorTxCode"],"VendorTxCode");
		$_SESSION["VendorTxCode"]=$strVendorTxCode;
		//error_log("[".time() . "] ".SESSIONID." has reached sagepay3d2. Transaction: ".$_REQUEST["VendorTxCode"]."\n",3,SITE_PATH."sagepay.log");
		$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;3d Secure");
		$this->obTpl->set_var("TPL_VAR_BODY",'
		<SCRIPT LANGUAGE="Javascript"> jQuery(document).ready(function() {
			jQuery("#sagepay3d2").submit();
		});
		</SCRIPT>'.
		"<FORM id=\"sagepay3d2\" name=\"form\" action=\"".SITE_SAFEURL.'ecom/index.php?action=checkout.sage3dr&VendorTxCode=' . $strVendorTxCode."\" method=\"POST\" target=\"_top\"/>
			<input type=\"hidden\" name=\"PARes\" value=\"" . $strPaRes . "\"/>
			<input type=\"hidden\" name=\"MD\" value=\"" . $strMD . "\"/>
			Redirecting... If you arent redirected, click the button below.<br/><input type=\"submit\" value=\"Go\"/>



			</form>");
	}
	
	
function cleanInput($strRawText,$strType)
{

	if ($strType=="Number") {
		$strClean="0123456789.";
		$bolHighOrder=false;
	}
	else if ($strType=="VendorTxCode") {
		$strClean="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
		$bolHighOrder=false;
	}
	else {
  		$strClean=" ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.,'/{}@():?-_&�$=%~<>*+\"";
		$bolHighOrder=true;
	}
	
	$strCleanedText="";
	$iCharPos = 0;
		
	do
	{
    	// Only include valid characters
		$chrThisChar=substr($strRawText,$iCharPos,1);
			
		if (strspn($chrThisChar,$strClean,0,strlen($strClean))>0) { 
			$strCleanedText=$strCleanedText . $chrThisChar;
		}
		else if ($bolHighOrder==true) {
				// Fix to allow accented characters and most high order bit chars which are harmless 
				if (bin2hex($chrThisChar)>=191) {
					$strCleanedText=$strCleanedText . $chrThisChar;
				}
			}
			
		$iCharPos=$iCharPos+1;
		}
	while ($iCharPos<strlen($strRawText));
		
  	$cleanInput = ltrim($strCleanedText);
	return $cleanInput;

}

	
	#SagePay 3D Page Result
	function m_Sagepay_3DR()
	{
		$strMD = $_REQUEST["MD"];
		$strPaRes=$_REQUEST["PARes"];
		$strVendorTxCode=$_SESSION["VendorTxCode"];

		// POST for Sage Pay Direct 3D completion page
		$strPost = "MD=" . $strMD . "&PARes=" . urlencode($strPaRes);

		//Use cURL to POST the data directly from this server to Sage Pay. cURL connection code is in includes.php.
		$obSaveOrder=new c_saveOrder();
		$arrResponse = $obSaveOrder->requestPost($_SESSION['str3DCallbackPage'], $strPost);
		//Analyse the response from Sage Pay Direct to check that everything is okay
		$arrStatus=split(" ",$arrResponse["Status"]);
		$strStatus=array_shift($arrStatus);
		$arrStatusDetail=split("=",$arrResponse["StatusDetail"]);
		$strStatusDetail = array_shift($arrStatusDetail);
				
		//Get the results form the POST if they are there
		$arrVPSTxId=split(" ",$arrResponse["VPSTxId"]);
		$strVPSTxId=array_shift($arrVPSTxId);
		$arrSecurityKey=split(" ",$arrResponse["SecurityKey"]);
		$strSecurityKey=array_shift($arrSecurityKey);
		$arrTxAuthNo=split(" ",$arrResponse["TxAuthNo"]);
		$strTxAuthNo=array_shift($arrTxAuthNo);
		$arrAVSCV2=split(" ",$arrResponse["AVSCV2"]);
		$strAVSCV2=array_shift($arrAVSCV2);
		$arrAddressResult=split(" ",$arrResponse["AddressResult"]);
		$strAddressResult=array_shift($arrAddressResult);
		$arrPostCodeResult=split(" ",$arrResponse["PostCodeResult"]);
		$strPostCodeResult=array_shift($arrPostCodeResult);
		$arrCV2Result=split(" ",$arrResponse["CV2Result"]);
		$strCV2Result=array_shift($arrCV2Result); 
		$arr3DSecureStatus=split(" ",$arrResponse["3DSecureStatus"]);
		$str3DSecureStatus=array_shift($arr3DSecureStatus);
		$arrCAVV=split(" ",$arrResponse["CAVV"]);
		$strCAVV=array_shift($arrCAVV);

		//Update the database and redirect the user appropriately
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
		
		if ($strStatus=="OK" || $strStatus=="AUTHENTICATED" || $strStatus=="REGISTERED")
		{
			$this->obDb->query= "UPDATE ".ORDERS." SET iPayStatus=1,iOrderStatus=1,v3DSecureStatus='".addslashes($strDBStatus). "' WHERE iTransactionId = '".$strVendorTxCode."'";
			$rs = $this->obDb->updateQuery();
			$this->obDb->query= "SELECT iOrderid_PK FROM ".ORDERS." WHERE iTransactionId = '".$strVendorTxCode."'";
			$rs = $this->obDb->fetchQuery();
			$orderId = $rs[0]->iOrderid_PK;
			$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.process&mode=".$orderId);
			$this->libFunc->m_mosRedirect($retUrl);
		}
		else
		{
			$this->obDb->query= "UPDATE ".ORDERS." SET v3DSecureStatus='".addslashes($strDBStatus)."' WHERE iTransactionId = '".$strVendorTxCode."'";
			$rs = $this->obDb->updateQuery();
			$_SESSION['cardsave_error']=$strDBStatus;
			$retUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.billing");
			$this->libFunc->m_mosRedirect($retUrl);
		}
		exit;
	}
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
		
		#Validates Cardsave Redirect Results
	function m_CardSaveRedirect_Validation()
	{
		
		$WebAddress = SITE_SAFEURL;
		$PreSharedKey = CSR_PREHASH;
		$MerchantID = CSR_MERCHANTID;
		$Password = CSR_MERCHANTPASS;
		$EmailResult = FALSE;
		
		
		$szHashDigest = "";
		$szOutputMessage = "";
		$boErrorOccurred = false;
		$nStatusCode = 30;
		$szMessage = "";
		$nPreviousStatusCode = 0;
		$szPreviousMessage = "";
		$szCrossReference = "";
		$nAmount = 0;
		$nCurrencyCode = 0;
		$szOrderID = "";
		$szTransactionType= "";
		$szTransactionDateTime = "";
		$szOrderDescription = "";
		$szCustomerName = "";
		$szAddress1 = "";
		$szAddress2 = "";
		$szAddress3 = "";
		$szAddress4 = "";
		$szCity = "";
		$szState = "";
		$szPostCode = "";
		$nCountryCode = "";

		try
			{
				// hash digest
				if (isset($_POST["HashDigest"]))
				{
					$szHashDigest = $_POST["HashDigest"];
				}

				// transaction status code
				if (!isset($_POST["StatusCode"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [StatusCode] not received");
					$boErrorOccurred = true;
				}
				else
				{
					if ($_POST["StatusCode"] == "")
					{
						$nStatusCode = null;
					}
					else
					{
						$nStatusCode = intval($_POST["StatusCode"]);
					}
				}
				// transaction message
				if (!isset($_POST["Message"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [Message] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szMessage = $_POST["Message"];
				}
				// status code of original transaction if this transaction was deemed a duplicate
				if (!isset($_POST["PreviousStatusCode"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [PreviousStatusCode] not received");
					$boErrorOccurred = true;
				}
				else
				{
					if ($_POST["PreviousStatusCode"] == "")
					{
						$nPreviousStatusCode = null;
					}
					else
					{
						$nPreviousStatusCode = intval($_POST["PreviousStatusCode"]);
					}
				}
				// status code of original transaction if this transaction was deemed a duplicate
				if (!isset($_POST["PreviousMessage"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [PreviousMessage] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szPreviousMessage = $_POST["PreviousMessage"];
				}
				// cross reference of transaction
				if (!isset($_POST["CrossReference"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [CrossReference] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szCrossReference = $_POST["CrossReference"];
				}
				// amount (same as value passed into payment form - echoed back out by payment form)
				if (!isset($_POST["Amount"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [Amount] not received");
					$boErrorOccurred = true;
				}
				else
				{
					if ($_POST["Amount"] == null)
					{
						$nAmount = null;
					}
					else
					{
						$nAmount = intval($_POST["Amount"]);
					}
				}
				// currency code (same as value passed into payment form - echoed back out by payment form)
				if (!isset($_POST["CurrencyCode"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [CurrencyCode] not received");
					$boErrorOccurred = true;
				}
				else
				{
					if ($_POST["CurrencyCode"] == null)
					{
						$nCurrencyCode = null;
					}
					else
					{
						$nCurrencyCode = intval($_POST["CurrencyCode"]);
					}
				}
				// order ID (same as value passed into payment form - echoed back out by payment form)
				if (!isset($_POST["OrderID"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [OrderID] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szOrderID = $_POST["OrderID"];
				}
				// transaction type (same as value passed into payment form - echoed back out by payment form)
				if (!isset($_POST["TransactionType"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [TransactionType] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szTransactionType = $_POST["TransactionType"];
				}
				// transaction date/time (same as value passed into payment form - echoed back out by payment form)
				if (!isset($_POST["TransactionDateTime"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [TransactionDateTime] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szTransactionDateTime = $_POST["TransactionDateTime"];
				}
				// order description (same as value passed into payment form - echoed back out by payment form)
				if (!isset($_POST["OrderDescription"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [OrderDescription] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szOrderDescription = $_POST["OrderDescription"];
				}
				// customer name (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["CustomerName"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [CustomerName] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szCustomerName = $_POST["CustomerName"];
				}
				// address1 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["Address1"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [Address1] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szAddress1 = $_POST["Address1"];
				}
				// address2 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["Address2"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [Address2] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szAddress2 = $_POST["Address2"];
				}
				// address3 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["Address3"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [Address3] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szAddress3 = $_POST["Address3"];
				}
				// address4 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["Address4"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [Address4] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szAddress4 = $_POST["Address4"];
				}
				// city (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["City"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [City] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szCity = $_POST["City"];
				}
				// state (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["State"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [State] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szState = $_POST["State"];
				}
				// post code (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["PostCode"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [PostCode] not received");
					$boErrorOccurred = true;
				}
				else
				{
					$szPostCode = $_POST["PostCode"];
				}
				// country code (not necessarily the same as value passed into payment form - as the customer can change it on the form)
				if (!isset($_POST["CountryCode"]))
				{
					$szOutputMessage = $this->addStringToStringList($szOutputMessage, "Expected variable [CountryCode] not received");
					$boErrorOccurred = true;
				}
				else
				{
					if ($_POST["CountryCode"] == "")
					{
						$nCountryCode = null;
					}
					else
					{
						$nCountryCode = intval($_POST["CountryCode"]);
					}
				}
			}
		catch (Exception $e)
		{
			$boErrorOccurred = true;
			$szOutputMessage = "Error";
			if (isset($_POST["Message"]))
			{
				$szOutputMessage = $_POST["Message"];
			}
		}
	
	// The nOutputProcessedOK should return 0 except if there has been an error talking to the gateway or updating the website order system.
	// Any other process status shown to the gateway will prompt the gateway to send an email to the merchant stating the error.
	// The customer will also be shown a message on the hosted payment form detailing the error and will not return to the merchants website.
	$nOutputProcessedOK = 0;
	
	if (is_null($nStatusCode))
	{
		$nOutputProcessedOK = 30;		
	}
	
	if ($boErrorOccurred == true)
	{
		$nOutputProcessedOK = 30;
	}
	
	// Check the passed HashDigest against our own to check the values passed are legitimate.
	$str1 = $_POST["HashDigest"];
	$hashcode = $this->createhash($PreSharedKey,$Password);
	if ($hashcode != $str1) {
		$nOutputProcessedOK = 30; 
		$szOutputMessage = "Hashes did not match";
	} 

	// *********************************************************************************************************
	// You should put your code that does any post transaction tasks
	// (e.g. updates the order object, sends the customer an email etc) in this section
	// *********************************************************************************************************
	if ($nOutputProcessedOK != 30)
		{	
			$nOutputProcessedOK = 0;
			$szOutputMessage = $szMessage;
			try
			{
				switch ($nStatusCode)
				{
					// transaction authorised
					case 0:
						$transauthorised = true;
						break;
					// card referred (treat as decline)
					case 4:
						$transauthorised = false;
						break;
					// transaction declined
					case 5:
						$transauthorised = false;
						break;
					// duplicate transaction
					case 20:
						// need to look at the previous status code to see if the
						// transaction was successful
						if ($nPreviousStatusCode == 0)
						{
							// transaction authorised
							$transauthorised = true;
						}
						else
						{
							// transaction not authorised
							$transauthorised = false;
						}
						break;
					// error occurred
					case 30:
						$transauthorised = false;
						break;
					default:
						$transauthorised = false;
						break;
				}
			
				if ($transauthorised == true) {
					// put code here to update/store the order with the a successful transaction result
					$this->obDb->query = "UPDATE ".ORDERS." SET iPayStatus=1 WHERE iOrderid_PK=".$_POST["OrderID"];
					$row=$this->obDb->updateQuery();
				} else {
					// put code here to update/store the order with the a failed transaction result
				}
			}
			catch (Exception $e)
			{
				$nOutputProcessedOK = 30;
				$szOutputMessage = "Error updating website system, please ask the developer to check code";
			}
        }

	if ($nOutputProcessedOK != 0 && $szOutputMessage == "")
	{
		$szOutputMessage = "Unknown error";
	}
	
	// output the status code and message letting the payment form
	// know whether the transaction result was processed successfully
	echo("StatusCode=".$nOutputProcessedOK."&Message=".$szOutputMessage);
		
	}
	
	function createhash($PreSharedKey,$Password) { 
			$str="PreSharedKey=" . $PreSharedKey;
			$str=$str . '&MerchantID=' . $_POST["MerchantID"];
			$str=$str . '&Password=' . $Password;
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
		
		// String together other strings using a "," as a seperator.
		function addStringToStringList($szExistingStringList, $szStringToAdd)
		{
			$szReturnString = "";
			$szCommaString = "";

			if (strlen($szStringToAdd) == 0)
			{
				$szReturnString = $szExistingStringList;
			}
			else
			{
				if (strlen($szExistingStringList) != 0)
				{
					$szCommaString = ", ";
				}
				$szReturnString = $szExistingStringList.$szCommaString.$szStringToAdd;
			}

			return ($szReturnString);
		}
}#END CLASS
?>