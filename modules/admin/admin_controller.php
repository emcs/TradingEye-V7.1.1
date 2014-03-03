<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;

# Class provides the order interface functionlaity
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/settings_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/settings_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/country_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/country_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/state_tax_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/state_tax_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/file_manager.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/csv_import.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/plugin_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/plugin_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."admin/classes/admin/theme_interface.php")); 
include_once($pluginInterface->plugincheck(SITE_PATH."admin/admin_messages.php")); 
class c_adminController
{

	# Class Constructor
	function c_adminController($obDatabase,$obTemplate,$attributes,$libFunc)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->libfunc=$libFunc;
		$this->templatePath=ADMINTHEMEPATH."admin/templates/admin/";
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

		$obOrd=new c_settingsInterface();
		$obOrd->obTpl=$this->obTpl;
		$obOrd->obDb=$this->obDb;
		$obOrd->request=$this->request;
        
		if(!isset($action[0]))
		{
			$action[0]="";
		}
		switch($action[0])
		{			
			case "settings":
			
			$obOrdDb=new c_settingsDb();
			$obOrdDb->obDb=$this->obDb;
			$obOrdDb->request=$this->request;

			switch($action[1]) {
				case "home":
					//Commented this out and added redirect, template doesnt exist
					/*$obOrd->settingsTemplate=$this->templatePath."shopSettingHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspHome());*/
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL.'admin/adminindex.php?action=settings.companyHome&flag=settings');
					$this->libFunc->m_mosRedirect($retUrl);
					
					break;
				
				case "analytics":
					$obOrd->settingsTemplate=$this->templatePath."analytics.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspAnalytics());
					break;
				case "updateAnalytics":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateAnalytics());
					break;
									
				case "postageHome":
					$obOrd->settingsTemplate=$this->templatePath."postageHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_postageHome());
					break;
					
				case "postageEditor":
					$obOrd->settingsTemplate=$this->templatePath."editPostage.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_postageEditor());
					break;
					
				case "updatePostage":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updatePostage());
					break;
					
				case "addZone":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_addPostagezone());
				break;
				
                case "addCity":
					$this->libfunc->check_token();
                    $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_addPostageCity());
                break;
                
                case "deletecity":
					$this->libfunc->check_token();
                    $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_deleteCity());
                break;
				
				case "setupcost":
				$obOrd->settingsTemplate=$this->templatePath."postageZoneCost.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_zoneSetupcostHome());
				break;
                
                case "setupcitycost":
                $obOrd->settingsTemplate=$this->templatePath."cityPostageCost.tpl.htm";
                $this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_citySetupcostHome());
                break;
				
				case "addrange":
					$this->libfunc->check_token();
                $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_addPostageZoneRange());
                break;
                
                case "updaterange":
					$this->libfunc->check_token();
                $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updatePostageZoneRange());
                break;
                
                case "editzone":
					$this->libfunc->check_token();
                $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_editzone());
                break;
                
                case "deletezone":
					$this->libfunc->check_token();
                $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_deletezone());
                break;
                
                case "deleterange":
					$this->libfunc->check_token();
                      $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_deleterange());
                break;
                
				case "addcityrange":
					$this->libfunc->check_token();
                $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_addCityRange());
                break;
                
                case "updatecityrange":
					$this->libfunc->check_token();
                $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateCityRange());
                break;
                
                case "deletecityrange":
					$this->libfunc->check_token();
                      $this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_deleteCityrange());
                break;
				
				
	
					
				case "postHomeUpdate":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateHomePostage());
					break;
				
				case "company":
					$obOrd->settingsTemplate=$this->templatePath."company.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_formCompanyInfo());
					break;
				
				case "companyHome":
					$obOrd->settingsTemplate=$this->templatePath."companyHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_formCompanyInfoHome());
					break;
					
				case "order":
					$obOrd->settingsTemplate=$this->templatePath."orderSetting.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_orderSettings());
					break;
			 
				case "payment":
					$obOrd->settingsTemplate=$this->templatePath."paymentSetting.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_paymentSettings());
					break;
					
				case "system":
					$obOrd->settingsTemplate=$this->templatePath."system.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_systemSettings());
					break;
					
				case "features":
					$obOrd->settingsTemplate=$this->templatePath."featureSetting.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_featureSettings());
					break;
					
				case "textareas":
					$obOrd->settingsTemplate=$this->templatePath."textareas.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_textAreas());
					break;
					
				case "metatags":
					$obOrd->metaTemplate=$this->templatePath."metatags.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_metaTagEditor());
					break;
				case "textarea_edit":
					$obOrd->settingsTemplate=$this->templatePath."textEditor.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_textEditor());
					break;
					
				case "updatemeta":
					$this->libfunc->check_token();
					$obOrdDb->m_updateMetaTags();
					break;
					
				case "updateCompInfo":
					$this->libfunc->check_token();
					if($obOrd->valiadateCompanyInfo()==1) {
						$obOrd->settingsTemplate=$this->templatePath."company.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_formCompanyInfo());
					} else {
						$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateCompInfo());
					}
					break;
					
				case "updateOrder":
					$this->libfunc->check_token();
					if($obOrd->valiadateOrderInfo()==1) {
						$obOrd->settingsTemplate=$this->templatePath."orderSetting.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_orderSettings());
					} else {
						$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateOrderInfo());
					}
					break;
			 
				case "updatePayment":
					$this->libfunc->check_token();
					if($obOrd->valiadatePaymentInfo()==1) {
					$obOrd->settingsTemplate=$this->templatePath."paymentSetting.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_paymentSettings());
					}else{
						$obOrdDb->m_updatePaymentInfo();
					}
					break;
					
				case "updateSystem":
					$this->libfunc->check_token();
					if($obOrd->valiadateSystemInfo()==1) {
						$obOrd->settingsTemplate=$this->templatePath."system.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_systemSettings());
					} else {
						$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateSystemInfo());
					}
					break;
				
				case "updateFeature":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateFeature());
					break;
					
				case "textarea_update":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateTextarea());
					break;
					
				case "global":
					$obOrd->settingsTemplate=$this->templatePath."shopGlobalSettings.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspHome());
					break;	
					
				case "design":
					$obOrd->settingsTemplate=$this->templatePath."designSettings.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspDesignSettings());
					break;
				case "updateDesign":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrdDb->m_updateDesign());
					break;
				
				default:
					//Commented out and redirected, template doesnt exist
					/*$obOrd->settingsTemplate=$this->templatePath."shopSettingHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspHome());*/
					
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL.'admin/adminindex.php?action=settings.companyHome&flag=settings');
					$this->libFunc->m_mosRedirect($retUrl);
					break;

			}#END SETTING
			break;
            
            case "country":
				$obCountry=new c_countryInterface();
				$obCountry->obTpl=$this->obTpl;
				$obCountry->obDb=$this->obDb;
				$obCountry->request=$this->request;
				
				$obCountryDb=new c_countryDb();
				$obCountryDb->obDb=$this->obDb;
				$obCountryDb->request=$this->request;
				switch($action[1])
				{
					case "home":
					$obCountry->countryTemplate=$this->templatePath."country.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCountry->m_createCountryList());
					break;
					case "delete":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obCountryDb->m_deleteCountry());
					break;
					case "update":
						$this->libfunc->check_token();
						$obCountryDb->m_updateCountry();
					break;
					case "new":
						$this->libfunc->check_token();
						$obCountryDb->m_insertCountry();
					break;
					default:
					$obCountry->countryTemplate=$this->templatePath."country.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCountry->m_createCountryList());
					break;
				}#END SWITCH
			break;#END COUNTRY

			case "state":
				$obState=new c_stateInterface();
				$obState->obTpl=$this->obTpl;
				$obState->obDb=$this->obDb;
				$obState->request=$this->request;
				
				$obStateDb=new c_stateDb();
				$obStateDb->obDb=$this->obDb;
				$obStateDb->request=$this->request;
				switch($action[1])
				{
					case "home":
					$obState->countryTemplate=$this->templatePath."stateTax.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obState->m_createStateList());
					break;
					case "delete":
					$this->libfunc->check_token();
					$this->obTpl->set_var("TPL_VAR_BODY",$obStateDb->m_deleteState());
					break;
					case "update":
					$this->libfunc->check_token();
					if(isset($this->request['mode']) && $this->request['mode']=="edit")
					{
						$checkValue=$obState->m_verifyEditState();
						if($checkValue==1)
						{
							$obStateDb->m_updateState();
						}
						else
						{
							$obState->request['cid']=$this->request['cid'];
							$obState->countryTemplate=$this->templatePath."stateTax.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obState->m_createStateList());	
						}
					}
					else
					{
						$checkValue=$obState->m_verifyInsertState();
						if($checkValue==1)
						{
							$obStateDb->m_insertState();
						}
						else
						{
							$obState->request['stateid']=$this->request['stateid'];		
							$obState->request['cid']=$this->request['cid'];		
							$obState->countryTemplate=$this->templatePath."stateTax.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obState->m_createStateList());
						}
					}
					break;
					default:
					$obState->countryTemplate=$this->templatePath."stateTax.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obState->m_createStateList());
					break;
				}#END SWITCH
			break;#END COUNTRY

			#HANDLING HELP PAGES
			case "help":
				$this->Template = MODULES_PATH."default/templates/admin/helpOuter.htm";
				$this->obTpl->set_file("mainContent",$this->Template);
			
				switch($action[1])
				{
					case "settings":
						$this->Template = MODULES_PATH."default/templates/help/settings.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "textareas":
						$this->Template = MODULES_PATH."default/templates/help/textareas.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "import":
						$this->Template = MODULES_PATH."default/templates/help/product_import.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "plugin":
						$this->Template = MODULES_PATH."default/templates/help/plugin_applications.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "filemanager":
						$this->Template = MODULES_PATH."default/templates/help/filemanager.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "country":
						$this->Template = MODULES_PATH."default/templates/help/taxes.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "state":
						$this->Template = MODULES_PATH."default/templates/help/taxes.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "feature":
						$this->Template = MODULES_PATH."default/templates/help/feature.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_SITE_URL",SITE_URL);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "postage":
						$this->Template = MODULES_PATH."default/templates/help/postage.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "company":
						$this->Template = MODULES_PATH."default/templates/help/settings_company.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "ordersetting":
					$this->Template = MODULES_PATH."default/templates/help/order_settings.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "paymentsetting":
					$this->Template = MODULES_PATH."default/templates/help/payment_settings.htm";
					$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "systemsetting":
						$this->Template = MODULES_PATH."default/templates/help/system_settings.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "metatag":
						$this->Template = MODULES_PATH."default/templates/help/meta_tags.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					default:
						$this->Template = MODULES_PATH."default/templates/help/toc.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
				}#END SETTING
					$this->obTpl->pparse("return","mainContent");
				exit;
			break;#HELP END

			case "file":
				$obFile=new c_fileManager();
				$obFile->obTpl=$this->obTpl;
				$obFile->obDb=$this->obDb;
				$obFile->request=$this->request;

				switch($action[1])
				{
					case "home":
					$obFile->browseTemplate=$this->templatePath."fileUploadHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFile->m_dspHome());
					break;
					case "save":
					$this->libfunc->check_token();
					$obFile->m_saveFile($_SESSION['filetosave']);
					unset($_SESSION['filetosave']);
					$obFile->browseTemplate=$this->templatePath."fileUploadHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFile->m_dspHome());
					break;
					case "uploadFrm":
					$obFile->browseTemplate=$this->templatePath."upload.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFile->m_uploadForm());
					break;
					case "list":
					$obFile->browseTemplate=$this->templatePath."fileListing.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFile->m_fileList());
					break;
					case "upload":
					$this->libfunc->check_token();
					if(!$obFile->m_verifyImageUpload()){
						$obFile->m_uploadFile();
					}else{
						$obFile->browseTemplate=$this->templatePath."upload.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obFile->m_uploadForm());
					}
					break;
					case "delete":
					$this->libfunc->check_token();
					$obFile->m_deleteFile();
					break;
					default:
					$obState->stateTemplate=$this->templatePath."country.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obState->m_createCountryList());
					break;
				}
			break;#END FILE

			case "csv":
				$obCSV=new c_csvImporter();
				$obCSV->obTpl=&$this->obTpl;
				$obCSV->obDb=$this->obDb;
				$obCSV->request=$this->request;
				$obCSV->templatePath=$this->templatePath;
				switch($action[1])
				{
					case "home":
					$obCSV->csvTemplate=$this->templatePath."csvImporter.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCSV->m_uploadFormCsv());
					break;
					case "export":
					$obCSV->csvTemplate=$this->templatePath."csvImporter.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCSV->m_exportCSV());
					break;
					case "upload":			
					$obCSV->m_uploadCsv();
					break;
					case "dspmsg":
					$obCSV->messageTemplate=$this->templatePath."message.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCSV->m_dspMessage());
					break;
					default:
					$obCSV->browseTemplate=$this->templatePath."csvImporter.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obCSV->m_uploadFormCsv());
					break;
				}
			break;
			case "plugin":
				$obPlugin=new c_pluginInterface();
				$obPlugin->obTpl=&$this->obTpl;
				$obPlugin->obDb=$this->obDb;
				$obPlugin->request=$this->request;

				$obPluginDb=new c_pluginDb();
				$obPluginDb->obDb=$this->obDb;
				$obPluginDb->request=$this->request;
				switch($action[1])
				{
					case "home":
					$obPlugin->pluginTemplate=$this->templatePath."pluginHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPlugin->m_dspPlugins());
					break;
					case "activate":
					$this->libfunc->check_token();
					$obPluginDb->m_activate();
					break;
					case "delete":
					$this->libfunc->check_token();
					$obPluginDb->m_delete();
					break;
					case "deactivate":
					$this->libfunc->check_token();
					$obPluginDb->m_deactivate();
					break;
		
					default:
					//Templates doesnt exist, redirect added
					/*$obPlugin->pluginTemplate=$this->templatePath."pluginHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPlugin->m_dspPlugins());*/
					$retUrl=$this->libFunc->m_safeUrl(SITE_URL.'admin/adminindex.php?action=plugin.home');
					$this->libFunc->m_mosRedirect($retUrl);
					break;
				}
			break;
			case "themes":
				$obTheme=new c_ThemeInterface();
				$obTheme->obTpl=&$this->obTpl;
				$obTheme->obDb=$this->obDb;
				$obTheme->request=$this->request;
				switch($action[1])
				{
					case "update":	
					$this->libfunc->check_token();
						$obTheme->m_updateThemes();
					break;
					
					default:						
						//$obTheme->themeTemplate=$this->templatePath."themeHome.tpl.htm";
						//$this->obTpl->set_var("TPL_VAR_BODY",$obTheme->m_dspThemes());					
						$obTheme->themeTemplate=$this->templatePath."themeManager.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obTheme->m_dspThemes());
					break;
				}
				
			break;
			default:
				$obOrd->settingsTemplate=$this->templatePath."shopSettingHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrd->m_dspHome());
				break;

		}#END SWITCH

	}#END FUNCTION

}#END CLASS