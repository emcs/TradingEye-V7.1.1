<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_home
{
	#CONSTRUCTOR
	function c_home()
	{
		$this->libFunc=new c_libFunctions();
	}

	#FUNCTION TO DISPLAY PRODUCT REPORT
	function m_showHomePage()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("hContent",$this->Template);

		
		//BEST SELLERS BLOCK
		$this->ObTpl->set_block("hContent","TPL_BESTSELLERS_BLK","bestSellers_blk");
		$this->ObTpl->set_block("hContent","TPL_OUTOFSTOCK_BLK","outOfStock_blk");
		$this->ObTpl->set_block("hContent","TPL_LOWSTOCK_BLK","lowStock_blk");
		$this->ObTpl->set_block("hContent","TPL_NEWORDER_BLK","newOrder_blk");
		$this->ObTpl->set_block("hContent","TPL_PENDINGORDER_BLK","pendingOrder_blk");
		$this->ObTpl->set_block("hContent","TPL_TOPSEARCHES_BLK","topsearches_blk");
		$this->ObTpl->set_block("hContent","TPL_MOSTVIEWED_BLK","mostviewed_blk");
		$this->ObTpl->set_block("hContent","TPL_ABANDONMENT_BLK","abandonment_blk");
		$this->ObTpl->set_block("hContent","TPL_NEWCUSTOMERS_BLK","newcustomers_blk");
		$this->ObTpl->set_block("hContent","TPL_RETCUSTOMERS_BLK","retcustomers_blk");
		$this->ObTpl->set_block("hContent","TPL_BESTCUSTOMERS_BLK","bestcustomers_blk");
		$this->ObTpl->set_block("hContent","TPL_INCOMPLETEORDER_BLK","incomplete_blk");
		
		$this->ObTpl->set_block("TPL_NEWORDER_BLK","TPL_BLK_REGISTERED_USER","registered_user_blk");
		$this->ObTpl->set_block("TPL_NEWORDER_BLK","TPL_BLK_NONREGISTERED_USER","nonregistered_user_blk");
		
		$this->ObTpl->set_var("registered_user_blk","");
		$this->ObTpl->set_var("nonregistered_user_blk","");
		$this->ObTpl->set_var("bestSellers_blk","");
		$this->ObTpl->set_var("outOfStock_blk","");
		$this->ObTpl->set_var("lowStock_blk","");
		$this->ObTpl->set_var("newOrder_blk","");
		$this->ObTpl->set_var("pendingOrder_blk","");
		$this->ObTpl->set_var("topsearches_blk","");
		$this->ObTpl->set_var("mostviewed_blk","");
		$this->ObTpl->set_var("abandonment_blk","");
		$this->ObTpl->set_var("newcustomers_blk","");
		$this->ObTpl->set_var("retcustomers_blk","");
		$this->ObTpl->set_var("bestcustomers_blk","");
		$this->ObTpl->set_var("incomplete_blk","");
        $this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		
		#Getting current product ID's 
		$this->obDb->query = "SELECT * FROM ".PRODUCTS;
		$rowProductId = $this->obDb->fetchQuery();
		$rowIdCount = $this->obDb->record_count;
		if ($rowIdCount>0){
		$id_rows = array();
        for ($i=0; $i<$rowIdCount; $i++ )
        {
           $id_rows[$i] = $rowProductId[$i]->iProdid_PK;
        }

		
		#QUERY TO GET TOP TEN PRODUCTS
		$this->obDb->query = "SELECT iProductid_FK, SUM(iQty) as top_10,seo_title,fPrice,vTitle FROM ".ORDERPRODUCTS." WHERE iProductid_FK IN (" . implode(",", $id_rows). ")
        GROUP BY iProductid_FK ORDER BY top_10 DESC";
		$rowTop10 = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
				
		if($rowCount >0)
		{
			if ($rowCount > 5)
			$rowCount=5;
			for ($i=0;$i<$rowCount;$i++)
			{
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowTop10[$i]->seo_title;
				$this->ObTpl->set_var("TPL_VAR_TOPTENPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
				$this->ObTpl->set_var("TPL_VAR_TOPSELLERTITLE",$this->libFunc->m_displayContent($rowTop10[$i]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_TOPSELLERCOUNT",$rowTop10[$i]->top_10);	
				$this->ObTpl->set_var("TPL_VAR_TOPSELLERPRICE",$rowTop10[$i]->fPrice);	
				$this->ObTpl->parse("bestSellers_blk","TPL_BESTSELLERS_BLK",true);
			}
		}
		##END BEST SELLERS BLOCK
		}
		##START OUT OF STOCK BLOCK
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iUseinventory = 1 AND iInventory <= 0 ";
		$OutOfStock = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
	if ($rowCount > 5) $rowCount = 5;
		for($j=0;$j<$rowCount;$j++)
		{
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$OutOfStock[$j]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_OUTOFPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
				$this->ObTpl->set_var("TPL_VAR_OUTOFTITLE",$this->libFunc->m_displayContent($OutOfStock[$j]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_OUTOFINVENTORY",$OutOfStock[$j]->iInventory);			
			$this->ObTpl->parse("outOfStock_blk","TPL_OUTOFSTOCK_BLK",true);
		} //ef
		
		
		##END OUT OF STOCK BLOCK
		
		##START LOW STOCK BLOCK
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iUseinventory = 1 AND iInventory <= iInventoryMinimum";
		$LowStock = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		if ($rowCount > 5) $rowCount = 5;
		for($k=0;$k<$rowCount;$k++){
			
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$LowStock[$k]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_LOWPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
				$this->ObTpl->set_var("TPL_VAR_LOWOFTITLE",$this->libFunc->m_displayContent($LowStock[$k]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_MINIMUM",$LowStock[$k]->iInventoryMinimum);
				$this->ObTpl->set_var("TPL_VAR_LOWOFINVENTORY",$LowStock[$k]->iInventory);
			$this->ObTpl->parse("lowStock_blk","TPL_LOWSTOCK_BLK",true);	
		}
		##END LOW STOCK BLOCK
		
		##START NEW ORDER BLOCK
		$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vStatus = 'New' ORDER BY tmOrderDate DESC";
		$NewOrders = $this->obDb->fetchQuery();
		$NewOrdersCount = $this->obDb->record_count;
		
		if($NewOrdersCount >0){	
			if ($NewOrdersCount > 5) $NewOrdersCount = 5;
			for($l=0;$l<$NewOrdersCount;$l++)
			{
			
				$this->obDb->query = "SELECT iCustmerid_PK FROM ".CUSTOMERS." WHERE vEmail = '".$NewOrders[$l]->vEmail."' AND iRegistered=1";
				$CheckCust = $this->obDb->fetchQuery();
				$CheckCustCount = $this->obDb->record_count;
				
				if($CheckCustCount >0)
				{
					$this->ObTpl->set_var("TPL_VAR_CUSTOMERID",$CheckCust[0]->iCustmerid_PK);
					$this->ObTpl->set_var("TPL_VAR_CUSTOMER",$NewOrders[$l]->vFirstName." ".$NewOrders[$l]->vLastName);
                    $this->ObTpl->set_var("nonregistered_user_blk","");
                    $this->ObTpl->parse("registered_user_blk","TPL_BLK_REGISTERED_USER");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CUSTOMER",$NewOrders[$l]->vFirstName." ".$NewOrders[$l]->vLastName);
                    $this->ObTpl->set_var("registered_user_blk","");    
                    $this->ObTpl->parse("nonregistered_user_blk","TPL_BLK_NONREGISTERED_USER");
                }
                
                      
				$this->ObTpl->set_var("TPL_VAR_NEWORDERNUMBER",$NewOrders[$l]->iInvoice);
				$this->ObTpl->set_var("TPL_VAR_ORDERTOTAL",CONST_CURRENCY.number_format($NewOrders[$l]->fTotalPrice,2));
				$this->ObTpl->parse("newOrder_blk","TPL_NEWORDER_BLK",true);
			}	
		}
		##END NEW ORDER BLOCK
		
		##START PENDING ORDERS BLOCK		
		$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vStatus = 'Pending' ORDER BY tmOrderDate DESC";
		$PenOrders = $this->obDb->fetchQuery();
		$PenOrdersCount = $this->obDb->record_count;
		
		if($PenOrdersCount >0)
		{
			if ($PenOrdersCount > 5) $PenOrdersCount = 5;
			
			for($m=0;$m<$PenOrdersCount;$m++)	
			{
				$this->obDb->query = "SELECT * FROM ".CUSTOMERS." WHERE vEmail = '".$PenOrders[$m]->vEmail."'";
				$CheckCust = $this->obDb->fetchQuery();
				$CheckCustCount = $this->obDb->record_count;
				
				if($CheckCustCount >0)
				{
					$CustUrl=SITE_URL."user/adminindex.php?action=user.details&id=".$PenOrders[$m]->iCustomerid_FK;
					$this->ObTpl->set_var("TPL_VAR_CUSTURL","<a href=\"".$CustUrl."\">");
					$this->ObTpl->set_var("TPL_VAR_PENORDERCUSTOMER",$PenOrders[$m]->vFirstName." ".$PenOrders[$m]->vLastName);
					$this->ObTpl->set_var("TPL_VAR_CLOSETAG","</a>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CUSTURL","");
					$this->ObTpl->set_var("TPL_VAR_PENORDERCUSTOMER","Unregistered - see invoice");
					$this->ObTpl->set_var("TPL_VAR_CLOSETAG","");
				}
				
				$orderUrl=SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$PenOrders[$m]->iInvoice;
				$this->ObTpl->set_var("TPL_VAR_PENURL",$orderUrl);
				$this->ObTpl->set_var("TPL_VAR_PENORDERNUMBER",($PenOrders[$m]->iInvoice));
				$this->ObTpl->set_var("TPL_VAR_PENORDERTOTAL",CONST_CURRENCY.number_format($PenOrders[$m]->fTotalPrice,2));
				
				$this->ObTpl->parse("pendingOrder_blk","TPL_PENDINGORDER_BLK",true);	
			}		
		}		
		##END PENDING ORDERS BLOCK
		
		## START INCOMPLETE ORDERS BLOCK 
		$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE iOrderStatus = '0' ORDER BY tmOrderDate DESC";
		$INCOrders = $this->obDb->fetchQuery();
		$INCOrdersCount = $this->obDb->record_count;
	
		
			if($INCOrdersCount>0)
			{
				if ($INCOrdersCount > 5) $INCOrdersCount = 5;
				for($m=0;$m<$INCOrdersCount;$m++)	
				{
				
					$this->obDb->query = "SELECT * FROM ".CUSTOMERS." WHERE vEmail = '".$INCOrders[$m]->vEmail."'";
					$CheckIncCust = $this->obDb->fetchQuery();
					$CheckIncCount = $this->obDb->record_count;	
					
					if($CheckIncCount >0)
					{
						$CustUrl=SITE_URL."user/adminindex.php?action=user.details&id=".$INCOrders[$m]->iCustomerid_FK;
						$this->ObTpl->set_var("TPL_VAR_CUSTURL","<a href=\"".$CustUrl."\">");
						$this->ObTpl->set_var("TPL_VAR_INCCUSTOMER",$INCOrders[$m]->vFirstName." ".$INCOrders[$m]->vLastName);
						$this->ObTpl->set_var("TPL_VAR_CLOSETAG","</a>");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_CUSTURL","");
						$this->ObTpl->set_var("TPL_VAR_INCCUSTOMER","Unregistered - see invoice");
						$this->ObTpl->set_var("TPL_VAR_CLOSETAG","");
					}
					
					$orderUrl=SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$INCOrders[$m]->iInvoice;
					$this->ObTpl->set_var("TPL_VAR_INCURL",$orderUrl);
					$this->ObTpl->set_var("TPL_VAR_INCORDERNUMBER",($INCOrders[$m]->iInvoice));
					$this->ObTpl->set_var("TPL_VAR_INCORDERTOTAL",CONST_CURRENCY.number_format($INCOrders[$m]->fTotalPrice,2));
					
					$this->ObTpl->parse("incomplete_blk","TPL_INCOMPLETEORDER_BLK",true);	
				}	
			}	
		##END INCOMPLETE ORDERS GROUP
		
		##START TOP SEARCHES BLOCK
		$this->obDb->query = "SELECT * FROM ".SEARCHES." ORDER BY iNumberOfSearches DESC" ;
		$TopSearches = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		if ($rowCount > 5) $rowCount = 5;
		for($i=0;$i<$rowCount;$i++)
		{
			$this->ObTpl->set_var("TPL_VAR_TOPSEARCHTITLE",$this->libFunc->m_displayContent($TopSearches[$i]->vSearchTerm));
			$this->ObTpl->set_var("TPL_VAR_TOPSEARCHCOUNT",$this->libFunc->m_displayContent($TopSearches[$i]->iNumberOfSearches));
			$this->ObTpl->set_var("TPL_VAR_RECORDSFOUND",$this->libFunc->m_displayContent($TopSearches[$i]->iRecFoud));
			
			$this->ObTpl->parse("topsearches_blk","TPL_TOPSEARCHES_BLK",true);
			
		}
		##END TOP SEARCHES BLOCK
		
		
		##START MOST VIEWED PRODUCTS BLOCK
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iViewCount > 0 ORDER BY iViewCount DESC" ;
		$MostViewed = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		if ($rowCount > 5) $rowCount = 5;
		for($i=0;$i<$rowCount;$i++)
		{
			
			$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$MostViewed[$i]->vSeoTitle;
			$this->ObTpl->set_var("TPL_VAR_MOSTPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
			$this->ObTpl->set_var("TPL_VAR_MOSTTITLE",$this->libFunc->m_displayContent($MostViewed[$i]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_VIEWS",$this->libFunc->m_displayContent($MostViewed[$i]->iViewCount));
			$this->ObTpl->set_var("TPL_VAR_MOSTPRICE",CONST_CURRENCY.$this->libFunc->m_displayContent($MostViewed[$i]->fPrice));
			
			$this->ObTpl->parse("mostviewed_blk","TPL_MOSTVIEWED_BLK",true);
			
		}
		##END MOST VIEWED PRODUCTS BLOCK
		
		
		
		##START CART ABANDONMENT BLK
		$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iAddCount > 0 ORDER BY iAddCount DESC" ;
		$Abandonment = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		if ($rowCount > 5) $rowCount = 5;
		for($i=0;$i<$rowCount;$i++)
		{
			
			#QUERY TO GET TOP TEN PRODUCTS
			$this->obDb->query = "SELECT iProductid_FK, COUNT(*) as top_10 FROM ".ORDERPRODUCTS." WHERE iProductid_FK ='".$Abandonment[$i]->iProdid_PK."' GROUP BY iProductid_FK";
			$TotalPurchased = $this->obDb->fetchQuery();
			
			
			$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$Abandonment[$i]->vSeoTitle;
			$this->ObTpl->set_var("TPL_VAR_ABANDONPRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	
			$this->ObTpl->set_var("TPL_VAR_ABANDONTITLE",$this->libFunc->m_displayContent($Abandonment[$i]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_TOTALADDED",$this->libFunc->m_displayContent($Abandonment[$i]->iAddCount));
			$this->ObTpl->set_var("TPL_VAR_TOTALPURCHASED",$TotalPurchased[0]->top_10);
			
			$this->ObTpl->parse("abandonment_blk","TPL_ABANDONMENT_BLK",true);
			
		}
		##END CART ABANDONEMNT BLOCK
	
		##START NEW CUSTOMERS BLOCK
		$this->obDb->query = "SELECT * FROM ".CUSTOMERS." ORDER BY tmSignupDate DESC";
		$NewCust = $this->obDb->fetchQuery();
		$rowCount = $this->obDb->record_count;
		if ($rowCount > 5) $rowCount = 5;
		for($n=0;$n<$rowCount;$n++)
		{
				$CustUrl=SITE_URL."user/adminindex.php?action=user.details&amp;id=".$NewCust[$n]->iCustmerid_PK;
				$this->ObTpl->set_var("TPL_VAR_CUSTURL",$CustUrl);
				$this->ObTpl->set_var("TPL_VAR_NEWCUSTOMER",$NewCust[$n]->vFirstName." ".$NewCust[$n]->vLastName);	
				$signUpDate = date("d/m/Y",$NewCust[$n]->tmSignupDate);
				$this->ObTpl->set_var("TPL_VAR_SIGNUPDATE",$signUpDate);	
				
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." WHERE iCountryid_PK =".$NewCust[$n]->vCountry;
				$GetCountry = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_LOCATION",$GetCountry[0]->vCountryName);	
				$this->ObTpl->parse("newcustomers_blk","TPL_NEWCUSTOMERS_BLK",true);
		}		
		
		##END NEW CUSTOMERS BLOCK
		
		##START RETURNING CUSTOMERS BLOCK
		$this->obDb->query = "SELECT iCustomerid_FK,vEmail,vFirstName,vLastName, COUNT(vEmail) AS orderCount FROM ".ORDERS."  WHERE iOrderStatus>0 GROUP BY vEmail HAVING COUNT(vEmail)> 1 ORDER BY orderCount DESC"; 	
		$ReturnCust = $this->obDb->fetchQuery();
		$ReturnCount = $this->obDb->record_count;
		if ($ReturnCount > 5) $ReturnCount = 5;
		for($i=0;$i<$ReturnCount;$i++)
		{
			$Total= 0;
			$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vEmail ='".$ReturnCust[$i]->vEmail."' and iOrderStatus>0"; 	
			$GetOrders = $this->obDb->fetchQuery();
			$Count = $this->obDb->record_count;
			if ($Count > 5) $Count = 5;
			for($j=0;$j<$Count;$j++)
			{
				$Total = $Total + $GetOrders[$j]->fTotalPrice;
			}
			//Neeti
			$this->obDb->query = "SELECT iCustomerid_FK FROM ".ORDERS." WHERE iCustomerid_FK !='' and vEmail ='".$ReturnCust[$i]->vEmail."'"; 	
			$CustId = $this->obDb->fetchQuery();
			if($CustId[0]->iCustomerid_FK != "0"){
				$ReturnCust[$i]->iCustomerid_FK = $CustId[0]->iCustomerid_FK;
			}
			$CustUrl=SITE_URL."user/adminindex.php?action=user.details&id=".$ReturnCust[$i]->iCustomerid_FK;
			$this->ObTpl->set_var("TPL_VAR_CUSTURL",$CustUrl);
			$this->ObTpl->set_var("TPL_VAR_RETCUSTOMER",$ReturnCust[$i]->vFirstName." ".$ReturnCust[$i]->vLastName);	
			$this->ObTpl->set_var("TPL_VAR_NUMORDERS",$Count);	
			$this->ObTpl->set_var("TPL_VAR_TOTAL",CONST_CURRENCY.number_format($Total,2));
			$this->ObTpl->parse("retcustomers_blk","TPL_RETCUSTOMERS_BLK",true);	
		}
		##END RETURNING CUSTOMERS BLOCK
		
		##START BESTCUSTOMERS BLOCK
		$this->obDb->query = "SELECT iCustomerid_FK,vEmail,vFirstName,vLastName,SUM(fTotalPrice) AS total FROM ".ORDERS." WHERE iPayStatus =1 GROUP BY vEmail ORDER BY total DESC"; 	
		$BestCust = $this->obDb->fetchQuery();
		$BestCount = $this->obDb->record_count;
		if ($BestCount > 5) $BestCount = 5;
		
		for($i=0;$i<$BestCount;$i++)
		{
			
			$this->obDb->query = "SELECT * FROM ".ORDERS." WHERE vEmail ='".$BestCust[$i]->vEmail."' and iPayStatus =1"; 	
			$Orders = $this->obDb->fetchQuery();
			$OrderCount = $this->obDb->record_count;
	
			$totalAmount = $BestCust[$i]->total;
			//Neeti
			$this->obDb->query = "SELECT iCustomerid_FK FROM ".ORDERS." WHERE iCustomerid_FK !='' and vEmail ='".$BestCust[$i]->vEmail."'"; 	
			$CustId = $this->obDb->fetchQuery();
			if($CustId[0]->iCustomerid_FK != "0"){
				$BestCust[$i]->iCustomerid_FK = $CustId[0]->iCustomerid_FK;
			}
			$CustUrl=SITE_URL."user/adminindex.php?action=user.details&id=".$BestCust[$i]->iCustomerid_FK;
			$this->ObTpl->set_var("TPL_VAR_CUSTURL",$CustUrl);
			$this->ObTpl->set_var("TPL_VAR_BESTCUSTOMER",$BestCust[$i]->vFirstName." ".$BestCust[$i]->vLastName);	
			$this->ObTpl->set_var("TPL_VAR_BESTNUMORDERS",$OrderCount);	
			$this->ObTpl->set_var("TPL_VAR_BESTTOTAL",CONST_CURRENCY.number_format($totalAmount,2));
			
			$this->ObTpl->parse("bestcustomers_blk","TPL_BESTCUSTOMERS_BLK",true);
		}
		
		##END BESTCUSTOMERS BLOCK
		return($this->ObTpl->parse("return","hContent"));
	}


	#FUNCTION TO DISPLAY PRODUCT REPORT
	function m_showContentReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("cReport",$this->contentTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_block("cReport","TPL_MESSAGE_BLK","dspMess_blk");
		$this->ObTpl->set_block("cReport","TPL_CONTENT_BLK","dspContent_blk");
		$this->ObTpl->set_var("dspContent_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		 #QUERY TO GET PRODUCT REPORT 

		$query1 = "SELECT vTitle,F.iState as state,iContentid_PK,iOwner_FK,vOwnerType FROM ".CONTENTS." C LEFT JOIN ".FUSIONS." F ON iContentid_PK=iSubId_FK AND vType='content'";

		if(!empty($_POST))
		{
			$this->request['search']=trim($this->request['search']);
			if(!empty($this->request['search']))
			{
				$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT",$this->request['search']);

				$query1.=" WHERE ( vTitle LIKE '%".$this->request['search']."%'  || tShortDescription  LIKE '%".$this->request['search']."%' || tContent  LIKE '%".$this->request['search']."%')";
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT","");
			}

			if($this->request['order_type']=="desc")
			{
				$query1.=" ORDER BY ".$this->request['orderby']." DESC";
				$this->ObTpl->set_var("TPL_VAR_CHECK1","checked='checked'");
				$this->ObTpl->set_var("TPL_VAR_CHECK2","");
			}
			else
			{
				$query1.=" ORDER BY ".$this->request['orderby']." ASC" ;
				$this->ObTpl->set_var("TPL_VAR_CHECK2","checked='checked'");
				$this->ObTpl->set_var("TPL_VAR_CHECK1","");
			}
	
			if($this->request['orderby']=="iState")
			{
				$this->ObTpl->set_var("TPL_VAR_OSEL2","selected");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_OSEL1","selected");
			}
	
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_OSEL1","selected");
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT","");
			$this->ObTpl->set_var("TPL_VAR_CHECK1","checked='checked'");
			$this->ObTpl->set_var("TPL_VAR_CHECK2","");
		}
	
		$row1=$this->obDb->execQry($query1);
		$totalRecord=mysql_num_rows($row1);
		$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",$totalRecord);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		
		if(mysql_num_rows($row1)>0)
		{
			while($res1=mysql_fetch_object($row1))
			{
				if($res1->state==1)
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","On");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","Off");
				}
				if($res1->iOwner_FK=='')
				{
					$this->ObTpl->set_var("TPL_CONTENT_URL","#");
				}
				else
				{
					$this->ObTpl->set_var("TPL_CONTENT_URL",SITE_URL."ecom/adminindex.php?action=ec_show.home&owner=$res1->iContentid_PK&type=content");
				}
				$this->ObTpl->set_var("TPL_SUBPRODUCT_ID",$res1->iContentid_PK);
				$this->ObTpl->set_var("TPL_OWNER_ID",$res1->iOwner_FK);
				$this->ObTpl->set_var("TPL_OTYPE",$res1->vOwnerType);
				$this->ObTpl->set_var("TPL_SHOP_URL",SITE_URL."ecom/adminindex.php");
				$this->ObTpl->set_var("TPL_VAR_CONTENT_TITLE",$res1->vTitle);
				$this->ObTpl->set_var("dspMess_blk","");
				$this->ObTpl->parse("dspContent_blk","TPL_CONTENT_BLK",true);	
			}
		}
		else
		{
			$this->ObTpl->set_var("dspContent_blk","");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NO_CONTENT);
			$this->ObTpl->parse("dspMess_blk","TPL_MESSAGE_BLK");	
		}		
		$this->ObTpl->set_var("FORM_URL",SITE_URL."adminindex.php?action=home.creport");		
		$this->ObTpl->set_var("HELP_URL",SITE_URL."adminindex.php?action=home.help");		
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMB",SHOPBUILDER_HOME);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		return($this->ObTpl->parse("return","cReport"));
	}

	#FUNCTION TO DELETE PRODUCT
	function m_deleteProduct()
	{
		$obFile=new FileUpload(); 
		$this->imagePath=SITE_PATH."/images/";
		if(isset($this->request['id']) && !empty($this->request['id']))
		{
			$this->obDb->query = "select vImage1,vImage2,vImage3,vDownloadablefile from ".PRODUCTS." where iProdid_PK=".$this->request['id'];
			$this->imagePath=$this->imagePath."product/";
			$rs = $this->obDb->fetchQuery();
			$num_rows = $this->obDb->record_count;	
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage1) && file_exists($this->imagePath.$rs[0]->vImage1))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage1);

				if(!empty($rs[0]->vImage2) && file_exists($this->imagePath.$rs[0]->vImage2))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage2);		
				
				if(!empty($rs[0]->vImage3) && file_exists($this->imagePath.$rs[0]->vImage3))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage3);
				if(!empty($rs[0]->vDownloadablefile) && file_exists($this->imagePath.$rs[0]->vDownloadablefile))
					$obFile->deleteFile($this->imagePath.$rs[0]->vDownloadablefile);	


				$this->obDb->query = "select iOwner_FK,vOwnerType,iSort from ".FUSIONS." where iSubId_FK=".$this->request['id']."  AND vtype='product'";
				$rsOwner = $this->obDb->fetchQuery();

				$this->obDb->query = "SELECT iSubId_FK,vtype,iSort from ".FUSIONS." where iOwner_FK=".$this->request['id']."  AND vOwnerType='product'";
				$rsChild = $this->obDb->fetchQuery();
				$rsChildCount=$this->obDb->record_count;
			
				for($i=0;$i<$rsChildCount;$i++)
				{
					if($rsChild[$i]->vtype=='product')
					{
						$this->obDb->query = "DELETE from ".PRODUCTS." where iProdid_PK='".$rsChild[$i]->iSubId_FK."'";
						$this->obDb->updateQuery();
					}
					else
					{
						#DELETING CONTENT
						$this->obDb->query = "DELETE from ".CONTENTS." where iContentid_PK='".$rsChild[$i]->iSubId_FK."'";
						$this->obDb->updateQuery();
					}
				}

				#DELETING PRODUCT
				$this->obDb->query = "DELETE FROM ".PRODUCTS." where iProdid_PK=".$this->request['id'];
				$this->obDb->updateQuery();
			
				#DELETING RELATIONAL ENTRY FROM FUSION
				$this->obDb->query = "DELETE from ".FUSIONS." where (iOwner_FK='".$this->request['id']."' AND vOwnerType='product') OR iSubId_FK=".$this->request['id']."  AND vtype='product'";
				$this->obDb->updateQuery();

				#RESORTING
				if($rsOwner[0]->iOwner_FK!='')
				{
					$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 where (iSort>".$rsOwner[0]->iSort." AND iOwner_FK='".$rsOwner[0]->iOwner_FK."' AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='product') ";
					$this->obDb->updateQuery();
				}
			}
		}
		else
		{
			$this->request['owner']=0;
		}			
		$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=home.preport");	
	}#EF

	function m_deleteContent()
	{
		$obFile=new FileUpload(); 
		$this->imagePath=SITE_PATH."/images/";
		if(isset($this->request['id']) && !empty($this->request['id']))
		{
			$this->obDb->query = "select vImage1,vImage2,vImage3 from ".CONTENTS." where iContentid_PK=".$this->request['id'];
			$this->imagePath=$this->imagePath."content/";
			$rs = $this->obDb->fetchQuery();
			$num_rows = $this->obDb->record_count;	
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage1) && file_exists($this->imagePath.$rs[0]->vImage1))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage1);

				if(!empty($rs[0]->vImage2) && file_exists($this->imagePath.$rs[0]->vImage2))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage2);		
				
				if(!empty($rs[0]->vImage3) && file_exists($this->imagePath.$rs[0]->vImage3))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage3);


				#GETTING OWNER OF DELETED DEPARTMENT
				$this->obDb->query = "select iOwner_FK,vOwnerType,iSort from ".FUSIONS." where iSubId_FK=".$this->request['id']."  AND vtype='content'";
				$rsOwner = $this->obDb->fetchQuery();

				#DELETING CHILDS
				$this->obDb->query = "SELECT iSubId_FK,vtype,iSort from ".FUSIONS." where iOwner_FK=".$this->request['id']."  AND vOwnerType='content'";
				$rsChild = $this->obDb->fetchQuery();
				$rsChildCount=$this->obDb->record_count;
				for($i=0;$i<$rsChildCount;$i++)
				{
						#DELETING CONTENT
						$this->obDb->query = "DELETE from ".CONTENTS." where iContentid_PK='".$rsChild[$i]->iSubId_FK."'";
						$this->obDb->updateQuery();
				}
				
				#DELETING CONTENT
				$this->obDb->query = "DELETE from ".CONTENTS." where iContentid_PK=".$this->request['id'];
				$this->obDb->updateQuery();
				

				#DELETING RELATIONAL ENTRY FROM FUSION
				$this->obDb->query = "DELETE from ".FUSIONS." where (iOwner_FK='".$this->request['id']."' AND vOwnerType='content') OR iSubId_FK=".$this->request['id']."  AND vtype='content'";
				$this->obDb->updateQuery();

				#RESORTING
				if($rsOwner[0]->iOwner_FK!='')
				{
					$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 where (iSort>".$rsOwner[0]->iSort." AND iOwner_FK=".$rsOwner[0]->iOwner_FK." AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='content') ";
					$this->obDb->updateQuery();
				}
			}
		}
		else
		{
			$this->request['owner']=0;
		}			
		$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=home.creport");	
	}#EF
}#EC
?>