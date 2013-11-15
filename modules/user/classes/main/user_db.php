<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
	class c_userDb
	{
		#CONSTRUCTOR
		function c_userDb()
		{
			$this->libFunc=new c_libFunctions();
		}
	
		function m_newsletter(){
		
			$error = 0;
			if (isset($this->request['news']))
				{
				$this->obDb->query = "SELECT vEmail FROM ".NEWSLETTERS;
				$vEmail = $this->obDb->fetchQuery();
				$emailcount = $this->obDb->record_count;
				
				$validate = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $this->request['news']);
				if($validate === 0 or $validate === FALSE){
					$error = 3;
					$_SESSION['newslettererror']= 'Please input a valid email';
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=contactus.thanks");
					$this->libFunc->m_mosRedirect($retUrl);	 
				}
				for ($i=0;$i<$emailcount;$i++){
					if ($vEmail[$i]->vEmail ==$this->request['news'])
					{	
					$error = 1;
					}
				}
				
				if ($this->request['news']=='Email'){
					$error =2;
				}
				
				
				
				if ($error!=1 && $error!=2 && $error!=3){
				$this->obDb->query="INSERT INTO ". NEWSLETTERS. "(vEmail) VALUES ('".$this->request['news']."')";	
				$this->obDb->updateQuery();
				
				$_SESSION['newslettererror'] = "Thank you very much for subscribing";	
				}else {
					if ($error==1){$_SESSION['newslettererror']= 'Email already exist';}
					elseif ($error==2){$_SESSION['newslettererror']= 'Please enter an email';} 
					elseif($error==3){$_SESSION['newslettererror']= 'Please input a valid email';}
				}
				
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=contactus.thanks");
				$this->libFunc->m_mosRedirect($retUrl);	 
			}
		}
	
		#NEW CUSTOMER ADD
		function m_insertUser()
		{
			$_SESSION['referer']=$this->libFunc->ifSet($_SESSION,'referer',"");
			$comFunc=new c_commonFunctions();
			$comFunc->obDb=$this->obDb;
			$timestamp=time();
			$status=$this->libFunc->ifSet($this->request,"status","");

			if(!isset($this->request['bill_state_id']) || empty($this->request['bill_state_id']))
			{
				$this->request['bill_state_id']="";
			}
			else
			{
				$this->request['bill_state']="";
			}
			$this->obDb->query= "select iCustmerid_PK FROM ".CUSTOMERS." WHERE vEmail = '".$this->request['txtemail']."'";
			$qryResult = $this->obDb->fetchQuery();
			$rCount=$this->obDb->record_count;
			if($rCount == 1)
			{
				if($this->request['customertype'] == "trade"){
				$this->obDb->query="UPDATE ".CUSTOMERS." SET vFirstName='".$this->libFunc->m_addToDB($this->request['first_name'])."',vLastName='".$this->libFunc->m_addToDB($this->request['last_name'])."',vPassword=PASSWORD('".$this->libFunc->m_addToDB($this->request['txtpassword'])."') ,vAddress1='".$this->libFunc->m_addToDB($this->request['address1'])."',vAddress2='".$this->libFunc->m_addToDB($this->request['address2'])."',vCity='".$this->libFunc->m_addToDB($this->request['city'])."',vState='".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',vStateName='".$this->libFunc->m_addToDB($this->request['bill_state'])."',vCountry='".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',vZip='".$this->libFunc->m_addToDB($this->request['zip'])."',vCompany='".$this->libFunc->m_addToDB($this->request['company'])."',vRetail='".$this->libFunc->m_addToDB($this->request['customertype'])."',vPhone='".$this->libFunc->m_addToDB($this->request['phone'])."',vHomePage='".$this->libFunc->m_addToDB($this->request['homepage'])."',iMailList='".$this->request['mail_list']."',tmSignupDate='$timestamp',iRegistered='1' WHERE vEmail='".$this->request['txtemail']."'";
				

				$this->obDb->updateQuery();
				$subObjId=$this->obDb->last_insert_id;
				$comFunc->m_sendDetails_trade($this->request['txtemail'],$this->request['txtpassword']);
				}
				else {
				$this->obDb->query="UPDATE ".CUSTOMERS." SET vFirstName='".$this->libFunc->m_addToDB($this->request['first_name'])."',vLastName='".$this->libFunc->m_addToDB($this->request['last_name'])."',vPassword=PASSWORD('".$this->libFunc->m_addToDB($this->request['txtpassword'])."') ,vAddress1='".$this->libFunc->m_addToDB($this->request['address1'])."',vAddress2='".$this->libFunc->m_addToDB($this->request['address2'])."',vCity='".$this->libFunc->m_addToDB($this->request['city'])."',vState='".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',vStateName='".$this->libFunc->m_addToDB($this->request['bill_state'])."',vCountry='".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',vZip='".$this->libFunc->m_addToDB($this->request['zip'])."',vCompany='".$this->libFunc->m_addToDB($this->request['company'])."',vRetail='".$this->libFunc->m_addToDB($this->request['customertype'])."',vPhone='".$this->libFunc->m_addToDB($this->request['phone'])."',vHomePage='".$this->libFunc->m_addToDB($this->request['homepage'])."',iMailList='".$this->request['mail_list']."',tmSignupDate='$timestamp',iRegistered='1' WHERE vEmail='".$this->request['txtemail']."'";

				$this->obDb->updateQuery();
				$subObjId=$this->obDb->last_insert_id;
				$comFunc->m_sendDetails($this->request['txtemail'],$this->request['txtpassword']);
				$_SESSION['userid']=$subObjId;
				$_SESSION['username']=$this->request['first_name'];
				}
			}
			else
			{
				#INSERTING CUSTOMER
				if($this->request['customertype'] == "trade"){
					$this->obDb->query="INSERT INTO ".CUSTOMERS."
					(iCustmerid_PK,vFirstName,vLastName,
					 vEmail ,vPassword ,vAddress1,vAddress2,vCity,
					vState,vStateName,vCountry,vZip,vCompany,vRetail,vPhone ,
					 vHomePage ,iMailList,tmSignupDate,iStatus,iRegistered) 
					values('',
					'".$this->libFunc->m_addToDB($this->request['first_name'])."',
					'".$this->libFunc->m_addToDB($this->request['last_name'])."',
					'".$this->libFunc->m_addToDB($this->request['txtemail'])."',
					PASSWORD('".$this->libFunc->m_addToDB($this->request['txtpassword'])."'),
					'".$this->libFunc->m_addToDB($this->request['address1'])."',
					'".$this->libFunc->m_addToDB($this->request['address2'])."',
					'".$this->libFunc->m_addToDB($this->request['city'])."',
					'".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
					'".$this->libFunc->m_addToDB($this->request['bill_state'])."',
					'".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
					'".$this->libFunc->m_addToDB($this->request['zip'])."',
					'".$this->libFunc->m_addToDB($this->request['company'])."',
					'".$this->libFunc->m_addToDB($this->request['customertype'])."',
					'".$this->libFunc->m_addToDB($this->request['phone'])."',
					'".$this->libFunc->m_addToDB($this->request['homepage'])."',
					'".$this->request['mail_list']."',	
					'$timestamp','1','1')";
					$this->obDb->updateQuery();
					$subObjId=$this->obDb->last_insert_id;
					//$comFunc->m_sendDetails_trade($this->request['txtemail'],$this->request['txtpassword']);
				}else{
					$this->obDb->query="INSERT INTO ".CUSTOMERS."
					(iCustmerid_PK,vFirstName,vLastName,
					vEmail ,vPassword ,vAddress1,vAddress2,vCity,
					vState,vStateName,vCountry,vZip,vCompany,vRetail,vPhone ,
					 vHomePage ,iMailList,tmSignupDate,iRegistered) 
					values('',
					'".$this->libFunc->m_addToDB($this->request['first_name'])."',
					'".$this->libFunc->m_addToDB($this->request['last_name'])."',
					'".$this->libFunc->m_addToDB($this->request['txtemail'])."',
					PASSWORD('".$this->libFunc->m_addToDB($this->request['txtpassword'])."'),
					'".$this->libFunc->m_addToDB($this->request['address1'])."',
					'".$this->libFunc->m_addToDB($this->request['address2'])."',
					'".$this->libFunc->m_addToDB($this->request['city'])."',
					'".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
					'".$this->libFunc->m_addToDB($this->request['bill_state'])."',
					'".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
					'".$this->libFunc->m_addToDB($this->request['zip'])."',
					'".$this->libFunc->m_addToDB($this->request['company'])."',
					'".$this->libFunc->m_addToDB($this->request['customertype'])."',
					'".$this->libFunc->m_addToDB($this->request['phone'])."',
					'".$this->libFunc->m_addToDB($this->request['homepage'])."',
					'".$this->request['mail_list']."',	
					'$timestamp','1')";
					$this->obDb->updateQuery();
					$subObjId=$this->obDb->last_insert_id;
					//$comFunc->m_sendDetails($this->request['txtemail'],$this->request['txtpassword']);
					$_SESSION['userid']=$subObjId;
					$_SESSION['username']=$this->request['first_name'];
				}
			}
			if(!empty($_SESSION['referer']))
			{
				if($this->request['customertype'] == "trade"){
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.loginForm&tc=s");
					$this->libFunc->m_mosRedirect($retUrl);	
				}else{
					$this->libFunc->m_mosRedirect($_SESSION['referer']);
				}
			}
			else
			{
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
				$this->libFunc->m_mosRedirect($retUrl);	
			}
			exit;						
		}#END INSERT CUSTOMER

		function m_updateUser()
		{
			$this->request['mail_list']=$this->libFunc->ifSet($this->request,"mail_list",0);

			if(!isset($this->request['bill_state_id']) || empty($this->request['bill_state_id']))
			{
				$this->request['bill_state_id']="";
			}
			else
			{
				$this->request['bill_state']="";
			}

			#UPDATING CUSTOMER
			$this->obDb->query="UPDATE ".CUSTOMERS." SET 
			vFirstName='".$this->libFunc->m_addToDB($this->request['first_name'])."',
			vLastName='".$this->libFunc->m_addToDB($this->request['last_name'])."',
			vAddress1='".$this->libFunc->m_addToDB($this->request['address1'])."',
			vAddress2='".$this->libFunc->m_addToDB($this->request['address2'])."',
			vCity='".$this->libFunc->m_addToDB($this->request['city'])."',
			vState='".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
			vStateName='".$this->libFunc->m_addToDB($this->request['bill_state'])."',
			vCountry='".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
			vRetail='".$this->libFunc->m_addToDB($this->request['customertype'])."',
			vZip='".$this->libFunc->m_addToDB($this->request['zip'])."',
			vCompany ='".$this->libFunc->m_addToDB($this->request['company'])."',
			vPhone ='".$this->libFunc->m_addToDB($this->request['phone'])."',
			vHomePage ='".$this->libFunc->m_addToDB($this->request['homepage'])."',
			iMailList='".$this->request['mail_list']."'
			WHERE (iCustmerid_PK ='".$_SESSION['userid']."')";
			$this->obDb->updateQuery();
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
			$this->libFunc->m_mosRedirect($retUrl);	
			exit;	
		}

		# FUNCTION TO UPDATE PASSWORD
		function m_updatePass()
		{
			$this->obDb->query="UPDATE ".CUSTOMERS." SET vPassword='".$this->libFunc->m_addToDB($this->request['password'])."'
			WHERE (iCustmerid_PK ='".$_SESSION['userid']."')";
			$this->obDb->updateQuery();
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home&mode=password&msg=1");
			$this->libFunc->m_mosRedirect($retUrl);	
			exit;
		}

}#CLASS ENDS
?>