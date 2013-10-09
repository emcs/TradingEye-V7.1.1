<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
include_once($pluginInterface->plugincheck(MODULES_PATH."default/classes/admin/home.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."default/classes/admin/reports.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."default/messages_admin.php")); 
class c_homeDisplay
	{
	# Class constructor		
	function c_homeDisplay($obDatabase,$obTemplate,$attributes,$libfunc)
		{
			$this->obDb=$obDatabase;
			$this->obTpl=&$obTemplate;
			$this->request=$attributes;
			$this->libfunc=$libfunc;
			$this->templatePath=ADMINTHEMEPATH."default/templates/admin/";
			$this->printMainBlock();
		}
	

	# Event handler
	function printMainBlock()
	{
		if(!isset($this->request['action']))
		{
			$this->request['action']="show";
		}
		$obHome=new c_home();
		$obHome->obDb=$this->obDb;
		$obHome->request=$this->request;
		$action=explode(".",$this->request['action']);
		switch($action[0])
			{
				case "home":

				$obRpt=new c_reports();
				$obRpt->obDb=$this->obDb;
				$obRpt->request=$this->request;
				if(!isset($action[1]))
				{
					$action[1]="home";
				}
				switch($action[1])
				{
					case "preport":
					$obRpt->productTemplate=$this->templatePath."productReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showProductReport());
					break;
					case "bSeller":
					$obRpt->bSellerTemplate=$this->templatePath."BestSellerReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showBestSellerReport());
					break;
					case "outOf":
					$obRpt->outOfStockTemplate=$this->templatePath."OutOfStockReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showOutOfStockReport());
					break;
					case "lowStock":
					$obRpt->lowStockStockTemplate=$this->templatePath."LowStockReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showlowStockReport());
					break;
					case "topSearch":
					$obRpt->topSearchTemplate=$this->templatePath."TopSearchReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showTopSearchReport());
					break;
					case "mostViewed":
					$obRpt->mostViewedTemplate=$this->templatePath."MostViewedReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showMostViewedReport());
					break;
					case "abandonment":
					$obRpt->abandonmentTemplate=$this->templatePath."AbandonmentReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showAbandonmentReport());
					break;
					case "newOrder":
					$obRpt->newOrderTemplate=$this->templatePath."NewOrderReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showNewOrderReport());
					break;
					case "pending":
					$obRpt->pendingTemplate=$this->templatePath."PendingReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showPendingReport());
					break;
					case "incomplete":
					$obRpt->incompleteTemplate=$this->templatePath."incompleteReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showIncompleteReport());
					break;
					case "newCust":
					$obRpt->newCustTemplate=$this->templatePath."newCustomerReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showNewCustomerReport());
					break;
					case "returnCust":
					$obRpt->returnCustTemplate=$this->templatePath."ReturningCustomerReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showReturningCustomerReport());
					break;
					case "bestCust":
					$obRpt->bestCustTemplate=$this->templatePath."BestCustomerReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showBestCustomerReport());
					break;
					case "creport":
					$obRpt->contentTemplate=$this->templatePath."contentReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showContentReport());
					break;
					case "sreport":
					$obRpt->stockTemplate=$this->templatePath."stockReport.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obRpt->m_showStockReport());
					break;
					case "deleteProduct":
					$this->libfunc->check_token();
					$obRpt->m_deleteProduct();
					break;
					case "deleteContent":
					$this->libfunc->check_token();
					$obRpt->m_deleteContent();
					break;

					case "help":
					$this->Template = MODULES_PATH."default/templates/admin/helpOuter.htm";
					$this->obTpl->set_file("mainContent",$this->Template);
					$this->Template = $this->templatePath."../help/reports.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
				
				
					$this->obTpl->pparse("return","mainContent");
					exit;
					break;
					default:
					$this->Template = $this->templatePath."adminHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_PAGETITLE","Home");
					$this->obTpl->set_file("hContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
					$this->obTpl->set_var('TPL_VAR_BODY', $this->obTpl->parse("return","hContent"));
					break;

				}
				break;
				
				default :
					$obHome->Template = $this->templatePath."adminHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_PAGETITLE","Home");
					$this->obTpl->set_var('TPL_VAR_BODY',$obHome->m_showHomePage());
				break;
			}
	}
	
}
?>