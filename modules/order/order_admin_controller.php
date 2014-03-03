<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
# Class provides the order interface functionlaity
include_once($pluginInterface->plugincheck(MODULES_PATH."order/classes/admin/order_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."order/classes/admin/order_db.php")); 
include_once($pluginInterface->plugincheck(SITE_PATH."order/admin_messages.php")); 

class c_orderAdminController
{

	# Class Constructor
	function c_orderAdminController($obDatabase,$obTemplate,$attributes,$libfunc)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->libfunc=$libfunc;
		$this->templatePath=ADMINTHEMEPATH."order/templates/admin/";
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
		switch($action[0])
		{
			case "orders":
			$obOrd=new c_orderInterface();
			$obOrd->obTpl=$this->obTpl;
			$obOrd->obDb=$this->obDb;
			$obOrd->request=$this->request;
			
			$obOrdDb=new c_orderDb();
			$obOrdDb->obTpl=$this->obTpl;
			$obOrdDb->obDb=$this->obDb;
			$obOrdDb->request=$this->request;
			switch($action[1])
			{
				case "home":
				$obOrd->orderTemplate=$this->templatePath."dspOrders.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspOrders());
				break;
				case "track":
				$obOrd->trackTemplate=$this->templatePath."tracking.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_trackingForm());
				break;
				case "find":
				$obOrd->findTemplate=$this->templatePath."findProduct.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_findProduct());
				break;
				case "status":
				$obOrd->statusTemplate=MODULES_PATH."default/templates/admin/statusMessage.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_statusMessage());
				break;
				case "addproductform":
				$obOrd->addTemplate=$this->templatePath."addProduct.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_addProductForm());
				break;
				case "addproduct":
					$this->libfunc->check_token();
				$obOrdDb->m_addProduct();
				break;
				case "updatetrack":
					$this->libfunc->check_token();
				$obOrdDb->mailTemplate=$this->templatePath."mailTemplate.tpl.htm";
				$obOrdDb->m_updateTrackingInfo();
				break;
				case "removeCredit":
					$this->libfunc->check_token();
				$obOrdDb->m_removeCreditInfo();
				break;
				case "updatehome":
					$this->libfunc->check_token();
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateHome());
				break;
				case "dspDetails":
				$obOrd->orderTemplate=$this->templatePath."orderDetails.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspOrderDetails());
				break;
				
				case "updateInvoice":
					$this->libfunc->check_token();
				if(isset($this->request['ec']) && $this->request['ec']=='1' && isset($this->request['pay_status']))
				{
					$obOrd->m_sendOrdersDetails();
				}
				$obOrdDb->m_updateInvoice();
				break;
				
				default:
				$obOrd->orderTemplate=$this->templatePath."dspOrders.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspOrders());
				break;

			}#END ORDER
			break;

			case "help":
				$this->Template = MODULES_PATH."default/templates/admin/helpOuter.htm";
				$this->obTpl->set_file("mainContent",$this->Template);
			
				switch($action[1])
				{

					case "order":
						$this->Template = MODULES_PATH."default/templates/help/orders.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
				}
					$this->obTpl->pparse("return","mainContent");
				exit;
			break;
			default:
				header("Location:".SITE_URL."order/adminindex.php?action=orders.home");
				exit;
			break;

		}#END SWITCH

	}#END FUNCTION

}#END CLASS