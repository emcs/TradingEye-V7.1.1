<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_emailInterface
{
#CONSTRUCTOR
	function  c_emailInterface()
	{
		$this->err=0;
		$this->errMsg="";
		$this->libFunc=new c_libFunctions();
	}

	#FUNCTION TO DISPLAY All EMAIL CAMPAIGNs
	function m_dspemails()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_EMAIL_FILE",$this->emailTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_EMAIL_FILE","TPL_EMAIL_BLK", "email_blk");
		$this->ObTpl->set_block("TPL_EMAIL_FILE","TPL_MESSAGE_BLK", "message_blk");
		$this->ObTpl->set_block("TPL_EMAIL_FILE","TPL_MSG_BLK1", "msg_blk1");

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");

		#INTAILIZING ***
		$this->ObTpl->set_var("email_blk","");	
		$this->ObTpl->set_var("message_blk","");	
		$this->ObTpl->set_var("msg_blk1","");	
		$this->ObTpl->set_var("msg_blk2","");	

		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		$this->ObTpl->set_var("TPL_VAR_MESSAGE","");

		#DATABASE QUERY
		$this->obDb->query = "SELECT *  FROM ".EMAILS;
		$queryResult = $this->obDb->fetchQuery();
		$campaigncount = $this->obDb->record_count;
		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_EMAIL_INSERTED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}
		elseif($this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_EMAIL_DELETED);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}
		elseif($this->request['msg']==5)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_EMAIL_SENT);
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
			$this->ObTpl->parse("msg_blk2","TPL_MSG_BLK2");
		}

		if($campaigncount>0)
		{
			#PARSING DISCOUNT BLOCK
			for($j=0;$j<$campaigncount;$j++)
			{		
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iMailid_PK);
				if ($queryResult[$j]->vUserList=="All"){
                   $this->obDb->query = "SELECT count(*) as cnt FROM ".CUSTOMERS." WHERE iStatus=1 AND iMailList !=0";
                }else{    
                   $this->obDb->query = "SELECT count(*) as cnt FROM ".LEADLIST." WHERE iLeadId_FK='". $queryResult[$j]->vUserList."'";
                }
				$qryRs = $this->obDb->fetchQuery();
				
				if ($queryResult[$j]->vVisitorList=="1"){
                   $this->obDb->query = "SELECT count(*) as cnt FROM ".NEWSLETTERS;
				   $qryVs = $this->obDb->fetchQuery();
				   $qryRs[0]->cnt = $qryRs[0]->cnt + $qryVs[0]->cnt;
                }

				$this->ObTpl->set_var("TPL_VAR_USERCOUNT",$qryRs[0]->cnt);
                
                $this->ObTpl->set_var("TPL_VAR_SUBJECT",$this->libFunc->m_displayContent($queryResult[$j]->vSubject));
				$this->ObTpl->set_var("TPL_VAR_SID",$this->libFunc->m_displayContent($queryResult[$j]->vSid));
				$this->ObTpl->set_var("TPL_VAR_BUILDDATE",$this->libFunc->dateFormat2($queryResult[$j]->tmBuildDate));	
				$sentDate=$this->libFunc->dateFormat2($queryResult[$j]->tmSentDate);
				if(empty($sentDate))
				{				
					$this->ObTpl->set_var("TPL_VAR_SENTDATE","Send now");
					$this->ObTpl->set_var("TPL_VAR_VIEWLABEL","View/Sent");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SENTDATE",$sentDate);
					$this->ObTpl->set_var("TPL_VAR_VIEWLABEL","View");
				}
				$this->ObTpl->parse("email_blk","TPL_EMAIL_BLK",true);
			}
			$this->ObTpl->set_var("TPL_VAR_MSG",$campaigncount." records found");
			$this->ObTpl->parse("msg_blk1","TPL_MSG_BLK1");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOEMAILS);
			$this->ObTpl->parse("message_blk","TPL_MESSAGE_BLK");
		}
	
		return($this->ObTpl->parse("return","TPL_EMAIL_FILE"));
	}

    function m_dspcustomerlist()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_CUSTOMERLIST_FILE",$this->leadTemplate);
		$this->ObTpl->set_block("TPL_CUSTOMERLIST_FILE","TPL_CUSTOMER_BLK","customer_blk");
		$this->ObTpl->set_var("customer_blk","");
		
		if(isset($this->request['id'])&& $this->request['id']!="")
		{
			$this->obDb->query="SELECT vUserList FROM ".EMAILS." WHERE iMailid_PK=".$this->request['id'];
			$vUserList=$this->obDb->fetchQuery();
			if ($vUserList[0]->vUserList=="All"){
			$this->obDb->query = "SELECT vEmail,vFirstname,vLastname  FROM ".CUSTOMERS." WHERE iStatus=1 AND iMailList !=0";
			}elseif($this->obDb->record_count==0){
			$this->obDb->query= "SELECT vFirstname,vEmail,vLastname FROM ".LEADLIST.",".CUSTOMERS."	WHERE iCustomerid_FK=iCustmerid_PK AND iLeadId_FK='".$this->request['id']."'";	
			}else{
			$this->obDb->query= "SELECT vFirstname,vEmail,vLastname FROM ".LEADLIST.",".CUSTOMERS."	WHERE iCustomerid_FK=iCustmerid_PK AND iLeadId_FK='".$vUserList[0]->vUserList."'";
			}
		}elseif(isset($this->request['leadid'])&& $this->request['leadid']!=""){
		$this->obDb->query= "SELECT vFirstname,vEmail,vLastname FROM ".LEADLIST.",".CUSTOMERS."	WHERE iCustomerid_FK=iCustmerid_PK AND iLeadId_FK='".$this->request['leadid']."'";	
		}
		
		$rsCustomer=$this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		for ($i=0;$i<$rCount;$i++)
		{
			$j=$i+1;
			$this->ObTpl->set_var("TPL_VAR_ORDER",$j);
			$this->ObTpl->set_var("TPL_VAR_NAME",$rsCustomer[$i]->vFirstname." ".$rsCustomer[$i]->vLastname);
			$this->ObTpl->set_var("TPL_VAR_EMAIL",$rsCustomer[$i]->vEmail);
			$this->ObTpl->parse("customer_blk","TPL_CUSTOMER_BLK",true);
		}	
		$this->ObTpl->pparse("return","TPL_CUSTOMERLIST_FILE");
		exit;
	}

	#FUNCTION TO DISPLAY EMAIL LEADS
	function m_dspLeads()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_LEAD_FILE",$this->leadTemplate);
		$this->ObTpl->set_block("TPL_LEAD_FILE","TPL_MESSAGE_BLK", "message_blk");
		$this->ObTpl->set_block("TPL_LEAD_FILE","TPL_MSG_BLK", "msg_blk");

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_LEAD_FILE","TPL_LEAD_BLK", "lead_blk");

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SALESURL",SITE_URL."sales/");

		#INTAILIZING 
		$this->ObTpl->set_var("lead_blk","");	
		$this->ObTpl->set_var("message_blk","");	
		$this->ObTpl->set_var("msg_blk","");	

		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		$this->ObTpl->set_var("TPL_VAR_MESSAGE","");

		#DATABASE QUERY
		$this->obDb->query = "SELECT *  FROM ".LEADS." WHERE vdescription!=' '";
		$queryResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;

		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_LEAD_INSERTED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		elseif($this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_LEAD_DELETED);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		elseif($this->request['msg']==5)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_EMAIL_SENT);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}

		if($recordCount>0)
		{
			#PARSING LEAD BLOCK
			for($j=0;$j<$recordCount;$j++)
			{		
				$this->obDb->query = "SELECT count(*) as cnt FROM ".LEADLIST." WHERE iLeadId_FK='". $queryResult[$j]->iLeadid_PK."'";
				$qryRs = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_COUNT",$qryRs[0]->cnt);
				$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iLeadid_PK);
				$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$this->libFunc->m_displayContent($queryResult[$j]->vdescription));
				$this->ObTpl->set_var("TPL_VAR_BUILDDATE",$this->libFunc->dateFormat2($queryResult[$j]->tmBuildDate));	
				
				$this->ObTpl->parse("lead_blk","TPL_LEAD_BLK",true);
			}
			$this->ObTpl->set_var("TPL_VAR_MSG",$recordCount." records found");
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOLEADS);
			$this->ObTpl->parse("message_blk","TPL_MESSAGE_BLK");
		}
	
		return($this->ObTpl->parse("return","TPL_LEAD_FILE"));
	}


	#FUNCTION TO BUILD EMAIL LEAD
	function m_emailBuilder()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_EMAIL_FILE",$this->emailTemplate);
		$this->ObTpl->set_block("TPL_EMAIL_FILE","TPL_TOPMSG_BLK", "topmsg_blk");
		$this->ObTpl->set_block("TPL_EMAIL_FILE","TPL_SELECT_BLK", "select_blk");

		$this->ObTpl->set_block("TPL_EMAIL_FILE","TPL_BTN_BLK", "btn_blk");
		$this->ObTpl->set_block("TPL_EMAIL_FILE","TPL_TESTMAIL_BLK", "testmail_blk");
		$this->ObTpl->set_block("TPL_TESTMAIL_BLK","TPL_SENDMAIL_BLK", "sendmail_blk");
		$this->ObTpl->set_block("TPL_TESTMAIL_BLK","TPL_SENTMSG_BLK", "sentmsg_blk");

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);

		#INTIALIZING
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("btn_blk","");
		$this->ObTpl->set_var("topmsg_blk","");
		$this->ObTpl->set_var("testmail_blk","");
		$this->ObTpl->set_var("sendmail_blk","");
		$this->ObTpl->set_var("sentmsg_blk","");
		$this->ObTpl->set_var("select_blk","");

		$emailRs[0]->vSubject ="";
		$emailRs[0]->vSid ="";
		$emailRs[0]->tHtmlMail ="";
		$emailRs[0]->tTextMail ="";
		$emailRs[0]->tmSentDate="";
		$emailRs[0]->vUserList=$this->libFunc->ifSet($this->request,"leadid");
		$this->ObTpl->set_var("TPL_VAR_SENTDATE","");
		$this->ObTpl->set_var("TPL_VAR_ADMIN",$_SESSION['uname']);
		#DISPLAYING MESSAGES
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_EMAIL_UPDATED);
			$this->ObTpl->parse("topmsg_blk","TPL_TOPMSG_BLK");
		}
		
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("topmsg_blk","TPL_TOPMSG_BLK");
		}

		
		if(isset($_POST))
		{
			if(isset($this->request['subject']))
				$emailRs[0]->vSubject =$this->request['subject'];
			if(isset($this->request['sid']))
				$emailRs[0]->vSid=$this->request['sid'];
			if(isset($this->request['html_mail']))
				$emailRs[0]->tHtmlMail=$this->request['html_mail'];
			if(isset($this->request['text_mail']))
				$emailRs[0]->tTextMail=$this->request['text_mail'];
			if(isset($this->request['user_list']))
				$emailRs[0]->vUserList = $this->request['user_list'];
		}


		if(isset($this->request['id']) && !empty($this->request['id']) && is_numeric($this->request['id']))
		{
			if($this->err==0)
			{
				#DATABASE QUERY
				$this->obDb->query = "SELECT *  FROM ".EMAILS." WHERE iMailid_PK='".$this->request['id']."'";
				$emailRs = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_SENTDATE",$this->libFunc->dateFormat2($emailRs[0]->tmSentDate));
				if(empty($emailRs[0]->tmSentDate))
					{
						$this->ObTpl->parse("sendmail_blk","TPL_SENDMAIL_BLK");
					}
					else
					{
						$this->ObTpl->parse("sentmsg_blk","TPL_SENTMSG_BLK");
					}
				$this->ObTpl->parse("testmail_blk","TPL_TESTMAIL_BLK");	
			
			}
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_EDITCAMPAIGN_BTN);
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_ID","");
			$this->ObTpl->set_var("TPL_VAR_MODE","add");
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_ADDCAMPAIGN_BTN);
		}


		$this->ObTpl->parse("btn_blk","TPL_BTN_BLK");

		$this->obDb->query = "SELECT count(*) as cntCustomer  FROM ".CUSTOMERS." WHERE iStatus=1 AND iMailList !=0";
		$custCnt = $this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_CUSTCNT",$custCnt[0]->cntCustomer);

		if(isset($emailRs[0]->vVisitorList)){
			if($emailRs[0]->vVisitorList == "0"){
				$this->ObTpl->set_var("TPL_MAILN_VISITORS","selected='selected'");	
			}else{
				$this->ObTpl->set_var("TPL_MAILY_VISITORS","selected='selected'");	
			}
		}

		$this->obDb->query = "SELECT count(*) as cntVisitor  FROM ".NEWSLETTERS;
		$cntVisitor = $this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_VAR_VISITORCNT",$cntVisitor[0]->cntVisitor);

		$this->obDb->query = "SELECT *  FROM ".LEADS." WHERE vdescription!=' '";
		$leadRs = $this->obDb->fetchQuery();
		$recordCount1=$this->obDb->record_count;
		if($recordCount1>0)
		{
			#PARSING DISCOUNT BLOCK
			for($j=0;$j<$recordCount1;$j++)
			{		
				if (isset($this->request['leadid']))
                {
                $emailRs[0]->vUserList = $this->request['leadid'];
                }
                
                if($emailRs[0]->vUserList==$leadRs[$j]->iLeadid_PK)
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED","selected");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED","");
				}
                
                
				$this->obDb->query = "SELECT count(*) as cnt FROM ".LEADLIST." WHERE iLeadId_FK='". $leadRs[$j]->iLeadid_PK."'";
				$qryRs = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_COUNT",$qryRs[0]->cnt);
				$this->ObTpl->set_var("TPL_VAR_LEADID",$leadRs[$j]->iLeadid_PK);
				$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($leadRs[$j]->vdescription));

				$this->ObTpl->parse("select_blk","TPL_SELECT_BLK",true);
			}
		}	

		//******************************************
		$this->ObTpl->set_var("TPL_VAR_SUBJECT",$this->libFunc->m_displayContent($emailRs[0]->vSubject));
		$this->ObTpl->set_var("TPL_VAR_SID",$this->libFunc->m_displayContent($emailRs[0]->vSid));
		$this->ObTpl->set_var("TPL_VAR_HTMLMSG",$this->libFunc->m_displayContent($emailRs[0]->tHtmlMail));
		$this->ObTpl->set_var("TPL_VAR_PLAINMSG",$this->libFunc->m_displayContent($emailRs[0]->tTextMail));		
		return($this->ObTpl->parse("return","TPL_EMAIL_FILE"));
	}
	
	#FUNCTIO TO DISPLAY RECURSIVE TITLE
	function m_getTitle($ownerid,$type)
	{
		if($ownerid!=0)
		{
			$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".DEPARTMENTS." D ,".FUSIONS." F WHERE iDeptid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			$row = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				$_SESSION['dspTitle']=" /".$this->libFunc->m_displayContent($row[0]->vTitle).$_SESSION['dspTitle'];
				$this->m_getTitle($row[0]->iOwner_FK,$row[0]->vOwnerType);
			}
		}

			return $_SESSION['dspTitle'];
	}

	#FUNCTION TO DISPLAY LEAD BUILDER	
	function m_leadBuilder()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_LEAD_FILE",$this->leadTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_LEAD_FILE","TPL_DEPARTMENT_BLK", "dept_blk");
		$this->ObTpl->set_block("TPL_LEAD_FILE","TPL_ITEMS_BLK", "items_blk");
		$this->ObTpl->set_block("TPL_LEAD_FILE","TPL_MAINATTACHED_BLK", "mainattached_blk");
		$this->ObTpl->set_block("TPL_MAINATTACHED_BLK","TPL_ATTACHED_BLK", "attached_blk");
		#INTIALIZING VARIABLES
	
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("dept_blk","");
		$this->ObTpl->set_var("items_blk","");
		$this->ObTpl->set_var("attached_blk","");
		$this->ObTpl->set_var("mainattached_blk","");
		$this->ObTpl->set_var("TPL_VAR_TODATE","");
		$this->ObTpl->set_var("TPL_VAR_FROMDATE","");
		if(!isset($this->request['postOwner']))
		{
			$this->request['postOwner']="0";
		}
		if(!isset($this->request['leadid']))
		{
			$this->request['leadid']="";
		}
		$this->ObTpl->set_var("TPL_VAR_LEADID",$this->request['leadid']);
		$this->ObTpl->set_var("TPL_VAR_POSTOWNER",$this->request['postOwner']);
		#START DISPLAY DEPARETMENT BLOCK
		$this->obDb->query = "SELECT vTitle,iDeptId_PK FROM ".DEPARTMENTS.", ".FUSIONS."  WHERE iDeptId_PK=iSubId_FK AND vType='department'";
		$deptResult = $this->obDb->fetchQuery();
		 $recordCount=$this->obDb->record_count;
		#PARSING DEPARTMENT BLOCK
		$this->ObTpl->set_var("SELECTED1","selected");
		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				$_SESSION['dspTitle']="";		
				 $this->ObTpl->set_var("TPL_VAR_TITLE",$this->m_getTitle($deptResult[$i]->iDeptId_PK,'department'));
				$this->ObTpl->set_var("TPL_VAR_ID",$deptResult[$i]->iDeptId_PK);
				if(isset($this->request['postOwner']) && $this->request['postOwner'] == $deptResult[$i]->iDeptId_PK)
				{
					$this->ObTpl->set_var("SELECTED1","");
					$this->ObTpl->set_var("SELECTED2","selected");
				}
				else
				{
					$this->ObTpl->set_var("SELECTED2","");
				}
				
				$this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);
			}
		}
		
		#END DISPLAY DEPARETMENT BLOCK
		if($this->request['postOwner']=="orphan")
		{
			 $this->obDb->query= "SELECT vTitle,fusionid,iProdId_PK FROM ".PRODUCTS." LEFT JOIN ".FUSIONS." ON iProdId_PK = iSubId_FK " ;
			$queryResult = $this->obDb->fetchQuery();
			$recordCount=$this->obDb->record_count;

			if(!isset($this->request['owner'])){
				$this->request['owner'] = "";
			}
				
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if(empty($queryResult[$j]->fusionid) && $this->request['owner']!=$queryResult[$j]->iProdId_PK)
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
			else
			{#IF OTHER THAN ORPHAN
				$query = "SELECT vTitle,iProdId_PK FROM ".PRODUCTS.", ".FUSIONS."  WHERE iProdId_PK=iSubId_FK AND iOwner_FK='".$this->request['postOwner']."' AND vOwnerType='department' AND vType='product'";
				$this->obDb->query=$query;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						/*if($this->request['owner']!=$queryResult[$j]->iProdId_PK)
						{*/
						$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
						$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
						$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						//}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}

		
		#TO DISPLAY CURRENTLY ATTACHED ITEMS
			$query1 = "SELECT vTitle,iLeadProductId_PK,iProductid_FK  FROM ".LEADPRODUCT." ,".PRODUCTS."  WHERE iProductid_FK=iProdid_PK  AND  iLeadId_FK='".$this->request['leadid']."'";
			$this->obDb->query=$query1;
			$queryResult = $this->obDb->fetchQuery();
			$recordCount=$this->obDb->record_count;
			if($recordCount>0)
			{
				#PARSING TPL_ITEMS_BLK
				for($j=0;$j<$recordCount;$j++)
				{
					$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iLeadProductId_PK);
					$this->ObTpl->set_var("TPL_VAR_PID",$queryResult[$j]->iProductid_FK);
					$this->ObTpl->parse("attached_blk","TPL_ATTACHED_BLK",true);
				}
				$this->ObTpl->parse("mainattached_blk","TPL_MAINATTACHED_BLK",true);
			}
			
		#END DISPLAY CURRENTLY ATTACHED ITEMS

		return($this->ObTpl->parse("return","TPL_LEAD_FILE"));
	}#END FUNCTION LEADBUILDER
	

	function m_leadBuilder1()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_LEAD_FILE",$this->leadTemplate);
		$this->ObTpl->set_block("TPL_LEAD_FILE","TPL_MSG_BLK", "msg_blk");
		#INTIALIZING VARIABLES
	
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("msg_blk","");

		if(!isset($this->request['leadid']) || empty($this->request['leadid']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.leadForm&msg=2");	
		}
		if(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		$this->ObTpl->set_var("TPL_VAR_LEADID",$this->request['leadid']);
		$this->ObTpl->set_var("TPL_VAR_TODATE","");
		$this->ObTpl->set_var("TPL_VAR_FROMDATE","");
		
		return($this->ObTpl->parse("return","TPL_LEAD_FILE"));
	}#END FUNCTION LEADBUILDER1
	

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEdit()
	{
		$this->errMsg="";
		if(empty($this->request['subject']))
		{
			$this->err=1;
			$this->errMsg=MSG_SUBJECT_EMPTY."<br>";
		}
		if(!empty($this->request['sid']))
		{
			#VALIDATING EXISTING OPTION TITLE
			$this->obDb->query = "SELECT iMailid_PK   FROM ".EMAILS." where vSid  = '".$this->request['sid']."'";
			$row_code = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				if($row_code[0]->iMailid_PK  !=$this->request['id'])
				{
					$this->err=1;
					$this->errMsg.=MSG_SID_EXIST."<br>";
				}
			}
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsert()
	{
		$this->errMsg="";

		if(empty($this->request['subject']))
		{
			$this->err=1;
			$this->errMsg=MSG_SUBJECT_EMPTY."<br>";
		}
		
		if(!empty($this->request['sid']))
		{
			#VALIDATING EXISTING OPTION TITLE
			$this->obDb->query = "SELECT iMailid_PK   FROM ".EMAILS." where vSid  = '".$this->request['sid']."'";
			$row_code = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				$this->err=1;
				$this->errMsg.=MSG_SID_EXIST."<br>";
			}
		}
		return $this->err;
	}
}
?>