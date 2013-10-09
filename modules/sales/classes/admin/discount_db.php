<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_discountDb
{
	#CONSTRUCTOR
	function c_discountDb()
	{
		$this->libFunc=new c_libFunctions();
	}
	#FUNCTION UPDATE,ADD NEW PACKAGE
	function m_insertDiscount()
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
		$this->request['state']=$this->libFunc->ifSet($this->request,"state");
		$this->request['usage']=$this->libFunc->ifSet($this->request,"usage");

	$this->obDb->query = "INSERT INTO  ".DISCOUNTS." SET  
		fMinimum		='".$this->libFunc->checkWrongValue($this->request['minimum'])."',
		vCode			='".$this->libFunc->m_addToDB($this->request['code'])."', 
		fDiscount		='".$this->libFunc->checkWrongValue($this->request['discount'])."',
		fFixamount 		='".$this->libFunc->checkWrongValue($this->request['discount_price'])."', 
		tmStartDate	='".$this->request['start_date']."', 
		tmEndDate		='".$this->request['end_date']."', 
		iState			='".$this->request['state']."', 
		iUseonce			='".$this->request['usage']."',
		tmBuildDate='".$timeStamp."'" ;

		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.discount.home&msg=1");	
		exit;	
	}
	
	function m_updateDiscount()
	{
		$timeStamp=time();
		$libFunc=new c_libFunctions();
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
		$this->request['state']=$this->libFunc->ifSet($this->request,"state");
		$this->request['usage']=$this->libFunc->ifSet($this->request,"usage");
		
		$this->obDb->query = "UPDATE ".DISCOUNTS." SET  
		fMinimum		='".$this->libFunc->checkWrongValue($this->request['minimum'])."',		
		vCode			='".$this->libFunc->m_addToDB($this->request['code'])."', 
		fDiscount		='".$this->libFunc->checkWrongValue($this->request['discount'])."',
		fFixamount 		='".$this->libFunc->checkWrongValue($this->request['discount_price'])."',		 
		tmStartDate	='".$this->libFunc->m_addToDB($this->request['start_date'])."', 
		tmEndDate		='".$this->libFunc->m_addToDB($this->request['end_date'])."', 
		iState			='".$this->request['state']."',
		iUseonce			='".$this->request['usage']."',		
		tmEditDate		='".$timeStamp."'
		WHERE iDiscountid ='".$this->request['id']."'"; ;

		$this->obDb->updateQuery();
		
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.discount.dspForm&id=".$this->request['id']."&msg=1");	
		exit;	
	}

# FUNTION TO DELETE DISCOUNT
function m_discountDelete()
	{
		if(isset($this->request['id']) && !empty($this->request['id']))
		{			
				$this->obDb->query = "DELETE FROM ".DISCOUNTS." WHERE  iDiscountid =".$this->request['id'];
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.discount.home&msg=3");	
		exit;	
	}
	
	#FUNCTION WILL UPDATE HOME( STATE FIELD)
	function m_updateHome()
	{
		if(isset($this->request['state']))
		{
			$state=$this->request['state'];
		}
		$this->obDb->query="UPDATE ".DISCOUNTS." set `iState`='0'";
		$this->obDb->updateQuery();

		if(isset($state))
		{
			foreach($state as $stateid=>$stateValue)
			{
				$this->obDb->query="UPDATE ".DISCOUNTS." set
				 `iState`='$stateValue' where iDiscountid='$stateid'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=promotions.discount.home&msg=2");	
		exit;
	}	#EF
}#CLASS ENDS
?>