<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
# Class provides the MArketing interface functionlaity
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/email_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/email_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/promotion_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/promotion_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/discount_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/discount_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/giftcert_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/giftcert_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/giftwrap_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/giftwrap_db.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/reports_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/froogle.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/google_interface.php")); 
include_once($pluginInterface->plugincheck(MODULES_PATH."sales/classes/admin/feeds_interface.php")); 
include_once($pluginInterface->plugincheck(SITE_PATH."sales/admin_messages.php")); 

include_once($pluginInterface->plugincheck(SITE_PATH."sales/admin_messages.php")); 
class c_salesAdminController
{

	# Class Constructor
	function c_salesAdminController($obDatabase,$obTemplate,$attributes,$libfunc)
	{
		$this->obDb=$obDatabase;
		$this->obTpl=&$obTemplate;
		$this->request=$attributes;
		$this->libfunc=$libfunc;
		$this->templatePath=ADMINTHEMEPATH."sales/templates/admin/";
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

		$obFroogle=new c_froogleClass();
		$obFroogle->obDb=$this->obDb;
		$obFroogle->request=$this->request;

		switch($action[0])
		{			
			case "google":		
				
				#Declare GoogleInterface class
				$obGoogle				=	new c_googleInterface();
				$obGoogle->obDb			=	$this->obDb;
				$obGoogle->request		=	$this->request;				
				
				switch($action[1]) {				
					case "home":					
					$obGoogle->googleTemplate=$this->templatePath."googleHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obGoogle->m_dspHome());
					break;
				}				
			break;#END GOOGLE
			
				case "froogle":
			switch($action[1])
			{
				case "home":
				$obFroogle->froogleTemplate=$this->templatePath."froogleHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obFroogle->m_froogleHome());
				break;
				case "csvupload":
					$this->libfunc->check_token();
				$obFroogle->m_generateFroogleFeedFile();
				break;
				case "form":
				$obFroogle->froogleTemplate=$this->templatePath."froogle_settings.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obFroogle->m_froogleForm());
				break;
				case "update":
					$this->libfunc->check_token();
				if($obFroogle->m_validateFroogleSettings()!=1)
				{
					$obFroogle->m_updateFroogle();
				}
				else
				{
					$obFroogle->froogleTemplate=$this->templatePath."froogle_settings.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFroogle->m_froogleForm());
				}
				break;
			}
			break;

		case "feeds":
		$obFeed_interface = new feed_interface();
		$obFeed_interface->obDb=$this->obDb;
		$obFeed_interface->obTpl=$this->obTpl;
		$obFeed_interface->request=$this->request;
		
				switch ($action[1])
				{
					case "home":
					$obFeed_interface->Feedtemplate=$this->templatePath."feedshome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFeed_interface->m_dspFeedshome());
					break;
					case "products":
					$obFeed_interface->rssProdTemplate=$this->templatePath."feeds_products.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFeed_interface->m_dspProductFeed());
					break;
					case "articles":
					$obFeed_interface->rssArticleTemplate=$this->templatePath."feeds_articles.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obFeed_interface->m_dspArticleFeed());
					break;
					case "prodUpdate":
					$this->libfunc->check_token();
					$obFeed_interface->m_updateProductFeed();
					break;
					case "articleUpdate":
					$this->libfunc->check_token();
					$obFeed_interface->m_updateArticleFeed();
					break;
					
				}
		break;
//--------------------		
			case "promotions":
			$obPromotionInterface=new c_promotionInterface();
			$obPromotionInterface->obTpl=$this->obTpl;
			$obPromotionInterface->obDb=$this->obDb;
			$obPromotionInterface->request=$this->request;

			$obPromotionDb=new c_promotionDb();
			$obPromotionDb->obDb=$this->obDb;
			$obPromotionDb->request=$this->request;

			switch($action[1])	{
				case "home":
				$obPromotionInterface->discountTemplate=$this->templatePath."promotionsHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_dspPromotions());
				break;

				case "flat":
				$obPromotionInterface->discountTemplate=$this->templatePath."flatDiscount.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_flatDiscount());
				break;
				case "updateflat":
					$this->libfunc->check_token();
				if(!$obPromotionInterface->m_verifyEditFlat())
				{
					$obPromotionDb->m_insertFlatDiscount();
				}
				else
				{
					$obDiscountInterface->request['id']=$this->request['id'];
					$obPromotionInterface->discountTemplate=$this->templatePath."flatDiscount.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_flatDiscount());
				}
				break;

				case "range":
				$obPromotionInterface->discountTemplate=$this->templatePath."ranges.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_rangeDiscount());
				break;
				case "updaterange":
					$this->libfunc->check_token();
				if(!$obPromotionInterface->m_verifyEditRange())
				{
					$obPromotionDb->m_insertRangeDiscount();
				}
				else
				{
					$obDiscountInterface->request['id']=$this->request['id'];
					$obPromotionInterface->discountTemplate=$this->templatePath."flatDiscount.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_rangeDiscount());
				}
				break;

				case "free":
				$obPromotionInterface->discountTemplate=$this->templatePath."freePostage.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_freeDiscount());
				break;
				case "updatefree":
					$this->libfunc->check_token();
				if(!$obPromotionInterface->m_verifyEditFree())
				{
					$obPromotionDb->m_insertFreeDiscount();
				}
				else
				{
					$obPromotionInterface->discountTemplate=$this->templatePath."freePostage.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_freeDiscount());
				}
				break;
				
				case "giftcert":
				$obGiftCertInterface=new c_giftCertInterface();
				$obGiftCertInterface->obTpl=$this->obTpl;
				$obGiftCertInterface->obDb=$this->obDb;
				$obGiftCertInterface->request=$this->request;

				$obGiftCertDb=new c_giftCertDb();
				$obGiftCertDb->obDb=$this->obDb;
				$obGiftCertDb->request=$this->request;
				switch($action[2])
				{
					
					case "home":
					$obGiftCertInterface->giftCertTemplate=$this->templatePath."giftCertHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obGiftCertInterface->m_dspGiftCert());
					break;
					case "dspForm":
					$obGiftCertInterface->giftCertTemplate=$this->templatePath."giftCertForm.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obGiftCertInterface->m_giftCertBuilder());
					break;
					case "update":
					$this->libfunc->check_token();
						if(isset($this->request['mode']) && $this->request['mode']=="edit")
						{
							if(!$obGiftCertInterface->m_verifyEdit())
							{
								$obGiftCertDb->m_updateGiftCert();
							}
							else
							{
								$obGiftCertInterface->request['id']=$this->request['id'];
								$obGiftCertInterface->giftCertTemplate=$this->templatePath."giftCertForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obGiftCertInterface->m_giftCertBuilder());
							}
						}
						else
						{
							if(!$obGiftCertInterface->m_verifyInsert())
							{
								$obGiftCertDb->m_insertGiftCert();
							}
							else
							{
								$obGiftCertInterface->giftCertTemplate=$this->templatePath."giftCertForm.tpl.htm";
									$this->obTpl->set_var("TPL_VAR_BODY",$obGiftCertInterface->m_giftCertBuilder());
							}
						}
					break;
					case "delete":
					$this->libfunc->check_token();
						$obGiftCertDb->m_giftCertDelete();
					break;
					case "updateHome":
						$obGiftCertDb->m_updateHome();
					break;
					}#END SWITH 2
				break;#END GIFT CERTIFICATE

				case "giftwrap":
				$obGiftWrapInterface=new c_giftWrapInterface();
				$obGiftWrapInterface->obTpl=$this->obTpl;
				$obGiftWrapInterface->obDb=$this->obDb;
				$obGiftWrapInterface->request=$this->request;
				$obGiftWrapInterface->imagePath=SITE_PATH."images/";
				$obGiftWrapInterface->imageUrl=SITE_URL."images/";
				$obGiftWrapDb=new c_giftWrapDb();
				$obGiftWrapDb->obDb=$this->obDb;
				$obGiftWrapDb->request=$this->request;
				$obGiftWrapDb->imagePath=SITE_PATH."images/";
				switch($action[2])
				{
					
					case "home":
					$obGiftWrapInterface->giftWrapTemplate=$this->templatePath."giftWrapHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obGiftWrapInterface->m_dspGiftWrap());
					break;
					case "dspForm":
					$obGiftWrapInterface->giftWrapTemplate=$this->templatePath."giftWrapForm.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obGiftWrapInterface->m_giftWrapBuilder());
					break;
					case "uploadForm":
					$obGiftWrapInterface->browseTemplate=MODULES_PATH."default/templates/admin/upload.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obGiftWrapInterface->m_uploadForm());
					break;
					
					case "update":
					$this->libfunc->check_token();
						if(isset($this->request['mode']) && $this->request['mode']=="edit")
						{
							if(!$obGiftWrapInterface->m_verifyEdit())
							{
								$obGiftWrapDb->m_updateGiftWrap();
							}
							else
							{
								$obGiftWrapInterface->request['id']=$this->request['id'];
								$obGiftWrapInterface->giftWrapTemplate=$this->templatePath."giftWrapForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obGiftWrapInterface->m_giftWrapBuilder());
							}
						}
						else
						{
							if(!$obGiftWrapInterface->m_verifyInsert())
							{
								$obGiftWrapDb->m_insertGiftWrap();
							}
							else
							{
								$obGiftWrapInterface->giftWrapTemplate=$this->templatePath."giftWrapForm.tpl.htm";
								$this->obTpl->set_var("TPL_VAR_BODY",$obGiftWrapInterface->m_giftWrapBuilder());
							}
						}
					break;
					case "upload":
					$this->libfunc->check_token();
						$obGiftWrapDb->m_uploadImage();
					break;
					case "delete":
					$this->libfunc->check_token();
						$obGiftWrapDb->m_giftWrapDelete();
					break;
					case "updateHome":
						$obGiftWrapDb->m_updateHome();
					break;
					}#END SWITH 2
				break;#END GIFT WRAP

				case "discount":
				$obDiscountInterface=new c_discountInterface();
				$obDiscountInterface->obTpl=$this->obTpl;
				$obDiscountInterface->obDb=$this->obDb;
				$obDiscountInterface->request=$this->request;

				$obDiscountDb=new c_discountDb();
				$obDiscountDb->obDb=$this->obDb;
				$obDiscountDb->request=$this->request;
				switch($action[2])
				{
					
					case "home":
					$obDiscountInterface->discountTemplate=$this->templatePath."discountHome.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obDiscountInterface->m_dspDiscounts());
					break;
					case "dspForm":
					$obDiscountInterface->discountTemplate=$this->templatePath."discountForm.tpl.htm";
					$this->obTpl->set_var("TPL_VAR_BODY",$obDiscountInterface->m_discountBuilder());
					break;
					case "update":
					$this->libfunc->check_token();
						if(isset($this->request['mode']) && $this->request['mode']=="edit")
						{
							if(!$obDiscountInterface->m_verifyEdit())
							{
								$obDiscountDb->m_updateDiscount();
							}
							else
							{
								$obDiscountInterface->request['id']=$this->request['id'];
								$obDiscountInterface->discountTemplate=$this->templatePath."discountForm.tpl.htm";
									$this->obTpl->set_var("TPL_VAR_BODY",$obDiscountInterface->m_discountBuilder());
							}
						}
						else
						{
							if(!$obDiscountInterface->m_verifyInsert())
							{
								$obDiscountDb->m_insertDiscount();
							}
							else
							{
								$obDiscountInterface->discountTemplate=$this->templatePath."discountForm.tpl.htm";
									$this->obTpl->set_var("TPL_VAR_BODY",$obDiscountInterface->m_discountBuilder());
							}
						}
					break;
					case "delete":
					$this->libfunc->check_token();
						$obDiscountDb->m_discountDelete();
					break;
					case "updateHome":
						$obDiscountDb->m_updateHome();
					break;
					}#END SWITH 2
					break;#END DISCOUNT
					#point discount
					case 'memberpoint';
						$obPromotionInterface->discountTemplate=$this->templatePath."memberpointDiscount.tpl.htm";
						$this->obTpl->set_var("TPL_VAR_BODY",$obPromotionInterface->m_memberpointDiscount());

					break;
					case 'updatememberpoint';
					$this->libfunc->check_token();
							$obPromotionDb->m_updatememberpoint();
					break;


					default:
						header("Location:".SITE_URL."sales/adminindex.php?action=promotions.home");
					break;
		
				}#END SWICTH 1
			break;	#END PROMOTION
			case "help":
				$this->Template = MODULES_PATH."default/templates/admin/helpOuter.htm";
				$this->obTpl->set_file("mainContent",$this->Template);
			
				switch($action[1])
				{
					// [DRK]
					case "analytics":
						$this->Template = MODULES_PATH."default/templates/help/analytics.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Google Analytics Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					// [/DRK]
					case "froogle":
						$this->Template = MODULES_PATH."default/templates/help/froogle.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Froogle Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "email":
						$this->Template = MODULES_PATH."default/templates/help/email.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "lead":
						$this->Template = MODULES_PATH."default/templates/help/email_leads.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "discount":
						$this->Template = MODULES_PATH."default/templates/help/discounts.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "giftcert":
						$this->Template = MODULES_PATH."default/templates/help/gift_certificates.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "giftwrap":
						$this->Template = MODULES_PATH."default/templates/help/add_ons.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
						$this->obTpl->set_var("TPL_VAR_PAGETITLE","Department Help");
						$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
					case "promotion":
						$this->Template = MODULES_PATH."default/templates/help/promotions.htm";
						$this->obTpl->set_file("innerContent",$this->Template);
					$this->obTpl->set_var("TPL_VAR_HELPBODY",$this->obTpl->parse("return","innerContent"));
					break;
				}
				$this->obTpl->pparse("return","mainContent");
				exit;
			break;
			
			case "report":
			$obOrderInterface=new c_orderReport();
			$obOrderInterface->obTpl=$this->obTpl;
			$obOrderInterface->obDb=$this->obDb;
			$obOrderInterface->request=$this->request;

			switch($action[1])
			{
				case "order":
				$obOrderInterface->orderTemplate=$this->templatePath."orderReports.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrderInterface->m_orderReport());
				break;
				case "today":
				$obOrderInterface->orderTemplate=$this->templatePath."todayOrder.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrderInterface->m_todayReport());
				break;
				case "home":
				$obOrderInterface->orderTemplate=$this->templatePath."reportHome.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$obOrderInterface->m_reportHome());
				break;
		
			}#END SWITH 1
			break;#END EMAIL
			default:
				header("Location:".SITE_URL."sales/adminindex.php?action=promotions.home");
			break;
			}#END SWICTH 1

	}#END EVENT HANDLER FUNCTION

}#CLASS END