<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_promotionInterface
{
#CONSTRUCTOR
	function  c_promotionInterface()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

//********FUNCTION TO DISPLAY HOMEPAGE FOR PROMOTIONS**********************

	function m_dspPromotions()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_MSG_BLK","msg_blk");

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");

		#INTAILIZING ***
		$this->ObTpl->set_var("discount_blk","");	
		$this->ObTpl->set_var("msg_blk","");	

		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		$this->ObTpl->set_var("TPL_VAR_MSG","");

		#DATABASE QUERY
		$this->obDb->query = "SELECT tmStartDate,tmEndDate  FROM ".PROMOTIONS." WHERE vPromotype='flat'";
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		$stDate=$this->libFunc->dateFormat3($queryResult[0]->tmStartDate);
		$enddate=$this->libFunc->dateFormat3($queryResult[0]->tmEndDate);
		$this->ObTpl->set_var("TPL_VAR_RANGE1","(".$stDate.")-(".$enddate.")");


		$this->obDb->query = "SELECT tmStartDate,tmEndDate  FROM ".PROMOTIONS." WHERE vPromotype='range'";
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		$stDate=$this->libFunc->dateFormat3($queryResult[0]->tmStartDate);
		$enddate=$this->libFunc->dateFormat3($queryResult[0]->tmEndDate);
		$this->ObTpl->set_var("TPL_VAR_RANGE2","(".$stDate.")-(".$enddate.")");

		$this->obDb->query = "SELECT tmStartDate,tmEndDate  FROM ".PROMOTIONS." WHERE vPromotype='free'";
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		$stDate=$this->libFunc->dateFormat3($queryResult[0]->tmStartDate);
		$enddate=$this->libFunc->dateFormat3($queryResult[0]->tmEndDate);
		$this->ObTpl->set_var("TPL_VAR_RANGE3","(".$stDate.")-(".$enddate.")");

		switch($this->request['msg']){
		case 1:
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FLAT_INSERTED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		break;
		case 2:
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_RANGE_INSERTED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		break;
		case 3:
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FREE_INSERTED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		break;
		case 4:
	  		$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_MEMBERPOINTS_UPDATED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		break;
		}
	
		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}
//***********************FLAT DISCOUNT**********************************

	#FUNCTION TO BUILD PACKAGE
	function m_flatDiscount()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("CURRENCY",CONST_CURRENCY);
		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$discountRs[0]->iCarttotal ="";
		$discountRs[0]->fDiscount ="";
		$discountRs[0]->tmStartDate ="";
		$discountRs[0]->tmEndDate ="";
		$discountRs[0]->iState ="1";
		$this->ObTpl->set_var("TPL_VAR_STARTDATE","");
		$this->ObTpl->set_var("TPL_VAR_ENDDATE","");

		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_UPDATED);
		}
		
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
		}
	
		if(isset($_POST))
		{
			if(isset($this->request['total']))
				$discountRs[0]->vCode=$this->request['total'];
			if(isset($this->request['discount']))
				$discountRs[0]->fDiscount=$this->request['discount'];
			if(isset($this->request['start_date']))
				$discountRs[0]->tmStartDate=$this->request['start_date'];
			if(isset($this->request['end_date']))
				$discountRs[0]->tmEndDate=$this->request['end_date'];
			$this->ObTpl->set_var("TPL_VAR_STARTDATE",$discountRs[0]->tmStartDate);
			$this->ObTpl->set_var("TPL_VAR_ENDDATE",$discountRs[0]->tmEndDate);
		
		}
	

		#START DISPLAY MODULES
	if($this->err==0)
	{
		#DATABASE QUERY
		$this->obDb->query = "SELECT *  FROM ".PROMOTIONS." WHERE vPromotype='flat'";
		$discountRs = $this->obDb->fetchQuery();
		if($this->obDb->record_count>0)
		{
			$this->ObTpl->set_var("TPL_VAR_MODE",'edit');
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MODE",'add');
		}
		$this->ObTpl->set_var("TPL_VAR_STARTDATE",$this->libFunc->dateFormat2($discountRs[0]->tmStartDate));
		$this->ObTpl->set_var("TPL_VAR_ENDDATE",$this->libFunc->dateFormat2($discountRs[0]->tmEndDate));
	}

		$this->ObTpl->set_var("TPL_VAR_TOTAL",$discountRs[0]->fCarttotal);
		$this->ObTpl->set_var("TPL_VAR_DISCOUNT",$discountRs[0]->fDiscount);
	
		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}


//********************************FREE  POSTAGE****************************
	function m_freeDiscount()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("CURRENCY",CONST_CURRENCY);
		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$discountRs[0]->iCarttotal ="0";

		$discountRs[0]->tmStartDate ="";
		$discountRs[0]->tmEndDate ="";
		$discountRs[0]->iState ="1";
		$this->ObTpl->set_var("TPL_VAR_STARTDATE","");
		$this->ObTpl->set_var("TPL_VAR_ENDDATE","");

		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_UPDATED);
		}
		
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
		}
	
		if(isset($_POST))
		{
			if(isset($this->request['total']))
				$discountRs[0]->vCode=$this->request['total'];
		
			if(isset($this->request['start_date']))
				$discountRs[0]->tmStartDate=$this->request['start_date'];
			if(isset($this->request['end_date']))
				$discountRs[0]->tmEndDate=$this->request['end_date'];
			$this->ObTpl->set_var("TPL_VAR_STARTDATE",$discountRs[0]->tmStartDate);
			$this->ObTpl->set_var("TPL_VAR_ENDDATE",$discountRs[0]->tmEndDate);
		
		}
	
		#START DISPLAY MODULES
	if($this->err==0)
	{
		#DATABASE QUERY
		$this->obDb->query = "SELECT *  FROM ".PROMOTIONS." WHERE vPromotype='free'";
		$discountRs = $this->obDb->fetchQuery();
		if($this->obDb->record_count>0)
		{
			$this->ObTpl->set_var("TPL_VAR_MODE",'edit');
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MODE",'add');
		}
		$this->ObTpl->set_var("TPL_VAR_STARTDATE",$this->libFunc->dateFormat2($discountRs[0]->tmStartDate));
		$this->ObTpl->set_var("TPL_VAR_ENDDATE",$this->libFunc->dateFormat2($discountRs[0]->tmEndDate));
	}

		$this->ObTpl->set_var("TPL_VAR_TOTAL",$discountRs[0]->fCarttotal);
			
		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}

#****************FUNCTION TO HANDLE RANGE DISCOUNT***********************

	function m_rangeDiscount()
	{

		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_CODE_BLK","dspcodes_blk");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("CURRENCY",CONST_CURRENCY);
		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("dspcodes_blk","");
		$resPostage[0]->tmStartDate ="";
		$resPostage[0]->tmEndDate ="";

		$this->ObTpl->set_var("TPL_VAR_STARTDATE","");
		$this->ObTpl->set_var("TPL_VAR_ENDDATE","");
		$this->ObTpl->set_var("TPL_VAR_COUNT","N/A");
		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_UPDATED);
		}
		
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
		}
	
		if(isset($_POST))
		{
	
			if(isset($this->request['start_date']))
				$resPostage[0]->tmStartDate=$this->request['start_date'];
			if(isset($this->request['end_date']))
				$resPostage[0]->tmEndDate=$this->request['end_date'];
			$this->ObTpl->set_var("TPL_VAR_STARTDATE",$resPostage[0]->tmStartDate);
			$this->ObTpl->set_var("TPL_VAR_ENDDATE",$resPostage[0]->tmEndDate);
		
		}
	
		#DATABASE QUERY
		$this->obDb->query = "SELECT *  FROM ".PROMOTIONS." WHERE vPromotype='range'";
		$resPostage = $this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		if ($rsCount >0){ 
			for($i=0;$i<$rsCount;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
				$this->ObTpl->set_var("TPL_VAR_ID",$resPostage[$i]->iPromotionid_PK);
				$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$resPostage[$i]->iRangefield);
				$this->ObTpl->set_var("TPL_VAR_TOTAL",$resPostage[$i]->fCarttotal);
				$this->ObTpl->set_var("TPL_VAR_DISCOUNT",$resPostage[$i]->fDiscount);
				$this->ObTpl->parse("dspcodes_blk","TPL_CODE_BLK",true);
			}
		}
		$this->ObTpl->set_var("TPL_VAR_STARTDATE",$this->libFunc->dateFormat2($resPostage[0]->tmStartDate));
		$this->ObTpl->set_var("TPL_VAR_ENDDATE",$this->libFunc->dateFormat2($resPostage[0]->tmEndDate));
	
		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}
#****************FUNCTION TO HANDLE MEMBER POINTS DISCOUNT***********************
	function m_memberpointDiscount() {
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var('TPL_VAR_MODE',"update");
		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1){
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_MEMBERPOINTS_UPDATED);
		}
		
		if($this->err==1){
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
		}// end of error

		#START DISPLAY MODULES
	if($this->err==0){
		#DATABASE QUERY

		//$this->obDb->query  = "SELECT * FROM ".SITESETTINGS." where vDatatype IN ('memberPointValue','memberPointCalculation')  order by iSettingid";
		// $data=$this->obDb->fetchquery();
   	     $this->ObTpl->set_var('TPL_VAR_ENABLEMEMBERSHIP','');
		if(OFFERMPOINT == 1)
		{
   	     $this->ObTpl->set_var('TPL_VAR_ENABLEMEMBERSHIP','checked');
		}
   	     $this->ObTpl->set_var('TPL_VAR_POINTSVALUE',MPOINTVALUE);
	     $this->ObTpl->set_var('TPL_VAR_POINTSCALC',MPOINTCALCULATION);
	}// end of error
		 
		
	
		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}
#**************** END OF FUNCTION TO HANDLE MEMBER POINTS DISCOUNT***********************



function displayIt($value) {
		if($value==1) {
			return "checked";
		}
		else {
			return "";
		}
}// end of function
#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditFlat()
	{
		$this->errMsg="";

		return $this->err;
	}
#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditRange()
	{
		$this->errMsg="";

		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditFree()
	{
		$this->errMsg="";

		return $this->err;
	}
}
?>
