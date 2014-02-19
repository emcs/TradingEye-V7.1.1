<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
class c_review
{
#CONSTRUCTOR
	function c_review()
	{
		$this->err=0;
		$this->credit=0;
		$this->solo=0;
		$this->subTotal=0;
		$this->volDiscount=0;
		$this->grandTotal=0;
		$this->postagePrice=0;
		$this->totalQty=0;
		$this->checkout=0;
		$this->giftCertPrice=0;
		$this->discountPrice=0;
		$this->taxTotal=0;
		$this->postageTotal=0;
		$this->cartWeight=0;
		$this->offertype="";
		$this->minAmount=0;
		$this->libFunc=new c_libFunctions();
	}
	
	#FUNCTION TO DISPLAY USER FORM
	function m_reviewCheckout()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
	
		
		if(count($_SESSION)==0)
		{
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);
		}
		#**Start: Reverify if the selected country carries correct shipping charges for international postage**
		if($_SESSION['postagedropdown'] == "1" && DEFAULT_POSTAGE_METHOD=='zones'){
             $postagePacking = $comFunc->m_recalculate_postage($_SESSION['ship_country_id']);
             if($postagePacking[0] != ""){
                 $_SESSION['postagePrice'] = $postagePacking[0];
                 if($postagePacking[1] != ""){
                    $_SESSION['zoneSpecialDelivery'] = $postagePacking[1];
                 }
             }
         }
        
        if($_SESSION['postagedropdown'] == "1" && DEFAULT_POSTAGE_METHOD=='cities'){
             $postagePacking = $comFunc->m_recalculate_postage($_SESSION['ship_country_id'],$_SESSION['ship_state_id']);
             if($postagePacking[0] != ""){
                 $_SESSION['postagePrice'] = $postagePacking[0];
                 if($postagePacking[1] != ""){
                    $_SESSION['citySpecialDelivery'] = $postagePacking[1];
                 }
             }
         }
		#**End: Reverify if the selected country carries correct shipping charges for international postage**
		#QUERY TEMPARARY CART & PRODUCT TABLE
		$this->obDb->query	 ="SELECT vTitle,vSeoTitle,fPrice,fRetailPrice,vSku,iQty,iTmpCartId_PK,iProdId_FK,vImage1,";
		$this->obDb->query	.="iKit,iGiftWrap,fVolDiscount,iTaxable,fItemWeight,iFreeShip,iOnorder,";  
		$this->obDb->query .="vShipCode,vShipNotes,tmDuedate ";
		#EXTRA SPACE IN FRONT OF FROM
		$this->obDb->query	.=" FROM ".TEMPCART." T,".PRODUCTS." P WHERE ";
		#EXTRA SPACE
		$this->obDb->query	.=" iProdId_FK=iProdId_PK AND  vSessionId='".SESSIONID."'";
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
		$rowCartCount=$this->obDb->record_count;
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_REVIEW_FILE",$this->reviewTemplate);
		
		
		#SETTING BLOCKS
		$this->ObTpl->set_block("TPL_REVIEW_FILE","TPL_CART_BLK","cart_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAR_CARTPRODUCTS","cartproduct_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_DELIVERY_BLK","delivery_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_GIFTCERT_BLK","giftcert_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_DISCOUNT_BLK","discount_blk");
		$this->ObTpl->set_block("TPL_VAR_CARTPRODUCTS","TPL_KIT_BLK","kit_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_MPOINTS_BLK","memberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_CARTWEIGHT_BLK","cartWeight_blk");		
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_USEDMEMBERPOINTS_BLK","usedMemberPoint_blk");

		$this->ObTpl->set_block("TPL_CART_BLK","TPL_COD_BLK","cod_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_PROMODISCOUNTS_BLK","promodiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VOLDISCOUNTS_BLK","volDiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_POSTAGE_BLK","postage_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAT_BLK","vat_blk");

		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_SAFEURL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_VAT",'');
		$this->ObTpl->set_var("TPL_VAR_TAXNAME", VAT_TAX_TEXT);


		$this->ObTpl->set_var("delivery_blk","");	
		$this->ObTpl->set_var("cart_blk","");	
		$this->ObTpl->set_var("cartWeight_blk","");	
		$this->ObTpl->set_var("giftcert_blk","");	
		$this->ObTpl->set_var("discount_blk","");	
		$this->ObTpl->set_var("memberpoint_blk","");	
		$this->ObTpl->set_var("usedMemberPoint_blk","");	
		$this->ObTpl->set_var("promodiscounts_blk","");	
		$this->ObTpl->set_var("volDiscounts_blk","");	
		$this->ObTpl->set_var("postage_blk","");		
		$this->ObTpl->set_var("cod_blk","");	
		$this->ObTpl->set_var("gift_blk","");	
		$this->ObTpl->set_var("cartproduct_blk","");	
		$this->ObTpl->set_var("kit_blk","");	
		$this->ObTpl->set_var("vat_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT","");
		$this->ObTpl->set_var("TPL_VAR_MESSAGE","");
		$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING","");
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_REVIEWYOURORDER",LANG_REVIEWORDERTXT);
		$this->ObTpl->set_var("LANG_VAR_PAYMENTMETHOD",LANG_PAYMENTMETHODTXT);
		$this->ObTpl->set_var("LANG_VAR_POSTAGEMETHOD",LANG_POSTAGEMETHODTXT);
		$this->ObTpl->set_var("LANG_VAR_BILLINGADDRESS",LANG_BILLINGADDRESS);
		$this->ObTpl->set_var("LANG_VAR_DELIVERYADDRESS",LANG_DELIVERYADDRESS);
		$this->ObTpl->set_var("LANG_VAR_QUANTITY",LANG_QUANTITY);
		$this->ObTpl->set_var("LANG_VAR_PRODUCT",LANG_PRODUCT);
		$this->ObTpl->set_var("LANG_VAR_PRICE",LANG_PRICE);
		$this->ObTpl->set_var("LANG_VAR_TOTAL",LANG_TOTAL);
		$this->ObTpl->set_var("LANG_VAR_ACCUMULATES",LANG_ACCUMULATE);
		$this->ObTpl->set_var("LANG_VAR_MEMPOINTS",LANG_REWARDPOINTS);
		$this->ObTpl->set_var("LANG_VAR_SUBTOTAL",LANG_SUBTOTAL);
		$this->ObTpl->set_var("LANG_VAR_VOLUME",LANG_VOLUMEDISCOUNT);
		$this->ObTpl->set_var("LANG_VAR_PRODUCTWEIGHT",LANG_PRODUCTWEIGT);
		$this->ObTpl->set_var("LANG_VAR_VIEWCARTIMAGES",LANG_VIEWCARTIMAGE);

		$_SESSION['alt_ship']=$this->libFunc->ifSet($_SESSION,"alt_ship",0);
		if($_SESSION['alt_ship']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING",MSG_SAMEASBILLING);
		}
		else
		{
			$this->ObTpl->parse("delivery_blk","TPL_DELIVERY_BLK");
		}
			
		#MESSAGE HANDLING
		$mode=$this->libFunc->ifSet($this->request,'mode','0');
		$mode1=$this->libFunc->ifSet($this->request,'mode1','0');
		$hsbcerr=$this->libFunc->ifSet($this->request,'errhsbc','0');
		$securetraderr=$this->libFunc->ifSet($this->request,'securetrad','0');
		
		if($hsbcerr==1)
		{
		    $msg=$this->hsbcmessages();		
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",$msg);	
		}
		elseif($mode=='cancel' || $mode1=='cancel')
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_PAYPAL_CANCEL);	
		}
		elseif(!empty($this->errMsg))
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",$this->errMsg);	
		}


		#FORM URL
		$formUrl=$this->libFunc->m_safeUrl(SITE_SAFEURL."ecom/index.php?action=checkout.saveorder");
		$this->ObTpl->set_var("TPL_VAR_FORMURL",$formUrl);
			
		#SETTING BILL STATENAME
		if($this->libFunc->ifSet($_SESSION,'bill_state_id','0'))
		{
			$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$_SESSION['bill_state_id']."'";
			$row_state = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_BILLSTATE",					$this->libFunc->m_displayContent($row_state[0]->vStateName));
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$_SESSION['bill_state']);
		}
		
		#SETTING BILL COUNTRY NAME
		$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$_SESSION['bill_country_id']."'";
		$row_country = $this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",
		$this->libFunc->m_displayContent($row_country[0]->vCountryName));
		
		#SETTING SHIP STATENAME
		if($this->libFunc->ifSet($_SESSION,'ship_state_id','0'))
		{
			$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$_SESSION['ship_state_id']."'";
			$row_state = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",					$this->libFunc->m_displayContent($row_state[0]->vStateName));
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
		$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",$this->libFunc->m_displayContent($_SESSION['company']));
		$this->ObTpl->set_var("TPL_VAR_MPOINTS","");
	
		#SETTING SHIPPING INFO
		$this->ObTpl->set_var("TPL_VAR_ALTNAME",$this->libFunc->m_displayContent($_SESSION['alt_name']));
		$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($_SESSION['alt_address1']));
		$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($_SESSION['alt_address2']));
		$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($_SESSION['alt_city']));
		$this->ObTpl->set_var("TPL_VAR_ALTCOMPANY",$this->libFunc->m_displayContent($_SESSION['alt_company']));
		
		$this->ObTpl->set_var("SHIP_STATE","");
		$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($_SESSION['alt_zip']));
		$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($_SESSION['alt_phone']));

		#POST VARIABLES**********************************************
		$this->request['paymethod'] = $_SESSION['payMethod'];
				
		$_SESSION['payMethod']=$this->libFunc->ifSet($_SESSION,'payMethod','none');
		if($this->libFunc->ifSet($this->request,'paymethod','0'))
		{
			$_SESSION['payMethod']=$this->request['paymethod'];
		}
		if($this->libFunc->ifSet($this->request,'mail_list','0'))
		{
			$_SESSION['mail_list']=$this->request['mail_list'];
		}
		#PAYMENT METHOD

		if($this->libFunc->ifSet($this->request,'comments','0'))
		{
			$_SESSION['comments']=$this->libFunc->m_displayContent($this->request['comments']);
		}
		if($_SESSION['comments']=='special requirements')
		{
			$_SESSION['comments']='';
		}
		
		if($this->libFunc->ifSet($_SESSION,'discountCode','')){
			$discountstring=$comFunc->m_calculateDiscount($_SESSION['discountCode']);
			$discountarray = explode(",",$discountstring);
			$this->discountPrice=$discountarray[0];
			$this->offertype=$discountarray[1];
			$this->minAmount=$discountarray[2];
		}
		
		if($this->libFunc->ifSet($_SESSION,'giftCertCode','')){
			$this->giftCertPrice=$comFunc->m_calculateGiftCertPrice($_SESSION['giftCertCode']);
		}
		
		// Begin Card Holder Protx Modification 
                if($this->libFunc->ifSet($this->request,'cardholder_name',''))
                {
                    $_SESSION['cardholder_name']=$this->request['cardholder_name'];
                }
                else
                {
                  $_SESSION['cardholder_name']=$this->libFunc->ifSet($_SESSION,'cardholder_name','');
                }
			// End Card Holder Protx Modification 
		if($this->libFunc->ifSet($this->request,'cc_number',''))
		{
			$_SESSION['cc_number']=$this->request['cc_number'];
		}
		else
		{
			$_SESSION['cc_number']=$this->libFunc->ifSet($_SESSION,'cc_number','');
		}
		
		if($this->libFunc->ifSet($this->request,'cc_type',''))
		{
			$_SESSION['cc_type']=$this->request['cc_type'];
		}
		else
		{
			$_SESSION['cc_type']=$this->libFunc->ifSet($_SESSION,'cc_type','');
		}
		
		if($this->libFunc->ifSet($this->request,'cv2',''))
		{
			$_SESSION['cv2']=$this->request['cv2'];
		}
		else
		{
			$_SESSION['cv2']=$this->libFunc->ifSet($_SESSION,'cv2','');
		}
		
		if($this->libFunc->ifSet($this->request,'cc_year',''))
		{
			$_SESSION['cc_year']=$this->request['cc_year'];
		}
		else
		{
			$_SESSION['cc_year']=$this->libFunc->ifSet($_SESSION,'cc_year','');
		}

		if($this->libFunc->ifSet($this->request,'cc_month','0'))
		{
			$_SESSION['cc_month']=$this->request['cc_month'];
		}
		else
		{
			$_SESSION['cc_month']=$this->libFunc->ifSet($_SESSION,'cc_month','');
		}

		if($this->libFunc->ifSet($this->request,'cc_start_year','0'))
		{
			$_SESSION['cc_start_year']=$this->request['cc_start_year'];
		}
		else
		{
			$_SESSION['cc_start_year']=$this->libFunc->ifSet($_SESSION,'cc_start_year','');
		}

		if($this->libFunc->ifSet($this->request,'cc_start_month','0'))
		{
			$_SESSION['cc_start_month']=$this->request['cc_start_month'];
		}
		else
		{
			$_SESSION['cc_start_month']=$this->libFunc->ifSet($_SESSION,'cc_start_month','');
		}

		if($this->libFunc->ifSet($this->request,'issuenumber','0'))
		{
			$_SESSION['issuenumber']=$this->request['issuenumber'];
		}
		else
		{
			$_SESSION['issuenumber']=$this->libFunc->ifSet($_SESSION,'issuenumber','');
		}

		if($this->libFunc->ifSet($this->request,'acct','0'))
		{
			$_SESSION['acct']=$this->request['acct'];
		}
		else
		{
			$_SESSION['acct']=$this->libFunc->ifSet($_SESSION,'acct','0');
		}

		if($this->libFunc->ifSet($this->request,'aba','0'))
		{
			$_SESSION['aba']=$this->request['aba'];
		}
		else
		{
			$_SESSION['aba']=$this->libFunc->ifSet($_SESSION,'aba','0');
		}

		#*************************************************************		

		$this->ObTpl->set_var("TPL_VAR_PAYMENTMETHOD", $comFunc->m_paymentMethod($_SESSION['payMethod'],$_SESSION['codPrice']));
		$this->ObTpl->set_var("TPL_VAR_PAYMETHOD",$_SESSION['payMethod']);
		$this->ObTpl->set_var("TPL_VAR_CCNUMBER",$_SESSION['cc_number']);
		$this->ObTpl->set_var("TPL_VAR_CCTYPE",$_SESSION['cc_type']);
		$this->ObTpl->set_var("TPL_VAR_CV2",$_SESSION['cv2']);
		$this->ObTpl->set_var("TPL_VAR_CCYEAR",$_SESSION['cc_year']);
		$this->ObTpl->set_var("TPL_VAR_CCMONTH",$_SESSION['cc_month']);
		$this->ObTpl->set_var("TPL_VAR_STARTYEAR",$_SESSION['cc_start_year']);
		$this->ObTpl->set_var("TPL_VAR_STARTMONTH",$_SESSION['cc_start_month']);
		$this->ObTpl->set_var("TPL_VAR_ISSUENUMBER",$_SESSION['issuenumber']);
		$this->ObTpl->set_var("TPL_VAR_ACCTNUMBER",$_SESSION['acct']);
		$this->ObTpl->set_var("TPL_VAR_ABA_ACCT",$_SESSION['aba']);
		
		
		
		
		#DISPLAY CART PRODUCT
		 if($rowCartCount>0)
		{
			
			if ( !is_null($_SESSION['totalVendor']) && $_SESSION['totalVendor'] > 0){
				$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER",$_SESSION['totalVendor']);
			} else {
				$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER","");
			}
				
			for($i=0;$i<$rowCartCount;$i++)
			{

				$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT","");
				$this->ObTpl->set_var("TPL_VAR_BACKORDER","");		
				$this->ObTpl->set_var("TPL_VAR_OPTIONS","");
				$this->ObTpl->set_var("TPL_VAR_CHOICES","");
				$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","");
				$this->ObTpl->set_var("TPL_VAR_TAXABLE","");
				$this->ObTpl->set_var("TPL_VAR_SHIPNOTES","");
				$this->ObTpl->set_var("kit_blk","");	
				$this->price=0;#INTIALIZING
				$this->total=0;

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
				if ($comFunc->m_checkCustomerType()==1 && ENABLE_WHOLESALE==1  && $rowCart[$i]->fRetailPrice>0){
				$rowCart[$i]->fPrice=$rowCart[$i]->fRetailPrice;
				}
				//----End switch price	
				#BACK ORDERED
				if(isset($_SESSION['backorder'][$rowCart[$i]->iProdId_FK])  && $_SESSION['backorder'][$rowCart[$i]->iProdId_FK]==1)
				{
					$strBackOrder="This item is on backorder";
					
					if($rowCart[$i]->iOnorder>0)
					{
						$strBackOrder.="<br />On Order: ".$rowCart[$i]->iOnorder;
					}

					if(!empty($rowCart[$i]->tmDuedate))
					{
						$formatedDueDate=$this->libFunc->dateFormat2($rowCart[$i]->tmDuedate);
						$strBackOrder.=" (Due date: ".$formatedDueDate.")";
					}
					$this->ObTpl->set_var("TPL_VAR_BACKORDER",$strBackOrder);		
				}

				$this->ObTpl->set_var("TPL_VAR_CARTID",$rowCart[$i]->iTmpCartId_PK);
				
				$comFunc->cartId=$rowCart[$i]->iTmpCartId_PK;
				
				#FOR POSTAGE-CODES
				$comFunc->productId=$rowCart[$i]->iProdId_FK;
				$comFunc->qty=$rowCart[$i]->iQty;
				$comFunc->price=$this->price;

				##GIFTWRAP URL
				if($rowCart[$i]->iGiftWrap!=0)
				{
					$this->ObTpl->set_var("gift_blk","");
					$this->ObTpl->set_var("TPL_VAR_GIFTWRAP",$comFunc->m_dspGiftWrap($rowCart[$i]->iGiftWrap,$rowCart[$i]->iTmpCartId_PK));
				}
				else
				{		
					$this->ObTpl->set_var("TPL_VAR_GIFTWRAP","");
					$giftWrapUrl=SITE_SAFEURL."ecom/index.php?action=ecom.giftwrap&mode=".$rowCart[$i]->iTmpCartId_PK;
					$this->ObTpl->set_var("TPL_VAR_GIFTWRAPURL",$this->libFunc->m_safeUrl($giftWrapUrl));	
					$this->ObTpl->parse("gift_blk","TPL_GIFTWRAP_BLK");
				}
		
				if($rowCart[$i]->iKit==1)
				{
					$this->obDb->query = "SELECT vTitle,iProdId_FK,vSku FROM ".PRODUCTKITS.",".PRODUCTS." WHERE iProdId_FK=iProdId_PK AND iKitId='".$rowCart[$i]->iProdId_FK."'";
					$rsKit=$this->obDb->fetchQuery();
					$rsKitCount=$this->obDb->record_count;
					for($j=0;$j<$rsKitCount;$j++)
					{
						$comFunc->kitProductId=$rsKit[$j]->iProdId_FK;
						#GET KIT OPTIONS
						$kitOptions=$comFunc->m_dspCartProductKitOptions();
						if($kitOptions==' ')
						{
							$this->ObTpl->set_var("TPL_VAR_KITOPTIONS","");
						}
						else
						{
							$this->ObTpl->set_var("TPL_VAR_KITOPTIONS",$kitOptions);
						}

						$this->ObTpl->set_var("TPL_VAR_KITSKU",$this->libFunc->m_displayContent($rsKit[$j]->vSku));
						$this->ObTpl->set_var("TPL_VAR_KITTITLE",$this->libFunc->m_displayContent($rsKit[$j]->vTitle));
						$this->ObTpl->parse("kit_blk","TPL_KIT_BLK",true);	
					}
				}
				else
				{
					#GET CART OPTIONS
					$this->ObTpl->set_var("TPL_VAR_OPTIONS",$comFunc->m_dspCartProductOptions());
					#GET CART CHOICES
					$this->ObTpl->set_var("TPL_VAR_CHOICES",$comFunc->m_dspCartProductChoices());
				}

				# (OPTION And choice effected amount)
				$this->price=$comFunc->price;	

				#VOLUME DISCOUNT**************************************************
				#DISCOUNT ACCORDING TO QTY
				$vDiscoutPerItem=number_format($rowCart[$i]->fVolDiscount,2,'.','');
				if($vDiscoutPerItem>0)
				{
					$vDiscountPerCartElement=number_format(($rowCart[$i]->iQty*$vDiscoutPerItem),2,'.','');
					$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT",
					"Volume Discount: ".CONST_CURRENCY.$vDiscoutPerItem." each - Total: ".CONST_CURRENCY.$vDiscountPerCartElement."<br />");
					$this->volDiscount=$this->volDiscount+$vDiscountPerCartElement;
				}
				
				$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rowCart[$i]->vTitle));
				//$this->ObTpl->set_var("TPL_VAR_CARTIMAGE",$this->libFunc->m_displayContent($rowCart[$i]->vImage1));
				 if ($this->libFunc->m_displayContent($rowCart[$i]->vImage1) != "") {
				$this->ObTpl->set_var("TPL_VAR_CARTIMAGE_TAG","<img src=\"".SITE_SAFEURL. "libs/timthumb.php?src=/images/product/" . $this->libFunc->m_displayContent($rowCart[$i]->vImage1) . "&amp;h=70&amp;w=70&amp;zc=r\" alt=\"" . $this->libFunc->m_displayContent($rowCart[$i]->vTitle) . "\" />");
				 } else {
					$this->ObTpl->set_var("TPL_VAR_CARTIMAGE_TAG","No image available");
				}
				
				$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rowCart[$i]->vSku));

				$this->price=$this->price+$rowCart[$i]->fPrice;
				$fullprice = $this->price;
				
				if ($rowCart[$i]->iTaxable == 1)
				{
					if (NETGROSS == 1)
						{
							$vatPercent = $this->libFunc->m_vatCalculate();
							$actualprice = $this->price * ($vatPercent/100 + 1);
							$vatAmount = ( $this->price * ($vatPercent/100))*$rowCart[$i]->iQty ;
							//$this->price = $actualprice;
						}
				}
				

				$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($this->price,2,'.',''));

				$this->ObTpl->set_var("TPL_VAR_QTY",$rowCart[$i]->iQty);
				$this->totalQty+=$rowCart[$i]->iQty;

				$this->total+=$rowCart[$i]->iQty*$this->price;
				$this->ObTpl->set_var("TPL_VAR_TOTAL",number_format($this->total,2,'.',''));
			
				
				if($rowCart[$i]->iFreeShip !=1)
				{
					$this->postageTotal+=$this->total;
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","<em>Free P&amp;P</em><br />");
				}
				if($rowCart[$i]->iTaxable ==1)
				{
					if (NETGROSS == 1)
						{
							$this->taxTotal +=$vatAmount;
						}else{
							$this->taxTotal += ($this->price*$rowCart[$i]->iQty);	
						}
				if ($comFunc->m_checkCustomerType()==1 && ENABLE_WHOLESALE==1  && $rowCart[$i]->fRetailPrice>0){
				
					$this->taxTotal = $this->taxTotal - (($rowCart[$i]->fPrice - $rowCart[$i]->fRetailPrice)*$rowCart[$i]->iQty);
				}
				} else 
				{
					if (HIDENOVAT != 1) {
						$this->ObTpl->set_var("TPL_VAR_TAXABLE","<em>".LBL_NOTAX."</em><br />");
					} else {
						$this->ObTpl->set_var("TPL_VAR_TAXABLE","");
					}
				}				
				//Quantity Multiplied
				if($rowCart[$i]->fItemWeight>0)
				{
					$this->cartWeight+=$rowCart[$i]->fItemWeight*$rowCart[$i]->iQty;
				}
				$this->subTotal=$this->subTotal+$this->total;
				#SAFE URLS
				$removeUrl=SITE_SAFEURL."ecom/index.php?action=ecom.remove&mode=".$rowCart[$i]->iTmpCartId_PK;
				$this->ObTpl->set_var("TPL_VAR_REMOVEURL",$this->libFunc->m_safeUrl($removeUrl));	

				$cartUpdateUrl=SITE_SAFEURL."ecom/index.php?action=ecom.updateCart";
				$this->ObTpl->set_var("TPL_VAR_UPDATEURL",$this->libFunc->m_safeUrl($cartUpdateUrl));	
	
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowCart[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	

				if(!empty($rowCart[$i]->vShipNotes))
				{
				$this->ObTpl->set_var("TPL_VAR_SHIPNOTES","Notes: ".$this->libFunc->m_displayContent($rowCart[$i]->vShipNotes)."<br />");
				}
				
				$this->ObTpl->parse("cartproduct_blk","TPL_VAR_CARTPRODUCTS",true);	
			}
					
			
				//echo $this->taxTotal."<br/>";
			$this->ObTpl->set_var("TPL_VAR_SUBTOTAL",number_format($this->subTotal,2,'.',''));
			$this->grandTotal=$this->subTotal;
			#***************MEMBER POINTS ON SUB TOTAL****************
			if(OFFERMPOINT==1)
			{
				$this->memPoints=MPOINTCALCULATION*$this->subTotal;
				$_SESSION['memberPointsEarned']=floor($this->memPoints);
				$this->ObTpl->set_var("TPL_VAR_MPOINTS",floor($this->memPoints));
				$this->ObTpl->parse("memberpoint_blk","TPL_MPOINTS_BLK");
			}
			
			#************************* PROMOTION DISCOUNTS*********
			$this->promotionDiscount=$comFunc->m_calculatePromotionDiscount($this->subTotal);
			if($this->promotionDiscount>=0)
			{
				if($this->promotionDiscount==0)
				{
					$displayDiscount='No Charge';
				}
				else
				{
					$displayDiscount="-".CONST_CURRENCY.number_format($this->promotionDiscount,2,'.','');
				}
				if(isset($comFunc->PromotionDesc) && !empty($comFunc->PromotionDesc))
				{
					$this->ObTpl->set_var("TPL_VAR_PROMOTIONDESC",$comFunc->PromotionDesc);
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_PROMOTIONDESC","Promotion Discounts");
				}
				$this->ObTpl->set_var("TPL_VAR_PDISCOUNTS",$displayDiscount);
				$_SESSION['promotionDiscountPrice']=$this->promotionDiscount;
				$this->grandTotal-=$this->promotionDiscount;
				$this->taxTotal-=$this->promotionDiscount;
				//echo $this->taxTotal."<br/>";
				$this->ObTpl->parse("promodiscounts_blk","TPL_PROMODISCOUNTS_BLK");
			}
			else
			{
				$_SESSION['promotionDiscountPrice']=0;
			}
			if($this->volDiscount>0)
			{
				$this->ObTpl->set_var("TPL_VAR_VOLDISCOUNT",number_format($this->volDiscount,2,'.',''));
				$this->grandTotal-=$this->volDiscount;
				$this->taxTotal-=$this->volDiscount;
				$this->ObTpl->parse("volDiscounts_blk","TPL_VOLDISCOUNTS_BLK");
			}	
			

			#COD PRICE(PAYMENT GATEWAY ADDITIONAL PRICE)
			if($_SESSION['codPrice']>0)
			{
				$this->ObTpl->set_var("TPL_VAR_CODPRICE",number_format($_SESSION['codPrice'],2,'.',''));
				$this->grandTotal+=number_format($_SESSION['codPrice'],2,'.','');
				$this->ObTpl->parse("cod_blk","TPL_COD_BLK");
			}
			$this->ObTpl->set_var("TPL_VAR_DISCOUNTCODE","");
			$this->ObTpl->set_var("TPL_VAR_GIFTCODE","");
			
			#CHECK FOR DISCOUNTS
			if($this->discountPrice!=0 )
			{
				if($this->grandTotal > $this->minAmount)
				{
					if ($this->offertype =="percent") {
						$discountedPrice = round($this->discountPrice * (($this->grandTotal ) / 100),2);
					} else {
						if($this->discountPrice > $this->grandTotal)
						{
							$this->discountPrice = $this->grandTotal;
						}
						$discountedPrice = round($this->discountPrice,2);
					}
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTCODE","(".$_SESSION['discountCode'].")");
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE",number_format($discountedPrice,2,'.',''));
									   
					if ($this->taxTotal > 0) {
						$this->taxTotal-=($discountedPrice);
						$this->grandTotal-=$discountedPrice;
					} else {
						$this->grandTotal-=$discountedPrice;
						//No VAT on order so do not adjust the VAT
					   
					}
				//echo $this->taxTotal."<br/>";
					$_SESSION['discountPrice']=$discountedPrice;
					$this->ObTpl->parse("discount_blk","TPL_DISCOUNT_BLK");
				}else{
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTCODE","(".$_SESSION['discountCode'].") Discount minimum is not reached ");
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE","0.00");
					$this->ObTpl->parse("discount_blk","TPL_DISCOUNT_BLK");
				}
			}elseif($this->libFunc->ifSet($_SESSION,'discountCode','0')  && $_SESSION['discountCode']!='discount code')
            {
                $this->ObTpl->set_var("TPL_VAR_DISCOUNTCODE","(".$_SESSION['discountCode'].") not found");
                $this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE","0.00");
                $this->ObTpl->parse("discount_blk","TPL_DISCOUNT_BLK");   
            }

			#CHECK FOR GIFTCERTIFICATES
			if($this->giftCertPrice!=0)
			{
				if($this->grandTotal<$this->giftCertPrice)
				{
					$this->giftCertPrice=$this->grandTotal;
				}
				if($this->grandTotal <= 0)
				{
				$this->giftCertPrice = 0;
				$this->grandTotal = 0;
				}
				$this->taxTotal-=	$this->giftCertPrice;	
				//echo $this->taxTotal."<br/>";
				$this->grandTotal-=$this->giftCertPrice;
				$_SESSION['giftCertPrice']=$this->giftCertPrice;	
				$this->ObTpl->set_var("TPL_VAR_GIFTCERTPRICE",number_format($this->giftCertPrice,2,'.',''));
				$this->ObTpl->parse("giftcert_blk","TPL_GIFTCERT_BLK");	
			}
			elseif($this->libFunc->ifSet($_SESSION,'giftCertCode','0') && $_SESSION['giftCertCode']!='gift certificate number')
			{
				$this->ObTpl->set_var("TPL_VAR_GIFTCODE","(".$_SESSION['giftCertCode'].") not found");
				$this->ObTpl->set_var("TPL_VAR_GIFTCERTPRICE","0.00");
				$this->ObTpl->parse("giftcert_blk","TPL_GIFTCERT_BLK");	
			}
			
			#TO USE MEMBER POINTS*****************************************
			if(isset($_SESSION['useMemberPoints']) && $_SESSION['useMemberPoints']=='yes' && OFFERMPOINT==1) {
					//Get the total points that will be enough to use in order.
				## OrderAmount = 100
				## PointValue = 5
				## TotalPointsEnough = 20
				//Deduct only these points on checkout process and leave the rest of the points
				if(MPOINTVALUE > 0)
					$pointsRequired=$this->grandTotal/MPOINTVALUE;
				else
					$pointsRequired = 0;

				if($_SESSION['memberPoints']>=$pointsRequired)
				{
					$_SESSION['usedMemberPoints']=$pointsRequired;
				}
				else
				{
					$_SESSION['usedMemberPoints']=$_SESSION['memberPoints'];
				}
				#Amount using member Points
				$_SESSION['memberPointsUsedAmount']=$_SESSION['usedMemberPoints']*MPOINTVALUE;

				#SETTING TEMPLATE VARIABLE FOR MEMBER POINTS
				if(isset($_SESSION['memberPointsUsedAmount'])  && isset($_SESSION['usedMemberPoints']))
				{	
					$this->ObTpl->set_var("TPL_VAR_MPOINTSAVAIABLE",floor($_SESSION['usedMemberPoints']));
					$this->ObTpl->set_var("TPL_VAR_MPOINTSPRICE",number_format($_SESSION['memberPointsUsedAmount'],2,'.',''));		

					#Modified Total
					$this->grandTotal-=$_SESSION['memberPointsUsedAmount'];
					#SUBTRACTING MEMBERPOINTS	
					$this->taxTotal-=$_SESSION['memberPointsUsedAmount'];
				//echo $this->taxTotal."<br/>";
					$this->ObTpl->parse("usedMemberPoint_blk","TPL_USEDMEMBERPOINTS_BLK");
				}
			}

			if($this->taxTotal<0)
			{
				$this->taxTotal=0;
			}
			
			#POSTAGE CALCULATION**************************
			$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",number_format($_SESSION['postagePrice2'],2,'.',''));
			$this->grandTotal += $_SESSION['postagePrice2'];
			$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$_SESSION['postagemethodname']);
			$this->ObTpl->parse("postage_blk", "TPL_POSTAGE_BLK");
			
		 	$temp = $comFunc->m_Calculate_Tax($this->taxTotal,$_SESSION['postagePrice2'],$_SESSION['ship_country_id'],$_SESSION['ship_state_id']);
			$this->vatTotal = $temp[0];	
			
			
			$this->ObTpl->set_var("TPL_VAR_VAT",$temp[1]);
			if($this->vatTotal>0)
			{
				$this->ObTpl->set_var("TPL_VAR_VATPRICE",number_format($this->vatTotal,2,'.',''));
				$this->grandTotal+=$this->vatTotal;
				$this->ObTpl->parse("vat_blk","TPL_VAT_BLK");
			}
			$_SESSION['vatTotal']=$this->vatTotal;
			$_SESSION['VAT']=$temp[1];
			$_SESSION['totalQty']=$this->totalQty;
			$this->grandTotal = ceil($this->grandTotal * 1000)/1000;
			$_SESSION['grandTotal']=$this->grandTotal;

			$this->ObTpl->set_var("TPL_VAR_CURRENTTOTAL",number_format($this->grandTotal,2,'.',''));
			$this->ObTpl->parse("cart_blk","TPL_CART_BLK");	
		}
		else
		{
			$returnUrl=SITE_URL."index.php";
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_CART_EMPTY." <a href=".$this->libFunc->m_safeUrl($returnUrl).">".MSG_RETURN."</a>");
		}
		return $this->ObTpl->parse("return","TPL_REVIEW_FILE");
	}
}#END CLASS
?>
