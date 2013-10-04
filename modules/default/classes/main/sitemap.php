<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_siteMap
{
#CONSTRUCTOR
	function c_siteMap()
	{
		$this->pageTplPath=THEMEPATH."default/templates/main/";
		$this->largeImage="largeImage.tpl.htm";
		$this->pageTplFile="pager.tpl.htm";
	}

	#FUNCTION TO DISPLAY SITEMAP
	function m_showSitemap()
	{
		$libFunc=new c_libFunctions();
		if(!isset($this->request['mode']))
		{
			$this->request['mode']=0;
		}
		
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SITEMAP_FILE",$this->siteMapTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$siteMapUrl=SITE_URL."index.php?action=sitemap";
		$this->ObTpl->set_var("TPL_VAR_SITEMAPURL",$libFunc->m_safeUrl($siteMapUrl));

		#SETTING TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_SITEMAP_FILE","TPL_MAINDEPARTMENT_BLK","maindept_blk");

		$this->ObTpl->set_block("TPL_MAINDEPARTMENT_BLK","TPL_DEPARTMENT_BLK","dept_blk");

		$this->ObTpl->set_block("TPL_DEPARTMENT_BLK","TPL_MAINSUBDEPT_BLK","mainSubDept_blk");
			$this->ObTpl->set_block("TPL_MAINSUBDEPT_BLK","TPL_SUBDEPT_BLK","subDept_blk");

		$this->ObTpl->set_block("TPL_DEPARTMENT_BLK","TPL_MAINSUBPRODUCT_BLK","mainSubProduct_blk");
		$this->ObTpl->set_block("TPL_MAINSUBPRODUCT_BLK","TPL_SUBPRODUCT_BLK","subProduct_blk");
		$this->ObTpl->set_block("TPL_DEPARTMENT_BLK","TPL_MAINSUBCONTENT_BLK","mainSubContent_blk");
		$this->ObTpl->set_block("TPL_MAINSUBCONTENT_BLK","TPL_SUBCONTENT_BLK","subContent_blk");

		$this->ObTpl->set_block("TPL_SITEMAP_FILE","TPL_MENU_BLK","menu_blk");
		$this->ObTpl->set_block("TPL_MENU_BLK","TPL_MENUITEM_BLK","menuitem_blk");
		$this->ObTpl->set_block("TPL_SITEMAP_FILE","TPL_MAINCONTENT_BLK","mainContent_blk");
		$this->ObTpl->set_block("TPL_MAINCONTENT_BLK","TPL_CONTENT_BLK","content_blk");
		#INTIALIZING 
		$this->ObTpl->set_var("maindept_blk","");
		$this->ObTpl->set_var("dept_blk","");
		$this->ObTpl->set_var("menu_blk","");
		$this->ObTpl->set_var("menuitem_blk","");
		$this->ObTpl->set_var("mainContent_blk","");
		$this->ObTpl->set_var("content_blk","");

		 #QUERY TO GET DEPARTMENTS UNDER SELECTED DEPARTMENT
		$this->obDb->query = "SELECT vTitle,vSeoTitle,iDeptid_PK  FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE (iDeptid_PK=iSubId_FK AND vtype='department' AND iOwner_FK='0' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
		$rowDept=$this->obDb->fetchQuery();
		$deptCount=$this->obDb->record_count;

		if($deptCount>0)
		{
			for($i=0;$i<$deptCount;$i++)
			{
				$deptUrl=SITE_URL."index.php?action=sitemap&mode=".$rowDept[$i]->vSeoTitle;
				
				$count = 0;								
				$this->ObTpl->set_var("mainSubDept_blk","");
				$this->ObTpl->set_var("subDept_blk","");
				$this->ObTpl->set_var("mainSubProduct_blk","");
				$this->ObTpl->set_var("subProduct_blk","");

				$this->ObTpl->set_var("mainSubContent_blk","");
				$this->ObTpl->set_var("subContent_blk","");
				
				# 15/02/2008 TO CHECK IF THERE ARE ANY PRODUCTS - DEPARTMENTS - CONTENTS UNDER DEPARTMENT
				
				$this->obDb->query = "SELECT vTitle,vSeoTitle,iDeptid_PK  FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE (iDeptid_PK=iSubId_FK AND vtype='department' AND iOwner_FK='".$rowDept[$i]->iDeptid_PK."' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
						$rowDepartment=$this->obDb->fetchQuery();
						$departmentCount=$this->obDb->record_count;
						if ($departmentCount > 0) $count++;
				
				$this->obDb->query = "SELECT vTitle,vSeoTitle FROM ".PRODUCTS." D, ".FUSIONS." F WHERE (iProdid_PK=iSubId_FK AND vtype='product' AND iOwner_FK='".$rowDept[$i]->iDeptid_PK."' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
						$rowProduct=$this->obDb->fetchQuery();
						$productCount=$this->obDb->record_count;
						if ($productCount > 0) $count++;
				
				$this->obDb->query = "SELECT vTitle,vSeoTitle FROM ".CONTENTS." D, ".FUSIONS." F WHERE (iContentid_PK=iSubId_FK AND vtype='content' AND iOwner_FK='".$rowDept[$i]->iDeptid_PK."' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
						$rowContent=$this->obDb->fetchQuery();
						$contentCount=$this->obDb->record_count;
						if ($contentCount > 0) $count++;
												
				if ($count == 0){
					$deptUrl=SITE_URL."ecom/index.php?action=ecom.details&mode=".$rowDept[$i]->vSeoTitle;
				}
								
				$this->ObTpl->set_var("TPL_VAR_DEPTURL",$libFunc->m_safeUrl($deptUrl));
				$this->ObTpl->set_var("TPL_VAR_DEPTTITLE",$libFunc->m_displayContent($rowDept[$i]->vTitle));
				
				
						
				#SUB ITEMS
				if(!empty($this->request['mode'])  && $rowDept[$i]->vSeoTitle==$this->request['mode'])
				{

					#TO GET OWNER
					$this->obDb->query = "SELECT iDeptid_PK FROM ".DEPARTMENTS." WHERE vSeoTitle='".$this->request['mode']."'";
					$rsDeptID = $this->obDb->fetchQuery();
					$modeCount=$this->obDb->record_count;
					if($modeCount>0)
					{
												
						 #QUERY TO GET DEPARTMENTS UNDER SELECTED DEPT
						$this->obDb->query = "SELECT vTitle,vSeoTitle,iDeptid_PK  FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE (iDeptid_PK=iSubId_FK AND vtype='department' AND iOwner_FK='".$rsDeptID[0]->iDeptid_PK."' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
						$rowDepartment=$this->obDb->fetchQuery();
						$departmentCount=$this->obDb->record_count;

						if($departmentCount>0)
						{			
							for($d=0;$d<$departmentCount;$d++)
							{
							 	$productUrl=SITE_URL."ecom/index.php?action=ecom.details&mode=".$rowDepartment[$d]->vSeoTitle;
								$this->ObTpl->set_var("TPL_VAR_SUBDEPURL",$libFunc->m_safeUrl($productUrl));
								$this->ObTpl->set_var("TPL_VAR_SUBDEPTTITLE",$libFunc->m_displayContent($rowDepartment[$d]->vTitle));	
								$this->ObTpl->parse("subDept_blk","TPL_SUBDEPT_BLK",true);	
							}
							$this->ObTpl->parse("mainSubDept_blk","TPL_MAINSUBDEPT_BLK");	
						}
						 #QUERY TO GET PRODUCTS UNDER SELECTED DEPT
						$this->obDb->query = "SELECT vTitle,vSeoTitle FROM ".PRODUCTS." D, ".FUSIONS." F WHERE (iProdid_PK=iSubId_FK AND vtype='product' AND iOwner_FK='".$rsDeptID[0]->iDeptid_PK."' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
						$rowProduct=$this->obDb->fetchQuery();
						$productCount=$this->obDb->record_count;

						if($productCount>0)
						{						
							for($p=0;$p<$productCount;$p++)
							{
								 $productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowProduct[$p]->vSeoTitle;
								$this->ObTpl->set_var("TPL_VAR_SUBPRODUCTURL",$libFunc->m_safeUrl($productUrl));
								$this->ObTpl->set_var("TPL_VAR_SUBPRODUCTTITLE",$libFunc->m_displayContent($rowProduct[$p]->vTitle));	
								$this->ObTpl->parse("subProduct_blk","TPL_SUBPRODUCT_BLK",true);	
							}
							$this->ObTpl->parse("mainSubProduct_blk","TPL_MAINSUBPRODUCT_BLK");	
						}
						

						 #QUERY TO GET CONTENTS UNDER SELECTED DEPT
						$this->obDb->query = "SELECT vTitle,vSeoTitle FROM ".CONTENTS." D, ".FUSIONS." F WHERE (iContentid_PK=iSubId_FK AND vtype='content' AND iOwner_FK='".$rsDeptID[0]->iDeptid_PK."' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
						$rowContent=$this->obDb->fetchQuery();
						$contentCount=$this->obDb->record_count;

						if($contentCount>0)
						{							
							for($c=0;$c<$contentCount;$c++)
							{
								 $contentUrl=SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$rowContent[$c]->vSeoTitle;
								$this->ObTpl->set_var("TPL_VAR_SUBCONTENTURL",$libFunc->m_safeUrl($contentUrl));
								$this->ObTpl->set_var("TPL_VAR_SUBCONTENTTITLE",$libFunc->m_displayContent($rowContent[$c]->vTitle));	
								$this->ObTpl->parse("subContent_blk","TPL_SUBCONTENT_BLK",true);	
							}
							$this->ObTpl->parse("mainSubContent_blk","TPL_MAINSUBCONTENT_BLK");	
						}#END CONTENT COUNT

					}#END MODE COUNT					

				}#END MODE CHECK
				
				$this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);	
			}
			$this->ObTpl->parse("maindept_blk","TPL_MAINDEPARTMENT_BLK");	
		}
		
		#QUERY TO GET MENU ITEMS
			#QUERY MENU TABLE
		$this->obDb->query= "SELECT iHeaderid_PK,vHeader,vImage  FROM ".MENUHEADERS." WHERE iState='1' ORDER BY iSort";
		$rsMenuHead = $this->obDb->fetchQuery();
		$rsMenuHeadCount=$this->obDb->record_count;
		
		if($rsMenuHeadCount>0)
		{
			for($i=0;$i<$rsMenuHeadCount;$i++)
			{
				$this->obDb->query= "SELECT iMenuItemsId,vItemtitle,vLink,vHrefAttributes,vImage   FROM ".MENUITEMS." WHERE iState='1' AND iHeaderid_FK='".$rsMenuHead[$i]->iHeaderid_PK."' ORDER BY iSort";
				$rsMenu = $this->obDb->fetchQuery();
				$rsMenuCount=$this->obDb->record_count;
				if($rsMenuCount>0)
				{
					$this->ObTpl->set_var("menuitem_blk","");
					for($j=0;$j<$rsMenuCount;$j++)
					{
						$this->ObTpl->set_var("TPL_VAR_MENUTITLE",$libFunc->m_displayContent($rsMenu[$j]->vItemtitle))	;$this->ObTpl->set_var("TPL_VAR_MENUURL",$libFunc->m_displayContent($rsMenu[$j]->vLink));
						$this->ObTpl->set_var("TPL_VAR_HREFATTRIBUTES",$libFunc->m_displayContent($rsMenu[$j]->vHrefAttributes));
						$this->ObTpl->parse("menuitem_blk","TPL_MENUITEM_BLK",true);
					}
					$this->ObTpl->set_var("TPL_VAR_MENUHEAD",$libFunc->m_displayContent($rsMenuHead[$i]->vHeader));
					$this->ObTpl->parse("menu_blk","TPL_MENU_BLK",true);
				}
				else
				{
					$this->ObTpl->set_var("menuitem_blk","");
				}			
			}
		}


		 #QUERY TO GET CONTENTS UNDER SELECTED HOMEPAGE
		$this->obDb->query = "SELECT vTitle,vSeoTitle FROM ".CONTENTS." D, ".FUSIONS." F WHERE (iContentid_PK=iSubId_FK AND vtype='content' AND iOwner_FK='0' AND vOwnerType='department' AND iState='1') ORDER BY iSort";
		$rowContent=$this->obDb->fetchQuery();
		 $contentCount=$this->obDb->record_count;

		if($contentCount>0)
		{
			for($c=0;$c<$contentCount;$c++)
			{
				 $contentUrl=SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$rowContent[$c]->vSeoTitle;
				$this->ObTpl->set_var("TPL_VAR_CONTENTURL",$libFunc->m_safeUrl($contentUrl));
				$this->ObTpl->set_var("TPL_VAR_CONTENTTITLE",$libFunc->m_displayContent($rowContent[$c]->vTitle));	
				$this->ObTpl->parse("content_blk","TPL_CONTENT_BLK",true);	
			}
			$this->ObTpl->parse("mainContent_blk","TPL_MAINCONTENT_BLK");	
		}
		return($this->ObTpl->parse("return","TPL_SITEMAP_FILE"));
	}#END DEPARTMENT DISPLAY
}#END  CLASS
?>