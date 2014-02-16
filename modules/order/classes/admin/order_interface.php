<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_orderInterface
{
	#CONSTRUCTOR
	function c_orderInterface()
	{
		$this->subTotal=0;
		$this->pageTplPath=MODULES_PATH."default/templates/admin/";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize="10";
		$this->totalQty=0;
		$this->volDiscount =0;
		$this->libFunc=new c_libFunctions();
	}

	#FUNCTION TO DISPLAY SHOPBUILDER HOMEPAGE
	function m_dspOrders()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ORDER_FILE", $this->orderTemplate);

		#INTIALIZING TEMPLATE BLOCKS

		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_ORDERMAIN_BLK","ordermain_blk");
		$this->ObTpl->set_block("TPL_ORDERMAIN_BLK","TPL_ORDERS_BLK","orders_blk");
		$this->ObTpl->set_block("TPL_ORDERS_BLK","TPL_BLK_REGISTERED_USER","registereduser_blk");
		$this->ObTpl->set_block("TPL_ORDERS_BLK","TPL_BLK_NOTREGISTERED_USER","notregistereduser_blk");
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_PAGE_BLK2", "page_blk2");

		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_MSG_BLK","msg_blk");
		
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		#INTIALIZING BLOCKS
		$this->ObTpl->set_var("orders_blk","");
		$this->ObTpl->set_var("page_blk2","");
		
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("ordermain_blk","");
		$this->ObTpl->set_var("TPL_VAR_SEL1","");
		$this->ObTpl->set_var("TPL_VAR_SEL2","");

		$this->ObTpl->set_var("SELECTED1","");
		$this->ObTpl->set_var("SELECTED2","");
		$this->ObTpl->set_var("SELECTED3","");
		$this->ObTpl->set_var("SELECTED4","");
		$this->ObTpl->set_var("SELECTED5","");
		$this->ObTpl->set_var("SELECTED6","");
		$this->ObTpl->set_var("SELECTED8","");

		$this->ObTpl->set_var("SELECTED_ORDERBY","");
		$this->ObTpl->set_var("SELECTED_ORDERBY1","");
		$this->ObTpl->set_var("SELECTED_ORDERBY2","");
		$this->ObTpl->set_var("SELECTED_ORDERBY3","");
		$this->ObTpl->set_var("SELECTED_ORDERBY4","");
		$this->ObTpl->set_var("SELECTED_ORDERBY5","");
		$this->ObTpl->set_var("SELECTED_ORDERBY6","");
		$this->ObTpl->set_var("SELECTED_ORDERBY8","");

		$this->ObTpl->set_var("TPL_VAR_MESSAGE","There are currently no orders available.");
			
		#QUERY TO GET ORDERS PLACED
		$query ="SELECT iInvoice,tmOrderDate,vPayMethod,iPayStatus,vStatus,";
		$query.=" vFirstName,vLastName,iCustomerid_FK,iOrderid_PK,iOrderStatus FROM ".ORDERS." WHERE 1=1 ";
		if(!isset($this->request['mstatus']))
		{
			$this->request['mstatus']="";
		}
		switch($this->request['mstatus'])
		{
			case "New":
				$query.=" AND vStatus='New' ";
				$this->ObTpl->set_var("SELECTED1","selected");
			break;
			case "Pending":
				$query.=" AND vStatus='Pending' ";
				$this->ObTpl->set_var("SELECTED2","selected");
			break;
			case "Received":
				$query.=" AND vStatus='Received' ";
				$this->ObTpl->set_var("SELECTED3","selected");
			break;
			case "Backorder":
				$query.=" AND vStatus='Backorder' ";
				$this->ObTpl->set_var("SELECTED4","selected");
			break;
			case "Shipped":
				$query.=" AND vStatus='Shipped' ";
				$this->ObTpl->set_var("SELECTED5","selected");
			break;
			case "Void":
				$query.=" AND vStatus='Void' ";
				$this->ObTpl->set_var("SELECTED6","selected");
			break;
			case "Delete":
				$query.=" AND vStatus='Delete' ";
				$this->ObTpl->set_var("SELECTED7","selected");
			break;
			case "All":
				$query.="";
				$this->ObTpl->set_var("SELECTED8","selected");
			break;
			default:
				$query.=" AND vStatus='New' ";
				$this->ObTpl->set_var("SELECTED1","selected");
			break;
		}
	
		if(!isset($this->request['orderby']))
		{
			$this->request['orderby']="";
		}
		switch($this->request['orderby'])
		{
			case "date":
				$query.="  ORDER BY tmOrderDate";
				$this->ObTpl->set_var("SELECTED_ORDERBY1","selected");
			break;
			case "paymethod":
				$query.=" ORDER BY vPayMethod";
				$this->ObTpl->set_var("SELECTED_ORDERBY2","selected");
			break;
			case "paystatus":
				$query.=" ORDER BY iPayStatus";
				$this->ObTpl->set_var("SELECTED_ORDERBY3","selected");
			break;
			case "lastname":
				$query.=" ORDER BY vLastName";
				$this->ObTpl->set_var("SELECTED_ORDERBY4","selected");
			break;
			case "city":
				$query.=" ORDER BY vCity";
				$this->ObTpl->set_var("SELECTED_ORDERBY5","selected");
			break;
			case "state":
				$query.=" ORDER BY vState";
				$this->ObTpl->set_var("SELECTED_ORDERBY6","selected");
			break;
			case "country":
				$query.=" ORDER BY vCountry";
				$this->ObTpl->set_var("SELECTED_ORDERBY7","selected");
			break;
			case "alt_city":
				$query.=" ORDER BY vAltCity";
				$this->ObTpl->set_var("SELECTED_ORDERBY8","selected");
			break;
			case "alt_state":
				$query.=" ORDER BY vAltState";
				$this->ObTpl->set_var("SELECTED_ORDERBY9","selected");
			break;
			case "alt_country":
				$query.=" ORDER BY vAltCountry";
				$this->ObTpl->set_var("SELECTED_ORDERBY10","selected");
			break;
			default:
				$query.=" ORDER BY iInvoice";
				$this->ObTpl->set_var("SELECTED_ORDERBY","selected");
			break;
		}
		if(isset($this->request['direction']) && $this->request['direction']=="asc")
		{
			$query.=" ASC";
			$this->request['direction']="asc";
			$this->ObTpl->set_var("TPL_VAR_SEL1","checked");
		}
		else
		{
			$query.=" DESC";
			$this->request['direction']="desc";
			$this->ObTpl->set_var("TPL_VAR_SEL2","checked");
		}
		if(!isset($this->request['page']))
		{
			$this->request['page']='1';
		}

		$pn= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$extraStr	="action=orders.home&mstatus=".$this->request['mstatus']."&orderby=".$this->request['orderby']."&direction=".$this->request['direction'];
		$this->ObTpl->set_var("TPL_VAR_EXTRASTRING1","action=orders.home&mstatus=".$this->request['mstatus']."&orderby=".$this->request['orderby']."&direction=".$this->request['direction']."&page=".$this->request['page']);		
		$this->ObTpl->set_var("TPL_VAR_EXTRASTRING","action=orders.updatehome&mstatus=".$this->request['mstatus']."&orderby=".$this->request['orderby']."&direction=".$this->request['direction']."&page=".$this->request['page']);
		$pn->formno=1;
		$navArr	= $pn->create($query, $this->pageSize, $extraStr);
		$pn2			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);

		$pn2->formno=2;
		$navArr2	= $pn2->create($query, $this->pageSize, $extraStr,"top");

		$res=$navArr['qryRes'];
		$rCount=$navArr['selRecs'];
		$totalRecord=$navArr['totalRecs'];
		if($rCount>0)
		{
			$this->ObTpl->set_var("TPL_VAR_TOTALRECORD",$rCount);
			for($i=0;$i<$rCount;$i++)
			{
				$this->ObTpl->set_var("registereduser_blk","");
				$this->ObTpl->set_var("notregistereduser_blk","");
				$this->ObTpl->set_var("TPL_VALID_ORDER","Incomplete");
				if($res[$i]->iOrderStatus==1){
					$this->ObTpl->set_var("TPL_VALID_ORDER","Complete");
				}
				$this->ObTpl->set_var("SEL1","");
				$this->ObTpl->set_var("SEL2","");
				$this->ObTpl->set_var("SEL3","");
				$this->ObTpl->set_var("SEL4","");
				$this->ObTpl->set_var("SEL5","");
				$this->ObTpl->set_var("SEL6","");

				if($res[$i]->vStatus=="New")
				{
					$this->ObTpl->set_var("SEL1","selected");
				}
				elseif($res[$i]->vStatus=="Pending")
				{
					$this->ObTpl->set_var("SEL2","selected");
				}
				elseif($res[$i]->vStatus=="Received")
				{
					$this->ObTpl->set_var("SEL3","selected");
				}
				elseif($res[$i]->vStatus=="Backorder")
				{
					$this->ObTpl->set_var("SEL4","selected");
				}
				elseif($res[$i]->vStatus=="Shipped")
				{
					$this->ObTpl->set_var("SEL5","selected");
				}
				elseif($res[$i]->vStatus=="Void")
				{
					$this->ObTpl->set_var("SEL6","selected");
				}
				else
				{
					$this->ObTpl->set_var("SEL1","selected");
				}	

				$this->ObTpl->set_var("TPL_VAR_ORDERDATE",$this->libFunc->dateFormat1($res[$i]->tmOrderDate));
				
				$this->ObTpl->set_var("TPL_VAR_ORDERID",$res[$i]->iOrderid_PK);
				$this->ObTpl->set_var("TPL_VAR_INVOICE",$res[$i]->iInvoice);
				$this->ObTpl->set_var("TPL_VAR_CUSTOMER",$this->libFunc->m_displayContent($res[$i]->vFirstName)." ".$this->libFunc->m_displayContent($res[$i]->vLastName));
				$this->ObTpl->set_var("TPL_VAR_CUSTOMERID",$res[$i]->iCustomerid_FK);

				if($res[$i]->iCustomerid_FK>0){
				#IF ORDER PLACED BY REGISTERED USER
					$this->ObTpl->parse("registereduser_blk","TPL_BLK_REGISTERED_USER");
				}else{
				#IF ORDER IS PLACED BY NOT REGISTERD USER
					$this->ObTpl->parse("notregistereduser_blk","TPL_BLK_NOTREGISTERED_USER");
				}


				$this->ObTpl->set_var("TPL_VAR_METHOD",$res[$i]->vPayMethod);
				if($res[$i]->iPayStatus=="1")
				{
					$this->ObTpl->set_var("TPL_VAR_PAYSTATUS","paid");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_PAYSTATUS","unpaid");
				}

				
				$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
				$this->ObTpl->set_var("TPL_VAR_MESSAGE","");
				$this->ObTpl->parse("orders_blk","TPL_ORDERS_BLK",true);	
			}
			$this->ObTpl->parse("ordermain_blk","TPL_ORDERMAIN_BLK",true);	
			$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
			$this->ObTpl->parse("page_blk2","TPL_PAGE_BLK2");
		}
		else
		{
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");	
		}
		
			
				
		return($this->ObTpl->parse("return","TPL_ORDER_FILE"));
	}

	#FUNCTION TO DISPLAY ORDER DETAILS
	function m_dspOrderDetails()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->tComments="";
		$this->vSid="";
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ORDER_FILE", $this->orderTemplate);
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_CART_BLK","cart_blk");
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_MSG_BLK","msg_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_DISPATCHED_BLK","dispatched_blk");

		$this->ObTpl->set_block("TPL_CART_BLK","TPL_BLK_REGISTERED_USER","registereduser_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_BLK_NOTREGISTERED_USER","notregistereduser_blk");

		$this->ObTpl->set_block("TPL_CART_BLK","TPL_DELIVERY_BLK","delivery_blk");
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_LINK_BLK","link_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_PRODUCT_BLK","cartproduct_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_GIFTCERT_BLK","giftcert_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_DISCOUNT_BLK","discount_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_COD_BLK","cod_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_PROMODISCOUNTS_BLK","promodiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VOLDISCOUNTS_BLK","volDiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_POSTAGE_BLK","postage_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_MPOINTS_BLK","memberpoint_blk");
		# Added for earned member point
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_EMPOINTS_BLK","earnedmemberpoint_blk");
		# Added for total member point
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_TMPOINTS_BLK","totalmemberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_CARTWEIGHT_BLK","cartWeight_blk");	
		$this->ObTpl->set_block("TPL_CART_BLK","TPL_VAT_BLK","vat_blk");	
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_KIT_BLK","kit_blk");
		$this->ObTpl->set_block("TPL_KIT_BLK","TPL_KITELEMENT_BLK","kitElement_blk");	

		$this->ObTpl->set_var("TPL_VAR_MESSAGE","Sorry,No order details available.");
		

		#INTAILAIZING
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT","");
		$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING","");
		$this->ObTpl->set_var("TPL_VAR_CUSTOMER_IP","Not available");
		

		$this->ObTpl->set_var("msg_blk","");	
		$this->ObTpl->set_var("kit_blk","");	
		$this->ObTpl->set_var("kitElement_blk","");	
		$this->ObTpl->set_var("vat_blk","");	
		$this->ObTpl->set_var("dispatched_blk","");	
		$this->ObTpl->set_var("delivery_blk","");	
		$this->ObTpl->set_var("cartproduct_blk","");
		$this->ObTpl->set_var("cart_blk","");	
		$this->ObTpl->set_var("link_blk","");	
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
		# Added for earned member point
		$this->ObTpl->set_var("earnedmemberpoint_blk","");
		#  Added for total member point
		$this->ObTpl->set_var("totalmemberpoint_blk","");
		$this->ObTpl->set_var("registereduser_blk","");
		$this->ObTpl->set_var("notregistereduser_blk","");
		$this->ObTpl->set_var("ORDERID", $this->request['orderid']);

		$this->ObTpl->set_var("TPL_VAR_MSG","");

		if(!isset($this->request['orderid']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");
			exit;
		}
		else
		{
			$this->request['orderid']=intval($this->request['orderid']);
		}

		if($this->request['orderid']<1)
		{
			#URL TEMPER
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");
		}
		else
		{			
			#QUERY ORDER TABLE
			$this->obDb->query = "SELECT vAuthCode,iOrderid_PK,tmOrderDate,vPayMethod,vShipDescription,fShipTotal,";
			$this->obDb->query.= "vFirstName,vLastName,vEmail,vAddress1,vAddress2,vCity,iInvoice,";
			$this->obDb->query.= "vState,vStateName,vCountry,vZip,vCompany,vPhone,vHomepage,";
			$this->obDb->query.= "vAltCompany,vAltName,vAltAddress1,vAltAddress2,vAltCity,vAltState,";
			$this->obDb->query.= "vAltCountry,vAltStateName,vAltZip,vAltPhone,fCodCharge,";
			$this->obDb->query.= "fPromoValue,vDiscountCode,fDiscount,iGiftcert_FK,fGiftcertTotal,";
			$this->obDb->query.= "fMemberPoints,fShipByWeightPrice,fShipByWeightKg,iSameAsBilling,";
			$this->obDb->query.= "fTaxRate,fTaxPrice,tComments,vCustomerIP,tAdminComments,vStatus,iPayStatus,fTotalPrice, ";
			$this->obDb->query .= "iCustomerid_FK,vSid,iOrderStatus,iEarnedPoints FROM ".ORDERS." WHERE iInvoice='".$this->request['orderid']."' ";
			$rsOrder=$this->obDb->fetchQuery();
			$rsOrderCount=$this->obDb->record_count;
			
			if($rsOrderCount>0)
			{
				#ORDER ID FOR COMMON FUNCTION
				$comFunc->orderId=$rsOrder[0]->iOrderid_PK;
				$this->obDb->query = "SELECT * FROM ".COMPANYSETTINGS;
				$CompanySettings=$this->obDb->fetchQuery();
				# sprSimple Invoice Integration
				$htmlString = "<!doctype html><html dir=\"ltr\" lang=\"en\" class=\"no-js\"><meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" /><meta name=\"viewport\" content=\"width=device-width\" /><title>Invoice #".$rsOrder[0]->iInvoice."</title><link rel=\"stylesheet\" href=\"".SITE_URL."css/reset.css\" /><link rel=\"stylesheet\" href=\"".SITE_URL."css/invstyle.css\" media=\"all\"/><style>@media print  {	  tr {border:1px solid #000;}	  body { background-color:#fff !important; )	  #invoice { background-color:transparent !important; )  }  </style><!--[if lte IE 8]><script src=\"http://html5shim.googlecode.com/svn/trunk/html5.js\"><\/script><![endif]--><script>(function(H){H.className=H.className.replace(/\bno-js\b/,\"js\")})(document.documentElement)<\/script></head><body>";

				
				$this->datePost = "";
				if($rsOrder[0]->vStatus=='Shipped')
				{
					$this->obDb->query = "SELECT vShipper,vTracking,tmShipDate FROM ".SHIPPINGDETAILS." WHERE  iOrderid_FK='".$rsOrder[0]->iOrderid_PK."'";
					$queryResult	=		$this->obDb->fetchQuery();
					$recordNum		=		$this->obDb->record_count;
					if($recordNum>0)
					{
						$this->ObTpl->set_var("TPL_VAR_MODE","update");
						$this->ObTpl->set_var("TPL_VAR_MSG",MSG_POSTAGE_DEFINED);
						
						$this->shipper		=	$queryResult[0]->vShipper;
						$this->datePost	=	$queryResult[0]->tmShipDate;
						$this->trackNum	=	$queryResult[0]->vTracking;
						$this->ObTpl->set_var("TPL_VAR_SHIPPER",$this->libFunc->m_displayContent($this->shipper));
						$this->ObTpl->set_var("TPL_VAR_DATEPOST",$this->libFunc->dateFormat2($this->datePost));
						$this->ObTpl->set_var("TPL_VAR_TRACKNUM",$this->trackNum);
						$this->ObTpl->parse("dispatched_blk","TPL_DISPATCHED_BLK");	
					}
				}

if($rsOrder[0]->iPayStatus==1)
{
	$htmlString = $htmlString . "<div id=\"invoice\" class=\"paid\">";
}
else
{
	$htmlString = $htmlString . "<div id=\"invoice\" class=\"unpaid\">";
}


	$htmlString = $htmlString . "<div class=\"this-is\">		<strong>Invoice</strong>	</div>	<header id=\"header\">		<div class=\"invoice-intro\">			<img src=".SITE_URL."images/company/".$CompanySettings[0]->vLogo." />			<p>".$CompanySettings[0]->vSlogan."</p>		</div>		<dl class=\"invoice-meta\">			<dt class=\"invoice-number\">Invoice #</dt>			<dd>".$rsOrder[0]->iInvoice."</dd>			<dt class=\"invoice-date\">Date of Invoice</dt>			<dd>".$this->libFunc->dateFormat2($rsOrder[0]->tmOrderDate)."</dd>			<dt class=\"invoice-due\">Due Date</dt>			<dd>".$this->libFunc->dateFormat2($rsOrder[0]->tmOrderDate)."</dd>		</dl>	</header>	<section id=\"parties\">		<table class=\"fromtostatus\"><tr>		<td valign=\"top\" class=\"invoice-to\">			<h2>Invoice To:</h2>			<div id=\"hcard-Hiram-Roth\" class=\"vcard\">				<a class=\"url fn\" href=\"#\">".$rsOrder[0]->vFirstName." ".$rsOrder[0]->vLastName."</a>				<div class=\"org\">".$rsOrder[0]->vCompany."</div>				<a class=\"email\" href=\"mailto:".$rsOrder[0]->vEmail."\">".$rsOrder[0]->vEmail."</a>								<div class=\"adr\">					<div class=\"street-address\">".$rsOrder[0]->vAddress1."<br/>".$rsOrder[0]->vAddress2."</div>					<span class=\"locality\">".$rsOrder[0]->vCity.",".$rsOrder[0]->vStateName." ".$rsOrder[0]->vZip."</span>";
					
					$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$rsOrder[0]->vCountry."'";
					$row_country = $this->obDb->fetchQuery();
					$htmlString = $htmlString . "<br/><span class=\"country-name\">".$this->libFunc->m_displayContent($row_country[0]->vCountryName)."</span>				</div>				<div class=\"tel\">".$rsOrder[0]->vPhone."</div>			</div>		</td>		<td valign=\"top\" class=\"invoice-from\">			<h2>Invoice From:</h2>			<div id=\"hcard-Admiral-Valdore\" class=\"vcard\">				<div class=\"org\">".$CompanySettings[0]->vCname."</div>								<div class=\"adr\">					<div class=\"street-address\">".$CompanySettings[0]->vAddress."</div>					<span class=\"locality\">".$CompanySettings[0]->vCity.", ".$CompanySettings[0]->vStateName." ".$CompanySettings[0]->vZip."</span>";
					$this->obDb->query ="SELECT vCountryName FROM ".COUNTRY." where ";
					$this->obDb->query.="iCountryId_PK  = '".$CompanySettings[0]->vCountry."'";
					$row_country = $this->obDb->fetchQuery();
					$htmlString = $htmlString . "<br/><span class=\"country-name\">".$this->libFunc->m_displayContent($row_country[0]->vCountryName)."</span>				</div>				<div class=\"tel\">".$CompanySettings[0]->vPhone."</div>			</div>		</td>		<td valign=\"top\" class=\"invoice-status\">			<h2>Invoice Status</h2>";
	if($rsOrder[0]->iPayStatus==1)
	{
			$htmlString = $htmlString . "<strong>Invoice is <em>Paid</em></strong>";
	}
	else
	{
			$htmlString = $htmlString . "<strong>Invoice is <em>Unpaid</em></strong>";
	}
		$htmlString = $htmlString . "</td></tr></table>	</section>	<section class=\"invoice-financials\">		<div class=\"invoice-items\">			<table>				<caption>Your Invoice</caption>				<thead>					<tr>						<th>Item &amp; Description</th>						<th>Quantity</th>						<th>Price</th>					</tr>				</thead>				<tbody>";
				$this->obDb->query ="SELECT iOrderProductid_PK,iProductid_FK,iQty,iGiftwrapFK,fPrice,";
				$this->obDb->query.="iVendorid_FK,fDiscount,vTitle,vSku,iKit,tShortDescription,seo_title,";
				$this->obDb->query.="iTaxable,iFreeship,vPostageNotes  FROM ".ORDERPRODUCTS;
				$this->obDb->query.=" WHERE iOrderid_FK='".$rsOrder[0]->iOrderid_PK."'";
				$rsOrderProduct=$this->obDb->fetchQuery();
				$rsOrderProductCount=$this->obDb->record_count;
				foreach($rsOrderProduct as $i => $v)
				{
					$comFunc->productId=$rsOrderProduct[$i]->iProductid_FK;
					$comFunc->qty=$rsOrderProduct[$i]->iQty;
					$comFunc->price=$rsOrderProduct[$i]->fPrice;
					$comFunc->orderProductId=$rsOrderProduct[$i]->iOrderProductid_PK;
					$tempItem = "<h3>".$this->libFunc->m_displayContent($rsOrderProduct[$i]->vTitle)."</h3>";
					if($rsOrderProduct[$i]->iKit==1)
					{
						$this->obDb->query ="SELECT iProdId_FK,iQty,vTitle,vSeoTitle,vSku FROM ".PRODUCTKITS.",".PRODUCTS." WHERE iProdId_FK=iProdId_PK AND iKitId ='".$rsOrderProduct[$i]->iProductid_FK."'";
						$rsKit=$this->obDb->fetchQuery();
						$rsKitCount=$this->obDb->record_count;
						if($rsKitCount>0)
						{
							for($j=0;$j<$rsKitCount;$j++)
							{
								$kitElementUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rsKit[$j]->vSeoTitle;
								$comFunc->kitProductId=$rsKit[$j]->iProdId_FK;
								$comFunc->productId=$rsKit[$j]->iProdId_FK;
								#GET CART OPTIONS
								$kitOptions=$comFunc->m_orderKitProductOptions();
								$tempItem = $tempItem . "<h6>".$rsKit[$j]->vSku."</h6>";
								if(!empty($kitOptions))
								{
								$tempItem = $tempItem . "<h6>".$kitOptions."</h6>";
								$tempItem = $tempItem . "<h6>".$rsKit[$j]->iQty."</h6>";
								}
							}#END FOR I LOOP
						}#END IF
					}
					else
					{
						$options = $comFunc->m_orderProductOptions();
						$choices = $comFunc->m_orderProductChoices();
						if(!empty($rsOrderProduct[$i]->vSku))
						{
						$tempItem = $tempItem . "<h6>".$rsOrderProduct[$i]->vSku."</h6>";
						}
						#GET OPTIONS
						if(!empty($options))
						{
						$tempItem = $tempItem . "<h6>".$options."</h6>";
						}
						#GET CHOICES
						if(!empty($choices))
						{
						$tempItem = $tempItem . "<h6>".$choices."</h6>";
						}
					}
					$htmlString = $htmlString . "<tr>						<th>".$tempItem."</th>						<td style=\"text-align:left;\">".$rsOrderProduct[$i]->iQty."</td>						<td>".CONST_CURRENCY.$comFunc->price."</td>					</tr>";
				}
				$htmlString = $htmlString . "</tbody>				<tfoot>					<tr>						<td colspan=\"3\" style=\"text-align:left;\"><strong>Customer Comments:</strong> ".$rsOrder[0]->tComments."</td>					</tr>				</tfoot>			</table>		</div>";

				$htmlString = str_replace(Array("'","\n","\r\n","\r"),Array("\\'"," "," "," "),$htmlString);
		
				$this->ObTpl->set_var("TPL_VAR_PRINTABLE_INVOICE",$htmlString);
				
				
				$this->ObTpl->set_var("TPL_VAR_ORDERID",$rsOrder[0]->iOrderid_PK);
				$this->ObTpl->set_var("TPL_VAR_MEMBERPOINTS_USED",$rsOrder[0]->fMemberPoints);
				$this->ObTpl->set_var("TPL_VAR_MEMBERPOINTS_EARNED",$rsOrder[0]->iEarnedPoints);
				$this->ObTpl->set_var("TPL_VAR_GIFTCERTAMOUNT",$rsOrder[0]->fGiftcertTotal);
				$this->ObTpl->set_var("TPL_VAR_GIFTCERTID",$rsOrder[0]->iGiftcert_FK);
				$this->ObTpl->set_var("TPL_VAR_CUSTOMERID",$rsOrder[0]->iCustomerid_FK);
				if($rsOrder[0]->iPayStatus==1)
				{
					$this->ObTpl->set_var("CHECK1","checked");
					$this->ObTpl->set_var("TPL_MSG1","<font class='message'>Invoice paid</font>");
				}
				else
				{
					$this->ObTpl->set_var("CHECK1","");
					$this->ObTpl->set_var("TPL_MSG1","Mark order as paid");
				}
				
				if($rsOrder[0]->iOrderStatus==1)
				{
					$this->ObTpl->set_var("CHECK2","checked");
					$this->ObTpl->set_var("TPL_MSG2","<font class='message'>Order Complete</font>");
				}
				else
				{
					$this->ObTpl->set_var("CHECK2","");
					$this->ObTpl->set_var("TPL_MSG2","Mark order as complete");
				}
				
				if($rsOrder[0]->vCustomerIP!=0){
				$this->ObTpl->set_var("TPL_VAR_CUSTOMER_IP",$rsOrder[0]->vCustomerIP);
				}
				 
				$this->ObTpl->set_var("TPL_VAR_AUTH",$rsOrder[0]->vAuthCode);

				$this->ObTpl->set_var("SEL1","");
				$this->ObTpl->set_var("SEL2","");
				$this->ObTpl->set_var("SEL3","");
				$this->ObTpl->set_var("SEL4","");
				$this->ObTpl->set_var("SEL5","");
				$this->ObTpl->set_var("SEL6","");

				if($rsOrder[0]->vStatus=="New")
				{
					$this->ObTpl->set_var("SEL1","selected");
				}
				elseif($rsOrder[0]->vStatus=="Received")
				{
					$this->ObTpl->set_var("SEL2","selected");
				}
				elseif($rsOrder[0]->vStatus=="Backorder")
				{
					$this->ObTpl->set_var("SEL3","selected");
				}
				elseif($rsOrder[0]->vStatus=="Shipped")
				{
					$this->ObTpl->set_var("SEL4","selected");
				}
				elseif($rsOrder[0]->vStatus=="Void")
				{
					$this->ObTpl->set_var("SEL5","selected");
				}
				elseif($rsOrder[0]->vStatus=="Delete")
				{
					$this->ObTpl->set_var("SEL6","selected");
				}
				else
				{
					$this->ObTpl->set_var("SEL1","selected");
				}	
				$comFunc->orderId=$rsOrder[0]->iOrderid_PK;
				$this->ObTpl->set_var("TPL_VAR_INVOICE",$rsOrder[0]->iInvoice);	
				$this->ObTpl->set_var("TPL_VAR_ORDERDATE",$this->libFunc->dateFormat2($rsOrder[0]->tmOrderDate));
				if($rsOrder[0]->vPayMethod=='cod')
				{
					$vPayMethod=	$comFunc->m_paymentMethod($rsOrder[0]->vPayMethod,$rsOrder[0]->fCodCharge);
				}
				elseif($rsOrder[0]->vPayMethod=='cc')
				{
					$vPayMethod=	$comFunc->m_paymentMethod($rsOrder[0]->vPayMethod);
					$returnInfo=$comFunc->m_dspCreditCardInfo();
					if(empty($returnInfo))
					{
						$vPayMethod.="<br />CC info not available";
					}
					else
					{
						$vPayMethod.=$comFunc->m_dspCreditCardInfo();
					}
				}
				else
				{
					$vPayMethod=	$comFunc->m_paymentMethod($rsOrder[0]->vPayMethod);
				}
				$this->ObTpl->set_var("TPL_VAR_PAYMETHOD",$vPayMethod);
				$this->ObTpl->set_var("TPL_VAR_SHIPDESC",$this->libFunc->m_displayContent1($rsOrder[0]->vShipDescription));
				$this->ObTpl->set_var("TPL_VAR_ORDERSTATUS",$rsOrder[0]->vStatus);
				
				
				if(empty($rsOrder[0]->tComments) && empty($rsOrder[0]->vSid))
				{
					$this->ObTpl->set_var("TPL_VAR_COMMENTS","None");
				}
				else
				{
					if(!empty($rsOrder[0]->tComments))
					{
						$this->tComments=$rsOrder[0]->tComments."<br /><br />";
					}
					if(!empty($rsOrder[0]->vSid))
					{
						$this->vSid="Source ID: ".$rsOrder[0]->vSid."<br />";
					}
					$this->ObTpl->set_var("TPL_VAR_COMMENTS",$this->tComments.$this->vSid);
				}
				
				# handle admin comments - MCB, 10/09/08
				# copy-and-pasted from regular comments handler block above
				if (empty ($rsOrder[0]->tAdminComments) && empty ($rsOrder[0]->vSid)) {
					$this->ObTpl->set_var("TPL_VAR_ADMIN_COMMENTS", "None");
				}
				else {
					if (!empty ($rsOrder[0]->tAdminComments)) {
						$this->tAdminComments = $rsOrder[0]->tAdminComments;
					} else {
					    $this->tAdminComments = "";
					}
					if (!empty($rsOrder[0]->vSid)) {
						$this->vSid="Source ID: " . $rsOrder[0]->vSid . "<br />";
					} else {
					    $this->vSid="Sourice ID: None.";
					}
					//$this->ObTpl->set_var("TPL_VAR_ADMIN_COMMENTS", $this->tAdminComments.$this->vSid);
					$this->ObTpl->set_var("TPL_VAR_ADMIN_COMMENTS", $this->tAdminComments);
				}


				$this->ObTpl->set_var("TPL_VAR_CUSTOMERID",$rsOrder[0]->iCustomerid_FK);
				if(isset($rsOrder[0]->vState) && !empty($rsOrder[0]->vState))
				{
					$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$rsOrder[0]->vState."'";
					$row_state = $this->obDb->fetchQuery();
					if($this->libFunc->m_displayContent($row_state[0]->vStateName)==='Other')
					{
						$this->ObTpl->set_var("TPL_VAR_BILLSTATE", $this->libFunc->m_displayContent($rsOrder[0]->vStateName));
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_BILLSTATE",$this->libFunc->m_displayContent($row_state[0]->vStateName));
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_BILLSTATE", $this->libFunc->m_displayContent($rsOrder[0]->vStateName));
				}
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$rsOrder[0]->vCountry."'";
				$row_country = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));

				if(isset($rsOrder[0]->vAltState) && !empty($rsOrder[0]->vAltState))
				{
					$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$rsOrder[0]->vAltState."'";
					$row_state = $this->obDb->fetchQuery();
					if($this->libFunc->m_displayContent($row_state[0]->vStateName)==='Other')
					{
						$this->ObTpl->set_var("TPL_VAR_SHIPSTATE", $this->libFunc->m_displayContent($rsOrder[0]->vAltStateName));
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_SHIPSTATE",$this->libFunc->m_displayContent($row_state[0]->vStateName));
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SHIPSTATE", $this->libFunc->m_displayContent($rsOrder[0]->vAltStateName));
				}
				
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$rsOrder[0]->vAltCountry."'";
				$row_country = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_SHIPCOUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));
				$this->ObTpl->set_var("TPL_VAR_BILLNAME",$this->libFunc->m_displayContent($rsOrder[0]->vFirstName)." ".$this->libFunc->m_displayContent($rsOrder[0]->vLastName));
				if($rsOrder[0]->iCustomerid_FK>0){
				#IF ORDER PLACED BY REGISTERED USER
					$this->ObTpl->parse("registereduser_blk","TPL_BLK_REGISTERED_USER");
				}else{
				#IF ORDER IS PLACED BY NOT REGISTERD USER
					$this->ObTpl->parse("notregistereduser_blk","TPL_BLK_NOTREGISTERED_USER");
				}
				$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($rsOrder[0]->vEmail));
				$this->ObTpl->set_var("TPL_VAR_BILLADDRESS1", $this->libFunc->m_displayContent($rsOrder[0]->vAddress1));
				$this->ObTpl->set_var("TPL_VAR_BILLADDRESS2", $this->libFunc->m_displayContent($rsOrder[0]->vAddress2));
				$this->ObTpl->set_var("TPL_VAR_CITY",$this->libFunc->m_displayContent($rsOrder[0]->vCity));
				$this->ObTpl->set_var("TPL_VAR_BILLZIP",$this->libFunc->m_displayContent($rsOrder[0]->vZip));
				$this->ObTpl->set_var("TPL_VAR_COMPANY", $this->libFunc->m_displayContent($rsOrder[0]->vCompany));
				$this->ObTpl->set_var("TPL_VAR_BILLPHONE", $this->libFunc->m_displayContent($rsOrder[0]->vPhone));
				$this->ObTpl->set_var("TPL_VAR_HOMEPAGE", $this->libFunc->m_displayContent($rsOrder[0]->vHomepage));
				$this->ObTpl->set_var("TPL_VAR_MPOINTS","");
				if($rsOrder[0]->iSameAsBilling==1){
					$this->ObTpl->set_var("TPL_VAR_SAMEASBILLING","Same as billing address");
				}else{
					$this->ObTpl->parse("delivery_blk","TPL_DELIVERY_BLK");
				}

				$this->ObTpl->set_var("TPL_VAR_ALTCOMPANY", $this->libFunc->m_displayContent($rsOrder[0]->vAltCompany));
				$this->ObTpl->set_var("TPL_VAR_SHIPNAME", $this->libFunc->m_displayContent($rsOrder[0]->vAltName));
				$this->ObTpl->set_var("TPL_VAR_SHIPADDRESS1", $this->libFunc->m_displayContent($rsOrder[0]->vAltAddress1));
				$this->ObTpl->set_var("TPL_VAR_SHIPADDRESS2", $this->libFunc->m_displayContent($rsOrder[0]->vAltAddress2)."<br />");
				$this->ObTpl->set_var("TPL_VAR_ALTCITY", $this->libFunc->m_displayContent($rsOrder[0]->vAltCity));
				
			$this->ObTpl->set_var("TPL_VAR_SHIPZIP", $this->libFunc->m_displayContent($rsOrder[0]->vAltZip));
			$this->ObTpl->set_var("TPL_VAR_SHIPPHONE", $this->libFunc->m_displayContent($rsOrder[0]->vAltPhone));
		
			if($rsOrderProductCount>0)
			{
				for($i=0;$i<$rsOrderProductCount;$i++)
				{
					$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT","");
					$this->ObTpl->set_var("TPL_VAR_MAINOPTIONS","");
					$this->ObTpl->set_var("TPL_VAR_MAINCHOICES","");
					$this->ObTpl->set_var("kit_blk","");	
					$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG","");
					$this->ObTpl->set_var("TPL_VAR_TAXABLE","");
					$this->ObTpl->set_var("TPL_VAR_NOTES","");	
					$this->price=0;#INTIALIZING
					$this->total=0;
				
					$comFunc->orderProductId=$rsOrderProduct[$i]->iOrderProductid_PK;
					$comFunc->qty=$rsOrderProduct[$i]->iQty;
					$comFunc->price=$this->price;

					##CHECK FOR DROPSHIP FEATURE and SHOW IF SUPPLIER CONFIRMED THE PRODUCTS IN ORDER
					if($rsOrderProduct[$i]->iVendorid_FK!=0 && DROP_SHIP_FEATURE!=0)
					{
						
						$this->obDb->query ="SELECT status  FROM ".CONFIRMATIONORDERS." WHERE iInvoice='".$rsOrder[0]->iInvoice."' AND iVendorid_FK='".$rsOrderProduct[$i]->iVendorid_FK."'";
						$rsSOrderStatus=$this->obDb->fetchQuery();
						if($rsSOrderStatus[0]->status != ""){
							$this->ObTpl->set_var("TPL_VAR_SUPPLIERS_STATUS","Supplier Status:".$rsSOrderStatus[0]->status);
						}else{
							$this->ObTpl->set_var("TPL_VAR_SUPPLIERS_STATUS","");
						}
					}else{
						$this->ObTpl->set_var("TPL_VAR_SUPPLIERS_STATUS","");
					}

					##GIFTWRAP URL
					if($rsOrderProduct[$i]->iGiftwrapFK!=0)
					{
						$this->ObTpl->set_var("TPL_VAR_GIFTWRAP",$comFunc->m_dspGiftWrap($rsOrderProduct[$i]->iGiftwrapFK));
					}
					else
					{		
						$this->ObTpl->set_var("TPL_VAR_GIFTWRAP","");
					}
			
					$this->ObTpl->set_var("TPL_VAR_ISKIT","0");
					#PRODUCT ID FOR COMMON FUNCTION CLASS
					$comFunc->productId=$rsOrderProduct[$i]->iProductid_FK;

					if($rsOrderProduct[$i]->iKit==1)
					{
						$this->ObTpl->set_var("kit_blk","");
						$this->ObTpl->set_var("TPL_VAR_ISKIT","1");
						
						$this->obDb->query ="SELECT iProdId_FK,iQty,vTitle,vSeoTitle,vSku FROM ".PRODUCTKITS.",".PRODUCTS." WHERE iProdId_FK=iProdId_PK AND iKitId ='".$rsOrderProduct[$i]->iProductid_FK."'";
						$rsKit=$this->obDb->fetchQuery();
						$rsKitCount=$this->obDb->record_count;
						if($rsKitCount>0)
						{
							$this->ObTpl->set_var("kit_blk","");
							$this->ObTpl->set_var("kitElement_blk","");
							for($j=0;$j<$rsKitCount;$j++)
							{
								$this->ObTpl->set_var("TPL_VAR_OPTIONS","");
								$this->ObTpl->set_var("TPL_VAR_COUNT",$j+1);
								$kitElementUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rsKit[$j]->vSeoTitle;
								$comFunc->kitProductId=$rsKit[$j]->iProdId_FK;
								$comFunc->productId=$rsKit[$j]->iProdId_FK;
								#GET CART OPTIONS
								$kitOptions=$comFunc->m_orderKitProductOptions();
								$this->ObTpl->set_var("TPL_VAR_KITSKU",$rsKit[$j]->vSku);
								$this->ObTpl->set_var("TPL_VAR_QTY",$rsKit[$j]->iQty);
							
							//	print_r($comFunc->selectedOrderOptionId);
								#GET OPTIONS
								$this->ObTpl->set_var("TPL_VAR_OPTIONS",$comFunc->m_getOptions('1',$comFunc->selectedOptions,$comFunc->selectedOrderOptionId));

								$this->ObTpl->set_var("TPL_VAR_KITELEMENT_URL",$this->libFunc->m_safeUrl($kitElementUrl));	
								$this->ObTpl->set_var("TPL_VAR_KITELEMENT",$this->libFunc->m_displayContent($rsKit[$j]->vTitle));				
								$this->ObTpl->parse("kitElement_blk","TPL_KITELEMENT_BLK",true);
							}#END FOR I LOOP
							$this->ObTpl->parse("kit_blk","TPL_KIT_BLK");
						}#END IF
					}
					else
					{
						$comFunc->m_orderProductOptions();
						$comFunc->m_orderProductChoices();
						#GET OPTIONS
						$this->ObTpl->set_var("TPL_VAR_MAINOPTIONS",$comFunc->m_getOptions('0',$comFunc->selectedOptions,$comFunc->selectedOrderOptionId));
						#GET CHOICES
						$this->ObTpl->set_var("TPL_VAR_MAINCHOICES",$comFunc->m_getChoices($comFunc->selectedChoices));
					}

					# (OPTION And choice effected amount)
					$this->price=$comFunc->price;	

					#VOLUME DISCOUNT
					#DISCOUNT ACCORDING TO QTY
					$vDiscountPerCartElement=number_format(($rsOrderProduct[$i]->fDiscount),2);
					if($vDiscountPerCartElement>0)
					{
						$totalDiscountItem=$vDiscountPerCartElement*$rsOrderProduct[$i]->iQty;
						$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT",
							"Volume Discount: ".CONST_CURRENCY.$vDiscountPerCartElement." each Total: ".CONST_CURRENCY.$totalDiscountItem."<br />");
						$this->volDiscount=$this->volDiscount+$totalDiscountItem;
					}
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
					}	$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&amp;mode=".$rsOrderProduct[$i]->seo_title;
					$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$productUrl);	
					$this->ObTpl->set_var("TPL_VAR_ORDERPRODUCTID",$rsOrderProduct[$i]->iOrderProductid_PK);	
					$this->ObTpl->set_var("TPL_VAR_PRODUCTID",$rsOrderProduct[$i]->iProductid_FK);						$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rsOrderProduct[$i]->vSku));

					$this->price=$this->price+$rsOrderProduct[$i]->fPrice;
					$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($this->price,2));

					$this->ObTpl->set_var("TPL_VAR_QTY",$rsOrderProduct[$i]->iQty);
					$this->totalQty+=$rsOrderProduct[$i]->iQty;

					$this->total+=$rsOrderProduct[$i]->iQty*$this->price;
					$this->ObTpl->set_var("TPL_VAR_TOTAL",number_format($this->total,2));
					$this->subTotal=$this->subTotal+$this->total;
				
					$this->ObTpl->parse("cartproduct_blk","TPL_PRODUCT_BLK",true);	
				}#END FOR
				#END PRODUCT DISPLAY

				#******************** SUB TOTAL ****************	*************		
				$this->ObTpl->set_var("TPL_VAR_SUBTOTAL",number_format($this->subTotal,2));
				$this->grandTotal=$this->subTotal;
				$this->subTotal-=$this->volDiscount;
				#******************** PROMOTION CODE ************************
				if($rsOrder[0]->fPromoValue>0)
				{
					$this->ObTpl->set_var("TPL_VAR_PDISCOUNTS",number_format($rsOrder[0]->fPromoValue,2));
					$this->grandTotal-=$rsOrder[0]->fPromoValue;
					$this->ObTpl->parse("promodiscounts_blk","TPL_PROMODISCOUNTS_BLK");
				}
				
				#******************** VOLUME DISCOUNT ************************
				if($this->volDiscount>0)
				{
					$this->ObTpl->set_var("TPL_VAR_VOLDISCOUNT",number_format($this->volDiscount,2));
					$this->ObTpl->parse("volDiscounts_blk","TPL_VOLDISCOUNTS_BLK");
				}
				#CART WEIGHT *******
				if($rsOrder[0]->fShipByWeightPrice>0)
				{
					
					$this->ObTpl->set_var("TPL_VAR_WEIGHT",$rsOrder[0]->fShipByWeightKg);
					$this->ObTpl->set_var("TPL_VAR_WEIGHTPRICE",number_format($rsOrder[0]->fShipByWeightPrice,2));
					
					$this->grandTotal+=$rsOrder[0]->fShipByWeightPrice;
					$this->ObTpl->parse("cartWeight_blk","TPL_CARTWEIGHT_BLK");
				}
				#MEMBER POINTS
				if($rsOrder[0]->fMemberPoints>0)
				{
					$this->ObTpl->set_var("TPL_VAR_MPOINTS",number_format($rsOrder[0]->fMemberPoints,2));
					$this->grandTotal-=number_format($rsOrder[0]->fMemberPoints,2);
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
					$this->ObTpl->set_var("TPL_VAR_TMPOINTS",$rsCust[0]->fMemberPoints);
					$this->ObTpl->parse("totalmemberpoint_blk","TPL_TMPOINTS_BLK");
					}				


				#POSTAGE CALCULATION
				if($rsOrder[0]->fShipTotal>0)
				{
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Postage method (".$rsOrder[0]->vShipDescription.")");
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",CONST_CURRENCY.number_format($rsOrder[0]->fShipTotal,2));
					$this->grandTotal+=number_format($rsOrder[0]->fShipTotal,2);
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
					$this->ObTpl->set_var("TPL_VAR_CODPRICE",number_format($rsOrder[0]->fCodCharge ,2));
					$this->grandTotal+=number_format($rsOrder[0]->fCodCharge ,2);
					$this->ObTpl->parse("cod_blk","TPL_COD_BLK");
				}
				#CHECK FOR DISCOUNTS
				if($rsOrder[0]->fDiscount!=0)
				{
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE",number_format($rsOrder[0]->fDiscount,2));
					$this->grandTotal-=number_format($rsOrder[0]->fDiscount,2);
					$this->ObTpl->parse("discount_blk","TPL_DISCOUNT_BLK");	
				}#END DIS
				#CHECK FOR GIFTCERTIFICATES
				if($rsOrder[0]->fGiftcertTotal!=0)
				{
					$this->grandTotal-=number_format($rsOrder[0]->fGiftcertTotal,2);
					$this->ObTpl->set_var("TPL_VAR_GIFTCERTPRICE",number_format($rsOrder[0]->fGiftcertTotal,2));
					$this->ObTpl->parse("giftcert_blk","TPL_GIFTCERT_BLK");	
				}#END GIFT CERTIFICATE

				#CHECK FOR VAT
				if($rsOrder[0]->fTaxPrice>0)
				{
					$this->ObTpl->set_var("TPL_VAR_VAT",number_format($rsOrder[0]->fTaxRate,2));
					$this->ObTpl->set_var("TPL_VAR_VATPRICE",number_format($rsOrder[0]->fTaxPrice,2));
					$this->grandTotal+=number_format($rsOrder[0]->fTaxPrice,2);
					$this->ObTpl->parse("vat_blk","TPL_VAT_BLK");	
				}
	
				 $this->ObTpl->set_var("TPL_VAR_CURRENTTOTAL",number_format($rsOrder[0]->fTotalPrice,2));
				$this->ObTpl->parse("cart_blk","TPL_CART_BLK");		
				$this->ObTpl->parse("link_blk","TPL_LINK_BLK");	
				$this->ObTpl->set_var("TPL_VAR_MESSAGE","");

			}#END PRODUCT IF
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NO_ORDER);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");	
			}
		}#END ORDERS IF
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NO_ORDER);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");	
			}
		}#END URL TEMPER IF

		return($this->ObTpl->parse("return","TPL_ORDER_FILE"));

	}#END FUNCTIOn
	
	#FUNCTION RETURN DEPARTMENT NAME
	function m_getTitle($ownerid,$type)
	{
		if($ownerid!=0)
		{
			$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".DEPARTMENTS." D ,".FUSIONS." F WHERE iDeptid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			$row = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				$_SESSION['dspTitle']=" /".$this->libFunc->m_displayContent($row[0]->vTitle).$_SESSION['dspTitle'];
				$this->m_getTitle($row[0]->iOwner_FK,$row[0]->vOwnerType);
			}
		}

			return $_SESSION['dspTitle'];
			
	}

	#FIND PRODUCT DISPLAY
	function m_findProduct()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_FINDPRODUCT_FILE",$this->findTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_FINDPRODUCT_FILE","TPL_DEPARTMENT_BLK", "dept_blk");
		$this->ObTpl->set_block("TPL_FINDPRODUCT_FILE","TPL_ITEMS_BLK", "items_blk");
		if(!isset($this->request['orderid']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");
			exit;
		}
		#INTIALIZING VARIABLES
		if(!isset($this->request['owner']))
		{
			$this->request['owner']="0";
		}
		if(!isset($this->request['type']))
		{
			$this->request['type']="product";
		}
		if(!isset($this->request['otype']))
		{
			$this->request['otype']="department";
		}

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_OWNER",$this->request['owner']);
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);
		$this->ObTpl->set_var("TPL_VAR_OTYPE",$this->request['otype']);
		$this->ObTpl->set_var("TPL_VAR_ORDERID",$this->request['orderid']);
		
		#START DISPLAY DEPARETMENT BLOCK
		$this->obDb->query = "SELECT vTitle,iDeptId_PK FROM ".DEPARTMENTS.", ".FUSIONS."  WHERE iDeptId_PK=iSubId_FK AND vType='department'";
		$deptResult = $this->obDb->fetchQuery();
		 $recordCount=$this->obDb->record_count;
		#PARSING DEPARTMENT BLOCK
		$this->ObTpl->set_var("SELECTED1","selected");
		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				$_SESSION['dspTitle']="";		
				 $this->ObTpl->set_var("TPL_VAR_TITLE",$this->m_getTitle($deptResult[$i]->iDeptId_PK,'department'));
				$this->ObTpl->set_var("TPL_VAR_ID",$deptResult[$i]->iDeptId_PK);
				if(isset($this->request['postOwner']) && $this->request['postOwner'] == $deptResult[$i]->iDeptId_PK)
				{
					$this->ObTpl->set_var("SELECTED1","");
					$this->ObTpl->set_var("SELECTED2","selected");
				}
				else
				{
					$this->ObTpl->set_var("SELECTED2","");
				}
				
				$this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);
			}
		}
		else
		{
			$this->ObTpl->set_var("dept_blk","");
		}
		#END DISPLAY DEPARETMENT BLOCK

		#START DISPLAY ITEM BLOCK
		#IF TYPE IS CONTENT
		if($this->request['type']=="content" && isset($this->request['postOwner']))
		{
			#IF TYPE IS CONTENT AND ORPHAN IS SELECTED
			if($this->request['postOwner']=="orphan")
			{
				 $this->obDb->query= "SELECT vTitle,fusionid,iContentid_PK FROM ".CONTENTS." LEFT JOIN ".FUSIONS." ON iContentId_PK = iSubId_FK AND vType='content'" ;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if(empty($queryResult[$j]->fusionid) && $this->request['owner']!=$queryResult[$j]->iContentid_PK)
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iContentid_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
			else
			{
				#IF OTHER THAN ORPHAN
				$query = "SELECT vTitle,iContentid_PK  FROM ".CONTENTS.", ".FUSIONS."  WHERE  iContentid_PK=iSubId_FK AND iOwner_FK='".$this->request['postOwner']."' AND vOwnerType='department' AND vType='".$this->request['type']."'";
				$this->obDb->query=$query;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if($this->request['owner']!=$queryResult[$j]->iContentid_PK)
						{
						$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
						$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iContentid_PK);
						$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
		}
		elseif($this->request['type']=="product" && isset($this->request['postOwner']))#PRODUCT
		{#FOR ORPHAN PRODUCT
			if($this->request['postOwner']=="orphan")
			{
				 $this->obDb->query= "SELECT vTitle,fusionid,iProdId_PK FROM ".PRODUCTS." LEFT JOIN ".FUSIONS." ON iProdId_PK = iSubId_FK AND vType='product'" ;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if(empty($queryResult[$j]->fusionid) && $this->request['owner']!=$queryResult[$j]->iProdId_PK)
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
			else
			{#IF OTHER THAN ORPHAN
				$query = "SELECT vTitle,iProdId_PK FROM ".PRODUCTS.", ".FUSIONS."  WHERE iProdId_PK=iSubId_FK AND iOwner_FK='".$this->request['postOwner']."' AND vOwnerType='department' AND vType='".$this->request['type']."'";
				$this->obDb->query=$query;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if($this->request['owner']!=$queryResult[$j]->iProdId_PK)
						{
						$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
						$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
						$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
		}
		else
		{
			$this->ObTpl->set_var("items_blk","");
		}
		
		return($this->ObTpl->parse("return","TPL_FINDPRODUCT_FILE"));
	}#END FIND PRODUCT FUNCTION


	#FUNCTION TO DISPLAY ADD PRODUCT FORM
	function m_addProductForm()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;

		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ADDPRODUCT_FILE",$this->addTemplate);
		$this->ObTpl->set_block("TPL_ADDPRODUCT_FILE","TPL_KIT_BLK","kit_blk");
		$this->ObTpl->set_block("TPL_KIT_BLK","TPL_KITELEMENT_BLK","kitElement_blk");
		if(!isset($this->request['orderid']) || !isset($this->request['productid']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");
			exit;
		}
		
		$query = "SELECT vTitle,vSku,fPrice,iKit FROM ".PRODUCTS." WHERE iProdId_PK='".$this->request['productid']."'";
		$this->obDb->query=$query;
		$queryResult = $this->obDb->fetchQuery();

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("TPL_VAR_MAINOPTIONS","");
		$this->ObTpl->set_var("TPL_VAR_MAINCHOICES","");
		$this->ObTpl->set_var("TPL_VAR_ISKIT","0");
		$this->ObTpl->set_var("kit_blk","");	
		$this->ObTpl->set_var("kitElement_blk","");	
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_ORDERID",$this->request['orderid']);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);

		$this->ObTpl->set_var("TPL_VAR_PRODUCTID",$this->request['productid']);
		$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($queryResult[0]->vSku));
		$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[0]->vTitle));
		$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($queryResult[0]->fPrice));

		#PRODUCT ID FOR COMMON FUNCTION CLASS
		$comFunc->productId=$this->request['productid'];

		if($queryResult[0]->iKit==1)
		{
			$this->ObTpl->set_var("TPL_VAR_ISKIT","1");
			
			$this->obDb->query ="SELECT iProdId_FK,iQty,vTitle,vSeoTitle,vSku FROM ".PRODUCTKITS.",".PRODUCTS." WHERE iProdId_FK=iProdId_PK AND iKitId ='".$this->request['productid']."'";
			$rsKit=$this->obDb->fetchQuery();
			$rsKitCount=$this->obDb->record_count;
			if($rsKitCount>0)
			{
				for($i=0;$i<$rsKitCount;$i++)
				{
					$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
					$kitElementUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rsKit[$i]->vSeoTitle;
					$comFunc->productId=$rsKit[$i]->iProdId_FK;
					$this->ObTpl->set_var("TPL_VAR_KITSKU",$rsKit[$i]->vSku);
					$this->ObTpl->set_var("TPL_VAR_QTY",$rsKit[$i]->iQty);
				
					#GET OPTIONS
					$this->ObTpl->set_var("TPL_VAR_OPTIONS",$comFunc->m_getOptions('1'));

					$this->ObTpl->set_var("TPL_VAR_KITELEMENT_URL",$this->libFunc->m_safeUrl($kitElementUrl));	
					$this->ObTpl->set_var("TPL_VAR_KITELEMENT",$this->libFunc->m_displayContent($rsKit[$i]->vTitle));				
					$this->ObTpl->parse("kitElement_blk","TPL_KITELEMENT_BLK",true);
				}#END FOR I LOOP
				$this->ObTpl->parse("kit_blk","TPL_KIT_BLK");
			}#END IF
		}
		else
		{
			#GET OPTIONS
			$this->ObTpl->set_var("TPL_VAR_MAINOPTIONS",$comFunc->m_getOptions('0'));
			#GET CHOICES
			$this->ObTpl->set_var("TPL_VAR_MAINCHOICES",$comFunc->m_getChoices());
		}
		return($this->ObTpl->parse("return","TPL_ADDPRODUCT_FILE"));
	}#ef

	#FUNCTION TO DISPALY TRACKING FORM
	function m_trackingForm()
	{
		if(!isset($this->request['orderid']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");
			exit;
		}

		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_TRACKING_FILE", $this->trackTemplate);

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_ORDERID",$this->request['orderid']);
		$this->ObTpl->set_var("TPL_VAR_MODE","insert");
		$this->ObTpl->set_var("TPL_VAR_SEL1","");
		$this->ObTpl->set_var("TPL_VAR_SEL2","");
		$this->ObTpl->set_var("TPL_VAR_SEL3","");
		$this->ObTpl->set_var("TPL_VAR_SEL4","");
		$this->ObTpl->set_var("TPL_VAR_SEL5","");
		$this->ObTpl->set_var("TPL_VAR_SEL6","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");

		$this->shipper="";
		$this->trackNum="";
		$this->datePost=time();
		
		#DATABASE QUERY
		$this->obDb->query = "SELECT iInvoice,iCustomerid_FK FROM ".ORDERS." WHERE  iOrderid_PK='".$this->request['orderid']."'";
		$orderRs			=		$this->obDb->fetchQuery();
		$recordCnt		=		$this->obDb->record_count;
		if($recordCnt==0)
		{
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");
			exit;
		}
		$this->ObTpl->set_var("TPL_VAR_INVOICE",$orderRs[0]->iInvoice);
		$this->ObTpl->set_var("TPL_VAR_CUSTID",$orderRs[0]->iCustomerid_FK);

		#DATABASE QUERY
		$this->obDb->query = "SELECT vShipper,vTracking,tmShipDate FROM ".SHIPPINGDETAILS." WHERE  iOrderid_FK='".$this->request['orderid']."'";
		$queryResult	=		$this->obDb->fetchQuery();
		$recordNum		=		$this->obDb->record_count;
		if($recordNum>0)
		{
			$this->ObTpl->set_var("TPL_VAR_MODE","update");
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_POSTAGE_DEFINED);
			
			$this->shipper		=	$queryResult[0]->vShipper;
			$this->datePost	=	$queryResult[0]->tmShipDate;
			$this->trackNum	=	$queryResult[0]->vTracking;
		}

		switch($this->shipper)
		{
			case "Royal Mail":
				$this->ObTpl->set_var("TPL_VAR_SEL1","selected");
			break;
			case "Parcelforce":
				$this->ObTpl->set_var("TPL_VAR_SEL2","selected");
			break;
			case "DHL":
				$this->ObTpl->set_var("TPL_VAR_SEL3","selected");
				break;
			case "Yorkshire Parcels":
				$this->ObTpl->set_var("TPL_VAR_SEL4","selected");
				break;
			case "UPS":
				$this->ObTpl->set_var("TPL_VAR_SEL5","selected");
				break;
			case "FedEx":
				$this->ObTpl->set_var("TPL_VAR_SEL6","selected");
				break;
		}

		$this->ObTpl->set_var("TPL_VAR_SHIPPER",$this->libFunc->m_displayContent($this->shipper));
		$this->ObTpl->set_var("TPL_VAR_DATEPOST",$this->libFunc->dateFormat2($this->datePost));
		$this->ObTpl->set_var("TPL_VAR_TRACKNUM",$this->trackNum);

		return($this->ObTpl->parse("return","TPL_TRACKING_FILE"));
	}


	#FUNCTION TO DISPLAY STATUS
	function m_statusMessage()
	{
		if(!isset($this->request['invoice']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");
			exit;
		}

		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_STATUS_FILE", $this->statusTemplate);

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_ORDERID",$this->request['invoice']);
		$this->ObTpl->set_var("TPL_VAR_MSG",MSG_POSTAGE_UPDATED);

		$this->ObTpl->set_var("TPL_VAR_LINK",SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$this->request['invoice']);
		$this->ObTpl->set_var("TPL_VAR_LABEL",LBL_STATUS);

		return($this->ObTpl->parse("return","TPL_STATUS_FILE"));
	}

	function m_sendOrdersDetails()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ORDERMAIL_FILE",MODULES_PATH."order/templates/admin/orderMailDownload.tpl.htm");

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
		$downloadVariable = "";

		#QUERY ORDER TABLE
		$this->obDb->query = "SELECT tmOrderDate,vPayMethod,vShipDescription,fShipTotal,";
		$this->obDb->query.= "vFirstName,vLastName,vEmail,vAddress1,vAddress2,vCity,iInvoice,";
		$this->obDb->query.= "vState,vStateName,vCountry,vZip,vCompany,vPhone,vHomepage,";
		$this->obDb->query.= "vAltName,vAltAddress1,vAltAddress2,vAltCity,vAltState,vAltCountry,";
		$this->obDb->query.= "vAltStateName,vAltZip,vAltPhone,fCodCharge,fPromoValue,";
		$this->obDb->query.= "vDiscountCode,fDiscount,iGiftcert_FK,fGiftcertTotal,fMemberPoints,";
		$this->obDb->query.= "fShipByWeightPrice,fShipByWeightKg,iSameAsBilling,";
		$this->obDb->query.= "fTaxRate,fTaxPrice,tComments,vStatus,iPayStatus,fTotalPrice,iEarnedPoints,iCustomerid_FK";
		$this->obDb->query .= " FROM ".ORDERS." WHERE iOrderid_PK='".$this->request['orderid']."'";

		$qryResult = $this->obDb->fetchQuery();
		//echo "<pre>";print_r($qryResult);exit;
		$rCount=$this->obDb->record_count;
		if($rCount!=1)
		{
			$errrorUrl=SITE_URL."index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
		if($rCount>0)
		{
			$this->ObTpl->set_var("TPL_VAR_INVOICE",$qryResult[0]->iInvoice);	
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
			$this->ObTpl->set_var("TPL_VAR_ORDERSTATUS",$this->request['status']);
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
			$this->ObTpl->set_var("TPL_VAR_BILLCOUNTRY",$this->libFunc->m_displayContent($row_country[0]->vCountryName));

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
			$this->ObTpl->set_var("TPL_VAR_ALTNAME",$this->libFunc->m_displayContent($qryResult[0]->vAltName));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR1",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress1));
			$this->ObTpl->set_var("TPL_VAR_ALTADDR2",$this->libFunc->m_displayContent($qryResult[0]->vAltAddress2));
			$this->ObTpl->set_var("TPL_VAR_ALTCITY",$this->libFunc->m_displayContent($qryResult[0]->vAltCity));
			
			$this->ObTpl->set_var("TPL_VAR_ALTZIP",$this->libFunc->m_displayContent($qryResult[0]->vAltZip));
			$this->ObTpl->set_var("TPL_VAR_ALTPHONE",$this->libFunc->m_displayContent($qryResult[0]->vAltPhone));
		
			$this->obDb->query = "SELECT iOrderProductid_PK,iProductid_FK,iQty,iGiftwrapFK,fPrice,";
			$this->obDb->query.= "fDiscount,vTitle,vSku,iKit,tShortDescription,seo_title,iTaxable,iFreeship,vPostageNotes ";
			$this->obDb->query .= " FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['orderid']."'";
			$rsOrderProduct=$this->obDb->fetchQuery();
			$rsOrderProductCount=$this->obDb->record_count;
			foreach($rsOrderProduct as $key=>$value){
				$this->obDb->query = "SELECT vDownloadablefile FROM ".PRODUCTS." WHERE iProdid_PK = '".$rsOrderProduct[$key]->iProductid_FK."'";
				$downloadProduct=$this->obDb->fetchQuery();
				$rsOrderProduct[$key]->vDownloadablefile = $downloadProduct[0]->vDownloadablefile;
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
					
				$comFunc->orderId=$this->request['orderid'];
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
						if(!empty($rsOrderProduct[$i]->vDownloadablefile))
						{
							$downloadVariable = 1;
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
					}else{
						$this->ObTpl->set_var("TPL_VAR_FILELINK","");
						$this->ObTpl->parse("download_blk","TPL_DOWNLOAD_BLK");
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
						$this->ObTpl->set_var("TPL_VAR_TAXABLE","<em>".LBL_NOTAX."</em><br />");
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



					#POSTAGE CALCULATION**************************

				if($qryResult[0]->fShipTotal>0)
				{
					
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Postage method (".$qryResult[0]->vShipDescription.")");
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",CONST_CURRENCY.number_format($qryResult[0]->fShipTotal,2,'.',''));
					$this->grandTotal+=number_format($qryResult[0]->fShipTotal,2,'.','');
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
				}
				elseif($qryResult[0]->vShipDescription=="Free P&P")
				{
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$qryResult[0]->vShipDescription);
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE","No Charge");
					$this->ObTpl->parse("postage_blk","TPL_POSTAGE_BLK");
				}

				#COD PRICE(PAYMENT GATEWAY ADDITIONAL PRICE)
				if($qryResult[0]->fCodCharge>0)
				{
					$this->ObTpl->set_var("TPL_VAR_CODPRICE",number_format($qryResult[0]->fCodCharge ,2,'.',''));
					$this->grandTotal+=number_format($qryResult[0]->fCodCharge ,2,'.','');
					$this->ObTpl->parse("cod_blk","TPL_COD_BLK");
				}
				#CHECK FOR DISCOUNTS
				if($qryResult[0]->fDiscount!=0)
				{
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE",number_format($qryResult[0]->fDiscount,2,'.',''));
					$this->grandTotal-=number_format($qryResult[0]->fDiscount,2,'.','');
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
					$this->ObTpl->parse("vat_blk","TPL_VAT_BLK");
				}

				$this->grandTotal+=number_format($qryResult[0]->fDiscount,2,'.','');
				$this->ObTpl->set_var("TPL_VAR_CURRENTTOTAL",number_format($qryResult[0]->fTotalPrice,2,'.',''));
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
		if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", ADMIN_EMAIL)) {
			if($downloadVariable == "1" && ($qryResult[0]->vPayMethod == "mail" || $qryResult[0]->vPayMethod == "cod")){
				$result = $obMail->send(array($qryResult[0]->vEmail));
			}
		}
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
		if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", ORDER_EMAIL)) { 
			$result = $obMail->send(array(ORDER_EMAIL));
		}
		
		#WIRELESS EMAIL
		$Name=$this->libFunc->m_displayContent($qryResult[0]->vFirstName)." ".$this->libFunc->m_displayContent($qryResult[0]->vLastName);
		$obMail->setSubject(SITE_NAME." Invoice ".$qryResult[0]->iInvoice);
		$obMail->setCrlf("\n"); //to handle mails in Outlook Express
		$wirelesscontent="Invoice: ".$qryResult[0]->iInvoice."<br />";
		$wirelesscontent.="Customer: ".$Name."<br />";
		$wirelesscontent.="Total: ".CONST_CURRENCY.number_format($qryResult[0]->fTotalPrice,2,'.','');
		 
		$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$wirelesscontent));
		$obMail->setHtml($wirelesscontent,$txtcontent);
		$obMail->buildMessage();
		if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", WIRELESS_EMAIL)) {
			$result = $obMail->send(array(WIRELESS_EMAIL));
		}

	}

}#END CLASS
?>