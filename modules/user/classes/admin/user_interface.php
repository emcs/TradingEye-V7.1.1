<?php
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_userInterface
{
#CONSTRUCTOR
	function c_userInterface()
	{
		$this->err=0;
		$this->pageTplPath=MODULES_PATH."default/templates/admin/";
		$this->pageTplFile="pager.tpl.htm";
		$this->pageSize="50";
		$this->libFunc=new c_libFunctions();
	}
	function m_dspCustomers()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_USERMAIN_BLK", "usermain_blk");
		$this->ObTpl->set_block("TPL_USERMAIN_BLK","TPL_USER_BLK", "user_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_MSG_BLK", "msg_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","TPL_PAGE_BLK2", "page_blk2");
				
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("usermain_blk","");
		$this->ObTpl->set_var("user_blk","");
		$this->ObTpl->set_var("msg_blk","");
		
		$this->ObTpl->set_var("page_blk1","");
		$this->ObTpl->set_var("page_blk2","");
		
		#defining language pack variables.
		$this->ObTpl->set_var("LANG_VAR_CUSTOMERS",LANG_CUSTOMERS);
		$this->ObTpl->set_var("LANG_VAR_ADDCUSTOMER",LANG_ADDCUSTOMER);
		$this->ObTpl->set_var("LANG_VAR_SEACRH",LANG_SEARCH);
		$this->ObTpl->set_var("LANG_VAR_BY",LANG_BY);
		$this->ObTpl->set_var("LANG_VAR_FIRSTNAME",LANG_FIRSTNAME);
		$this->ObTpl->set_var("LANG_VAR_LASTNAME",LANG_LASTNAME);
		$this->ObTpl->set_var("LANG_VAR_CITY",LANG_CITY);
		$this->ObTpl->set_var("LANG_VAR_STATE",LANG_COUNTYSTATE);
		$this->ObTpl->set_var("LANG_VAR_COUNTRY",LANG_COUNTRY);
		$this->ObTpl->set_var("LANG_VAR_NEWSLETTER",LANG_NEWSLETTER);
		$this->ObTpl->set_var("LANG_VAR_SIGNUP",LANG_SIGNUP);
		$this->ObTpl->set_var("LANG_VAR_DETAILS",LANG_DETAILS);
		$this->ObTpl->set_var("LANG_VAR_STATUS",LANG_STATUS);
		$this->ObTpl->set_var("LANG_VAR_ENABLE",LANG_ENABLE);
		$this->ObTpl->set_var("LANG_VAR_DISABLE",LANG_DISABLE);
		$this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
		
		$this->ObTpl->set_var("TPL_VAR_MSG","");

		$this->request['search']=$this->libFunc->ifSet($this->request,'search',"");
		$this->request['searchtype']=$this->libFunc->ifSet($this->request,'searchtype',"");
		$this->request['todo']=$this->libFunc->ifSet($this->request,'todo',"");
		$this->request['status']=$this->libFunc->ifSet($this->request,'status',"");

		switch($this->request['todo'])
		{
			case "delete":
			if($this->request['status']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_USER_DELSUCCESS);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_USER_DELNOSUCCESS);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
			}
			break;
			case "disable":
			if($this->request['status']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_USER_DISABLESUCCESS);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_USER_DISABLENOSUCCESS);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
			}
			break;
			case "enable":
			if($this->request['status']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_USER_ENABLESUCCESS);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_USER_ENABLENOSUCCESS);
				$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
			}
			break;
		}

		$this->ObTpl->set_var("TPL_VAR_SEL1","");
		$this->ObTpl->set_var("TPL_VAR_SEL2","");
		$this->ObTpl->set_var("TPL_VAR_SEL3","");
		$this->ObTpl->set_var("TPL_VAR_SEL4","");
		$this->ObTpl->set_var("TPL_VAR_SEL5","");

		$query ="SELECT iCustmerid_PK,vEmail,vLastName,vFirstName,vState,vStateName,vCountry,";
		$query.="iMailList,tmSignupDate,iStatus  FROM ".CUSTOMERS." WHERE iRegistered='1'";
		if(!empty($this->request['search']))
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT",$this->request['search']);
			if($this->request['searchtype']=="last_name")
			{
				$query.=" AND (vLastName LIKE '%".$this->request['search']."%')";
				$this->ObTpl->set_var("TPL_VAR_SEL1","selected");
			}
			elseif($this->request['searchtype']=="first_name")
			{
				$query.=" AND (vFirstName LIKE '%".$this->request['search']."%')";
				$this->ObTpl->set_var("TPL_VAR_SEL2","selected");
			}
			elseif($this->request['searchtype']=="city")
			{
				$query.=" AND (vCity LIKE '%".$this->request['search']."%')";
				$this->ObTpl->set_var("TPL_VAR_SEL3","selected");
			}
			elseif($this->request['searchtype']=="state")
			{
				 $this->obDb->query = "SELECT iStateId_PK  FROM ".STATES." WHERE  (vStateName LIKE '%".$this->request['search']."%')";
				$row_state = $this->obDb->fetchQuery();
				$stateCount=$this->obDb->record_count;
				$stateString="2000000,";
				for($i=0;$i<$stateCount;$i++)
				{
					$stateString=$stateString.$row_state[$i]->iStateId_PK.",";
				}
					$stateString=substr($stateString,0,-1);
				$query.=" WHERE vState IN ($stateString)";
				$this->ObTpl->set_var("TPL_VAR_SEL4","selected");
			}
			elseif($this->request['searchtype']=="country")
			{	
				$this->obDb->query = "SELECT iCountryId_PK FROM ".COUNTRY." WHERE (vCountryName  LIKE '%".$this->request['search']."%') order by vCountryName";
				$row_country = $this->obDb->fetchQuery();
				$countryCount=$this->obDb->record_count;
								$stateCount=$this->obDb->record_count;
				$countryString="20000000,";
				for($i=0;$i<$countryCount;$i++)
				{
					$countryString=$countryString.$row_country[$i]->iCountryId_PK.",";
					
				}
				$countryString=substr($countryString,0,-1);
				$query.=" WHERE vCountry IN ($countryString)";
				$this->ObTpl->set_var("TPL_VAR_SEL5","selected");
			}
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_SEARCHTEXT","");
		}
		$query.=" ORDER BY vFirstName";


		if(!isset($this->request['page']))
		{
			$this->request['page']='1';
		}

		$extraStr	="action=user.home&search=".$this->request['search']."&searchtype=".$this->request['searchtype'];
		$this->ObTpl->set_var("TPL_VAR_EXTRASTRING","search=".$this->request['search']."&searchtype=".$this->request['searchtype']."&page=".$this->request['page']);
		$pn				=new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$pn->formno	=1;
		$navArr			=$pn->create($query, $this->pageSize, $extraStr);

		$pn2				=new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$pn2->formno	=2;
		$navArr2			= $pn2->create($query, $this->pageSize, $extraStr,"top");
		$row_customer=$navArr['qryRes'];
		$recordCount	=$navArr['selRecs'];
		$totalRecord	=$navArr['totalRecs'];

		$this->ObTpl->set_var("PagerBlock1","");
		$this->ObTpl->set_var("PagerBlock2","");
		$this->ObTpl->set_var("TPL_VAR_STATE","");

		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				if($row_customer[$i]->iStatus==1)
				{
					$this->ObTpl->set_var("TPL_VAR_IMG","<span class=\"statusActive\">Active</span>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMG","<span class=\"statusInactive\">Inactive</span>");
				}
				$this->ObTpl->set_var("TPL_VAR_ID",$row_customer[$i]->iCustmerid_PK);
				$this->ObTpl->set_var("TPL_VAR_EMAIL",$this->libFunc->m_displayContent($row_customer[$i]->vEmail));
				$this->ObTpl->set_var("TPL_VAR_USERNAME",$this->libFunc->m_displayContent($row_customer[$i]->vLastName).", ".$this->libFunc->m_displayContent($row_customer[$i]->vFirstName));
				if($row_customer[$i]->vState>1)	{
					$this->obDb->query = "SELECT distinct vStateName FROM ".STATES." where iStateId_PK  = '".$row_customer[$i]->vState."'";
					$row_state = $this->obDb->fetchQuery();
						if(!empty($row_state[0]->vStateName)) {
							$this->ObTpl->set_var("TPL_VAR_STATE",
							$this->libFunc->m_displayContent($row_state[0]->vStateName));
						}
				}
				elseif($row_customer[$i]->vState ="-1")	{
				  $this->ObTpl->set_var("TPL_VAR_STATE","");
				}
				else {
					if(!empty($row_customer[$i]->vStateName)){
						$this->ObTpl->set_var("TPL_VAR_STATE",
						$this->libFunc->m_displayContent($row_customer[$i]->vStateName));
					}
				}
				$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$row_customer[$i]->vCountry."' order by vCountryName";
				$row_country = $this->obDb->fetchQuery();
				if(!empty($row_country[0]->vCountryName))
				{
					$this->ObTpl->set_var("TPL_VAR_COUNTRY",
					$this->libFunc->m_displayContent($row_country[0]->vCountryName));
				}

				if($row_customer[$i]->iMailList==1)
				{
					$maillist="HTML";
				}
				elseif($row_customer[$i]->iMailList==2)
				{
					$maillist="Plain text ";
				}
				else
				{
					$maillist="None";
				}
				$this->ObTpl->set_var("TPL_VAR_NEWSLETTER",$maillist);
				$this->ObTpl->set_var("TPL_VAR_SIGNUPDATE", $this->libFunc->dateFormat1($row_customer[$i]->tmSignupDate));
				$this->ObTpl->parse("user_blk","TPL_USER_BLK",true);
			}

				$this->ObTpl->parse("usermain_blk","TPL_USERMAIN_BLK");
	
				$this->ObTpl->set_var("PagerBlock1", $navArr['pnContents']);
				$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
				$this->ObTpl->parse("page_blk2","TPL_PAGE_BLK2");
				$this->ObTpl->parse("countmsg_blk","TPL_COUNTMSG_BLK");
		}#endif
		$this->ObTpl->set_var("TPL_VAR_RECORDCOUNT",$totalRecord." records found");
			

		return($this->ObTpl->parse("return","TPL_USER_FILE"));
	}#ef

	#FUNCTION TO DISPLAY USER FORM
	function m_dspUserForm()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_block("TPL_USER_FILE","DSPMSG_BLK", "msg_blk");
		$this->ObTpl->set_block("TPL_USER_FILE","countryblk","countryblks");
		$this->ObTpl->set_block("TPL_USER_FILE","BillCountry","nBillCountry");
		$this->ObTpl->set_block("TPL_USER_FILE","stateblk","stateblks");
		$this->ObTpl->set_var("TPL_USERURL",SITE_URL."user/");
		
		#defining language pack variables.
		$this->ObTpl->set_var("LANG_VAR_CUSTOMERS",LANG_CUSTOMERS);
		$this->ObTpl->set_var("LANG_VAR_PASSWORD",LANG_PASSWORD);
		$this->ObTpl->set_var("LANG_VAR_FIRSTNAME",LANG_FIRSTNAME);
		$this->ObTpl->set_var("LANG_VAR_LASTNAME",LANG_LASTNAME);
		$this->ObTpl->set_var("LANG_VAR_COMPANY",LANG_COMPANY);
		$this->ObTpl->set_var("LANG_VAR_EMAIL",LANG_EMAILTXT);
		$this->ObTpl->set_var("LANG_VAR_ADDRESS1",LANG_ADDRESS1);
		$this->ObTpl->set_var("LANG_VAR_ADDRESS2",LANG_ADDRESS2);
		$this->ObTpl->set_var("LANG_VAR_CITY",LANG_CITY);
		$this->ObTpl->set_var("LANG_VAR_STATE",LANG_COUNTYSTATE);
		$this->ObTpl->set_var("LANG_VAR_STATEOTHER",LANG_COUNTYSTATEOTHER);
		$this->ObTpl->set_var("LANG_VAR_COUNTRY",LANG_COUNTRY);
		$this->ObTpl->set_var("LANG_VAR_POSTCODE",LANG_POSTCODE);
		$this->ObTpl->set_var("LANG_VAR_TELEPHONE",LANG_TELEPHONE);
		$this->ObTpl->set_var("LANG_VAR_WEBSITE",LANG_WEBSITE);
		$this->ObTpl->set_var("LANG_VAR_NEWSLETTER",LANG_NEWSLETTER);
		$this->ObTpl->set_var("LANG_VAR_HTML",LANG_HTML);
		$this->ObTpl->set_var("LANG_VAR_PLAIN",LANG_PLAIN);
		$this->ObTpl->set_var("LANG_VAR_NONE",LANG_NONE);
		$this->ObTpl->set_var("LANG_VAR_MEMPOINTS",LANG_MEMPOINTS);
		$this->ObTpl->set_var("LANG_VAR_STATUS",LANG_STATUS);
		$this->ObTpl->set_var("LANG_VAR_LBLCUSTOMERTYPE",LANG_LBLCUSTOMERTYPE);
		
		
		
		#INTIALIZING
		$row_customer[0]->vFirstName  = "";
		$row_customer[0]->vLastName  ="";
		$row_customer[0]->vEmail  = "";
		$row_customer[0]->vPassword  = "";
		$row_customer[0]->vPhone  = "";
		$row_customer[0]->vCompany = "";
		$row_customer[0]->vAddress1 = "";
		$row_customer[0]->vAddress2 = "";
		$row_customer[0]->vState ="";
		$row_customer[0]->vStateName="";
		$row_customer[0]->vCity = "";
		$row_customer[0]->vCountry = "";	
		$row_customer[0]->vZip = "";	
		$row_customer[0]->vHomePage  = "";	
		$row_customer[0]->fMemberPoints = "";
		$row_customer[0]->iMailList = "";
		$row_customer[0]->iStatus = "1";
        $row_customer[0]->vRetail = "";

		$this->ObTpl->set_var("msg_blk","");

		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/

		if(count($_POST) > 0)
		{
			if(isset($this->request["first_name"]))
				$row_customer[0]->vFirstName  = $this->request["first_name"];
			if(isset($this->request["password"]))
				$row_customer[0]->vPassword  = $this->request["password"];
			if(isset($this->request["last_name"]))
				$row_customer[0]->vLastName  = $this->request["last_name"];
	
			if(isset($this->request["company"]))
				$row_customer[0]->vCompany  = $this->request["company"];
			if(isset($this->request["txtemail"]))
				$row_customer[0]->vEmail  = $this->request["txtemail"];
			if(isset($this->request["address1"]))
				$row_customer[0]->vAddress1  = $this->request["address1"];
			if(isset($this->request["address2"]))
				$row_customer[0]->vAddress2  = $this->request["address2"];
			if(isset($this->request["city"]))
				$row_customer[0]->vCity = $this->request["city"];
			if(isset($this->request["bill_state_id"]))
				$row_customer[0]->vState = $this->request["bill_state_id"];	
			if(isset($this->request["bill_state"]))
				$row_customer[0]->vStateName  = $this->request["bill_state"];	
			if(isset($this->request["zip"]))
				$row_customer[0]->vZip  = $this->request["zip"];	
			if(isset($this->request["bill_country_id"]))
				$row_customer[0]->vCountry  = $this->request["bill_country_id"];	
			if(isset($this->request["phone"]))
				$row_customer[0]->vPhone = $this->request["phone"];	
			if(isset($this->request["homepage"]))
				$row_customer[0]->vHomePage  = $this->request["homepage"];	
			if(isset($this->request["mail_list"]))
				$row_customer[0]->iMailList  = $this->request["mail_list"];	
			if(isset($this->request["member_points"]))
				$row_customer[0]->fMemberPoints  = $this->request["member_points"];	
			if(isset($this->request["iStatus"]))
				$row_customer[0]->iStatus = $this->request["status"];
			if(isset($this->request["customertype"]))
				$row_customer[0]->vRetail = $this->request["customertype"];			
			else
				$row_customer[0]->iStatus = "";
		}
	
		#DISPLAYING MESSAGES
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","DSPMSG_BLK");	
		}


		#IF EDIT MODE SELECTED

		if(!empty($this->request['id']))
		{
			if(!isset($this->request['msg']) || empty($this->request['msg']))
			{
				$this->request['id']=intval($this->request['id']);
				$this->obDb->query = "SELECT * FROM ".CUSTOMERS." WHERE iCustmerid_PK  ='".$this->request['id']."' AND iRegistered='1'";
				$row_customer=$this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount!=1)
				{
					$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=user.status");
					exit;
				}
			}

			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);
			$this->ObTpl->set_var("TPL_VAR_FUNCTION", "Edit");
			#HANDLING BLOCKS		
			
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_EDIT_BTN);
		}	
		else #IF ADD
		{
			$this->ObTpl->set_var("TPL_VAR_MODE","Add");
			$this->ObTpl->set_var("TPL_VAR_ID","");
			$this->ObTpl->set_var("TPL_VAR_FUNCTION", "Add New");
			
			$this->ObTpl->set_var("TPL_VAR_BTNLBL",LBL_ADD_BTN);
		}	

				
		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("FORM_URL", SITE_URL."user/adminindex.php?action=user.updateUser");
		
		$this->obDb->query = "SELECT iStateId_PK, vStateName FROM ".STATES." ORDER BY vStateName";
		$row_state = $this->obDb->fetchQuery();
		$row_state_count = $this->obDb->record_count;
		
		$this->obDb->query = "SELECT iCountryId_PK, vCountryName, vShortName FROM  ".COUNTRY." ORDER BY iSortFlag,vCountryName";
		$row_country = $this->obDb->fetchQuery();
		$row_country_count = $this->obDb->record_count;

		# Loading billing country list		
		for($i=0;$i<$row_country_count;$i++)
		{
			$this->ObTpl->set_var("k", $row_country[$i]->iCountryId_PK);
			$this->ObTpl->parse('countryblks','countryblk',true);
			$this->ObTpl->set_var("TPL_COUNTRY_VALUE", $row_country[$i]->iCountryId_PK);
			
			if($row_customer[0]->vCountry> 0)
			{
				if($row_customer[0]->vCountry == $row_country[$i]->iCountryId_PK)
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
				else
					$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "");
			}
			else
			{
					$row_customer[0]->vCountry = SELECTED_COUNTRY;
					if($row_country[$i]->iCountryId_PK==$row_customer[0]->vCountry)
					{
						$this->ObTpl->set_var("BILL_COUNTRY_SELECT", "selected");
					}	
			}	
		$this->ObTpl->set_var("TPL_COUNTRY_NAME",$this->libFunc->m_displayContent($row_country[$i]->vCountryName));
			$this->ObTpl->parse("nBillCountry","BillCountry",true);
		}
	   
	    if($row_customer[0]->vCountry != '')
			$this->ObTpl->set_var('selbillcountid',$row_customer[0]->vCountry);
		else
			$this->ObTpl->set_var('selbillcountid',251);

		if($row_customer[0]->vState != '')
			$this->ObTpl->set_var('selbillstateid',$row_customer[0]->vState);
		else
			$this->ObTpl->set_var('selbillstateid',0);
		
		# Loading the state list here
		$this->obDb->query = "SELECT C.iCountryId_PK as cid,S.iStateId_PK as sid,S.vStateName as statename FROM ".COUNTRY." C,".STATES." S WHERE S.iCountryId_FK=C.iCountryId_PK ORDER BY C.vCountryName,S.vStateName";
		$cRes = $this->obDb->fetchQuery();
		$country_count = $this->obDb->record_count;

		if($country_count == 0)
		{
			$this->ObTpl->set_var("countryblks", "");
			$this->ObTpl->set_var("stateblks", "");
		}
		else
		{
		$loopid=0;
			for($i=0;$i<$country_count;$i++)
			{
				if($cRes[$i]->cid==$loopid)
				{
					$stateCnt++;
				}
				else
				{
					$loopid=$cRes[$i]->cid;
					$stateCnt=0;
				}
				$this->ObTpl->set_var("i", $cRes[$i]->cid);
				$this->ObTpl->set_var("j", $stateCnt);
				$this->ObTpl->set_var("stateName",$cRes[$i]->statename);
				$this->ObTpl->set_var("stateVal",$cRes[$i]->sid);
				$this->ObTpl->parse('stateblks','stateblk',true);
			}
		}


		#ASSIGNING FORM VARAIABLES

		$this->ObTpl->set_var("TPL_VAR_FNAME", $this->libFunc->m_displayContent($row_customer[0]->vFirstName));
		$this->ObTpl->set_var("TPL_VAR_LNAME", $this->libFunc->m_displayContent($row_customer[0]->vLastName));
//		echo $row_customer[0]->vEmail;
		$this->ObTpl->set_var("TPL_VAR_EMAIL", $this->libFunc->m_displayContent($row_customer[0]->vEmail));
		$this->ObTpl->set_var("TPL_VAR_PASS", $this->libFunc->m_displayContent($row_customer[0]->vPassword));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS1", $this->libFunc->m_displayContent($row_customer[0]->vAddress1 ));
		$this->ObTpl->set_var("TPL_VAR_ADDRESS2", $this->libFunc->m_displayContent($row_customer[0]->vAddress2 ));
		$this->ObTpl->set_var("TPL_VAR_CITY", $this->libFunc->m_displayContent($row_customer[0]->vCity));

		$this->ObTpl->set_var("TPL_VAR_STATE",
			$this->libFunc->m_displayContent($row_customer[0]->vState ));
		if($row_customer[0]->vState>1)
			{
				$this->ObTpl->set_var("BILL_STATE","");
			}
			else
			{
				$this->ObTpl->set_var("BILL_STATE",
				$this->libFunc->m_displayContent($row_customer[0]->vStateName));
			}
		$this->ObTpl->set_var("TPL_VAR_COUNTRY",
			$this->libFunc->m_displayContent($row_customer[0]->vCountry ));
		$this->ObTpl->set_var("TPL_VAR_ZIP",
			$this->libFunc->m_displayContent($row_customer[0]->vZip));
		$this->ObTpl->set_var("TPL_VAR_COMPANY",
			$this->libFunc->m_displayContent($row_customer[0]->vCompany));
		$this->ObTpl->set_var("TPL_VAR_PHONE",
			$this->libFunc->m_displayContent($row_customer[0]->vPhone));
		$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",
			$this->libFunc->m_displayContent($row_customer[0]->vHomePage));
			if($row_customer[0]->iMailList==1)
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK1","checked");
			}
			elseif($row_customer[0]->iMailList==2)
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK2","checked");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_CHECK3","checked");
			}
		
		$this->ObTpl->set_var("TPL_VAR_MPOINTS",
			$this->libFunc->m_displayContent($row_customer[0]->fMemberPoints));
		if($row_customer[0]->vRetail=='t')
		{
			$this->ObTpl->set_var("TPL_VAR_CUSTOMERTYPE","checked='checked'");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_CUSTOMERTYPE","");					
		}	
		
		if($row_customer[0]->iStatus==1)
		{
			$this->ObTpl->set_var("TPL_VAR_STATUS","checked='checked'");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_STATUS","");					
		}	
		
		return $this->ObTpl->parse("return","TPL_USER_FILE");
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditUser()
	{
		if(empty($this->request['first_name']))
		{
			$this->err=1;
			$this->errMsg=MSG_FIRST_EMPTY."<br>";
		}
		if(empty($this->request['txtemail']))
		{
			$this->err=1;
			$this->errMsg=MSG_EMAIL_EMPTY."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iCustmerid_PK FROM ".CUSTOMERS." where vEmail = '".$this->request['txtemail']."' AND iRegistered='1'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iCustmerid_PK !=$this->request['id'])
			{
			$this->err=1;
			$this->errMsg=MSG_EMAIL_EXIST."<br>";
			}
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsertUser()
	{
		if(empty($this->request['first_name']))
		{
			$this->err=1;
			$this->errMsg=MSG_FIRST_EMPTY."<br>";
		}
		if(empty($this->request['txtemail']))
		{
			$this->err=1;
			$this->errMsg=MSG_EMAIL_EMPTY."<br>";
		}
	
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iCustmerid_PK FROM ".CUSTOMERS." where vEmail = '".$this->request['txtemail']."' AND iRegistered='1'";

		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->err=1;
			$this->errMsg=MSG_EMAIL_EXIST."<br>";
		}
		
		return $this->err;
	}

	#FUNCTION TO DISPLAY STATUS
	function m_userStatus()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		return($this->ObTpl->parse("return","TPL_USER_FILE"));
	}#END FUNCTION

	#FUNSTION TO DISPLAY CUSTOMER DETAILS
	function dspUserDetails()
	{
		if(!isset($this->request['id']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=user.home");
		}
		else
		{
			$this->request['id']=intval($this->request['id']);
		}

		if($this->request['id']<1)
		{
			#URL TEMPER
			$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=user.home");
		}
		else
		{
			$libFunc=new c_libFunctions();
			#INTIALIZING TEMPLATES
			$this->ObTpl=new template();
			$this->ObTpl->set_var("TPL_VAR_MSG","");
			$this->ObTpl->set_file("TPL_USER_FILE", $this->userTemplate);
			$this->ObTpl->set_block("TPL_USER_FILE","TPL_MAINORDER_BLK", "mainorder_blk");
			$this->ObTpl->set_block("TPL_MAINORDER_BLK","TPL_ORDERS_BLK", "orders_blk");
			$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
			$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
			$this->ObTpl->set_var("mainorder_blk","");
			$this->ObTpl->set_var("orders_blk","");
			
			#defining language pack variables.
			$this->ObTpl->set_var("LANG_VAR_FIRSTNAME",LANG_FIRSTNAME);
			$this->ObTpl->set_var("LANG_VAR_LASTNAME",LANG_LASTNAME);
			$this->ObTpl->set_var("LANG_VAR_COMPANY",LANG_COMPANY);
			$this->ObTpl->set_var("LANG_VAR_EMAIL",LANG_EMAILTXT);
			$this->ObTpl->set_var("LANG_VAR_ADDRESS1",LANG_ADDRESS1);
			$this->ObTpl->set_var("LANG_VAR_ADDRESS2",LANG_ADDRESS2);
			$this->ObTpl->set_var("LANG_VAR_CITY",LANG_CITY);
			$this->ObTpl->set_var("LANG_VAR_STATE",LANG_COUNTYSTATE);
			$this->ObTpl->set_var("LANG_VAR_COUNTRY",LANG_COUNTRY);
			$this->ObTpl->set_var("LANG_VAR_POSTCODE",LANG_POSTCODE);
			$this->ObTpl->set_var("LANG_VAR_TELEPHONE",LANG_TELEPHONE);
			$this->ObTpl->set_var("LANG_VAR_WEBSITE",LANG_WEBSITE);
			$this->ObTpl->set_var("LANG_VAR_NEWSLETTER",LANG_NEWSLETTER);
			$this->ObTpl->set_var("LANG_VAR_HTML",LANG_HTML);
			$this->ObTpl->set_var("LANG_VAR_PLAIN",LANG_PLAIN);
			$this->ObTpl->set_var("LANG_VAR_NONE",LANG_NONE);
			$this->ObTpl->set_var("LANG_VAR_MEMPOINTS",LANG_MEMPOINTS);
			$this->ObTpl->set_var("LANG_VAR_STATUS",LANG_STATUS);
			$this->ObTpl->set_var("LANG_VAR_SIGNUPDATE",LANG_SIGNUPDATE);
			$this->ObTpl->set_var("LANG_VAR_EMAILPASSWORD",LANG_EMAILPASSWORD);
			$this->ObTpl->set_var("LANG_VAR_CUSTOMERORDERS",LANG_CUSTOMERORDERS);
			$this->ObTpl->set_var("LANG_VAR_ORDERDATE",LANG_ORDERDATE);
			$this->ObTpl->set_var("LANG_VAR_INVOICE",LANG_INVOICE);
			$this->ObTpl->set_var("LANG_VAR_DETAILS",LANG_DETAILS);
			$this->ObTpl->set_var("LANG_VAR_ORDERSTODATE",LANG_ORDERSTODATE);
			$this->ObTpl->set_var("LANG_VAR_CUSTOMERDETAILS",LANG_CUSTOMERDETAILS);
			$this->ObTpl->set_var("LANG_VAR_EDITCUSTOMER",LANG_EDITCUSTOMERINFO);
			$this->ObTpl->set_var("LANG_VAR_LBLCUSTOMERTYPE",LANG_LBLCUSTOMERTYPE);

			if(isset($this->request['msg']) && $this->request['msg']==2)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",LOGIN_DETAILS_SENT);
			}
			$this->ObTpl->set_var("TPL_VAR_USERID",$this->request['id']);
			#QUERY DATABASE
			$this->obDb->query = "SELECT * FROM ".CUSTOMERS." where iCustmerid_PK = '".$this->request['id']."' AND iRegistered='1'";
			$row_customer = $this->obDb->fetchQuery();
			$recordCount=$this->obDb->record_count;
			if($recordCount!=1)
			{
				$this->libFunc->m_mosRedirect(SITE_URL."user/adminindex.php?action=user.status");
				exit;
			}
			$this->ObTpl->set_var("TPL_VAR_FNAME", $this->libFunc->m_displayContent($row_customer[0]->vFirstName));
			$this->ObTpl->set_var("TPL_VAR_LNAME", $this->libFunc->m_displayContent($row_customer[0]->vLastName));
			$this->ObTpl->set_var("TPL_VAR_EMAIL", $this->libFunc->m_displayContent($row_customer[0]->vEmail));
			$this->ObTpl->set_var("TPL_VAR_PASS", $this->libFunc->m_displayContent($row_customer[0]->vPassword));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS1", $this->libFunc->m_displayContent($row_customer[0]->vAddress1 ));
			$this->ObTpl->set_var("TPL_VAR_ADDRESS2", $this->libFunc->m_displayContent($row_customer[0]->vAddress2 ));
			$this->ObTpl->set_var("TPL_VAR_CITY", $this->libFunc->m_displayContent($row_customer[0]->vCity));
			if($row_customer[0]->vState>1)
			{
				$this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$row_customer[0]->vState."'";
				$row_state = $this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_STATE",
				$this->libFunc->m_displayContent($row_state[0]->vStateName));
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_STATE",
				$this->libFunc->m_displayContent($row_customer[0]->vStateName));
			}
			$this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$row_customer[0]->vCountry."' order by vCountryName";
				$row_country = $this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_COUNTRY",
				$this->libFunc->m_displayContent($row_country[0]->vCountryName));
			$this->ObTpl->set_var("TPL_VAR_ZIP",
				$this->libFunc->m_displayContent($row_customer[0]->vZip));
			$this->ObTpl->set_var("TPL_VAR_COMPANY",
				$this->libFunc->m_displayContent($row_customer[0]->vCompany));
			$this->ObTpl->set_var("TPL_VAR_PHONE",
				$this->libFunc->m_displayContent($row_customer[0]->vPhone));
			$this->ObTpl->set_var("TPL_VAR_HOMEPAGE",
				$this->libFunc->m_displayContent($row_customer[0]->vHomePage));
			if($row_customer[0]->iMailList==1)
			{
				$maillist="HTML";
			}
			elseif($row_customer[0]->iMailList==2)
			{
				$maillist="Plain text ";
			}
			else
			{
				$maillist="None";
			}
			$this->ObTpl->set_var("TPL_VAR_NEWSLETTER",$maillist);
			$this->ObTpl->set_var("TPL_VAR_SIGNUPDATE",
				$this->libFunc->dateFormat1($row_customer[0]->tmSignupDate));
			$this->ObTpl->set_var("TPL_VAR_MPOINTS",
				$this->libFunc->m_displayContent($row_customer[0]->fMemberPoints));
				
			if ($row_customer[0]->vRetail=='n'){
				$this->ObTpl->set_var("TPL_VAR_CUSTOMERTYPE","Normal Customer");
			}else{
				$this->ObTpl->set_var("TPL_VAR_CUSTOMERTYPE","Trade Customer");
			}
				
			if($row_customer[0]->iStatus==1)
			{
				$this->ObTpl->set_var("TPL_VAR_STATUS","checked='checked'");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_STATUS","");					
			}	
			
			$this->obDb->query = "SELECT iOrderid_PK,iInvoice,tmOrderDate  FROM ".ORDERS." WHERE (iCustomerid_FK  = '".$this->request['id']."' AND iOrderStatus='1')  ORDER BY iInvoice DESC";
			$row_order = $this->obDb->fetchQuery();
			$orderCount=$this->obDb->record_count;
			$this->ObTpl->set_var("TPL_VAR_ORDERCOUNT",$orderCount);
			if($orderCount>0)
			{
				for($i=0;$i<$orderCount;$i++)
				{
					$this->ObTpl->set_var("TPL_VAR_ORDERID",$row_order[$i]->iOrderid_PK);
					$this->ObTpl->set_var("TPL_VAR_INVOICE",
					$this->libFunc->m_displayContent($row_order[$i]->iInvoice));
					$this->ObTpl->set_var("TPL_VAR_ORDERDATE",
					$this->libFunc->dateFormat1($row_order[$i]->tmOrderDate));
					$this->ObTpl->parse("orders_blk","TPL_ORDERS_BLK",true);
				}
				$this->ObTpl->parse("mainorder_blk","TPL_MAINORDER_BLK");
			}
			return($this->ObTpl->parse("return","TPL_USER_FILE"));
		}#END ELSE LOOP	
	}#END FUNCTION


	function m_sendPassword()
	{
		$comFunc=new c_commonFunctions();
		$comFunc->obDb=$this->obDb;
		$this->obDb->query= "select iCustmerid_PK,vFirstName,vEmail,tmSignupDate FROM ".CUSTOMERS." WHERE vEmail = '".$this->request['email']."' AND iRegistered='1'";
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		if(isset($this->cart))
		{
			$action="ecom/index.php?action=checkout.loginForm";
		}
		else
		{
			$action="user/index.php?action=user.loginForm";
		}
		if($rCount>0) 
		{
		$requesttime = time();
		$recoveryid = md5($qryResult[0]->iCustmerid_PK . $qryResult[0]->vFirstName . $qryResult[0]->vEmail . $qryResult[0]->tmSignupDate . $requesttime);
		$this->obDb->query="UPDATE " . CUSTOMERS . " SET vRecovery='" . $recoveryid . "',tRequestTime='" . $requesttime . "' WHERE iCustmerid_PK='" . $qryResult[0]->iCustmerid_PK . "' AND iRegistered='1'";
		$this->obDb->updateQuery();
	//	$uniqID=uniqid (3);
			$message ="Hi ".$this->libFunc->m_displayContent($qryResult[0]->vFirstName);
			$message .="<br><br>You requested to reset your login details for Username:&nbsp;".$qryResult[0]->vEmail;
			$message .="<br><br>You can do so by visiting this <a href='".SITE_URL."user/index.php?action=user.recover&id=". $recoveryid . "'>link</a>.";
			$message .="<br>If the link is not clickable, copy and paste this url into your browser: " . SITE_URL."user/index.php?action=user.recover&id=". $recoveryid;
			$message .="<br>You must click the above password within 24 hours of your request or the link will be deactivated.";
			$message .="<br><br>If you didn't request to reset your password, then please disregard this message.";
			$message .="<br><br>Best Regards,";
			$message .="<br><a href='".SITE_URL."'>".SITE_NAME."</a>";
			$obMail = new htmlMimeMail();
			$obMail->setReturnPath(ADMIN_EMAIL);
			$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
			$obMail->setSubject("Login details from ".SITE_NAME);
			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);
			$obMail->buildMessage();
			$result = $obMail->send(array($qryResult[0]->vEmail));
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL.$action."&mode=sent&msg=1");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
		else
		{	
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL.$action."&mode=lost&msg=2");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
	
	}
}#END CLASS
?>