<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_search
{
#CONSTRUCTOR
	function c_search()
	{
		$this->pageTplPath=MODULES_PATH."default/templates/main/";
		$this->largeImage="largeImage.tpl.htm";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize=15;
		$this->totalRecords=0;
		$this->libFunc=new c_libFunctions();
		$this->noPaging = 0; #PAGING ACTIVE
	}


	#FUNCTION TO DISPLAY SEARCH PAGE
	function m_searchResults()
	{
		if($this->request['adv'] != ""){
			$_SESSION['adv'] = $this->request['adv'];
		}
		if($_SESSION['adv'] == ""){
			$_SESSION['adv'] = "SearchAll";	
		}
		if(empty($this->request['mode']))
		{
			$this->request['mode']="";
		}else{
			$tempmode = explode(" ",$this->request['mode']);
			$productSearch = "";
			foreach($tempmode as $k => $v)
			{
				if($k == 0)
				{
					$productSearch = $productSearch . "(SELECT iProdid_PK as id,vTitle,vSeoTitle,tShortDescription,vImage1, 'product' FROM ".PRODUCTS." INNER JOIN ".FUSIONS." ON iProdid_PK=iSubId_FK WHERE (vTitle LIKE '%".$v."%' OR ";
					$productSearch .="tShortDescription  LIKE '%".$v."%' OR ";
					$productSearch .="vSku  LIKE '%".$v."%' OR ";
					$productSearch .="tContent LIKE '%".$v."%')";
				}
				else
				{
					$productSearch = $productSearch . " AND (vTitle LIKE '%".$v."%' OR ";
					$productSearch .="tShortDescription  LIKE '%".$v."%' OR ";
					$productSearch .="vSku  LIKE '%".$v."%' OR ";
					$productSearch .="tContent LIKE '%".$v."%')";
				}
			}
			$productSearch .=" AND vType='product' AND iState='1')";
			if($_SESSION['adv']=="SearchAll")
			{	
				$productSearch .=" UNION";
			}
			$this->request['mode']=addslashes($this->request['mode']);
		}
	
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_SEARCH_FILE",$this->searchTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		$this->ObTpl->set_var("TPL_VAR_GRAPHICSURL",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);	
		
		#SETTING TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_SEARCH_FILE","TPL_DEPARTMENT_BLK","dept_blk");
		$this->ObTpl->set_block("TPL_SEARCH_FILE","TPL_PRODUCT_BLK","product_blk");
		$this->ObTpl->set_block("TPL_SEARCH_FILE","TPL_CONTENT_BLK","content_blk");

		#INTIALIZING 
		$this->ObTpl->set_var("dept_blk","");
		$this->ObTpl->set_var("product_blk","");
		$this->ObTpl->set_var("content_blk","");

	
	##START OF ADVANCED SEARCH 
	if($_SESSION['adv']=="SearchAll")
	{		
			#TO QUERY DEPARTMENT TABLE
			$extraStr = "ecom/index.php?action=ecom.search&mode=".$this->request['mode'] ;
			$query = "(SELECT iDeptid_PK as id,vTitle,vSeoTitle,tShortDescription,'' as vImage1, 'department' as flag FROM ".DEPARTMENTS." INNER JOIN ".FUSIONS." ON iDeptid_PK=iSubId_FK WHERE ";
			$query .="(vTitle LIKE '%".$this->request['mode']."%' OR ";
	 		$query .="tShortDescription  LIKE '%".$this->request['mode']."%' OR ";
	 		$query .="tContent LIKE '%".$this->request['mode']."%') AND vType='department' AND iState='1') UNION";

			$query .= $productSearch;


			$query .= "(SELECT iContentid_PK as id,vTitle,vSeoTitle,tShortDescription,'' as vImage1, 'content' FROM ".CONTENTS." INNER JOIN ".FUSIONS." ON iContentid_PK=iSubId_FK WHERE ";
			$query .="(vTitle LIKE '%".$this->request['mode']."%' OR ";
	 		$query .="tShortDescription  LIKE '%".$this->request['mode']."%' OR ";
	 		$query .="tContent LIKE '%".$this->request['mode']."%') AND vType='content' AND iState='1')";

			$pn = new PrevNext($this->pageTplPath, $this->pageTplFile, $this->obDb);
			$pn->formno = 1;
			 
			$navArr = $pn->create($query, $this->pageSize, $extraStr, $this->noPaging);
			$rowDept = $navArr['qryRes'];
			$totalRecords = $navArr['totalRecs'];
			$deptCount = $navArr['fetchedRecords'];
			$this->totalRecords = $totalRecords;
	
			if($deptCount>0)
			{
				for($i=0;$i<$deptCount;$i++)
				{
					$this->obDb->query = "SELECT count(*) as cnt FROM ".FUSIONS." WHERE iSubId_FK='".$rowDept[$i]->id."' AND iState='1'  AND vType='".$rowDept[$i]->flag."'";
					 $rowCnt = $this->obDb->fetchQuery();
					if($rowCnt[0]->cnt>0)
					{
						if($rowDept[$i]->flag == "department"){
						$deptUrl=SITE_URL."ecom/index.php?action=ecom.details&mode=".$rowDept[$i]->vSeoTitle;
						}elseif($rowDept[$i]->flag == "product"){
						$deptUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowDept[$i]->vSeoTitle;
						}elseif($rowDept[$i]->flag == "content"){
						$deptUrl=SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$rowDept[$i]->vSeoTitle;
						} 
						$this->ObTpl->set_var("TPL_VAR_DEPTURL",$this->libFunc->m_safeUrl($deptUrl));
						$this->ObTpl->set_var("TPL_VAR_DEPTNAME",$this->libFunc->m_displayContent($rowDept[$i]->vTitle));	
						if($rowDept[$i]->tShortDescription !="")
						{
						$shortDesc=substr($this->libFunc->m_displayContent($rowDept[$i]->tShortDescription),0,100);
						$this->ObTpl->set_var("TPL_VAR_SHORTDESC","<p>".$shortDesc."</p>");	
						}else
						{
							$this->ObTpl->set_var("TPL_VAR_SHORTDESC","");		
						}
						if($rowDept[$i]->flag == "department"){
						$this->ObTpl->set_var("TPL_VAR_FLAG","Department");
						}elseif($rowDept[$i]->flag == "product"){
						$this->ObTpl->set_var("TPL_VAR_FLAG","Product");
						}elseif($rowDept[$i]->flag == "content"){
						$this->ObTpl->set_var("TPL_VAR_FLAG","Content");
						} 
						
						$this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);	
					}
					else
					{
						$this->totalRecords--;
					}
				}
			}

			if ($totalRecords > $this->pageSize) {
			#PAGINATION
			$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
		} else {
			$this->ObTpl->set_var("PagerBlock1", "");
		}
					
	}elseif($_SESSION['adv']=="products")
	{

		#TO QUERY PRODUCT TABLE 
		 $extraStr = "ecom/index.php?action=ecom.search&mode=".$this->request['mode'] ;
         $query = $productSearch;

				//$query .= "(SELECT iProdid_PK as id,vTitle,vSeoTitle,tShortDescription, 'product' FROM ".PRODUCTS." INNER JOIN ".FUSIONS." ON iProdid_PK=iSubId_FK WHERE ";
				//$query .="(vTitle LIKE '%".$this->request['mode']."%' OR ";
				//$query .="tShortDescription  LIKE '%".$this->request['mode']."%' OR ";
				//$query .="vSku  LIKE '%".$this->request['mode']."%' OR ";
				//$query .="tContent LIKE '%".$this->request['mode']."%') AND vType='product' AND iState='1') UNION";

		 $pn = new PrevNext($this->pageTplPath, $this->pageTplFile, $this->obDb);
		 $pn->formno = 1;
		 
		 $navArr = $pn->create($query, $this->pageSize, $extraStr, $this->noPaging);
		 $rowProduct = $navArr['qryRes'];
		 $totalRecords = $navArr['totalRecs'];
		 $productCount = $navArr['fetchedRecords'];
		 $this->totalRecords = $totalRecords;

        if($productCount>0) 
        { 
            for($i=0;$i<$productCount;$i++) 
            { 
                $this->obDb->query = "SELECT count(*) as cnt FROM ".FUSIONS." WHERE iSubId_FK='".$rowProduct[$i]->id."' AND iState='1' AND vType='product'"; 
                $rowCnt = $this->obDb->fetchQuery(); 
                if($rowCnt[0]->cnt>0) 
                { 
                    $prodUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rowProduct[$i]->vSeoTitle; 
                    $this->ObTpl->set_var("TPL_VAR_PRODUCTURL",$this->libFunc->m_safeUrl($prodUrl)); 
                    $this->ObTpl->set_var("TPL_VAR_PRODUCTNAME",$this->libFunc->m_displayContent($rowProduct[$i]->vTitle));     
                    if ($this->libFunc->m_displayContent($rowProduct[$i]->vImage1) != "") {
						$this->ObTpl->set_var("TPL_VAR_PRODIMAGE_TAG","<img src=\"" . SITE_URL . "libs/timthumb.php?src=/images/product/" . $this->libFunc->m_displayContent($rowProduct[$i]->vImage1) . "&amp;h=40&amp;w=40&amp;zc=r\" alt=\"" . $this->libFunc->m_displayContent($rowProduct[$i]->vTitle) . "\" /> ");
						 } else {
						 	$this->ObTpl->set_var("TPL_VAR_PRODIMAGE_TAG","");
						 }
                    if($rowProduct[$i]->tShortDescription != ""){
	                    $shortDesc=substr($this->libFunc->m_displayContent($rowProduct[$i]->tShortDescription),0,100); 
	                    $this->ObTpl->set_var("TPL_VAR_SHORTDESC","<p>".$shortDesc."</p>");  
                    }else
                    {
                    	$this->ObTpl->set_var("TPL_VAR_SHORTDESC","");  
                    }   
                    $this->ObTpl->parse("product_blk","TPL_PRODUCT_BLK",true);     
                } 
                else 
                { 
                    $this->totalRecords--; 
                } 
				$this->ObTpl->set_var("TPL_VAR_FLAG","Product");
            } 
        } 

		if ($totalRecords > $this->pageSize) {
			#PAGINATION
			$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
		} else {
			$this->ObTpl->set_var("PagerBlock1", "");
		}
	}
	elseif($_SESSION['adv']=="department"){
				#TO QUERY DEPARTMENT TABLE
				$extraStr = "ecom/index.php?action=ecom.search&mode=".$this->request['mode'] ;
                 $query = "SELECT iDeptid_PK,vTitle,vSeoTitle,tShortDescription FROM ".DEPARTMENTS." INNER JOIN ".FUSIONS." ON iDeptid_PK=iSubId_FK WHERE ";
                 $query .="(vTitle LIKE '%".$this->request['mode']."%' OR ";
                 $query .="tShortDescription  LIKE '%".$this->request['mode']."%' OR ";
                 $query .="tContent LIKE '%".$this->request['mode']."%') AND vType='department' AND iState='1'";
				 $pn = new PrevNext($this->pageTplPath, $this->pageTplFile, $this->obDb);
				 $pn->formno = 1;
				 
				 $navArr = $pn->create($query, $this->pageSize, $extraStr, $this->noPaging);
				 $rowDept = $navArr['qryRes'];
				 $totalRecords = $navArr['totalRecs'];
				 $deptCount = $navArr['fetchedRecords'];
				 $this->totalRecords = $totalRecords;
                
                if($deptCount>0)
                {
                        for($i=0;$i<$deptCount;$i++)
                        {
                                $this->obDb->query = "SELECT count(*) as cnt FROM ".FUSIONS." WHERE iSubId_FK='".$rowDept[$i]->iDeptid_PK."' AND iState='1'  AND vType='department'";
                                 $rowCnt = $this->obDb->fetchQuery();
                                if($rowCnt[0]->cnt>0)
                                {
                                        $deptUrl=SITE_URL."ecom/index.php?action=ecom.details&mode=".$rowDept[$i]->vSeoTitle;
                                        $this->ObTpl->set_var("TPL_VAR_DEPTURL",$this->libFunc->m_safeUrl($deptUrl));
                                        $this->ObTpl->set_var("TPL_VAR_DEPTNAME",$this->libFunc->m_displayContent($rowDept[$i]->vTitle));
                                        if($rowDept[$i]->tShortDescription != ""){
                                        	$shortDesc=substr($this->libFunc->m_displayContent($rowDept[$i]->tShortDescription),0,100);
                                        	$this->ObTpl->set_var("TPL_VAR_SHORTDESC","<p>".$shortDesc."</p>");
                                        }else{
                                        	$this->ObTpl->set_var("TPL_VAR_SHORTDESC","");	
                                        }
                                        $this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);
                                }
                                else
                                {
                                        $this->totalRecords--;
                                }
								$this->ObTpl->set_var("TPL_VAR_FLAG","Department");
                        }
                }
				if ($totalRecords > $this->pageSize) {
					#PAGINATION
					$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
				} else {
					$this->ObTpl->set_var("PagerBlock1", "");
				}
	}
	elseif($_SESSION['adv']=="content"){
		 #TO QUERY CONTENT TABLE 
		 $extraStr = "ecom/index.php?action=ecom.search&mode=".$this->request['mode'] ;
         $query = "SELECT iContentid_PK,vTitle,vSeoTitle,tShortDescription FROM ".CONTENTS." INNER JOIN ".FUSIONS." ON iContentid_PK=iSubId_FK WHERE "; 
         $query .="(vTitle LIKE '%".$this->request['mode']."%' OR "; 
         $query .="tShortDescription  LIKE '%".$this->request['mode']."%' OR "; 
         $query .="tContent LIKE '%".$this->request['mode']."%') AND vType='content' AND iState='1'"; 
         $rowProduct = $this->obDb->fetchQuery(); 

		 $pn = new PrevNext($this->pageTplPath, $this->pageTplFile, $this->obDb);
		 $pn->formno = 1;
		 
		 $navArr = $pn->create($query, $this->pageSize, $extraStr, $this->noPaging);
		 $rowProduct = $navArr['qryRes'];
		 $totalRecords = $navArr['totalRecs'];
		 $productCount = $navArr['fetchedRecords'];
		 $this->totalRecords = $totalRecords;

        if($productCount>0) 
        { 
            for($i=0;$i<$productCount;$i++) 
            { 
                $this->obDb->query = "SELECT count(*) as cnt FROM ".FUSIONS." WHERE iSubId_FK='".$rowProduct[$i]->iContentid_PK."' AND iState='1' AND vType='content'"; 
                 $rowCnt = $this->obDb->fetchQuery(); 
                if($rowCnt[0]->cnt>0) 
                { 
                $prodUrl=SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$rowProduct[$i]->vSeoTitle; 
                $this->ObTpl->set_var("TPL_VAR_CONTENTURL",$this->libFunc->m_safeUrl($prodUrl)); 
                $this->ObTpl->set_var("TPL_VAR_CONTENTNAME",$this->libFunc->m_displayContent($rowProduct[$i]->vTitle));     
                if($rowProduct[$i]->tShortDescription != ""){
                	$shortDesc=substr($this->libFunc->m_displayContent($rowProduct[$i]->tShortDescription),0,100); 
                	$this->ObTpl->set_var("TPL_VAR_SHORTDESC","<p>".$shortDesc."</p>");     
                }else{
                	$this->ObTpl->set_var("TPL_VAR_SHORTDESC","");
                }
                $this->ObTpl->parse("content_blk","TPL_CONTENT_BLK",true);     
                } 
                else 
                { 
                    $this->totalRecords--; 
                } 
				$this->ObTpl->set_var("TPL_VAR_FLAG","Content");
            } 
        } 
		if ($totalRecords > $this->pageSize) {
			#PAGINATION
			$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
		} else {
			$this->ObTpl->set_var("PagerBlock1", "");
		}
		
	  }
	  if($this->totalRecords==0)
		{
			$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",MSG_NO_SEARCHRESULT);	
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_TOTALRECORDS",$this->totalRecords."  Records Found");	
		}
		
		##Search terms for admin statistics.	
		$this->obDb->query = "SELECT * FROM ".SEARCHES." WHERE vSearchTerm ='".strtolower($this->request['mode'])."'";
		$rowSearch = $this->obDb->fetchQuery();
		$rowCount=$this->obDb->record_count; 
		
		if ($rowCount >0){
			$NumberOfSearches = $rowSearch[0]->iNumberOfSearches +1;
			$RecordsFound = $rowSearch[0]->iRecFound + $this->totalRecords;
			$this->obDb->query = "UPDATE ".SEARCHES." SET  iNumberOfSearches='".$NumberOfSearches."', iRecFoud ='".$RecordsFound."' WHERE iSearchPK='".$rowSearch[0]->iSearchPK."'" ;
			$this->obDb->updateQuery();
		}else{
			$this->obDb->query="INSERT INTO ".SEARCHES."(`vSearchTerm`, `iNumberOfSearches`,`iRecFoud`) VALUES(
		'".$this->libFunc->m_addToDB(strtolower($this->request['mode']))."','1','".$this->totalRecords."')";
			$this->obDb->updateQuery();
		}
		
		
		return($this->ObTpl->parse("return","TPL_SEARCH_FILE"));
}#END SEARCH CONTENT DISPLAY



}#END  CLASS
?>