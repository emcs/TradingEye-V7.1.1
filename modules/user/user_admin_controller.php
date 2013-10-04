<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
# Class provides the order interface functionlaity
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/admin/suppliers_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/admin/suppliers_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/admin/user_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/admin/user_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/admin/security_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/admin/security_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/admin/enquires_interface.php")); 
include_once($pluginInterface->plugincheck(SITE_PATH."user/admin_messages.php")); 
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
class c_userAdminController
{

	# Class Constructor
	function c_userAdminController($obDatabase,$obTemplate,$attributes,$libFunc)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->libfunc=$libFunc;
		$this->templatePath=ADMINTHEMEPATH."user/templates/admin/";
		$this->m_eventHandler();
	}


	# Function to handle order events
	function m_eventHandler()
	{
		if(!isset($this->request['action']))
		{
			$this->request['action']="";
		}
		$action=explode(".",$this->request['action']);
		switch($action[0])
		{

			case "user":
			$obUserInterface=new c_userInterface();
			$obUserInterface->obTpl=$this->obTpl;
			$obUserInterface->obDb=$this->obDb;
			$obUserInterface->request=$this->request;

			$obUserDb=new c_userDb();
			$obUserDb->obDb=$this->obDb;
			$obUserDb->request=$this->request;
			if(!isset($action[1]))
			{
				$action[1]="";
			}
			switch($action[1])
			{
				case "home":
				$obUserInterface->userTemplate=$this->templatePath."userDisplayHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspCustomers());
				break;
				case "dspForm":
				$obUserInterface->userTemplate=$this->templatePath."userForm.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserForm());
				break;
				case "status":
				$obUserInterface->userTemplate=$this->templatePath."status.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_userStatus());
				break;
				case "details":
				$obUserInterface->userTemplate=$this->templatePath."userDetails.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->dspUserDetails());
				break;
				case "sendpass":
				$obUserInterface->m_sendPassword();
				break;
				case "updateUser":
					$this->libfunc->check_token();
					if(isset($this->request['mode']) && $this->request['mode']=="edit")
					{
						$checkValue=$obUserInterface->m_verifyEditUser();
						if($checkValue==0)
						{
							$obUserDb->m_updateUser();
						}
						else
						{
							$obUserInterface->request['id']=$this->request['id'];
				
							$obUserInterface->userTemplate=$this->templatePath."userForm.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserForm());
						
						}
					}
					else
					{
						$checkValue=$obUserInterface->m_verifyInsertUser();
						if($checkValue==0)
						{
							$obUserDb->m_insertUser();
						}
						else
						{
							$obUserInterface->userTemplate=$this->templatePath."userForm.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserForm());
						
						}
					}
				break;
				case "changestatus":
					$this->libfunc->check_token();
					$obUserDb->m_changeStatus();
				break;
				default:
				$obUserInterface->userTemplate=$this->templatePath."userDisplayHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspCustomers());
				break;
	
			}
			break;#END CUSTOMER

			case "enquiry":
			$obEnquiryInterface=new c_enquiryInterface();
			$obEnquiryInterface->obTpl=$this->obTpl;
			$obEnquiryInterface->obDb=$this->obDb;
			$obEnquiryInterface->request=$this->request;

		
			switch($action[1])
			{
				case "home":
				$obEnquiryInterface->contactTemplate =$this->templatePath."enquiriesHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obEnquiryInterface->m_dspEnquiries());
				break;
				case "details":
				$obEnquiryInterface->contactTemplate =$this->templatePath."enquiriesDetails.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obEnquiryInterface->dspEnquiryDetails());
				break;
				case "deleteEnq":
					$this->libfunc->check_token();
				$obEnquiryInterface->m_deleteEnquiries();
				break;
				case "delete":
					$this->libfunc->check_token();
				$obEnquiryInterface->m_deleteEnquiry();
				break;
			}
			break;
			case "supplier":
			$obSupplierInterface=new c_supplierInterface();
			$obSupplierInterface->obTpl=$this->obTpl;
			$obSupplierInterface->obDb=$this->obDb;
			$obSupplierInterface->request=$this->request;
			$obSupplierInterface->imagePath=SITE_PATH."images/";
			$obSupplierInterface->imageUrl=SITE_URL."images/";

			$obSupplierDb=new c_supplierDb();
			$obSupplierDb->obDb=$this->obDb;
			$obSupplierDb->request=$this->request;
			$obSupplierDb->imagePath=SITE_PATH."images/";

			switch($action[1])
			{
				case "home":
				$obSupplierInterface->supplierTemplate=$this->templatePath."suppliersHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obSupplierInterface->m_dspSuppliers());
				break;
				case "dspForm":
				$obSupplierInterface->supplierTemplate=$this->templatePath."suppliersForm.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obSupplierInterface->m_dspSupplierForm());
				break;

				case "updateSupplier":
					$this->libfunc->check_token();
					if(isset($this->request['mode']) && $this->request['mode']=="edit")
					{
						if(!$obSupplierInterface->m_verifyEditSupplier())
						{
							$obSupplierDb->m_updateSupplier();
						}
						else
						{
							$obSupplierInterface->request['id']=$this->request['id'];
							$obSupplierInterface->supplierTemplate=$this->templatePath."suppliersForm.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obSupplierInterface->m_dspSupplierForm());
						}
					}
					else
					{

						if(!$obSupplierInterface->m_verifyInsertSupplier())
						{
							$obSupplierDb->m_insertSupplier();
						}
						else
						{
							$obSupplierInterface->supplierTemplate=$this->templatePath."suppliersForm.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obSupplierInterface->m_dspSupplierForm());
						}
					}
				break;
				case "uploadForm":
				$obSupplierInterface->uploadTemplate=MODULES_PATH."default/templates/admin/upload.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obSupplierInterface->m_uploadForm());
				 break;		
				case "upload":
					$this->libfunc->check_token();
				if(!$obSupplierInterface->m_verifyImageUpload()){
					 $obSupplierDb->m_uploadImage();
				}else{
					$obSupplierInterface->uploadTemplate=MODULES_PATH."default/templates/admin/upload.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obSupplierInterface->m_uploadForm());
				}
				 break;
				case "delete":
					$this->libfunc->check_token();
					$obSupplierDb->m_deleteSupplier();
				break;
	
			}
			break;#END SUPPLIER


			case "security":
			$obSecurityInterface=new c_securityInterface();
			$obSecurityInterface->obTpl=$this->obTpl;
			$obSecurityInterface->obDb=$this->obDb;
			$obSecurityInterface->request=$this->request;

			$obSecurityDb=new c_securityDb();
			$obSecurityDb->obDb=$this->obDb;
			$obSecurityDb->request=$this->request;

			switch($action[1])
			{
				case "home":
                $obSecurityInterface->adminTemplate=$this->templatePath."adminUsers.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obSecurityInterface->m_dspAdminUsers());
				break;
				case "createAdmin":
				$obSecurityInterface->adminTemplate=$this->templatePath."createAdminForm.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obSecurityInterface->m_createAdminForm());
				break;
				case "addAdmin":
					$this->libfunc->check_token();
					if(isset($this->request['mode']) && $this->request['mode']=="edit")
					{
						if($obSecurityInterface->m_verifyEditAdmin()==1)
						{
							$obSecurityDb->m_updateAdmin();
						}
						else
						{
							if($obSecurityInterface->m_verifyEditAdmin()==2)
								$obSecurityInterface->request['msg']=1;
							if($obSecurityInterface->m_verifyEditAdmin()==3)
								$obSecurityInterface->request['msg']=2;
							$obSecurityInterface->request['adminid']=$this->request['adminid'];
							$obSecurityInterface->adminTemplate=$this->templatePath."createAdminForm.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obSecurityInterface->m_createAdminForm());
						
						}
					}
					else
					{
						if($obSecurityInterface->m_verifyInsertAdmin()==1)
						{
							$obSecurityDb->m_insertAdmin();
						}
						else
						{
							if($obSecurityInterface->m_verifyInsertAdmin()==2)
								$obSecurityInterface->request['msg']=1;
							if($obSecurityInterface->m_verifyInsertAdmin()==3)
								$obSecurityInterface->request['msg']=2;	
							$obSecurityInterface->adminTemplate=$this->templatePath."createAdminForm.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obSecurityInterface->m_createAdminForm());
						}
					}
				break;
				case "deleteAdmin":
					$this->libfunc->check_token();
					$obSecurityDb->m_deleteAdmin();
				break;
	
			}
			break;#END SECURITY
		
		#HANDLING HELP PAGES
		case "help":
			$this->Template = MODULES_PATH."default/templates/admin/helpOuter.htm";
			$this->obTpl->set_file("mainContent",$this->Template);
		
			switch($action[1])
			{
				case "security":
					$this->Template = MODULES_PATH."default/templates/help/admin_security.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
				break;
				case "users":
					$this->Template = MODULES_PATH."default/templates/help/admin_users.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
				break;
				case "customer":
					$this->Template = MODULES_PATH."default/templates/help/customer.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
				break;
				case "contact":
					$this->Template = MODULES_PATH."default/templates/help/contact.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
				break;
				case "supplier":
					$this->Template = MODULES_PATH."default/templates/help/suppliers.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
				break;
			}
			$this->obTpl->pparse("return","mainContent");
			exit;
		break;
		case "logout":
			session_destroy();
			header("Location:".SITE_URL."adminindex.php");
		break;
		case "unauthorized":
		$this->Template = MODULES_PATH."default/templates/admin/unAutherized.tpl.htm";
		$this->obTpl->set_file("Content",$this->Template);
		$this->obTpl->set_var("TPL_VAR_BODY",$this->obTpl->parse("return","Content"));
		break;

		default:
			if(isset($_SESSION['uname']) && trim($_SESSION['uname'])!="")
			{
				header("Location:".SITE_URL."user/adminindex.php?action=user.home");
				exit;
			}
			else
			{
				header("Location:".SITE_URL."adminindex.php");
				exit;
			}
		}#END SWITCH CASE

	}#END EVENT HANDLER FUNCTION

}#CLASS END