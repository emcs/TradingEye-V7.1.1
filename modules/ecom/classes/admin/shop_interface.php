<?php
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_shopInterface
{
	#CONSTRUCTOR
	function c_shopInterface()
	{
		$this->errMsg="";
		$this->err=0;
		$this->errImg=0;
		$this->libFunc=new c_libFunctions();
		$this->pageTitle="";
		$this->real_path = $this->libFunc->real_path();	
		$this->departmentTemplatePath=THEMEPATH."ecom/templates/main/department/";
		$this->productTemplatePath=THEMEPATH."ecom/templates/main/product/";
		$this->contentTemplatePath=THEMEPATH."ecom/templates/main/content/";
		$this->layoutTemplatePath=THEMEPATH."default/templates/main/layout/";
	}

	#FUNCTION THAT RETURN BREDCRUMS
	function m_topNavigation($ownerid,$type)
	{
		global $Navigation,$topNavigation;
		$topNavigation  = "<a class='breadcrumbs' href=\"" . SITE_URL . "ecom/adminindex.php?action=ec_show.home\">".SHOPBUILDER_HOME."</a> ";
		$this->m_getMainNavigation($ownerid,$type);
		$topNavigation.=$Navigation;
		return $topNavigation;
	}

	#RECURSIVE FUNCTION TO GENERATE BREDCRUMBS
	function m_getMainNavigation($ownerid,$type)
	{
		global $Navigation;

		if($ownerid!=0)
		{
			if($type=="product")
			{
				$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".PRODUCTS." D ,			".FUSIONS." F WHERE iProdid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			}
			elseif($type=="content")
			{
				$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".CONTENTS." D ,			".FUSIONS." F WHERE iContentid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			}
			else
			{
				$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".DEPARTMENTS." D ,			".FUSIONS." F WHERE iDeptid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			}

			$row = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				if($type=="product")
				{
					$Navigation=" &raquo; "."<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.home&amp;owner=$ownerid&amp;type=product>".$this->libFunc->m_displayContent($row[0]->vTitle)."</a>".$Navigation;
				}
				elseif($type=="content")
				{
					$Navigation=" &raquo; "."<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.home&amp;owner=$ownerid&amp;type=content>".$this->libFunc->m_displayContent($row[0]->vTitle)."</a>".$Navigation;
				}
				else
				{
					$Navigation=" &raquo; "."<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.home&amp;owner=$ownerid&amp;type=department>".$this->libFunc->m_displayContent($row[0]->vTitle)."</a>".$Navigation;
				}
				//echo $row[0]->iOwner_FK."<br>";
				$this->m_getMainNavigation($row[0]->iOwner_FK,"department");
			}
		}
		else
		{
			return $Navigation;
		}
	}
	
	#FUNCTION TO DISPLAY SHOPBUILDER HOMEPAGE
	function m_showDepartments()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("shopHome", $this->departmentTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_block("shopHome","TPL_MAINDEPARTMENT_BLK","dspmaindept_blk");
		$this->ObTpl->set_block("TPL_MAINDEPARTMENT_BLK","TPL_DSPDEPARTMENT_BLK","dspdept_blk");
		$this->ObTpl->set_block("shopHome","TPL_SHOPBTN_BLK1","shopbtn_blk1");
		$this->ObTpl->set_block("shopHome","TPL_SHOPLINK_BLK1","shoplink_blk1");

		$this->ObTpl->set_block("shopHome","DSPMESSAGE_BLK","dspmess_blk");
		$this->ObTpl->set_block("shopHome","TPL_SHOPBTN_BLK2","shopbtn_blk2");
		$this->ObTpl->set_block("shopHome","TPL_SHOPLINK_BLK2","shoplink_blk2");
		$this->ObTpl->set_block("shopHome","TPL_MAINPRODUCT_BLK","mainproduct_blk");
		$this->ObTpl->set_block("TPL_MAINPRODUCT_BLK","TPL_PRODUCT_BLK","dspproduct_blk");
		

		
		$this->ObTpl->set_block("shopHome","TPL_SHOPBTN_BLK3","shopbtn_blk3");
		$this->ObTpl->set_block("shopHome","TPL_SHOPLINK_BLK3","shoplink_blk3");
		$this->ObTpl->set_block("shopHome","TPL_MAINARTICLES_BLK","mainarticles_blk");	
		$this->ObTpl->set_block("TPL_MAINARTICLES_BLK","TPL_CONTENT_BLK","dspcontent_blk");
		

		$this->ObTpl->set_var("shopbtn_blk1","");
		$this->ObTpl->set_var("shopbtn_blk2","");
		$this->ObTpl->set_var("shopbtn_blk3","");

		$this->ObTpl->set_var("shoplink_blk1","");
		$this->ObTpl->set_var("shoplink_blk2","");
		$this->ObTpl->set_var("shoplink_blk3","");
		
		$this->ObTpl->set_var("dspmaindept_blk","");
		$this->ObTpl->set_var("mainproduct_blk","");
		$this->ObTpl->set_var("mainarticles_blk","");
		
		
		
		//defining Language Variables
		$this->ObTpl->set_var("LANG_VAR_ADDDEPARTMENT",LANG_ADDDEPARTMENT);
		$this->ObTpl->set_var("LANG_VAR_ADDPRODUCT",LANG_ADDPRODUCT);
		$this->ObTpl->set_var("LANG_VAR_ADDARTICLE",LANG_ADDDARTICLE);
		$this->ObTpl->set_var("LANG_VAR_PREVIEW",LANG_PREVIEW);
		$this->ObTpl->set_var("LANG_VAR_SORT",LANG_SORT);
		$this->ObTpl->set_var("LANG_VAR_TITLE",LANG_TITLE);
		$this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
		$this->ObTpl->set_var("LANG_VAR_DUPLICATE",LANG_DUPLICATE);
		$this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
		$this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
		$this->ObTpl->set_var("LANG_VAR_ORDERLIST",LANG_ORDERLIST);
		$this->ObTpl->set_var("LANG_VAR_APPLYCHANGES",LANG_APPLYCHANGES);
		$this->ObTpl->set_var("LANG_VAR_ASSOCIATEPRODUCTS",LANG_ASSOCIATEPRODUCTS);
		$this->ObTpl->set_var("LANG_VAR_ASSOCIATEARTICLES",LANG_ASSOCIATEARTICLES);
		$this->ObTpl->set_var("LANG_VAR_SAVE",LANG_SAVE);
		
		
		#INTIALIZING OWNER ID
		if(empty($this->request['owner']))
		{
			$this->request['owner']=0;
		}

		#INTIALIZING TYPE
		if(!isset($this->request['type']))
		{
			$this->request['type']="department";
		}
			
		#BREDCRUMBS
		$topNavigation=$this->m_topNavigation($this->request['owner'],$this->request['type']);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBS", stripslashes($topNavigation));
		

		#TO DISPLAY THE HEAD NAME
		if(isset($this->request['type']) && $this->request['type']=="content" && $this->request['owner']!=0)
		{
			$this->obDb->query = "SELECT vTitle FROM ".CONTENTS." WHERE iContentId_PK='".$this->request['owner']."'";
		}
		elseif(isset($this->request['type']) && $this->request['type']=="product" && $this->request['owner']!=0)
		{
			$this->obDb->query = "SELECT vTitle FROM ".PRODUCTS." WHERE iProdId_PK='".$this->request['owner']."'";
		}
		else
		{
			 $this->obDb->query = "SELECT vTitle FROM ".DEPARTMENTS." WHERE iDeptid_PK='".$this->request['owner']."'";
		}
		$row = $this->obDb->fetchQuery();
			$vTitle = $row[0]->vTitle;

		if($this->request['owner']!=0)
		{
			$this->pageTitle=$this->libFunc->m_displayContent($vTitle);
		}
		else
		{
			$this->pageTitle=SHOPBUILDER_HOME;
		}
		$this->ObTpl->set_var("TPL_VAR_NAME",$this->pageTitle);	

		$this->ObTpl->set_var("TPL_DEPARTMENT_ID",$this->request['owner']);
		$this->ObTpl->set_var("TPL_OWNERTYPE",$this->request['type']);
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");

		 #QUERY TO GET DEPARTMENTS UNDER SELECTED DEPARTMENT
		$query1 = "SELECT vTitle,vSeoTitle,vTemplate,F.iSort,F.iState as state,iDeptid_PK  FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE iDeptid_PK=iSubId_FK AND vtype='department' AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['type']."' ORDER BY iSort";
		$row1=$this->obDb->execQry($query1);
		$this->ObTpl->set_var("TPL_DEPARTMENT_NUMBER",mysql_num_rows($row1));
		$departmentnumber = mysql_num_rows($row1);
		if(mysql_num_rows($row1)>0)
		{
			while($res1=mysql_fetch_object($row1))
			{
				if($res1->state==1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","checked=\"checked\"");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED","");
				}	
				#PREVIEW URL
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$res1->vSeoTitle);
				$this->ObTpl->set_var("TPL_VAR_PREVIEWURL",$retUrl);
				$this->ObTpl->set_var("TPL_VAR_TEMPLATE",$res1->vTemplate);

				$this->ObTpl->set_var("TPL_SUBDEPARTMENT_ID",$res1->iDeptid_PK);
				$this->ObTpl->set_var("TPL_SORT_NUM",$res1->iSort);
				$this->ObTpl->set_var("TPL_VAR_DEPARTMENTTITLE",$this->libFunc->m_displayContent($res1->vTitle));
				$this->ObTpl->set_var("dspmess_blk","");
				$this->ObTpl->set_var("TPL_VAR_MESSAGE","");
				$this->ObTpl->parse("dspdept_blk","TPL_DSPDEPARTMENT_BLK",true);	
			}
			$this->ObTpl->parse("shopbtn_blk1","TPL_SHOPBTN_BLK1");
			$this->ObTpl->parse("shoplink_blk1","TPL_SHOPLINK_BLK1");
		
		}
		else
		{
			$this->ObTpl->set_var("dspdept_blk","");
			$this->ObTpl->set_var("dspmess_blk","");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",NODEPARTMENT." <b>".$this->pageTitle."</b>");
		}

		$this->ObTpl->set_var("TPL_PRODUCT_ID",$this->request['owner']);

		 #QUERY TO GET PRODUCTS UNDER SELECTED 
		$query1 = "SELECT vTitle,vSeoTitle,vTemplate,F.iSort,F.iState as state,iProdId_PK  FROM ".PRODUCTS." D, ".FUSIONS." F WHERE iProdId_PK=iSubId_FK AND vtype='product' AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['type']."' ORDER BY iSort";
		$row1=$this->obDb->execQry($query1);
		$this->ObTpl->set_var("TPL_PRODUCT_NUMBER",mysql_num_rows($row1));
		if(mysql_num_rows($row1)>0)
		{
			while($res1=mysql_fetch_object($row1))
			{
				if($res1->state==1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED1","checked=\"checked\"");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED1","");
				}	
				#PREVIEW URL
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$res1->vSeoTitle);
				$this->ObTpl->set_var("TPL_VAR_PREVIEWURL",$retUrl);
				$this->ObTpl->set_var("TPL_VAR_TEMPLATE",$res1->vTemplate);

				$this->ObTpl->set_var("TPL_SUBPRODUCT_ID",$res1->iProdId_PK);
				$this->ObTpl->set_var("TPL_SORT_NUM",$res1->iSort);
				$this->ObTpl->set_var("TPL_VAR_PRODUCTTITLE",$this->libFunc->m_displayContent($res1->vTitle));
				$this->ObTpl->set_var("dspmess_blk","");
				$this->ObTpl->set_var("TPL_VAR_MESSAGE1","");
				$this->ObTpl->parse("dspproduct_blk","TPL_PRODUCT_BLK",true);	
			}
			$this->ObTpl->parse("shopbtn_blk2","TPL_SHOPBTN_BLK2");
			$this->ObTpl->parse("shoplink_blk2","TPL_SHOPLINK_BLK2");
		$this->ObTpl->parse("mainproduct_blk","TPL_MAINPRODUCT_BLK");	
		}
		else
		{
			$this->ObTpl->set_var("dspproduct_blk","");
			$this->ObTpl->set_var("dspmess_blk","");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE1",MSG_NOPRODUCT." <b>".$this->pageTitle."</b>");
		}

		#IF OWNER TYPE IS NOT DEPARTMENT
		if($this->request['type']=='content')
		{
			$this->ObTpl->set_var("dspmaindept_blk","");
		}
		elseif($this->request['type']=='product')
		{
			$this->ObTpl->set_var("dspmaindept_blk","");	
		}
		else
		{
			if($departmentnumber>0){
			$this->ObTpl->parse("dspmaindept_blk","TPL_MAINDEPARTMENT_BLK");
			}	
		}
		

		$this->ObTpl->set_var("TPL_CONTENT_ID",$this->request['owner']);
		 #QUERY TO GET CONTENTS UNDER SELECTED 
		$query1 = "SELECT vTitle,vSeoTitle,vTemplate,F.iSort,F.iState as state, iContentId_PK  FROM ".CONTENTS." D, ".FUSIONS." F WHERE iContentId_PK=iSubId_FK AND vtype='content' AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['type']."'  ORDER BY iSort";
		$row1=$this->obDb->execQry($query1);
		$this->ObTpl->set_var("TPL_ARTICLES_NUMBER",mysql_num_rows($row1));
		if(mysql_num_rows($row1)>0)
		{
			while($res1=mysql_fetch_object($row1))
			{
				if($res1->state==1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED2","checked=\"checked\"");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED2","");
				}	
				#PREVIEW URL
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$res1->vSeoTitle);
				$this->ObTpl->set_var("TPL_VAR_PREVIEWURL",$retUrl);
				$this->ObTpl->set_var("TPL_VAR_TEMPLATE",$res1->vTemplate);

				$this->ObTpl->set_var("TPL_SUBCONTENT_ID",$res1-> iContentId_PK);
				$this->ObTpl->set_var("TPL_SORT_NUM",$res1->iSort);
				$this->ObTpl->set_var("TPL_VAR_CONTENTTITLE",$this->libFunc->m_displayContent($res1->vTitle));
				$this->ObTpl->set_var("dspmess_blk","");
				$this->ObTpl->set_var("TPL_VAR_MESSAGE2","");
				$this->ObTpl->parse("dspcontent_blk","TPL_CONTENT_BLK",true);	
			}
			$this->ObTpl->parse("shopbtn_blk3","TPL_SHOPBTN_BLK3");
			$this->ObTpl->parse("shoplink_blk3","TPL_SHOPLINK_BLK3");
		$this->ObTpl->parse("mainarticles_blk","TPL_MAINARTICLES_BLK");
		
		}
		else
		{
			$this->ObTpl->set_var("dspcontent_blk","");
			$this->ObTpl->set_var("dspmess_blk","");
		}
		
		$this->ObTpl->set_var("TPL_VAR_DEPTLINK",SITE_URL."ecom/adminindex.php?action=ec_show.deptFrm&amp;type=department&amp;owner=".$this->request['owner']);						
		$this->ObTpl->set_var("TPL_VAR_PRODLINK",SITE_URL."ecom/adminindex.php?action=ec_show.dspProFrm&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);	
		$this->ObTpl->set_var("TPL_VAR_CONTENTLINK",SITE_URL."ecom/adminindex.php?action=ec_show.contentFrm&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);					
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMB",SHOPBUILDER_HOME);
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	

		return($this->ObTpl->parse("return","shopHome"));
	}
	
	
	#FUNCTION TO DISPLAY DEPARTMENT FORM
	function m_dspDepartmentForm()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("department", $this->departmentTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_block("department","DSPIMAGEBOX_BLK", "imagebox_blk");
		$this->ObTpl->set_block("department","DSPIMAGELINK_BLK", "imagelink_blk");			
		$this->ObTpl->set_block("department","DSPMSG_BLK", "msg_blk");	
		$this->ObTpl->set_block("department","TPL_TEMPLATE_BLK","template_blk");
		$this->ObTpl->set_block("department","TPL_LAYOUT_BLK","layout_blk");

		$this->ObTpl->set_var("imagebox_blk","");
		$this->ObTpl->set_var("imagelink_blk","");
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("layout_blk","");
		$this->ObTpl->set_var("template_blk","");
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_SELECT1","selected");	
		$this->ObTpl->set_var("TPL_VAR_SELECT2","");	
		
		//defining language variables.
		$this->ObTpl->set_var("LANG_VAR_DEPARTMENTBUILD",LANG_ADEPARTMENTBUILDER);	
		$this->ObTpl->set_var("LANG_VAR_DEPARTMENTTITLE",LANG_DEPARTMENTTITLE);	
		$this->ObTpl->set_var("LANG_VAR_SELECTLAYOUT",LANG_SELECTLAYOUT);	
		$this->ObTpl->set_var("LANG_VAR_SELECTTEMPLATE",LANG_SELECTTEMPLATE);
		$this->ObTpl->set_var("LANG_VAR_IMAGES",LANG_IMAGES);
		$this->ObTpl->set_var("LANG_VAR_IMAGEA",LANG_IMAGEA);
		$this->ObTpl->set_var("LANG_VAR_IMAGEB",LANG_IMAGEB);
		$this->ObTpl->set_var("LANG_VAR_RESIZETXT",LANG_RESIZETXT);
		$this->ObTpl->set_var("LANG_VAR_ONLYTXT",LANG_ONLYTEXT);
		$this->ObTpl->set_var("LANG_VAR_AUTORESIZETXT",LANG_AUTORESIZETXT);
		$this->ObTpl->set_var("LANG_VAR_SEOTXT",LANG_SEOTXT);
		$this->ObTpl->set_var("LANG_VAR_FILENAME",LANG_FILENAME);
		$this->ObTpl->set_var("LANG_VAR_METATITLE",LANG_METATITLE);
		$this->ObTpl->set_var("LANG_VAR_METADESCRIPTION",LANG_METADESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_METAKEYWORDS",LANG_METAKEYWORDS);
		$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKMETATITLE",LANG_LEAVEBLANKFORDEPT);
		$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKGLOBAL",LANG_LEAVEBLANKGLOBAL);
		$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKGLOBALKEY",LANG_LEAVEBLANKGLOBALKEY);
		$this->ObTpl->set_var("LANG_VAR_STATUSANDDESCRIPTION",LANG_STATUANDDESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_DISPLAYMAINNAV",LANG_DISPLAYMAINNAV);
		$this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
		$this->ObTpl->set_var("LANG_VAR_SHORTDESC",LANG_SHORTDESC);
		$this->ObTpl->set_var("LANG_VAR_LONGDESC",LANG_LONGDESC);
        
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		
		// [DRK]
		$this->ObTpl->set_var("imgWidth1",  UPLOAD_DEPTSMIMAGEWIDTH);
		$this->ObTpl->set_var("imgHeight1", UPLOAD_DEPTSMIMAGEHEIGHT);
		$this->ObTpl->set_var("imgWidth2",  UPLOAD_DEPTMDIMAGEWIDTH);
		$this->ObTpl->set_var("imgHeight2", UPLOAD_DEPTMDIMAGEHEIGHT);
		// Tell the user which image types are suppoerted:
		$gd = gd_info();
		$tmp = array ();
		$gd['GIF Create Support'] == true ? $tmp[] = "gif" : false;
		$gd['JPEG Support'] == true ? $tmp[] = "jpg" : false;
		$gd['PNG Support'] == true ? $tmp[] = "png" : false;
		$this->ObTpl->set_var("resampleList", implode(", ", $tmp));
		// [/DRK]
		
		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/

		$row_department[0]->vTitle = "";
		$row_department[0]->vTemplate ="";
		$row_department[0]->vLayout = "";
		$row_department[0]->vSeoTitle = "";
		$row_department[0]->vMetaTitle = "";
		$row_department[0]->tMetaDescription = "";
		$row_department[0]->tKeywords = "";
		$row_department[0]->tContent = "";
		$row_department[0]->tShortDescription ="";
		$row_department[0]->iState = "1";
		$row_department[0]->iMember = "0";
		$row_department[0]->iDisplayInNav="1";	
		$row_department[0]->vImage1 = "";	
		$row_department[0]->vImage2 = "";	
		$row_department[0]->vImage3 = "";	

		if(count($_POST) > 0)
		{
			if(isset($this->request["title"]))
				$row_department[0]->vTitle = $this->request["title"];
			if(isset($this->request["template"]))
				$row_department[0]->vTemplate = $this->request["template"];
			if(isset($this->request["layout"]))
				$row_department[0]->vLayout = $this->request["layout"];
			if(isset($this->request["seo_title"])) 
				$row_department[0]->vSeoTitle = $this->request["seo_title"];
			if(isset($this->request["meta_title"]))
				$row_department[0]->vMetaTitle = $this->request["meta_title"];
			if(isset($this->request["meta_description"]))
				$row_department[0]->tMetaDescription = $this->request["meta_description"];
			if(isset($this->request["keywords"]))
				$row_department[0]->tKeywords = $this->request["keywords"];
			if(isset($this->request["content"]))
				$row_department[0]->tContent = $this->request["content"];
			if(isset($this->request["short_description"]))
				$row_department[0]->tShortDescription = $this->request["short_description"];
			if(isset($this->request["state"]))
				$row_department[0]->iState = $this->request["state"];
			if(isset($this->request["member"]))
				$row_department[0]->iMember = $this->request["member"];	
			else
			$row_department[0]->iState = "";
			$row_department[0]->iMember = "";
			$row_department[0]->vImage1 = "";	
			$row_department[0]->vImage2 = "";	
			$row_department[0]->vImage3 = "";
		}

		#IF EDIT MODE SELECTED
		if(!empty($this->request['id']))
		{
		    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Update Department");
			if(!isset($this->request['msg']) || empty($this->request['msg']))
			{
				$this->obDb->query = "SELECT D.*,F.iOwner_FK,F.iState FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE iDeptid_PK=iSubId_FK AND vType='department' AND iDeptId_PK='".$this->request['id']."'";
				$row_department=$this->obDb->fetchQuery();
				
			/*	$this->request["tmEditDate"]=$row_department[0]->tmEditDate;
				$this->request["tmFormatEditDate"]=date("M d,Y",$this->request["tmEditDate"]);*/
				$this->request["tmFormatEditDate"]=$this->libFunc->dateFormat($row_department[0]->tmEditDate);
				$this->ObTpl->set_var("TPL_VAR_MSG",LBL_LAST_UPDATE.$this->request['tmFormatEditDate']);
			}
			elseif($this->request['msg']==1)
			{
				$this->obDb->query = "SELECT vImage1,vImage2,vImage3  FROM ".DEPARTMENTS." WHERE  iDeptId_PK=".$this->request['id'];
				$row_deptM=$this->obDb->fetchQuery();
				
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);						
			}
			$strNavigationLabel=LBL_EDIT_DEPT;
		//	$this->request["owner"]=$row_department[0]->iOwner_FK;
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_DEPTID",$this->request['id']);
			
			#HANDLING BLOCKS		
			$this->ObTpl->parse("msg_blk","DSPMSG_BLK");											
			$this->ObTpl->set_var("imagebox_blk","");
			$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_EDIT_DEPT);
			$this->ObTpl->parse("imagelink_blk","DSPIMAGELINK_BLK");											
			$this->ObTpl->set_var("TPL_VAR_POPUPURL", SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&amp;id=".$this->request['id']);
		}	
		elseif(!empty($this->request['dupeid']))#IF DUPLICATE SELECTED
		{
		    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Duplicate Department");
			if(!isset($this->request['msg']))
			{
			$this->obDb->query = "SELECT D.*,F.iOwner_FK,F.iState  FROM ".DEPARTMENTS." D, ".FUSIONS." F WHERE iDeptid_PK=iSubId_FK AND vType='department' AND iDeptId_PK='".$this->request['dupeid']."'";
			$row_department=$this->obDb->fetchQuery();
			$this->ObTpl->set_var("msg_blk","");
			}
			elseif($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
			}	
			$strNavigationLabel=LBL_DUPLICATE_DEPT;					

			$this->ObTpl->set_var("TPL_VAR_MODE","duplicate");
			$this->ObTpl->set_var("TPL_VAR_DEPTID",$this->request['dupeid']);
			#HANDLING BLOCKS
			$this->ObTpl->set_var("imagelink_blk","");
			$this->ObTpl->parse("imagebox_blk","DSPIMAGEBOX_BLK");											
					
			$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_DUPLICATE_DEPT);
		}
		else #IF ADD
		{
		    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Add Department");
			$strNavigationLabel=LBL_ADD_DEPT;
			$this->ObTpl->set_var("TPL_VAR_MODE","Add");
			$this->ObTpl->set_var("imagelink_blk","");
			$this->ObTpl->set_var("TPL_VAR_DEPTID","");
			if(!isset($this->request['msg']))
			{
				$this->ObTpl->set_var("msg_blk","");
			}
			elseif($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
			}		
			$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_ADD_DEPT);
			$this->ObTpl->parse("imagebox_blk","DSPIMAGEBOX_BLK");											
		}	

		#INTIALIZING OWNER
		if(empty($this->request['owner']))
		{
			$this->request['owner']=0;
		}
		$this->ObTpl->set_var("TPL_VAR_TYPE","department");
		//******************TOP NAVIGATION********************
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
		$topNavigation=$this->m_topNavigation($this->request['owner'],$this->request['type']);
		$topNavigation.=" &raquo;&nbsp;".$strNavigationLabel;
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBS", stripslashes($topNavigation));
		//****************************************************
				
		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("FORM", SITE_URL."ecom/adminindex.php?action=ec_db.Dept&amp;owner=". $this->request["owner"]);

		#ASSIGNING FORM VARAIABLES

		$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($row_department[0]->vTitle));
		$this->ObTpl->set_var("TPL_VAR_SEOTITLE", $this->libFunc->m_displayContent($row_department[0]->vSeoTitle));
		$this->ObTpl->set_var("TPL_VAR_METATITLE", $this->libFunc->m_displayContent($row_department[0]->vMetaTitle));
		$this->ObTpl->set_var("TPL_VAR_KEYWORDS", $this->libFunc->m_displayContent($row_department[0]->tKeywords));
		$this->ObTpl->set_var("TPL_VAR_METADESC", $this->libFunc->m_displayContent($row_department[0]->tMetaDescription));
		if($this->libFunc->m_displayContent($row_department[0]->vTemplate)=="product_list.htm")
		{
			$this->ObTpl->set_var("TPL_VAR_SELECT1","");
			$this->ObTpl->set_var("TPL_VAR_SELECT2","selected'");
		}
		
			if (is_dir($this->departmentTemplatePath)) 
			{
				if ($dh = opendir($this->departmentTemplatePath))
				{			
					while (($templateName = readdir($dh)) !== false) 
					{
						if($templateName!="." && $templateName!="..") {
							if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
								if($templateName==$row_department[0]->vTemplate)
								{
									$this->ObTpl->set_var("SELTEMPLATE","selected");
								}
								else
								{
									$this->ObTpl->set_var("SELTEMPLATE","");
								}
								$this->ObTpl->set_var("TPL_VAR_TEMPLATENAME",$templateName);
								$this->ObTpl->parse("template_blk","TPL_TEMPLATE_BLK",true);
							}
						}
					}
					closedir($dh);
				}
			}

			if (is_dir($this->layoutTemplatePath)) 
			{
				if ($dh = opendir($this->layoutTemplatePath))
				{			
					while (($templateName = readdir($dh)) !== false) 
					{
						if($templateName!="." && $templateName!="..") {
							if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
								if($templateName==$row_department[0]->vLayout)
								{
									$this->ObTpl->set_var("SELLAYOUT","selected");
								}elseif (($row_department[0]->vLayout == "") && ($templateName == MAIN_LAYOUT))
 								{
								 	$this->ObTpl->set_var("SELLAYOUT","selected");
								 }
								else
								{
									$this->ObTpl->set_var("SELLAYOUT","");
								}
								$this->ObTpl->set_var("TPL_VAR_LAYOUT",$templateName);
								$this->ObTpl->parse("layout_blk","TPL_LAYOUT_BLK",true);
							}
						}
					}
					closedir($dh);
				}
			}

		
		if($row_department[0]->iState==1)
		{
			$this->ObTpl->set_var("TPL_VAR_STATE","checked='checked'");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_STATE","");					
		}	
		
		if($row_department[0]->iMember==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MEMBER","checked='checked'");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MEMBER","");					
		}
	
		if($row_department[0]->iDisplayInNav==1)
		{
			$this->ObTpl->set_var("TPL_VAR_DISPLAYNAV","checked='checked'");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_DISPLAYNAV","");					
		}	
		$this->ObTpl->set_var("TPL_VAR_CONTENT",$this->libFunc->m_displayContent($row_department[0]->tContent));
		$this->ObTpl->set_var("TPL_VAR_SHORTDESC",$this->libFunc->m_displayContent($row_department[0]->tShortDescription));
		if(isset($this->request['msg']) && $this->request['msg']==1 && isset( $this->request['id']))
		{
			if($this->libFunc->m_checkFileExist($row_deptM[0]->vImage1,"department"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_ADDIMAGE);	
			}

			if($this->libFunc->m_checkFileExist($row_deptM[0]->vImage2,"department"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_ADDIMAGE);	
			}
			if($this->libFunc->m_checkFileExist($row_deptM[0]->vImage3,"department"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_ADDIMAGE);	
			}
		}
		else
		{
			if($this->libFunc->m_checkFileExist($row_department[0]->vImage1,"department"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_ADDIMAGE);	
			}

			if($this->libFunc->m_checkFileExist($row_department[0]->vImage2,"department"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_ADDIMAGE);	
			}
			if($this->libFunc->m_checkFileExist($row_department[0]->vImage3,"department"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_ADDIMAGE);	
			}
		}

		#SETTING UP FCK EDITOR			
		$oFCKeditor = new CKEditor();
		$oFCKeditor->basePath = '../ckeditor/';
		$oFCKeditor->Value=$this->libFunc->m_displayCms($row_department[0]->tContent);
		
		$oFCKeditor->Height="300";
		$oFCKeditor->ToolbarSet="Default";
		$this->ObTpl->set_var("cmsEditor","<textarea id='TextEditor' name='content'>" . $this->libFunc->m_displayCms($row_department[0]->tContent) . "</textarea><script type='text/javascript'>CKEDITOR.replace('TextEditor');</script>");
		
		return($this->ObTpl->parse("return","department"));

	}
	
	#FUNCTION TO CREATE PRODUCT FORM
	function m_dspProductForm()
		{
			
			$comFunc = new c_commonFunctions();
			$comFunc->obDb = $this->obDb;
			
			#SETTING TEMPLATE VARIABLE
			$this->ObTpl=new template();
			$this->ObTpl->set_file("product", $this->productTemplate);
			$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
			$this->ObTpl->set_block("product","DSPIMAGEBOX_BLK", "imagebox_blk");
			$this->ObTpl->set_block("product","DSPIMAGELINK_BLK", "imagelink_blk");	
			$this->ObTpl->set_block("product","TPL_VAR_POSTAGECODE", "postageCode_blk");	
			$this->ObTpl->set_block("product","TPL_VAR_SUPPLIER", "supplier_blk");	
			$this->ObTpl->set_block("product","DSPMSG_BLK", "msg_blk");	
			$this->ObTpl->set_block("product","TPL_OPTIONLINK_BLK","optlink_blk");
			$this->ObTpl->set_block("product","TPL_STOCKCONTROL_BLK","stock_blk");
			$this->ObTpl->set_block("product","TPL_TEMPLATE_BLK","template_blk");
			$this->ObTpl->set_block("product","TPL_LAYOUT_BLK","layout_blk");
			$this->ObTpl->set_block("product","TPL_OPTION_BLK","option_blk");
            $this->ObTpl->set_block("product","TPL_VAR_RETAILPRICE_BLK","retailprice_blk");
			$this->ObTpl->set_block("product","TPL_ATTRIBUTES_BLK","attributes_blk");
			$this->ObTpl->set_block("product","TPL_ATTRIBUTESFOREDIT_BLK","attributesforedit_blk");
			$this->ObTpl->set_block("product","TPL_AJAX_BLK","ajax_blk");
			
			$this->ObTpl->set_block("product","TPL_DOWNLOADABLEFILE_BOX_BLK","downloadablefile_box_blk");
			$this->ObTpl->set_block("product","TPL_DOWNLOADABLEFILE_LINK_BLK","downloadablefile_link_blk");

			$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
			$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
			$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
			$this->ObTpl->set_var("layout_blk","");
			
			
			$this->ObTpl->set_var("TPL_VAR_RETAILPRICE","");
			$this->ObTpl->set_var("template_blk","");
			$this->ObTpl->set_var("postageCode_blk","");
			$this->ObTpl->set_var("supplier_blk","");
			$this->ObTpl->set_var("stock_blk","");
			$this->ObTpl->set_var("attributes_blk","");
			$this->ObTpl->set_var("attributesforedit_blk","");
			$this->ObTpl->set_var("ajax_blk","");
            $this->ObTpl->set_var("retailprice_blk","");
			
			$this->ObTpl->set_var("downloadablefile_box_blk","");
			$this->ObTpl->set_var("downloadablefile_link_blk","");
			
			$this->ObTpl->set_var("option_blk","");
			
			//defining language variables
			$this->ObTpl->set_var("LANG_VAR_PRODUCTBUILDER",LANG_PRODUCTBUILDER);
			$this->ObTpl->set_var("LANG_VAR_PRODUCTTITLE",LANG_PRODUCTTITLE);
			$this->ObTpl->set_var("LANG_VAR_PRODUCTCODE",LANG_PRODUCTCODE);
			$this->ObTpl->set_var("LANG_VAR_PRODUCTPRICE",LANG_PRICE);
			$this->ObTpl->set_var("LANG_VAR_RRP",LANG_RRPPRICE);
			$this->ObTpl->set_var("LANG_VAR_SELECTLAYOUT",LANG_SELECTLAYOUT);	
			$this->ObTpl->set_var("LANG_VAR_SELECTTEMPLATE",LANG_SELECTTEMPLATE);
			$this->ObTpl->set_var("LANG_VAR_STOCKCONTROLTXT",LANG_STOCKCONTROLTXT);
			$this->ObTpl->set_var("LANG_VAR_USESTOCK",LANG_USESTOCK);
			$this->ObTpl->set_var("LANG_VAR_ALLOWBACKORDERS",LANG_ALLOWBACKORDERS);
			$this->ObTpl->set_var("LANG_VAR_STOCKLEVELS",LANG_STOCKLEVELS);
			$this->ObTpl->set_var("LANG_VAR_MINSTOCKLEVELS",LANG_MINSTOCKLEVELS);
			$this->ObTpl->set_var("LANG_VAR_ONORDER",LANG_ONORDER);
			$this->ObTpl->set_var("LANG_VAR_DUEDATE",LANG_DUEDATE);
			$this->ObTpl->set_var("LANG_VAR_POSTAGESUPPLIER",LANG_POSTAGESUPPLIER);
			$this->ObTpl->set_var("LANG_VAR_POSTAGECODE",LANG_POSTAGECODE);
			$this->ObTpl->set_var("LANG_VAR_CODESELECT",LANG_SELECTCODE);
			$this->ObTpl->set_var("LANG_VAR_ITEMWEIGHT",LANG_ITEMWEIGHT);
			$this->ObTpl->set_var("LANG_VAR_PRODUCTNOTES",LANG_PRODUCTNOTES);
			$this->ObTpl->set_var("LANG_VAR_FREEPOSTAGE",LANG_FREEPOSTAGE);
			$this->ObTpl->set_var("LANG_VAR_SUPPLIERLOGO",LANG_SUPPLIERLOGO);
			$this->ObTpl->set_var("LANG_VAR_SELECTSUPP",LANG_SELECTSUPP);
			$this->ObTpl->set_var("LANG_VAR_IMAGESFILES",LANG_IMAGESFILES);
			$this->ObTpl->set_var("LANG_VAR_IMAGEASMALL",LANG_IMAGEASMALL);
			$this->ObTpl->set_var("LANG_VAR_IMAGEBMED",LANG_IMAGEBMED);
			$this->ObTpl->set_var("LANG_IMAGECLAR",LANG_IMAGECLAR);
			$this->ObTpl->set_var("LANG_DOWNLOADFILE",LANG_DOWNLOADFILE);
			$this->ObTpl->set_var("LANG_VAR_RESIZETXT",LANG_RESIZETXT);
			$this->ObTpl->set_var("LANG_VAR_ONLYTXT",LANG_ONLYTEXT);
			$this->ObTpl->set_var("LANG_VAR_AUTORESIZETXT",LANG_AUTORESIZETXT);
			$this->ObTpl->set_var("LANG_VAR_SEOTXT",LANG_SEOTXT);
			$this->ObTpl->set_var("LANG_VAR_FILENAME",LANG_FILENAME);
			$this->ObTpl->set_var("LANG_VAR_METATITLE",LANG_METATITLE);
			$this->ObTpl->set_var("LANG_VAR_METADESCRIPTION",LANG_METADESCRIPTION);
			$this->ObTpl->set_var("LANG_VAR_METAKEYWORDS",LANG_METAKEYWORDS);
			$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKMETATITLE",LANG_LEAVEBLANKFORDEPT);
			$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKGLOBAL",LANG_LEAVEBLANKGLOBAL);
			$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKGLOBALKEY",LANG_LEAVEBLANKGLOBALKEY);
			$this->ObTpl->set_var("LANG_VAR_STATUSANDDESCRIPTION",LANG_STATUANDDESCRIPTION);
			$this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
			$this->ObTpl->set_var("LANG_VAR_SHORTDESC",LANG_SHORTDESC);
			$this->ObTpl->set_var("LANG_VAR_LONGDESC",LANG_LONGDESC);
			$this->ObTpl->set_var("LANG_VAR_VATTAX",LANG_VATTAX);
			$this->ObTpl->set_var("LANG_VAR_ONSALE",LANG_ONSALE);
			$this->ObTpl->set_var("LANG_VAR_PRODUCTOPTIONS",LANG_PRODUCTOPTIONS);
			$this->ObTpl->set_var("LANG_VAR_PVOLUMEDISCOUNTS",LANG_VOLUMEDISCOUNTS);
			$this->ObTpl->set_var("LANG_VAR_ADDTOBASKETBUTT",LANG_ADDTOBASKBUTT);
			$this->ObTpl->set_var("LANG_VAR_PRODUCTRETAILPRICE",LANG_RETAILPRICE);
			$this->ObTpl->set_var("LANG_VAR_ENQUIREBUTT",LANG_ENQUIREBUTT);
			$this->ObTpl->set_var("TPL_VAR_REAL_PATH",$this->real_path);
			
			$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
			
			$this->ObTpl->set_var("imgWidth1",  UPLOAD_SMIMAGEWIDTH);
			$this->ObTpl->set_var("imgHeight1", UPLOAD_SMIMAGEHEIGHT);
			$this->ObTpl->set_var("imgWidth2",  UPLOAD_MDIMAGEWIDTH);
			$this->ObTpl->set_var("imgHeight2", UPLOAD_MDIMAGEHEIGHT);
			$this->ObTpl->set_var("imgWidth3",  UPLOAD_LGIMAGEWIDTH);
			$this->ObTpl->set_var("imgHeight3", UPLOAD_LGIMAGEHEIGHT);
			// Tell the user which image types are suppoerted:
			$gd = gd_info();
			$tmp = array ();
			$gd['GIF Create Support'] == true ? $tmp[] = "gif" : false;
			$gd['JPEG Support'] == true ? $tmp[] = "jpg" : false;
			$gd['PNG Support'] == true ? $tmp[] = "png" : false;
			$this->ObTpl->set_var("resampleList", implode(", ", $tmp));
			

			$row_product[0]->vTitle = "";
			$row_product[0]->vSku = "";
			$row_product[0]->fListPrice = "";
			$row_product[0]->fPrice  = "";
			$row_product[0]->fRetailPrice  = "";
			$row_product[0]->fItemWeight  = "";	
			$row_product[0]->iInventory  = "";
			$row_product[0]->iInventoryMinimum  = "";
			$row_product[0]->vTemplate ="";
			$row_product[0]->vLayout = "";
			$row_product[0]->vSeoTitle = "";
			$row_product[0]->vMetaTitle = "";
			$row_product[0]->tMetaDescription = "";
			$row_product[0]->tKeywords = "";
			$row_product[0]->tContent = "";
			$row_product[0]->tShortDescription ="";
			$row_product[0]->iState = "1";
			$row_product[0]->state = "";		
			$row_product[0]->iBackorder  = "";	
			$row_product[0]->iUseinventory = "";	
			$row_product[0]->iOnorder  = "";	
			$row_product[0]->tmDuedate  = "";	
			$row_product[0]->vShipNotes  = "";	
			$row_product[0]->vShipCode = "";	
			$row_product[0]->iFreeShip= "";	
			$row_product[0]->iTaxable= "";
			$row_product[0]->iIncVat= "";	
			$row_product[0]->iDiscount  = "";	
			$row_product[0]->iSale  = "";	
			$row_product[0]->fShipWeight = "";	
			$row_product[0]->iCartButton  = "1";	
			$row_product[0]->iEnquiryButton  = "";
			$row_product[0]->tImages  = "";
			$row_product[0]->vImage1="";
			$row_product[0]->vImage2="";
			$row_product[0]->vImage3="";
			$row_product[0]->vDownloadablefile ="";
			$row_product[0]->fItemHeight ="";
			$row_product[0]->fItemWidth ="";
			$row_product[0]->fItemDepth ="";
			$row_product[0]->vASIN ="";
			$row_product[0]->vISBN ="";
			$row_product[0]->vMPN ="";
			$row_product[0]->vUPC ="";
			$dueDate="";

	#CHECKING POST VARIABLES TO INTIALIZE VALUES
			if(count($_POST) > 0)
			{
				if(isset($this->request["title"])) 
					$row_product[0]->vTitle = $this->request["title"];
				if(isset($this->request["sku"]))
					$row_product[0]->vSku = $this->request["sku"];
				if(isset($this->request["list_price"]))
					$row_product[0]->fListPrice = $this->request["list_price"];
				if(isset($this->request["price"]))
					$row_product[0]->fPrice  = $this->request["price"];
				if(isset($this->request["item_weight"]))
					$row_product[0]->fItemWeight  = $this->request["item_weight"];
				if(isset($this->request["inventory"]))
					$row_product[0]->iInventory= $this->request["inventory"];
				if(isset($this->request["inventory_minimum"]))
					$row_product[0]->iInventoryMinimum= $this->request["inventory_minimum"];
				if(isset($this->request["template"]))
					$row_product[0]->vTemplate = $this->request["template"];
				if(isset($this->request["layout"]))
					$row_product[0]->vLayout = $this->request["layout"];
				if(isset($this->request["seo_title"]))
					$row_product[0]->vSeoTitle = $this->request["seo_title"];
				if(isset($this->request["meta_title"]))
					$row_product[0]->vMetaTitle = $this->request["meta_title"];
				if(isset($this->request["meta_description"]))
					$row_product[0]->tMetaDescription = $this->request["meta_description"];
				if(isset($this->request["keywords"]))
					$row_product[0]->tKeywords = $this->request["keywords"];
				if(isset($this->request["content"]))
					$row_product[0]->tContent = $this->request["content"];
				if(isset($this->request["short_description"])) 
					$row_product[0]->tShortDescription = $this->request["short_description"];
				if(isset($this->request["ship_code"])) 
					$row_product[0]->vShipCode = $this->request["ship_code"];
				
				if(isset($this->request["backorder"]))
					$row_product[0]->iBackorder = $this->request["backorder"];	
				else
					$row_product[0]->iBackorder ="";
				if(isset($this->request["on_order"]))
					$row_product[0]->iOnorder = $this->request["on_order"];		
				if(isset($this->request["due_date"])) 
					$row_product[0]->tmDuedate = $this->request["due_date"];		
				if(isset($this->request["use_inventory"]))
					$row_product[0]->iUseinventory = $this->request["use_inventory"];
				else
					$row_product[0]->iUseinventory = "";
				if(isset($this->request["ship_notes"]))
					$row_product[0]->vShipNotes= $this->request["ship_notes"];		
				if(isset($this->request["free_postage"]))
					$row_product[0]->iFreeShip = $this->request["free_postage"];
				else
					$row_product[0]->iFreeShip ="";
				if(isset($this->request["taxable"]))
					$row_product[0]->iTaxable = $this->request["taxable"];	
				else
					$row_product[0]->iTaxable = "";
				if(isset($this->request["inc_vat"]))
					$row_product[0]->iIncVat = $this->request["inc_vat"];	
				else
					$row_product[0]->iIncVat = "";
				if(isset($this->request["vdiscount"]))
					$row_product[0]->iDiscount = $this->request["vdiscount"];	
				else
					$row_product[0]->iDiscount = "";
				if(isset($this->request["sale"]))
					$row_product[0]->iSale  = $this->request["sale"];	
				else
					$row_product[0]->iSale  = "";

				if(isset($this->request["ship_weight"]))
					$row_product[0]->fShipWeight = $this->request["ship_weight"];		
				if(isset($this->request["cart_button"]))
					$row_product[0]->iCartButton = $this->request["cart_button"];	
				else
					$row_product[0]->iCartButton = "";
				if(isset($this->request["vendorid"]))
					$row_product[0]->iVendorid_FK  = $this->request["vendorid"];	
				if(isset($this->request["state"]))
					$row_product[0]->iState = $this->request["state"];	
				else
					$row_product[0]->iState ="";
				$row_product[0]->vImage1="";
				$row_product[0]->vImage2="";
				$row_product[0]->vImage3="";
				$row_product[0]->vDownloadablefile ="";
				$dueDate=$row_product[0]->tmDuedate;
			}
			
			#CHECKING ID ,DUPLICATE ID ,TO USE SAME FORM FORM ADD,DUPLICATE AND EDIT
			if(!empty($this->request['id']))  //FOR EDITING
			{
			    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Update Product");
				$this->m_getAttributes($this->request['id']);
				if(!isset($this->request['msg']) || empty($this->request['msg']))
				{
					$this->obDb->query = "SELECT D.*,F.iOwner_FK,F.iState  FROM ".PRODUCTS." D LEFT JOIN ".FUSIONS." F ON iProdId_PK=iSubId_FK WHERE vType='product' AND  iProdId_PK='".$this->request['id']."' AND F.iOwner_FK='".$this->request['owner']."'";
					$row_product=$this->obDb->fetchQuery();	
					$this->request["tmFormatEditDate"]=$this->libFunc->dateFormat($row_product[0]->tmEditDate);
					$this->ObTpl->set_var("TPL_VAR_MSG",LBL_LAST_UPDATE.$this->request['tmFormatEditDate']);
					
				}
				else
				{
					$this->obDb->query = "SELECT vImage1,vImage2,vImage3,tImages,vDownloadablefile FROM ".PRODUCTS." WHERE iProdId_PK=".$this->request['id'];
					$row_prodM=$this->obDb->fetchQuery();
					$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);	
				}
				$dueDate=$this->libFunc->dateFormat2($row_product[0]->tmDuedate);
				$strNavigationLabel="&nbsp;".LBL_EDIT_PROD;
				
				$this->ObTpl->set_var("TPL_VAR_MODE","edit");
				$this->ObTpl->set_var("TPL_VAR_PRODID",$this->request['id']);
				$this->ObTpl->parse("msg_blk","DSPMSG_BLK");											
				$this->ObTpl->set_var("imagebox_blk","");
				$this->ObTpl->parse("imagelink_blk","DSPIMAGELINK_BLK");	
				$this->ObTpl->parse("downloadablefile_link_blk","TPL_DOWNLOADABLEFILE_LINK_BLK");	
				$this->ObTpl->parse("optlink_blk","TPL_OPTIONLINK_BLK");		
				
				$this->ObTpl->set_var("TPL_VAR_POPUPURL", SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&amp;id=".$this->request['id']);
				$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_EDIT_PROD);
			}	
			elseif(!empty($this->request['dupeid']))  //TO DUPLICATE
			{
			    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Duplicate Product");
				if(!isset($this->request['msg']) || empty($this->request['msg']))
				{
					$this->obDb->query = "SELECT D.*,F.iOwner_FK,iState FROM ".PRODUCTS." D, ".FUSIONS." F WHERE iProdId_PK=iSubId_FK AND vType='product' AND  iProdId_PK='".$this->request['dupeid']."' AND F.iOwner_FK='".$this->request['owner']."'";
					$row_product=$this->obDb->fetchQuery();
					$this->ObTpl->set_var("msg_blk","");
				}
				elseif($this->request['msg']==1)
				{
					$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
					$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
				}	
				$dueDate=$this->libFunc->dateFormat2($row_product[0]->tmDuedate);
				$strNavigationLabel="&nbsp;".LBL_DUPLICATE_PROD;					
				$this->ObTpl->set_var("optlink_blk","");
				$this->ObTpl->set_var("TPL_VAR_MODE","duplicate");
				$this->ObTpl->set_var("TPL_VAR_PRODID",$this->request['dupeid']);
				$this->ObTpl->set_var("imagelink_blk","");
				$this->ObTpl->parse("imagebox_blk","DSPIMAGEBOX_BLK");										
				$this->ObTpl->parse("downloadablefile_box_blk","TPL_DOWNLOADABLEFILE_BOX_BLK");
				$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_DUPLICATE_PROD);
				$this->ObTpl->parse("option_blk","TPL_OPTION_BLK");		
			}
			else //FOR INSERTING NEW RECORD
			{
			    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Add Product");
				$this->m_getAttributes(0);
				$this->ObTpl->set_var("TPL_VAR_PRODID","");
				$strNavigationLabel="&nbsp;".LBL_ADD_PROD;
				$this->ObTpl->set_var("imagelink_blk","");
				if(!isset($this->request['msg']))
				{
					$this->ObTpl->set_var("msg_blk","");
				}
				elseif($this->request['msg']==1)
				{
					$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
					$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
				}		
				$this->ObTpl->parse("imagebox_blk","DSPIMAGEBOX_BLK");
				$this->ObTpl->parse("downloadablefile_box_blk","TPL_DOWNLOADABLEFILE_BOX_BLK");
				$this->ObTpl->set_var("optlink_blk","");				
				$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_ADD_PROD);
				$this->ObTpl->parse("option_blk","TPL_OPTION_BLK");	
			}

			if(empty($this->request['owner']))
			{
				$this->request['owner']=0;
			}
			if(!isset($this->request['type']))
			{
				$this->request['type']="department";
			}

			$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);
			$this->ObTpl->set_var("TPL_VAR_OWNER",$this->request['owner']);
			//******************TOP NAVIGATION********************

			$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
			$topNavigation=$this->m_topNavigation($this->request['owner'],$this->request['type']);
			$topNavigation.=" &raquo;".$strNavigationLabel;
			$this->ObTpl->set_var("TPL_VAR_BREDCRUMBS", stripslashes($topNavigation));
			//****************************************************
			$this->ObTpl->set_var("FORMURL", SITE_URL."ecom/adminindex.php?action=ec_db.insertProduct&amp;type=product&amp;owner=". $this->request["owner"]);
			#***************************************************************#PARSING STOCKCONTROL BLOCK
			if(STOCK_CHECK==1)
			{
				$this->ObTpl->parse("stock_blk","TPL_STOCKCONTROL_BLK");
			}
			#***************************************************************#PARSING POSTAGE CODE
			$this->obDb->query = "SELECT vCompany,iVendorid_PK  FROM ".SUPPLIERS." WHERE iStatus=1";
			$row_supplier=$this->obDb->fetchQuery();
			$supllier_count=$this->obDb->record_count;
			if($supllier_count>0)
			{
				for($i=0;$i<$supllier_count;$i++)
				{	
					if($row_product[0]->iVendorid_FK== $row_supplier[$i]->iVendorid_PK)
					{
						$this->ObTpl->set_var("SELECTED","selected");
					}
					else
					{
						$this->ObTpl->set_var("SELECTED","");
					}
					$this->ObTpl->set_var("TPL_VAR_VALUE",$row_supplier[$i]->iVendorid_PK);
					$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($row_supplier[$i]->vCompany));
					$this->ObTpl->parse("supplier_blk","TPL_VAR_SUPPLIER",true);
				}
			}
			
			#****************************************************************
			$this->obDb->query ="SELECT PD.vDescription,vField1,vField2,iPostDescId_PK  FROM  ".POSTAGE." P,".POSTAGEDETAILS." PD WHERE iPostId_PK=iPostId_FK AND vKey='codes'";
			$rsPostage=$this->obDb->fetchQuery();
			$postageCnt=$this->obDb->record_count;
			if($postageCnt>0)
			{
				for($i=0;$i<$postageCnt;$i++)
				{	
					if($row_product[0]->vShipCode== $rsPostage[$i]->iPostDescId_PK)
					{
						$this->ObTpl->set_var("SELECTED1","selected");
					}
					else
					{
						$this->ObTpl->set_var("SELECTED1","");
					}
					$this->ObTpl->set_var("TPL_VAR_POSTID",$rsPostage[$i]->iPostDescId_PK);
					$this->ObTpl->set_var("TPL_VAR_POSTVALUE",$rsPostage[$i]->vField2);
					$this->ObTpl->set_var("TPL_VAR_POSTTITLE",$this->libFunc->m_displayContent($rsPostage[$i]->vDescription));
					$this->ObTpl->parse("postageCode_blk","TPL_VAR_POSTAGECODE",true);
				}
			}
	
		#ASSIGNING TEMPLATES VARIABLES
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($row_product[0]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_SKU",$this->libFunc->m_displayContent($row_product[0]->vSku));
		

			if (is_dir($this->productTemplatePath)) 
			{
				if ($dh = opendir($this->productTemplatePath))
				{			
					while (($templateName = readdir($dh)) !== false) 
					{
						if($templateName!="." && $templateName!="..") {
							if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
							if($templateName==$row_product[0]->vTemplate)
							{
								$this->ObTpl->set_var("SELTEMPLATE","selected");
							}
							else
							{
								$this->ObTpl->set_var("SELTEMPLATE","");
							}
							$this->ObTpl->set_var("TPL_VAR_TEMPLATENAME",$templateName);
							$this->ObTpl->parse("template_blk","TPL_TEMPLATE_BLK",true);
							}# End  file type validation
						}
					}
					closedir($dh);
				}
			}
			
			# Added Item Dimensions and multiple identifier codes for shipping, seo, and product feed purposes
			$this->ObTpl->set_var("TPL_VAR_WIDTH",$row_product[0]->fItemWidth);
			$this->ObTpl->set_var("TPL_VAR_HEIGHT",$row_product[0]->fItemHeight);
			$this->ObTpl->set_var("TPL_VAR_DEPTH",$row_product[0]->fItemDepth);
			$this->ObTpl->set_var("TPL_VAR_ASIN",$row_product[0]->vASIN);
			$this->ObTpl->set_var("TPL_VAR_ISBN",$row_product[0]->vISBN);
			$this->ObTpl->set_var("TPL_VAR_MPN",$row_product[0]->vMPN);
			$this->ObTpl->set_var("TPL_VAR_UPC",$row_product[0]->vUPC);
			
			if (is_dir($this->layoutTemplatePath)) 
			{
				if ($dh = opendir($this->layoutTemplatePath))
				{			
					while (($templateName = readdir($dh)) !== false) 
					{
						if($templateName!="." && $templateName!="..") {
							if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
								if($templateName==$row_product[0]->vLayout)
								{
									$this->ObTpl->set_var("SELLAYOUT","selected");
								}elseif (($row_product[0]->vLayout == "") && ($templateName == MAIN_LAYOUT))
								 {
								 	$this->ObTpl->set_var("SELLAYOUT","selected");
								 }
								else
								
								{
									$this->ObTpl->set_var("SELLAYOUT","");
								}
								$this->ObTpl->set_var("TPL_VAR_LAYOUT",$templateName);
								$this->ObTpl->parse("layout_blk","TPL_LAYOUT_BLK",true);
							}
						}
					}
					closedir($dh);
				}
			}
			
			if(NETGROSS == 1)
			{
				$this->ObTpl->set_var("TPL_VAR_PRICEMESSAGE","Enter price inclusive of VAT");
			}else
			{
				$this->ObTpl->set_var("TPL_VAR_PRICEMESSAGE","Enter price exclusive of VAT");	
			}
			
			$this->ObTpl->set_var("TPL_VAR_LISTPRICE",$this->libFunc->m_displayContent($row_product[0]->fListPrice));
			$this->ObTpl->set_var("TPL_VAR_PRICE",$this->libFunc->m_displayContent($row_product[0]->fPrice));
			if(ENABLE_WHOLESALE ==1)
            {
            $this->ObTpl->set_var("TPL_VAR_RETAILPRICE",$this->libFunc->m_displayContent($row_product[0]->fRetailPrice));
            $this->ObTpl->parse("retailprice_blk","TPL_VAR_RETAILPRICE_BLK");
            }
			$this->ObTpl->set_var("TPL_VAR_ITEMWEIGHT",$this->libFunc->m_displayContent($row_product[0]->fItemWeight));
			$this->ObTpl->set_var("TPL_VAR_STOCKLEVELS",$this->libFunc->m_displayContent($row_product[0]->iInventory ));
			$this->ObTpl->set_var("TPL_VAR_STOCKMINIMUM",$this->libFunc->m_displayContent($row_product[0]->iInventoryMinimum ));
			$this->ObTpl->set_var("TPL_VAR_ONORDER",$this->libFunc->m_displayContent($row_product[0]->iOnorder));
			
			$this->ObTpl->set_var("TPL_VAR_PRODUCTNOTES",$this->libFunc->m_displayContent($row_product[0]->vShipNotes  ));

			$this->ObTpl->set_var("TPL_VAR_DUEDATE",$dueDate);
			$this->ObTpl->set_var("TPL_VAR_STOCKLEVELS",$this->libFunc->m_displayContent($row_product[0]->iInventory ));
		
			$this->ObTpl->set_var("TPL_VAR_SEOTITLE",$this->libFunc->m_displayContent($row_product[0]->vSeoTitle));
			$this->ObTpl->set_var("TPL_VAR_METATITLE", $this->libFunc->m_displayContent($row_product[0]->vMetaTitle));
			$this->ObTpl->set_var("TPL_VAR_KEYWORDS", $this->libFunc->m_displayContent($row_product[0]->tKeywords));
			$this->ObTpl->set_var("TPL_VAR_METADESC", $this->libFunc->m_displayContent($row_product[0]->tMetaDescription));
			$this->ObTpl->set_var("TPL_VAR_TEMPLATE",$this->libFunc->m_displayContent($row_product[0]->vTemplate));
			$this->ObTpl->set_var("TPL_VAR_LAYOUT",$this->libFunc->m_displayContent($row_product[0]->vLayout));
			$this->ObTpl->set_var("TPL_VAR_STATE",$this->libFunc->assignToTemplates($row_product[0]->iState));
			$this->ObTpl->set_var("TPL_VAR_BACKORDERS",$this->libFunc->assignToTemplates($row_product[0]->iBackorder));
			$this->ObTpl->set_var("TPL_VAR_STOCK",$this->libFunc->assignToTemplates($row_product[0]->iUseinventory));
			$this->ObTpl->set_var("TPL_VAR_FREEPOSTAGE",$this->libFunc->assignToTemplates($row_product[0]->iFreeShip));
			$this->ObTpl->set_var("TPL_VAR_VAT",$this->libFunc->assignToTemplates($row_product[0]->iTaxable));
			$this->ObTpl->set_var("TPL_VAR_INCVAT",$this->libFunc->assignToTemplates($row_product[0]->iIncVat));
			$this->ObTpl->set_var("TPL_VAR_SALE",$this->libFunc->assignToTemplates($row_product[0]->iSale));
			$this->ObTpl->set_var("TPL_VAR_DISCOUNTS",$this->libFunc->assignToTemplates($row_product[0]->iDiscount));
			$this->ObTpl->set_var("TPL_VAR_BASKET",$this->libFunc->assignToTemplates($row_product[0]->iCartButton));
			$this->ObTpl->set_var("TPL_VAR_ENQUIRE",$this->libFunc->assignToTemplates($row_product[0]->iEnquiryButton));
			$this->ObTpl->set_var("TPL_VAR_CONTENT",$this->libFunc->m_displayContent($row_product[0]->tContent));
			$this->ObTpl->set_var("TPL_VAR_SHORTDESC",$this->libFunc->m_displayContent($row_product[0]->tShortDescription));
		

			if(isset($this->request['msg']) && $this->request['msg']==1 && isset( $this->request['id']))
			{
				if($this->libFunc->m_checkFileExist($row_prodM[0]->vImage1,"product"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_EDITIMAGE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_ADDIMAGE);	
				}

				if($this->libFunc->m_checkFileExist($row_prodM[0]->vImage2,"product"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_EDITIMAGE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_ADDIMAGE);	
				}
				if($this->libFunc->m_checkFileExist($row_prodM[0]->vImage3,"product"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_EDITIMAGE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_ADDIMAGE);	
				}
				# Image gallery
				if ($row_prodM[0]->tImages==""){
					$row_prodM[0]->tImages=","; 
				}
				$tImages =explode(",",$row_prodM[0]->tImages);
					for ($i=0;$i<6;$i++)
					{
						$k=$i+1;
						if($this->libFunc->m_checkFileExist($tImages[$i],"product"))
						{
							$this->ObTpl->set_var("TPL_VAR_EXTRAIMGLABEL".$k,LBL_EDITIMAGE);
						}else {
							$this->ObTpl->set_var("TPL_VAR_EXTRAIMGLABEL".$k,LBL_ADDIMAGE);
						}
					}
				#
				if($this->libFunc->m_checkFileExist($row_prodM[0]->vDownloadablefile,"files"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL4",LBL_EDITFILE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL4",LBL_ADDFILE);	
				}
		}
		else
		{
			
				if($this->libFunc->m_checkFileExist($row_product[0]->vImage1,"product"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_EDITIMAGE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_ADDIMAGE);	
				}

				if($this->libFunc->m_checkFileExist($row_product[0]->vImage2,"product"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_EDITIMAGE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_ADDIMAGE);	
				}
				if($this->libFunc->m_checkFileExist($row_product[0]->vImage3,"product"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_EDITIMAGE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_ADDIMAGE);	
				}
			# Image gallery
		    if($row_product[0]->tImages != ""){
				$tImages =explode(",",$row_product[0]->tImages);
			}
			if(isset($tImages)){
				for ($i=0;$i<6;$i++)
				{
	     			$k=$i+1;
					if($this->libFunc->m_checkFileExist($tImages[$i],"product"))
					{
						$this->ObTpl->set_var("TPL_VAR_EXTRAIMGLABEL".$k,LBL_EDITIMAGE);
					}else {
						$this->ObTpl->set_var("TPL_VAR_EXTRAIMGLABEL".$k,LBL_ADDIMAGE);
					}
				}
			}
			if($this->libFunc->m_checkFileExist($row_product[0]->vDownloadablefile,"files"))
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL4",LBL_EDITFILE);	
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMGLABEL4",LBL_ADDFILE);	
				}
		}

			#FCKEDITOR	
			$oFCKeditor = new CKEditor();
			$oFCKeditor->basePath = '../ckeditor/';
			$oFCKeditor->Value=$this->libFunc->m_displayCms($row_product[0]->tContent);
			$oFCKeditor->Height="300";
			$oFCKeditor->ToolbarSet="Default";
			$this->ObTpl->set_var("cmsEditor","<textarea id='TextEditor' name='content'>" . $this->libFunc->m_displayCms($row_product[0]->tContent) . "</textarea><script type='text/javascript'>CKEDITOR.replace('TextEditor');</script>");
			
		
			return($this->ObTpl->parse("return","product"));
	}

	#**********************************************************************
	
	  function m_getAttributes($productid){
    	   	
    	if($productid!=0) 
    	{     
	    	$this->obDb->query = "SELECT * FROM ".PRODUCTATTRIBUTES." WHERE iProductid_FK =".$productid;
    		$attribute = $this->obDb->fetchQuery();
    		$count = $this->obDb->record_count;
    	  		
    		if ($count > 0)  # IF THERE IS AN ATTRIBUTE ASSOCIATED WITH PRODUCT
    		{
    			$this->obDb->query = "SELECT A.*,AV.* FROM ".ATTRIBUTES." A, ".ATTRIBUTEVALUES." AV WHERE A.iAttributesid_PK =".$attribute[0]->iAttributeid_FK." AND A.iAttributesid_PK = AV.iAttributesid_FK"; 
				$attributevalue = $this->obDb->fetchQuery();
						
				$fieldname= explode("<!>",$attributevalue[0]->vFieldname);
				$prefix = explode("<!>",$attributevalue[0]->vPrefix);	
				$description = explode("<!>",$attributevalue[0]->tValues);
				$suffix = explode ("<!>",$attributevalue[0]->vSuffix);
			
			
				for($j=0;$j<$attributevalue[0]->iFieldnumber;$j++)
					{
					$this->ObTpl->set_var("TPL_VAR_ATTRIBUTENAME",$fieldname[$j]);
					$this->ObTpl->set_var("TPL_VAR_PREFIX",$this->libFunc->m_displayContent2($prefix[$j]));
					$this->ObTpl->set_var("TPL_VAR_VALUE",$attributevalue[$j]->tValues);
					$this->ObTpl->set_var("TPL_VAR_SUFFIX",$suffix[$j]);	
					$this->ObTpl->parse("attributesforedit_blk","TPL_ATTRIBUTESFOREDIT_BLK",true);
					}
			}
    	}
	  	
    	$this->obDb->query = "SELECT * FROM ".ATTRIBUTES;	
    	$allAttribute = $this->obDb->fetchQuery();
    	$totalcount = $this->obDb->record_count;
	    $this->ObTpl->set_var("TPL_VAR_ATTMODE","&addnew=1");
	      for($i=0;$i<$totalcount;$i++)
	    	{
	    		if($productid!=0)
	    		{  
		    		if ($allAttribute[$i]->iAttributesid_PK == $attribute[0]->iAttributeid_FK){
	    			$this->ObTpl->set_var("TPL_VAR_SELECTED","selected");}else{
	    			$this->ObTpl->set_var("TPL_VAR_SELECTED","");	
	    			}
	    		}
    			$this->ObTpl->set_var("TPL_VAR_ATTRIBUTES",$allAttribute[$i]->vAttributeTitle );		
				$this->ObTpl->set_var("TPL_VAR_ATTRIBUTEID",$allAttribute[$i]->iAttributesid_PK);
				$this->ObTpl->parse("attributes_blk","TPL_ATTRIBUTES_BLK",true);	
	    	}
    	
    $this->ObTpl->parse("ajax_blk","TPL_AJAX_BLK",true);	 	
    }
	
	#DISPLAY CONTENT FORM
	function m_dspContentForm()
	{

		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("content", $this->contentTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_block("content","DSPIMAGEBOX_BLK", "imagebox_blk");
		$this->ObTpl->set_block("content","DSPIMAGELINK_BLK", "imagelink_blk");	
		$this->ObTpl->set_block("content","TPL_TEMPLATE_BLK","template_blk");
		$this->ObTpl->set_block("content","TPL_LAYOUT_BLK","layout_blk");

		$this->ObTpl->set_block("content","DSPMSG_BLK", "msg_blk");	
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");

		$this->ObTpl->set_var("imagebox_blk","");
		$this->ObTpl->set_var("imagelink_blk","");
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("layout_blk","");
		$this->ObTpl->set_var("template_blk","");
		
		//defining language variables
		$this->ObTpl->set_var("LANG_VAR_CONTENTTITLE",LANG_CONTENTBUILDER);
		$this->ObTpl->set_var("LANG_VAR_PAGETITLE",LANG_PAGETITLE);
		$this->ObTpl->set_var("LANG_VAR_SELECTLAYOUT",LANG_SELECTLAYOUT);	
		$this->ObTpl->set_var("LANG_VAR_SELECTTEMPLATE",LANG_SELECTTEMPLATE);
		$this->ObTpl->set_var("LANG_VAR_IMAGES",LANG_IMAGES);
		$this->ObTpl->set_var("LANG_VAR_IMAGEARTICLE",LANG_IMAGEAARTICLE);
		$this->ObTpl->set_var("LANG_VAR_RESIZETXT",LANG_RESIZETXT);
		$this->ObTpl->set_var("LANG_VAR_ONLYTXT",LANG_ONLYTEXT);
		$this->ObTpl->set_var("LANG_VAR_AUTORESIZETXT",LANG_AUTORESIZETXT);
		$this->ObTpl->set_var("LANG_VAR_SEOTXT",LANG_SEOTXT);
		$this->ObTpl->set_var("LANG_VAR_FILENAME",LANG_FILENAME);
		$this->ObTpl->set_var("LANG_VAR_METATITLE",LANG_METATITLE);
		$this->ObTpl->set_var("LANG_VAR_METADESCRIPTION",LANG_METADESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_METAKEYWORDS",LANG_METAKEYWORDS);
		$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKMETATITLE",LANG_LEAVEBLANKFORDEPT);
		$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKGLOBAL",LANG_LEAVEBLANKGLOBAL);
		$this->ObTpl->set_var("LANG_VAR_LEAVEBLANKGLOBALKEY",LANG_LEAVEBLANKGLOBALKEY);
		$this->ObTpl->set_var("LANG_VAR_STATUSANDDESCRIPTION",LANG_STATUANDDESCRIPTION);
		$this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
		$this->ObTpl->set_var("LANG_VAR_SHORTDESC",LANG_SHORTDESC);
		$this->ObTpl->set_var("LANG_VAR_LONGDESC",LANG_LONGDESC);
		
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		
		// [DRK]
		$this->ObTpl->set_var("imgWidth1",  UPLOAD_CONTENTSMIMAGEWIDTH);
		$this->ObTpl->set_var("imgHeight1", UPLOAD_CONTENTSMIMAGEHEIGHT);

		// Tell the user which image types are suppoerted:
		$gd = gd_info();
		$tmp = array ();
		$gd['GIF Create Support'] == true ? $tmp[] = "gif" : false;
		$gd['JPEG Support'] == true ? $tmp[] = "jpg" : false;
		$gd['PNG Support'] == true ? $tmp[] = "png" : false;
		$this->ObTpl->set_var("resampleList", implode(", ", $tmp));
		// [/DRK]
		
		/*CHECKING FOR POST VARIABLES
		IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
		AS USED WHEN RETURNED FROM DATABASE
		THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/

		if(count($_POST) > 0)
		{
			if(isset($this->request["title"]))
				$row_content[0]->vTitle = $this->request["title"];
			if(isset($this->request["template"]))
				$row_content[0]->vTemplate = $this->request["template"];
			if(isset($this->request["layout"]))
				$row_content[0]->vLayout = $this->request["layout"];
			if(isset($this->request["seo_title"])) 
				$row_content[0]->vSeoTitle = $this->request["seo_title"];
			if(isset($this->request["meta_title"]))
				$row_content[0]->vMetaTitle = $this->request["meta_title"];
			if(isset($this->request["meta_description"]))
				$row_content[0]->tMetaDescription = $this->request["meta_description"];
			if(isset($this->request["keywords"]))
				$row_content[0]->tKeywords = $this->request["keywords"];
			if(isset($this->request["content"]))
				$row_content[0]->tContent = $this->request["content"];
			if(isset($this->request["short_description"]))
				$row_content[0]->tShortDescription = $this->request["short_description"];
			if(isset($this->request["state"]))
				$row_content[0]->iState = $this->request["state"];	
			else
				$row_content[0]->iState="";
			$row_content[0]->vImage1="";
			$row_content[0]->vImage2="";
			$row_content[0]->vImage3="";
		}
		else
		{
			$row_content[0]->vTitle = "";
			$row_content[0]->vTemplate ="";
			$row_content[0]->vLayout = "";
			$row_content[0]->vSeoTitle = "";
			$row_content[0]->vMetaTitle = "";
			$row_content[0]->tMetaDescription = "";
			$row_content[0]->tKeywords = "";
			$row_content[0]->tContent = "";
			$row_content[0]->tShortDescription ="";
			$row_content[0]->iState = "1";
			$row_content[0]->vImage1="";
			$row_content[0]->vImage2="";
			$row_content[0]->vImage3="";
		}		
		#IF EDIT MODE SELECTED

		if(!empty($this->request['id']))
		{
		    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Update Article");
			if(!isset($this->request['msg']) || empty($this->request['msg']))
			{
				$this->obDb->query = "SELECT D.*,F.iOwner_FK,F.iState  FROM ".CONTENTS." D LEFT JOIN ".FUSIONS." F ON iContentid_PK=iSubId_FK WHERE vType='content' AND iContentId_PK='".$this->request['id']."'";
				$row_content=$this->obDb->fetchQuery();

				$this->request["tmFormatEditDate"]=$this->libFunc->dateFormat($row_content[0]->tmEditDate);
				$this->ObTpl->set_var("TPL_VAR_MSG",LBL_LAST_UPDATE.$this->request['tmFormatEditDate']);
			}
			elseif($this->request['msg']==1)
			{
				$this->obDb->query = "SELECT vImage1,vImage2,vImage3  FROM ".CONTENTS." WHERE  iContentId_PK=".$this->request['id'];
				$row_contM=$this->obDb->fetchQuery();
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);						
			}	

			$strNavigationLabel=LBL_EDIT_CONTENT;
	
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_DEPTID",$this->request['id']);
					
			#HANDLING BLOCKS		
			$this->ObTpl->parse("msg_blk","DSPMSG_BLK");											
			$this->ObTpl->set_var("imagebox_blk","");
			$this->ObTpl->parse("imagelink_blk","DSPIMAGELINK_BLK");
			
														
			$this->ObTpl->set_var("TPL_VAR_POPUPURL", SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&amp;id=".$this->request['id']);
			$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_EDIT_CONTENT);

		}	
		elseif(!empty($this->request['dupeid']))#IF DUPLICATE SELECTED
		{
		    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Duplicate Article");
			if(!isset($this->request['msg']) || empty($this->request['msg']))
			{
				$this->obDb->query = "SELECT D.*,F.iOwner_FK,F.iState  FROM ".CONTENTS." D, ".FUSIONS." F WHERE iContentId_PK=iSubId_FK AND vType='content' AND iContentId_PK=".$this->request['dupeid'];
				$row_content=$this->obDb->fetchQuery();
				$this->ObTpl->set_var("msg_blk","");
			}
			elseif($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
			}	
			$strNavigationLabel=LBL_DUPLICATE_CONTENT;					

			$this->ObTpl->set_var("TPL_VAR_MODE","duplicate");
			$this->ObTpl->set_var("TPL_VAR_DEPTID",$this->request['dupeid']);
			#HANDLING BLOCKS
			$this->ObTpl->set_var("imagelink_blk","");
			$this->ObTpl->parse("imagebox_blk","DSPIMAGEBOX_BLK");											
			
				
			$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_DUPLICATE_CONTENT);
		}
		else #IF ADD
		{
		    $this->ObTpl->set_var("TPL_VAR_BUILDACT", "Add Article");
			$strNavigationLabel=LBL_ADD_CONTENT;
			$this->ObTpl->set_var("imagelink_blk","");
			$this->ObTpl->set_var("TPL_VAR_DEPTID","");
			if(!isset($this->request['msg']))
			{
				$this->ObTpl->set_var("msg_blk","");
			}
			elseif($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
				$this->ObTpl->parse("msg_blk","DSPMSG_BLK");						
			}		
			$this->ObTpl->parse("imagebox_blk","DSPIMAGEBOX_BLK");
					
			$this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_ADD_CONTENT);
		}	

		#INTIALIZING OWNER
		if(empty($this->request['owner']))
		{
			$this->request['owner']=0;
		}
		if(!isset($this->request['type']))
		{
			$this->request['type']="department";
		}
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);

		//******************TOP NAVIGATION********************
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
	
		$topNavigation=$this->m_topNavigation($this->request['owner'],$this->request['type']);
		$topNavigation.="&raquo;&nbsp;".$strNavigationLabel;
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBS", stripslashes($topNavigation));
		//****************************************************
				
		#ASSIGNING FORM ACTION						
		$this->ObTpl->set_var("FORM", SITE_URL."ecom/adminindex.php?action=ec_db.content&amp;owner=". $this->request["owner"]);

		#ASSIGNING FORM VARAIABLES

		$this->ObTpl->set_var("TPL_VAR_TITLE", $this->libFunc->m_displayContent($row_content[0]->vTitle));
		$this->ObTpl->set_var("TPL_VAR_SEOTITLE", $this->libFunc->m_displayContent($row_content[0]->vSeoTitle));
		$this->ObTpl->set_var("TPL_VAR_METATITLE", $this->libFunc->m_displayContent($row_content[0]->vMetaTitle));
		$this->ObTpl->set_var("TPL_VAR_KEYWORDS", $this->libFunc->m_displayContent($row_content[0]->tKeywords));
		$this->ObTpl->set_var("TPL_VAR_METADESC", $this->libFunc->m_displayContent($row_content[0]->tMetaDescription));
		if (is_dir($this->contentTemplatePath)) 
			{
				if ($dh = opendir($this->contentTemplatePath))
				{			
					while (($templateName = readdir($dh)) !== false) 
					{
						if($templateName!="." && $templateName!="..") {
							if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
								if($templateName==$row_content[0]->vTemplate)
								{
									$this->ObTpl->set_var("SELTEMPLATE","selected");
								}
								else
								{
									$this->ObTpl->set_var("SELTEMPLATE","");
								}
								$this->ObTpl->set_var("TPL_VAR_TEMPLATENAME",$templateName);
								$this->ObTpl->parse("template_blk","TPL_TEMPLATE_BLK",true);
							}
						}
					}
					closedir($dh);
				}
			}

			if (is_dir($this->layoutTemplatePath)) 
			{
				if ($dh = opendir($this->layoutTemplatePath))
				{			
					while (($templateName = readdir($dh)) !== false) 
					{
						if($templateName!="." && $templateName!="..") {
							if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
								if($templateName==$row_content[0]->vLayout)
								{
									$this->ObTpl->set_var("SELLAYOUT","selected");
								}
								else
								{
									$this->ObTpl->set_var("SELLAYOUT","");
								}
								$this->ObTpl->set_var("TPL_VAR_LAYOUT",$templateName);
								$this->ObTpl->parse("layout_blk","TPL_LAYOUT_BLK",true);
							}
						}
					}
					closedir($dh);
				}
			}
		if($row_content[0]->iState==1)
		{
			$this->ObTpl->set_var("TPL_VAR_STATE","checked='checked'");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_STATE","");					
		}	
		$this->ObTpl->set_var("TPL_VAR_CONTENT",$this->libFunc->m_displayContent($row_content[0]->tContent));
		$this->ObTpl->set_var("TPL_VAR_SHORTDESC",$this->libFunc->m_displayContent($row_content[0]->tShortDescription));

		#SETTING UP FCK EDITOR	
		$oFCKeditor = new CKEditor();
		$oFCKeditor->basePath = '../ckeditor/';
		$oFCKeditor->Value=$this->libFunc->m_displayCms($row_content[0]->tContent);
		$oFCKeditor->Height="300";
		$oFCKeditor->ToolbarSet="Default";
		$this->ObTpl->set_var("cmsEditor","<textarea id='TextEditor' name='content'>" . $this->libFunc->m_displayCms($row_content[0]->tContent) . "</textarea><script type='text/javascript'>CKEDITOR.replace('TextEditor');</script>");

		if(isset($this->request['msg']) && $this->request['msg']==1 && isset( $this->request['id']))
		{
			if($this->libFunc->m_checkFileExist($row_contM[0]->vImage1,"content"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_ADDIMAGE);	
			}

			if($this->libFunc->m_checkFileExist($row_contM[0]->vImage2,"content"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_ADDIMAGE);	
			}
			if($this->libFunc->m_checkFileExist($row_contM[0]->vImage3,"content"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_ADDIMAGE);	
			}
		}
		else
		{
			if($this->libFunc->m_checkFileExist($row_content[0]->vImage1,"content"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL1",LBL_ADDIMAGE);	
			}

			if($this->libFunc->m_checkFileExist($row_content[0]->vImage2,"content"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL2",LBL_ADDIMAGE);	
			}
			if($this->libFunc->m_checkFileExist($row_content[0]->vImage3,"content"))
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_EDITIMAGE);	
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_IMGLABEL3",LBL_ADDIMAGE);	
			}
		}
		return($this->ObTpl->parse("return","content"));
	}

	# MESSAGE BOX DISPLYED AFTER EVERY TRANSACTION
	function m_dspMessage()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("shopMess", $this->msgTemplate);
		$this->ObTpl->set_block("shopMess","INSERTBLK", "insert_blk");
		$this->ObTpl->set_block("shopMess", "UPDATEBLK", "update_blk");
		$this->ObTpl->set_block("shopMess", "DELETEBLK", "delete_blk");
		$this->ObTpl->set_block("DELETEBLK", "DELETEINSTANCE", "deleteinstance_blk");
		
		#INTIALIZING
		$this->ObTpl->set_var("deleteinstance_blk","");
		$this->ObTpl->set_var("update_blk","");												
		$this->ObTpl->set_var("delete_blk","");												
		$this->ObTpl->set_var("insert_blk","");		

		if(empty($this->request['owner']))
		{
			$this->request['owner']=0;
		}

		$topNavigation=$this->m_topNavigation($this->request['owner'],$this->request['type']);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBS", stripslashes($topNavigation));
	
		if(isset($this->request['msg']) && isset($this->request['owner']))
		{
			switch($this->request['msg'])
			{
				case 1:
				$status=MSG_DEPT_INSERTED;
				$this->ObTpl->parse("insert_blk","INSERTBLK");
				 $this->obDb->query = "SELECT vSeoTitle FROM ".DEPARTMENTS." WHERE iDeptid_PK='".$this->request['id']."'";
				$res = $this->obDb->fetchQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$res[0]->vSeoTitle);

				$this->ObTpl->set_var("PREVIEW_LINK",$retUrl);
				$this->ObTpl->set_var("ADD_LINK",SITE_URL."ecom/adminindex.php?action=ec_show.deptFrm&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);	$this->ObTpl->set_var("DUPLICATE_LINK",SITE_URL."ecom/adminindex.php?action=ec_show.deptFrm&amp;type=".$this->request['type']."&amp;dupeid=".$this->request['id']."&amp;owner=".$this->request['owner']);
				break;
				
				case 2:
				$status=MSG_DEPT_UPDATED;
				 $this->obDb->query = "SELECT vSeoTitle FROM ".DEPARTMENTS." WHERE iDeptid_PK='".$this->request['id']."'";
				$res = $this->obDb->fetchQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$res[0]->vSeoTitle);

				$this->ObTpl->set_var("PREVIEW_LINK",$retUrl);
				$this->ObTpl->parse("update_blk","UPDATEBLK");	
				break;

				case 3:
				$this->ObTpl->set_var("CANCEL_LINK",SITE_URL."ecom/adminindex.php?action=ec_show.home&amp;owner=".$this->request['owner']."&amp;type=".$this->request['otype']);
				if($this->request['type']=="product")	
				{	
					$status=MSG_PRODUCT_DELETED;	$this->ObTpl->set_var("DELETE_LINK",SITE_URL."ecom/adminindex.php?action=ec_db.delProduct&amp;id=".$this->request['id']."&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);		
					$this->ObTpl->set_var("DELETEINSTANCE_LINK",SITE_URL."ecom/adminindex.php?action=ec_db.delPInstance&amp;id=".$this->request['id']."&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);
					$this->ObTpl->parse("deleteinstance_blk","DELETEINSTANCE");
				}
				elseif($this->request['type']=="content")	
				{	
					$status=MSG_CONTENT_DELETED;	$this->ObTpl->set_var("DELETE_LINK",SITE_URL."ecom/adminindex.php?action=ec_db.delContent&amp;id=".$this->request['id']."&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);			
					$this->ObTpl->set_var("DELETEINSTANCE_LINK",SITE_URL."ecom/adminindex.php?action=ec_db.delCInstance&amp;id=".$this->request['id']."&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);	
					$this->ObTpl->parse("deleteinstance_blk","DELETEINSTANCE");
				}
				else
				{
					$status=MSG_DEPT_DELETED;	$this->ObTpl->set_var("DELETE_LINK",SITE_URL."ecom/adminindex.php?action=ec_db.delDept&amp;id=".$this->request['id']."&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);		
				}
				$this->ObTpl->parse("delete_blk","DELETEBLK");
				break;

				case 5:
				$status=MSG_DEPT_NOTUPDATED;		
				 $this->obDb->query = "SELECT vSeoTitle FROM ".DEPARTMENTS." WHERE iDeptid_PK='".$this->request['id']."'";
				$res = $this->obDb->fetchQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.details&mode=".$res[0]->vSeoTitle);

				$this->ObTpl->set_var("PREVIEW_LINK",$retUrl);
				$this->ObTpl->parse("update_blk","UPDATEBLK");	
				break;

				case 6:
				$status=MSG_PROD_INSERTED;
				 $this->obDb->query = "SELECT vSeoTitle FROM ".PRODUCTS." WHERE iProdid_PK='".$this->request['id']."'";
				$res = $this->obDb->fetchQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$res[0]->vSeoTitle);

				$this->ObTpl->set_var("PREVIEW_LINK",$retUrl);
				$this->ObTpl->parse("insert_blk","INSERTBLK");
				$this->ObTpl->set_var("ADD_LINK",SITE_URL."ecom/adminindex.php?action=ec_show.dspProFrm&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);	$this->ObTpl->set_var("DUPLICATE_LINK",SITE_URL."ecom/adminindex.php?action=ec_show.dspProFrm&amp;type=".$this->request['type']."&amp;dupeid=".$this->request['id']."&amp;owner=".$this->request['owner']);
				break;
				
				case 7:
				$status=MSG_PROD_UPDATED;						
				 $this->obDb->query = "SELECT vSeoTitle FROM ".PRODUCTS." WHERE iProdid_PK='".$this->request['id']."'";
				$res = $this->obDb->fetchQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$res[0]->vSeoTitle);

				$this->ObTpl->set_var("PREVIEW_LINK",$retUrl);
				$this->ObTpl->parse("update_blk","UPDATEBLK");		
				break;
				
				case 8:
				$status=MSG_CONTENT_INSERTED;
				$this->ObTpl->parse("insert_blk","INSERTBLK");
				 $this->obDb->query = "SELECT vSeoTitle FROM ".CONTENTS." WHERE iContentid_PK='".$this->request['id']."'";
				$res = $this->obDb->fetchQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$res[0]->vSeoTitle);

				$this->ObTpl->set_var("PREVIEW_LINK",$retUrl);
				$this->ObTpl->set_var("ADD_LINK",SITE_URL."ecom/adminindex.php?action=ec_show.contentFrm&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);	$this->ObTpl->set_var("DUPLICATE_LINK",SITE_URL."ecom/adminindex.php?action=ec_show.contentFrm&amp;type=".$this->request['type']."&amp;dupeid=".$this->request['id']."&amp;owner=".$this->request['owner']);
				break;

				case 9:
				$status=MSG_CONTENT_UPDATED;						
				 $this->obDb->query = "SELECT vSeoTitle FROM ".CONTENTS." WHERE iContentid_PK='".$this->request['id']."'";
				$res = $this->obDb->fetchQuery();
				$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.cdetails&mode=".$res[0]->vSeoTitle);
				$this->ObTpl->set_var("PREVIEW_LINK",$retUrl);
				$this->ObTpl->parse("update_blk","UPDATEBLK");		
				break;
				default:
				$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);	
				break;
			}

		}
		else		
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);	
		}	

		$this->ObTpl->set_var("TPL_VAR_BREDCRUMBTEXT",BREDCRUMBTEXT);
		$this->ObTpl->set_var("TPL_VAR_BREDCRUMB",SHOPBUILDER_HOME);
		
		$this->ObTpl->set_var("RETURN_LINK", SITE_URL."ecom/adminindex.php?action=ec_show.home&amp;type=".$this->request['type']."&amp;owner=".$this->request['owner']);
		$this->ObTpl->set_var("TPL_VAR_STATUS", $status);
		$this->ObTpl->set_var("TPL_STATUS_TOPTEXT",HEADINGMSG);
		
		return($this->ObTpl->parse("return","shopMess"));
	}

	#FUNCTION USED FOR UPLOADING IMAGES/FILES DURING EDIT PROCESS
	function m_uploadForm()
	{
		$obFile=new FileUpload();
		$this->ObTpl=new template();

		$this->ObTpl->set_file("Editor",$this->uploadTemplate);
		$this->ObTpl->set_block("Editor","TPL_IMAGE_BLK","image_blk");
		$this->ObTpl->set_block("Editor","TPL_FILE_BLK","file_blk");
		$this->ObTpl->set_block("Editor","TPL_FILELINK_BLK","filelink_blk");
		$this->ObTpl->set_block("Editor","TPL_CONTENTLINK_BLK","content_blk");
		
		$this->ObTpl->set_block("TPL_IMAGE_BLK","TPL_RESIZEIMAGE_BLK","resizeimage_blk");
		
		$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"");	
		$this->ObTpl->set_var("TPL_VAR_AUTH_TOKEN",$_SESSION['AUTHTOKEN2']);
		$this->ObTpl->set_var("content_blk","");	
		$this->ObTpl->set_var("file_blk","");	
		$this->ObTpl->set_var("filelink_blk","");	
		$this->ObTpl->set_var("resizeimage_blk","");
        
        
		$this->request['type']= $this->libFunc->ifSet($this->request,"type","department");


		if($this->request['type']=="product")
		{
			$this->obDb->query = "select iProdid_PK,vTitle,vImage1,tImages,vImage2,vImage3,vDownloadablefile from ".PRODUCTS." where iProdid_PK ='".$this->request['id']."'";
			$this->ObTpl->parse("filelink_blk","TPL_FILELINK_BLK");	
			$this->ObTpl->parse("content_blk","TPL_CONTENTLINK_BLK");	
			$this->imageUrl=$this->imageUrl."product/";
			$this->imagePath=$this->imagePath."product/";
		}
		elseif($this->request['type']=="content")
		{
			$this->obDb->query = "select iContentId_PK,vTitle,vImage1,vImage2,vImage3,'' as tImages from ".CONTENTS." where iContentId_PK ='".$this->request['id']."'";
			$this->ObTpl->set_var("filelink_blk","");	
			$this->imageUrl=$this->imageUrl."content/";
			$this->imagePath=$this->imagePath."content/";
		}
		else
		{
			$this->ObTpl->parse("content_blk","TPL_CONTENTLINK_BLK");	
			$this->obDb->query = "select iDeptId_PK,vTitle,vImage1,vImage2,vImage3, '' as tImages from ".DEPARTMENTS." where iDeptId_PK ='".$this->request['id']."'";
			$this->ObTpl->set_var("filelink_blk","");
			$this->imageUrl=$this->imageUrl."department/";
			$this->imagePath=$this->imagePath."department/";
		}
		$row_code = $this->obDb->fetchQuery();


		if($this->request['image']=="image1")
		{
			if(file_exists($this->imagePath.$row_code[0]->vImage1) && !empty($row_code[0]->vImage1))
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE","<img src=".$this->imageUrl.$row_code[0]->vImage1." alt='No Image' width=100 height=100>");	
				$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&type=".$this->request['type']."&image=".$this->request['image']."&delete=1>Delete</a>");		
				$this->ObTpl->set_var("imgName1","selected");
				if(isset($this->request['delete']) && $this->request['delete']==1)
				{
					$source=$this->imagePath.$row_code[0]->vImage1;
					$obFile->deleteFile($source);
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
					$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
					$this->request['msg']=1;
				}
			}
			else
			{
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
			}
			$this->ObTpl->set_var("imgName1","selected");	
			$this->ObTpl->set_var("file_blk","");	
			$this->ObTpl->parse("resizeimage_blk","TPL_RESIZEIMAGE_BLK");
			$this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");	
		}
		elseif($this->request['image']=="image2")
		{
			if(file_exists($this->imagePath.$row_code[0]->vImage2) && !empty($row_code[0]->vImage2))
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE","<img src=".$this->imageUrl.$row_code[0]->vImage2."  alt='No Image' width=100 height=100>");	
				$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&type=".$this->request['type']."&image=".$this->request['image']."&delete=1>Delete</a>");		

				if(isset($this->request['delete']) && $this->request['delete']==1)
				{
					$source=$this->imagePath.$row_code[0]->vImage2;
					$obFile->deleteFile($source);
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
					$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
					$this->request['msg']=1;
				}
			}
			else
			{
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
			}
			$this->ObTpl->set_var("imgName2","selected");
			$this->ObTpl->set_var("file_blk","");	
			$this->ObTpl->parse("resizeimage_blk","TPL_RESIZEIMAGE_BLK");
			$this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");	
		}	
		elseif($this->request['image']=="image3")
		{
			if(file_exists($this->imagePath.$row_code[0]->vImage3) && !empty($row_code[0]->vImage3))
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE","<img src=".$this->imageUrl.$row_code[0]->vImage3."  alt='No Image' width=100 height=100>");	
				$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&type=".$this->request['type']."&image=".$this->request['image']."&delete=1>Delete</a>");		
				if(isset($this->request['delete']) && $this->request['delete']==1)
				{
					$source=$this->imagePath.$row_code[0]->vImage3;
					$obFile->deleteFile($source);
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
					$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
					$this->request['msg']=1;
				}
			}
			else
			{
					$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
			}
			$this->ObTpl->set_var("imgName3","selected");
			$this->ObTpl->set_var("file_blk","");	
			$this->ObTpl->parse("resizeimage_blk","TPL_RESIZEIMAGE_BLK");
			$this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");	
		}	
		elseif($this->request['image']=="image4")
		{
			if(file_exists($this->imagePath."../files/".$row_code[0]->vDownloadablefile) && !empty($row_code[0]->vDownloadablefile))
			{
				$this->ObTpl->set_var("TPL_VAR_IMAGE","<a href=".$this->imageUrl."../files/".$row_code[0]->vDownloadablefile."  alt='Link to file'>View File</a>");		
				$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&type=".$this->request['type']."&image=".$this->request['image']."&delete=1>Delete</a>");		
				if(isset($this->request['delete']) && $this->request['delete']==1)
				{
					$source=$this->imagePath."../files/".$row_code[0]->vDownloadablefile;
					$obFile->deleteFile($source);
					$this->ObTpl->set_var("TPL_VAR_IMAGE",LBL_NOFILE);
					$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
					$this->request['msg']=1;
				}
			}
			else
			{
					$this->ObTpl->set_var("TPL_VAR_IMAGE",LBL_NOFILE);
			}
			$this->ObTpl->set_var("imgName4","selected");
			$this->ObTpl->set_var("image_blk","");	
			$this->ObTpl->parse("file_blk","TPL_FILE_BLK");	
		}	
		
		if($this->request['image']=="image4")
		{
			$imgLabel="File";
		}
		else
		{
			$imgLabel=$this->request['image'];
		}
//------
		
        $extraimages = explode(",",$row_code[0]->tImages);
		$countExtraImage = count($extraimages);
		
		for($i=0;$i<6;$i++){
			$j= $i+1;
			if($this->request['image']=="extraimage".$j){
					
					if(file_exists($this->imagePath.$extraimages[$i]) && !empty($extraimages[$i]))
					{
						$this->ObTpl->set_var("TPL_VAR_IMAGE","<img src=".$this->imageUrl.$extraimages[$i]."  alt='No Image' width=100 height=100>");		
						$this->ObTpl->set_var("TPL_VAR_DELETELINK",	"<a href=".SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&type=".$this->request['type']."&image=extraimage".$j."&delete=1>Delete</a>");		
						if(isset($this->request['delete']) && $this->request['delete']==1)
						{
							$source=$this->imagePath.$extraimages[$i];
							$obFile->deleteFile($source);
							$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
							$this->ObTpl->set_var("TPL_VAR_DELETELINK","");
							$this->request['msg']=1;
						}
					}
					else
					{
							$this->ObTpl->set_var("TPL_VAR_IMAGE",$this->libFunc->m_noImage());
					}
					$this->ObTpl->set_var("extraimage".$j,"selected");
					$this->ObTpl->set_var("file_blk","");	
					$this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");		
					}	
		}
		
		if(isset($this->request['status']))
		{
		$this->ObTpl->set_var("TPL_VAR_TOPMSG","<strong><font size='4'>".ucfirst($imgLabel)." has been Updated</font></strong>");			
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_TOPMSG","");			
		}

		if(isset($this->request['msg'])) 
		{
			if($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_TOPMSG","<span class='adminDetail'>".MSG_IMGDELETE_SUCCESS."</span>");
			}
			elseif($this->request['msg']==2)
			{
				$this->ObTpl->set_var("TPL_VAR_TOPMSG","<span class='adminDetail'>".MSG_IMGDELETE_SUCCESS."</span>");
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_TOPMSG","");
			}
		}elseif($this->err==1){

			$this->ObTpl->set_var("TPL_VAR_TOPMSG",$this->errMsg);	
		}

		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);
		$this->ObTpl->set_var("imgName",$this->request['image']);
		// [DRK]
		// Define the imgWidth and imgHeight so the user knows the size they can resample to:
		switch ($this->request['image']) {
			case "image1":
				// Small image:
				switch ($this->request['type']) {
					case "product":
						$this->ObTpl->set_var("imgWidth",  UPLOAD_SMIMAGEWIDTH);
						$this->ObTpl->set_var("imgHeight", UPLOAD_SMIMAGEHEIGHT);
						break;
					case "dept":
						$this->ObTpl->set_var("imgWidth",  UPLOAD_DEPTSMIMAGEWIDTH);
						$this->ObTpl->set_var("imgHeight", UPLOAD_DEPTSMIMAGEHEIGHT);
						break;
					case "content":
						$this->ObTpl->set_var("imgWidth",  UPLOAD_CONTENTSMIMAGEWIDTH);
						$this->ObTpl->set_var("imgHeight", UPLOAD_CONTENTSMIMAGEHEIGHT);
						break;
				}
				break;
			case "image2":
				// Medium image:
				switch ($this->request['type']) {
					case "product":
						$this->ObTpl->set_var("imgWidth",  UPLOAD_MDIMAGEWIDTH);
						$this->ObTpl->set_var("imgHeight", UPLOAD_MDIMAGEHEIGHT);
						break;
					case "dept":
						$this->ObTpl->set_var("imgWidth",  UPLOAD_DEPTMDIMAGEWIDTH);
						$this->ObTpl->set_var("imgHeight", UPLOAD_DEPTMDIMAGEHEIGHT);
						break;
				}
				break;
			case "image3":
				// Large image:
				$this->ObTpl->set_var("imgWidth",  UPLOAD_LGIMAGEWIDTH);
				$this->ObTpl->set_var("imgHeight", UPLOAD_LGIMAGEHEIGHT);
				break;
			default:
				break;
		}
		// Tell the user which image types are supported:
		$gd = gd_info();
		$tmp = array ();
		$gd['GIF Create Support'] == true ? $tmp[] = "gif" : false;
		$gd['JPEG Support'] == true ? $tmp[] = "jpg" : false;
		$gd['PNG Support'] == true ? $tmp[] = "png" : false;
		$this->ObTpl->set_var("resampleList", implode(", ", $tmp));
		// [/DRK]
		$this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);			
		$this->ObTpl->set_var("FORMURL",SITE_URL."ecom/adminindex.php?action=ec_db.uploadDeptImages&amp;id=".$this->request['id']);
		
		$this->ObTpl->pparse("return","Editor");
		exit;
	}
	
	#FUNCTION TO DELETE IMAGE
	function m_deleteImage()
	{
		$obFile=new FileUpload();

		if(!isset($this->request['id']))
		{
			echo "Error-No product selected";
			exit;
		}
		if(!isset($this->request['dir']))
		{	
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&image=".$this->request['image']."&type=".$this->request['dir']."&msg=2");
		}
		if(!isset($this->request['image']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&image=".$this->request['image']."&type=".$this->request['dir']."&msg=2");
		}
		if(!isset($this->request['name']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&image=".$this->request['image']."&type=".$this->request['dir']."&msg=2");
		}
		if($this->request['image']=="image4")
		{
			$deleteDir="files";
		}
		else
		{
			$deleteDir=$this->request['dir'];
		}

		$filename=$deleteDir."/".$this->request['name'];
		$source=$this->imagePath.$filename;
		$obFile->deleteFile($source);
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&id=".$this->request['id']."&image=".$this->request['image']."&type=".$this->request['dir']."&msg=1");
	}

	#FUNCTION TO DISPLAY ORDER LIST(RESORTING)
	function m_reorder()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_REORDER_FILE",$this->reorderTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_REORDER_FILE","TPL_ORG_SORT_BLK", "org_sort_blk");
		$this->ObTpl->set_block("TPL_REORDER_FILE","TPL_ALPHA_ASC_BLK", "alpha_asc_blk");
		$this->ObTpl->set_block("TPL_REORDER_FILE","TPL_ALPHA_DESC_BLK", "alpha_desc_blk");
		$this->ObTpl->set_block("TPL_REORDER_FILE","TPL_DATE_ASC_BLK", "date_asc_blk");
		$this->ObTpl->set_block("TPL_REORDER_FILE","TPL_DATE_DESC_BLK", "date_desc_blk");
		#INTIALIZING VARIABLES
		if(!isset($this->request['owner']))
		{
			$this->request['owner']="0";
		}
		if(!isset($this->request['type']))
		{
			$this->request['type']="department";
		}
		if(!isset($this->request['otype']))
		{
			$this->request['otype']="department";
		}
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_OWNER",$this->request['owner']);
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['otype']);



		if($this->request['type']=="product")
		{
			$query = "SELECT vTitle,fusionid FROM ".PRODUCTS." , ".FUSIONS."  WHERE iProdId_PK=iSubId_FK AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['otype']."' AND vType='".$this->request['type']."'";
		}
		elseif($this->request['type']=="content")
		{
			$query = "SELECT vTitle,fusionid  FROM ".CONTENTS.", ".FUSIONS."  WHERE  iContentid_PK=iSubId_FK AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['otype']."' AND vType='".$this->request['type']."'";
		}
		else
		{
			$query = "SELECT vTitle,fusionid FROM ".DEPARTMENTS.", ".FUSIONS."  WHERE iDeptId_PK=iSubId_FK AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['otype']."' AND vType='".$this->request['type']."'";
		}

		#ORIGINAL RESULT SET
		$query1=$query." ORDER BY iSort";
		$this->obDb->query=$query1;
		$originalResult = $this->obDb->fetchQuery();

		#COMMON RECORD COUNT
		$recordCount=$this->obDb->record_count;
		
		#ALPHA ASCENDING RESULT SET
		$query2=$query." ORDER BY vTitle";
		$this->obDb->query=$query2;
		$alpahAsenResult = $this->obDb->fetchQuery();

		#ALPHA DESCENDING RESULT SET
		$query3=$query." ORDER BY vTitle DESC";
		$this->obDb->query=$query3;
		$alpahDescResult = $this->obDb->fetchQuery();

		#DATE ASCENDING DATE SET
		$query4=$query." ORDER BY tmBuildDate";
		$this->obDb->query=$query4;
		$dateAsenResult = $this->obDb->fetchQuery();

		#DATE DESCENDING RESULT SET
		$query5=$query." ORDER BY tmBuildDate DESC";
		$this->obDb->query=$query5;
		$dateDescResult = $this->obDb->fetchQuery();

		#PARSING ORIGINAL DIV
		for($i=0;$i<$recordCount;$i++)
		{
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($originalResult[$i]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_ID",$originalResult[$i]->fusionid);
			$this->ObTpl->parse("org_sort_blk","TPL_ORG_SORT_BLK",true);
		}

		#PARSING ASCENDING ALPHABETICAL DIV
		for($j=0;$j<$recordCount;$j++)
		{
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($alpahAsenResult[$j]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_ID",$alpahAsenResult[$j]->fusionid);
			$this->ObTpl->parse("alpha_asc_blk","TPL_ALPHA_ASC_BLK",true);
		}

		#PARSING DESCENDING ALPHABETICAL DIV
		for($j=0;$j<$recordCount;$j++)
		{
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($alpahDescResult[$j]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_ID",$alpahDescResult[$j]->fusionid);
			$this->ObTpl->parse("alpha_desc_blk","TPL_ALPHA_DESC_BLK",true);
		}

		#PARSING ASCENDING DATE DIV
		for($j=0;$j<$recordCount;$j++)
		{
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($dateAsenResult[$j]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_ID",$dateAsenResult[$j]->fusionid);
			$this->ObTpl->parse("date_asc_blk","TPL_DATE_ASC_BLK",true);
		}

		#PARSING DESCENDING DATE DIV
		for($j=0;$j<$recordCount;$j++)
		{
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($dateDescResult[$j]->vTitle));
			$this->ObTpl->set_var("TPL_VAR_ID",$dateDescResult[$j]->fusionid);
			$this->ObTpl->parse("date_desc_blk","TPL_DATE_DESC_BLK",true);
		}
		return($this->ObTpl->parse("return","TPL_REORDER_FILE"));
	}


	function m_getTitle($ownerid,$type)
	{
		if($ownerid!=0)
		{
			$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".DEPARTMENTS." D ,".FUSIONS." F WHERE iDeptid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			$row = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				$_SESSION['dspTitle']=" /".$this->libFunc->m_displayContent($row[0]->vTitle).$_SESSION['dspTitle'];
				$this->m_getTitle($row[0]->iOwner_FK,$row[0]->vOwnerType);
			}
		}

			return $_SESSION['dspTitle'];
			
	}

	#FUNCTION TO DISPLAY ASSOCIATE ITEMS
	function m_associateItems()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ASSOCIATE_FILE",$this->associateTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_ASSOCIATE_FILE","TPL_DEPARTMENT_BLK", "dept_blk");
		$this->ObTpl->set_block("TPL_ASSOCIATE_FILE","TPL_ITEMS_BLK", "items_blk");
		$this->ObTpl->set_block("TPL_ASSOCIATE_FILE","TPL_MAINTABLE_BLK", "maintable_blk");
		$this->ObTpl->set_block("TPL_MAINTABLE_BLK","TPL_ATTACHED_BLK", "attached_blk");
		
		$this->ObTpl->set_var("maintable_blk","");			
		#INTIALIZING VARIABLES
		$this->request['otype']			=$this->libFunc->ifSet($this->request,"otype","department");
		$this->request['type']			=$this->libFunc->ifSet($this->request,"type","product");
		$this->request['owner']			=$this->libFunc->ifSet($this->request,"owner","0");
		$this->request['postOwner']	=$this->libFunc->ifSet($this->request,"postOwner","0");

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_OWNER",$this->request['owner']);
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);
		$this->ObTpl->set_var("TPL_VAR_OTYPE",$this->request['otype']);
		$this->ObTpl->set_var("TPL_VAR_POSTOWNER",$this->request['postOwner']);

		#START DISPLAY DEPARETMENT BLOCK
		$this->obDb->query = "SELECT vTitle,iDeptId_PK FROM ".DEPARTMENTS.", ".FUSIONS."  WHERE iDeptId_PK=iSubId_FK AND vType='department'";
		$deptResult = $this->obDb->fetchQuery();
		 $recordCount=$this->obDb->record_count;
		#PARSING DEPARTMENT BLOCK
		$this->ObTpl->set_var("SELECTED1","selected");
		if($this->request['postOwner']=="orphan"){
			$this->ObTpl->set_var("TPL_VAR_ORPHAN_SELECTED","selected");
			$this->ObTpl->set_var("SELECTED1","");
		}

		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				$_SESSION['dspTitle']="";	
				$this->ObTpl->set_var("SELECTED2","");
				$this->ObTpl->set_var("TPL_VAR_TITLE",$this->m_getTitle($deptResult[$i]->iDeptId_PK,'department'));
				$this->ObTpl->set_var("TPL_VAR_ID",$deptResult[$i]->iDeptId_PK);
				if(isset($this->request['postOwner']) && $this->request['postOwner'] == $deptResult[$i]->iDeptId_PK)
				{
					$this->ObTpl->set_var("SELECTED1","");
					$this->ObTpl->set_var("TPL_VAR_ORPHAN_SELECTED","");
					$this->ObTpl->set_var("SELECTED2","selected");
				}
			
				$this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);
			}
		}
		else
		{
			$this->ObTpl->set_var("dept_blk","");
		}
		#END DISPLAY DEPARETMENT BLOCK

		#START DISPLAY ITEM BLOCK
		#IF TYPE IS CONTENT
		if($this->request['type']=="content" && isset($this->request['postOwner']))
		{
			#IF TYPE IS CONTENT AND ORPHAN IS SELECTED
			if($this->request['postOwner']=="orphan")
			{
				 $this->obDb->query= "SELECT vTitle,fusionid,iContentid_PK FROM ".CONTENTS." LEFT JOIN ".FUSIONS." ON iContentId_PK = iSubId_FK AND vType='content'" ;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if(empty($queryResult[$j]->fusionid))
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iContentid_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
			else
			{
				#IF OTHER THAN ORPHAN
				$query = "SELECT vTitle,iContentid_PK  FROM ".CONTENTS.", ".FUSIONS."  WHERE  iContentid_PK=iSubId_FK AND iOwner_FK='".$this->request['postOwner']."' AND vOwnerType='department' AND vType='".$this->request['type']."'";
				$this->obDb->query=$query;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if($this->request['owner']!=$queryResult[$j]->iContentid_PK || $this->request['otype']!='content')
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iContentid_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
		}
		elseif($this->request['type']=="product" && isset($this->request['postOwner']))#PRODUCT
		{#FOR ORPHAN PRODUCT
			if($this->request['postOwner']=="orphan")
			{
				 $this->obDb->query= "SELECT vTitle,fusionid,iProdId_PK FROM ".PRODUCTS." LEFT JOIN ".FUSIONS." ON iProdId_PK = iSubId_FK AND vType='product'" ;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if(empty($queryResult[$j]->fusionid))
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
			else
			{#IF OTHER THAN ORPHAN
				$query = "SELECT vTitle,iProdId_PK FROM ".PRODUCTS.", ".FUSIONS."  WHERE iProdId_PK=iSubId_FK AND iOwner_FK='".$this->request['postOwner']."' AND vOwnerType='department' AND vType='".$this->request['type']."'";
				$this->obDb->query=$query;
				$queryResult = $this->obDb->fetchQuery();
				$recordCount=$this->obDb->record_count;
				if($recordCount>0)
				{
					#PARSING TPL_ITEMS_BLK
					for($j=0;$j<$recordCount;$j++)
					{
						if($this->request['owner']!=$queryResult[$j]->iProdId_PK || $this->request['otype']!='product')
						{
							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
							$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
							$this->ObTpl->parse("items_blk","TPL_ITEMS_BLK",true);
						}
					}
				}
				else
				{
						$this->ObTpl->set_var("items_blk","");
				}
			}
		}
		else
		{
			$this->ObTpl->set_var("items_blk","");
		}
		
		#TO DISPLAY CURRENTLY ATTACHED ITEMS
		if($this->request['type']=="content")
		{
			$query1 = "SELECT vTitle,fusionid,iContentid_PK FROM ".CONTENTS.", ".FUSIONS."  WHERE  iContentid_PK=iSubId_FK AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['otype']."' AND vType='".$this->request['type']."'";
			$this->obDb->query=$query1;
			$queryResult = $this->obDb->fetchQuery();
			$recordCount=$this->obDb->record_count;
			if($recordCount>0)
			{
				#PARSING TPL_ITEMS_BLK
				for($j=0;$j<$recordCount;$j++)
				{
					$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iContentid_PK);
					$this->ObTpl->set_var("TPL_VAR_FID",$queryResult[$j]->fusionid);
					$this->ObTpl->parse("attached_blk","TPL_ATTACHED_BLK",true);
				}
			$this->ObTpl->parse("maintable_blk","TPL_MAINTABLE_BLK"); //locloc
			}
			else
			{
				$this->ObTpl->set_var("attached_blk","");
			}
		}
		else
		{
			$query1 = "SELECT vTitle,vSeoTitle,iProdId_PK,fusionid FROM ".PRODUCTS.", ".FUSIONS."  WHERE iProdId_PK=iSubId_FK AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$this->request['otype']."' AND vType='".$this->request['type']."'";
			$this->obDb->query=$query1;
			$queryResult = $this->obDb->fetchQuery();
			$recordCount=$this->obDb->record_count;
			if($recordCount>0)
			{
				#PARSING TPL_ITEMS_BLK
				for($j=0;$j<$recordCount;$j++)
				{
					
					$previewUrl = SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$queryResult[$j]->vSeoTitle;
					$previewUrl = $this->libFunc->m_safeUrl($previewUrl);
					
					$this->ObTpl->set_var("TPL_VAR_PREVURL",$previewUrl);
					$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($queryResult[$j]->vTitle));
					$this->ObTpl->set_var("TPL_VAR_ID",$queryResult[$j]->iProdId_PK);
					$this->ObTpl->set_var("TPL_VAR_FID",$queryResult[$j]->fusionid);
					$this->ObTpl->parse("attached_blk","TPL_ATTACHED_BLK",true);
					$this->ObTpl->parse("maintable_blk","TPL_MAINTABLE_BLK"); //locloc
				}
			}
			else
			{
					$this->ObTpl->set_var("attached_blk","");
			}
		}#END DISPLAY CURRENTLY ATTACHED ITEMS

		return($this->ObTpl->parse("return","TPL_ASSOCIATE_FILE"));
	}
#***********************VERIFY INSERT DEPARTMENT****************************			
	


	function verifyImageUpload(){
		$this->request['image1']=$this->libFunc->ifSet($this->request,"image1","");
		$this->request['image2']=$this->libFunc->ifSet($this->request,"image2","");
		$this->request['image3']=$this->libFunc->ifSet($this->request,"image3","");
		$this->request['image4']=$this->libFunc->ifSet($this->request,"image4","");

		if(!$this->libFunc->m_validateUpload($this->request['image1'])){
			$this->errMsg=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image2'])){
			$this->errMsg=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image3'])){
			$this->errMsg=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image4'], 2)){
			$this->errMsg=MSG_VALID_FILE."<br />";
			$this->err=1;
		}
		return $this->err;
	}
	#FUNCTION TO VERIFY DATABASE UPDATION
	function verifyInsertDept()
	{
		#14-05-07
		if($this->libFunc->m_isNull($this->request['title'])){
			$this->errMsg.=MSG_TITLE_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['seo_title'])){
			$this->errMsg.=MSG_SEOTITLE_EMPTY."<br />";
			$this->err=1;
		}

		if(!$this->libFunc->m_validateUpload($this->request['image1'])){
			$this->errImg=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image2'])){
			$this->errImg=1;
		}
		
		if($this->errImg==1){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		#VALIDATING EXISTING DEPARTMENT TITLE
		 $this->obDb->query = "select iDeptId_PK from ".DEPARTMENTS." where vSeoTitle  = '".$this->libFunc->seoText($this->request['seo_title'])."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->errMsg.=MSG_TITLE_EXIST;
			$this->err=1;
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function verifyEditDept()
	{
		#14-05-07
		if($this->libFunc->m_isNull($this->request['title'])){
			$this->errMsg.=MSG_TITLE_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['seo_title'])){
			$this->errMsg.=MSG_SEOTITLE_EMPTY."<br />";
			$this->err=1;
		}

		
		if($this->errImg==1){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		#VALIDATING EXISTING DEPARTMENT TITLE
		$this->obDb->query = "select iDeptid_PK from ".DEPARTMENTS." where vSeoTitle  = '".$this->libFunc->seoText($this->request['seo_title'])."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iDeptid_PK!=$this->request['deptId'])
			{
				$this->errMsg.=MSG_TITLE_EXIST;
				$this->err=1;
			}
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function verifyInsertProduct()
	{
		#14-05-07
		if($this->libFunc->m_isNull($this->request['title'])){
			$this->errMsg.=MSG_TITLE_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['seo_title'])){
			$this->errMsg.=MSG_SEOTITLE_EMPTY."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image1'])){
			$this->errImg=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image2'])){
			$this->errImg=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image3'])){
			$this->errImg=1;
		}

		if($this->errImg==1){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['downloadable_file'], 2)){
			$this->errMsg.=MSG_VALID_FILE."<br />";
			$this->err=1;
		}
		$this->obDb->query = "select iProdId_PK from ".PRODUCTS." where vSeoTitle  = '".$this->libFunc->seoText($this->request['seo_title'])."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->errMsg.=MSG_TITLE_EXIST;
			$this->err=1;
		}
		return $this->err;
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function verifyEditProduct()
	{
		#14-05-07
		if($this->libFunc->m_isNull($this->request['title'])){
			$this->errMsg.=MSG_TITLE_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['seo_title'])){
			$this->errMsg.=MSG_SEOTITLE_EMPTY."<br />";
			$this->err=1;
		}
		
		#VALIDATING EXISTING PRODUCT TITLE
		$this->obDb->query = "select iProdid_PK from ".PRODUCTS." where vSeoTitle  = '".$this->libFunc->seoText($this->request['seo_title'])."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iProdid_PK!=$this->request['prodId'])
			{
				$this->errMsg.=MSG_TITLE_EXIST;
				$this->err=1;
			}
		}
		return $this->err;
	}
	
	function verifyInsertContent()
	{
		#14-05-07
		if($this->libFunc->m_isNull($this->request['title'])){
			$this->errMsg.=MSG_TITLE_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['seo_title'])){
			$this->errMsg.=MSG_SEOTITLE_EMPTY."<br />";
			$this->err=1;
		}
		if(!$this->libFunc->m_validateUpload($this->request['image1'])){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}

		#VALIDATING EXISTING CONTENT TITLE
		 $this->obDb->query = "select iContentid_PK from ".CONTENTS." where vSeoTitle  = '".$this->libFunc->seoText($this->request['seo_title'])."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			$this->errMsg.=MSG_TITLE_EXIST;
			$this->err=1;
		}
		return $this->err;
	}

	function verifyEditContent()
	{
		#14-05-07
		if($this->libFunc->m_isNull($this->request['title'])){
			$this->errMsg.=MSG_TITLE_EMPTY."<br />";
			$this->err=1;
		}
		if($this->libFunc->m_isNull($this->request['seo_title'])){
			$this->errMsg.=MSG_SEOTITLE_EMPTY."<br />";
			$this->err=1;
		}

		#VALIDATING EXISTING CONTENT TITLE
		$this->obDb->query = "select iContentid_PK from ".CONTENTS." where vSeoTitle  = '".$this->libFunc->seoText($this->request['seo_title'])."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iContentid_PK!=$this->request['contentId'])
			{
				$this->errMsg.=MSG_TITLE_EXIST;
				$this->err=1;
			}
		}
		return $this->err;
	}

	#FUNCTION TO DISPLAY VOLUME DISCOUNT
	function m_volDiscount()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_DISCOUNT_FILE",$this->discountTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_DISCOUNT_FILE","TPL_VOLDISC_BLK", "voldisc_blk");

		#INTIALIZING VARIABLES
		$this->request['productid']		=$this->libFunc->ifSet($this->request,"productid",0);
		$this->request['type']			=$this->libFunc->ifSet($this->request,"type",0);
		$this->request['owner']			=$this->libFunc->ifSet($this->request,"owner",0);
			
		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_PRODUCTID",$this->request['productid']);
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);
		$this->ObTpl->set_var("TPL_VAR_OWNER",$this->request['owner']);
		

		$query = "SELECT * FROM ".VDISCOUNTS." WHERE  iProductId_FK ='".$this->request['productid']."'";

		$this->obDb->query=$query;
		$rs = $this->obDb->fetchQuery();

		#COMMON RECORD COUNT
		$recordCount=$this->obDb->record_count;
		if($recordCount>0)
		{
			for($j=0;$j<$recordCount;$j++)
			{
				$this->ObTpl->set_var("TPL_VAR_ID",$rs[$j]->iDiscountId);
				$this->ObTpl->set_var("TPL_VAR_RANGEA",$rs[$j]->iRangea);
				$this->ObTpl->set_var("TPL_VAR_RANGEB",$rs[$j]->iRangeb);
				$this->ObTpl->set_var("TPL_VAR_DISCOUNT",$rs[$j]->fDiscount);
				$this->ObTpl->parse("voldisc_blk","TPL_VOLDISC_BLK",true);
			}
		}
		else
		{
			$this->ObTpl->set_var("voldisc_blk","");
		}



		return($this->ObTpl->parse("return","TPL_DISCOUNT_FILE"));
	}

	#FUNCTION TO DISPLAY ATTACHE OPTION PAGE
	function m_attachOptions()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_OPTIONS_FILE",$this->optionTemplate);

		#SETTING ALL TEMPLATE BLOCKS
		$this->ObTpl->set_block("TPL_OPTIONS_FILE","TPL_AVAILABLE_BLK", "availble_blk");
		$this->ObTpl->set_block("TPL_OPTIONS_FILE","TPL_CURRENT_BLK", "current_blk");
		#INTIALIZING VARIABLES
		$this->request['owner']=$this->libFunc->ifSet($this->request,"owner",0);
		$this->request['type']=$this->libFunc->ifSet($this->request,"type","department");
		$this->request['productid']=$this->libFunc->ifSet($this->request,"productid",1);
		$this->request['vdiscount']=$this->libFunc->ifSet($this->request,"vdiscount","0");
		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg","0");

		#SETTING TEMPLATE VARIABLE
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
		$this->ObTpl->set_var("TPL_VAR_OWNER",$this->request['owner']);
		$this->ObTpl->set_var("TPL_VAR_TYPE",$this->request['type']);
		$this->ObTpl->set_var("TPL_VAR_PRTYPE",$this->request['prtype']);
		$this->ObTpl->set_var("TPL_VAR_PRODUCTID",$this->request['productid']);
		$this->ObTpl->set_var("TPL_VAR_VDISCOUNT",$this->request['vdiscount']);

		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_OPTION_ATTACHED);	
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_OPTION_DEFAULT);
		}

		$this->obDb->query="SELECT vTitle FROM ".PRODUCTS." WHERE  iProdid_PK='".$this->request['productid']."'";
		$rsName = $this->obDb->fetchQuery();
		$this->ObTpl->set_var("TPL_PRODUCT_NAME",$this->libFunc->m_displayContent($rsName[0]->vTitle));

		if($this->request['prtype']=="choice")
		{
			$query1 = "SELECT vName,vDescription,C.iChoiceid_PK as id  FROM ".PRODUCTCHOICES." P ,".CHOICES." C WHERE iChoiceid =C.iChoiceid_PK AND iProductid_FK=".$this->request['productid']." order by iSort";;
		}
		elseif($this->request['prtype']=="option")
		{
			$query1 = "SELECT  vName,vDescription,C.iOptionid_PK as id  FROM ".PRODUCTOPTIONS." P ,".OPTIONS." C WHERE iOptionid =C.iOptionid_PK AND iProductid_FK=".$this->request['productid']." order by iSort";;
		}
		$this->obDb->query=$query1;
		$rsCur = $this->obDb->fetchQuery();
		$recordCount1=$this->obDb->record_count;
		$this->ObTpl->set_var("TPL_VAR_CRECORDS",$recordCount1);
		#PARSING CURRENT BLOCK
		$list=0;
		for($j=0;$j<$recordCount1;$j++)
		{
			$list=$list.",".$rsCur[$j]->id;
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsCur[$j]->vName));
			$this->ObTpl->set_var("TPL_VAR_DESC",$this->libFunc->m_displayContent($rsCur[$j]->vDescription));
			$this->ObTpl->set_var("TPL_VAR_ID",$rsCur[$j]->id);
			$this->ObTpl->parse("current_blk","TPL_CURRENT_BLK",true);
		}

		if($this->request['prtype']=="choice")
		{
			$query = "SELECT vName,vDescription,iChoiceid_PK as id  FROM ".CHOICES." WHERE iChoiceid_PK NOT IN ($list)" ;
		}
		elseif($this->request['prtype']=="option")
		{
			$query = "SELECT vName,vDescription,iOptionid_PK as id  FROM ".OPTIONS." WHERE iOptionid_PK NOT IN ($list) ";
		}

		$this->obDb->query=$query;
		$originalResult = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		
		#PARSING AVAILABLE 
		for($i=0;$i<$recordCount;$i++)
		{
			$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($originalResult[$i]->vName));
			$this->ObTpl->set_var("TPL_VAR_DESC",$this->libFunc->m_displayContent($originalResult[$i]->vDescription));
			$this->ObTpl->set_var("TPL_VAR_ID",$originalResult[$i]->id);
			$this->ObTpl->parse("availble_blk","TPL_AVAILABLE_BLK",true);
		}
		return($this->ObTpl->parse("return","TPL_OPTIONS_FILE"));
	}#ef
}#ec
?>