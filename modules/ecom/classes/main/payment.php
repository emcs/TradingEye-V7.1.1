<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
class c_payment
{
#CONSTRUCTOR
	function c_payment()
	{
		$this->err=0;
		$this->credit=0;
		$this->solo=0;
		$this->codPrice=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}
	
	

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyPaymentGateway()
	{
		$this->errMsg="";
		$this->startMsg=1;
		$currentYear=date('Y');
		$currentMonth=date('m');
		if(!isset($this->request['paymethod']))
		{
			$this->request['paymethod']="";
		}
		if(!isset($this->request['cc_type']))
		{
			$this->request['cc_type']="";
		}
		switch($this->request['paymethod'])
		{
			case "cc":
			if(SELECTED_PAYMENTGATEWAY!=VERISIGN){
				if(empty($this->request['cc_type']))
				{
					$this->err=1;
					$this->errMsg.=MSG_CCTYPE_EMPTY."<br>";
				}
			}
			
			if($this->request['cc_type']=='SWITCH' || $this->request['cc_type']=='SOLO')
			{
				if(empty($this->request['issuenumber']))
				{
					if(empty($this->request['cc_start_month']) || empty($this->request['cc_start_year']))
					{
						$this->err=1;
						$this->errMsg.=MSG_ISSUENOANDDATE_EMPTY."<br>";
					}
					else
					{
						if($this->request['cc_start_year']>$currentYear)
						{
							$this->startMsg=0;
							$this->err=1;
							$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
						}
						elseif($this->request['cc_start_year']==$currentYear)
						{
							if($this->request['cc_start_month']>$currentMonth)
							{
								$this->startMsg=0;
								$this->err=1;
								$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
							}
						}
					}
				}
			}
			// Start Validation Modification for Card Holder Name (Protx)
			if(empty($this->request['cardholder_name']))
			{
					$this->err=1;
					$this->errMsg.=MSG_CARDHOLDER_EMPTY."<br />";
			}
		    // End Validation Modification for Card Holder Name (Protx) 
			if(empty($this->request['cc_number']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CCNUM_EMPTY."<br>";
			}
			if(empty($this->request['cc_month']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CCMONTH_EMPTY."<br>";
			}
			if(empty($this->request['cc_year']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CCYEAR_EMPTY."<br>";
			}

			if($this->request['cc_year']<$currentYear)
			{
				$this->err=1;
				$this->errMsg.=MSG_CCEXPDATE_INVALID."<br>";
			}
			elseif($this->request['cc_year']==$currentYear)
			{
				if($this->request['cc_month']<$currentMonth)
				{
					$this->err=1;
					$this->errMsg.=MSG_CCEXPDATE_INVALID."<br>";
				}
			}
			if($this->startMsg==1 && (!empty($this->request['cc_start_month']) || !empty($this->request['cc_start_year'])))
			{
				if($this->request['cc_start_year']>$currentYear)
				{
					$this->err=1;
					$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
				}
				elseif($this->request['cc_start_year']==$currentYear)
				{
					if($this->request['cc_start_month']>$currentMonth)
					{
						$this->err=1;
						$this->errMsg.=MSG_CCSTARTDATE_INVALID."<br>";
					}
				}
			}

			if(empty($this->request['cv2']))
			{
				$this->err=1;
				$this->errMsg.=MSG_CV2_EMPTY."<br>";
			}
			break;
			case "eft":
			if(empty($this->request['acct']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ACCT_EMPTY."<br>";
			}
			if(empty($this->request['aba']))
			{
				$this->err=1;
				$this->errMsg.=MSG_ABA_EMPTY."<br>";
			}
			break;
		}

		return $this->err;
	}

}#END CLASS
?>