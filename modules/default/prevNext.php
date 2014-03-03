<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
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
			$this->libFunc=new c_libFunctions();
		}
		
		function create($qry,$pageSize="10",$extraStr="",$noPaging='0')
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
				$extraStr=$extraStr;
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
			elseif($noPaging=='1')
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
			$cn->query=$query;
			$resArr['qryRes']=$cn->fetchQuery();
			$resArr['fetchedRecords']=$cn->record_count;
			$resArr['totalRecs']=$totalRecs;
			$resArr['numPages']=$numPages;
			$resArr['query']    = $query;
			$t = new Template($this->templatepath);	
			$t->set_file("htmlpage",$this->pntemplate);
			$t->set_block("htmlpage","prev","prevs");
			$t->set_block("htmlpage","next","nexts");
			
			$t->set_block("htmlpage","nextsets","nextset");
			$t->set_block("htmlpage","prevsets","prevset");
			
			$t->set_block("htmlpage","pagelist","pagelists");
			
			$t->set_var("nextset","");
			$t->set_var("prevset","");
			
			$t->set_var("Extra",$extraStr);
			$t->set_var("pageNo",$page);
			$t->set_var("totalPages",$numPages);
			$t->set_var("itemtotal",$totalRecs);
			$t->set_var("itemfrom",(($page-1) * $pageSize)+1);

			if($numPages>1)
				{
					if($_REQUEST['viewallpages']!='y')
					{
					$t->set_var("TPL_VAR_VIEWALL","<a href='".$this->selfAddr."?".$extraStr."&viewallpages=y'>View All</a>");	
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
				$t->set_var("FirstLink","");
			}
			else
			{
				$t->set_var("FirstLink",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=1"));
			}

			if($page>1)
			{
				$t->set_var("prevLink",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=".($page -1)));
				$t->parse("prevs","prev",true);
			}
			else
			{
				$t->set_var("prevs","");
			}

			if($page == $numPages)
			{
				$t->set_var("LastLink","");
			}
			else
			{
				$t->set_var("LastLink",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=".$numPages));
			}
			
			$page_no = 10;					
			
			$page_set = ceil($page/$page_no);
						
			/*	
			$start_index =  $page - $page_no;
			if ($start_index < 1 ) {	$start_index = 1; }
			
			$end_index = $page + $page_no -1;			
			if ($end_index > $numPages) { $end_index = $numPages; }
			
			*/
									
			
			$start_index 	=  ($page_set-1)*10+1;					
			$end_index 		= 	$page_set*10;
						
			if ($end_index > $numPages) { $end_index = $numPages; }
			
			// Setting previous set and next set			
			
			if ($page_set==1 || empty($page) ){				
				$t->set_var("prevset","");
			}
			else{				
				$t->set_var("prevSetLink",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=".($page_set-1)*10));
				$t->parse("prevset","prevsets",true);
			}
			
			if ($page_set == ceil($numPages/$page_no )){				
				$t->set_var("nextset","");
			}
			else{				
				$t->set_var("nextSetLink",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=".($page_set*10 +1)));
				$t->parse("nextset","nextsets",true);
			}
			
			
			for($i = $start_index; $i<$end_index+1; $i++)
			{
				$t->set_var("pageLink",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=".$i));
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
				$t->set_var("nextLink",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=".($page+1)));
				$t->parse("nexts","next");
			}
			else
			{
				$t->set_var("nexts","");
			}
			
			
			$t->set_var("FLINK",$this->libFunc->m_safeUrl(SITE_URL.$extraStr."&page=1"));
			$t->parse('output','htmlpage');	
			$resArr['pnContents']=$t->get_var("output");
			return ($resArr);
		}
	}
?>