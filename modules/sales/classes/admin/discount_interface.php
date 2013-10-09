<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
class c_discountInterface
{
#CONSTRUCTOR
	function  c_discountInterface()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

#FUNCTION TO DISPLAY All AVAILABLE DISCOUNTS
	function m_dspDiscounts()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_WHOLETABLE_BLK", "wholetable_blk");
		$this->ObTpl->set_block("TPL_WHOLETABLE_BLK","TPL_DISCOUNT_BLK", "discount_blk");
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_BUTTON_BLK", "button_blk");
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_MESSAGE_BLK", "message_blk");
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_MSG_BLK1", "msg_blk1");
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_MSG_BLK2", "msg_blk2");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");

		#INTAILIZING ***

		$this->ObTpl->set_var("wholetable_blk","");
		$this->ObTpl->set_var("discount_blk","");	
		$this->ObTpl->set_var("button_blk","");
		$this->ObTpl->set_var("message_blk","");	
		$this->ObTpl->set_var("msg_blk1","");	
		$this->ObTpl->set_var("msg_blk2","");

		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		$this->ObTpl->set_var("TPL_VAR_MESSAGE","");

		#DATABASE QUERY
		$this->obDb->query = "SELECT iDiscountid,vCode,fFixamount,fDiscount,tmStartDate,tmEndDate,tmBuildDate,iState  FROM ".DISCOUNTS;
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;

		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_INSERTED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}
		elseif($this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNTS_UPDATED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}
		elseif($this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_DELETED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}

		if($recordCount>0)
		{
			#PARSING DISCOUNT BLOCK
			for($j=0;$j<$recordCount;$j++)
			{		
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iDiscountid);
				$str =$this->libFunc->m_displayContent($queryResult[$j]->vCode);
				$str=str_replace("'","\'",$str);

				$this->ObTpl->set_var("TPL_VAR_CODE1", $str);
				$this->ObTpl->set_var("TPL_VAR_CODE",$this->libFunc->m_displayContent($queryResult[$j]->vCode));
				
                if ($queryResult[$j]->fDiscount > 0){
                    $this->ObTpl->set_var("TPL_VAR_DISCOUNT",number_format($queryResult[$j]->fDiscount,2));
                    $this->ObTpl->set_var("TPL_VAR_DISCOUNT_TYPE","%");
                    $this->ObTpl->set_var("TPL_VAR_DISCOUNT_CURRENCY","");
                }else{
                    $this->ObTpl->set_var("TPL_VAR_DISCOUNT",number_format($queryResult[$j]->fFixamount,2));
                    $this->ObTpl->set_var("TPL_VAR_DISCOUNT_TYPE","");
                    $this->ObTpl->set_var("TPL_VAR_DISCOUNT_CURRENCY",CONST_CURRENCY);
                }    
                //$this->ObTpl->set_var("TPL_VAR_DISCOUNT",number_format($queryResult[$j]->fDiscount,2));
				$this->ObTpl->set_var("TPL_VAR_BUILDDATE",$this->libFunc->dateFormat2($queryResult[$j]->tmBuildDate));
				$this->ObTpl->set_var("TPL_VAR_STARTDATE",$this->libFunc->dateFormat2($queryResult[$j]->tmStartDate));
				$this->ObTpl->set_var("TPL_VAR_ENDDATE",$this->libFunc->dateFormat2($queryResult[$j]->tmEndDate));
				
				if($queryResult[$j]->iState==1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","checked=\"checked\"");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","");
				}
				$this->ObTpl->parse("discount_blk","TPL_DISCOUNT_BLK",true);
			}
			$this->ObTpl->parse("wholetable_blk","TPL_WHOLETABLE_BLK");
			$this->ObTpl->parse("button_blk","TPL_BUTTON_BLK");
			$this->ObTpl->set_var("TPL_VAR_MSG",$recordCount." records found");
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NODISCOUNT);
			$this->ObTpl->parse("message_blk","TPL_MESSAGE_BLK");
		}
		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}

	#FUNCTION TO BUILD PACKAGE
	function m_discountBuilder()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_MSG_BLK", "msg_blk");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);

		#INTIALIZING
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		
		$this->ObTpl->set_var("TPL_VAR_MINIMUM","");
		$this->ObTpl->set_var("TPL_VAR_DISCOUNT_PRICE","");
		
		
		$discountRs[0]->vCode ="";
		$discountRs[0]->fDiscount ="";
		$discountRs[0]->tmStartDate ="";
		$discountRs[0]->tmEndDate ="";
		$discountRs[0]->fFixamount ="";
		$discountRs[0]->fMinimum ="";
		$discountRs[0]->iState ="1";
		$discountRs[0]->iUseone ="1";
		
		
		$this->ObTpl->set_var("TPL_VAR_STARTDATE","");
		$this->ObTpl->set_var("TPL_VAR_ENDDATE","");
		

		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_DISCOUNT_UPDATED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}	
	
		if(isset($_POST))
		{
			if(isset($this->request['code']))
				$discountRs[0]->vCode=$this->request['code'];
			if(isset($this->request['discount']))
				$discountRs[0]->fDiscount=$this->request['discount'];
			if(isset($this->request['start_date']))
				$discountRs[0]->tmStartDate=$this->request['start_date'];
			if(isset($this->request['end_date']))
				$discountRs[0]->tmEndDate=$this->request['end_date'];
			
			if(isset($this->request['minimum']))
				$discountRs[0]->fMinimum=$this->request['minimum'];
			if(isset($this->request['discount_price']))
				$discountRs[0]->fFixamount=$this->request['discount_price'];
			
			if(isset($this->request['usage']))
				$discountRs[0]->iUseonce=$this->request['usage'];
			else
				$discountRs[0]->iUseonce=0;
			
			if(isset($this->request['state']))
				$discountRs[0]->iState= $this->request['state'];
			else
				$discountRs[0]->iState=0;
			$this->ObTpl->set_var("TPL_VAR_STARTDATE",$discountRs[0]->tmStartDate);
			$this->ObTpl->set_var("TPL_VAR_ENDDATE",$discountRs[0]->tmEndDate);
		}
	

		#START DISPLAY MODULES
		if(isset($this->request['id']) && !empty($this->request['id']) && is_numeric($this->request['id']))
		{
			if($this->err==0)
			{
				#DATABASE QUERY
				$this->obDb->query = "SELECT vCode,fMinimum,fDiscount,fFixamount,tmStartDate,tmEndDate,tmBuildDate,iUseonce,iState  FROM ".DISCOUNTS." WHERE iDiscountid='".$this->request['id']."'";
				$discountRs = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_STARTDATE",$this->libFunc->dateFormat2($discountRs[0]->tmStartDate));
				$this->ObTpl->set_var("TPL_VAR_ENDDATE",$this->libFunc->dateFormat2($discountRs[0]->tmEndDate));
				
				$this->ObTpl->set_var("TPL_VAR_DISCOUNT_PRICE",number_format($discountRs[0]->fFixamount,2));
				$this->ObTpl->set_var("TPL_VAR_MINIMUM",number_format($discountRs[0]->fMinimum,2));
				
			}
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_EDITDISCOUNT_BTN);
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_ID","");
			$this->ObTpl->set_var("TPL_VAR_MODE","add");
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_ADDDISCOUNT_BTN);
		}
		$this->ObTpl->set_var("TPL_VAR_CODE",$this->libFunc->m_displayContent($discountRs[0]->vCode));
		$this->ObTpl->set_var("TPL_VAR_DISCOUNT",$discountRs[0]->fDiscount);
		
		if($discountRs[0]->iState==1)
		{
				$discountRs[0]->iState="checked";
			}
			else
			{
				$discountRs[0]->iState="";
			}
		$this->ObTpl->set_var("TPL_VAR_STATE",$discountRs[0]->iState);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		if($discountRs[0]->iUseonce==1)
		{
				$discountRs[0]->iUseonce="checked";
			}
			else
			{
				$discountRs[0]->iUseonce="";
			}
		$this->ObTpl->set_var("TPL_VAR_USAGE",$discountRs[0]->iUseonce);
		
		
		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}
	
#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEdit()
	{
		$this->errMsg="";
		if(empty($this->request['code']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CODE_EMPTY."<br>";
		}
		
		if(!empty($this->request['discount']) && $this->request['discount']>100)
		{
			$this->err=1;
			$this->errMsg.=MSG_DISCOUNT_INVALID."<br>";
		}
		
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iDiscountid  FROM ".DISCOUNTS." where vCode  = '".$this->request['code']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iDiscountid !=$this->request['id'])
			{
				$this->err=1;
				$this->errMsg.=MSG_CODE_EXIST."<br>";
			}
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsert()
	{
		$this->errMsg="";
	
		if(empty($this->request['code']))
		{
			$this->err=1;
			$this->errMsg.=MSG_CODE_EMPTY."<br>";
		}
		if(!empty($this->request['discount']) && $this->request['discount']>100)
		{
			$this->err=1;
			$this->errMsg.=MSG_DISCOUNT_INVALID."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iDiscountid  FROM ".DISCOUNTS." where vCode  = '".$this->request['code']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->err=1;
			$this->errMsg.=MSG_CODE_EXISTS."<br>";
		}
		return $this->err;
	}
}
?>