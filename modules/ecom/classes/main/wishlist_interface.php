<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_wishInterface
{
#CONSTRUCTOR
	function c_wishInterface()
	{
		$this->templatePath=THEMEPATH."ecom/templates/main/";
		$this->pageTplPath=THEMEPATH."default/templates/main/";
		$this->largeImage="largeImage.tpl.htm";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize="100";
		$this->libFunc=new c_libFunctions();
	}

	#FUNCTION TO DISPLAY PRODUCT DETAILS
	function m_showWishlist()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_WISHLIST_FILE",$this->template);

		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_USERNAME",$this->libFunc->m_displayContent($_SESSION['username']));
		
		#SETTING TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_WISHLIST_FILE","TPL_MAINPRODUCT_BLK","mainproduct_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK","TPL_PRODUCT_BLK","product_blk");

		$this->ObTpl->set_block("TPL_WISHLIST_FILE","TPL_MAINWISHLIST_BLK","mainwishlist_blk");
		$this->ObTpl->set_block("TPL_MAINWISHLIST_BLK","TPL_WISHLIST_BLK","wishlist_blk");

		$this->ObTpl->set_block("TPL_WISHLIST_FILE","TPL_ADDWISHEMAIL_BLK","addwishlist_blk");
		$this->ObTpl->set_block("TPL_WISHLIST_FILE","TPL_SENDWISHEMAIL_BLK","sendwishlist_blk");

		#INTIALIZING 
		$this->ObTpl->set_var("mainproduct_blk","");
		$this->ObTpl->set_var("product_blk","");
		$this->ObTpl->set_var("mainwishlist_blk","");
		$this->ObTpl->set_var("wishlist_blk","");
		$this->ObTpl->set_var("addwishlist_blk","");
		$this->ObTpl->set_var("sendwishlist_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		if(isset($this->request['mode']) && $this->request['mode']=="dspmsg")
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_EMAIL_SENT);
		}

		$this->ObTpl->set_var("TPL_VAR_WISHLIST",MSG_VAR_WISHLIST);
		#*******************DISPLAY MAIN PRODUCT*****************************
		##WISHLIST URL
		$wishModifyUrl=SITE_URL."ecom/index.php?action=wishlist.modify";
		$this->ObTpl->set_var("TPL_VAR_WISHLISTMODIFYURL",$this->libFunc->m_safeUrl($wishModifyUrl));	

		$addEmailUrl=SITE_URL."ecom/index.php?action=wishlist.emailadd";
		$this->ObTpl->set_var("TPL_VAR_ADDEMAIL",$this->libFunc->m_safeUrl($addEmailUrl));	

		$removeUrl=SITE_URL."ecom/index.php?action=wishlist.emailremove";
		$this->ObTpl->set_var("TPL_VAR_REMOVE_EMAIL",$this->libFunc->m_safeUrl($removeUrl));	
	
		$sendUrl=SITE_URL."ecom/index.php?action=wishlist.emailsend";
		$this->ObTpl->set_var("TPL_VAR_SEND_EMAIL",$this->libFunc->m_safeUrl($sendUrl));	
	
		#******************DISPLAY WISHLIST PRODUCT**********************
		#QUERY TO GET PRODUCTS UNDER SELECTED 
		$query ="SELECT iShopWishid_PK,vTitle,vQuantity,vSeoTitle  FROM ".PRODUCTS.",".WISHLIST." WHERE iProductid_FK=iProdid_PK AND iCustomerid_FK='".$_SESSION['userid']."'";
		$pn			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$extraStr	="action=ecom.wishlist&mode=".$this->request['mode'];
		$pn->formno=1;
		$navArr	= $pn->create($query, $this->pageSize, $extraStr);
		$this->obDb->query=$navArr['query'];

		$rowProduct=$this->obDb->fetchQuery();
		$productCount=$this->obDb->record_count;
		if($productCount>0)
		{
			for($i=0;$i<$productCount;$i++)
			{
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowProduct[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));
				$this->ObTpl->set_var("TPL_VAR_ID",$this->libFunc->m_displayContent($rowProduct[$i]->iShopWishid_PK));	$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rowProduct[$i]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_QTY",$this->libFunc->m_displayContent($rowProduct[$i]->vQuantity));

				$this->ObTpl->parse("product_blk","TPL_PRODUCT_BLK",true);	
			}
			$this->ObTpl->parse("mainproduct_blk","TPL_MAINPRODUCT_BLK");	
		

			#QUERY TO GET WISH EMAILS
			$this->obDb->query ="SELECT iWishid_PK,vEmail  FROM ".WISHEMAILS." WHERE  iCustomerid_FK='".$_SESSION['userid']."'";
			$rsWishEmail=$this->obDb->fetchQuery();

			$rsCount=$this->obDb->record_count;
			if($rsCount>0)
			{
				for($i=0;$i<$rsCount;$i++)
				{
					$this->ObTpl->set_var("TPL_VAR_EMAILID",$this->libFunc->m_displayContent($rsWishEmail[$i]->iWishid_PK));	$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($rsWishEmail[$i]->vEmail));
					$this->ObTpl->parse("wishlist_blk","TPL_WISHLIST_BLK",true);	
				}
				$this->ObTpl->parse("mainwishlist_blk","TPL_MAINWISHLIST_BLK");	
				$this->ObTpl->parse("sendwishlist_blk","TPL_SENDWISHEMAIL_BLK");	
			}

			$this->ObTpl->parse("addwishlist_blk","TPL_ADDWISHEMAIL_BLK");
			#PAGINATION
			$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
		}#END PRODUCT DISPLAY
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NOPRODUCT_WISHLIST);
		}
		return($this->ObTpl->parse("return","TPL_WISHLIST_FILE"));
	}


function m_sendEmail()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_WISHEMAIL",$this->templatePath."wishlistEmail.tpl.htm");
		$this->ObTpl->set_block("TPL_VAR_WISHEMAIL","TPL_PRODUCT_BLK","product_blk");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_SITENAME",SITE_NAME);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("product_blk","");

		$this->obDb->query ="SELECT vFirstName,vLastName,vEmail  FROM ".CUSTOMERS." WHERE iCustmerid_PK ='".$_SESSION['userid']."'";
		$rsCustomer=$this->obDb->fetchQuery();
		$rsCustomer[0]->vFirstName;

		$senderName=$this->libFunc->m_displayContent($rsCustomer[0]->vFirstName)." ".$this->libFunc->m_displayContent($rsCustomer[0]->vLastName);
		$this->ObTpl->set_var("TPL_VAR_SENDERNAME",$senderName);

		$this->ObTpl->set_var("TPL_VAR_MESSAGE",nl2br($this->libFunc->m_displayContent($this->request['comment'])));

		$this->obDb->query ="SELECT iShopWishid_PK,vTitle,vQuantity,vSeoTitle,iTaxable,fPrice  FROM ".PRODUCTS.",".WISHLIST." WHERE iProductid_FK=iProdid_PK AND iCustomerid_FK='".$_SESSION['userid']."'";
		$rowProduct=$this->obDb->fetchQuery();
		$productCount=$this->obDb->record_count;
		if($productCount>0)
		{
			for($i=0;$i<$productCount;$i++)
			{
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowProduct[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));
				$this->ObTpl->set_var("TPL_VAR_ID",$this->libFunc->m_displayContent($rowProduct[$i]->iShopWishid_PK));	$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rowProduct[$i]->vTitle));
				if($rowProduct[$i]->iTaxable==1)
				{
					#GETTING VAT PRICE
					$vatPercent=$this->libFunc->m_vatCalculate();
					$vatPrice=number_format((($vatPercent*$rowProduct[$i]->fPrice)/100+$rowProduct[$i]->fPrice),2);
					$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($rowProduct[$i]->fPrice)." (".CONST_CURRENCY.$vatPrice." inc. Vat)");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($rowProduct[$i]->fPrice));
				}

				$this->ObTpl->parse("product_blk","TPL_PRODUCT_BLK",true);	
			}
			
		}


		$message =$this->ObTpl->parse("return","TPL_VAR_WISHEMAIL");
		
		$obMail = new htmlMimeMail();
		$obMail->setReturnPath(ADMIN_EMAIL);
		$obMail->setFrom($this->libFunc->m_displayContent(SITE_NAME)."<".ADMIN_EMAIL.">");
		$obMail->setSubject("Wishlist from ".$senderName."  at ".SITE_NAME);
		$obMail->setCrlf("\n"); //to handle mails in Outlook Express
		$htmlcontent=$message;
		$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
		$obMail->setHtml($htmlcontent,$txtcontent);
		$obMail->buildMessage();
		$this->obDb->query ="SELECT iWishid_PK,vEmail  FROM ".WISHEMAILS." WHERE  iCustomerid_FK='".$_SESSION['userid']."'";
		$rsWishEmail=$this->obDb->fetchQuery();

		$rsCount=$this->obDb->record_count;
		if($rsCount>0)
		{
			$toArray = array();
			for ($i=0; $i<=$rsCount-1; $i++ ){
				$toArray[$i]= $rsWishEmail[$i]->vEmail;
			}
			$result = $obMail->send($toArray);
		}
		$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=wishlist.display&mode=dspmsg");
		$this->libFunc->m_mosRedirect($retUrl);	
	}#END SENDMAIL FUNCTION
	
}#END CLASS
?>