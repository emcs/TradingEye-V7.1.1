<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
include_once SITE_PATH . "LanguagePacks/" . LANGUAGE_PACK;
class c_shopInterface {
	#CONSTRUCTOR
	function c_shopInterface() {
		$this->templatePath = THEMEPATH . "ecom/templates/main/";
		$this->pageTplPath = THEMEPATH . "default/templates/main/";
		$this->largeImage = "largeImage.tpl.htm";
		$this->pageTplFile = "pager.tpl.htm";
		$this->pageSize = DEPT_PRODUCT_LIMIT;
		$this->subTotal = 0;
		$this->volDiscount = 0;
		$this->grandTotal = 0;
		$this->postagePrice = 0;
		$this->totalQty = 0;
		$this->checkout = 0;
		$this->taxTotal = 0;
		$this->postageTotal = 0;
		$this->postageQty = 0;
		$this->cartWeight = 0;
		$this->errMsg = '';
		$this->libFunc = new c_libFunctions();
		$this->noPaging = 0; #PAGING ACTIVE
	}

	#FUNCTION THAT RETURN BREDCRUMS
	function m_topNavigation($type) {
		global $Navigation, $topNavigation;
		$this->departmentLevel = 1;
		if ($type == "product") {
			if(isset($this->request['id']) && !empty($this->request['id']))
			{
			$this->obDb->query = "SELECT iProdid_PK as id FROM " . PRODUCTS . " WHERE iProdid_PK='" . $this->request['id'] . "'";
			}
			else
			{
			$this->obDb->query = "SELECT iProdid_PK as id FROM " . PRODUCTS . " WHERE vSeoTitle='" . $this->request['mode'] . "'";
			}
		}
		elseif ($type == "content") {
			$this->obDb->query = "SELECT iContentid_PK as id FROM " . CONTENTS . " WHERE vSeoTitle='" . $this->request['mode'] . "'";
		} else {
			$this->obDb->query = "SELECT iDeptid_PK as id FROM " . DEPARTMENTS . " WHERE vSeoTitle='" . $this->request['mode'] . "'";
		}

		$rowHead = $this->obDb->fetchQuery();
		$topNavigation = "";
		if(isset($_SESSION['own']) && !empty($_SESSION['own'])){
			$this->request['own'] = $_SESSION['own'];
			$_SESSION['own'] = "";
		}
		$this->m_getMainNavigation($rowHead[0]->id, $type, $this->request['mode'], 1,$this->request['own']);

		$topNavigation .= $Navigation;
		return $topNavigation;
	}

	#RECURSIVE FUNCTION TO GENERATE BREDCRUMBS
	function m_getMainNavigation($ownerid, $type, $title, $first, $own='') {
		global $Navigation;

		if ($ownerid != 0) {
			if ($type == "product") {
				if($own != ""){
					$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType,vSeoTitle,iState FROM " . PRODUCTS . " D ,	" . FUSIONS . " F WHERE iProdid_PK=iSubId_FK and iSubId_FK=" . $ownerid . " AND iOwner_FK='" . $own . "'  AND vtype='" . $type . "' AND iState=1";
				}else{
					$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType,vSeoTitle,iState FROM " . PRODUCTS . " D ,	" . FUSIONS . " F WHERE iProdid_PK=iSubId_FK and iSubId_FK=" . $ownerid . " AND vtype='" . $type . "' AND iState=1";
				}
			}
			elseif ($type == "content") {
				$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType,vSeoTitle,iState FROM " . CONTENTS . " D ,	" . FUSIONS . " F WHERE iContentid_PK=iSubId_FK and iSubId_FK=" . $ownerid . " AND vtype='" . $type . "' AND iState=1";
			} else {
				$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType,vSeoTitle,iState FROM " . DEPARTMENTS . " D ," . FUSIONS . " F WHERE iDeptid_PK=iSubId_FK and iSubId_FK=" . $ownerid . " AND vtype='" . $type . "' AND iState=1";
			}
			$row = $this->obDb->fetchQuery();
			if ($this->obDb->record_count != 0) {
				if ($type == "product") {
					$purl = SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $row[0]->vSeoTitle;
					if ($first == 1) {
						$Navigation = " &raquo; " . $this->libFunc->m_displayContent($row[0]->vTitle) . $Navigation;
					} else {
						$Navigation = " &raquo; " . "<a href=\"" . $this->libFunc->m_safeUrl($purl) . "\">" . $this->libFunc->m_displayContent($row[0]->vTitle) . "</a>" . $Navigation;
					}
				}
				elseif ($type == "content") {
					$curl = SITE_URL . "ecom/index.php?action=ecom.cdetails&mode=" . $row[0]->vSeoTitle;
					if ($first == 1) {
						$Navigation = " &raquo; " . $this->libFunc->m_displayContent($row[0]->vTitle) . $Navigation;
					} else {
						$Navigation = " &raquo; " . "<a href=\"" . $this->libFunc->m_safeUrl($curl) . "\">" . $this->libFunc->m_displayContent($row[0]->vTitle) . "</a>" . $Navigation;
					}
				} else {
					$durl = SITE_URL . "ecom/index.php?action=ecom.details&mode=" . $row[0]->vSeoTitle;
					if ($first == 1) {
						$Navigation = " &raquo; " . $this->libFunc->m_displayContent($row[0]->vTitle) . $Navigation;
					} else {
						$this->departmentLevel++;
						$Navigation = " &raquo; " . "<a href=\"" . $this->libFunc->m_safeUrl($durl) . "\">" . $this->libFunc->m_displayContent($row[0]->vTitle) . "</a>" . $Navigation;
					}
				}
				$this->m_getMainNavigation($row[0]->iOwner_FK, $row[0]->vOwnerType, $row[0]->vSeoTitle, 0);
			}
		} else {
			return $Navigation;
		}
	}

	function m_checkMemberPage()
	{
		$this->obDb->query = "SELECT iMember FROM ".DEPARTMENTS." d,".FUSIONS." f WHERE vSeoTitle = '".$this->request['mode']."' AND f.iState =1 AND f.iSubId_FK = d.iDeptid_PK AND f.vtype = 'department'";
		$member_department=$this->obDb->fetchQuery();
		
		if($member_department[0]->iMember == 1)
		{
			return true;
		}else
		{
			return false;
		}
		
	}

#FUNCTION TO DISPLAY DEPARTMENT DETAILS
	function m_showDeptDetails() {
		$parseSubDepartmentLinks = 1;
		$libFunc = new c_libFunctions();
		if ($this->libFunc->m_isNull($this->request['mode'])) {
			$this->request['mode'] = 0;
		}
		#TO DISPLAY THE HEAD NAME
		$this->obDb->query = "SELECT iDeptid_PK,vTitle,vTemplate,tContent,vSeoTitle  FROM " . DEPARTMENTS . "," . FUSIONS . " WHERE iDeptid_PK=iSubId_FK AND vtype='department' AND vOwnerType='department' AND  vSeoTitle='" . $this->request['mode'] . "' AND iState=1";
		$rowHead = $this->obDb->fetchQuery();
		if ($this->libFunc->m_isNull($rowHead[0]->vTemplate)) {
			$errrorUrl = SITE_URL . "index.php?action=error&mode=department";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}
	
		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;

		$this->ObTpl = new template();
		$this->ObTpl->set_file("TPL_DETAILS_FILE", $this->templatePath . "department/" . $rowHead[0]->vTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY", CONST_CURRENCY);
		#SETTING TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINDEPTDESC_BLK", "maindeptdesc_blk");
	
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINDEPT_BLK", "dspmaindept_blk");
		$this->ObTpl->set_block("TPL_MAINDEPT_BLK", "TPL_DEPT_BLK", "dspdept_blk");

		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINPRODUCT_BLK", "mainproduct_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK", "TPL_PRODUCT_BLK", "dspproduct_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK", "TPL_ATTRIBUTES_BLK", "attributes_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTES_BLK", "TPL_ATTRIBUTELINE_BLK", "attributeline_blk");
				
		$this->ObTpl->set_block("TPL_ATTRIBUTELINE_BLK", "TPL_FIELDVALUES_BLK", "fieldvalues_blk");
		
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK", "TPL_SORTOPTIONS_BLK", "sortoptions_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK", "TPL_SORTATTRIBUTETAB_BLK", "sortattributetab_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK", "TPL_SORTATTRIBUTE_BLK", "sortattribute_blk");
				
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_PDESC_BLK", "productdesc_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_RRPPRICE_BLK", "rrp_price_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINCONTENT_BLK", "dspmaincontent_blk");
		$this->ObTpl->set_block("TPL_MAINCONTENT_BLK", "TPL_CONTENT_BLK", "dspcontent_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_WISHLIST_BLK", "wishlist_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_COMPARE_BLK", "compare_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_REVIEWRANK_BLK", "reviewrank_blk");
		
		$this->ObTpl->set_block("TPL_SORTOPTIONS_BLK", "TPL_PRODPERPAGE_BLK", "properpage_blk");
		
		#INTIALIZING 
		$this->ObTpl->set_var("maindeptdesc_blk", "");
		$this->ObTpl->set_var("sortattribute_blk", "");
		$this->ObTpl->set_var("product_grid_blk", "");
		$this->ObTpl->set_var("sortattributetab_blk", "");
							
		$this->ObTpl->set_var("wishlist_blk","");
		$this->ObTpl->set_var("compare_blk","");
		$this->ObTpl->set_var("properpage_blk","");
		$this->ObTpl->set_var("reviewrank_blk","");
		
		
		$this->ObTpl->set_var("dspmaindept_blk", "");
		$this->ObTpl->set_var("dspdept_blk", "");
		$this->ObTpl->set_var("sortoptions_blk", "");
		$this->ObTpl->set_var("attributes_blk", "");
		$this->ObTpl->set_var("attributeline_blk", "");
		$this->ObTpl->set_var("fieldvalues_blk", "");
		
		$this->ObTpl->set_var("mainproduct_blk", "");
		$this->ObTpl->set_var("dspproduct_blk", "");
		$this->ObTpl->set_var("productdesc_blk", "");
		$this->ObTpl->set_var("rrp_price_blk", "");
		$this->ObTpl->set_var("dspmaincontent_blk", "");
		$this->ObTpl->set_var("dspcontent_blk", "");
		$this->ObTpl->set_var("TPL_VAR_ATTRIBUTETITLE", "");
		$this->ObTpl->set_var("TPL_VAR_PREFIX", "");
		$this->ObTpl->set_var("TPL_VAR_SUFFIX", "");
		
		$this->ObTpl->set_var("TPL_VAR_DESC", "");
		$this->ObTpl->set_var("TPL_VAR_RRP_AMOUNT", "");
		$this->ObTpl->set_var("TPL_VAR_IMGONLYSELECTED", "");
		
		$this->ObTpl->set_var("TPL_VAR_ASC2","");
		$this->ObTpl->set_var("TPL_VAR_DESC2","");
		$this->ObTpl->set_var("TPL_VAR_ASC1","");
		$this->ObTpl->set_var("TPL_VAR_DESC1","");
		
		$this->ObTpl->set_var("TPL_VAR_ATTBDEPTURL", SITE_URL."ecom/index.php?action=ecom.details&amp;mode=".$rowHead[0]->vSeoTitle."&amp;sort=attribute");
		$this->ObTpl->set_var("TPL_VAR_SORTOPTIONURL", SITE_URL."ecom/index.php?action=ecom.details&amp;mode=".$rowHead[0]->vSeoTitle."&amp;sort=option");
		
		$this->ObTpl->set_var("TPL_VAR_DEPTURL", SITE_URL."ecom/index.php?action=ecom.details&amp;mode=".$rowHead[0]->vSeoTitle);
		
		#SHOW PRODUCT ATTRIBUTE TABLE	
		if (isset($this->request['sort']) && $this->request['sort']=="attribute")
		{
		   # SELECT ATTRIBUTES EXISTING IN THE DEPARTMENT 
			$this->obDb->query ="SELECT DISTINCT iAttributeid_FK,A.*";
			$this->obDb->query.=" FROM " . PRODUCTS . " D, " . FUSIONS . " F,".PRODUCTATTRIBUTES." PA,".ATTRIBUTES." A "; 
			$this->obDb->query.=" WHERE iProdid_PK=iSubId_FK AND vtype='product' AND ";
			$this->obDb->query.=" iOwner_FK='".$rowHead[0]->iDeptid_PK."' AND vOwnerType='department' AND iState=1 ";
			$this->obDb->query.=" AND PA.iProductid_FK = D.iProdid_PK AND PA.iAttributeid_FK = A.iAttributesid_PK"; 
			$this->obDb->query.=" GROUP BY (iAttributeid_FK) ORDER BY iSort";
			
		
			$attRow = $this->obDb->fetchQuery(); 
			$attcount = $this->obDb->record_count; //attribute count
			
			for($i=0;$i<$attcount;$i++)
			{  
				$this->ObTpl->set_var("TPL_VAR_ATTRIBUTETITLE",$attRow[$i]->vAttributeTitle);  
				$fieldname = explode("<!>",$attRow[$i]->vFieldname);
				$this->ObTpl->set_var("attributeline_blk","");
				for($j=0;$j<$attRow[$i]->iFieldnumber;$j++)
				{
					#SELECT VALUES IN EACH FIELD
					$this->obDb->query="SELECT AV.* FROM ".PRODUCTS." D,".FUSIONS." F,".PRODUCTATTRIBUTES." PA,".ATTRIBUTEVALUES." AV"; 
					$this->obDb->query.=" WHERE iProdid_PK=iSubId_FK AND vtype='product' AND PA.iValueid_FK = iValueId_PK ";
					$this->obDb->query.=" AND iOwner_FK='".$rowHead[0]->iDeptid_PK."' AND D.iProdid_PK = PA.iProductid_FK AND vOwnerType='department'";
					$this->obDb->query.=" AND iState=1 AND PA.iAttributeid_FK ='".$attRow[$i]->iAttributeid_FK."'";
					 	
					$valueRow = $this->obDb->fetchQuery();
					$valueCount = $this->obDb->record_count;
					$valueArray[$j] = array();
					$prefixArray[$j]=array();
					$suffixArray[$j]=array();
					for($k=0;$k<$valueCount;$k++)
					{
					$value = explode("<!>",$valueRow[$k]->tValues);
					array_push($valueArray[$j],$value[$j]);
					$prefix = explode("<!>",$attRow[$i]->vPrefix);
					array_push($prefixArray[$j],$prefix[$j]);
					$suffix = explode("<!>",$attRow[$i]->vSuffix);
					array_push($suffixArray[$j],$suffix[$j]);
					}
	 				
					$this->ObTpl->set_var("fieldvalues_blk","");
					$myArr=array();
					for($t=0;$t<count($valueArray[$j]);$t++){
						if(!in_array($valueArray[$j][$t],$myArr)){
							$myArr[]=$valueArray[$j][$t];
							$this->ObTpl->set_var("TPL_VAR_ATTRIBUTEVALUE",$valueArray[$j][$t]);		
							$this->ObTpl->set_var("TPL_VAR_PREFIX",$prefixArray[$j][$t]);
							$this->ObTpl->set_var("TPL_VAR_SUFFIX",$suffixArray[$j][$t]);
													
							$this->obDb->query = "SELECT COUNT(iValueId_PK) as prodnumber FROM ".ATTRIBUTEVALUES." WHERE tValues LIKE '%?".$valueArray[$j][$t]."?%'"; 
							$this->obDb->query.=" AND iAttributesid_FK=".$attRow[$i]->iAttributeid_FK;
							$valuecount =$this->obDb->fetchQuery();
							$this->ObTpl->set_var("TPL_VAR_PRODQUANTITY",$valuecount[0]->prodnumber);
							$this->ObTpl->set_var("TPL_VAR_SEARCHLINK",SITE_URL."ecom/index.php?action=ecom.details&mode=".$rowHead[0]->vSeoTitle."&sort=attribute&attkey=".$valueArray[$j][$t]);						
							$this->ObTpl->parse("fieldvalues_blk","TPL_FIELDVALUES_BLK",true);
						}
					}
					$this->ObTpl->set_var("TPL_VAR_ATTRIBUTEFIELD",$fieldname[$j]); 	
					$this->ObTpl->parse("attributeline_blk","TPL_ATTRIBUTELINE_BLK",true);
				}
			$this->ObTpl->parse("attributes_blk","TPL_ATTRIBUTES_BLK",true);			
			}
		}
		#END SHOWING ATTRIBUTE TALBE
		
		for($i=0;$i<7;$i++)  // 11 because the max product number is 22
		{
					
			$this->ObTpl->set_var("TPL_VAR_OPTVALUE",($i+1)*10);
				
				if (isset($this->request['prodperpage']) && $this->request['prodperpage']!=0){
				$_SESSION['prodperpage']=$this->request['prodperpage'];
				}
				
				if($_SESSION['prodperpage'] == ($i+1)*10){
				$this->ObTpl->set_var("SELECT","selected=\"selected\"");
				}else {
				$this->ObTpl->set_var("SELECT","");	
				}
			$this->ObTpl->parse("properpage_blk","TPL_PRODPERPAGE_BLK",true);
		}
		
		
				
		#MAIN DEPARTMENT DESC AND TITLE
		$this->ObTpl->set_var("TPL_VAR_DEPARTMENTTITLE", $this->libFunc->m_displayContent($rowHead[0]->vTitle));
		if (!$this->libFunc->m_isNull($rowHead[0]->tContent)) {
			$this->ObTpl->set_var("TPL_VAR_DEPARTMENTDESC", $this->libFunc->m_displayContent1($rowHead[0]->tContent));
			$this->ObTpl->parse("maindeptdesc_blk", "TPL_MAINDEPTDESC_BLK");
		}

		#QUERY TO GET DEPARTMENTS UNDER SELECTED DEPARTMENT
		$this->obDb->query = "SELECT vTitle,vImage2,iDeptid_PK,vSeoTitle FROM " . DEPARTMENTS . " D, " . FUSIONS . " F WHERE iDeptid_PK=iSubId_FK AND vtype='department' AND iOwner_FK=" . $rowHead[0]->iDeptid_PK . " AND vOwnerType='department' and iState=1 ORDER BY iSort";
		$rowDept = $this->obDb->fetchQuery();
		$deptCount = $this->obDb->record_count;
		if (TREE_MENU == 1) {
			if ($this->departmentLevel < 3) {
				$parseSubDepartmentLinks = 0;
			}
		}

		if ($deptCount > 0 && $parseSubDepartmentLinks) {
			for ($i = 0; $i < $deptCount; $i++) {
				$deptUrl = SITE_URL . "ecom/index.php?action=ecom.details&mode=" . $rowDept[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_DEPTURL", $this->libFunc->m_safeUrl($deptUrl));
				if (!$this->libFunc->m_isNull($rowDept[$i]->vImage2)) {
					$img = $this->libFunc->m_checkFile($rowDept[$i]->vImage2, "department", $this->libFunc->m_displayContent($rowDept[$i]->vTitle));
					if ($img) {
						$this->ObTpl->set_var("TPL_VAR_TITLE", $img);

					} else {
						$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowDept[$i]->vTitle));
					}
				} else {
					$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowDept[$i]->vTitle));
				}
				$this->ObTpl->parse("dspdept_blk", "TPL_DEPT_BLK", true);
			}
			$this->ObTpl->parse("dspmaindept_blk", "TPL_MAINDEPT_BLK");
		}

		#FUNCTION TO DISPLAY ATTACHED PRODUCT LIST	
		$this->m_attachedProduct($rowHead[0]->iDeptid_PK, "department");
		
		#FUNCTION TO DISPLAY ATTACHED CONTENT LIST
		$this->m_dspAttachedContent($rowHead[0]->iDeptid_PK, "department");

		return ($this->ObTpl->parse("return", "TPL_DETAILS_FILE"));
	} #END DEPARTMENT DISPLAY

	#FUNCTION TO DISPLAY CONTENT DETAILS
	function m_showContentDetails() {
		$libFunc = new c_libFunctions();

		if (!isset ($this->request['mode']) || $this->libFunc->m_isNull($this->request['mode'])) {
			$this->request['mode'] = 0;
		}
		$this->obDb->query = "SELECT iContentid_PK,vTitle,tContent,tmEditDate,tmBuildDate,";
		$this->obDb->query .= "vTemplate,tShortDescription  FROM " . CONTENTS . "," . FUSIONS;
		$this->obDb->query .= " WHERE iContentid_PK=iSubId_FK AND vtype='content' AND ";
		$this->obDb->query .= "iState=1 AND vSeoTitle='" . $this->request['mode'] . "'";
		$rowContent = $this->obDb->fetchQuery();
		if ($this->libFunc->m_isNull($rowContent[0]->vTemplate)) {
			$errrorUrl = SITE_URL . "index.php?action=error&mode=content";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}

		$this->ObTpl = new template();
		$this->ObTpl->set_file("TPL_DETAILS_FILE", $this->templatePath . "content/" . $rowContent[0]->vTemplate);

		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINCONTENT_BLK", "dspmaincontent_blk");
		$this->ObTpl->set_block("TPL_MAINCONTENT_BLK", "TPL_CONTENT_BLK", "dspcontent_blk");

		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINPRODUCT_BLK", "mainproduct_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK", "TPL_PRODUCT_BLK", "dspproduct_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_PDESC_BLK", "productdesc_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_RRPPRICE_BLK", "rrp_price_blk");
		$this->ObTpl->set_var("mainproduct_blk", "");
		$this->ObTpl->set_var("dspproduct_blk", "");
		$this->ObTpl->set_var("productdesc_blk", "");
		$this->ObTpl->set_var("rrp_price_blk", "");
		$this->ObTpl->set_var("dspmaincontent_blk", "");
		$this->ObTpl->set_var("dspcontent_blk", "");
		$this->ObTpl->set_var("TPL_VAR_PRODUCTNAME", $this->libFunc->m_displayContent($rowContent[0]->vTitle));
		if ($this->libFunc->m_isNull($rowContent[0]->tmEditDate)) {
			$this->ObTpl->set_var("TPL_VAR_DATE", $this->libFunc->dateFormat1($rowContent[0]->tmBuildDate));
		} else {
			$this->ObTpl->set_var("TPL_VAR_DATE", $this->libFunc->dateFormat1($rowContent[0]->tmEditDate));
		}
		if ($this->libFunc->m_isNull($rowContent[0]->tContent)) {
			$this->ObTpl->set_var("TPL_VAR_LONGDESC", $this->libFunc->m_displayContent($rowContent[0]->tShortDescription));
		} else {
			$this->ObTpl->set_var("TPL_VAR_LONGDESC", $this->libFunc->m_displayContent1($rowContent[0]->tContent));
		}

		$this->m_attachedProduct($rowContent[0]->iContentid_PK, "content");
		$this->m_dspAttachedContent($rowContent[0]->iContentid_PK, "content");

		return ($this->ObTpl->parse("return", "TPL_DETAILS_FILE"));
	} #END CONTENT DISPLAY

	/***********************************************************************/
	/*	FUNCTION TO HANDLE PRODUCT DISPLAY ACCORDING TO TEMPLATES								*/
	/***********************************************************************/

	#FUNCTION TO DISPLAY PRODUCT DETAILS
	#KIT ELEMENT ARE PRODUCT IN KIT
	function m_showProductDetails() {
		
		$this->libFunc->obDb = $this->obDb;
		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;

		if (!isset ($this->request['mode']) || $this->libFunc->m_isNull($this->request['mode'])) {
			$this->request['mode'] = 0;
		}
		if(isset($this->request['id']) && !empty($this->request['id']))
		{
		$this->obDb->query = 'SELECT * FROM ' . PRODUCTS . ' WHERE iProdid_PK="'.$this->request['id'].'"';
		}
		else
		{		
		#TO DISPLAY THE HEAD NAME
		$this->obDb->query = "SELECT * FROM " . PRODUCTS . ", " . FUSIONS . " F WHERE iProdid_PK =iSubId_FK AND vtype='product' AND iState=1 AND vSeoTitle='" . $this->request['mode'] . "'";
		}
		$rowHead = $this->obDb->fetchQuery();

		#MARGIN CALCULATOR
		switch (MARGINSTATUS)
		{
			case "increase":
				$rowHead[0]->fPrice= ($rowHead[0]->fPrice * MARGINPERCENT/100 ) + $rowHead[0]->fPrice;
			break;
			case "decrease":
				$rowHead[0]->fPrice=  $rowHead[0]->fPrice - ($rowHead[0]->fPrice * MARGINPERCENT/100 );
			break;
			default:
				$rowHead[0]->fPrice = $rowHead[0]->fPrice;
			break;	
		}
		#END MARGIN CALCULATOR
        
        //--- Switch to retail price if Retail customer
				if ($comFunc->m_checkCustomerType()==1 && ENABLE_WHOLESALE==1 && $rowHead[0]->fRetailPrice>0){
				$rowHead[0]->fPrice=$rowHead[0]->fRetailPrice;
				}
		//----End switch price	
		if ($this->libFunc->m_isNull($rowHead[0]->vTemplate)) {
			$errrorUrl = SITE_URL . "index.php?action=error&mode=product";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
			exit;
		}
		
			//SETTING COOKIES FOR RECENTLY VIEWED PRODUCTS
		if(RECENTVIEWED ==1)
		{
			$productSEO = stripslashes($rowHead[0]->vSeoTitle);
			//check to see if cookie already set
			if(!isset($_COOKIE['jimbeam']))
			{
				setcookie('jimbeam[0]',$productSEO,0,"/");
			}else {
				$i = count($_COOKIE['jimbeam']);
				if(!in_array($productSEO,$_COOKIE['jimbeam']))
				{
				   setcookie('jimbeam['.$i.']',$productSEO,0,"/");				
				}
				
			}
		}
		$this->ObTpl = new template();
		$this->ObTpl->set_file("TPL_DETAILS_FILE", $this->templatePath . "product/" . $rowHead[0]->vTemplate);
		$this->ObTpl->set_var("TPL_VAR_PRODUCTTITLE", $this->libFunc->m_displayContent($rowHead[0]->vTitle));
		
		//UPDATE PRODUCT VIEW COUNT.
		$ViewCount = $rowHead[0]->iViewCount + 1;
		$this->obDb->query = "UPDATE ".PRODUCTS." SET  iViewCount='".$ViewCount."' WHERE iProdId_PK='".$rowHead[0]->iProdid_PK."'" ;
		$this->obDb->updateQuery();
		
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY", CONST_CURRENCY);
		#SETTING TEMPLATE BLOCKS

		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINPRODUCT_BLK", "mainproduct_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_IMAGE_GALLERY_BLK", "image_gallery_blk");
		$this->ObTpl->set_block("TPL_IMAGE_GALLERY_BLK", "TPL_IMAGE_LIST_BLK", "image_list_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE","TPL_HIDDEN_BLK","hidden_blk");
		
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK", "TPL_PRODUCT_BLK", "dspproduct_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_PDESC_BLK", "productdesc_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK", "TPL_RRPPRICE_BLK", "rrp_price_blk");
		
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_REVIEWCONTAINER_BLK", "reviewcontainer_blk");
		
		$this->ObTpl->set_block("TPL_REVIEWCONTAINER_BLK", "TPL_REVIEWLINK_BLK", "reviewlink_blk");
		$this->ObTpl->set_block("TPL_REVIEWCONTAINER_BLK", "TPL_REVIEW_BLK", "dspreview_blk");
		$this->ObTpl->set_block("TPL_REVIEW_BLK", "TPL_LINK_BLK", "link_blk");
		$this->ObTpl->set_block("TPL_REVIEWCONTAINER_BLK", "TPL_REVIEWFORM_BLK", "reviewform_blk");
				
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_BASKET_BLK", "basket_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_WISHLIST_BLK", "wishlist_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_COMPARE_BLK", "compare_blk");
		
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_ENQUIRY_BLK", "enquiry_blk");
	
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_SUPPLIERIMG_BLK", "supplierimg_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_ONSALELBL_BLK", "onsalelbl_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_RRPLBL_BLK", "rrplbl_blk");
		
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_REVIEWRANK_BLK", "reviewrank_blk");

		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_QTY_BLK", "qty_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_PDETAILS_BLK", "pdetail_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_MAINCONTENT_BLK", "dspmaincontent_blk");
		$this->ObTpl->set_block("TPL_MAINCONTENT_BLK", "TPL_CONTENT_BLK", "dspcontent_blk");
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_ATTRIBUTETABLE_BLK", "attributetable_blk");
		
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_WIDTH_BLK", "itemwidth_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_HEIGHT_BLK", "itemheight_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_DEPTH_BLK", "itemdepth_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_WEIGHT_BLK", "itemweight_blk");
		
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_ASIN_BLK", "itemasin_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_ISBN_BLK", "itemisbn_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_MPN_BLK", "itemmpn_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_UPC_BLK", "imtemupc_blk");
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ITEM_SKU_BLK", "imtemsku_blk");
		
		$this->ObTpl->set_block("TPL_ATTRIBUTETABLE_BLK", "TPL_ATTRIBUTEFIELD_BLK", "attributefield_blk");
		
		$this->ObTpl->halt_on_error = "no";
		#TO CHECK KIT BLOCK
		$this->kitBlk = $this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_KIT_BLK", "kit_blk");
		if ($this->kitBlk) {
			$this->ObTpl->set_block("TPL_KIT_BLK", "TPL_KITELEMENT_BLK", "kitElement_blk");
		}

		#INTIALIZING 
		$this->ObTpl->set_var("mainproduct_blk", "");
		$this->ObTpl->set_var("image_gallery_blk", "");
		$this->ObTpl->set_var("image_list_blk", "");
		$this->ObTpl->set_var("dspproduct_blk", "");
		$this->ObTpl->set_var("productdesc_blk", "");
		$this->ObTpl->set_var("hidden_blk", "");
		
		$this->ObTpl->set_var("rrp_price_blk", "");
		$this->ObTpl->set_var("dspmaincontent_blk", "");
		$this->ObTpl->set_var("dspcontent_blk", "");
		$this->ObTpl->set_var("reviewlink_blk", "");
		$this->ObTpl->set_var("reviewform_blk", "");
		$this->ObTpl->set_var("reviewcontainer_blk", "");
		$this->ObTpl->set_var("reviewrank_blk", "");
		
		
		$this->ObTpl->set_var("dspreview_blk", "");
		$this->ObTpl->set_var("link_blk", "");
		$this->ObTpl->set_var("basket_blk", "");
		$this->ObTpl->set_var("wishlist_blk","");
		$this->ObTpl->set_var("compare_blk","");
	
		$this->ObTpl->set_var("enquiry_blk", "");
		$this->ObTpl->set_var("attributetable_blk", "");
		
		$this->ObTpl->set_var("itemwidth_blk", "");
		$this->ObTpl->set_var("itemheight_blk", "");
		$this->ObTpl->set_var("itemdepth_blk", "");
		$this->ObTpl->set_var("itemweight_blk", "");
		
		$this->ObTpl->set_var("itemasin_blk", "");
		$this->ObTpl->set_var("itemisbn_blk", "");
		$this->ObTpl->set_var("itemmpn_blk", "");
		$this->ObTpl->set_var("imtemupc_blk", "");
		$this->ObTpl->set_var("imtemsku_blk", "");
		
		
		$this->ObTpl->set_var("attributefield_blk", "");

		$this->ObTpl->set_var("supplierimg_blk", "");
		$this->ObTpl->set_var("onsalelbl_blk", "");
		$this->ObTpl->set_var("rrplbl_blk", "");

		$this->ObTpl->set_var("qty_blk", "");
		$this->ObTpl->set_var("pdetail_blk", "");

		$this->ObTpl->set_var("kit_blk", "");
		$this->ObTpl->set_var("kitElement_blk", "");

		$this->ObTpl->set_var("TPL_VAR_RRP", "");

		$this->ObTpl->set_var("TPL_VAR_MAINOPTIONS", "");
		$this->ObTpl->set_var("TPL_VAR_MAINCHOICES", "");

		$this->ObTpl->set_var("TPL_VAR_SUPPLIERIMAGE", "");
		$this->ObTpl->set_var("TPL_VAR_ONSALE", "");
		$this->ObTpl->set_var("TPL_VAR_SHIPNOTES", "");
		$this->ObTpl->set_var("TPL_VAR_FREEPOSTAGE", "");
		$this->ObTpl->set_var("TPL_VAR_STATUS", "");

		#defining language pack variables.
		$this->ObTpl->set_var("LANG_VAR_ADDTOBASKET", LANG_ADDTO_BASKET);
		$this->ObTpl->set_var("LANG_VAR_ENQUIRENOW", LANG_ENQUIRE_NOW);
		$this->ObTpl->set_var("LANG_VAR_PRICE", LANG_PRICE);
		$this->ObTpl->set_var("LANG_VAR_QTY", LANG_QTY);
		$this->ObTpl->set_var("LANG_VAR_OPTIONS", LANG_OPTIONS);
		$this->ObTpl->set_var("LANG_VAR_LATESTNEWS", LATEST_NEWS);
		$this->ObTpl->set_var("LANG_VAR_CUSTOMERREVIEWS", LANG_CUSTOMER_REVIEWS);
		$this->ObTpl->set_var("LANG_VAR_ADDWISHLIST", LANG_WISH_LISTADD);
		$this->ObTpl->set_var("LANG_VAR_PRODUCTDETAILS", LANG_PRODUCTDETAILS);
		$this->ObTpl->set_var("LANG_VAR_MAYWESUGGEST", LANG_MAYWESUGGEST);

		#DISPLAY IMAGE FOR SELECTED PRODUCT
        if(!$this->libFunc->m_isNull($rowHead[0]->vImage2))
        {
            $img=$this->libFunc->m_checkFile($rowHead[0]->vImage2,"product",$this->libFunc->m_displayContent($rowHead[0]->vTitle),1);
            if($img)
            {
                $this->ObTpl->set_var("TPL_VAR_IMAGE",$img);
            }
            else
            {
                $this->ObTpl->set_var("TPL_VAR_IMAGE",MSG_NOIMG);
            }
        }
        else
        {
            $this->ObTpl->set_var("TPL_VAR_IMAGE",MSG_NOIMG);
        }
        
        if(!$this->libFunc->m_isNull($rowHead[0]->vImage3))
        {
            if($this->libFunc->m_checkFileExist($rowHead[0]->vImage3,"product")){
                $this->ObTpl->set_var("TPL_VAR_IMGPATH","{TPL_VAR_REAL_PATH}images/product/".$rowHead[0]->vImage3);  
                $this->ObTpl->set_var("TPL_VAR_VIEWLARGEIMAGE","View Large Image");
            }else{
                $this->ObTpl->set_var("TPL_VAR_VIEWLARGEIMAGE","");
        }
        }else{
            $this->ObTpl->set_var("TPL_VAR_VIEWLARGEIMAGE","");
        }
      
				

#IMAGE GALLERY
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$imagearray = explode(",",$rowHead[0]->tImages);
	    $imagecount = count($imagearray);
	
	if($imagecount>0  )
		{ 		
			 for ($i=0;$i<$imagecount;$i++)
			{				
							if ($imagearray[$i]!=""){
							$content[$i]=SITE_URL."libs/timthumb.php?src=/images/product/".$imagearray[$i]."&amp;h=".GALLERY_LARGEHEIGHT."&amp;w=".GALLERY_LARGEWIDTH."&amp;zc=1 alt='".$imagearray[$i]."'";
							$hidden[$i]="<input type=\"hidden\" id=\"hidden".$i."\" value=\"{TPL_VAR_CONTENT".$i."}\"><br />";
							$this->ObTpl->set_var("TPL_VAR_HIDDEN",$hidden[$i]);
							$this->ObTpl->set_var("TPL_VAR_CONTENT".$i,$content[$i]);
							$this->ObTpl->parse("hidden_blk","TPL_HIDDEN_BLK",true);
							}
			}
			for($i=0;$i<$imagecount;$i++)
			{
				if ($imagearray[$i]!=""){	
					if ($this->libFunc->m_checkFileExist($imagearray[$i],"product"))
							{	
							$this->ObTpl->set_var("TPL_VAR_IMAGE_URL","<img src='".SITE_URL."libs/timthumb.php?src=/images/product/".$imagearray[$i]."&amp;h=".GALLERY_THUMBNAILHEIGHT."&amp;w=".GALLERY_THUMBNAILWIDTH."&amp;zc=1'  alt='".$imagearray[$i]."' />");			
							$this->ObTpl->set_var("imagenumber","imagenumber".$i);
							$this->ObTpl->set_var("TPL_VAR_IMAGENUMBER",$i);
							
							$this->ObTpl->set_var("TPL_VAR_THUMBNAILIMAGE","/images/product/".$imagearray[$i]);
							
							$this->ObTpl->parse("image_list_blk","TPL_IMAGE_LIST_BLK",true);	
							}
				}
			}
			$this->ObTpl->parse("image_gallery_blk","TPL_IMAGE_GALLERY_BLK");		 
		}	
	
	if ($rowHead[0]->iSale == 1) {
			$this->ObTpl->set_var("TPL_VAR_ONSALE", "<p class=\"onSale\">On Sale</p>");
			$this->ObTpl->parse("onsalelbl_blk", "TPL_ONSALELBL_BLK");
		}
		if (!$this->libFunc->m_isNull($rowHead[0]->vShipNotes)) {
			$this->ObTpl->set_var("TPL_VAR_SHIPNOTES", "<p>" . $this->libFunc->m_displayContent($rowHead[0]->vShipNotes) . "</p>");
		}
		
		#CHECK TO DISPLAY WISHLIST - MANAGED BY ADMIN
		if (USEWISHLIST == 1) {
			##WISHLIST URL
			$wishListUrl = SITE_URL . "ecom/index.php?action=wishlist.add&mode=" . $rowHead[0]->iProdid_PK;
			$this->ObTpl->set_var("TPL_VAR_WISHLISTLINK", $this->libFunc->m_safeUrl($wishListUrl));
			$this->ObTpl->parse("wishlist_blk", "TPL_WISHLIST_BLK");
		}
		
		#CHECK TO DISPLAY COMPARELIST - MANAGED BY ADMIN
		if (USECOMPARE == 1) {
			$compareListUrl = SITE_URL . "ecom/index.php?action=compare.add&mode=" . $rowHead[0]->iProdid_PK;
			$this->ObTpl->set_var("TPL_VAR_COMPARELINK", $this->libFunc->m_safeUrl($compareListUrl));
			$this->ObTpl->parse("compare_blk", "TPL_COMPARE_BLK");
		}
		
		##OVERALL PRODUCT STAR RANKING	
		$this->obDb->query = "SELECT SUM(vRank) as total, COUNT(iItemid_FK) as reviewcount FROM ".REVIEWS." WHERE iItemid_FK ='".$rowHead[0]->iProdid_PK."'";
		$OverallReviewRating = $this->obDb->fetchQuery();
		$ReviewRating = $OverallReviewRating[0]->total / $OverallReviewRating[0]->reviewcount;
		$ReviewRating = number_format($ReviewRating , 0, '.', '');
					
					
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
		#TO DISPLAY QUANTITY
		if (STOCK_CHECK==1 && $rowHead[0]->iUseinventory == 1) {
			$this->ObTpl->set_var("TPL_VAR_QTY", $this->libFunc->m_displayContent($rowHead[0]->iInventory));
			$this->ObTpl->parse("qty_blk", "TPL_QTY_BLK");
		}

		#TO CHECK FREE POSTAGE
		if ($rowHead[0]->iFreeShip == 1) {
			$this->ObTpl->set_var("TPL_VAR_FREEPOSTAGE", "<p>" . LBL_FREEPP . "</p>");
		}

		#TO DISPLAY SUPPLIER LOGO
		if ($rowHead[0]->iVendorid_FK != 0) {
			$this->obDb->query = "SELECT vImage,vCompany FROM " . SUPPLIERS . " WHERE iVendorid_PK='" . $rowHead[0]->iVendorid_FK . "'";
			$rowImage = $this->obDb->fetchQuery();
			#DISPLAY IMAGE/NAME
			$img = $this->libFunc->m_checkFile($rowImage[0]->vImage, "suppliers", "Supplier " . $this->libFunc->m_displayContent($rowImage[0]->vCompany));
			$this->ObTpl->set_var("TPL_VAR_SUPPLIERIMAGE", $img);
			$this->ObTpl->set_var("TPL_VAR_SUPPLIERNAME", $this->libFunc->m_displayContent($rowImage[0]->vCompany));
			$this->ObTpl->parse("supplierimg_blk", "TPL_SUPPLIERIMG_BLK");
		}

		#*******************DISPLAY MAIN PRODUCT*****************************
		##CHECK FOR RRP PRICE

		if (!$this->libFunc->m_isNull($rowHead[0]->fListPrice) && $rowHead[0]->fListPrice > 0) {
			$this->ObTpl->set_var("TPL_VAR_RRP", RRP_TEXT . " <strike>" . CONST_CURRENCY . number_format($rowHead[0]->fListPrice, 2, '.', '') . "</strike>");
			$this->ObTpl->parse("rrplbl_blk", "TPL_RRPLBL_BLK");
		}
		##CHECK FOR BASKET BUTTON
		if ($rowHead[0]->iCartButton == 1) {
			$cartUrl = SITE_URL . "ecom/index.php?action=ecom.addtocart";
			$this->ObTpl->set_var("TPL_VAR_CARTLINK", $this->libFunc->m_safeUrl($cartUrl));
			$this->ObTpl->parse("basket_blk", "TPL_BASKET_BLK");
		} 
		if($rowHead[0]->iEnquiryButton == 1) {
			##ENQUIRY URL
			$enquiryUrl = SITE_URL . "ecom/index.php?action=enquiry.dspForm&mode=" . $rowHead[0]->vSeoTitle;
			$this->ObTpl->set_var("TPL_VAR_ENQUIRYLINK", $this->libFunc->m_safeUrl($enquiryUrl));
			$this->ObTpl->parse("enquiry_blk", "TPL_ENQUIRY_BLK");
		}

		#*****DETAILS FOR SELECT PRODUCT / KIT
		$this->ObTpl->set_var("TPL_VAR_MAINID", $rowHead[0]->iProdid_PK);
		$this->ObTpl->set_var("TPL_VAR_SEOTITLE", $this->libFunc->m_displayContent($rowHead[0]->vSeoTitle));
		$this->ObTpl->set_var("TPL_VAR_QTY", $this->libFunc->m_displayContent($rowHead[0]->iInventory));

		#TO DISPLAY DESCRIPTION
		if (!$this->libFunc->m_isNull($rowHead[0]->tContent)) {
			$this->ObTpl->set_var("TPL_VAR_LONGDESCMAIN", $this->libFunc->m_displayContent1($rowHead[0]->tContent));
			$this->ObTpl->parse("pdetail_blk", "TPL_PDETAILS_BLK");
		}
		if (!$this->libFunc->m_isNull($rowHead[0]->tShortDescription)) {
			$this->ObTpl->set_var("TPL_VAR_SHORTDESCMAIN", $this->libFunc->m_displayContent($rowHead[0]->tShortDescription));
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SHORTDESCMAIN","");
		}

		#PRODUCT ID FOR COMMON FUNCTION CLASS
		$comFunc->productId = $rowHead[0]->iProdid_PK;

		#CHECKING WHEATHER SELECTED PRODUCT IS KIT OR NOT
		#IF YES THEN NO CHOICES AND OPTIONS TO DISPLAY ARE ALL THE PRODUCTS IN KIT
		#IF NO ALL CHOICES AND OPTIONS FOR SELECTED PRODUCT TO DISPALY

		if ($rowHead[0]->iKit == 1 && $this->kitBlk == 1) {
			$this->obDb->query = "SELECT iProdId_FK,iQty,vTitle,vSeoTitle FROM " . PRODUCTKITS . "," . PRODUCTS . " WHERE iProdId_FK=iProdId_PK AND iKitId ='" . $rowHead[0]->iProdid_PK . "'";
			$rsKit = $this->obDb->fetchQuery();
			$rsKitCount = $this->obDb->record_count;
			if ($rsKitCount > 0) {
				for ($i = 0; $i < $rsKitCount; $i++) {
					$kitElementUrl = SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $rsKit[$i]->vSeoTitle;
					$comFunc->productId = $rsKit[$i]->iProdId_FK;

					#GET OPTIONS**************************************************
					$this->ObTpl->set_var("TPL_VAR_OPTIONS", $comFunc->m_getOptions('1'));
					$this->ObTpl->set_var("TPL_VAR_KITELEMENT_URL", $this->libFunc->m_safeUrl($kitElementUrl));

					$this->ObTpl->set_var("TPL_VAR_KITELEMENT", $this->libFunc->m_displayContent($rsKit[$i]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_QTY", $rsKit[$i]->iQty);
					$this->ObTpl->parse("kitElement_blk", "TPL_KITELEMENT_BLK", true);
				} #END FOR I LOOP
				$this->ObTpl->parse("kit_blk", "TPL_KIT_BLK");
			} #END IF
		} else {
			#GET OPTIONS
			$this->ObTpl->set_var("TPL_VAR_MAINOPTIONS", $comFunc->m_getOptions('0'));
			#GET CHOICES
			$this->ObTpl->set_var("TPL_VAR_MAINCHOICES", $comFunc->m_getChoices());
		}

		#GET DISCOUNTS
		$this->ObTpl->set_var("TPL_VAR_VOLDISCOUNTS", $comFunc->m_getVolDiscount());

		#DISPLAY PRODUCT ATTRIBUTE 
		$this->obDb->query="SELECT * FROM ".PRODUCTATTRIBUTES." WHERE iProductid_FK ='".$rowHead[0]->iProdid_PK."'" ; 
		$attributerow =$this->obDb->fetchQuery();
		$attcount = $this->obDb->record_count;
		if ($attcount > 0){
			$this->obDb->query="SELECT A.*,AV.* FROM ".ATTRIBUTES." A INNER JOIN ".ATTRIBUTEVALUES." as AV ON AV.iAttributesid_FK = A.iAttributesid_PK WHERE A.iAttributesid_PK = ".$attributerow[0]->iAttributeid_FK;
			$attribute = $this->obDb->fetchQuery();
			if ($attribute[0]->vAttributeTitle!="")
			{
				$this->ObTpl->set_var("TPL_VAR_ATTRIBUTETITLE",$attribute[0]->vAttributeTitle);
				
				//$attdesc = explode("<!>",$attribute[0]->tValues);
				$attfieldname= explode("<!>",$attribute[0]->vFieldname);
				$prefix = explode("<!>",$this->libFunc->m_displayContent2($attribute[0]->vPrefix));
				$suffix = explode("<!>",$attribute[0]->vSuffix);
								
				for ($i=0;$i<$attribute[0]->iFieldnumber;$i++)
				{
				$this->ObTpl->set_var("TPL_VAR_FILEDNAME",$attfieldname[$i]);
				$this->ObTpl->set_var("TPL_VAR_FIELDVALUE",$attribute[$i]->tValues);		
				$this->ObTpl->set_var("TPL_VAR_PREFIX",$this->libFunc->m_displayContent2($prefix[$i]));
				$this->ObTpl->set_var("TPL_VAR_SUFFIX",$this->libFunc->m_displayContent2($suffix[$i]));
				
				$this->ObTpl->parse("attributefield_blk","TPL_ATTRIBUTEFIELD_BLK",true);
				}
		
				#DISPLAY PRODUCT IDS AND WIDTH/HEIGHT/DEPTH
				if(!empty($rowHead[0]->fItemWidth))
				{
					$this->ObTpl->set_var("TPL_VAR_ITEM_WIDTH",$rowHead[0]->fItemWidth);
					$this->ObTpl->parse("itemwidth_blk","TPL_ITEM_WIDTH_BLK");
				}
				if(!empty($rowHead[0]->fItemHeight))
				{
					$this->ObTpl->set_var("TPL_VAR_ITEM_HEIGHT",$rowHead[0]->fItemHeight);
					$this->ObTpl->parse("itemheight_blk","TPL_ITEM_HEIGHT_BLK");
				}
				if(!empty($rowHead[0]->fItemDepth))
				{
					$this->ObTpl->set_var("TPL_VAR_ITEM_DEPTH",$rowHead[0]->fItemDepth);
					$this->ObTpl->parse("itemdepth_blk","TPL_ITEM_DEPTH_BLK");
				}
				if(!empty($rowHead[0]->vASIN))
				{
					$this->ObTpl->set_var("TPL_VAR_ASIN",$rowHead[0]->vASIN);
					$this->ObTpl->parse("itemasin_blk","TPL_ITEM_ASIN_BLK");
				}
				if(!empty($rowHead[0]->vISBN))
				{
					$this->ObTpl->set_var("TPL_VAR_ISBN",$rowHead[0]->vISBN);
					$this->ObTpl->parse("itemisbn_blk","TPL_ITEM_ISBN_BLK");
				}
				if(!empty($rowHead[0]->vMPN))
				{
					$this->ObTpl->set_var("TPL_VAR_MPN",$rowHead[0]->vMPN);
					$this->ObTpl->parse("itemmpn_blk","TPL_ITEM_MPN_BLK");
				}
				if(!empty($rowHead[0]->vUPC))
				{
					$this->ObTpl->set_var("TPL_VAR_UPC",$rowHead[0]->vUPC);
					$this->ObTpl->parse("imtemupc_blk","TPL_ITEM_UPC_BLK");
				}
				if(!empty($rowHead[0]->vSku))
				{
					$this->ObTpl->set_var("TPL_VAR_SKU",$rowHead[0]->vSku);
					$this->ObTpl->parse("imtemsku_blk","TPL_ITEM_SKU_BLK");
				}
				if(!empty($rowHead[0]->fItemWeight))
				{
					$this->ObTpl->set_var("TPL_VAR_ITEM_WEIGHT",$rowHead[0]->fItemWeight);
					$this->ObTpl->parse("imtemweight_blk","TPL_ITEM_WEIGHT_BLK");
				}
				$this->ObTpl->parse("attributetable_blk","TPL_ATTRIBUTETABLE_BLK");
			}
		}
		
		#DISPALY PRICE FOR SELECTED PRODUCT
	
		$this->ObTpl->set_var("TPL_VAR_PRICEMAIN", $comFunc->m_Format_Price($rowHead[0]->fPrice));

	
		#CHECK CUSTOMER REVIEWS ENABLE /DISABLE MANAGED BY ADMIN
		if (CUSTOMER_REVIEWS == 1) {
			$reviewFormUrl = SITE_URL . "ecom/index.php?action=ecom.reviewForm&mode=" . $rowHead[0]->vSeoTitle;
			$this->ObTpl->set_var("TPL_VAR_REVIEWFORM", $this->libFunc->m_safeUrl($reviewFormUrl));

			$reviewPostUrl = SITE_URL . "ecom/index.php?action=ecom.reviewAdd&mode=" . $rowHead[0]->vSeoTitle;
			$this->ObTpl->set_var("TPL_VAR_REVIEWPOST", $this->libFunc->m_safeUrl($reviewPostUrl));

			if ($this->request['action'] == "ecom.reviewForm") {
				$this->ObTpl->parse("reviewform_blk", "TPL_REVIEWFORM_BLK");
			}
			#QUERY TO GET CONTENTS UNDER SELECTED 
			$this->obDb->query = "SELECT *  FROM " . REVIEWS . " WHERE iState=1 AND iItemid_FK='" . $rowHead[0]->iProdid_PK . "'";
			$rowContent = $this->obDb->fetchQuery();
			$reviewCount = $this->obDb->record_count;

			if ($reviewCount > 0) {
				for ($i = 0; $i < $reviewCount; $i++) {
					$this->ObTpl->set_var("link_blk", "");
					
					$this->ObTpl->set_var("TPL_VAR_DELETE_REVIEWURL", "");
					if (isset ($_SESSION['uid']) && isset ($_SESSION['uname']) && !$this->libFunc->m_isNull($_SESSION['uid'])) {
						$deleteReviewUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.deletereview&mode=" . $rowContent[$i]->iCustRevid_PK . "&id=" . $rowHead[0]->iProdid_PK);
						$this->ObTpl->set_var("TPL_VAR_DELETE_REVIEWURL", "<p class=\"deleteReview\">[ <a href='" . $deleteReviewUrl . "'>DELETE</a> ]</p>");
					}else{
						$this->ObTpl->set_var("TPL_VAR_DELETE_REVIEWURL", "");
					}
					$helpUrl = SITE_URL . "ecom/index.php?action=ecom.help&mode=" . $rowContent[$i]->iCustRevid_PK . "&id=" . $rowHead[0]->iProdid_PK;
					$this->ObTpl->set_var("TPL_VAR_HELPURL", $this->libFunc->m_safeUrl($helpUrl));
					$noHelpUrl = SITE_URL . "ecom/index.php?action=ecom.noHelp&mode=" . $rowContent[$i]->iCustRevid_PK . "&id=" . $rowHead[0]->iProdid_PK;
					$this->ObTpl->set_var("TPL_VAR_NOHELPURL", $this->libFunc->m_safeUrl($noHelpUrl));
					$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowContent[$i]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_COMMENT", $this->libFunc->m_displayContent($rowContent[$i]->vComment));
					$this->ObTpl->set_var("TPL_VAR_DATE", trim($this->libFunc->dateFormat2($rowContent[$i]->tmDateAdd)));
					
					##OVERALL PRODUCT STAR RANKING	
					$this->obDb->query = "SELECT SUM(vRank) as total, COUNT(iItemid_FK) as reviewcount FROM ".REVIEWS." WHERE iItemid_FK ='".$rowHead[0]->iProdid_PK."'";
					$OverallReviewRating = $this->obDb->fetchQuery();
					$ReviewRating = $OverallReviewRating[0]->total / $OverallReviewRating[0]->reviewcount;
					$ReviewRating = number_format($ReviewRating , 0, '.', '');
					
					switch ($rowContent[$i]->vRank)
					{
						case "0":
						$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating0\"><strong>Rating: 1/10</strong></p>");
						break;
						case "1":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating1\"><strong>Rating: 1/10</strong></p>");
						break;
						case "2":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating2\"><strong>Rating: 2/10</p></strong>");
						break;
						case "3":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating3\"><strong>Rating: 3/10</p></strong>");
						break;
						case "4":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating4\"><strong>Rating: 4/10</strong></p>");
						break;
						case "5":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating5\"><strong>Rating: 5/10</strong></p>");
						break;
						case "6":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating6\"><strong>Rating: 6/10</strong></p>");
						break;
						case "7":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating7\"><strong>Rating: 7/10</strong></p>");
						break;
						case "8":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating8\"><strong>Rating: 8/10</strong></p>");
						break;
						case "9":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating9\"><strong>Rating: 9/10</p></strong>");
						break;
						case "10":
						$this->ObTpl->set_var("TPL_VAR_RANK", "<p class=\"review rating10\"><strong>Rating: 10/10</strong></p>");
						break;	
					}
					if (isset ($_SESSION['userid'])) {
						$this->obDb->query = "SELECT COUNT(*) as cnt FROM " . REVIEWHELP . " WHERE iCustId_FK='" . $_SESSION['userid'] . "' AND iReviewId_FK='" . $rowContent[$i]->iCustRevid_PK . "'";
						$rs = $this->obDb->fetchQuery();
						if ($rs[0]->cnt == 0) {
							$this->ObTpl->parse("link_blk", "TPL_LINK_BLK");
						}
					}
					$this->obDb->query = "SELECT COUNT(*) as cnt FROM " . REVIEWHELP . " WHERE iReviewId_FK='" . $rowContent[$i]->iCustRevid_PK . "'";
					$rsCnt = $this->obDb->fetchQuery();

					$this->obDb->query = "SELECT COUNT(*) as cnt FROM " . REVIEWHELP . " WHERE iReviewId_FK='" . $rowContent[$i]->iCustRevid_PK . "' AND iStatus=1";
					$rsCntYes = $this->obDb->fetchQuery();
					

					#CHECK WHEATHER DISPLAY NAME OR NOT
					if (!$this->libFunc->m_isNull($rowContent[$i]->iDisplay)) {
						$this->ObTpl->set_var("TPL_VAR_BY", "<p class=\"reviewAuthor\"><strong>Posted by: " . $this->libFunc->m_displayContent($this->libFunc->m_getName($rowContent[$i]->iCustomerid_FK))."</strong></p>");
					} else {
						$this->ObTpl->set_var("TPL_VAR_BY", "");
					}
					$this->ObTpl->set_var("TPL_VAR_LBLREVIEW", LBL_REVIEW);
					$this->ObTpl->parse("dspreview_blk", "TPL_REVIEW_BLK", true);
				}
			} else {
				$this->ObTpl->set_var("TPL_VAR_LBLREVIEW", LBL_FIRST_REVIEW);
			}
			
			$this->ObTpl->parse("reviewrank_blk", "TPL_REVIEWRANK_BLK");
			$this->ObTpl->parse("reviewlink_blk", "TPL_REVIEWLINK_BLK", true);
			$this->ObTpl->parse("reviewcontainer_blk", "TPL_REVIEWCONTAINER_BLK");
			
		}

		#FUNCTION TO DISPLAY ATTACHED PRODUCT LIST	
		$this->m_attachedProduct($rowHead[0]->iProdid_PK, "product");
		#FUNCTION TO DISPLAY ATTACHED CONTENT LIST
		$this->m_dspAttachedContent($rowHead[0]->iProdid_PK, "product");

		return ($this->ObTpl->parse("return", "TPL_DETAILS_FILE"));
	} #END FUNCTION PRODUCT DISPALY

	#FUNCTION TO DISPLAY ATTACHED PRODUCTS
	function m_attachedProduct($ownerId, $ownerType) {
		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;
		
		$attCondition = "";
		if($this->request['sort']=='attribute'){
		$this->ObTpl->set_var("TPL_VAR_ATTRIBUTESELECTED","class=\"selected\"");
		$attCondition= " AND iAttrValueId_FK <> 0 ";
		}else{
		$this->ObTpl->set_var("TPL_VAR_OPTIONSELECTED","class=\"selected\"");
		}
		
		if(isset($this->request['listview']) && $this->request['listview']=="gridview"){
			$_SESSION['listview'] = $this->request['listview']; 
		}
		else
		{
			unset($_SESSION['listview']);
		}
		if ($_SESSION['listview']=="gridview"){
		$this->ObTpl->set_var("TPL_VAR_PRODUCTCLASS","class=\"products grid\"");
		$this->ObTpl->set_var("TPL_VAR_IMGONLYSELECTED","selected");
		}else{
		$this->ObTpl->set_var("TPL_VAR_PRODUCTCLASS","class=\"products\"");
		}
		
		if (!isset($_SESSION['sort'])){
		$_SESSION['sort'] = "ORDER BY iSort";
		}
		
		if (isset($this->request['sortbyprice'])){
		    if($this->request['sortbyprice'] == "ASC" || $this->request['sortbyprice'] == "DESC"){    
    		    $_SESSION['sort'] = "ORDER BY fPrice ".$this->request['sortbyprice'];
                $_SESSION['pricedirect'] = $this->request['sortbyprice'];
                unset($_SESSION['alphadirect']);
            }
		}
		if(isset($this->request['alphasort'])){
			if($this->request['alphasort'] == '0')
			{
				$_SESSION['sort'] = "";
				$_SESSION['alphadirect'] = "";
			}
			elseif($this->request['alphasort'] == 'ASC')
			{
				$_SESSION['sort'] = "ORDER BY vTitle ".$this->request['alphasort'];
				$_SESSION['alphadirect'] = $this->request['alphasort'];
			}
			elseif($this->request['alphasort'] == 'DESC')
			{
				$_SESSION['sort'] = "ORDER BY vTitle ".$this->request['alphasort'];
				$_SESSION['alphadirect'] = $this->request['alphasort'];
			}
			unset($_SESSION['pricedirect']);
		}
	
	
		if(isset($_SESSION['pricedirect'])){
			if ($_SESSION['pricedirect']=="ASC")
				$this->ObTpl->set_var("TPL_VAR_ASC2","selected = \"selected\"");
			if ($_SESSION['pricedirect']=="DESC")
				$this->ObTpl->set_var("TPL_VAR_DESC2","selected = \"selected\"");
		}
			
		if (isset($_SESSION['alphadirect'])){
			if ($_SESSION['alphadirect']=="ASC")
				$this->ObTpl->set_var("TPL_VAR_ASC1","selected = \"selected\"");
			if ($_SESSION['alphadirect']=="DESC")
				$this->ObTpl->set_var("TPL_VAR_DESC1","selected = \"selected\"");
		}
		if(isset($this->request['prodperpage']) && $this->request['prodperpage']!='0' && is_numeric($this->request['prodperpage'])){
			$_SESSION['pageSize']=$this->request['prodperpage'];
			$this->pageSize = $_SESSION['pageSize'];
		}
		elseif(isset($_SESSION['pageSize']) && !empty($_SESSION['pageSize']))
		{
			$this->pageSize = $_SESSION['pageSize'];
		}
			
				
		if (!isset($this->request['attkey'])){
		$query = "SELECT vTitle,iProdid_PK,iAttrValueId_FK,vSeoTitle,tShortDescription,fPrice,fRetailPrice,fListPrice,iTaxable,vImage1,iSale  ";
		$query .= " FROM " . PRODUCTS . " D, " . FUSIONS . " F WHERE iProdid_PK =iSubId_FK AND vtype='product' AND ";
		$query .= " iOwner_FK='" . $ownerId . "' AND vOwnerType='" . $ownerType . "' AND iState=1 ".$attCondition.$_SESSION['sort'];
		}else
		{
			$query="SELECT vTitle,iProdid_PK,iAttrValueId_FK,vSeoTitle,tShortDescription,fPrice,fRetailPrice,fListPrice,iTaxable,vImage1,iSale  ";
			$query.= "FROM ".PRODUCTS.", ".PRODUCTATTRIBUTES.", ".ATTRIBUTEVALUES;
			$query.= " WHERE iValueId_PK = iValueid_FK AND iProductid_FK = iProdid_PK";
			$query.=" AND tValues LIKE '%?".$this->request['attkey']."?%'";
		}
		$pn = new PrevNext($this->pageTplPath, $this->pageTplFile, $this->obDb);
		if ($ownerType == "product") {
			$extraStr = "ecom/index.php?action=ecom.pdetails&mode=" . $this->request['mode'];
		}
		elseif ($ownerType == "department") {
			$extraStr = "ecom/index.php?action=ecom.details&mode=" . $this->request['mode'];
		}
		$pn->formno = 1;
		$navArr = $pn->create($query, $this->pageSize, $extraStr, $this->noPaging);
		$pn2 = new PrevNext($this->pageTplPath, $this->pageTplFile, $this->obDb);

		$pn2->formno = 2;
		$navArr2 = $pn2->create($query, $this->pageSize, $extraStr, $this->noPaging);
		$rowProduct = $navArr['qryRes'];
		$totalRecords = $navArr['totalRecs'];
		$productCount = $navArr['fetchedRecords'];
		
		
		$attributeexistFlag=0;			
		if ($productCount > 0) {
			for ($i = 0; $i < $productCount; $i++) 
			{
				#MARGIN CALCULATOR
				switch (MARGINSTATUS)
				{
					case "increase":
						$rowProduct[$i]->fPrice= ($rowProduct[$i]->fPrice * MARGINPERCENT/100 ) + $rowProduct[$i]->fPrice;
					break;
					case "decrease":
						$rowProduct[$i]->fPrice=  $rowProduct[$i]->fPrice - ($rowProduct[$i]->fPrice * MARGINPERCENT/100 );
					break;
					default:
						$rowProduct[$i]->fPrice = $rowProduct[$i]->fPrice;
					break;	
				}
				#END MARGIN CALCULATOR
				//--- Switch to retail price if Retail customer
				if ($comFunc->m_checkCustomerType()==1 && ENABLE_WHOLESALE==1 && $rowProduct[$i]->fRetailPrice>0){
				$rowProduct[$i]->fPrice=$rowProduct[$i]->fRetailPrice;
				}
				//----End switch price	
                
                # CHECK IF THE DEPARTMENT HAS PRODUCTS WITH ATTRIBUTES
				if ($rowProduct[$i]->iAttrValueId_FK!=0)
				{
				$attributeexistFlag=1;
				}#
						
				$this->ObTpl->set_var("TPL_VAR_LISTOPTIONS", "");
				$this->ObTpl->set_var("TPL_VAR_LISTCHOICES", "");
				$this->ObTpl->set_var("TPL_VAR_ONSALE", "");
				$this->ObTpl->set_var("rrp_price_blk", "");

				$comFunc->productId = $rowProduct[$i]->iProdid_PK;
				$shopUrl = SITE_URL . "ecom/index.php?action=ecom.addmulticart&mode=" . $rowProduct[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_SHOPURL", $this->libFunc->m_safeUrl($shopUrl));
				if ($rowProduct[$i]->iSale == 1) {
					$this->ObTpl->set_var("TPL_VAR_ATTACHED_ONSALE", "<p class=\"onSale\">On Sale!</p>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ATTACHED_ONSALE", "");
				}
				$productUrl = SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $rowProduct[$i]->vSeoTitle;
				$_SESSION['own'] = $ownerId;
				$this->ObTpl->set_var("TPL_VAR_PRODUCTURL", $this->libFunc->m_safeUrl($productUrl));
				$this->ObTpl->set_var("TPL_VAR_ID", $this->libFunc->m_displayContent($rowProduct[$i]->iProdid_PK));
				$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowProduct[$i]->vTitle));
				
				##TO CHECK WHEATHER TO DISPLAY WISHLIST OR NOT MANAGED BY ADMIN
				if (USEWISHLIST == 1) 
				{
					//if ($this->request['listview']=="gridview"){
					//$this->ObTpl->set_var("attached_wishlist_blk","");	
					//}else{
					$wishListUrl = SITE_URL . "ecom/index.php?action=wishlist.add&mode=" . $rowProduct[$i]->iProdid_PK;
					$this->ObTpl->set_var("TPL_VAR_ATTACHED_WISHLISTLINK", "<p id=\"addWishlistAttached\"><a href=\"".$this->libFunc->m_safeUrl($wishListUrl)."\">Add to Wish List</a></p>");
					//$this->ObTpl->parse("attached_wishlist_blk", "TPL_ATTACHED_WISHLIST_BLK");
					//}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ATTACHED_WISHLISTLINK","");
				}
									
				#TO CHECK WHETHER TO DISPLAY COMPARELIST OR NOT MANAGED BY ADMIN
				if (USECOMPARE == 1) 
				{
					//if ($this->request['listview']=="gridview"){
					//$this->ObTpl->set_var("attached_compare_blk","");
					//}else{
					$compareListUrl = SITE_URL . "ecom/index.php?action=compare.add&mode=" . $rowProduct[$i]->iProdid_PK;
					$this->ObTpl->set_var("TPL_VAR_ATTACHED_COMPARELINK", "<p id=\"addComparisonAttached\"><a href=\"".$this->libFunc->m_safeUrl($compareListUrl)."\">Add to Comparison List</a></p>");
					//$this->ObTpl->parse("attached_compare_blk", "TPL_ATTACHED_COMPARE_BLK");
					//}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ATTACHED_COMPARELINK", "");
				}
				if ($rowProduct[$i]->tShortDescription != "") {
					if ($this->request['listview']=="gridview"){
					$this->ObTpl->set_var("productdesc_blk","");	
					}else
					{
					$this->ObTpl->set_var("TPL_VAR_DESC", nl2br($this->libFunc->m_displayContent($rowProduct[$i]->tShortDescription)));
					$this->ObTpl->parse("productdesc_blk", "TPL_PDESC_BLK");
					}
				}else{
					if ($this->request['listview']=="gridview"){
						$this->ObTpl->set_var("productdesc_blk","");	
					}else
					{
						$this->ObTpl->set_var("TPL_VAR_DESC", "");
						$this->ObTpl->parse("productdesc_blk", "TPL_PDESC_BLK");
					}
				} 
			
				# MANIPULATE NETGROSS & INC_VAT DISPLAY	
				if($rowProduct[$i]->iTaxable==1)
				{
					$this->ObTpl->set_var("TPL_VAR_PRICE", $comFunc->m_Format_Price($rowProduct[$i]->fPrice));
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($rowProduct[$i]->fPrice,2));
				}
				##CHECK FOR RRP PRICE
				if (!$this->libFunc->m_isNull($rowProduct[$i]->fListPrice) && $rowProduct[$i]->fListPrice > 0) {
					$this->ObTpl->set_var("TPL_VAR_RRP_AMOUNT", RRP_TEXT . ": <strike>" . CONST_CURRENCY . number_format($rowProduct[$i]->fListPrice, 2, '.', '') . "</strike>");
					$this->ObTpl->parse("rrp_price_blk", "TPL_RRPPRICE_BLK");
				}
				if (!$this->libFunc->m_isNull($rowProduct[$i]->vImage1)) {
					$img = $this->libFunc->m_checkFile($rowProduct[$i]->vImage1, "product", $this->libFunc->m_displayContent($rowProduct[$i]->vTitle));
					if ($img) {
						$this->ObTpl->set_var("TPL_VAR_IMG", $img);

					} else {
						$this->ObTpl->set_var("TPL_VAR_IMG", MSG_NOIMG);
					}
				} else {
					$this->ObTpl->set_var("TPL_VAR_IMG", MSG_NOIMG);
				}
				
			if (CUSTOMER_REVIEWS == 1){
				##OVERALL PRODUCT STAR RANKING	
				$this->obDb->query = "SELECT SUM(vRank) as total, COUNT(iItemid_FK) as reviewcount FROM ".REVIEWS." WHERE iItemid_FK ='".$rowProduct[$i]->iProdid_PK."'";
				$OverallReviewRating = $this->obDb->fetchQuery();
				$ReviewRating = $OverallReviewRating[0]->total / $OverallReviewRating[0]->reviewcount;
				$ReviewRating = number_format($ReviewRating , 0, '.', '');
									
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
					$this->ObTpl->set_var("TPL_VAR_OVERALLRANK", "<p class=\"review rating5\">".$OverallReviewRating[0]->reviewcount."reviews</p>");
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
			$this->ObTpl->parse("reviewrank_blk", "TPL_REVIEWRANK_BLK");
			}
				
				#GET OPTIONS
				$this->ObTpl->set_var("TPL_VAR_LISTOPTIONS", $comFunc->m_getOptions('0'));
				#GET CHOICES
				$this->ObTpl->set_var("TPL_VAR_LISTCHOICES", $comFunc->m_getChoices());
				$this->ObTpl->parse("dspproduct_blk", "TPL_PRODUCT_BLK", true);
		}# END FOR LOOP
			
				# DISPLAY "SORT BY ATTRIBUTES" TAB IF THE DEPARTMENT HAS PRODUCTS WITH ATTRIBUTE 
				if ($attributeexistFlag==1)
				{
						$this->ObTpl->parse("sortattributetab_blk","TPL_SORTATTRIBUTETAB_BLK");
				}
				if ($this->request["sort"]=="attribute"){
				$this->ObTpl->parse("sortattribute_blk","TPL_SORTATTRIBUTE_BLK");	
				}else{
				$this->ObTpl->parse("sortoptions_blk","TPL_SORTOPTIONS_BLK");
				}
				
				$this->ObTpl->parse("mainproduct_blk", "TPL_MAINPRODUCT_BLK");
	} # END IF PRODUCT COUNT > 0
		
		if ($totalRecords > $this->pageSize) {
			#PAGINATION
			$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
			$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
		} else {
			$this->ObTpl->set_var("PagerBlock1", "");
			$this->ObTpl->set_var("PagerBlock2", "");
		}
	}

	#FUNCTION TO DISPLAY ATTACHED CONTENT
	function m_dspAttachedContent($ownerId, $ownerType) {
		#QUERY TO GET CONTENTS UNDER SELECTED 
		$this->obDb->query = "SELECT vTitle,vSeoTitle,iContentid_PK,vImage1  FROM " . CONTENTS . " D, " . FUSIONS . " F WHERE iContentid_PK=iSubId_FK AND vtype='content' AND iOwner_FK='" . $ownerId . "' AND vOwnerType='" . $ownerType . "' and iState=1 ORDER BY iSort";
		$rowContent = $this->obDb->fetchQuery();
		$contentCount = $this->obDb->record_count;

		if ($contentCount > 0) {
			for ($i = 0; $i < $contentCount; $i++) {
				$contentUrl = SITE_URL . "ecom/index.php?action=ecom.cdetails&mode=" . $rowContent[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_CONTENTURL", $this->libFunc->m_safeUrl($contentUrl));
				$this->ObTpl->set_var("TPL_VAR_ID", $this->libFunc->m_displayContent($rowContent[$i]->iContentid_PK));

				if (!$this->libFunc->m_isNull($rowContent[$i]->vImage1)) {
					$img = $this->libFunc->m_checkFile($rowContent[$i]->vImage1, "content", $this->libFunc->m_displayContent($rowContent[$i]->vTitle));
					if ($img) {
						$this->ObTpl->set_var("TPL_VAR_TITLE", $img);
					} else {
						$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowContent[$i]->vTitle));
					}
				} else {
					$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowContent[$i]->vTitle));
				}
				$this->ObTpl->parse("dspcontent_blk", "TPL_CONTENT_BLK", true);
			}
			$this->ObTpl->parse("dspmaincontent_blk", "TPL_MAINCONTENT_BLK");
		}
	}

	#FUNCTION TO DISPLAY LARGE IMAGE
	function m_dspLargeImg() {
		$this->ObTpl = new template();
		#SETTING FILE
		$this->ObTpl->set_file("TPL_IMG_FILE", $this->templatePath . $this->largeImage);

		$this->request['mode'] = $this->libFunc->ifSet($this->request, "mode", "");
		$this->request['type'] = $this->libFunc->ifSet($this->request, "type", "");
		if ($this->request['type'] == "gift") {
			$this->obDb->query = "SELECT vTitle,vImageLarge FROM " . GIFTWRAPS . " WHERE  iState='1' AND iGiftwrapid_PK='" . $this->request['mode'] . "'";
			$rowImg = $this->obDb->fetchQuery();
			$image = $rowImg[0]->vImageLarge;
			$imageDirectory = "giftwrap";
			$cartUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
			$this->breadcrumb = "&nbsp;&raquo;&nbsp;<a href=" . $cartUrl . ">Shopping basket</a>&nbsp;&raquo;&nbsp;Gift wrap";
			$returnLink = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.giftwrap&mode=" . $this->request['mode']);

			$this->ObTpl->set_var("TPL_VAR_RETURNLINK", "<a href=" . $returnLink . ">Return to giftwrap ></a>");
		} else {
			$this->obDb->query = "SELECT vTitle,vImage3 	FROM " . PRODUCTS . " WHERE vSeoTitle='" . $this->request['mode'] . "'";
			$rowImg = $this->obDb->fetchQuery();
			$image = $rowImg[0]->vImage3;
			$imageDirectory = "product";
			$this->breadcrumb = $this->m_topNavigation('product');
			$returnLink = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $this->request['mode']);
			$this->ObTpl->set_var("TPL_VAR_RETURNLINK", "<a href=" . $returnLink . ">Return to product ></a>");
		}

		if (!$this->libFunc->m_isNull($image)) {
			$img = $this->libFunc->m_checkFile($image, $imageDirectory, $this->libFunc->m_displayContent($rowImg[0]->vTitle));
			if ($img) {
				$this->ObTpl->set_var("TPL_VAR_IMG", $img);
			} else {
				$this->ObTpl->set_var("TPL_VAR_IMG", MSG_NOIMG);
			}
		} else {
			$this->ObTpl->set_var("TPL_VAR_IMG", MSG_NOIMG);
		}
		return $this->ObTpl->parse("output", "TPL_IMG_FILE");
	} #END LARGE IMAGE FUNCTION

	#FUNCTION TO DISPLAY VIEWCART** DETAILS
	function m_viewCart() {
		$libFunc = new c_libFunctions();
		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;
		$withoutBackorder = 0;
		$maxPostage = 0;
		#QUERY TEMPERARY & PRODUCT TABLE
		$this->obDb->query = "SELECT vTitle,vSeoTitle,fPrice,fRetailPrice,vSku,iQty,iTmpCartId_PK,iProdId_FK,vImage1,";
		$this->obDb->query .= "iKit,iGiftWrap,fVolDiscount,iTaxable,fItemWeight,";
		$this->obDb->query .= "iFreeShip,iOnorder,vShipCode,vShipNotes,tmDuedate  ";
		$this->obDb->query .= " FROM " . TEMPCART . " AS T," . PRODUCTS . " AS P  WHERE ";
		$this->obDb->query .= "(iProdId_FK=iProdId_PK AND vSessionId='" . SESSIONID . "') ";
		#FLAG TO INDICATE SEPERATE BACKORDER AND NORMAL ORDER

		$_SESSION['backOrderSeperate'] = $this->libFunc->ifSet($_SESSION, 'backOrderSeperate', '0');

		#FLAG TO INDICATE WHETHER PROCESSING BACKORDER OR NOT
		$_SESSION['backOrderProcess'] = $this->libFunc->ifSet($_SESSION, 'backOrderProcess', '0');

		if ($_SESSION['backOrderSeperate'] == 1 && $_SESSION['backOrderProcess'] == 1) {
			$this->obDb->query .= " AND T.iBackOrder='1'";
		}
		elseif ($_SESSION['backOrderSeperate'] == 1) {
			$this->obDb->query .= " AND T.iBackOrder<>'1'";
		}
		$this->obDb->query .= " ORDER BY T.iTmpCartId_PK";
		$rowCart = $this->obDb->fetchQuery();
		$rowCartCount = $this->obDb->record_count;

		$this->ObTpl = new template();
		$this->ObTpl->set_file("TPL_DETAILS_FILE", $this->template);
		
		$this->ObTpl->set_var("TPL_VAR_JAVASCRIPTS",file_get_contents(SITE_PATH."jscript/viewcart.js"));

		#SETTING BLOCKS FRO CART DISPLAY PAGE
		$this->ObTpl->set_block("TPL_DETAILS_FILE", "TPL_CART_BLK", "cart_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_VAR_CARTPRODUCTS", "cartproduct_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_VAR_POSTAGEDROPDOWN", "postagedropdown_blk");
        $this->ObTpl->set_block("TPL_VAR_POSTAGEDROPDOWN", "TPL_VAR_POSTAGESTATEDROPDOWN", "postagestatedropdown_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_REFUND_BLK", "return_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_MPOINTS_BLK", "memberpoint_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_DISCOUNTS_BLK", "discounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_VOLDISCOUNTS_BLK", "volDiscounts_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_CARTWEIGHT_BLK", "cartWeight_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_POSTAGE_BLK", "postage_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_DISCOUNT_BLK", "discount_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_GIFTCERT_BLK", "giftcert_blk");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_VAT_BLK", "vat_blk");
		
		
		$this->ObTpl->set_block("TPL_VAR_CARTPRODUCTS", "TPL_KIT_BLK", "kit_blk");
		$this->ObTpl->set_block("TPL_VAR_CARTPRODUCTS", "TPL_GIFTWRAP_BLK", "gift_blk");

		#IMAGES BLOCKS
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_MASTERCARD_BLK", "MASTERCARD_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_VISA_BLK", "VISA_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_AMEX_BLK", "AMEX_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_DISCOVER_BLK", "DISCOVER_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_DINERS_CLUB_BLK", "DINERS_CLUB_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_MAESTRO_BLK", "MAESTRO_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_SOLO_BLK", "SOLO_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_SWITCHCARD_BLK", "SWITCHCARD_BLK");
		$this->ObTpl->set_block("TPL_CART_BLK", "TPL_STARTCHECKOUT_BLK", "startCheckout_blk");
		
	

		#country and state blocks
	//	$this->ObTpl->set_block("TPL_CART_BLK", "countryblk", "countryblks");
	//	$this->ObTpl->set_block("TPL_CART_BLK", "BillCountry", "nBillCountry");
		$this->ObTpl->set_block("TPL_VAR_POSTAGEDROPDOWN", "BillCountry", "nBillCountry");
	//	$this->ObTpl->set_block("TPL_CART_BLK", "stateblk", "stateblks");

		#INTAILAIZING
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_SAFESITEURL", SITE_SAFEURL);
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY", CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_VAT", "");
		$this->ObTpl->set_var("TPL_VAR_TAXNAME", VAT_TAX_TEXT);
		$this->ObTpl->set_var("TPL_VAR_MSG", "");

		$this->ObTpl->set_var("cart_blk", "");
		$this->ObTpl->set_var("return_blk", "");
		$this->ObTpl->set_var("cartproduct_blk", "");
		$this->ObTpl->set_var("memberpoint_blk", "");
		$this->ObTpl->set_var("discounts_blk", "");
		$this->ObTpl->set_var("volDiscounts_blk", "");
		$this->ObTpl->set_var("cartWeight_blk", "");
		$this->ObTpl->set_var("postage_blk", "");
		$this->ObTpl->set_var("discount_blk", "");
		$this->ObTpl->set_var("giftcert_blk", "");
		$this->ObTpl->set_var("kit_blk", "");
		$this->ObTpl->set_var("vat_blk", "");
		$this->ObTpl->set_var("gift_blk", "");
		$this->ObTpl->set_var("startCheckout_blk", "");
		$this->ObTpl->set_var("postagedropdown_blk", "");
		$this->ObTpl->set_var("postagestatedropdown_blk", "");
	

		#defining language pack variables.
		$this->ObTpl->set_var("LANG_VAR_SHOPPINGBASKET", LANG_SHOPPINGBASKET);
		$this->ObTpl->set_var("LANG_VAR_ITEMQUANTITY", LANG_ITEMQUANTITY);
		$this->ObTpl->set_var("LANG_VAR_ADDGIFTWRAP", LANG_ADD_GIFTWRAP);
		$this->ObTpl->set_var("LANG_VAR_PRODUCT", LANG_PRODUCT);
		$this->ObTpl->set_var("LANG_VAR_PRICE", LANG_EXCLUDEVATPRICE);
		$this->ObTpl->set_var("LANG_VAR_TOTAL", LANG_TOTAL);
		$this->ObTpl->set_var("LANG_VAR_REMOVE", LANG_REMOVE);
		$this->ObTpl->set_var("LANG_VAR_MEMACCUMULATE", LANG_ACCUMULATE);
		$this->ObTpl->set_var("LANG_VAR_REWARDPOINTS", LANG_REWARDPOINTS);
		$this->ObTpl->set_var("LANG_VAR_SUBTOTAL", LANG_SUBTOTAL);
		$this->ObTpl->set_var("LANG_VAR_VOLUMEDISCOUNT", LANG_VOLUMEDISCOUNT);
		$this->ObTpl->set_var("LANG_VAR_PRODUCTWEIGHT", LANG_PRODUCTWEIGT);
		$this->ObTpl->set_var("LANG_VAR_POSTAGEMETHOD", LANG_POSTAGEMETHOD);
		$this->ObTpl->set_var("LANG_VAR_CURRENTTOTAL", LANG_CURRENTTOTAL);
		$this->ObTpl->set_var("LANG_VAR_STARTCHECKOUT", LANG_STARTCHECKOUT);
		$this->ObTpl->set_var("LANG_VAR_UPDATEBASKET", LANG_UPDATEBASKET);
		$this->ObTpl->set_var("LANG_VAR_EMPTYBASKET", LANG_EMPTYBASKET);
		$this->ObTpl->set_var("LANG_VAR_CONTINUESHOP", LANG_CONTINUESHOP);
		$this->ObTpl->set_var("LANG_VAR_PRODDELCONFIRM", LANG_PRODDELETECONFIRM);
		$this->ObTpl->set_var("LANG_VAR_EMPTYBASKCONFIRM", LANG_EMPTYBASKETCONF);

		#######Country blocks

		$this->obDb->query = "SELECT iStateId_PK, vStateName FROM ".STATES." ORDER BY vStateName";
		$row_state = $this->obDb->fetchQuery();
		$row_state_count = $this->obDb->record_count;
		
		$this->obDb->query = "SELECT iCountryId_PK, vCountryName, vShortName FROM  ".COUNTRY." ORDER BY iSortFlag,vCountryName";
		$row_country = $this->obDb->fetchQuery();
		$row_country_count = $this->obDb->record_count;

		# Loading billing country list		
		for($i=0;$i<$row_country_count;$i++)
		{
			$this->ObTpl->set_var("k", $row_country[$i]->iCountryId_PK);
			$this->ObTpl->parse('countryblks','countryblk',true);
			$this->ObTpl->set_var("TPL_COUNTRY_VALUE", $row_country[$i]->iCountryId_PK);
			
			
			//if($row_customer[0]->vCountry> 0)
			//{
			//	if($row_customer[0]->vCountry == $row_country[$i]->iCountryId_PK)
			//		$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
			//	else
			//		$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "");
			//}
			
			$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("nBillCountry","BillCountry",true);
		}
		
		if(isset($row_customer[0]->vCountry) && $row_customer[0]->vCountry != '')	
			$this->ObTpl->set_var('selbillcountid',$row_customer[0]->vCountry);
		else
			$this->ObTpl->set_var('selbillcountid',"1");

        $this->ObTpl->parse("postagestatedropdown_blk", "TPL_VAR_POSTAGESTATEDROPDOWN");
		
		//if((isset($_SESSION['RATESDEFINED'])) && ($_SESSION['RATESDEFINED'] == "NO")){
		//	$this->ObTpl->set_var("TPL_VAR_MSG","<p class=\"message\">Sorry, you will not be able to checkout.</p>");
		//}	
		
		#To Show Cart images according to admin
		if (MASTERCARD)
			$this->ObTpl->parse("MASTERCARD_BLK", "TPL_MASTERCARD_BLK", true);
		else
			$this->ObTpl->set_var("MASTERCARD_BLK", "");
		if (VISA)
			$this->ObTpl->parse("VISA_BLK", "TPL_VISA_BLK", true);
		else
			$this->ObTpl->set_var("VISA_BLK", "");
		if (AMEX)
			$this->ObTpl->parse("AMEX_BLK", "TPL_AMEX_BLK", true);
		else
			$this->ObTpl->set_var("AMEX_BLK", "");
		if (DISCOVER)
			$this->ObTpl->parse("DISCOVER_BLK", "TPL_DISCOVER_BLK", true);
		else
			$this->ObTpl->set_var("DISCOVER_BLK", "");
		if (DINERS_CLUB)
			$this->ObTpl->parse("DINERS_CLUB_BLK", "TPL_DINERS_CLUB_BLK", true);
		else
			$this->ObTpl->set_var("DINERS_CLUB_BLK", "");
		if (MAESTRO)
			$this->ObTpl->parse("MAESTRO_BLK", "TPL_MAESTRO_BLK", true);
		else
			$this->ObTpl->set_var("MAESTRO_BLK", "");
		if (SOLO)
			$this->ObTpl->parse("SOLO_BLK", "TPL_SOLO_BLK", true);
		else
			$this->ObTpl->set_var("SOLO_BLK", "");
		if (SWITCHCARD)
			$this->ObTpl->parse("SWITCHCARD_BLK", "TPL_SWITCHCARD_BLK", true);
		else
			$this->ObTpl->set_var("SWITCHCARD_BLK", "");
		#CHECKING WITHOUT BACKORDER ITEMS
		$this->obDb->query = "SELECT iProdId_FK FROM " . TEMPCART . " AS T ";
		$this->obDb->query .= " WHERE (vSessionId='" . SESSIONID . "')";
		if ($_SESSION['backOrderSeperate'] == 1 && $_SESSION['backOrderProcess'] == 1) {
			$this->obDb->query .= " AND T.iBackOrder='1'";
		}
		elseif ($_SESSION['backOrderSeperate'] == 1) {
			$this->obDb->query .= " AND T.iBackOrder<>'1'";
		}
		$rowProductId = $this->obDb->fetchQuery();
		$rowIdCount = $this->obDb->record_count;
		if ($rowIdCount > 0) {
			for ($j = 0; $j < $rowIdCount; $j++) {
				#TO CHECK BACK ORDER	
				if (!isset ($_SESSION['backorder'][$rowProductId[$j]->iProdId_FK]) || $_SESSION['backorder'][$rowProductId[$j]->iProdId_FK] != 1) {
					#TO SET THE FLAG WHEATHER GOT SOME PRODUCT WITH NO BACKOREDR
					$withoutBackorder = 1;
				}
			}
		}
		if ($rowCartCount > 0) {

			/* THIS NEED TO CHECK IF DROP SHIP FEATURE IS ENABLE */

			# COUNT TOTAL OF SUPPLIER FROM BASKET			
			$id_rows = array ();
			for ($i = 0; $i < $rowIdCount; $i++) {
				$id_rows[$i] = $rowProductId[$i]->iProdId_FK;
			}

			$this->obDb->query = " SELECT distinct iVendorid_FK FROM " . PRODUCTS .
			" WHERE iVendorid_FK>0 AND iProdid_PK IN (" . implode(",", $id_rows) . ")";

			$row = $this->obDb->fetchQuery();
			$totalVendor = $this->obDb->record_count;

			$_SESSION['totalVendor'] = $totalVendor;

			if ($_SESSION['totalVendor'] > 0) {
				$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER", $_SESSION['totalVendor']);
			} else {
				$this->ObTpl->set_var("TPL_VAR_TOTAL_SUPPLIER", "");
			}
			$novattotal = 0;
			for ($i = 0; $i < $rowCartCount; $i++) {
				$this->price = 0; #INTIALIZING
				$this->total = 0;
				#FOR POSTAGE-CODES
				$comFunc->productId = $rowCart[$i]->iProdId_FK;
				$comFunc->qty = $rowCart[$i]->iQty;
				$comFunc->price = $this->price;

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

				#INTIALIZING
				$this->ObTpl->set_var("TPL_VAR_SHIPNOTES", "");
				$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT", "");
				$this->ObTpl->set_var("TPL_VAR_BACKORDER", "");
				$this->ObTpl->set_var("TPL_VAR_OPTIONS", "");
				$this->ObTpl->set_var("TPL_VAR_CHOICES", "");
				$this->ObTpl->set_var("kit_blk", "");
				$this->ObTpl->set_var("TPL_VAR_VATTAXMSG", "");
				$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG", "");

				$this->ObTpl->set_var("TPL_VAR_CARTID", $rowCart[$i]->iTmpCartId_PK);
				$comFunc->cartId = $rowCart[$i]->iTmpCartId_PK;

				#TO CHECK BACK ORDER	
				if (isset ($_SESSION['backorder'][$rowCart[$i]->iProdId_FK]) && $_SESSION['backorder'][$rowCart[$i]->iProdId_FK] == 1) {
					$strBackOrder = "This item is on backorder";
					if ($withoutBackorder == 1) {
						$strBackOrder .= ": [<a href=" . $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.instructions&mode=" . $rowCart[$i]->iTmpCartId_PK) . ">Instructions</a>]";
					}
					if ($rowCart[$i]->iOnorder > 0) {
						$strBackOrder .= "<br />On Order: " . $rowCart[$i]->iOnorder;
					}

					if (!$this->libFunc->m_isNull($rowCart[$i]->tmDuedate)) {
						$formatedDueDate = $this->libFunc->dateFormat2($rowCart[$i]->tmDuedate);
						$strBackOrder .= " (Due date: " . $formatedDueDate . ")";
					}
					$this->ObTpl->set_var("TPL_VAR_BACKORDER", $strBackOrder . "</ br>");
				} else {
					$withoutBackorder = 1;
				}

				$giftWrapUrl = SITE_URL . "ecom/index.php?action=ecom.giftwrap&mode=" . $rowCart[$i]->iTmpCartId_PK;
				$this->ObTpl->set_var("TPL_VAR_GIFTWRAPURL", $this->libFunc->m_safeUrl($giftWrapUrl));
				##GIFTWRAP URL
				$this->ObTpl->set_var("TPL_VAR_GIFTWRAP", "");
				$this->ObTpl->set_var("gift_blk", "");
				if ($rowCart[$i]->iGiftWrap != 0 && ENABLE_GIFTWRAP == 1) {
					$this->ObTpl->set_var("TPL_VAR_GIFTWRAP", $comFunc->m_dspGiftWrap($rowCart[$i]->iGiftWrap, $rowCart[$i]->iTmpCartId_PK));
				}
				elseif (ENABLE_GIFTWRAP == 1) {
					$this->ObTpl->parse("gift_blk", "TPL_GIFTWRAP_BLK");
				}
				if ($rowCart[$i]->iKit == 1) {
					$this->obDb->query = "SELECT vTitle,iProdId_FK,vSku FROM " . PRODUCTKITS . "," . PRODUCTS . " WHERE iProdId_FK=iProdId_PK AND iKitId='" . $rowCart[$i]->iProdId_FK . "'";
					$rsKit = $this->obDb->fetchQuery();
					$rsKitCount = $this->obDb->record_count;
					for ($j = 0; $j < $rsKitCount; $j++) {
						$comFunc->kitProductId = $rsKit[$j]->iProdId_FK;
						#GET CART OPTIONS
						$kitOptions = $comFunc->m_dspCartProductKitOptions();
						if ($kitOptions == ' ') {
							$this->ObTpl->set_var("TPL_VAR_KITOPTIONS", "");
						} else {
							$this->ObTpl->set_var("TPL_VAR_KITOPTIONS", $kitOptions);
						}

						$this->ObTpl->set_var("TPL_VAR_KITSKU", $this->libFunc->m_displayContent($rsKit[$j]->vSku));
						$this->ObTpl->set_var("TPL_VAR_KITTITLE", $this->libFunc->m_displayContent($rsKit[$j]->vTitle));
						$this->ObTpl->parse("kit_blk", "TPL_KIT_BLK", true);
					}
				} else {
					#GET CART OPTIONS
					$this->ObTpl->set_var("TPL_VAR_OPTIONS", $comFunc->m_dspCartProductOptions());
					#GET CART CHOICES
					$this->ObTpl->set_var("TPL_VAR_CHOICES", $comFunc->m_dspCartProductChoices());
				}

				# (OPTION And choice effected amount)
				$this->price = $comFunc->price;

				if (!$this->libFunc->m_isNull($rowCart[$i]->vShipNotes)) {
					$this->ObTpl->set_var("TPL_VAR_SHIPNOTES", "Notes: " . $this->libFunc->m_displayContent($rowCart[$i]->vShipNotes) . "<br />");
				}

				#POSTAGE 
				if ($rowCart[$i]->iFreeShip == 1) {
					$this->ObTpl->set_var("TPL_VAR_FREESHIPMSG", "<em>" . LBL_FREEPP . "</em><br />");
				} else {
					if (DEFAULT_POSTAGE_METHOD == 'codes' && !$this->libFunc->m_isNull($rowCart[$i]->vShipCode)) {
						$comFunc->postageId = $rowCart[$i]->vShipCode;
						if (DEFAULT_HIGHEST) {
							if ($this->postagePrice < $comFunc->m_postageCodePrice())
								$this->postagePrice = $comFunc->m_postageCodePrice();
						} else {
							$this->postagePrice += $comFunc->m_postageCodePriceMultiplyQty();
						}
					}
				}

				if (isset ($_SESSION['calcShip'])) {

					$this->obDb->query = "SELECT fShipCharge FROM " . COUNTRY . " WHERE iCountryId_PK =" . $this->request['bill_country_id'];
					$cShip = $this->obDb->fetchQuery();
					$cShip_count = $this->obDb->record_count;

					$this->obDb->query = "SELECT fShipCharge FROM " . STATES . " WHERE iStateId_PK =" . $this->request['bill_state_id'];
					$sShip = $this->obDb->fetchQuery();
					$sShip_count = $this->obDb->record_count;

					if ($cShip[0]->fShipCharge == $sShip[0]->fShipCharge) {
						$this->ObTpl->set_var("TPL_VAR_POSTAGE", $cShip[0]->fShipCharge);
					}
					elseif ($cShip[0]->fShipCharge != $sShip[0]->fShipCharge && $sShip[0]->fShipCharge > 0) {

						$this->ObTpl->set_var("TPL_VAR_POSTAGE", $sShip[0]->fShipCharge);

					}
				}
				
				
				
				
				
				#VOLUME DISCOUNT
				#****************************************************************
				#DISCOUNT ACCORDING TO QTY
				$vDiscoutPerItem = number_format($rowCart[$i]->fVolDiscount, 2, '.', '');
				$vDiscountPerCartElement = number_format(($rowCart[$i]->iQty * $vDiscoutPerItem), 2, '.', '');
				if ($vDiscoutPerItem > 0) {
					$this->ObTpl->set_var("TPL_VAR_CART_VOLDISCOUNT", "Volume Discount: " .
					CONST_CURRENCY . $vDiscoutPerItem . " each - Total: " . CONST_CURRENCY . $vDiscountPerCartElement . "<br />");
				$this->volDiscount = $this->volDiscount + $vDiscountPerCartElement;
				}
				#**************************************************************
				$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowCart[$i]->vTitle));
				$this->ObTpl->set_var("LANG_VAR_VIEWCARTIMAGES",LANG_VIEWCARTIMAGE);
				//$this->ObTpl->set_var("TPL_VAR_CARTIMAGE",$this->libFunc->m_displayContent($rowCart[$i]->vImage1));  
				if ($this->libFunc->m_displayContent($rowCart[$i]->vImage1) != "") {
				$this->ObTpl->set_var("TPL_VAR_CARTIMAGE_TAG","<img src=\"" . SITE_URL . "libs/timthumb.php?src=/images/product/" . $this->libFunc->m_displayContent($rowCart[$i]->vImage1) . "&amp;h=70&amp;w=70&amp;zc=r\" alt=\"" . $this->libFunc->m_displayContent($rowCart[$i]->vTitle) . "\" />");
				} else {
					$this->ObTpl->set_var("TPL_VAR_CARTIMAGE_TAG","No image available");
				}
				
				$strTitle = $this->libFunc->m_displayContent($rowCart[$i]->vTitle);
				$strTitle = str_replace("'", "\'", $strTitle);
				$this->ObTpl->set_var("TPL_VAR_TITLE1", $strTitle);
				$this->ObTpl->set_var("TPL_VAR_SKU", $this->libFunc->m_displayContent($rowCart[$i]->vSku));

				$this->price = $this->price + $rowCart[$i]->fPrice;
				$fullprice = $this->price;
				#locloc
				if ($rowCart[$i]->iTaxable == 1)
				{
					$this->taxTotal += $rowCart[$i]->iQty * $this->price;
				}
				else
				{
					$novattotal = $novattotal + ($rowCart[$i]->fPrice * $rowCart[$i]->iQty);
				}
				#locloc
				$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($this->price, 2, '.', ''));
			
				$this->ObTpl->set_var("TPL_VAR_QTY", $rowCart[$i]->iQty);
				$this->totalQty += $rowCart[$i]->iQty;

				$this->total += $rowCart[$i]->iQty * $this->price;
				$this->ObTpl->set_var("TPL_VAR_TOTAL", number_format($this->total, 2, '.', ''));

				
				if ($rowCart[$i]->iTaxable == 0 && HIDENOVAT != 1)
				{
					$this->ObTpl->set_var("TPL_VAR_VATTAXMSG", "<em>" . LBL_NOTAX . "</em><br />");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_VATTAXMSG", "");
				}

				if ($rowCart[$i]->iFreeShip != 1) {
					$this->postageTotal += $this->total;
				} else {
					$this->postageQty += $rowCart[$i]->iQty;
				}
				$this->subTotal = $this->subTotal + $this->total;
				//Quantity Multiplied
				if ($rowCart[$i]->fItemWeight > 0) {
					$this->cartWeight += $rowCart[$i]->fItemWeight * $rowCart[$i]->iQty;
				}
				$_SESSION['cartweight'] = $this->cartWeight; // for shipping estimate	
				#SAFE URLS
				$removeUrl = SITE_URL . "ecom/index.php?action=ecom.remove&mode=" . $rowCart[$i]->iTmpCartId_PK;
				$this->ObTpl->set_var("TPL_VAR_REMOVEURL", $this->libFunc->m_safeUrl($removeUrl));

				$cartUpdateUrl = SITE_URL . "ecom/index.php?action=ecom.updateCart";
				$this->ObTpl->set_var("TPL_VAR_UPDATEURL", $this->libFunc->m_safeUrl($cartUpdateUrl));

				$productUrl = SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $rowCart[$i]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_PRODUCTURL", $this->libFunc->m_safeUrl($productUrl));

				$this->ObTpl->parse("cartproduct_blk", "TPL_VAR_CARTPRODUCTS", true);
			}
			#**********************END PRODUCT DISPLAY**********************
			$this->ObTpl->set_var("TPL_VAR_NOVATTOTAL", $novattotal);
			#*********Start: Check if shipping estimates dropdown required.*****************
			$_SESSION['postagedropdown'] = "";
			for ($i = 0; $i < $rowCartCount; $i++) {
				if ($rowCart[$i]->iFreeShip == "0" && DEFAULT_POSTAGE_METHOD=='zones') {
                    $_SESSION['postagedropdown'] = "1";
                } elseif ($rowCart[$i]->iFreeShip == "0" && DEFAULT_POSTAGE_METHOD=='cities') {
                    $_SESSION['postagedropdown'] = "1";
                }
			}
			if($_SESSION['postagedropdown'] != "1"){
				$_SESSION['postagePrice'] = "";
				$_SESSION['zoneSpecialDelivery'] = "";
			}
			if($_SESSION['postagedropdown'] == "1"){
				$this->ObTpl->parse("postagedropdown_blk", "TPL_VAR_POSTAGEDROPDOWN");
			}
			#*********End: Check if shipping estimates dropdown required.*****************
			#******************TO CHECK MEMBER POINT ENABLE******************
			if (OFFERMPOINT == 1) {
				$this->memPoints = MPOINTCALCULATION * $this->subTotal;
				$this->ObTpl->set_var("TPL_VAR_MPOINTS", floor($this->memPoints));
				if(isset($_SESSION['userid']) && $_SESSION['userid'] !=0)
				{
					$this->obDb->query = "SELECT fMemberPoints FROM ".CUSTOMERS." WHERE iCustmerid_PK  ='".$_SESSION['userid']."'";
					$row_customer=$this->obDb->fetchQuery();
					$recordCount=$this->obDb->record_count;
					if($recordCount==1)
					{
						$mpoints = $row_customer[0]->fMemberPoints;
						$mptext = "You have ".$mpoints." ".LANG_REWARDPOINTS." saved up. Check to use them. <input type=\"checkbox\" onclick=\"updateMemPoints()\" value=\"yes\" id=\"memptsbox\" name=\"member_points\"/>";
						$this->ObTpl->set_var("TPL_VAR_LOGSTATUS","1");
					}
				}
				else
				{
					$mpoints = 0;
					$mptext = " Login to see how many ".LANG_REWARDPOINTS." you have and the option to use them.";
					$this->ObTpl->set_var("TPL_VAR_LOGSTATUS","0");
					}
				//<p class=\"note\"><input type=\"checkbox\" value=\"1\" name=\"member_points\"/></p>
				$this->ObTpl->set_var("TPL_VAR_MPOINTSR", "<span class=\"mpoints\">".$mptext."</span>");
				if($_SESSION['useMemberPoints'] == 'yes')
				{
					$this->ObTpl->set_var("TPL_VAR_SCRIPTMEMB","<script type=\"text/javascript\">document.getElementById('memptsbox').checked=true;</script>");
				}
				$this->ObTpl->parse("memberpoint_blk", "TPL_MPOINTS_BLK");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MPOINTSR","");
				$this->ObTpl->set_var("TPL_VAR_LOGSTATUS","0");
				$this->ObTpl->parse("memberpoint_blk","");
			}

			#**************************SUB TOTAL HERE**********					
			$this->ObTpl->set_var("TPL_VAR_SUBTOTAL", number_format($this->subTotal, 2, '.', ''));
			$this->grandTotal = $this->subTotal;
			$_SESSION['grandsubTotal'] = number_format($this->grandTotal, 2, '.', '');
			#************************* PROMOTION DISCOUNTS*********
			$this->promotionDiscount = $comFunc->m_calculatePromotionDiscount($this->subTotal);

			if ($this->promotionDiscount >= 0) {
				// Fix tev6.2: Vat charged must be subtotal subtracted from calculated discount.
				 if($this->promotionDiscount > 0){
					$this->taxTotal = $this->taxTotal - $this->promotionDiscount;
				 }
				if ($this->promotionDiscount == 0) {
					$displayDiscount = 'No Charge';
				} else {
					$displayDiscount = "-" . CONST_CURRENCY . number_format($this->promotionDiscount, 2, '.', '');
				}
				if (isset ($comFunc->PromotionDesc) && !$this->libFunc->m_isNull($comFunc->PromotionDesc)) {
					$this->ObTpl->set_var("TPL_VAR_PROMOTIONDESC", $comFunc->PromotionDesc);
				} else {
					$this->ObTpl->set_var("TPL_VAR_PROMOTIONDESC", "Promotion Discount");
				}

				$this->ObTpl->set_var("TPL_VAR_PDISCOUNTS", $displayDiscount);
				$_SESSION['promotionDiscountPrice'] = $this->promotionDiscount;
				$this->grandTotal -= $this->promotionDiscount;
				$this->ObTpl->parse("discounts_blk", "TPL_DISCOUNTS_BLK");
			} else {
				$_SESSION['promotionDiscountPrice'] = 0;
			}

			#VOLUME DISCOUNTS
			if ($this->volDiscount > 0) {
				$this->ObTpl->set_var("TPL_VAR_VOLDISCOUNT", number_format($this->volDiscount, 2, '.', ''));
				$this->grandTotal -= $this->volDiscount;
				$this->postageTotal -= $this->volDiscount;
				$this->taxTotal = $this->taxTotal - $this->volDiscount;//Recalculate VAT total based on grant total after discount.
				$this->ObTpl->parse("volDiscounts_blk", "TPL_VOLDISCOUNTS_BLK");
			}
			$this->ObTpl->set_var("LANG_VAR_DISCCODETXT",LANG_DISCOUNTCODETEXT);
			$this->ObTpl->set_var("LANG_VAR_DISCCERTTXT",LANG_DISCOUNTCERTTEXT);
			
			
			#DISCOUNT CODE DISCOUNTS
			if(isset($_SESSION['discountPrice']))
			{
			$this->discountPrice = $_SESSION['discountPrice'];
			}
			if(isset($this->discountPrice) && $this->discountPrice!=0 )
			{
			$this->minAmount = $_SESSION['discountMini'];
			$this->offertype = $_SESSION['discountType'];
				if($this->grandTotal > $this->minAmount)
				{
					if ($this->offertype =="percent") {
						$discountedPrice = round($this->discountPrice * (($this->grandTotal ) / 100),2);
					} elseif ($this->offertype =="fix"){
						if($this->discountPrice > $this->grandTotal)
						{
							$this->discountPrice = $this->grandTotal;
						}
						$discountedPrice = round($this->discountPrice,2);
					}
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTCODE","(".$_SESSION['discountCode'].")");
					$this->ObTpl->set_var("TPL_VAR_DISCOUNTPRICE",number_format($discountedPrice,2,'.',''));
									   
					if ($this->taxTotal > 0) {
						$this->taxTotal-=$discountedPrice;
						$this->grandTotal-=$discountedPrice;
					} else {
						$this->grandTotal-=$discountedPrice;
						//No VAT on order so do not adjust the VAT
					   
					}
					//$_SESSION['discountPrice']=$discountedPrice;
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
			
			#GIFT CERTIFICATE DISCOUNTS
			if(isset($_SESSION['giftCertPrice']) && isset($_SESSION['giftCertCode']))
			{
			$this->giftCertPrice = $_SESSION['giftCertPrice'];
			}
			if(isset($this->giftCertPrice) && $this->giftCertPrice!=0)
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
				$this->grandTotal-=$this->giftCertPrice;
				$_SESSION['giftCertPrice']=$this->giftCertPrice;
				$this->ObTpl->set_var("TPL_VAR_GIFTCODE","(".$_SESSION['giftCertCode'].")");				
				$this->ObTpl->set_var("TPL_VAR_GIFTCERTPRICE",number_format($this->giftCertPrice,2,'.',''));
				$this->ObTpl->parse("giftcert_blk","TPL_GIFTCERT_BLK");	
			}
			elseif(isset($_SESSION['giftCertCode']) && !empty($_SESSION['giftCertCode']) && $_SESSION['giftCertCode']!='gift certificate number')
			{
				$this->ObTpl->set_var("TPL_VAR_GIFTCODE","(".$_SESSION['giftCertCode'].") not found");
				$this->ObTpl->set_var("TPL_VAR_GIFTCERTPRICE","0.00");
				$this->ObTpl->parse("giftcert_blk","TPL_GIFTCERT_BLK");	
			}
			
			
			#CART WEIGHT
			if ($this->cartWeight > 0 && ISACTIVE_ITEMWEIGHT == 1) {
				$this->cartWeightPrice = $this->cartWeight * DEFAULT_ITEMWEIGHT;
				$this->ObTpl->set_var("TPL_VAR_WEIGHT", $this->cartWeight);
				$this->ObTpl->set_var("TPL_VAR_WEIGHTPRICE", number_format($this->cartWeightPrice, 2, '.', ''));

				if (VAT_POSTAGE_FLAG)
				$this->taxTotal += $this->cartWeightPrice; // locloc
				$this->grandTotal += $this->cartWeightPrice;

				$this->ObTpl->parse("cartWeight_blk", "TPL_CARTWEIGHT_BLK");
			}

			#ASSIGNING PRICE ,QTY FOR METHODS TO CALULATE ON TOTAL PRICE
			$comFunc->grandTotal = $this->postageTotal;
			$comFunc->totalQty = $this->totalQty;
			$comFunc->postageQty = $this->postageQty;

			if (!isset ($_SESSION['freeShip']) || $_SESSION['freeShip'] != 1) {
				#CHECK FOR PRODUCT CODES METHOD
				if ((!isset($this->postagePrice) || $this->postagePrice == 0) && $this->postageTotal > 0) {
					$this->postagePrice = $comFunc->m_postagePrice();
				}

				#POSTAGE VALUE IN SESSION
				$_SESSION['defPostageMethod'] = DEFAULT_POSTAGE_NAME;
				$_SESSION['defPostagePrice'] = $this->postagePrice;

				$this->ObTpl->set_var("TPL_VAR_POSTAGENAME", DEFAULT_POSTAGE_NAME);
				$this->ObTpl->set_var("TPL_VAR_POSTAGE",number_format($this->postagePrice,2,'.',''));

			
				$this->grandTotal += $this->postagePrice;
				$this->ObTpl->parse("postage_blk", "TPL_POSTAGE_BLK");
			}
				$temp = $comFunc->m_Calculate_Tax($this->taxTotal,$this->postagePrice,0,0);
				$this->vatTotal = $temp[0];
				$this->ObTpl->set_var("TPL_VAR_VAT", $temp[1]);
			
			if ($this->vatTotal > 0) {
				$this->ObTpl->set_var("TPL_VAR_VATPRICE", number_format($this->vatTotal, 2, '.', ''));
				$this->grandTotal += $this->vatTotal;
				$this->ObTpl->parse("vat_blk", "TPL_VAT_BLK");
			}
			$_SESSION['totalQty'] = $this->totalQty;
			$_SESSION['grandTotal'] = number_format($this->grandTotal, 2, '.', '');

			$this->ObTpl->set_var("TPL_VAR_CURRENTTOTAL", number_format($this->grandTotal, 2, '.', ''));

			//Checking for minimum order total
			if (MINORDERTOTAL > 0){
				
				if(number_format($this->grandTotal, 2, '.', '') > MINORDERTOTAL)
				{
					$this->ObTpl->parse("startCheckout_blk", "TPL_STARTCHECKOUT_BLK");
				}else
				{
					$this->ObTpl->set_var("TPL_VAR_MSG","<p class=\"message\">Your order total does not meet the minimum order total of ".CONST_CURRENCY.MINORDERTOTAL."  </p>");	
						
				}	
			}else
			{
				$this->ObTpl->parse("startCheckout_blk", "TPL_STARTCHECKOUT_BLK");
				
			}
			
			# DISPLAY THE NOTICE BOX FOR REFUND
			if (isset($_SESSION['INVOICE_EDITING']) && $_SESSION['INVOICE_EDITING'] != "") {
				$this->ObTpl->parse("return_blk", "TPL_REFUND_BLK");
			}
			$this->ObTpl->parse("cart_blk", "TPL_CART_BLK");
		} else {
			$_SESSION['totalQty'] = 0;
			$_SESSION['grandTotal'] = number_format(0, 2, '.', '');
			$returnUrl = SITE_URL;
			$this->ObTpl->set_var("TPL_VAR_MSG", MSG_CART_EMPTY . " <a href='" . $this->libFunc->m_safeUrl($returnUrl) . "'>" . MSG_RETURN . "</a>");
		}

		if ($this->checkout == 1) {	
			
			$retUrl = $this->libFunc->m_safeUrl(SITE_SAFEURL . "ecom/index.php?action=checkout.billing");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
		
		
		// Select postage start
	
		
			
			$this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEMETHOD",$_SESSION['defPostageMethod']);
			$this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",number_format($_SESSION['defPostagePrice'],2));
			//--
			if(DEFAULT_POSTAGE_METHOD=='zones')
            {
                $postagePrice=$_SESSION['defPostagePrice'];
                $this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE",number_format($postagePrice,2));
                $this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Special Delivery");
                
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEMETHOD",$_SESSION['defPostageMethod']);
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",$postagePrice);
                
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","1");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",$postagePrice);
                $this->ObTpl->parse("postageoptions_blk","TPL_POSTAGEOPTIONS_BLK");
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","2");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",number_format($postagePrice,2));
                $this->ObTpl->parse("postageoptions_blk","TPL_POSTAGEOPTIONS_BLK");
                                    
                $this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");            
                //$this->ObTpl->parse("special_postage_blk","TPL_SPECIALPOSTAGE_BLK");
                $this->ObTpl->parse("specialrate_blk","TPL_SPECIALRATE_BLK",true);
            }elseif(DEFAULT_POSTAGE_METHOD=='cities')
            {
                $postagePrice=$_SESSION['defPostagePrice'];
                $this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE",number_format($postagePrice,2));
                $this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD","Special Delivery");
                
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEMETHOD",$_SESSION['defPostageMethod']);
                $this->ObTpl->set_var("TPL_VAR_DEFAULT_POSTAGEPRICE",$postagePrice);
                
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","1");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",$postagePrice);
                $this->ObTpl->parse("postageoptions_blk","TPL_POSTAGEOPTIONS_BLK");
                
                $this->ObTpl->set_var("TPL_VAR_METHODID","2");
                $this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",number_format($postagePrice,2));
                $this->ObTpl->parse("postageoptions_blk","TPL_POSTAGEOPTIONS_BLK");
                                    
                $this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");            
                //$this->ObTpl->parse("special_postage_blk","TPL_SPECIALPOSTAGE_BLK");
                $this->ObTpl->parse("specialrate_blk","TPL_SPECIALRATE_BLK",true);
			}
			//--
			#IF SPECIAL POSTAGE IS NOT ENABLED THE DEFAULT POSTAGE OPTION WILL BE DISPLAYED 
			#OTHERWISE DEFAULT RATES WILL BE ADDED TO SPECIAL
			if(!SPECIAL_POSTAGE){
				$this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");
			}else{
				$this->ObTpl->parse("default_postage_blk","TPL_DEFAULTPOSTAGE_BLK");
                $this->ObTpl->parse("special_postage_blk","TPL_SPECIALPOSTAGE_BLK");
			}
	
			$this->obDb->query ="SELECT vField1,vField2,iPostDescId_PK,PD.vDescription FROM  ".POSTAGE." P,".POSTAGEDETAILS." PD WHERE iPostId_PK=iPostId_FK AND vKey='special' AND iStatus='1'";
			$rsPostage=$this->obDb->fetchQuery();
			$rsCount=$this->obDb->record_count;
			if($rsCount>0 && SPECIAL_POSTAGE)
			{
				for($j=0;$j<$rsCount;$j++)
				{
					$this->ObTpl->set_var("TPL_VAR_METHODID",$rsPostage[$j]->iPostDescId_PK);
					$this->ObTpl->set_var("TPL_VAR_POSTAGEMETHOD",$rsPostage[$j]->vDescription);
					#REASON FOR SUBTRACT 1 is additional after first 
					$addtional=$_SESSION['totalQty']-1;
					if($addtional>0)
					{
						$postagePrice=$rsPostage[$j]->vField1+($rsPostage[$j]->vField2*$addtional);
					}
					else
					{
						$postagePrice=$rsPostage[$j]->vField1;
					}
					$this->ObTpl->set_var("TPL_VAR_DISPLAYPRICE",number_format($postagePrice,2));
					if(SPECIAL_POSTAGE){
						$this->ObTpl->set_var("TPL_VAR_SPECIAL_POSTAGEPRICE",$rsPostage[$j]->vField2);
						$postagePrice=$postagePrice+$_SESSION['defPostagePrice'];
					}
					$this->ObTpl->set_var("TPL_VAR_POSTAGEPRICE",$postagePrice);
					$this->ObTpl->parse("postageoptions_blk","TPL_POSTAGEOPTIONS_BLK",true);
				}
			}else			
			if($_SESSION['zoneSpecialDelivery']==0 || !SPECIAL_POSTAGE)
			{
				$_SESSION['postageId']='0';
				$_SESSION['postageMethod']=$_SESSION['defPostageMethod'];
				$_SESSION['postagePrice']=$_SESSION['defPostagePrice'];
				$this->ObTpl->set_var("postage_blk","");	
			}
		$this->ObTpl->parse("specialrate_blk","TPL_SPECIALRATE_BLK");
// End Select postage
		
		
		
		
		return ($this->ObTpl->parse("return", "TPL_DETAILS_FILE"));
	} #END CART DISPLAY

	#FUNCTION TO DISPLAY GIFT WRAP
	function m_dspGiftWrap() {

		$libFunc = new c_libFunctions();

		$this->ObTpl = new template();
		$this->ObTpl->set_file("TPL_GIFTWRAP_FILE", $this->giftTemplate);
		$this->ObTpl->set_block("TPL_GIFTWRAP_FILE", "TPL_GIFTWRAP_BLK", "giftwrap_blk");
		$this->ObTpl->set_block("TPL_GIFTWRAP_BLK", "TPL_LARGEIMG_BLK", "largeimg_blk");

		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY", CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_MSG", "");
		#INTAILAIZING
		$this->ObTpl->set_var("giftwrap_blk", "");
		$this->ObTpl->set_var("largeimg_blk", "");
		$cartUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->ObTpl->set_var("TPL_VAR_CARTURL", $cartUrl);

		if (isset ($this->request['msg']) && $this->request['msg'] == 1) {
			$this->ObTpl->set_var("TPL_VAR_MSG", MSG_GIFTWRAP_SUCCESS);
		}

		$this->obDb->query = "SELECT vTitle,vImage,vDescription,fPrice,vImageLarge,iGiftwrapid_PK FROM " . GIFTWRAPS . " WHERE  iState='1' ORDER BY iSort";
		$rowGiftWrap = $this->obDb->fetchQuery();
		$rowCartCount = $this->obDb->record_count;
		if ($rowCartCount > 0 && !isset ($this->request['msg']) && isset ($this->request['mode']) && !$this->libFunc->m_isNull($this->request['mode']) && ENABLE_GIFTWRAP == 1) {
			for ($i = 0; $i < $rowCartCount; $i++) {
				$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($rowGiftWrap[$i]->vTitle));
				$this->ObTpl->set_var("TPL_VAR_DESCRIPTION", $this->libFunc->m_displayContent($rowGiftWrap[$i]->vDescription));
				$this->ObTpl->set_var("TPL_VAR_PRICE", number_format($rowGiftWrap[$i]->fPrice, 2, '.', ''));
				#DISPLAY IMAGE FOR SELECTED PRODUCT
				if (!$this->libFunc->m_isNull($rowGiftWrap[$i]->vImage)) {
					$img = $this->libFunc->m_checkFile($rowGiftWrap[$i]->vImage, "giftwrap", $this->libFunc->m_displayContent($rowGiftWrap[$i]->vTitle));
					if ($img) {
						$this->ObTpl->set_var("TPL_VAR_IMAGE", $img);
					} else {
						$this->ObTpl->set_var("TPL_VAR_IMAGE", MSG_NOIMG);
					}
				} else {
					$this->ObTpl->set_var("TPL_VAR_IMAGE", MSG_NOIMG);
				} #END IMAGE LOOP

				##CHECK FOR LARGE IMAGE LINK
				if (!$this->libFunc->m_isNull($rowGiftWrap[0]->vImageLarge)) {
					$img = $this->libFunc->m_checkFile($rowGiftWrap[$i]->vImageLarge, "giftwrap", $this->libFunc->m_displayContent($rowGiftWrap[$i]->vTitle));
					if ($img) {
						$largeImgUrl = SITE_URL . "ecom/index.php?action=ecom.largeImg&mode=" . $rowGiftWrap[$i]->iGiftwrapid_PK . "&type=gift";
						$this->ObTpl->set_var("TPL_VAR_LARGEIMGLINK", $this->libFunc->m_safeUrl($largeImgUrl));
						$this->ObTpl->parse("largeimg_blk", "TPL_LARGEIMG_BLK");
					}
				} #END LARGE IMAGE IF LOOP

				##ADD GIFT URL
				$addUrl = SITE_URL . "ecom/index.php?action=ecom.giftAdd&mode=" . $this->request['mode'] . "&id=" . $rowGiftWrap[$i]->iGiftwrapid_PK;
				$this->ObTpl->set_var("TPL_VAR_ADDURL", $this->libFunc->m_safeUrl($addUrl));
				$this->ObTpl->parse("giftwrap_blk", "TPL_GIFTWRAP_BLK", true);
			} #END FOR LOOP
		} #END IF LOOP

		return ($this->ObTpl->parse("return", "TPL_GIFTWRAP_FILE"));
	} #END FUNCTION m_dspGiftWrap

	#FUNCTION TO DIPSLAY BACKORDER INSTRUCTIONS
	function m_dspBackOrderInstructions() {
		$this->ObTpl = new template();
		$this->ObTpl->set_file("TPL_BACKORDER_FILE", $this->giftTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY", CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_MSG", "");
		$this->request['mode'] = $this->libFunc->ifSet($this->request, 'mode', "");
		#FLAG TO INDICATE SEPERATE BACKORDER AND NORMAL ORDER
		$_SESSION['backOrderSeperate'] = $this->libFunc->ifSet($_SESSION, 'backOrderSeperate', '0');

		#FLAG TO INDICATE WHETHER PROCESSING BACKORDER OR NOT
		$_SESSION['backOrderProcess'] = $this->libFunc->ifSet($_SESSION, 'backOrderProcess', '0');
		if ($this->request['mode'] == "") {
			$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
		$this->obDb->query = "SELECT count(*) as cnt ";
		$this->obDb->query .= " FROM " . TEMPCART . " AS T," . PRODUCTS . " AS P";
		$this->obDb->query .= " WHERE (iProdId_FK=iProdId_PK AND  vSessionId='" . SESSIONID . "') ";
		$this->obDb->query .= " AND T.iBackOrder=1";
		$rowCart = $this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_QTY", $rowCart[0]->cnt);
		#INTAILAIZING
		$backAllUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.backall");
		$this->ObTpl->set_var("TPL_VAR_ALLITEM", $backAllUrl);
		$backItemUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.backitem&mode=" . $this->request['mode']);
		$this->ObTpl->set_var("TPL_VAR_BACKITEM", $backItemUrl);
		$backRemoveUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.backremove&mode=" . $this->request['mode']);
		$this->ObTpl->set_var("TPL_VAR_REMOVEURL", $backRemoveUrl);

		return ($this->ObTpl->parse("return", "TPL_BACKORDER_FILE"));
	} #END FUNCTION m_dspBackOrderInstructions

} #END  CLASS
?>
