<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_brand
{
#CONSTRUCTOR
	function c_brand()
	{
		
		$this->pageTplPath=MODULES_PATH."default/templates/main/";
		$this->largeImage="largeImage.tpl.htm";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize=3;
		$this->totalRecords=0;
		$this->libFunc=new c_libFunctions();
	}


	#FUNCTION TO DISPLAY SEARCH PAGE
	function m_brandResults()
	{
		
		if(empty($this->request['mode']))
		{
			$this->request['mode']="";
		}
	
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_BRAND_FILE",$this->brandTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);	
		
		#SETTING TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_BRAND_FILE","TPL_MAINPRODUCT_BLK","mainproduct_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK","TPL_PRODUCT_BLK","product_blk");
		#INTIALIZING 
		$this->ObTpl->set_var("mainproduct_blk","");
		$this->ObTpl->set_var("product_blk","");
		#********************************************************************
		
				
		
	
		
		#TO QUERY PRODUCT TABLE
		 $this->obDb->query = "SELECT * FROM ".PRODUCTS." as p, ".FUSIONS." as f WHERE ";
		 $this->obDb->query .="(p.iVendorid_FK = '".$this->request['mode']."' and f.iState = '1' and p.iProdid_PK = f.iSubId_FK and f.vtype = 'product' ) group by p.iProdid_PK";
		 $row_product = $this->obDb->fetchQuery();
		 $prodCount=$this->obDb->record_count;
	
		
		$this->totalRecords+=$prodCount;
		if($prodCount>0)
		{
			for($i=0;$i<$prodCount;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_ONSALE","");
				if($row_product[$i]->iSale ==1)
				{
					$this->ObTpl->set_var("TPL_VAR_ONSALE","On Sale");
				}
			
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$row_product[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($productUrl));	$this->ObTpl->set_var("TPL_VAR_ID",$this->libFunc->m_displayContent($row_product[$i]->iProdid_PK));	$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($row_product[$i]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_DESC",$this->libFunc->m_displayContent($row_product[$i]->tShortDescription));

				##OVERALL PRODUCT STAR RANKING	
					$this->obDb->query = "SELECT SUM(vRank) as total, COUNT(iItemid_FK) as reviewcount FROM ".REVIEWS." WHERE iItemid_FK ='".$row_product[$i]->iProdid_PK."'";
					$OverallReviewRating = $this->obDb->fetchQuery();
					$ReviewRating = $OverallReviewRating[0]->total / $OverallReviewRating[0]->reviewcount;
					$ReviewRating = number_format($ReviewRating , 0, '.', '');
								
					$this->ObTpl->set_var("TPL_VAR_REVIEWCOUNT", $OverallReviewRating[0]->reviewcount." reviews");			
					switch ($ReviewRating)
					{
						case "0":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating0\" />Rating: 0/10</p>");
						break;
						case "1":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating1\" />Rating: 1/10</p>");
						break;
						case "2":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating2\" />Rating: 2/10</p>");
						break;
						case "3":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating3\" />Rating: 3/10</p>");
						break;
						case "4":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating4\" />Rating: 4/10</p>");
						break;
						case "5":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating5\" />Rating: 5/10</p>");
						break;
						case "6":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating6\" />Rating: 6/10</p>");
						break;
						case "7":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating7\" />Rating: 7/10</p>");
						break;
						case "8":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating8\" />Rating: 8/10</p>");
						break;
						case "9":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating9\" />Rating: 9/10</p>");
						break;
						case "10":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating10\" />Rating: 10/10</p>");
						break;	
					}
				
				
				if($row_product[$i]->iTaxable==1)
				{
					#GETTING VAT PRICE
					$vatPercent=$this->libFunc->m_vatCalculate();
					$vatPrice=number_format((($vatPercent*$row_product[$i]->fPrice)/100+$row_product[$i]->fPrice),2);
					if(INC_VAT_FLAG == 1)
					{
						if (INC_VAT==1) {
							$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",number_format($row_product[0]->fPrice,2)." (".CONST_CURRENCY.$vatPrice." inc. ".VAT_TAX_TEXT.")");
							$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($row_product[$i]->fPrice)." (".CONST_CURRENCY.$vatPrice." inc. ".VAT_TAX_TEXT.")");
						}
						else {
							$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",$vatPrice." inc. ".VAT_TAX_TEXT);
							$this->ObTpl->set_var("TPL_VAR_PRICE",$vatPrice." inc. ".VAT_TAX_TEXT);	
						}
					}
					else
					{
						if (INC_VAT==1) {
							$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",number_format($row_product[0]->fPrice,2)." (".CONST_CURRENCY.$vatPrice.")");
							$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($row_product[$i]->fPrice)." (".CONST_CURRENCY.$vatPrice.")");
						}
						else {
							$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",$vatPrice);
							$this->ObTpl->set_var("TPL_VAR_PRICE",$vatPrice);	
						}

					}

				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($row_product[$i]->fPrice));
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
		
		if($this->totalRecords==0)
		{
			$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",MSG_NO_SEARCHRESULT);	
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",$this->totalRecords."  Records Found");	
		}
	
		return($this->ObTpl->parse("return","TPL_BRAND_FILE"));
	}#END SEARCH CONTENT DISPLAY



}#END  CLASS
?>