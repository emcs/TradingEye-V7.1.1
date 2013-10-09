<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
	class PrevNext
	{
		var $selfAddr;
		var $pntemplate;
		var $templatepath;		
		var $dbCon;

		function PrevNext($path,$tpl,$dbConn)
		{
			global $PHP_SELF;
			$tempArr=array_reverse(explode("/",$PHP_SELF));
			$this->selfAddr=$tempArr[0];
			$this->templatepath=$path;
			$this->pntemplate=$tpl;
			$this->dbCon = $dbConn;
		}
		
		function create($qry,$pageSize="10",$extraStr="",$border="bottom")
			{
			
			if(!isset($_REQUEST['page']))
			{
				$page=1;
			}
			else
			{
				$page=$_REQUEST['page'];
			}
			
			if($extraStr!="")
			{
				$extraStr="&".$extraStr;
			}
			if(!isset($_REQUEST['viewallpages']))
			{
				$_REQUEST['viewallpages']="";
			}
			 

			$query=$qry;
			$cn=$this->dbCon;
			
			if(!$qryResult =$cn->execQry($query)) 
			{
				trigger_error(mysql_error(),E_USER_ERROR);
			}
			
			
			$totalRecs = mysql_num_rows($qryResult); 
			$initialrecords=$totalRecs;
			$numPages = ceil($totalRecs / $pageSize);
			
			
			if($_REQUEST['viewallpages']=='y')
				{
				$query = $query . " LIMIT 0, " . $totalRecs; 
				}
			else
				{

			if($page <= 1)
			{
				$page = 1; 
				$query = $query . " LIMIT 0, " . $pageSize; 
			}
			else
			{ 
				// If page value is greater than total number of pages, set page value to max. number of pages
				
				if($page > $numPages)
				{
					$page = $numPages;
				}
				$query = $query . " LIMIT " . (($page-1) * $pageSize) . ", " . $pageSize; 
			}
				}
			if(!$qryResult =$cn->execQry($query)) 
			{
				trigger_error(mysql_error(),E_USER_ERROR);
			}
			$cn->query=$query;
			$resArr['qryRes']=$cn->fetchQuery();
			$resArr['selRecs']=$cn->record_count;
			$resArr['totalRecs']=$totalRecs;
			$resArr['numPages']=$numPages;
			$resArr['query']    = $query;
			$t = new Template($this->templatepath);	
			$t->set_file("htmlpage",$this->pntemplate);
			$t->set_block("htmlpage","prev","prevs");
			$t->set_block("htmlpage","next","nexts");
			$t->set_block("htmlpage","pagelist","pagelists");
			$t->set_var("Extra",$extraStr);
			if($border=="bottom")
				$t->set_var("TPL_VAR_CLASS","bottombordergray");
			else
				$t->set_var("TPL_VAR_CLASS","topbordergray");
			$t->set_var("pageNo",$page);
			$t->set_var("totalPages",$numPages);
			$t->set_var("itemtotal",$totalRecs);
			$t->set_var("itemfrom",(($page-1) * $pageSize)+1);

			if($numPages>1)
				{
					if($_REQUEST['viewallpages']!='y')
					{
					$t->set_var("TPL_VAR_VIEWALL","<a href='".$this->selfAddr."?viewallpages=y".$extraStr."'>View All</a>");	
					}
					else
					{
					$t->set_var("TPL_VAR_VIEWALL","View All");	
					}
				}
			else
				{
					$t->set_var("TPL_VAR_VIEWALL","");	
				}

			if($this->formno=="")
				{
					$t->set_var("TPL_VAR_FORMNO","1");						
				}
			else
				{
					$t->set_var("TPL_VAR_FORMNO",$this->formno);						
				}
			if(($pageSize*$page)<$totalRecs)
				$t->set_var("itemto",$pageSize*$page);
			else
				$t->set_var("itemto",$totalRecs);


			if($_REQUEST['viewallpages']=='y')
				{
					$t->set_var("itemfrom",1);
					$t->set_var("itemto",$initialrecords);
				}		
			
			if(1 == $page || empty($page))
			{
				$t->set_var("FirstLink","First");
			}
			else
			{
				$t->set_var("FirstLink", "<a href=".$this->selfAddr."?page=1".$extraStr." class='link'>First</a> ");
			}

			if($page>1)
			{
				$t->set_var("prevLink",$this->selfAddr."?page=".($page -1).$extraStr);
				$t->parse("prevs","prev",true);
			}
			else
			{
				$t->set_var("prevs","");
			}

			if($page == $numPages)
			{
				$t->set_var("LastLink","Last");
			}
			else
			{
				$t->set_var("LastLink", "<a href=".$this->selfAddr."?page=".$numPages.$extraStr." class='link'>Last</a>" );
			}

			for($i = 1; $i<$numPages+1; $i++)
			{
				$t->set_var("pageLink",$this->selfAddr."?page=".$i.$extraStr);
				$t->set_var("dspPage",$i);
				if($page == $i)
				{
					$t->set_var("sel","selected");
				}
				else
				{
					$t->set_var("sel","");
				}
				$t->parse("pagelists","pagelist",true);
			}
				

			if($page < $numPages)
			{
				$t->set_var("nextLink",$this->selfAddr."?page=".($page+1).$extraStr);
				$t->parse("nexts","next");
			}
			else
			{
				$t->set_var("nexts","");
			}
			$t->set_var("FLINK",$this->selfAddr."?page=1".$extraStr);
			$t->parse('output','htmlpage');	
			$resArr['pnContents']=$t->get_var("output");
			
			return ($resArr);
		}
	}
?>