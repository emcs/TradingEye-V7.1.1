<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

	class c_emailDb
	{
		#CONSTRUCTOR
		function c_emailDb()
		{
			$this->libFunc=new c_libFunctions();
		}
		#FUNCTION UPDATE,ADD NEW PACKAGE
		function m_insertEmail()
		{
			$timeStamp=time();
			$this->obDb->query = "INSERT INTO  ".EMAILS." SET  
			vSubject 	='".$this->libFunc->m_addToDB($this->request['subject'])."', 
			vSid 			='".$this->libFunc->m_addToDB($this->request['sid'])."', 
			tHtmlMail 	='".$this->libFunc->m_addToDB($this->request['html_mail'])."', 
			tTextMail 	='".$this->libFunc->m_addToDB($this->request['text_mail'])."', 
			vUserList 	='".$this->request['optUserList']."',
			vVisitorList='".$this->request['optVisitorList']."',
			iAdminUser 	='".$_SESSION['uid']."', 
			tmBuildDate='".$timeStamp."'" ;

			$this->obDb->updateQuery();
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.home&msg=1");	
			exit;	
		}

		#FUNCTION TO INSERT NEW LEAD STEP 1
		function m_insertLead()
		{
			$timeStamp=time();

			if(!isset($this->request['leadid']) || empty($this->request['leadid']))
			{
				$this->obDb->query = "INSERT INTO  ".LEADS." SET  
				iAdminUser 	='".$_SESSION['uid']."', 
				tmBuildDate='".$timeStamp."'" ;
				$this->obDb->updateQuery();
				$leadid=$this->obDb->last_insert_id;
			}
			else
			{
				$leadid=$this->request['leadid'];
			}
			
			
			$this->obDb->query = "SELECT count(*) as cnt FROM  ".LEADPRODUCT." WHERE 
			iProductid_FK 	='".$this->request['items']."' AND iLeadId_FK='$leadid'" ;
			$rsCnt=$this->obDb->fetchQuery();
			if($rsCnt[0]->cnt<1)
			{		
				$this->obDb->query = "INSERT INTO  ".LEADPRODUCT." SET  
				iLeadId_FK  	='".$leadid."', 
				iProductid_FK 	='".$this->request['items']."'" ;
				$this->obDb->updateQuery();
			}
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.leadForm&leadid=".$leadid."&msg=1&postOwner=".$this->request['postOwner']);	
			exit;	
		}

	#FUNCTION TO INSERT NEW LEAD STEP 2
	function m_insertLead1()
	{
		$timeStamp=time();
		if(isset($this->request['start_date']) && !empty($this->request['start_date']))
		{
			$arrStartDate=explode("/",$this->request['start_date']);
			$arrStartDate[1]=$this->libFunc->ifSet($arrStartDate,1);
			$arrStartDate[0]=$this->libFunc->ifSet($arrStartDate,0);
			$arrStartDate[2]=$this->libFunc->ifSet($arrStartDate,2);
			$this->request['start_date']=mktime(0,0,0,$arrStartDate[1],$arrStartDate[0],$arrStartDate[2]);
		}
		if(isset($this->request['end_date']) && !empty($this->request['end_date']))
		{
			$arrStartDate=explode("/",$this->request['end_date']);
			$arrStartDate[1]=$this->libFunc->ifSet($arrStartDate,1);
			$arrStartDate[0]=$this->libFunc->ifSet($arrStartDate,0);
			$arrStartDate[2]=$this->libFunc->ifSet($arrStartDate,2);
            $this->request['end_date']=mktime(23,59,59,$arrStartDate[1],$arrStartDate[0],$arrStartDate[2]);
		}
        
        $this->obDb->query ="SELECT DISTINCT iCustomerid_FK FROM  ".ORDERS." O,".ORDERPRODUCTS." OP,".LEADPRODUCT." L 
                              WHERE L.iProductid_FK=OP.iProductid_FK AND iOrderid_FK=iOrderid_PK AND 
                              O.tmOrderDate>='".$this->request['start_date']."' AND O.tmOrderDate<='".$this->request['end_date']."'  
                              AND iLeadId_FK='".$this->request['leadid']."'" ;
        $rsCustomer=$this->obDb->fetchQuery();
        $rCount=$this->obDb->record_count;
        
        
            $customerstring="";
            for ($i=0;$i<$rCount;$i++)
            {
            $customerstring = $rsCustomer[$i]->iCustomerid_FK.",";
            }    
            $customerstring = "(".substr_replace($customerstring ,"",-1).")";
         
                 
         if (isset($this->request['leadBuyers']) && $this->request['leadBuyers']=="no")
             {
                if ($customerstring = "()") // means no one ever bought this product, add everyone into the lead
                {
                $this->obDb->query = "SELECT DISTINCT iCustmerid_PK as iCustomerid_FK FROM ".CUSTOMERS;
                }else{  // select customers have bought the product and add rest of customers to the lead
                $this->obDb->query = "SELECT DISTINCT iCustmerid_PK as iCustomerid_FK FROM ".CUSTOMERS." WHERE iCustmerid_PK NOT IN ".$customerstring;
                }    
                
                $rsCustomer= $this->obDb->fetchQuery();
                $rCount=$this->obDb->record_count;
             }
       	
		
		if($rCount>0)
		{
			for($i=0;$i<$rCount;$i++)
			{
				#IF CUSTOMER IS REGISTERED
				if($rsCustomer[$i]->iCustomerid_FK>0){
					$this->obDb->query = "SELECT count(*) as cntCustomer  FROM ".CUSTOMERS." WHERE iStatus=1 AND iMailList !=0 AND iCustmerid_PK='".$rsCustomer[$i]->iCustomerid_FK."'";
					$custCnt = $this->obDb->fetchQuery();

					if($custCnt[0]->cntCustomer==1){
						$this->obDb->query = "INSERT INTO  ".LEADLIST." SET  
						iLeadId_FK  	='".$this->request['leadid']."', 
						iCustomerid_FK='".$rsCustomer[$i]->iCustomerid_FK."'" ;
						$this->obDb->updateQuery();
					}#END COUNT IF
				}#END IF CUSTOMER
			}#END FOR
		}#END IF
		else
		{
			$leadid=$this->request['leadid'];	$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.leadForm1&leadid=".$leadid."&msg=1");	
			exit;	
		}

		if(isset($this->request['leadid']) || !empty($this->request['leadid']))
		{
			$this->obDb->query = "UPDATE ".LEADS." SET 
			vdescription ='".$this->request['txtName']."' WHERE iLeadid_PK='".$this->request['leadid']."'"	;
			$this->obDb->updateQuery();
			
		}
	
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.dspForm&leadid=".$this->request['leadid']);	
		exit;	
	}

	#FUNCTION TO UPDATE EMAIL CAMPAIGNS
	function m_updateEmail()
	{
		$timeStamp=time();
		$libFunc=new c_libFunctions();
		
		$this->obDb->query = "UPDATE  ".EMAILS." SET  
		vSubject 	='".$this->libFunc->m_addToDB($this->request['subject'])."', 
		vSid 			='".$this->libFunc->m_addToDB($this->request['sid'])."', 
		tHtmlMail 	='".$this->libFunc->m_addToDB($this->request['html_mail'])."', 
		tTextMail 	='".$this->libFunc->m_addToDB($this->request['text_mail'])."', 
		vUserList 	='".$this->request['optUserList']."', 
		vVisitorList 	='".$this->request['optVisitorList']."',
		tmEditDate	='".$timeStamp."' 
		WHERE
		iMailid_PK	='".$this->request['id']."'"; 

		$this->obDb->updateQuery();
		
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.dspForm&id=".$this->request['id']."&msg=1");	
		exit;	
	}

	# FUNTION TO DELETE EMAIL CAMPAIGNS
	function m_emailDelete()
	{
		if(isset($this->request['id']) && !empty($this->request['id']))
		{			
				$this->obDb->query = "DELETE FROM ".EMAILS." WHERE  iMailid_PK =".$this->request['id'];
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.home&msg=3");	
		exit;	
	}

	#FUNCTION TO DELETE LEAD
	function m_emailDeleteLead()
	{
		if(isset($this->request['id']) && !empty($this->request['id']))
		{			
				$this->obDb->query = "DELETE FROM ".LEADPRODUCT." WHERE  iLeadProductId_PK =".$this->request['id'];
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.leadForm&leadid=".$this->request['leadid']."&postOwner=".$this->request['postOwner']."&msg=3");	
		exit;	
	}
	
	#FUNCTION TO DLETE LEAD
	function m_emailDeleteLeadMain()
	{
		if(isset($this->request['id']) && !empty($this->request['id']))
		{			
				$this->obDb->query = "DELETE FROM ".LEADPRODUCT." WHERE iLeadId_FK=".$this->request['id'];
				$this->obDb->updateQuery();

				$this->obDb->query = "DELETE FROM ".LEADLIST." WHERE  iLeadId_FK=".$this->request['id'];
				$this->obDb->updateQuery();

				$this->obDb->query = "DELETE FROM ".LEADS." WHERE   iLeadid_PK=".$this->request['id'];
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.leadHome&msg=2");	
		exit;	
	}	

	#FUNCTION TO SEND MAIL
	function m_sendMail()
	{
		$timestamp=time();
		$accounturl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
		$htmlfooter="<br /><br />==================================================";
		$htmlfooter.="<br />You have requested to receive emails from ".SITE_NAME." <br />		If you do not wish to receive any emails of this nature please <br />		<a href='".$accounturl."'>Click here</a> to be removed from our list.
		<br />";
		$htmlfooter.="==================================================<br />";
		$this->obDb->query= "select vSid,vSubject,tHtmlMail,tTextMail,vUserList,vVisitorList FROM ".EMAILS." WHERE iMailid_PK  = '".$this->request['id']."'";
		$qryResult = $this->obDb->fetchQuery();
		
		$rCount=$this->obDb->record_count;
		if($rCount>0) 
		{
			if(!isset($this->request['to']))
			{
				if($qryResult[0]->vUserList!="All")
				{
					$this->obDb->query= "SELECT vFirstName,vEmail,iMailList FROM ".LEADLIST.",".CUSTOMERS."	WHERE iCustomerid_FK=iCustmerid_PK AND iLeadId_FK='".$qryResult[0]->vUserList."'";
				}
				else
				{
					$this->obDb->query= "SELECT vFirstName,vEmail,iMailList FROM ".CUSTOMERS."	WHERE iStatus =1 && iMailList!='0'";
				}
				$qryResult1 = $this->obDb->fetchQuery();
				$rCount1=$this->obDb->record_count;
				if($qryResult[0]->vVisitorList == "1"){
					 $this->obDb->query = "SELECT * FROM ".NEWSLETTERS;
					 $qryVs = $this->obDb->fetchQuery();
				}
				$start = count($qryResult1);
				$a = 0;
				if(isset($qryVs)){
					foreach($qryVs as $k=>$v){
						$qryResult1[$start]->vFirstName = ""; 
						$qryResult1[$start]->vEmail = $v->vEmail;
						$qryResult1[$start]->iMailList = 1;
						$qryResult1[$start]->PKVisitor = $v->iSignup_PK;
						$start = $start + 1;
						$a = $a+1;
					}
					$rCount1 = $rCount1 + $a;
				}
				$this->obDb->query = "SELECT '' as vFirstName, '' as iMailList, vEmail FROM ".NEWSLETTERS;
				$newsletters = $this->obDb->fetchQuery();
				$newsletterscount = $this->obDb->record_count;
								
				$totalcount = $newsletterscount+$rCount1;
				
				$qryResult1 = $qryResult1+$newsletters;
				
				if($totalcount>0)
				{
					for($i=0;$i<$rCount1;$i++)
					{
						if($qryResult1[$i]->vFirstName!=''){
						$message ="Hi ".$qryResult1[$i]->vFirstName;	
						}else{
						$message ="Hi Customer ";
						}
						if(!empty($qryResult[0]->vSid))
						{
							$message.="<br /><br /><a href=".SITE_URL."sid/".$this->libFunc->m_displayContent($qryResult[0]->vSid).">".SITE_URL."sid/".$this->libFunc->m_displayContent($qryResult[0]->vSid)."</a><br />";
						}
						$obMail = new htmlMimeMail();
						$obMail->setReturnPath(ADMIN_EMAIL);
						$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
						$obMail->setSubject($qryResult[0]->vSubject);
						$obMail->setCrlf("\n"); //to handle mails in Outlook Express
						
						if(isset($qryResult1[$i]->PKVisitor)){
							$accounturl=$this->libFunc->m_safeUrl(SITE_URL."index.php?action=contactus.unsubscribe&mode=".$qryResult1[$i]->PKVisitor);
							$htmlfooter="<br /><br />==================================================";
							$htmlfooter.="<br />You have requested to receive emails from ".SITE_NAME." <br />		If you do not wish to receive any emails of this nature please <br />		<a href='".$accounturl."'>Click here</a> to be removed from our list.<br />";
							$htmlfooter.="==================================================<br />";
						}
						if($qryResult1[$i]->iMailList!=1)
						{
							$htmlcontent=$message."<br /><br />".$this->libFunc->m_displayContent($qryResult[0]->tTextMail);
							$htmlcontent.=$htmlfooter;
						}
						else
						{
							$htmlcontent=$message."<br />".$this->libFunc->m_displayContent1($qryResult[0]->tHtmlMail);
							$htmlcontent.=$htmlfooter;
						}
						$plaintxt	=$message."<br />".$this->libFunc->m_displayContent($qryResult[0]->tTextMail);
						$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$plaintxt));
						$obMail->setHtml(nl2br($htmlcontent),$txtcontent);
						$obMail->buildMessage();
						$result = $obMail->send(array($qryResult1[$i]->vEmail));
					}
				}

				$this->obDb->query= "UPDATE ".EMAILS." SET 
				tmSentDate ='$timestamp' 
				WHERE iMailid_PK  = '".$this->request['id']."'";
				$qryResult2 = $this->obDb->updateQuery();
			}
			elseif($this->request['to']=='test')
			{
				$message ="Hi Admin";
				$obMail = new htmlMimeMail();
				$obMail->setReturnPath(ADMIN_EMAIL);
				$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
				$obMail->setSubject($qryResult[0]->vSubject);
				$obMail->setCrlf("\n"); //to handle mails in Outlook Express
				if(!empty($qryResult[0]->vSid))
				{
					$message.="<br /><br /><a href=".SITE_URL."sid/".$this->libFunc->m_displayContent($qryResult[0]->vSid).">".SITE_URL."sid/".$this->libFunc->m_displayContent($qryResult[0]->vSid)."</a><br />";
				}
				$htmlcontent1=$message."<br>".$this->libFunc->m_displayContent($qryResult[0]->tTextMail);
				$htmlcontent1.=$htmlfooter;

			
				$htmlcontent2=$message."<br>".$this->libFunc->m_displayContent1($qryResult[0]->tHtmlMail);	
				$htmlcontent2.=$htmlfooter;
				$plaintxt	=$message."<br>".$qryResult[0]->tTextMail;
				
				$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$plaintxt));
				$obMail->setHtml(nl2br($htmlcontent1),$txtcontent);
				$obMail->buildMessage();
				$result = $obMail->send(array($this->request['email']));
				$obMail->setHtml(nl2br($htmlcontent2),$txtcontent);
				$obMail->buildMessage();
				$result = $obMail->send(array($this->request['email']));
			}

			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.home&msg=5");
			exit;
		}
		else
		{	
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=email.home&msg=6");
			exit;
		}
	}#EF
}#CLASS ENDS
?>