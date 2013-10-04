<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
# Class provides the order interface functionlaity
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/main/user_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."user/classes/main/user_db.php")); 
include_once($pluginInterface->plugincheck(SITE_PATH."user/messages.php")); 
class c_userController
{

	# Class Constructor
	function c_userController($obDatabase,&$obTemplate,$attributes)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->templatePath=THEMEPATH."user/templates/main/";
		$this->libFunc=new c_libFunctions();
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
		if(!isset($action[1]))
		{
			$action[1]="";
		}
		$this->libFunc=new c_libFunctions();
		$obUserInterface=new c_userInterface();
		$obUserInterface->obTpl=&$this->obTpl;
		$obUserInterface->obDb=$this->obDb;
		$obUserInterface->request=$this->request;
		switch($action[0])
		{

			case "user":
			$obUserDb=new c_userDb();
			$obUserDb->obDb=$this->obDb;
			$obUserDb->request=$this->request;

			switch($action[1])
			{
				case "loginForm":
                //$_SESSION['referer']=$_SERVER['HTTP_REFERER'];  
				$obUserInterface->userTemplate=$this->templatePath."loginForm.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Customer Login");
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_loginForm());
				break;
				case "home":
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
				$_SESSION['referer']=$retUrl;
				$this->libFunc->authenticate();
				unset($_SESSION['referer']);
			//	$this->obTpl->set_var("TPL_VAR_BREDCRUMBS"," &raquo; My Account");
				$obUserInterface->userTemplate=$this->templatePath."userAccount.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserAccount());
				break;
				case "checkLogin":
				if($obUserInterface->m_checkLogin()==1)
				{
					$obUserInterface->userTemplate=$this->templatePath."loginForm.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Customer Login");
					$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_loginForm());
				}
				else
				{
					if(isset($_SESSION['referer']))
					{
						header("Location:".$_SESSION['referer']);
						exit;				
					}
					else
					{
						$retUrl=$this->libFunc->m_safeUrl(SITE_URL."index.php");
						header("Location:".$retUrl);
						exit;				
					}
				}
				break;
				case "update":
				if($obUserInterface->m_verifyEditUser()==1)
				{
					$this->libFunc->authenticate();
					$obUserInterface->request['mode']="editDetails";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;My Account");
					$obUserInterface->userTemplate=$this->templatePath."userAccount.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserAccount());
				}
				else
				{
					$obUserDb->m_updateUser();
				}
				break;	
				case "updatePass":
				if($obUserInterface->m_verifyEditPass()==1)
				{
					$this->libFunc->authenticate();
					$obUserInterface->request['mode']="changePass";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;My Account");
					$obUserInterface->userTemplate=$this->templatePath."userAccount.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserAccount());
				}
				else
				{
					$obUserDb->m_updatePass();
				}
				break;
			
				case "logout":
				session_destroy();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/action/user.home/");
				header("Location:".$retUrl);
				break;
				case "signupForm":
				$obUserInterface->userTemplate=$this->templatePath."signup.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;New account");
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspSignupForm());
				break;
				case "insert":
				if($obUserInterface->m_verifyInsertUser()==1)
				{
					$obUserInterface->userTemplate=$this->templatePath."signup.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;New account");
					$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspSignupForm());
				}
				else
				{
					$obUserDb->m_insertUser();
				}
				break;
			
				case "addnewsletter":
					$obUserDb->m_newsletter();
				break;	
						
				case "emailPass":
					$obUserInterface->m_sendPassword();
				break;
				case "recover":
					$obUserInterface->userTemplate=$this->templatePath."resetpassword.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;Reset Password");
					$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_reset_Password());
				break;
				case "recoversave":
					$obUserInterface->m_save_new_Password();
				break;
				default:
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
					$_SESSION['referer']=$retUrl;
					$this->libFunc->authenticate();
					unset($_SESSION['referer']);
					$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;My Account");
					$obUserInterface->userTemplate=$this->templatePath."userAccount.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserAccount());
				break;
			}
		break;#END CUSTOMER

		default:
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."user/index.php?action=user.home");
				$_SESSION['referer']=$retUrl;
				$this->libFunc->authenticate();
				unset($_SESSION['referer']);
				$this->obTpl->set_var("TPL_VAR_BREDCRUMBS","&nbsp;&raquo;&nbsp;My Account");
				$obUserInterface->userTemplate=$this->templatePath."userAccount.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obUserInterface->m_dspUserAccount());
		break;

		}#END SWITCH CASE

	}#END EVENT HANDLER FUNCTION

}#CLASS END