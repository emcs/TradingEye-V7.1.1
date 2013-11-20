<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_enquiryInterface
{
	#CONSTRUCTOR
	function  c_enquiryInterface()
	{
		$this->libFunc=new c_libFunctions();		
	}

	#FUNCTION TO DISPLAY PACKAGE
	function m_dspEnquiries()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_CONTACT_FILE",$this->contactTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_CONTACT_FILE","TPL_MSG_BLK", "msg_blk");
		$this->ObTpl->set_block("TPL_CONTACT_FILE","TPL_MAIN_BLK", "main_blk");
		$this->ObTpl->set_block("TPL_MAIN_BLK","TPL_ENQUIRY_BLK", "enquiry_blk");
		$this->ObTpl->set_var("msg_blk","");	
		$this->ObTpl->set_var("main_blk","");	
		$this->ObTpl->set_var("enquiry_blk","");	
		
		#defining language pack variables.
		$this->ObTpl->set_var("LANG_VAR_ENQUIRIES",LANG_ENQUIRIES);
		$this->ObTpl->set_var("LANG_VAR_NAME",LANG_NAME);
		$this->ObTpl->set_var("LANG_VAR_EMAIL",LANG_EMAILTXT);
		$this->ObTpl->set_var("LANG_VAR_COUNTRY",LANG_COUNTRY);
		$this->ObTpl->set_var("LANG_VAR_DELETECONTACT",LANG_DELETECONTACT);
		

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_USERURL",SITE_URL."user/");
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
        
		$this->obDb->query=$this->obDb->query = "SELECT  iContactId_PK,vName,vEmail,tmAddDate,vCountry  FROM ".ENQUIRIES;
		$queryResult = $this->obDb->fetchQuery();
		#TO DISPLAY ENQUIRIES 
		$recordCount=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_RECORDCOUNT",$recordCount);
		if($recordCount>0)
		{
			#PARSING TPL_USER_BLK
			for($j=0;$j<$recordCount;$j++)
			{							
				$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($queryResult[$j]->vName));
				$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($queryResult[$j]->vEmail));
				$this->ObTpl->set_var("TPL_VAR_COUNTRY",$this->libFunc->m_displayContent($queryResult[$j]->vCountry));
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iContactId_PK);
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iContactId_PK);
                $this->ObTpl->set_var("TPL_VAR_DATE",$this->libFunc->dateFormat2($queryResult[$j]->tmAddDate));
                $this->ObTpl->parse("enquiry_blk","TPL_ENQUIRY_BLK",true);
			}
			$this->ObTpl->parse("main_blk","TPL_MAIN_BLK");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NO_ENQUIRY);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		
		return($this->ObTpl->parse("return","TPL_CONTACT_FILE"));
	}#FUNCTION END 

	function dspEnquiryDetails()
	{
		if(!isset($this->request['id']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=enquiry.home");
			exit;
		}
		else
		{
			$this->request['id']=intval($this->request['id']);
		}

		if($this->request['id']<1)
		{
			#URL TEMPER
			$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=enquiry.home");
			exit;
		}
		else
		{
			#INTIALIZING TEMPLATES
			$this->ObTpl=new template();
			$this->ObTpl->set_file("TPL_CONTACT_FILE", $this->contactTemplate);

			$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
			$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
			$this->ObTpl->set_var("TPL_USERURL",SITE_URL."user/");
			$this->ObTpl->set_var("TPL_VAR_USERID",$this->request['id']);
			
			#defining language pack variables.
			$this->ObTpl->set_var("LANG_VAR_MANAGE",LANG_MANGECONTACT);
			$this->ObTpl->set_var("LANG_VAR_NAME",LANG_NAME);
			$this->ObTpl->set_var("LANG_VAR_EMAIL",LANG_EMAILADDRESS);
			$this->ObTpl->set_var("LANG_VAR_ADDRESS1",LANG_ADDRESS1);
			$this->ObTpl->set_var("LANG_VAR_ADDRESS2",LANG_ADDRESS2);
			$this->ObTpl->set_var("LANG_VAR_TELEPHONE",LANG_TELEPHONE);
			$this->ObTpl->set_var("LANG_VAR_COUNTRY",LANG_COUNTRY);
			$this->ObTpl->set_var("LANG_VAR_GOBACK",LANG_GOBACK);
			$this->ObTpl->set_var("LANG_VAR_DELETETHISCONTACT",LANG_DELTHISCONTACT);
			
			
			
			#QUERY DATABASE
			$this->obDb->query =$this->obDb->query = "SELECT iContactId_PK,vName,vEmail,vCountry,";
			$this->obDb->query.="vAddress1,vAddress2,vWorkPhone,vComments,tmAddDate ";
			$this->obDb->query.=" FROM ".ENQUIRIES." WHERE iContactId_PK = '".$this->request['id']."'";
			$row_customer		= $this->obDb->fetchQuery();

			$this->ObTpl->set_var("TPL_VAR_ID", $this->libFunc->m_displayContent($row_customer[0]->iContactId_PK));
			$this->ObTpl->set_var("TPL_VAR_NAME", $this->libFunc->m_displayContent($row_customer[0]->vName));
			$this->ObTpl->set_var("TPL_VAR_EMAIL", $this->libFunc->m_displayContent($row_customer[0]->vEmail));
			$this->ObTpl->set_var("TPL_VAR_COUNTRY", $this->libFunc->m_displayContent($row_customer[0]->vCountry));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS1", $this->libFunc->m_displayContent($row_customer[0]->vAddress1 ));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS2", $this->libFunc->m_displayContent($row_customer[0]->vAddress2 ));
			$this->ObTpl->set_var("TPL_VAR_PHONE", $this->libFunc->m_displayContent($row_customer[0]->vWorkPhone));
			$this->ObTpl->set_var("TPL_VAR_REQUEST", $this->libFunc->m_displayContent($row_customer[0]->vComments));
			$this->ObTpl->set_var("TPL_VAR_DATE",$this->libFunc->dateFormat($row_customer[0]->tmAddDate));
			return($this->ObTpl->parse("return","TPL_CONTACT_FILE"));
		}#END ELSE LOOP
	
	}#END FUNCTION

	function m_deleteEnquiries()
	{
		if(isset($this->request['txtChkbox']))
		{
			$rcount=count($this->request['txtChkbox']);
			for($i=0;$i<$rcount;$i++)
			{
				 $this->obDb->query="DELETE FROM ".ENQUIRIES." WHERE 
				 iContactid_PK='".$this->request['txtChkbox'][$i]."'"; 
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=enquiry.home");
		exit;
	}#ef

	function m_deleteEnquiry()
	{
		if(isset($this->request['contactId']))
		{
				 $this->obDb->query="DELETE FROM ".ENQUIRIES." WHERE 
				 iContactid_PK='".$this->request['contactId']."'"; 
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=enquiry.home");
		exit;
	}#ef
}#CLASS END
?>