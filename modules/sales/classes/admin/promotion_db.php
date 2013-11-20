<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
	class c_promotionDb
	{
		#CONSTRUCTOR
		function c_promotionDb()
		{
			$this->libFunc=new c_libFunctions();
		}

		#********FUNCTION WILL INSERT/UPDATYE DELETE FLAT DISCOUNT***************
		function m_insertFlatDiscount()
		{
			$timeStamp=time();
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
	
			if($this->request['mode']=='edit')
			{
				$this->obDb->query = "UPDATE ".PROMOTIONS." SET  
				fCarttotal		='".$this->libFunc->checkWrongValue($this->request['total'])."',
				fDiscount		='".$this->libFunc->checkWrongValue($this->request['discount'])."', 
 				tmStartDate	='".$this->libFunc->m_addToDB($this->request['start_date'])."', 
				tmEndDate		='".$this->libFunc->m_addToDB($this->request['end_date'])."', 
				tmEditDate		='".$timeStamp."'
				WHERE vPromotype ='flat'"; 
			}
			else
			{
				$this->obDb->query = "INSERT INTO  ".PROMOTIONS." SET  
				fCarttotal		='".$this->libFunc->checkWrongValue($this->request['total'])."', 
				fDiscount		='".$this->libFunc->checkWrongValue($this->request['discount'])."', 
				tmStartDate	='".$this->request['start_date']."', 
				tmEndDate		='".$this->request['end_date']."', 
				vPromotype		='flat',
				iSort				='2',
				tmEditDate		='".$timeStamp."',
				iAdminUser		='".$_SESSION['uid']."'"; ;
			}
			$this->obDb->updateQuery();

			if(isset($this->request['endPromo']) && $this->request['endPromo']==1)
			{
				$this->obDb->query = "DELETE FROM ".PROMOTIONS." WHERE (vPromotype ='flat')"; 
				$this->obDb->updateQuery();
			}
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.home&msg=1");	
		}

		function m_insertFreeDiscount()
		{
			$timeStamp=time();

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
	
			if($this->request['mode']=='edit')
			{
				$this->obDb->query = "UPDATE ".PROMOTIONS." SET  
				fCarttotal		='".$this->libFunc->checkWrongValue($this->request['total'])."',
				tmStartDate	='".$this->libFunc->m_addToDB($this->request['start_date'])."', 
				tmEndDate		='".$this->libFunc->m_addToDB($this->request['end_date'])."', 
				tmEditDate		='".$timeStamp."'
				WHERE vPromotype ='free'"; 
			}
			else
			{
				$this->obDb->query = "INSERT INTO  ".PROMOTIONS." SET  
				fCarttotal		='".$this->libFunc->checkWrongValue($this->request['total'])."', 
				tmStartDate	='".$this->request['start_date']."', 
				tmEndDate		='".$this->request['end_date']."', 
				vPromotype		='free',
				iSort				='1',
				tmEditDate		='".$timeStamp."',
				iAdminUser		='".$_SESSION['uid']."'"; ;
			}
			$this->obDb->updateQuery();

			if(isset($this->request['endPromo']) && $this->request['endPromo']==1)
			{
				$this->obDb->query = "DELETE FROM ".PROMOTIONS." WHERE (vPromotype ='free')"; 
				$this->obDb->updateQuery();
			}
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.home&msg=3");	
		}

		//********FUNCTION WILL INSERT/UPDATYE DELETE DISCOUNT RANGES***************

	function m_insertRangeDiscount()
	{
		$libFunc=new c_libFunctions();
		$timeStamp=time();

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

		if(!empty($this->request['newtotal']) && !empty($this->request['newdiscount']))
		{
			$this->obDb->query = "INSERT INTO  ".PROMOTIONS." SET  
			fCarttotal		='".$this->libFunc->checkWrongValue($this->request['newtotal'])."', 
			fDiscount		='".$this->libFunc->checkWrongValue($this->request['newdiscount'])."', 
			iRangefield		='".$this->libFunc->m_addToDB($this->request['newdescription'])."',
			tmStartDate	='".$this->request['start_date']."', 
			tmEndDate		='".$this->request['end_date']."', 
			vPromotype		='range',
			iSort				='3',
			tmEditDate		='".$timeStamp."',
			iAdminUser		='".$_SESSION['uid']."'";
			$this->obDb->updateQuery();
		}
		if(isset($this->request['id']))
		{
			$cnt=count($this->request['id']);
			for($i=0;$i<$cnt;$i++)
			{
				$this->obDb->query = "UPDATE ".PROMOTIONS." SET  
				fCarttotal		='".$this->libFunc->checkWrongValue($this->request['total'][$i])."', 
				fDiscount		='".$this->libFunc->checkWrongValue($this->request['discount'][$i])."', 
				iRangefield		='".$this->libFunc->m_addToDB($this->request['description'][$i])."',
				tmStartDate	='".$this->request['start_date']."', 
				tmEndDate		='".$this->request['end_date']."', 
				vPromotype		='range',
				tmEditDate		='".$timeStamp."' WHERE iPromotionid_PK='".$this->request['id'][$i] ."'";
				$this->obDb->updateQuery();
			}
		}

		if(isset($this->request['endPromo']) && $this->request['endPromo']==1)
		{
			$this->obDb->query = "DELETE FROM ".PROMOTIONS." WHERE (vPromotype ='range')"; 
			$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.home&msg=2");	
	}#ef
	function m_updatememberpoint(){
		if(isset($_REQUEST['membership']) && !empty($_REQUEST['membership']))
		{
			$query="UPDATE ".SITESETTINGS." SET nNumberdata ='1' WHERE vDatatype='membership'";
			$this->obDb->execQry($query);
		}
		else
		{
			$query="UPDATE ".SITESETTINGS." SET nNumberdata ='0' WHERE vDatatype='membership'";
			$this->obDb->execQry($query);
		}
		$this->request['memberPointValue'] = $this->libFunc->ifSet($this->request,'memberPointValue');
		$this->request['memberPointCalculation'] = $this->libFunc->ifSet($this->request,'memberPointCalculation');
		if('update'===$this->request['mode']){
			foreach($this->request as $fieldname=>$value){
					if($fieldname==='memberPointValue' || $fieldname==='memberPointCalculation'|| $fieldname==='membership'){
					$query="UPDATE ".SITESETTINGS." SET 
						nNumberdata ='".$this->libFunc->m_addToDB($value)."' WHERE vDatatype='".$fieldname."'";
						$this->obDb->execQry($query);
				}// inner if
			}// end of foreach
		}// end of update
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.home&msg=4");
	}
}#CLASS ENDS
?>