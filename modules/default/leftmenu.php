<?php
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
CLASS HANDLES LEFT MENU FOR SITE-
HANDLES META TAGS
HANDLES INLINE EDITOR
*/

defined('_TEEXEC') or die;
class c_leftMenu
{
	#CONSTRUCTOR
	function c_leftMenu($obDatabase,&$obMainTemplate,$attributes)
	{
		$this->obDb				=$obDatabase;
		$this->request				=$attributes;
		$this->obTpl				=&$obMainTemplate;
		$this->templatePath		=THEMEPATH."default/templates/main/";
		$this->selected			=" class='selected' ";
		$this->libFunc				=new c_libFunctions();
		$this->showPageMetaTags();
		$this->m_leftMenuDisplay();
	}

	#FUNCTION TO DISPLAY LEFT MENU
	function m_leftMenuDisplay()
	{
		$this->Template = $this->templatePath."leftMenu.tpl.htm";
		$this->obTpl->set_file("TPL_LEFT_FILE",$this->Template);
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_TREEMENU_BLK","tree_blk");
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_DEPARTMENT_BLK","department_blk");
		$this->obTpl->set_block("TPL_DEPARTMENT_BLK", 
		"TPL_SUB_DEPARTMENT_MAIN_BLK1","sub_department_main_blk1");
		$this->obTpl->set_block("TPL_SUB_DEPARTMENT_MAIN_BLK1", "TPL_SUB_DEPARTMENT_BLK1","sub_department_blk1");
		$this->obTpl->set_block("TPL_SUB_DEPARTMENT_BLK1", "TPL_SUB_DEPARTMENT_MAIN_BLK2","sub_department_main_blk2");
		$this->obTpl->set_block("TPL_SUB_DEPARTMENT_MAIN_BLK2", "TPL_SUB_DEPARTMENT_BLK2","sub_department_blk2");


		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_WISHLINK_BLK","wishlink_blk");
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_COMPARE_BLK","compare_blk");
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_MENUHEAD_BLK","menuhead_blk");
		$this->obTpl->set_block("TPL_MENUHEAD_BLK","TPL_MENU_BLK","menu_blk");
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_SHOPBYBRAND_BLK","shopbybrand_blk");
		$this->obTpl->set_block("TPL_SHOPBYBRAND_BLK","TPL_BRAND_BLK","brand_blk");
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_NEWSLETTER_BLK","newsletter_blk");
		$this->obTpl->set_block("TPL_LEFT_FILE","TPL_MAINRECENT_BLK","mainrecent_blk");
		$this->obTpl->set_block("TPL_MAINRECENT_BLK","TPL_RECENT_BLK","recent_blk");
		
		#INTIALIZING
		$this->obTpl->set_var("tree_blk","");
		$this->obTpl->set_var("department_blk","");
		$this->obTpl->set_var("sub_department_main_blk1","");
		$this->obTpl->set_var("sub_department_blk1","");
		$this->obTpl->set_var("sub_department_main_blk2","");
		$this->obTpl->set_var("sub_department_blk2","");
		$this->obTpl->set_var("menuhead_blk","");
		$this->obTpl->set_var("wishlink_blk","");
		$this->obTpl->set_var("compare_blk","");
		$this->obTpl->set_var("menu_blk","");
		$this->obTpl->set_var("shopbybrand_blk","");
		$this->obTpl->set_var("brand_blk","");
		$this->obTpl->set_var("newsletter_blk","");
		$this->obTpl->set_var("mainrecent_blk","");
		$this->obTpl->set_var("recent_blk","");
		
		
		$this->obTpl->set_var("TPL_VAR_HOMECLASS","");
		$this->obTpl->set_var("TPL_VAR_ACTCLASS","");
		$this->obTpl->set_var("TPL_VAR_VIEWCARTCLASS","");
		$this->obTpl->set_var("TPL_VAR_WISHLISTCLASS","");
		$this->obTpl->set_var("TPL_VAR_SIGNUPCLASS","");
		
		##Defining language pack variables for headings left menu
		$this->obTpl->set_var("LANG_VAR_HOMEPAGE",LANG_HOME_PAGE);
		$this->obTpl->set_var("LANG_VAR_DEPARTMENTHEADER",LANG_DEPARTMENTS);
		$this->obTpl->set_var("LANG_VAR_SEARCHLABEL",LANG_SEARCH);
		$this->obTpl->set_var("LANG_VAR_GO",LANG_GO);
		$this->obTpl->set_var("LANG_VAR_MYTOOLS",LANG_MYTOOLS);
		$this->obTpl->set_var("LANG_VAR_MYACCOUNT",LANG_MYACCOUNT);
		$this->obTpl->set_var("LANG_VAR_MYWISHLIST",LANG_MYWISHLIST);
		$this->obTpl->set_var("LANG_VAR_VIEWBASKET",LANG_VIEWBASKET);
		$this->obTpl->set_var("LANG_VAR_YOUAREHERE",LANG_YOUARE_HERE);
		$this->obTpl->set_var("LANG_VAR_ITEMSINBASKET",LANG_ITEMS_BASKET);
		$this->obTpl->set_var("LANG_VAR_TOTAL",LANG_TOTAL);
	

		//$this->obTpl->set_var("TPL_VAR_NEWSLETTER",SITE_URL."user/index.php?action=user.addnewsletter");
		$this->obTpl->set_var("TPL_VAR_NEWSLETTER","");

		$this->request['action']=$this->libFunc->ifSet($this->request,"action","");
	
		if(empty($this->request['action']))
		{
			$this->obTpl->set_var("TPL_VAR_HOMECLASS",$this->selected);
		}
		elseif($this->request['action']=='user.home')
		{
			$this->obTpl->set_var("TPL_VAR_ACTCLASS",$this->selected);
		}
		elseif($this->request['action']=='ecom.viewcart')
		{
			$this->obTpl->set_var("TPL_VAR_VIEWCARTCLASS",$this->selected);
		}
		elseif($this->request['action']=='wishlist.display')
		{
			$this->obTpl->set_var("TPL_VAR_WISHLISTCLASS",$this->selected);
		}
		elseif($this->request['action']=='compare.display')
		{
			$this->obTpl->set_var("TPL_VAR_COMPARECLASS",$this->selected);
		}
		elseif($this->request['action']=='user.loginForm')
		{
			$this->obTpl->set_var("TPL_VAR_SIGNUPCLASS",$this->selected);
		}
		#SAFE URLS
		$homeUrl = SITE_URL;
		$this->obTpl->set_var("TPL_VAR_HOMEURL",$this->libFunc->m_safeUrl($homeUrl));

		$searchUrl = SITE_URL."ecom/index.php?action=ecom.search";
		$this->obTpl->set_var("TPL_VAR_SEARCHURL",$this->libFunc->m_safeUrl($searchUrl));

		$myAccountUrl = SITE_URL."user/index.php?action=user.home";
		$this->obTpl->set_var("TPL_VAR_MYACCOUNT",$this->libFunc->m_safeUrl($myAccountUrl));
		
		$myShopUrl = SITE_URL."ecom/index.php?action=ecom.viewcart";
		$this->obTpl->set_var("TPL_VAR_VIEWCART",$this->libFunc->m_safeUrl($myShopUrl));
		
		//Start shop by brand block
		$this->obDb->query="SELECT iVendorid_PK,vCompany FROM ".SUPPLIERS." ORDER BY vCompany ASC";
		$rsSupplier = $this->obDb->fetchQuery();
		$rsSupplierCount=$this->obDb->record_count;
		
		$BrandsearchUrl = SITE_URL."ecom/index.php?action=ecom.brand";
		$this->obTpl->set_var("TPL_VAR_BRANDURL",$this->libFunc->m_safeUrl($BrandsearchUrl));
		
		if(SHOPBYBRAND == 1 && $rsSupplierCount > 0)
		{
			
			for ($i=0;$i<$rsSupplierCount;$i++)
			{
				
				$this->obTpl->set_var("TPL_VAR_BRANDID",$rsSupplier[$i]->iVendorid_PK);
				$this->obTpl->set_var("TPL_VAR_SUPPLIER",$rsSupplier[$i]->vCompany);
						
				$this->obTpl->parse("brand_blk","TPL_BRAND_BLK",true);
			}
		
			$this->obTpl->parse("shopbybrand_blk","TPL_SHOPBYBRAND_BLK");	
				
		}
		
		if(NEWSLETTER_NAV ==1)
		{
			//$this->obTpl->parse("newsletter_blk","TPL_NEWSLETTER_BLK");	
		}
		
		$this->obTpl->set_var("TPL_VAR_PRODUCTTITLE","");
		$this->obTpl->set_var("TPL_VAR_PRODUCTURL","");
		
		
		if(RECENTVIEWED ==1)
		{
			if(isset($_COOKIE['jimbeam'])){
				$productUrl = SITE_URL."ecom/index.php?action=ecom.pdetails&mode=";
				$productUrl = $this->libFunc->m_safeUrl($productUrl);
			$x = 1;		
			 foreach ($_COOKIE['jimbeam'] as $name => $value)
	    		{
					$value = htmlentities($value);
					//echo "RVP:".RVP_LIMIT." x:".$x."\n";
					if(RVP_LIMIT == 0 || $x <= RVP_LIMIT)
					{
						$this->obDb->query="SELECT vTitle FROM ".PRODUCTS." WHERE vSeoTitle = '".$value."'";
						$rsProd = $this->obDb->fetchQuery();
						$this->obTpl->set_var("TPL_VAR_PRODUCTTITLE",stripslashes($rsProd[0]->vTitle));
						$this->obTpl->set_var("TPL_VAR_PRODUCTURL",$productUrl.$value);
						$this->obTpl->parse("recent_blk","TPL_RECENT_BLK",true);
					}
					$x = $x + 1;
	    		}
				$this->obTpl->parse("mainrecent_blk","TPL_MAINRECENT_BLK");			
			}
			
		}
		
		if(USEWISHLIST==1)
		{
			##WISHLIST URL
			$myWishlistUrl = SITE_URL."ecom/index.php?action=wishlist.display";
			$this->obTpl->set_var("TPL_VAR_WISHLIST",$this->libFunc->m_safeUrl($myWishlistUrl));
			$this->obTpl->parse("wishlink_blk","TPL_WISHLINK_BLK");
		}
		
		if(USECOMPARE==1)
		{
			##WISHLIST URL
			$myComparelistUrl = SITE_URL."ecom/index.php?action=compare.display";
			$this->obTpl->set_var("TPL_VAR_COMPARE",$this->libFunc->m_safeUrl($myComparelistUrl));
			$this->obTpl->parse("compare_blk","TPL_COMPARE_BLK");
		}
		
		
		if(isset($_SESSION['userid']) && isset($_SESSION['username']))
		{
			$mySignUpUrl = SITE_URL."user/index.php?action=user.logout";
			$this->obTpl->set_var("TPL_VAR_SIGNUP",$this->libFunc->m_safeUrl($mySignUpUrl));
			$this->obTpl->set_var("TPL_VAR_LABEL","Logout");
		}
		else
		{
			$mySignUpUrl = SITE_URL."user/index.php?action=user.loginForm";
			$this->obTpl->set_var("TPL_VAR_SIGNUP",$this->libFunc->m_safeUrl($mySignUpUrl));
			$this->obTpl->set_var("TPL_VAR_LABEL","Sign in/Register");
		}
		
		//HIGHLISTS THE CURRENT DEPARTMENT. CURRENTLY SELECTS ONLY THE FIRST ASSIGNED...
		$action=$this->libFunc->ifSet($this->request,"action","");
		$mode=$this->libFunc->ifSet($this->request,"mode","");
		if($action == "ecom.pdetails")
		{
			$this->obDb->query="SELECT iProdid_PK FROM ".PRODUCTS." P WHERE (vSeoTitle='".$mode."')";
			$myresults = $this->obDb->fetchQuery();
			$productid = $myresults[0]->iProdid_PK;
			$this->obDb->query="SELECT iOwner_FK FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE (iSubId_FK=".$productid." AND vtype='product' AND vOwnerType='department' AND iOwner_FK>0 AND iState=1)";
			$myresults = $this->obDb->fetchQuery();
			$departmentid = $myresults[0]->iOwner_FK;
			$this->obDb->query="SELECT vSeoTitle FROM ".DEPARTMENTS." D WHERE (iDeptid_PK='".$departmentid."')";
			$myresults = $this->obDb->fetchQuery();
			$selectedDept["vSeoTitle"] = $myresults[0]->vSeoTitle;
		}
		else if($action == "ecom.details" && isset($this->request['mode']))
		{
			$selectedDept["vSeoTitle"]= $this->m_getMainDept($this->request['mode']);
		}
		else if(isset($this->request['mode']))
		{
			$selectedDept["vSeoTitle"]= $this->m_getMainDept($this->request['mode']);
		}
		else
		{
			$selectedDept["vSeoTitle"] = META_TITLE;
		}
		
		$this->request['mode']=$this->libFunc->ifSet($this->request,"mode","");
		//$selectedDept=$this->m_getMainDept($this->request['mode']);
		#QUERY DEPARTMENT TABLE
		$this->obDb->query="SELECT vTitle,vSeoTitle,iDeptid_PK,vImage1 FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE (iDeptid_PK=iSubId_FK AND vtype='department' AND iOwner_FK='0' AND vOwnerType='department' AND iState=1 AND iDisplayInNav=1) ORDER BY iSort";
		$rsDept = $this->obDb->fetchQuery();
		$rsDeptCount=$this->obDb->record_count;
			
		if($rsDeptCount>0)
		{
			$this->obTpl->set_var("department_blk","");
			for($i=0;$i<$rsDeptCount;$i++)
			{
				$this->obTpl->set_var("sub_department_main_blk1","");
				$this->obTpl->set_var("TPL_VAR_DEPTURL",$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$rsDept[$i]->vSeoTitle));
				if($selectedDept["vSeoTitle"]==$rsDept[$i]->vSeoTitle){
					$this->obTpl->set_var("TPL_VAR_DEPTCLASS",$this->selected);
				}else{
					$this->obTpl->set_var("TPL_VAR_DEPTCLASS","");
				}
				$this->obTpl->set_var("TPL_VAR_ID",$rsDept[$i]->iDeptid_PK);
				if(!empty($rsDept[$i]->vImage1)){
					$img=$this->libFunc->m_checkFile($rsDept[$i]->vImage1,"department",$this->libFunc->m_displayContent($rsDept[$i]->vTitle));
					if($img){
						$this->obTpl->set_var("TPL_VAR_DNAME",$img);
					}else{
						$this->obTpl->set_var("TPL_VAR_DNAME", $this->libFunc->m_displayContent($rsDept[$i]->vTitle));
					}
				}else{
					$this->obTpl->set_var("TPL_VAR_DNAME",$this->libFunc->m_displayContent($rsDept[$i]->vTitle));
				}

					#QUERY SUB DEPARTMENT1-BLOCK-----------------------------------------------------------
				$this->obDb->query="SELECT vTitle,vSeoTitle,iDeptid_PK,vImage1 FROM ".DEPARTMENTS." D,";
				$this->obDb->query.=" ".FUSIONS." F WHERE (iDeptid_PK=iSubId_FK AND vtype='department' AND ";
				$this->obDb->query.=" iOwner_FK='".$rsDept[$i]->iDeptid_PK."' AND vOwnerType='department' AND ";
				$this->obDb->query.=" iState=1 AND iDisplayInNav=1) ORDER BY iSort";
				$rsSubDept1 = $this->obDb->fetchQuery();
				$rsSubDeptCount1=$this->obDb->record_count;
				if($rsSubDeptCount1>0 && TREE_MENU)
				{
					$this->obTpl->set_var("sub_department_blk1","");
					for($j=0;$j<$rsSubDeptCount1;$j++)
					{
						$this->obTpl->set_var("sub_department_main_blk2","");
						$this->obTpl->set_var("TPL_VAR_DEPTURL1",$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$rsSubDept1[$j]->vSeoTitle));
						if($selectedDept["vSeoTitle"]==$rsSubDept1[$j]->vSeoTitle){
							$this->obTpl->set_var("TPL_VAR_DEPTCLASS1",$this->selected);
							$this->obTpl->set_var("TPL_VAR_DEPTCLASS","");
						}else{
							$this->obTpl->set_var("TPL_VAR_DEPTCLASS1","");
						}
						$this->obTpl->set_var("TPL_VAR_ID1",$rsSubDept1[$j]->iDeptid_PK);
						if(!empty($rsSubDept1[$j]->vImage1)){
							$img1=$this->libFunc->m_checkFile($rsSubDept1[$j]->vImage1,"department", $this->libFunc->m_displayContent($rsSubDept1[$j]->vTitle));
							if($img1){
								$this->obTpl->set_var("TPL_VAR_DNAME1",$img1);
							}else{
								$this->obTpl->set_var("TPL_VAR_DNAME1", $this->libFunc->m_displayContent($rsSubDept1[$j]->vTitle));
							}
						}else{
							$this->obTpl->set_var("TPL_VAR_DNAME1", $this->libFunc->m_displayContent($rsSubDept1[$j]->vTitle));
						}

						#QUERY SUB DEPARTMENT2-BLOCK-----------------------------------------------------------
						$this->obDb->query="SELECT vTitle,vSeoTitle,iDeptid_PK,vImage1 FROM ".DEPARTMENTS." D,";
						$this->obDb->query.=" ".FUSIONS." F WHERE (iDeptid_PK=iSubId_FK AND vtype='department' AND ";
						$this->obDb->query.=" iOwner_FK='".$rsSubDept1[$j]->iDeptid_PK."' AND vOwnerType='department' ";
						$this->obDb->query.=" AND iState=1 AND iDisplayInNav=1) ORDER BY iSort";
						$rsSubDept2 = $this->obDb->fetchQuery();
						$rsSubDeptCount2=$this->obDb->record_count;
						if($rsSubDeptCount2>0)
						{
							$this->obTpl->set_var("sub_department_blk2","");
							for($k=0;$k<$rsSubDeptCount2;$k++)
							{
								$this->obTpl->set_var("TPL_VAR_DEPTURL2",$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$rsSubDept2[$k]->vSeoTitle));
								if($selectedDept["vSeoTitle"]==$rsSubDept2[$k]->vSeoTitle){
									$this->obTpl->set_var("TPL_VAR_DEPTCLASS2",$this->selected);
									$this->obTpl->set_var("TPL_VAR_DEPTCLASS","");
									$this->obTpl->set_var("TPL_VAR_DEPTCLASS1","");
								}else{
									$this->obTpl->set_var("TPL_VAR_DEPTCLASS2","");
								}
								$this->obTpl->set_var("TPL_VAR_ID2",$rsSubDept2[$k]->iDeptid_PK);
								if(!empty($rsSubDept2[$k]->vImage1)){
									$img2=$this->libFunc->m_checkFile($rsSubDept2[$k]->vImage1,"department", $this->libFunc->m_displayContent($rsSubDept2[$k]->vTitle));
									if($img2){
										$this->obTpl->set_var("TPL_VAR_DNAME2",$img2);
									}else{
										$this->obTpl->set_var("TPL_VAR_DNAME2", $this->libFunc->m_displayContent($rsSubDept2[$k]->vTitle));
									}
								}else{
									$this->obTpl->set_var("TPL_VAR_DNAME2", $this->libFunc->m_displayContent($rsSubDept2[$k]->vTitle));
								}
								$this->obTpl->parse("sub_department_blk2","TPL_SUB_DEPARTMENT_BLK2",true);
							}
							$this->obTpl->parse("sub_department_main_blk2","TPL_SUB_DEPARTMENT_MAIN_BLK2",true);
						}
						$this->obTpl->parse("sub_department_blk1","TPL_SUB_DEPARTMENT_BLK1",true);
					}
					$this->obTpl->parse("sub_department_main_blk1","TPL_SUB_DEPARTMENT_MAIN_BLK1",true);
				}
					#---------------------------------------------------------------------------------------------
				$this->obTpl->parse("department_blk","TPL_DEPARTMENT_BLK",true);
			}
		}
		$this->obTpl->set_var("TPL_VAR_TREECLASS","navDept");
		#PARSING A BLOCK TO CALL JAVSCRIPT
		if(TREE_MENU){
			//$this->obTpl->set_var("TPL_VAR_TREECLASS","leftmenu");
			$this->obTpl->set_var("TPL_VAR_TREECLASS","navDept");
			$this->obTpl->parse("tree_blk","TPL_TREEMENU_BLK");
		}
		
		
		
		#QUERY MENU TABLE
		$this->obDb->query= "SELECT iHeaderid_PK,vHeader,vImage  FROM ".MENUHEADERS." WHERE iState='1' ORDER BY iSort";
		$rsMenuHead = $this->obDb->fetchQuery();
		$rsMenuHeadCount=$this->obDb->record_count;
		
		if($rsMenuHeadCount>0)
		{
			for($i=0;$i<$rsMenuHeadCount;$i++)
			{
				$this->obDb->query= "SELECT iMenuItemsId,vItemtitle,vLink,vHrefAttributes,iMethod,vImage,iSubmenuid_FK FROM ".MENUITEMS." WHERE iState='1' AND iHeaderid_FK='".$rsMenuHead[$i]->iHeaderid_PK."' ORDER BY iSort";
				$rsMenu = $this->obDb->fetchQuery();
				$rsMenuCount=$this->obDb->record_count;
				if($rsMenuCount>0)
				{
					$this->obTpl->set_var("menu_blk","");
					for($j=0;$j<$rsMenuCount;$j++)
					{
						$this->obTpl->set_var("TPL_VAR_ID",$rsMenu[$j]->iMenuItemsId);
						if(!empty($rsMenu[$j]->vImage))
						{
							 $img=$this->libFunc->m_checkFile($rsMenu[$j]->vImage,"menu",$this->libFunc->m_displayContent($rsMenu[$j]->vItemtitle));
							if($img)
							{
								$this->obTpl->set_var("TPL_VAR_MENUTITLE",$img);
							}
							else
							{
								$this->obTpl->set_var("TPL_VAR_MENUTITLE",$this->libFunc->m_displayContent($rsMenu[$j]->vItemtitle));
							}
						}
						else
						{
							$this->obTpl->set_var("TPL_VAR_MENUTITLE",$this->libFunc->m_displayContent($rsMenu[$j]->vItemtitle));
						}	

						if ($rsMenu[$j]->iMethod==0){
	                        $this->obTpl->set_var("TPL_VAR_METHOD","_parent");
	                        }else{
	                        $this->obTpl->set_var("TPL_VAR_METHOD","_blank");
	                        }
						
						$this->obTpl->set_var("TPL_VAR_LINK",$this->libFunc->m_displayContent($rsMenu[$j]->vLink));
						$this->obTpl->set_var("TPL_VAR_HREFATTRIBUTES",$this->libFunc->m_displayContent($rsMenu[$j]->vHrefAttributes));
						if($rsMenu[$j]->iSubmenuid_FK > 0)
						{
							$this->obTpl->set_var("TPL_VAR_SUBMENU",$this->m_displaySubMenus());
						}
						else
						{
							$this->obTpl->set_var("TPL_VAR_SUBMENU","");
						}
						$this->obTpl->parse("menu_blk","TPL_MENU_BLK",true);
					}#end Menuitems for loop
				}#end Menuitems if loop
				else
				{
					$this->obTpl->set_var("menu_blk","");
				}

				if(!empty($rsMenuHead[$i]->vImage))
				{
					 $img=$this->libFunc->m_checkFile($rsMenuHead[$i]->vImage,"menu",$this->libFunc->m_displayContent($rsMenuHead[$i]->vHeader));
					if($img)
					{
						$this->obTpl->set_var("TPL_VAR_MENUHEAD",$img);
					}
					else
					{
						$this->obTpl->set_var("TPL_VAR_MENUHEAD",$this->libFunc->m_displayContent($rsMenuHead[$i]->vHeader));
					}
				}
				else
				{
					$this->obTpl->set_var("TPL_VAR_MENUHEAD",$this->libFunc->m_displayContent($rsMenuHead[$i]->vHeader));
				}
				$this->obTpl->parse("menuhead_blk","TPL_MENUHEAD_BLK",true);
			}#end MenuHead for loop
		}#end MenuHead if loop

		$this->obTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obTpl->set_var('TPL_VAR_LEFT', $this->obTpl->parse("return","TPL_LEFT_FILE"));
	}#END 

	function m_displaySubMenus()
	{
		/*$this->Template = $this->templatePath."subMenu.tpl.htm";
		$this->obTpl->set_file("TPL_SUB_MENU_FILE",$this->Template);
		$this->obTpl->set_block("TPL_SUB_MENU_FILE","TPL_MENU_BLK","menu_blk");
		
		$this->obDb->query= "SELECT iHeaderid_PK,vHeader,vImage  FROM ".MENUHEADERS." WHERE iState='1' ORDER BY iSort";
		$rsMenuHead = $this->obDb->fetchQuery();
		$rsMenuHeadCount=$this->obDb->record_count;
		
		if($rsMenuHeadCount>0)
		{
			for($i=0;$i<$rsMenuHeadCount;$i++)
			{
				$this->obDb->query= "SELECT iMenuItemsId,vItemtitle,vLink,vHrefAttributes,iMethod,vImage,iSubmenuid_FK FROM ".MENUITEMS." WHERE iState='1' AND iHeaderid_FK='".$rsMenuHead[$i]->iHeaderid_PK."' ORDER BY iSort";
				$rsMenu = $this->obDb->fetchQuery();
				$rsMenuCount=$this->obDb->record_count;
				if($rsMenuCount>0)
				{
					$this->obTpl->set_var("menu_blk","");
					for($j=0;$j<$rsMenuCount;$j++)
					{
						$this->obTpl->set_var("TPL_VAR_ID",$rsMenu[$j]->iMenuItemsId);
						if(!empty($rsMenu[$j]->vImage))
						{
							 $img=$this->libFunc->m_checkFile($rsMenu[$j]->vImage,"menu",$this->libFunc->m_displayContent($rsMenu[$j]->vItemtitle));
							if($img)
							{
								$this->obTpl->set_var("TPL_VAR_MENUTITLE",$img);
							}
							else
							{
								$this->obTpl->set_var("TPL_VAR_MENUTITLE",$this->libFunc->m_displayContent($rsMenu[$j]->vItemtitle));
							}
						}
						else
						{
							$this->obTpl->set_var("TPL_VAR_MENUTITLE",$this->libFunc->m_displayContent($rsMenu[$j]->vItemtitle));
						}	

						if ($rsMenu[$j]->iMethod==0){
	                        $this->obTpl->set_var("TPL_VAR_METHOD","_parent");
	                        }else{
	                        $this->obTpl->set_var("TPL_VAR_METHOD","_blank");
	                        }
						
						$this->obTpl->set_var("TPL_VAR_LINK",$this->libFunc->m_displayContent($rsMenu[$j]->vLink));
						$this->obTpl->set_var("TPL_VAR_HREFATTRIBUTES",$this->libFunc->m_displayContent($rsMenu[$j]->vHrefAttributes));
						if($rsMenu[$j]->iSubmenuid_FK > 0)
						{
							$this->obTpl->set_var("TPL_VAR_SUBMENU",$this->m_displaySubMenus());
						}
						$this->obTpl->parse("menu_blk","TPL_MENU_BLK",true);
					}#end Menuitems for loop
				}#end Menuitems if loop
				else
				{
					$this->obTpl->set_var("menu_blk","");
				}

				if(!empty($rsMenuHead[$i]->vImage))
				{
					 $img=$this->libFunc->m_checkFile($rsMenuHead[$i]->vImage,"menu",$this->libFunc->m_displayContent($rsMenuHead[$i]->vHeader));
					if($img)
					{
						$this->obTpl->set_var("TPL_VAR_MENUHEAD",$img);
					}
					else
					{
						$this->obTpl->set_var("TPL_VAR_MENUHEAD",$this->libFunc->m_displayContent($rsMenuHead[$i]->vHeader));
					}
				}
				else
				{
					$this->obTpl->set_var("TPL_VAR_MENUHEAD",$this->libFunc->m_displayContent($rsMenuHead[$i]->vHeader));
				}
				$this->obTpl->parse("menuhead_blk","TPL_MENUHEAD_BLK",true);
			}#end MenuHead for loop
		}*/
		return "";
	}
	
	#FUNCTION TO GET SEOTIITE UNDER HOEMPAGE TO SHOW IT SELECTED
	function m_getMainDept($seoTitle)
	{
		$this->leftDepTitle = $seoTitle;

		$this->obDb->query = "SELECT iOwner_FK FROM ".DEPARTMENTS." D ,".FUSIONS." F WHERE iDeptid_PK=iSubId_FK and vSeoTitle='".$seoTitle."' AND vOwnertype='department' AND  vtype='department'" ;
		$row = $this->obDb->fetchQuery();
		
		if(!empty($row[0]->iOwner_FK))
		{
			$this->obDb->query = "SELECT vSeoTitle FROM ".DEPARTMENTS." D ,".FUSIONS." F WHERE iDeptid_PK=iSubId_FK AND iSubId_FK='".$row[0]->iOwner_FK."' AND vOwnertype='department' AND vtype='department'";
			$rsTitle = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				 $this->m_getMainDept($rsTitle[0]->vSeoTitle);
			}
		}
		else
		{
			$this->leftDepTitle = $seoTitle;
		}

		return $this->leftDepTitle;
	}


	#FUNCTION TO DISPLAY INLINE EDITOR
	function m_inlineEditor()
	{
		if(!isset($this->request['action']))
		{
			$this->request['action']='';
		}
		if(!isset($this->request['mode']))
		{
			$this->request['mode']='0';
		}

		$this->obTpl->set_var("TPL_VAR_ADMINURL",SITE_URL."adminindex.php");
		$this->obTpl->set_var("TPL_VAR_ADMINLOGOUT",SITE_URL."user/adminindex.php?action=logout");

		if($this->request['action']=='ecom.details')
		{
			$this->obDb->query= "SELECT iDeptid_PK,iOwner_FK FROM ".DEPARTMENTS.",".FUSIONS." WHERE iSubId_FK=iDeptid_PK AND vSeoTitle='".$this->request['mode']."' AND vType='department'";
			$rsDept = $this->obDb->fetchQuery();
			$rsDeptCount=$this->obDb->record_count;
			$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."ecom/adminindex.php?action=ec_show.deptFrm&amp;type=department&amp;id=".$rsDept[0]->iDeptid_PK."&amp;owner=".$rsDept[0]->iOwner_FK);
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_PRODUCTLINK",SITE_URL."ecom/adminindex.php?action=ec_show.associate&amp;owner=".$rsDept[0]->iDeptid_PK."&amp;otype=department&amp;type=product");
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_ARTICLELINK",SITE_URL."ecom/adminindex.php?action=ec_show.associate&amp;owner=".$rsDept[0]->iDeptid_PK."&amp;otype=department&amp;type=content");
			$this->obTpl->parse("inlineEditor_blk","TPL_VAR_INLINE_EDITOR_BLK");
		}
		elseif($this->request['action']=='ecom.pdetails')
		{
			$this->obDb->query= "SELECT iProdid_PK,iOwner_FK,vOwnerType FROM ".PRODUCTS.",".FUSIONS." WHERE iSubId_FK=iProdid_PK AND vSeoTitle='".$this->request['mode']."'  AND vType='product'";
			$rsDept = $this->obDb->fetchQuery();
			$rsDeptCount=$this->obDb->record_count;
			$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."ecom/adminindex.php?action=ec_show.dspProFrm&amp;type=".$rsDept[0]->vOwnerType."&amp;id=".$rsDept[0]->iProdid_PK."&amp;owner=".$rsDept[0]->iOwner_FK);
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_PRODUCTLINK",SITE_URL."ecom/adminindex.php?action=ec_show.associate&amp;owner=".$rsDept[0]->iProdid_PK."&amp;otype=product&amp;type=product");
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_ARTICLELINK",SITE_URL."ecom/adminindex.php?action=ec_show.associate&amp;owner=".$rsDept[0]->iProdid_PK."&amp;otype=product&amp;type=content");

			$this->obTpl->parse("inlineEditor_blk","TPL_VAR_INLINE_EDITOR_BLK");
		}
		elseif($this->request['action']=='ecom.cdetails')
		{
			$this->obDb->query= "SELECT iContentid_PK,iOwner_FK,vOwnerType FROM ".CONTENTS.",".FUSIONS." WHERE iSubId_FK=iContentid_PK AND vSeoTitle='".$this->request['mode']."' AND vType='content'";
			$rsDept = $this->obDb->fetchQuery();
			$rsDeptCount=$this->obDb->record_count;
			$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."ecom/adminindex.php?action=ec_show.contentFrm&amp;type=".$rsDept[0]->vOwnerType."&amp;id=".$rsDept[0]->iContentid_PK."&amp;owner=".$rsDept[0]->iOwner_FK);
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_PRODUCTLINK","#");
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_ARTICLELINK",SITE_URL."ecom/adminindex.php?action=ec_show.associate&amp;owner=".$rsDept[0]->iContentid_PK."&amp;otype=content&amp;type=content");

			$this->obTpl->parse("inlineEditor_blk","TPL_VAR_INLINE_EDITOR_BLK");
		}
		elseif($this->request['action']=='')
		{
			$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."admin/adminindex.php?action=settings.textarea_edit&amp;which=index_body");
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_PRODUCTLINK",SITE_URL."ecom/adminindex.php?action=ec_show.associate&amp;owner=0&amp;otype=department&amp;type=product");
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_ARTICLELINK",SITE_URL."ecom/adminindex.php?action=ec_show.associate&amp;owner=0&amp;otype=product&amp;type=content");

			$this->obTpl->parse("inlineEditor_blk","TPL_VAR_INLINE_EDITOR_BLK");
		}
		elseif($this->request['action']=='cms')
		{
			if($this->request['mode']=='accessibility')
			{
				$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."admin/adminindex.php?action=settings.textarea_edit&amp;which=accessibility");
			}
			elseif($this->request['mode']=='conditions')
			{
				$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."admin/adminindex.php?action=settings.textarea_edit&amp;which=conditions");
			}
			elseif($this->request['mode']=='privacy')
			{
				$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."admin/adminindex.php?action=settings.textarea_edit&amp;which=privacy");
			}
			elseif($this->request['mode']=='member_points')
			{
				$this->obTpl->set_var("TPL_VAR_EDITLINK",SITE_URL."admin/adminindex.php?action=settings.textarea_edit&amp;which=member_points");
			}
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_PRODUCTLINK","#");
			$this->obTpl->set_var("TPL_VAR_ASSOCIATE_ARTICLELINK","#");

			$this->obTpl->parse("inlineEditor_blk","TPL_VAR_INLINE_EDITOR_BLK");
		}

	}#INLINE EDITOR FUNCTION

	#FUNCTION TO DISPLAY METATAGS
	function showPageMetaTags()
	{

		$libFunc					=new c_libFunctions();
		$stMetaTitle			="";
		$stMetaKeyword		="";
		$stMetaDescription	="";
		$layoutTemplate		=MAIN_LAYOUT;
		if(!isset($this->request['action']))
		{
			$this->request['action']="";
			$layoutTemplate		=HOMEPAGE_LAYOUT;
		}

		$this->obDb->query = "SELECT vSmalltext FROM ".SITESETTINGS." where vDatatype='cencoding'";
		$row_setting=$this->obDb->fetchQuery();
		$computer_encoding=unserialize(COMPUTER_ENCODING);

		$this->obTpl->set_var('TPL_VAR_CENCODING',$computer_encoding[$row_setting[0]->vSmalltext]);

		if($this->request['action']=='ecom.details')
		{
			$this->obDb->query= "SELECT vTitle,vMetaTitle,tMetaDescription,tKeywords,vLayout  FROM ".DEPARTMENTS.",".FUSIONS." WHERE iSubId_FK=iDeptid_PK AND iState=1 AND vSeoTitle='".$this->request['mode']."'";
			$rs= $this->obDb->fetchQuery();
			$rscount=$this->obDb->record_count;
			if($rscount<1)
			{
				//$errrorUrl=SITE_URL."index.php?action=error&mode=department";
				//header("Location:".$this->libFunc->m_safeUrl($errrorUrl));
				//exit;
			}

			if(empty($rs[0]->vMetaTitle))
			{
				$stMetaTitle=$rs[0]->vTitle;
			}
			else
			{
				$stMetaTitle=$rs[0]->vMetaTitle;
			}
			$stMetaKeyword		=$rs[0]->tKeywords;
			$stMetaDescription	=$rs[0]->tMetaDescription;
			$layoutTemplate		=$rs[0]->vLayout;
		}
		elseif($this->request['action']=='ecom.pdetails')
		{
			$this->obDb->query= "SELECT vTitle,vMetaTitle,tMetaDescription,tKeywords,vLayout FROM ".PRODUCTS.",".FUSIONS." WHERE iSubId_FK=iProdid_PK AND iState=1 AND vSeoTitle='".$this->request['mode']."'";
			$rs= $this->obDb->fetchQuery();
			$rscount=$this->obDb->record_count;
			if($rscount<1)
			{
				//$errrorUrl=SITE_URL."index.php?action=error&mode=product";
				//header("Location:".$this->libFunc->m_safeUrl($errrorUrl));
				//exit;
			}
			if(empty($rs[0]->vMetaTitle))
			{
				$stMetaTitle=$rs[0]->vTitle;
			}
			else
			{
				$stMetaTitle=$rs[0]->vMetaTitle;
			}
			$stMetaKeyword=$rs[0]->tKeywords;
			$stMetaDescription=$rs[0]->tMetaDescription;
			$layoutTemplate=$rs[0]->vLayout;
		}
		elseif($this->request['action']=='ecom.cdetails')
		{
			$this->obDb->query= "SELECT vTitle,vMetaTitle,tMetaDescription,tKeywords,vLayout FROM ".CONTENTS.",".FUSIONS." WHERE iSubId_FK=iContentid_PK AND iState=1 AND  vSeoTitle='".$this->request['mode']."'";
			$rs= $this->obDb->fetchQuery();
			$rscount=$this->obDb->record_count;
			if($rscount<1)
			{
				//$errrorUrl=SITE_URL."index.php?action=error&mode=content";
				//header("Location:".$this->libFunc->m_safeUrl($errrorUrl));
				//exit;
			}
			if(empty($rs[0]->vMetaTitle))
			{
				$stMetaTitle=$rs[0]->vTitle;
			}
			else
			{
				$stMetaTitle=$rs[0]->vMetaTitle;
			}
			$stMetaKeyword=$rs[0]->tKeywords;
			$stMetaDescription=$rs[0]->tMetaDescription;
			$layoutTemplate=$rs[0]->vLayout;
		}

		if($stMetaTitle=="")
		{
			$stMetaTitle=META_TITLE;
		}

		if($stMetaKeyword=="")
		{
			$stMetaKeyword=META_KEYWORD;
		}

		if($stMetaDescription=="")
		{
			$stMetaDescription=META_DESCRIPTION;
		}

		$layoutFilePath=THEMEPATH."default/templates/main/layout/".$layoutTemplate;

		if(!file_exists($layoutFilePath) || !is_file($layoutFilePath)){
			$layoutFilePath=THEMEPATH."default/templates/main/layout/layout.htm";
		}
		$this->obTpl->set_file('hMainTemplate',$layoutFilePath);
		$this->obTpl->set_block("hMainTemplate","TPL_VAR_INLINE_EDITOR_BLK","inlineEditor_blk");
				
		$this->obTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->obTpl->set_var("TPL_VAR_SITENAME",SITE_NAME);
		$this->obTpl->set_var("TPL_VAR_THEME_PATH",THEMEURLPATH);
	
		$this->obTpl->set_var("GRAPHICSMAINPATH",SITE_URL."/graphics");
		$this->obTpl->set_var("inlineEditor_blk","");

		$this->obTpl->set_var('TPL_VAR_METATITLE',$this->libFunc->m_displayContent($stMetaTitle));
		$this->obTpl->set_var('TPL_VAR_METAKEYWORDS',htmlspecialchars($this->libFunc->m_displayContent($stMetaKeyword)));
		$this->obTpl->set_var('TPL_VAR_METADESCRIPTION',htmlspecialchars($this->libFunc->m_displayContent($stMetaDescription)));
	}#END META TAGS

}#LEFT MENU CLASS ENDS
?>