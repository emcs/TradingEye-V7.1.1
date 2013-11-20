<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_orderReport
{
#CONSTRUCTOR
	function  c_orderReport()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

#FUNCTION TO DISPLAY ORDER REPORTS
	function m_orderReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ORDER_FILE",$this->orderTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_ORDER_BLK", "order_blk");
		$this->ObTpl->set_block("TPL_ORDER_FILE","TPL_PRODUCT_BLK", "product_blk");
		$this->ObTpl->set_block("TPL_PRODUCT_BLK","TPL_PRODUCTINNER_BLK", "productinner_blk");
		

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		#INTAILIZING ***
		$this->ObTpl->set_var("order_blk","");	
		$this->ObTpl->set_var("product_blk","");	
		$this->ObTpl->set_var("TPL_VAR_TODATE","");
		$this->ObTpl->set_var("TPL_VAR_FROMDATE","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("TPL_VAR_SELSTATUS1","");
		$this->ObTpl->set_var("TPL_VAR_SELSTATUS2","");
		$this->ObTpl->set_var("TPL_VAR_SELSTATUS3","");
		$this->ObTpl->set_var("TPL_VAR_SELSTATUS4","");
		$this->ObTpl->set_var("TPL_VAR_SELSTATUS5","");
		$this->ObTpl->set_var("TPL_VAR_REPOTYTYPE1","checked");
		$this->ObTpl->set_var("TPL_VAR_REPOTYTYPE2","");

		if(isset($this->request['start_date']) && !empty($this->request['start_date']))
		{
			$arrStartDate=explode("/",$this->request['start_date']);
			$this->request['start_date']=mktime(0,0,0,$arrStartDate[1],$arrStartDate[0],$arrStartDate[2]);
		}
		if(isset($this->request['end_date']) && !empty($this->request['end_date']))
		{
			$arrStartDate=explode("/",$this->request['end_date']);
			 $this->request['end_date']=mktime(23,59,59,$arrStartDate[1],$arrStartDate[0],$arrStartDate[2]);
		}
		if(!isset($this->request['status'])){
			$this->request['status']="All";
		}
		$status=$this->request['status'];
		$statusquery=" 1=1";
		if($this->request['status']!="All" && !empty($this->request['status']))
		{
			if($this->request['status']=="New")
			{
				$this->ObTpl->set_var("TPL_VAR_SELSTATUS1","selected");
			}
			elseif($this->request['status']=="Received")
			{
				$this->ObTpl->set_var("TPL_VAR_SELSTATUS2","selected");
			}
			elseif($this->request['status']=="Backorder")
			{
				$this->ObTpl->set_var("TPL_VAR_SELSTATUS3","selected");
			}
			elseif($this->request['status']=="Dispatched")
			{
				$status='Shipped';
				$this->ObTpl->set_var("TPL_VAR_SELSTATUS4","selected");
			}
			elseif($this->request['status']=="Void")
			{
				$this->ObTpl->set_var("TPL_VAR_SELSTATUS5","selected");
			}
			$statusquery=" vStatus='".$status."'";
		}

		if(isset($this->request['radReport']) && $this->request['radReport']=="Products")
		{
			$this->ObTpl->set_var("TPL_VAR_REPOTYTYPE2","checked");
				#DATABASE QUERY
			$this->obDb->query ="SELECT iOrderStatus,fPrice,iQty,tShortDescription,vTitle FROM ".ORDERS.",".ORDERPRODUCTS." WHERE iOrderid_FK=iOrderid_PK AND ";
			$this->obDb->query.=$statusquery;
			if(isset($this->request['start_date']) & $this->request['start_date']>0){
				$this->obDb->query.=" AND tmOrderDate >='".$this->request['start_date']."'";
			}else{
				$this->err=1;
				$this->errMsg=INVALID_START_DATE."<br>";
			}
			if(isset($this->request['end_date']) & $this->request['end_date']>0){
				$this->obDb->query.=" AND tmOrderDate <='".$this->request['end_date']."'";
			}else{
				$this->err=1;
				$this->errMsg.=INVALID_END_DATE;
			}

			if($this->err==0){
				$queryRs = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0){
					$this->ObTpl->set_var("TPL_VAR_FROMDATE", $this->libFunc->dateFormat2($this->request['start_date']));
					$this->ObTpl->set_var("TPL_VAR_TODATE", $this->libFunc->dateFormat2($this->request['end_date']));
					for($i=0;$i<$recordCount;$i++)
					{
						$this->ObTpl->set_var("TPL_VAR_STATUS","Incomplete");
						if($queryRs[$i]->iOrderStatus==1){
							$this->ObTpl->set_var("TPL_VAR_STATUS","Complete");
						}

						$this->ObTpl->set_var("TPL_VAR_EXTPRICE",number_format($queryRs[$i]->fPrice,2));
						$this->ObTpl->set_var("TPL_VAR_DESC",$this->libFunc->m_displayContent($queryRs[$i]->vTitle));
						$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($queryRs[$i]->fPrice,2));
						$this->ObTpl->set_var("TPL_VAR_QTY",$queryRs[$i]->iQty);
						$this->ObTpl->parse("productinner_blk","TPL_PRODUCTINNER_BLK",true);
					}
					$this->ObTpl->parse("product_blk","TPL_PRODUCT_BLK");
				}else{
					$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				}
			}
		}elseif(isset($this->request['radReport'])){
			$this->ObTpl->set_var("TPL_VAR_COUNT",0);
			$this->ObTpl->set_var("TPL_VAR_COMPLETE_TOTAL",0);
			$this->ObTpl->set_var("TPL_VAR_COMPLETE_AVGTOTAL",0);
			$this->ObTpl->set_var("TPL_VAR_COMPLETE_MAXTOTAL",0);
			$this->ObTpl->set_var("TPL_VAR_COMPLETE_COUNT",0);
			$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_TOTAL",0);
			$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_AVGTOTAL",0);
			$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_MAXTOTAL",0);
			$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_COUNT",0);
			$this->ObTpl->set_var("TPL_VAR_REPOTYTYPE1","checked");
			$this->ObTpl->set_var("PM_TPL_VAR_COMPLETE_VAT_TOTAL",0);
			
			#DATABASE QUERY
//	20090805	DJMasters.	Show total VAT.
	
//			$this->obDb->query = "SELECT iOrderStatus,MAX(fTotalPrice) as max,AVG(fTotalPrice) as avg,COUNT(fTotalPrice) as cnt,SUM(fTotalPrice) as total  FROM ".ORDERS;
			$this->obDb->query = "SELECT iOrderStatus, MAX(fTotalPrice) as max, AVG(fTotalPrice) as avg, COUNT(fTotalPrice) as cnt, SUM(fTotalPrice) as total, SUM(fTaxPrice) as totalTaxPrice  FROM ".ORDERS." WHERE ";
			$this->obDb->query.=$statusquery;
		
			if(isset($this->request['start_date']) & $this->request['start_date']>0){
				$this->obDb->query.=" AND tmOrderDate >='".$this->request['start_date']."'";
			}else{
				$this->err=1;
				$this->errMsg=INVALID_START_DATE."<br>";
			}
			if(isset($this->request['end_date']) & $this->request['end_date']>0){
				$this->obDb->query.=" AND tmOrderDate <='".$this->request['end_date']."'";
			}else{
				$this->err=1;
				$this->errMsg.=INVALID_END_DATE;
			}
			$this->obDb->query.=" GROUP BY iOrderStatus ORDER BY iOrderStatus DESC"; 
			if($this->err==0)
			{
				$queryRs = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0)
				{
					$this->ObTpl->set_var("TPL_VAR_FROMDATE",$this->libFunc->dateFormat2($this->request['start_date']));
					$this->ObTpl->set_var("TPL_VAR_TODATE",$this->libFunc->dateFormat2($this->request['end_date']));
					if ($recordCount > 1) {
						$this->ObTpl->set_var("TPL_VAR_COUNT",$queryRs[0]->cnt+$queryRs[1]->cnt);
					} else {
						$this->ObTpl->set_var("TPL_VAR_COUNT",$queryRs[0]->cnt);
					}
					for($i=0;$i<$recordCount;$i++){
						if($queryRs[$i]->iOrderStatus==1){
							$this->ObTpl->set_var("TPL_VAR_COMPLETE_TOTAL", number_format($queryRs[$i]->total,2));
							$this->ObTpl->set_var("TPL_VAR_COMPLETE_AVGTOTAL", number_format($queryRs[$i]->avg,2));
							$this->ObTpl->set_var("TPL_VAR_COMPLETE_MAXTOTAL", number_format($queryRs[$i]->max,2));
							$this->ObTpl->set_var("TPL_VAR_COMPLETE_COUNT",$queryRs[$i]->cnt);
						
							//	20090805 DJMasters. VAT Reporting.
							$this->ObTpl->set_var("PM_TPL_VAR_COMPLETE_VAT_TOTAL", number_format($queryRs[$i]->totalTaxPrice,2));
						}else{
							$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_TOTAL", number_format($queryRs[$i]->total,2));
							$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_AVGTOTAL", number_format($queryRs[$i]->avg,2));
							$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_MAXTOTAL", number_format($queryRs[$i]->max,2));
							$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_COUNT",$queryRs[$i]->cnt);
						}
					}
					$this->ObTpl->parse("order_blk","TPL_ORDER_BLK");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				}
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			}
			
		}

		return($this->ObTpl->parse("return","TPL_ORDER_FILE"));
	}#END FUNCTION

	#FUNCTION TO DISPLAY ORDER REPORTS
	function m_todayReport()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ORDER_FILE",$this->orderTemplate);
	
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_COUNT",0);
		$this->ObTpl->set_var("TPL_VAR_COMPLETE_TOTAL",0);
		$this->ObTpl->set_var("TPL_VAR_COMPLETE_AVGTOTAL",0);
		$this->ObTpl->set_var("TPL_VAR_COMPLETE_MAXTOTAL",0);
		$this->ObTpl->set_var("TPL_VAR_COMPLETE_COUNT",0);
		$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_TOTAL",0);
		$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_AVGTOTAL",0);
		$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_MAXTOTAL",0);
		$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_COUNT",0);
		#INTAILIZING ***
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$curMonth=date('m');
		$curDate=date('d');
		$curYear=date('Y');

		$this->request['start_date']=mktime(0,0,0,$curMonth,$curDate,$curYear);
		$this->request['end_date']=mktime(23,59,59,$curMonth,$curDate,$curYear);
			#DATABASE QUERY
		$this->obDb->query = "SELECT iOrderStatus,MAX(fTotalPrice) as max,AVG(fTotalPrice) as avg,COUNT(fTotalPrice) as cnt,SUM(fTotalPrice) as total  FROM ".ORDERS;
			
		$this->obDb->query.=" WHERE tmOrderDate >='".$this->request['start_date']."'";
		$this->obDb->query.=" AND tmOrderDate <='".$this->request['end_date']."'";
		$this->obDb->query.=" GROUP BY iOrderStatus";
		$queryRs = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		if(!isset($queryRs[1]->cnt)){
			$queryRs[1]->cnt=0;
		}
		if($recordCount>0){
			$this->ObTpl->set_var("TPL_VAR_COUNT", $queryRs[0]->cnt+$queryRs[1]->cnt);
			for($i=0;$i<$recordCount;$i++){
				if($queryRs[$i]->iOrderStatus==1){
					$this->ObTpl->set_var("TPL_VAR_COMPLETE_TOTAL", number_format($queryRs[$i]->total,2));
					$this->ObTpl->set_var("TPL_VAR_COMPLETE_AVGTOTAL", number_format($queryRs[$i]->avg,2));
					$this->ObTpl->set_var("TPL_VAR_COMPLETE_MAXTOTAL", number_format($queryRs[$i]->max,2));
					$this->ObTpl->set_var("TPL_VAR_COMPLETE_COUNT",$queryRs[$i]->cnt);
				}else{
					$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_TOTAL", number_format($queryRs[$i]->total,2));
					$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_AVGTOTAL", number_format($queryRs[$i]->avg,2));
					$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_MAXTOTAL", number_format($queryRs[$i]->max,2));
					$this->ObTpl->set_var("TPL_VAR_INCOMPLETE_COUNT",$queryRs[$i]->cnt);
				}
			}
		}
		
		return($this->ObTpl->parse("return","TPL_ORDER_FILE"));
	}#END FUNCTION

	function m_reportHome()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ORDER_FILE",$this->orderTemplate);
	
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
	
		return($this->ObTpl->parse("return","TPL_ORDER_FILE"));
	}#END FUNCTION
}#END CLASS
?>