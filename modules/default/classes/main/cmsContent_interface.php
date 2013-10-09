<?php
include_once($pluginInterface->plugincheck(MODULES_PATH."default/commonFunctions.php")); 
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_cmsContent
{
	#CONSTRUCTOR
	function c_cmsContent()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}
	
	#FUNCTION TO SHOW HOMEPAGE
	function m_showHomePage()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_CMS",$this->cmsTemplate);
		$this->ObTpl->set_block("TPL_VAR_CMS","TPL_MAINPRODUCT_BLK","mainproduct_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK","TPL_PRODUCT_BLK","product_blk");
		$this->ObTpl->set_block("TPL_VAR_CMS","TPL_MAINCONTENT_BLK","maincontent_blk");
		$this->ObTpl->set_block("TPL_MAINCONTENT_BLK","TPL_CONTENT_BLK","content_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_WISHLIST_BLK","wishlist_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_COMPARE_BLK","compare_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_CONTAINTDEPARTMENT_BLK","containdepartment_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_REVIEW_BLK","review_blk");
					
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_THEME_PATH",THEMEURLPATH);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("theme_blk","");
		$this->ObTpl->set_var("mainproduct_blk","");
		$this->ObTpl->set_var("product_blk","");
		$this->ObTpl->set_var("maincontent_blk","");
		$this->ObTpl->set_var("content_blk","");
		$this->ObTpl->set_var("wishlist_blk","");
		$this->ObTpl->set_var("compare_blk","");
		$this->ObTpl->set_var("containdepartment_blk","");
		$this->ObTpl->set_var("review_blk","");
	
	 #QUERY TO GET CMS
		##Defining language pack variables for headings on homepage
		$this->ObTpl->set_var("LANG_VAR_LATESTPRODUCTS","");
		$this->ObTpl->set_var("LANG_VAR_LATESTNEWS","");
		$this->ObTpl->set_var("LANG_VAR_LATESTNEWS",LATEST_NEWS);
		
		
		$this->obDb->query = "SELECT vSmalltext,tLargetext FROM ".SITESETTINGS." WHERE vDatatype='index_body'";
		$row_setting=$this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_HEADING",LANG_WELCOME_TO.SITE_NAME);
		$this->ObTpl->set_var("TPL_VAR_TEXT",$this->libFunc->m_displayContent1($row_setting[0]->tLargetext));

		
		

		
		
		
		//Top Sellers
		if(TOPSELLERS == 1)
		{
			
			$this->ObTpl->set_var("LANG_VAR_HOMEPAGEPRODUCTTEXT","Top Sellers");
			#Getting current product ID's 
			$this->obDb->query = "SELECT * FROM ".PRODUCTS;
			$rowProductId = $this->obDb->fetchQuery();
			$rowIdCount = $this->obDb->record_count;
		
			$id_rows = array();
	        for ($i=0; $i<$rowIdCount; $i++ )
	        {
	           $id_rows[$i] = $rowProductId[$i]->iProdid_PK;
	        }
	
			#QUERY TO GET TOP TEN PRODUCTS
			if ($rowIdCount>0){ 
			$this->obDb->query = "SELECT iProductid_FK, SUM(iQty) as top_10 FROM ".ORDERPRODUCTS." WHERE iProductid_FK IN (" . implode(",", $id_rows). ")
	 							GROUP BY iProductid_FK ORDER BY top_10 DESC";
			$rowTop10 = $this->obDb->fetchQuery();
			$rowCount = $this->obDb->record_count;
			}else{
             $rowCount=0;   
            }  
			if($rowCount>0)
			{
				for($i=0;$i<$rowCount;$i++)
				{
					
					$this->obDb->query = "SELECT * FROM ".PRODUCTS." WHERE iProdid_PK =".$rowTop10[$i]->iProductid_FK;
					$BestSellers = $this->obDb->fetchQuery();
					$BestCount = $this->obDb->record_count;
					#MARGIN CALCULATOR
					switch (MARGINSTATUS)
					{
						case "increase":
							$BestSellers[$i]->fPrice= ($BestSellers[$i]->fPrice * MARGINPERCENT/100 ) + $BestSellers[$i]->fPrice;
						break;
						case "decrease":
							$BestSellers[$i]->fPrice=  $BestSellers[$i]->fPrice - ($BestSellers[$i]->fPrice * MARGINPERCENT/100 );
						break;
						default:
							$BestSellers[$i]->fPrice = $BestSellers[$i]->fPrice;
						break;
						
					}
					#END MARGIN CALCULATOR
					
					$this->ObTpl->set_var("TPL_VAR_ONSALE","");
					if($BestSellers[0]->iSale ==1)
					{
						$this->ObTpl->set_var("TPL_VAR_ONSALE","<p class=\"onSale\">On Sale</p>");
					}
				
					$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$BestSellers[0]->vSeoTitle;
					$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	$this->ObTpl->set_var("TPL_VAR_ID",$this->libFunc->m_displayContent($BestSellers[0]->iProdid_PK));	$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($BestSellers[0]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_DESC",$this->libFunc->m_displayContent($BestSellers[0]->tShortDescription));
	
					#TO CHECK WHEATHER TO DISPLAY WISHLIST OR NOT MANAGED BY ADMIN
					if (USEWISHLIST == 1) 
					{
						##WISHLIST URL
						$wishListUrl = SITE_URL . "ecom/index.php?action=wishlist.add&mode=" . $BestSellers[0]->iProdid_PK;
						$this->ObTpl->set_var("TPL_VAR_WISHLISTLINK", $this->libFunc->m_safeUrl($wishListUrl));
						$this->ObTpl->parse("wishlist_blk", "TPL_WISHLIST_BLK");
					}
					
					#TO CHECK WHEATHER TO DISPLAY COMPARELIST OR NOT MANAGED BY ADMIN
					if (USECOMPARE == 1) 
					{
						$compareListUrl = SITE_URL . "ecom/index.php?action=compare.add&mode=" . $BestSellers[0]->iProdid_PK;
						$this->ObTpl->set_var("TPL_VAR_COMPARELINK", $this->libFunc->m_safeUrl($compareListUrl));
						$this->ObTpl->parse("compare_blk", "TPL_COMPARE_BLK");
					}
					
					
					if(CUSTOMER_REVIEWS==1){
					##OVERALL PRODUCT STAR RANKING	
					$this->obDb->query = "SELECT SUM(vRank) as total, COUNT(iItemid_FK) as reviewcount FROM ".REVIEWS." WHERE iItemid_FK ='".$rowTop10[$i]->iProductid_FK."'";
					$OverallReviewRating = $this->obDb->fetchQuery();
					$ReviewRating = $OverallReviewRating[0]->total / $OverallReviewRating[0]->reviewcount;
					$ReviewRating = number_format($ReviewRating , 0, '.', '');
					$this->ObTpl->set_var("TPL_VAR_REVIEWCOUNT", "<p class=\"reviewCount\">".$OverallReviewRating[0]->reviewcount." reviews</p>");			
									
					switch ($ReviewRating)
					{
						case "0":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating0\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "1":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating1\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "2":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating2\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "3":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating3\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "4":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating4\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "5":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating5\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "6":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating6\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "7":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating7\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "8":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating8\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "9":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating9\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "10":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating10\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;	
						}
					$this->ObTpl->parse("review_blk","TPL_REVIEW_BLK");
					}
					
					if($BestSellers[0]->iTaxable==1)
					{
						#GETTING VAT PRICE
						$vatPercent=$this->libFunc->m_vatCalculate();
						$vatPrice=number_format((($vatPercent*$BestSellers[0]->fPrice)/100+$BestSellers[0]->fPrice),2);
						if (INC_VAT_FLAG == 1 & INC_VAT==1) {
							$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",number_format($BestSellers[0]->fPrice,2)." (".CONST_CURRENCY.$vatPrice." inc. ".VAT_TAX_TEXT.")");
							$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($BestSellers[0]->fPrice)." (".CONST_CURRENCY.$vatPrice." inc. ".VAT_TAX_TEXT.")");
						}
						else if (INC_VAT_FLAG == 0 & INC_VAT==1) {
							$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",number_format($BestSellers[0]->fPrice,2)." (".CONST_CURRENCY.$vatPrice.")");
							$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($BestSellers[0]->fPrice)." (".CONST_CURRENCY.$vatPrice.")");
						}
						else {
							$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",$vatPrice." inc. ".VAT_TAX_TEXT);
							$this->ObTpl->set_var("TPL_VAR_PRICE",$vatPrice." inc. ".VAT_TAX_TEXT);	
						}
	
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent(number_format($BestSellers[0]->fPrice,2)));
					}
	
					if(!empty($BestSellers[0]->vImage1))
					{
						$img=$this->libFunc->m_checkFile($BestSellers[0]->vImage1,"product",$this->libFunc->m_displayContent($BestSellers[0]->vTitle));
						if($img)
						{
							$this->ObTpl->set_var("TPL_VAR_IMG",$img);
						}
						else
						{
							$this->ObTpl->set_var("TPL_VAR_IMG",MSG_NOIMG);
						}
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_IMG",MSG_NOIMG);
					}
					$this->ObTpl->parse("product_blk","TPL_PRODUCT_BLK",true);
				}
				$this->ObTpl->parse("mainproduct_blk","TPL_MAINPRODUCT_BLK",true);
			}
			
			
			
		}else//End top sellers
		{//Start Latest Products
		
			$this->ObTpl->set_var("LANG_VAR_HOMEPAGEPRODUCTTEXT","Latest Products");
			#QUERY TO GET PRODUCTS
			$this->obDb->query = "SELECT  iProdid_PK,vSeoTitle,vTitle,tShortDescription,vImage1,fPrice,fRetailPrice,iTaxable,iSale,iSubId_FK,fListPrice,iOwner_FK  FROM  ".PRODUCTS.",".FUSIONS." WHERE ( iProdid_PK=iSubId_FK AND iOwner_FK=0 AND vType='product' AND iState =1)  ORDER BY iSort";
			$row_product = $this->obDb->fetchQuery();
			$row_product_count = $this->obDb->record_count;
			
			if($row_product_count>0)
			{
				for($i=0;$i<$row_product_count;$i++)
				{
					#MARGIN CALCULATOR
					switch (MARGINSTATUS)
					{
						case "increase":
							$row_product[$i]->fPrice= ($row_product[$i]->fPrice * MARGINPERCENT/100 ) + $row_product[$i]->fPrice;
						break;
						case "decrease":
							$row_product[$i]->fPrice=  $row_product[$i]->fPrice - ($row_product[$i]->fPrice * MARGINPERCENT/100 );
						break;
						default:
							$row_product[$i]->fPrice = $row_product[$i]->fPrice;
						break;
						
					}
					#END MARGIN CALCULATOR
					$this->ObTpl->set_var("TPL_VAR_ONSALE","");
					if($row_product[$i]->iSale ==1)
					{
						$this->ObTpl->set_var("TPL_VAR_ONSALE","<p class=\"onSale\">On Sale</p>");
					}
		 	//--- Switch to retail price if Retail customer
				$comFunc = new c_commonFunctions();
				if ($comFunc->m_checkCustomerType()==1 && ENABLE_WHOLESALE==1 && $row_product[$i]->fRetailPrice>0){
				$row_product[$i]->fPrice=$row_product[$i]->fRetailPrice;
				}
			//----End switch price	       			
					
		//--------- Select all product in that department.			
				$this->obDb->query = "SELECT iOwner_FK,vTitle,vSeoTitle FROM ".DEPARTMENTS.", ".FUSIONS. " WHERE iSubId_FK=".$row_product[$i]->iProdid_PK." AND vType='product' AND iOwner_FK = iDeptid_PK AND iState =1" ;
				$dept_row = $this->obDb->fetchQuery();
				$deptcount = $this->obDb->record_count;
			
				if($deptcount>0){
						$this->ObTpl->set_var("TPL_VAR_DEPTNAME",$dept_row[0]->vTitle);
						$depturl = SITE_URL."ecom/index.php?action=ecom.details&mode=".$dept_row[0]->vSeoTitle;
						$this->ObTpl->set_var("TPL_VAR_DEPTURL",$this->libFunc->m_safeUrl($depturl));
						$this->ObTpl->parse("containdepartment_blk","TPL_CONTAINTDEPARTMENT_BLK");
				}
		//-----------------
					
					$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$row_product[$i]->vSeoTitle;
					$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	$this->ObTpl->set_var("TPL_VAR_ID",$this->libFunc->m_displayContent($row_product[$i]->iProdid_PK));	$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($row_product[$i]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_DESC",$this->libFunc->m_displayContent($row_product[$i]->tShortDescription));
					
					#TO CHECK WHEATHER TO DISPLAY WISHLIST OR NOT MANAGED BY ADMIN
					if (USEWISHLIST == 1) 
					{
						##WISHLIST URL
						$wishListUrl = SITE_URL . "ecom/index.php?action=wishlist.add&mode=" . $row_product[$i]->iProdid_PK;
						$this->ObTpl->set_var("TPL_VAR_WISHLISTLINK", $this->libFunc->m_safeUrl($wishListUrl));
						$this->ObTpl->parse("wishlist_blk", "TPL_WISHLIST_BLK");
					}
					
					#TO CHECK WHEATHER TO DISPLAY COMPARELIST OR NOT MANAGED BY ADMIN
					if (USECOMPARE == 1) 
					{
						$compareListUrl = SITE_URL . "ecom/index.php?action=compare.add&mode=" . $row_product[$i]->iProdid_PK;
						$this->ObTpl->set_var("TPL_VAR_COMPARELINK", $this->libFunc->m_safeUrl($compareListUrl));
						$this->ObTpl->parse("compare_blk", "TPL_COMPARE_BLK");
					}
					if(CUSTOMER_REVIEWS==1){
					##OVERALL PRODUCT STAR RANKING	
					$this->obDb->query = "SELECT SUM(vRank) as total, COUNT(iItemid_FK) as reviewcount FROM ".REVIEWS." WHERE iItemid_FK ='".$row_product[$i]->iProdid_PK."'";
					$OverallReviewRating = $this->obDb->fetchQuery();
					$ReviewRating = $OverallReviewRating[0]->total / $OverallReviewRating[0]->reviewcount;
					$ReviewRating = number_format($ReviewRating , 0, '.', '');
								
					$this->ObTpl->set_var("TPL_VAR_REVIEWCOUNT", $OverallReviewRating[0]->reviewcount." reviews");			
					switch ($ReviewRating)
					{
						case "0":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating0\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "1":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating1\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "2":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating2\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "3":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating3\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "4":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating4\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "5":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating5\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "6":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating6\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "7":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating7\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "8":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating8\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "9":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating9\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;
						case "10":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating10\">".$OverallReviewRating[0]->reviewcount." reviews</p>");
						break;	
					}
				$this->ObTpl->parse("review_blk","TPL_REVIEW_BLK");
				}
					if($row_product[$i]->iTaxable==1)
					{
						if(INC_VAT_FLAG == 1)
						{
							if(NETGROSS ==1 && INC_VAT ==1 )
							{
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((( $row_product[$i]->fPrice * $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($row_product[$i]->fPrice,2,'.','') . " inc. " . VAT_TAX_TEXT);
							
							}elseif(NETGROSS ==1 && INC_VAT ==0)
							{
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((( $row_product[$i]->fPrice* $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($row_product[$i]->fPrice, 2, '.', '') . " (" . CONST_CURRENCY . $vatPrice . " inc. " . VAT_TAX_TEXT . ")");		
						
							}elseif(NETGROSS ==0 && INC_VAT ==1)
							{
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((($row_product[$i]->fPrice * $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($row_product[$i]->fPrice, 2, '.', '') . " (" . CONST_CURRENCY . $vatPrice . " inc. " . VAT_TAX_TEXT . ")");
							
							}else
							{	
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((($row_product[$i]->fPrice * $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", $vatPrice." inc. ".VAT_TAX_TEXT);
							}
						}
						else
						{
							if(NETGROSS ==1 && INC_VAT ==1 )
							{
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((( $row_product[$i]->fPrice * $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($row_product[$i]->fPrice,2,'.',''));
							
							}elseif(NETGROSS ==1 && INC_VAT ==0)
							{
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((( $row_product[$i]->fPrice* $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($row_product[$i]->fPrice, 2, '.', '') . " (" . CONST_CURRENCY . $vatPrice . ")");		
						
							}elseif(NETGROSS ==0 && INC_VAT ==1)
							{
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((($row_product[$i]->fPrice * $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($row_product[$i]->fPrice, 2, '.', '') . " (" . CONST_CURRENCY . $vatPrice . ")");
							
							}else
							{	
								$vatPercent = $this->libFunc->m_vatCalculate();
								$vatPrice = number_format((($row_product[$i]->fPrice * $vatPercent) / 100 + $row_product[$i]->fPrice), 2, '.', '');	
								$this->ObTpl->set_var("TPL_VAR_PRICE", $vatPrice);
							}
						}
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent(number_format($row_product[$i]->fPrice,2)));
					}
					if (!$this->libFunc->m_isNull($row_product[$i]->fListPrice) && $row_product[$i]->fListPrice > 0) {
					$this->ObTpl->set_var("TPL_VAR_RRP_AMOUNT", "<span class=\"rrp\">".RRP_TEXT . ": <strike>" . CONST_CURRENCY . number_format($row_product[$i]->fListPrice, 2, '.', '') . "</strike></span>");
					}
					else
					{
					$this->ObTpl->set_var("TPL_VAR_RRP_AMOUNT","");
					}
					if(!empty($row_product[$i]->vImage1))
					{
						$img=$this->libFunc->m_checkFile($row_product[$i]->vImage1,"product",$this->libFunc->m_displayContent($row_product[$i]->vTitle));
						if($img)
						{
							$this->ObTpl->set_var("TPL_VAR_IMG",$img);
						}
						else
						{
							$this->ObTpl->set_var("TPL_VAR_IMG",MSG_NOIMG);
						}
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_IMG",MSG_NOIMG);
					}
					$this->ObTpl->parse("product_blk","TPL_PRODUCT_BLK",true);
				}
				$this->ObTpl->parse("mainproduct_blk","TPL_MAINPRODUCT_BLK",true);
			}
		}
		 #QUERY TO GET content
		$this->obDb->query = "SELECT iContentid_PK,vSeoTitle,vTitle,vImage1  FROM  ".CONTENTS.",".FUSIONS." WHERE (iContentid_PK=iSubId_FK AND iOwner_FK='0' AND vType='content' AND iState =1) ORDER BY iSort";
		$rowContent = $this->obDb->fetchQuery();
		$contentCount = $this->obDb->record_count;

		if($contentCount>0)
		{
			for($i=0;$i<$contentCount;$i++)
			{
				$contentUrl=SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$rowContent[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_CONTENTURL",$this->libFunc->m_safeUrl($contentUrl));	$this->ObTpl->set_var("TPL_VAR_ID",$this->libFunc->m_displayContent($rowContent[$i]->iContentid_PK));
				
				if(!empty($rowContent[$i]->vImage1))
				{
					 $img=$this->libFunc->m_checkFile($rowContent[$i]->vImage1,"content",$this->libFunc->m_displayContent($rowContent[$i]->vTitle));
					if($img)
					{
						$this->ObTpl->set_var("TPL_VAR_TITLE",$img);
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rowContent[$i]->vTitle));			
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rowContent[$i]->vTitle));			
				}
				$this->ObTpl->parse("content_blk","TPL_CONTENT_BLK",true);
			}
			$this->ObTpl->parse("maincontent_blk","TPL_MAINCONTENT_BLK",true);
		}
		
		$this->obDb->query = "SELECT * FROM ".COMPANYSETTINGS;
		$compset=$this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_SITENAME",$this->libFunc->m_displayContent($compset[0]->vCname));
		$this->ObTpl->set_var("TPL_VAR_SLOGAN",$this->libFunc->m_displayContent($compset[0]->vSlogan));
		return($this->ObTpl->parse("return","TPL_VAR_CMS"));
	}

	// CHECK FOR CSS EXISTENCE
	function m_checkCss($cssFile)
	{
		$cssPath=SITE_PATH."css/";
		//echo $cssPath.$cssFile;
		if(file_exists($cssPath.$cssFile) && filesize($cssPath.$cssFile)>0)
		{
			return $cssFile;
		}
		else
		{
			return DEFAULT_CSS;
		}
	}

	#FUNCTION TO DISPLAY CMS CONTENT
	function m_showCmsContent()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_CMS",$this->cmsTemplate);

		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CARTURL","");


		 #QUERY TO GET CMS
		if(!isset($this->request['mode']))
		{
			$this->request['mode']='';
		}
		
		if($this->request['mode']=='member_points')
		{
			$cartUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
			$this->ObTpl->set_var("TPL_VAR_CARTURL","Return to your <a href='".$cartUrl."'>shopping basket</a>");
		}
		$this->obDb->query = "SELECT vSmalltext,tLargetext FROM ".SITESETTINGS." WHERE vDatatype='".$this->request['mode']."'";
		$row_setting=$this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_HEADING",$this->libFunc->m_displayContent1($row_setting[0]->vSmalltext));
		$this->ObTpl->set_var("TPL_VAR_TEXT",$this->libFunc->m_displayContent1($row_setting[0]->tLargetext));
		
		return($this->ObTpl->parse("return","TPL_VAR_CMS"));
	}


	#FUNCTION TO SHOW CONTACT FORM
	function m_showContactForm()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_CMS",$this->cmsTemplate);
		$this->ObTpl->set_block("TPL_VAR_CMS","TPL_MSG_BLK","msg_blk");
		$this->ObTpl->set_block("TPL_VAR_CMS","BillCountry","nBillCountry");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_VATNUMBER","");
		$this->ObTpl->set_var("TPL_VAR_REGISTERNUMBER","");
		$this->ObTpl->set_var("TPL_VAR_COMPANY","");
		$this->ObTpl->set_var("TPL_VAR_ADDRESS","");
		$this->ObTpl->set_var("TPL_VAR_CITY","");
		$this->ObTpl->set_var("TPL_VAR_ZIP","");
		$this->ObTpl->set_var("TPL_VAR_PHONE","");
		$this->ObTpl->set_var("TPL_VAR_STATE","");
		$this->ObTpl->set_var("TPL_VAR_COUNTRY","");
		$this->ObTpl->set_block("TPL_VAR_CMS","TPL_CAPTCHA_BLK","captcha_blk");
		$this->ObTpl->set_var("captcha_blk","");
		$this->ObTpl->set_var("msg_blk","");

		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		 #QUERY TO GET CMS
		$this->obDb->query = "SELECT vSmalltext,tLargetext FROM ".SITESETTINGS." WHERE vDatatype='contact'";
		$row_setting=$this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_HEADING",$this->libFunc->m_displayContent1($row_setting[0]->vSmalltext));
		$this->ObTpl->set_var("TPL_VAR_TEXT",$this->libFunc->m_displayContent1($row_setting[0]->tLargetext));
	
		$this->obDb->query ="SELECT vAddress,vCity,vZip,vState,vStateName,vCountry FROM  ".COMPANYSETTINGS;
		$rsCompany=$this->obDb->fetchQuery();
		if(SITE_NAME!="")
		{
			$this->ObTpl->set_var("TPL_VAR_COMPANY",
			$this->libFunc->m_displayContent(SITE_NAME)."<br />");
		}
		if(!$this->libFunc->m_isNull($rsCompany[0]->vAddress))
		{
			$this->ObTpl->set_var("TPL_VAR_ADDRESS",
			nl2br($this->libFunc->m_displayContent($rsCompany[0]->vAddress))."<br />");
		}
		if(!$this->libFunc->m_isNull($rsCompany[0]->vCity))
		{
			$this->ObTpl->set_var("TPL_VAR_CITY",
			$this->libFunc->m_displayContent($rsCompany[0]->vCity)."<br />");
		}
		if(!$this->libFunc->m_isNull($rsCompany[0]->vZip))
		{
			$this->ObTpl->set_var("TPL_VAR_ZIP",
			$this->libFunc->m_displayContent($rsCompany[0]->vZip)."<br />");
		}
		if(SITE_PHONE!="")
		{
			$this->ObTpl->set_var("TPL_VAR_PHONE","<strong>Phone no:</strong> ".$this->libFunc->m_displayContent(SITE_PHONE)."<br />");
		}
		if(!$this->libFunc->m_isNull($rsCompany[0]->vState) || !$this->libFunc->m_isNull($rsCompany[0]->vStateName)){
			if($rsCompany[0]->vState>1)
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$rsCompany[0]->vState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_STATE",
				$this->libFunc->m_displayContent($row_state[0]->vStateName)."<br />");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_STATE",
				$this->libFunc->m_displayContent($rsCompany[0]->vStateName)."<br />");
			}
		}

		if(!$this->libFunc->m_isNull($rsCompany[0]->vCountry))
		{
			$this->obDb->query ="SELECT vCountryName FROM ".COUNTRY." where ";
			$this->obDb->query.="iCountryId_PK  = '".$rsCompany[0]->vCountry."'";
			$row_country = $this->obDb->fetchQuery();

			$this->ObTpl->set_var("TPL_VAR_COUNTRY",
			$this->libFunc->m_displayContent($row_country[0]->vCountryName)."<br />");
		}
		
		if(trim(COMPANY_VATNUMBER)!="")
		{
			$this->ObTpl->set_var("TPL_VAR_VATNUMBER", "<strong>".VAT_TAX_TEXT."Registration No:</strong> ".COMPANY_VATNUMBER."");
		}

		if(trim(COMPANY_REGISTERNUMBER)!="")
		{
			$this->ObTpl->set_var("TPL_VAR_REGISTERNUMBER","<strong>Company Registration No:</strong> ".COMPANY_REGISTERNUMBER."");
		}


		$this->contactName			=$this->libFunc->ifSet($this->request,"sName","");
		$this->contactEmail			=$this->libFunc->ifSet($this->request,"sEmail","");
		$this->contactAddress1	=$this->libFunc->ifSet($this->request,"sAddress1","");
		$this->contactAddresss2	=$this->libFunc->ifSet($this->request,"sAddress2","");
		$this->contactPhone		=$this->libFunc->ifSet($this->request,"sWorkPhone","");
		$this->contactCountry		=$this->libFunc->ifSet($this->request,"sCountry","");
		$this->contactRequest		=$this->libFunc->ifSet($this->request,"sComments","");
		$this->ObTpl->set_var("TPL_VAR_SELECTED","");
		$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($this->contactName));
		$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($this->contactEmail));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1",$this->libFunc->m_displayContent($this->contactAddress1));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2",$this->libFunc->m_displayContent($this->contactAddresss2));
		$this->ObTpl->set_var("TPL_VAR_CONTACTPHONE",$this->libFunc->m_displayContent($this->contactPhone));
		$this->ObTpl->set_var("TPL_VAR_REQUEST",$this->libFunc->m_displayContent($this->contactRequest));
		
		$this->obDb->query = "SELECT vCountryName,iCountryId_PK FROM  ".COUNTRY." ORDER BY iSortFlag,vCountryName";
		$row_country = $this->obDb->fetchQuery();
		$row_country_count = $this->obDb->record_count;
		
		if(CAPTCHA_CONTACTUS){
			$this->ObTpl->parse("captcha_blk","TPL_CAPTCHA_BLK",true);
		}


		# Loading billing country list	
		for($i=0;$i<$row_country_count;$i++) {
			if($row_country[$i]->vCountryName==$this->contactCountry){
				$this->ObTpl->set_var("TPL_VAR_SELECTED","selected=\"selected\"");	
			}elseif($row_country[$i]->iCountryId_PK==SELECTED_COUNTRY){
				$this->ObTpl->set_var("TPL_VAR_SELECTED","selected=\"selected\"");
			}else{
				$this->ObTpl->set_var("TPL_VAR_SELECTED","");
			}
			$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("nBillCountry","BillCountry",true);
		}
		return($this->ObTpl->parse("return","TPL_VAR_CMS"));
	}

	#FUNCTION TO VALIDATE CONTACT US
	function m_validateContact()
	{
		$data = implode(",",$this->request);
		if(CAPTCHA_CONTACTUS){
			if($_SESSION['image_auth_string'] != $this->request['cap_key']){
				$this->err=1;
				$this->errMsg.=MSG_INVALID_CAP_KEY."<br />";
			}
		}
		if(!$this->libFunc->m_validEmailData($data)){
			$this->err=1;
			$this->errMsg.=MSG_INVALID_DATA."<br />";
		}
		#MODIFIED ON 10-05-07
		if($this->libFunc->m_isNull($this->request['sName']))
		{
			$this->err=1;
			$this->errMsg=MSG_NAME_EMPTY."<br />";
		}
		if($this->libFunc->m_isNull($this->request['sEmail']))
		{
			$this->err=1;
			$this->errMsg.=MSG_EMAILADDRESS_EMPTY."<br />";
		}
		if(!$this->libFunc->m_validateEmail($this->request['sEmail']))
		{
			$this->err=1;
			$this->errMsg.=MSG_INVALID_EMAIL."<br />";
		}
		return $this->err;
	}#EF

	#FUNCTION TO SHOW CONTACT FORM
	function m_showThanks()
	{
		$this->ObTpl=new template();
		
		$this->ObTpl->set_file("TPL_VAR_CMS",$this->cmsTemplate);
		$this->ObTpl->set_block("TPL_VAR_CMS","TPL_NEWSLETTER_BLK","newsletter_blk");
		$this->ObTpl->set_block("TPL_VAR_CMS","TPL_ENQUIRE_BLK","enquiry_blk");
		
		$this->ObTpl->set_var("newsletter_blk","");
		$this->ObTpl->set_var("enquiry_blk","");
		
		if (!isset($_SESSION['newslettererror']))
		{
			$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
			$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
			$this->ObTpl->parse("enquiry_blk","TPL_ENQUIRE_BLK");
		}else
		{
			$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
			$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",$_SESSION['newslettererror']);
			$this->ObTpl->parse("newsletter_blk","TPL_NEWSLETTER_BLK");	
			unset ($_SESSION['newslettererror']);
		
		}	
		return($this->ObTpl->parse("return","TPL_VAR_CMS"));
	
	
	
	}#EF

	#FUNCTION TO UNSUBSCRIBE
	function m_unsubscribe()
	{
		$this->ObTpl=new template();		
		$this->ObTpl->set_file("TPL_VAR_CMS",$this->cmsTemplate);
		$this->ObTpl->set_block("TPL_VAR_CMS","TPL_ENQUIRE_BLK","enquiry_blk");
		$this->ObTpl->set_var("enquiry_blk","");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_UNS_MSG","");
		$this->ObTpl->parse("enquiry_blk","TPL_ENQUIRE_BLK");
		//$accounturl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=contactus.unsubscribe&mode=".$this->request['mode']."&unsub=Y");
		if(isset($this->request['unsub'])){
			$this->obDb->query = "DELETE FROM ".NEWSLETTERS." WHERE iSignup_PK='".$this->request['mode']."'";
			$this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_UNS_MSG","You have been unsubscribed from the newsletter.");
			$this->ObTpl->parse("enquiry_blk","");
		}
		$this->ObTpl->set_var("TPL_V_ID",$this->request['mode']);
		
		return($this->ObTpl->parse("return","TPL_VAR_CMS"));
	}#EF


}#END CLASS
?>