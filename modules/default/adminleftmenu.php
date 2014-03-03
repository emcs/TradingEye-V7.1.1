<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_leftMenu
{
	#CONSTRUCTOR
	function c_leftMenu($obDatabase,$obMainTemplate,$attributes)
	{
		$this->obDb=$obDatabase;
		$this->request=$attributes;
		$this->obTpl=&$obMainTemplate;
		$this->templatePath=ADMINTHEMEPATH."default/templates/admin/";
		$this->m_leftMenuDisplay();
	}
		
	function m_leftMenuDisplay()
	{
		$this->libFunc=new c_libFunctions();
		$moduleNameArray=array();
		$this->Template = $this->templatePath."leftMenu.tpl.htm";
		$this->obTpl->set_file("TPL_LEFT_FILE",$this->Template);

		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_BUILDER_BLK","builder_blk");
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_BUILDERSUB_BLK","buildersub_blk");//locloc
		$this->obTpl->set_block("TPL_BUILDERSUB_BLK","TPL_SHOP_BLK","shop_blk");
		$this->obTpl->set_block("TPL_BUILDERSUB_BLK","TPL_MENU_BLK","menu_blk");
		$this->obTpl->set_block("TPL_BUILDERSUB_BLK","TPL_OPTION_BLK","option_blk");
		$this->obTpl->set_block("TPL_BUILDERSUB_BLK","TPL_PACKAGE_BLK","package_blk");
	
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_ORDERSUB_BLK","ordersub_blk");
		$this->obTpl->set_block("TPL_ORDERSUB_BLK","TPL_INVOICE_BLK","invoice_blk");
		$this->obTpl->set_block("TPL_ORDERSUB_BLK","TPL_CUSTOMER_BLK","customer_blk");
		$this->obTpl->set_block("TPL_ORDERSUB_BLK","TPL_SUPPLIER_BLK","supplier_blk");
		$this->obTpl->set_block("TPL_ORDERSUB_BLK","TPL_ENQUIRY_BLK","enquiry_blk");
		
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_MARKETINGSUB_BLK","marketingsub_blk");
		$this->obTpl->set_block("TPL_MARKETINGSUB_BLK","TPL_PROMOTIONS_BLK","promotions_blk");
		$this->obTpl->set_block("TPL_MARKETINGSUB_BLK","TPL_REPORT_BLK","report_blk");
		
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_ADMINSUB_BLK","adminsub_blk");
		$this->obTpl->set_block("TPL_ADMINSUB_BLK","TPL_SECURITY_BLK","security_blk");
		$this->obTpl->set_block("TPL_ADMINSUB_BLK","TPL_CSV_BLK","csv_blk");
		$this->obTpl->set_block("TPL_ADMINSUB_BLK","TPL_PLUGINLINK_BLK","pluginlink_blk");
		$this->obTpl->set_block("TPL_ADMINSUB_BLK","TPL_PLUGIN_BLK","subplugin_blk");//locloc




		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_SETTINGSSUB_BLK","settingssub_blk");
		

		//$this->obTpl->set_block("TPL_LEFT_FILE","TPL_PLUGINMAIN_BLK","pluginmain_blk");
		//$this->obTpl->set_block("TPL_PLUGINMAIN_BLK","TPL_PLUGIN_BLK","plugin_blk");
		$this->order=0;
		$this->shop=0;
		$this->marketing=0;
		$this->admin=0;

		$this->obTpl->set_var("TPL_VAR_LOC","");
	
		$this->obTpl->set_var("builder_blk","");
		$this->obTpl->set_var("shop_blk","");
		$this->obTpl->set_var("menu_blk","");
		$this->obTpl->set_var("option_blk","");
		$this->obTpl->set_var("package_blk","");
		$this->obTpl->set_var("buildersub_blk",""); //locloc
		$this->obTpl->set_var("ordersub_blk",""); //locloc
		$this->obTpl->set_var("subplugin_blk",""); //locloc
		
		$this->obTpl->set_var("order_blk","");
		$this->obTpl->set_var("invoice_blk","");
		$this->obTpl->set_var("customer_blk","");
		$this->obTpl->set_var("adminsub_blk","");//LOCLOC
		$this->obTpl->set_var("supplier_blk","");
		$this->obTpl->set_var("enquiry_blk","");

		$this->obTpl->set_var("admin_blk","");
		$this->obTpl->set_var("settings_blk","");
		$this->obTpl->set_var("security_blk","");
		$this->obTpl->set_var("csv_blk","");
		$this->obTpl->set_var("pluginlink_blk","");
		$this->obTpl->set_var("settingssub_blk","");//locloc

		
		$this->obTpl->set_var("marketing_blk","");
		$this->obTpl->set_var("marketingsub_blk","");
		
		$this->obTpl->set_var("promotions_blk","");
		$this->obTpl->set_var("report_blk","");


		$this->obTpl->set_var("plugin_blk","");
		$this->obTpl->set_var("pluginmain_blk","");
		
		$this->obTpl->set_var("TPL_VAR_BUILDSELECTED","");
		$this->obTpl->set_var("TPL_VAR_ORDERSELECTED","");
		$this->obTpl->set_var("TPL_VAR_MARKETINGSELECTED","");
		$this->obTpl->set_var("TPL_VAR_ADMINSELECTED","");
		$this->obTpl->set_var("TPL_VAR_SETTINGSELECTED","");
		
		
		if (isset($this->request['flag']) && $this->request['flag']==''){
			unset ($this->request['flag']);
		}
		
		if (isset($_SESSION['dashSelec'])){
			$this->obTpl->set_var("TPL_VAR_DASHSELECTED","class='selected'");		
		}else{
		$this->obTpl->set_var("TPL_VAR_DASHSELECTED","");	
		}
		
		if (isset($this->request['flag'])){
			switch ($this->request['flag']){
				case "orders": 
					$_SESSION['flag']="orders"; 
				break;
				
				case "marketing": 
					$_SESSION['flag']="marketing"; 
				break;
				
				case "admin": 
					$_SESSION['flag']="admin"; 
				break;
				
                case "settings": 
                    $_SESSION['flag']="settings"; 
                break;
				case "builder": 
					$_SESSION['flag']="builder"; 
				break;
			}
		}
		
		$this->obDb->query= "SELECT vSecurity FROM ".ADMINSECURITY." WHERE iUserid_FK = '".$_SESSION['uid']."'";
		$rsSecurity = $this->obDb->fetchQuery();
		$moduleString=$rsSecurity[0]->vSecurity;
		
		$moduleArray=explode(",",$moduleString);
	
		foreach($moduleArray as $mid)
		{
			$this->obDb->query= "SELECT sName FROM ".MODULES." WHERE mid= '".$mid."'";
			$rsSecurity = $this->obDb->fetchQuery();
			$moduleName=$rsSecurity[0]->sName;
			array_push($moduleNameArray,$moduleName);
		}
//**************************************************************
		if(in_array("ec_show",$moduleNameArray))
		{
			$this->obTpl->parse("shop_blk","TPL_SHOP_BLK");		
			$this->shop=1;
		}
		if(in_array("ec_menu",$moduleNameArray))
		{
			$this->obTpl->parse("menu_blk","TPL_MENU_BLK");			
			$this->shop=1;
		}
		if(in_array("ec_option",$moduleNameArray))
		{
			$this->obTpl->parse("option_blk","TPL_OPTION_BLK");			
			$this->shop=1;
		}
		if(in_array("ec_package",$moduleNameArray))
		{
			$this->obTpl->parse("package_blk","TPL_PACKAGE_BLK");			
			$this->shop=1;
		}
		if($this->shop==1)
		{
			if (isset($_SESSION['flag']) && $_SESSION['flag']=="builder"){
			$this->obTpl->set_var("TPL_VAR_BUILDSELECTED","class='selected'");
			$this->obTpl->set_var("TPL_VAR_DASHSELECTED","");
			$this->obTpl->set_var("TPL_VAR_BUILDCLOSELIST","");	
			$this->obTpl->parse("buildersub_blk","TPL_BUILDERSUB_BLK");
			}
		$this->obTpl->parse("builder_blk","TPL_BUILDER_BLK");
		}
//************************************************************	
		if(in_array("orders",$moduleNameArray))
		{
			$this->obTpl->parse("invoice_blk","TPL_INVOICE_BLK");		
			$this->order=1;
		}
		if(in_array("user",$moduleNameArray))
		{
			$this->obTpl->parse("customer_blk","TPL_CUSTOMER_BLK");	
			$this->order=1;
		}
		if(in_array("supplier",$moduleNameArray))
		{
			$this->obTpl->parse("supplier_blk","TPL_SUPPLIER_BLK");			
			$this->order=1;
		}
		if(in_array("enquiry",$moduleNameArray))
		{
			$this->obTpl->parse("enquiry_blk","TPL_ENQUIRY_BLK");			
			$this->order=1;
		}
		if($this->order==1)
		{
			if (isset($_SESSION['flag']) && $_SESSION['flag']=="orders"){
			$this->obTpl->set_var("TPL_VAR_ORDERSELECTED","class='selected'");
			$this->obTpl->set_var("TPL_VAR_DASHSELECTED","");
			$this->obTpl->set_var("TPL_VAR_ORDERSCLOSELIST","");
			$this->obTpl->parse("ordersub_blk","TPL_ORDERSUB_BLK");
			}
		$this->obTpl->parse("order_blk","TPL_ORDER_BLK");	
		}
			
//*****************************************************************	
		if(in_array("security",$moduleNameArray))
		{
			$this->obTpl->parse("security_blk","TPL_SECURITY_BLK");			
			$this->admin=1;
		}
		if(in_array("csv",$moduleNameArray))
		{
			$this->obTpl->parse("csv_blk","TPL_CSV_BLK");			
			$this->admin=1;
		}
		if(in_array("plugin",$moduleNameArray))
		{
			$this->obTpl->parse("pluginlink_blk","TPL_PLUGINLINK_BLK");	
			$this->admin=1;
		}

		if($this->admin==1)
		{
			if (isset($_SESSION['flag']) && $_SESSION['flag']=="admin"){
			$this->obTpl->set_var("TPL_VAR_ADMINSELECTED","class='selected'");
			$this->obTpl->set_var("TPL_VAR_DASHSELECTED","");
			$this->obTpl->set_var("TPL_VAR_ADMINCLOSELIST","");
			$this->obTpl->parse("adminsub_blk","TPL_ADMINSUB_BLK");
			}
		$this->obTpl->parse("admin_blk","TPL_ADMIN_BLK");	
		}

//***********************************************************
		if(in_array("settings",$moduleNameArray))
		{
			if (isset($_SESSION['flag']) && $_SESSION['flag']=="settings"){
				$this->obTpl->set_var("TPL_VAR_DASHSELECTED","");
				$this->obTpl->set_var("TPL_VAR_SETTINGSELECTED","class='selected'");
				$this->obTpl->set_var("TPL_VAR_SETTINGSCLOSELIST","");
				$this->obTpl->parse("settingssub_blk","TPL_SETTINGSSUB_BLK");
				
			}
			$this->obTpl->parse("settings_blk","TPL_SETTINGS_BLK");
			$this->admin=1;
		}
//*****************************************************************	

		if(in_array("promotions",$moduleNameArray))
		{
			$this->obTpl->parse("promotions_blk","TPL_PROMOTIONS_BLK");			
			$this->marketing=1;
		}
		if(in_array("report",$moduleNameArray))
		{
			$this->obTpl->parse("report_blk","TPL_REPORT_BLK");			
			$this->marketing=1;
		}
		if($this->marketing==1)
		{
				if (isset($_SESSION['flag']) && $_SESSION['flag']=="marketing"){
			$this->obTpl->set_var("TPL_VAR_DASHSELECTED","");
			$this->obTpl->set_var("TPL_VAR_MARKETINGSELECTED","class='selected'");
			$this->obTpl->set_var("TPL_VAR_MARKETINGCLOSELIST","");	
			$this->obTpl->parse("marketingsub_blk","TPL_MARKETINGSUB_BLK");
			}
		$this->obTpl->parse("marketing_blk","TPL_MARKETING_BLK");	
			
		}
//***********************************************************



		#PLUGIN BLOCK
		$query= "SELECT *  FROM ".PLUGINS." WHERE iState=1";
		$query.=" ORDER BY vAppName";
		$this->obDb->query=$query;
		$row_plugin = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				$this->obTpl->set_var("TPL_VAR_ID",$row_plugin[$i]->iPluginid_PK);
				$this->obTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($row_plugin[$i]->vAppName));
				$this->obTpl->set_var("TPL_VAR_TEMPLATE",$row_plugin[$i]->vTemplate);
				if(file_exists(SITE_PATH."plugins/".$row_plugin[$i]->vTemplate) && is_dir(SITE_PATH."plugins/".$row_plugin[$i]->vTemplate)){					
					$this->obTpl->set_var("TPL_VAR_PLUGINPATH",SITE_URL."plugins/".$row_plugin[$i]->vTemplate);
				}else{
					$this->obTpl->set_var("TPL_VAR_PLUGINPATH","#");
				}
				$this->obTpl->parse("plugin_blk","TPL_PLUGIN_BLK",true);
			}
			$this->obTpl->parse("pluginmain_blk","TPL_PLUGINMAIN_BLK");
		}
		if(!in_array("plugin",$moduleNameArray))
		{
			$this->obTpl->set_var("pluginmain_blk","");			
		}
		//$this->obTpl->set_block("TPL_BUILDER_BLK","TPL_SHOP_BLK","shop_blk");
		$this->obTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		
		$this->obTpl->set_var('TPL_VAR_LEFT', $this->obTpl->parse("return","TPL_LEFT_FILE"));
	}#END AUTHORIZATION
}
?>
