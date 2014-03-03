<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_giftCertInterface
{
#CONSTRUCTOR
	function  c_giftCertInterface()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

#FUNCTION TO DISPLAY All AVAILABLE DISCOUNTS
	function m_dspGiftCert()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_GIFTCERT_FILE",$this->giftCertTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_GIFTCERT_FILE","TPL_GIFTCERT_BLK", "giftCert_blk");
		$this->ObTpl->set_block("TPL_GIFTCERT_FILE","TPL_MESSAGE_BLK", "message_blk");
		$this->ObTpl->set_block("TPL_GIFTCERT_FILE","TPL_MSG_BLK1", "msg_blk1");
		$this->ObTpl->set_block("TPL_GIFTCERT_FILE","TPL_MSG_BLK2", "msg_blk2");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		#INTAILIZING ***
		$this->ObTpl->set_var("giftCert_blk","");	
		$this->ObTpl->set_var("message_blk","");	
		$this->ObTpl->set_var("msg_blk1","");	
		$this->ObTpl->set_var("msg_blk2","");	
		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		#DATABASE QUERY
		$this->obDb->query = "SELECT*  FROM ".GIFTCERTIFICATES;
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_GIFTCERT_INSERTED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}
		elseif($this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_GIFTCERT_DELETED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}

		if($recordCount>0)
		{
			#PARSING DISCOUNT BLOCK
			for($j=0;$j<$recordCount;$j++)
			{		
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iGiftcertid_PK);
				$this->ObTpl->set_var("TPL_VAR_CODE",$this->libFunc->m_displayContent($queryResult[$j]->vGiftcode));

				$str =$this->libFunc->m_displayContent($queryResult[$j]->vGiftcode);
				$str=str_replace("'","\'",$str);
				//$str=str_replace('"','\"',$str);

				$this->ObTpl->set_var("TPL_VAR_CODE1", $str);
				$this->ObTpl->set_var("TPL_VAR_AMOUNT",number_format($queryResult[$j]->fAmount ,2));
				$this->ObTpl->set_var("TPL_VAR_REMAINING",number_format($queryResult[$j]->fRemaining  ,2));
				$this->ObTpl->set_var("TPL_VAR_BUILDDATE",$this->libFunc->dateFormat2($queryResult[$j]->tmBuildDate));
				$this->ObTpl->parse("giftCert_blk","TPL_GIFTCERT_BLK",true);
			}
			$this->ObTpl->set_var("TPL_VAR_MSG",$recordCount." records found");
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOGIFTCERT);
			$this->ObTpl->parse("message_blk","TPL_MESSAGE_BLK");
		}
		return($this->ObTpl->parse("return","TPL_GIFTCERT_FILE"));
	}


	#FUNCTION TO BUILD PACKAGE
	function m_giftCertBuilder()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_GIFTCERT_FILE",$this->giftCertTemplate);
		$this->ObTpl->set_block("TPL_GIFTCERT_FILE","TPL_MSG_BLK", "msg_blk");
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		#INTIALIZING
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$giftCertRs[0]->vGiftcode  ="";
		$giftCertRs[0]->fAmount  ="";

		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_GIFTCERT_UPDATED);
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
				$giftCertRs[0]->vGiftcode=$this->request['code'];
			if(isset($this->request['amount']))
				$giftCertRs[0]->fAmount=$this->request['amount'];
		}
	

		#START DISPLAY MODULES
		if(isset($this->request['id']) && !empty($this->request['id']) && is_numeric($this->request['id']))
		{
			if($this->err==0)
			{
				#DATABASE QUERY
				$this->obDb->query = "SELECT *  FROM ".GIFTCERTIFICATES." WHERE iGiftcertid_PK='".$this->request['id']."'";
				$giftCertRs = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->libFunc->dateFormat1($giftCertRs[0]->tmBuildDate));
			}
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_EDITGIFTCERT_BTN);
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_ID","");
			$this->ObTpl->set_var("TPL_VAR_MODE","add");
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_ADDGIFTCERT_BTN);
		}
		$this->ObTpl->set_var("TPL_VAR_CODE",$this->libFunc->m_displayContent($giftCertRs[0]->vGiftcode));
		$this->ObTpl->set_var("TPL_VAR_AMOUNT",number_format(floatval($giftCertRs[0]->fAmount),2));
		return($this->ObTpl->parse("return","TPL_GIFTCERT_FILE"));
	}
	
#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEdit()
	{
		$this->errMsg="";
		if(empty($this->request['code']))
		{
			$this->err=1;
			$this->errMsg=MSG_GIFTCODE_EMPTY."<br>";
		}
		
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iGiftcertid_PK  FROM ".GIFTCERTIFICATES." where vGiftcode = '".$this->request['code']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iGiftcertid_PK !=$this->request['id'])
			{
				$this->err=1;
				$this->errMsg.=MSG_GIFTCODE_EXIST."<br>";
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
			$this->errMsg=MSG_GIFTCODE_EMPTY."<br>";
		}
		
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iGiftcertid_PK  FROM ".GIFTCERTIFICATES." where vGiftcode = '".$this->request['code']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->err=1;
			$this->errMsg.=MSG_GIFTCODE_EXIST."<br>";
		}
		return $this->err;
	}
}
?>