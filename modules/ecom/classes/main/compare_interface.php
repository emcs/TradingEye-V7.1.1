<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

class c_compareInterface
{
#CONSTRUCTOR
	function c_compareInterface()
	{
		$this->templatePath=THEMEPATH."ecom/templates/main/";
		$this->pageTplPath=THEMEPATH."default/templates/main/";
		$this->largeImage="largeImage.tpl.htm";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize="5";
		$this->libFunc=new c_libFunctions();
	}

	#FUNCTION TO DISPLAY PRODUCT DETAILS
	function m_showComparelist()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_COMPARE_FILE",$this->template);

		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_USERNAME",$this->libFunc->m_displayContent($_SESSION['username']));
		
		#SETTING TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_COMPARE_FILE","TPL_MAINPRODUCT_BLK","mainproduct_blk");
		$this->ObTpl->set_block("TPL_COMPARE_FILE","TPL_MSG_BLK","msg_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK","TPL_PRODUCT_BLK","product_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_ATTRIBUTEFIELD_BLK","attributefield_blk");
	

		#INTIALIZING 
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("mainproduct_blk","");
		$this->ObTpl->set_var("product_blk","");
		
		$this->ObTpl->set_var("attributefield_blk","");
		
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("fieldname_blk","");

		$this->ObTpl->set_var("TPL_VAR_COMPARELIST",MSG_VAR_COMPARELIST);
		#*******************DISPLAY MAIN PRODUCT*****************************
		##WISHLIST URL
		$compareModifyUrl=SITE_URL."ecom/index.php?action=compare.modify";
		$this->ObTpl->set_var("TPL_VAR_COMPAREMODIFYURL",$this->libFunc->m_safeUrl($compareModifyUrl));	
	
	
		#******************DISPLAY WISHLIST PRODUCT**********************
		#QUERY TO GET PRODUCTS UNDER SELECTED 
		$query ="SELECT iCompareid_PK,vTitle,vSku,vSeoTitle,tShortDescription,fListPrice,fPrice,iProdid_PK,iVendorid_FK  FROM ".PRODUCTS.",".COMPARE." WHERE iProductid_FK=iProdid_PK AND iCustomerid_FK='".$_SESSION['userid']."'";
		$pn			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		echo $this->request['mode'];
		$extraStr	="action=ecom.compare&mode=".$this->request['mode'];
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
				$this->ObTpl->set_var("TPL_VAR_ID",$this->libFunc->m_displayContent($rowProduct[$i]->iCompareid_PK));
				$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rowProduct[$i]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($rowProduct[$i]->vSku));
				$this->ObTpl->set_var("TPL_VAR_SHORTDESC",$this->libFunc->m_displayContent($rowProduct[$i]->tShortDescription));
				$this->ObTpl->set_var("TPL_VAR_PRICERRP", CONST_CURRENCY.number_format($rowProduct[$i]->fListPrice, 2, '.', ''));
				$this->ObTpl->set_var("TPL_VAR_PRICEMAIN",CONST_CURRENCY.number_format($rowProduct[$i]->fPrice, 2, '.', ''));
				
				$this->obDb->query = "SELECT vCompany FROM " . SUPPLIERS . " WHERE iVendorid_PK='" . $rowProduct[$i]->iVendorid_FK . "'";
				$vendor = $this->obDb->fetchQuery();
				if ($vendor[0]->vCompany!=""){
				$this->ObTpl->set_var("TPL_VAR_SUPPLIER",$vendor[0]->vCompany);
				}else{
				$this->ObTpl->set_var("TPL_VAR_SUPPLIER","N/A");	
				}
				
				#DISPLAY PRODUCT ATTRIBUTE 
						$this->obDb->query="SELECT * FROM ".PRODUCTATTRIBUTES." WHERE iProductid_FK ='".$rowProduct[0]->iProdid_PK."'" ; 
						$attributerow =$this->obDb->fetchQuery();
						$attcount = $this->obDb->record_count;
						if ($attcount > 0){
							$this->obDb->query="SELECT A.*, AV.tValues FROM ".ATTRIBUTES." A, ".ATTRIBUTEVALUES." AV WHERE AV.iValueId_PK=".$attributerow[0]->iValueid_FK." AND A.iAttributesid_PK = ".$attributerow[0]->iAttributeid_FK;
							$attribute = $this->obDb->fetchQuery();
							
							if ($attribute[0]->vAttributeTitle!="")
							{
								$this->ObTpl->set_var("TPL_VAR_ATTRIBUTETITLE",$attribute[0]->vAttributeTitle);
								
								$attdesc = explode("¬",$attribute[0]->tValues);
								$attfieldname= explode("¬",$attribute[0]->vFieldname);
								$prefix = explode("¬",$attribute[0]->vPrefix);
								$suffix = explode("¬",$attribute[0]->vSuffix);
								
								for ($i=0;$i<$attribute[0]->iFieldnumber;$i++)
								{
								$this->ObTpl->set_var("TPL_VAR_FILEDNAME",$attfieldname[$i]);
								$this->ObTpl->set_var("TPL_VAR_FIELDVALUE",$attdesc[$i]);		
								$this->ObTpl->set_var("TPL_VAR_PREFIX",$prefix[$i]);
								$this->ObTpl->set_var("TPL_VAR_SUFFIX",$suffix[$i]);
								$this->ObTpl->parse("attributefield_blk","TPL_ATTRIBUTEFIELD_BLK",true);
								}
							$this->ObTpl->parse("attributetable_blk","TPL_ATTRIBUTETABLE_BLK");
							}
						}
				#DISPALY PRICE FOR SELECTED PRODUCT				


				$this->ObTpl->parse("product_blk","TPL_PRODUCT_BLK",true);	
			}
			$this->ObTpl->parse("mainproduct_blk","TPL_MAINPRODUCT_BLK");	
		
		}else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NOPRODUCT_WISHLIST);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		return($this->ObTpl->parse("return","TPL_COMPARE_FILE"));
	}


}#END CLASS
?>