<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
Function:This is controller file for admin section(Builder Module,handles all the actions and apply appropriate function
=================================================================*/

defined('_TEEXEC') or die;
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/shop_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/shop_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/menu_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/menu_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/option_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/option_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/package_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."ecom/classes/admin/package_db.php")); 
include_once($pluginInterface->plugincheck(SITE_PATH."ecom/admin_messages.php")); 

class c_ecomAdminController
{

	# Class Constructor
	function c_ecomAdminController($obDatabase,$obTemplate,$attributes,$libfunc)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->libfunc=$libfunc;
		$this->templatePath=ADMINTHEMEPATH."ecom/templates/admin/";
		$this->m_eventHandler();
	}


	# Function to handle events
	function m_eventHandler()
	{
		if(!isset($this->request['action']))
		{
			$this->request['action']="";
		}
		$action=explode(".",$this->request['action']);
		if(isset($this->request['owner']) && !is_Numeric($this->request['owner']))
		{
			$this->request['owner']=0;
		}

		$obShopInterface=new c_shopInterface();
		$obShopInterface->obTpl=$this->obTpl;
		$obShopInterface->obDb=$this->obDb;
		$obShopInterface->request=$this->request;
		$obShopInterface->imageUrl=SITE_URL."images/";
		$obShopInterface->imagePath=SITE_PATH."images/";

		switch($action[0])
		{
			#HANDLING VIEW(FRONTEND-SHOP BUILDER)
			case "ec_show":
			
			switch($action[1])
			{
				case "home":
					$obShopInterface->departmentTemplate=$this->templatePath."dspShopBuilder.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showDepartments());
				break;			
				case "deptFrm":
					$obShopInterface->departmentTemplate=$this->templatePath."dspDeptForm.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspDepartmentForm());
				break;
				case "contentFrm":
					$obShopInterface->contentTemplate=$this->templatePath."dspContentForm.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspContentForm());
				break;
				case "dspMsg":
					$obShopInterface->msgTemplate=$this->templatePath."dspMessage.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspMessage());
				break;
				case "dspUploadFrm":
					$obShopInterface->uploadTemplate=$this->templatePath."uploadImages.tpl.htm";
					$obShopInterface->m_uploadForm();
				break;

				case "dspProFrm":
					$obShopInterface->productTemplate=$this->templatePath."dspProdForm.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspProductForm());
				break;
				case "reorder":
					$obShopInterface->reorderTemplate=$this->templatePath."dspOrderList.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_reorder());
				break;
				case "associate":
					$obShopInterface->associateTemplate=$this->templatePath."dspAssociateItems.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_associateItems());
				break;
				case "vdiscount":
					$obShopInterface->discountTemplate=$this->templatePath."volDiscount.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_volDiscount());
				break;
				case "attachOpt":
					$obShopInterface->optionTemplate=$this->templatePath."attachOption.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_attachOptions());
				break;
				case "deleteimage":
					$this->libfunc->check_token();
					$obShopInterface->m_deleteImage();
				break;
				default:

					$obShopInterface->departmentTemplate=$this->templatePath."dspShopBuilder.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_showDepartments());
				break;

			}
			break;
			
			#HANDLING MODEL(DATABASE TRANSANCTION-SHOP BUILDER)
			case "ec_db":
				$obUpdateDb=new c_shopDb();
				$optionDb = new c_optionDb();
				$obUpdateDb->obDb=$this->obDb;
				$obUpdateDb->request=$this->request;
				$obUpdateDb->imagePath=SITE_PATH."images/";
				//die($this->request['type']);
				switch($action[1])
				{
					case "updateHome":
					$this->libfunc->check_token();
						if($this->request['type']=="product")
						{
							$obUpdateDb->m_updateHomeProduct();
						}
						elseif($this->request['type']=="content")
						{
							$obUpdateDb->m_updateHomeContent();
						}
						else
						{
							$obUpdateDb->m_updateHomeDept();
						}
					break;
					
					
					
					case "Dept":
					$this->libfunc->check_token();
						if($this->request['mode']=="edit")
						{
							if(!$obShopInterface->verifyEditDept())
							{
								$obUpdateDb->m_updateDept();
							}
							else
							{
								$obShopInterface->request['msg']=1;
								$obShopInterface->request['id']=$this->request['deptId'];
								$obShopInterface->request['type']=$this->request['type'];	$obShopInterface->departmentTemplate=$this->templatePath."dspDeptForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspDepartmentForm());
							
							}
						}
						else
						{
							if(!$obShopInterface->verifyInsertDept())
							{
								$obUpdateDb->m_insertDept();
							}
							else
							{
								if(empty($this->request['deptId']) )
								{
									$obShopInterface->request['dupeid']=$this->request['deptId'];
									$obShopInterface->request['type']=$this->request['type'];
								}
								$obShopInterface->request['msg']=1;
								$obShopInterface->departmentTemplate=$this->templatePath."dspDeptForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspDepartmentForm());

							}
						}
					break;
				
					case "updateAssociate":
					$this->libfunc->check_token();
						$obUpdateDb->m_updateAssociate();
					break;
					case "addDiscount":
					$this->libfunc->check_token();
						$obUpdateDb->m_addDiscount();
					break;
					case "attach":
					$this->libfunc->check_token();
						$obUpdateDb->m_attach();
					break;
					
					case "delRelation":
					$this->libfunc->check_token();
						$obUpdateDb->m_delRelation();
					break;
					case "updateSort":
						$obUpdateDb->m_updateSort();
					break;
					case "uploadDeptImages":
					$this->libfunc->check_token();
					if(!$obShopInterface->verifyImageUpload()){
						$obUpdateDb->m_uploadImage();
					}else{
						$obShopInterface->request['image']=$this->request['current_image'];
						$obShopInterface->uploadTemplate=$this->templatePath."uploadImages.tpl.htm";
						$obShopInterface->m_uploadForm();
					}
					break;

					case "insertProduct":
					$this->libfunc->check_token();
																				
						if($this->request['mode']=="edit")
						{
							if(!$obShopInterface->verifyEditProduct())
							{
								$obUpdateDb->m_updateProduct();
							}
							else
							{
								$obShopInterface->request['msg']=1;
								$obShopInterface->request['id']=$this->request['prodId'];
								$obShopInterface->productTemplate=$this->templatePath."dspProdForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspProductForm());
							
							}
						}
						else
						{
							if(!$obShopInterface->verifyInsertProduct())
							{
								$obUpdateDb->m_insertProduct();
							}
							else
							{
								if(empty($this->request['prodId']) )
								{
									$obShopInterface->request['dupeid']=$this->request['prodId'];
									$obShopInterface->request['type']=$this->request['type'];
								}
							
								$obShopInterface->request['msg']=1;
								$obShopInterface->productTemplate=$this->templatePath."dspProdForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspProductForm());

							}
						}
					
					break;
					
					case "content":
					$this->libfunc->check_token();
					if($this->request['mode']=="edit")
						{
							if(!$obShopInterface->verifyEditContent())
							{
								$obUpdateDb->m_updateContent();
							}
							else
							{
								$obShopInterface->request['msg']=1;
								$obShopInterface->request['id']=$this->request['contentId'];
								$obShopInterface->contentTemplate=$this->templatePath."dspContentForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspContentForm());
							
							}
						}
						else
						{
							if(!$obShopInterface->verifyInsertContent())
							{
								$obUpdateDb->m_insertContent();
							}
							else
							{
								if(empty($this->request['contentId']) )
								{
									$obShopInterface->request['dupeid']=$this->request['contentId'];
									$obShopInterface->request['type']=$this->request['type'];
								}
								$obShopInterface->request['msg']=1;
								$obShopInterface->contentTemplate=$this->templatePath."dspContentForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obShopInterface->m_dspContentForm());

							}
						}
					break;

					case "delDept":
						case "delProduct":
							case "delContent":
								$this->libfunc->check_token();
								$obUpdateDb->m_delete();
							break;
						break;
					break;
					case "delCInstance":
						case "delPInstance":
								$this->libfunc->check_token();
								$obUpdateDb->m_deleteInstance();
							break;
						break;
				
				}
			break;
			#HANDLING HELP PAGES
			case "help":
				$this->Template = MODULES_PATH."default/templates/admin/helpOuter.htm";
				$this->obTpl->set_file("mainContent",$this->Template);
			
				switch($action[1])
				{
					case "dept":
						$this->Template = MODULES_PATH."default/templates/help/department.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "product":
						$this->Template = MODULES_PATH."default/templates/help/products.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "content":
						$this->Template = MODULES_PATH."default/templates/help/content.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "orderlist":
						$this->Template = MODULES_PATH."default/templates/help/order_list.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Order List Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "associate":
						$this->Template = MODULES_PATH."default/templates/help/associate.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "menuhelp":
						$this->Template = MODULES_PATH."default/templates/help/menu.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "stdoption":
						$this->Template = MODULES_PATH."default/templates/help/options.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "ctmoption":
						$this->Template = MODULES_PATH."default/templates/help/choices.tpl.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "package":
						$this->Template = MODULES_PATH."default/templates/help/product_packages.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "vdiscount":
						$this->Template = MODULES_PATH."default/templates/help/volume_discounts.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "attach":
						$this->Template = MODULES_PATH."default/templates/help/choices.tpl.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					
				}
				$this->obTpl->pparse("return","mainContent");
				exit;
			break;
			
			#HANDLING MENU_FRONT END (VIEW)
			case "ec_menu":
				$obMenuInterface=new c_menuInterface();
				$obMenuInterface->obTpl=$this->obTpl;
				$obMenuInterface->obDb=$this->obDb;
				$obMenuInterface->request=$this->request;
				$obMenuInterface->imageUrl=SITE_URL."images/";
				$obMenuInterface->imagePath=SITE_PATH."images/";

				$obUpdateMenuDb=new c_menuDb();
				$obUpdateMenuDb->obDb=$this->obDb;
				$obUpdateMenuDb->request=$this->request;
				$obUpdateMenuDb->imagePath=SITE_PATH."images/";

				switch($action[1])
				{
					case "dspForm":
					$obMenuInterface->menuFormTemplate=$this->templatePath."formMenuHeader.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_formMenuHeaders());
					break;
					case "show":
					$obMenuInterface->menuHeadTemplate=$this->templatePath."dspMenuHeader.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_showMenuHeaders());
					break;
					case "menuadd":
					$this->libfunc->check_token();
						if($this->request['mode']=="edit")
						{
							if(!$obMenuInterface->m_verifyEditMenuHeader())
							{
								$obUpdateMenuDb->m_updateMenuHeader();
							}
							else
							{
								$obMenuInterface->request['msg']=1;
								$obMenuInterface->menuFormTemplate=$this->templatePath."formMenuHeader.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_formMenuHeaders());
							}
						}
						else
						{
						
							if(!$obMenuInterface->m_verifyInsertMenuHeader())
							{
								$obUpdateMenuDb->m_insertMenuHeader();
							}
							else
							{
								$obMenuInterface->request['msg']=1;
								$obMenuInterface->menuFormTemplate=$this->templatePath."formMenuHeader.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_formMenuHeaders());
			
							}
						}
					break;
					case "uploadForm":
					$obMenuInterface->uploadTemplate=MODULES_PATH."default/templates/admin/upload.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_uploadForm());
					 break;		
					case "upload":
					$this->libfunc->check_token();
					if(!$obMenuInterface->m_verifyImageUpload()){
						$obUpdateMenuDb->m_uploadImage();
					}else{
						$obMenuInterface->uploadTemplate=MODULES_PATH."default/templates/admin/upload.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_uploadForm());
					}
					 break;
					case "updatehome":
					$this->libfunc->check_token();
					 $obUpdateMenuDb->m_updateHomeMenuHeader();
					 break;

					case "deleteMenu":
					$this->libfunc->check_token();
					 $obUpdateMenuDb->m_deleteMenu();
					 break;

					case "itemForm":
					$obMenuInterface->menuFormTemplate=$this->templatePath."formMenuItems.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_formMenuItem());
					break;

					case "viewItems":
					$obMenuInterface->menuItemTemplate=$this->templatePath."dspMenuItems.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_showMenuItem());
					break;
					case "itemadd":
					$this->libfunc->check_token();
						if($this->request['mode']=="edit")
						{
							if(!$obMenuInterface->m_verifyEditMenuItem())
							{
								$obUpdateMenuDb->m_updateMenuItem();
							}
							else
							{
								$obMenuInterface->request['msg']=1;
								$obMenuInterface->menuFormTemplate=$this->templatePath."formMenuItems.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_formMenuItem());
				
							}
						}
						else
						{
						
							if(!$obMenuInterface->m_verifyInsertMenuItem())
							{
								$obUpdateMenuDb->m_insertMenuItem();
							}
							else
							{
								$obMenuInterface->request['msg']=1;
								$obMenuInterface->menuFormTemplate=$this->templatePath."formMenuItems.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_formMenuItem());
			
							}
						}
					break;
					case "itemhome":
					 $obUpdateMenuDb->m_updateHomeMenuItem();
					 break;
					case "deleteItem":
					$this->libfunc->check_token();
					 $obUpdateMenuDb->m_deleteItem();
					 break;


					default:
					$obMenuInterface->menuHeadTemplate=$this->templatePath."dspMenuHeader.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obMenuInterface->m_showMenuHeaders());
					break;
				}
				break;

				case "ec_option":
				$obOptionInterface=new c_optionInterface();
				$obOptionInterface->obTpl=$this->obTpl;
				$obOptionInterface->obDb=$this->obDb;
				$obOptionInterface->request=$this->request;
				$obOptionInterface->imageUrl=SITE_URL."images/";
				$obOptionInterface->imagePath=SITE_PATH."images/";

				$obUpdateOptionDb=new c_optionDb();
				$obUpdateOptionDb->obDb=$this->obDb;
				$obUpdateOptionDb->request=$this->request;
				$obUpdateOptionDb->imagePath=SITE_PATH."images/";
				#INTIALIZING ACTION
				if(!isset($action[1]))
					$action[1]="";
				switch($action[1])
				{
					case "home":
					$obOptionInterface->optionsTemplate=$this->templatePath."optionHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showOptions());
					break;
					
					case "dspStandardOpt":					
					$obOptionInterface->optionsTemplate=$this->templatePath."optionStandard.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showStandardOpt());
					break;
					
					case "dspCustomOpt":					
						$obOptionInterface->optionsTemplate=$this->templatePath."optionCustom.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showCustomOpt());
					break;
					
					case "dspAttributes":					
						$obOptionInterface->attributeTemplate=$this->templatePath."attributes.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showAttribute());
					break;
					
					case "dspAddattribute":
					$obOptionInterface->addattributeTemplate=$this->templatePath."addattributes.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showAddAttribute());
					break;
					
					case "dspNumForm":
					$obOptionInterface->optionNumTemplate=$this->templatePath."formNumOption.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_formNumOptions());
					break;

					case "stdOptForm":
					$obOptionInterface->optionTemplate=$this->templatePath."formOptions.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showOptionForm());
					break;
					
					case "ajaxAttribute":
					$obUpdateOptionDb->m_ajaxgetAtrribute();
					break;
					
					case "optionadd":
					$this->libfunc->check_token();
				
						if($obOptionInterface->m_verifyInsertOption())
						{	
							
							$images = $obUpdateOptionDb->m_uploadImages();
							$obUpdateOptionDb->m_insertOption($images);
						}
						else
						{
							$obOptionInterface->request['msg']=1;
							$obOptionInterface->optionTemplate=$this->templatePath."formOptions.tpl.htm";
							$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showOptionForm());
						}
					break;
					case "optionedit":
					$this->libfunc->check_token();
							if($obOptionInterface->m_verifyEditOption())
							{
								$obUpdateOptionDb->m_updateOption();
							}
							else
							{
								$obOptionInterface->request['msg']=1;
								$obOptionInterface->optionTemplate=$this->templatePath."formOptions.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showOptionForm());
				
							}
					break;
					case "editForm":
					$obOptionInterface->optionTemplate=$this->templatePath."formEditOptions.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_formEditOption());
					break;
					case "uploadForm":
					$this->libfunc->check_token();
					$obOptionInterface->uploadTemplate=MODULES_PATH."default/templates/admin/upload.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_uploadForm());
					 break;		
					case "upload":
					$this->libfunc->check_token();
					if(!$obOptionInterface->m_verifyImageUpload()){
						$obUpdateOptionDb->m_uploadImage();
					}else{
						$obOptionInterface->uploadTemplate=MODULES_PATH."default/templates/admin/upload.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_uploadForm());
					}
					 break;
					 
					case "addattribute":
					$this->libfunc->check_token();
					$obUpdateOptionDb->m_insertAttribute();
					break;
					
					case "editattribute":
					$this->libfunc->check_token();
					$obUpdateOptionDb->m_editAttribute();
					break;
					
					case "deleteattribute":
					$this->libfunc->check_token();
					$obUpdateOptionDb->m_delAttribute();
					break; 
					 
					case "delete":
					$this->libfunc->check_token();
					$obUpdateOptionDb->m_deleteOption();
					break;	
					case "deleteChoice":
					$this->libfunc->check_token();
					$obUpdateOptionDb->m_deleteChoice();
					break;

					case "editChoice":
					case "ctmOptForm":
					$obOptionInterface->optionTemplate=$this->templatePath."formCustomOption.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_customOptionForm());
					break;
					break;
					case "choiceadd":
					$this->libfunc->check_token();
						if($this->request['mode']=="edit")
						{
							if(!$obOptionInterface->m_verifyEditChoice())
							{
								$obUpdateOptionDb->m_updateChoice();
							}
							else
							{
								$obOptionInterface->request['msg']=1;
								$obOptionInterface->optionTemplate=$this->templatePath."formCustomOption.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_customOptionForm());
				
							}
						}
						else
						{
						
							if(!$obOptionInterface->m_verifyInsertChoice())
							{
								$obUpdateOptionDb->m_insertChoice();
							}
							else
							{
								$obOptionInterface->request['msg']=1;
								$obOptionInterface->optionTemplate=$this->templatePath."formCustomOption.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_customOptionForm());
			
							}
						}
					break;
					default:
					$obOptionInterface->optionsTemplate=$this->templatePath."optionHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obOptionInterface->m_showOptions());	break;
				}
			break;
			case "ec_package":
				$obPackInterface=new c_packageInterface();
				$obPackInterface->obTpl=$this->obTpl;
				$obPackInterface->obDb=$this->obDb;
				$obPackInterface->request=$this->request;
				$obPackInterface->imageUrl=SITE_URL."images/";
				$obPackInterface->imagePath=SITE_PATH."images/";

				$obUpdatePackDb=new c_packageDb();
				$obUpdatePackDb->obDb=$this->obDb;
				$obUpdatePackDb->request=$this->request;
				$obUpdatePackDb->imagePath=SITE_PATH."images/";
			#INTIALIZING ACTION
				if(!isset($action[1]))
					$action[1]="";
				switch($action[1])
				{
					case "home":
					$obPackInterface->packageTemplate=$this->templatePath."packageHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPackInterface->m_packageHome());
					break;
					case "build":
					$obPackInterface->packageTemplate=$this->templatePath."buildPackage.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPackInterface->m_packageBuild());
					break;
					case "disamble":
					$obPackInterface->packageTemplate=$this->templatePath."disamblePackage.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPackInterface->m_packageDisamble());
					break;
					 case "disambleit":
					$this->libfunc->check_token();
					 $obUpdatePackDb->m_disamblePack();
					 break;
					 case "delete":
					$this->libfunc->check_token();
					 $obUpdatePackDb->m_deletePackItem();
					 break;
					 case "update":
					$this->libfunc->check_token();
					 $obUpdatePackDb->m_updatePackage();
					 break;
					 case "updateHome":
					 $obUpdatePackDb->m_updateHome();
					 break;
					 case "updatePackHome":
					 $obUpdatePackDb->m_updatePackHome();
					 break;
					 
					default:
					$obPackInterface->packageTemplate=$this->templatePath."packageHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPackInterface->m_packageHome());	break;
				}
			break;

			default:
				header("Location:".SITE_URL."ecom/adminindex.php?action=ec_show.home");
				exit;
			break;

		}

	}

}