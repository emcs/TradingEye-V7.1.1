<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
include_once SITE_PATH."LanguagePacks/".LANGUAGE_PACK;
class c_menuInterface
{
    #CONSTRUCTOR
    function c_menuInterface()  {
        $this->libFunc=new c_libFunctions();
        $this->errMsg="";
        $this->err=0;
    }

    #FUNCTION TO DISPLAY MENU HEADERS
    function m_showMenuHeaders()
    {
        $this->ObTpl=new template();
        $this->ObTpl->set_file("menuHome", $this->menuHeadTemplate);

        #INTIALIZING TEMPLATE BLOCKS
        $this->ObTpl->set_block("menuHome","TPL_MAINTABLE_BLK","maintable_blk");
        $this->ObTpl->set_block("TPL_MAINTABLE_BLK","TPL_MENUHEADER_BLK","dspheadermenu_blk");
        $this->ObTpl->set_block("menuHome","TPL_MENUBTN_BLK","menubtn_blk");
        $this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
        $this->ObTpl->set_var("dspheadermenu_blk","");
        $this->ObTpl->set_var("menubtn_blk","");
        $this->ObTpl->set_var("maintable_blk","");

         #QUERY TO GET DEPARTMENTS UNDER SELECTED DEPARTMENT
        $this->obDb->query = "SELECT vHeader,iHeaderid_PK,iState,iSort  FROM ".MENUHEADERS." order by iSort";
        $resMenu=$this->obDb->fetchQuery();
        $varCount=$this->obDb->record_count;
        $this->ObTpl->set_var("TPL_VAR_COUNT",$varCount);
        
        //defining language variables
        $this->ObTpl->set_var("LANG_VAR_MENUTITLE",LANG_MENUTITLE);
        $this->ObTpl->set_var("LANG_VAR_MENUAVAILABLE",LANG_MENUAVAILABLE);
        $this->ObTpl->set_var("LANG_VAR_SORT",LANG_SORT);
        $this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
        $this->ObTpl->set_var("LANG_VAR_SUBMENUS",LANG_SUBMENU);
        $this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
        $this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
        $this->ObTpl->set_var("LANG_VAR_VIEWITEMS",LANG_VIEWITEMS);
        $this->ObTpl->set_var("LANG_VAR_APPLYCHANGES",LANG_APPLYCHANGES);
        $this->ObTpl->set_var("LANG_VAR_MENUBUILDER",LANG_MENUBUILDER);
        $this->ObTpl->set_var("LANG_VAR_ADDMENUTITLE",LANG_ADDMENUTITLE);
        
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
        
        if($varCount>0)
        {
            for($i=0;$i<$varCount;$i++)
            {
                if($resMenu[$i]->iState==1)
                {
                    $this->ObTpl->set_var("TPL_VAR_CHECKED","checked = \"checked\"");
                }
                else
                {
                    $this->ObTpl->set_var("TPL_VAR_CHECKED","");
                }   
                $this->ObTpl->set_var("TPL_HEADER_ID",  $resMenu[$i]->iHeaderid_PK);
                $this->ObTpl->set_var("TPL_SORT_NUM",   $resMenu[$i]->iSort);
                $this->ObTpl->set_var("TPL_VAR_MENUTITLE",$this->libFunc->m_displayContent($resMenu[$i]->vHeader));
                $this->ObTpl->set_var("TPL_VAR_MESSAGE","");
                $this->ObTpl->parse("dspheadermenu_blk","TPL_MENUHEADER_BLK",true); 
            }
            $this->ObTpl->parse("menubtn_blk","TPL_MENUBTN_BLK");   
            $this->ObTpl->parse("maintable_blk","TPL_MAINTABLE_BLK");
        }
        else
        {
            $this->ObTpl->set_var("TPL_VAR_MESSAGE",NOMENUHEADER);
        }

        $this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);    
        return($this->ObTpl->parse("return","menuHome"));
    }
    
    #FUNCTION TO DISPLAY MENU FORM
    function m_formMenuHeaders()
    {
        $dspImageBrowse=0;
        #INTIALIZING TEMPLATES
        $this->ObTpl=new template();
        $this->ObTpl->set_file("menuform", $this->menuFormTemplate);
        $this->ObTpl->set_block("menuform","TPL_IMAGE_BLK","image_blk");
        $this->ObTpl->set_block("menuform","TPL_LINK_BLK","link_blk");
        $this->ObTpl->set_var("image_blk","");
        $this->ObTpl->set_var("link_blk","");
        $this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
        $this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
        
        
        //defining language variables
        $this->ObTpl->set_var("LANG_VAR_MENUTITLE",LANG_MENUTITLE);
        $this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
        $this->ObTpl->set_var("LANG_VAR_MENUTITLEIMAGE",LANG_MENUIMAGE);
        $this->ObTpl->set_var("LANG_VAR_ADDMENUTITLE",LANG_ADDMENUTITLE);
        $this->ObTpl->set_var("LANG_VAR_EDITIMAGE",LANG_EDITIMAGE);
        $this->ObTpl->set_var("LANG_VAR_RETURNMENU",LANG_RETURNMENU);
        
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);

        /*CHECKING FOR POST VARIABLES
        IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
        AS USED WHEN RETURNED FROM DATABASE
        THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/
        $row_menuheader[0]->vHeader = "";
        $row_menuheader[0]->vImage = "";    
        $row_menuheader[0]->iState="";

        if(count($_POST) > 0)
        {
            if(isset($this->request["header"]))
                $row_menuheader[0]->vHeader= $this->request["header"];
            if(isset($this->request["state"]))
                $row_menuheader[0]->iState=$this->request["state"];
            $row_menuheader[0]->vImage = "";    
        }

        #IF EDIT MODE SELECTED

        if(isset($this->request['headerid']) && !empty($this->request['headerid']))
        {
            $this->obDb->query =$this->obDb->query = "SELECT vImage,vHeader,tmEditDate,iState ";
            $this->obDb->query.=" FROM ".MENUHEADERS." WHERE iHeaderid_PK='".$this->request['headerid']."'";
            $row_menuheader=$this->obDb->fetchQuery();

            if(!isset($this->request['msg']) || empty($this->request['msg']))
            {
                $this->request["tmEditDate"]=$row_menuheader[0]->tmEditDate;
                $this->request["tmFormatEditDate"]=date("M d,Y",$this->request["tmEditDate"]);
                $this->ObTpl->set_var("TPL_VAR_MSG",LBL_LAST_UPDATE.$this->request['tmFormatEditDate']);
            }
            elseif($this->request['msg']==1)
            {
                $this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);                     
            }

            if($row_menuheader[0]->iState==1)
            {
                $this->ObTpl->set_var("TPL_VAR_SELECTED","checked");
            }
            else
            {
                $this->ObTpl->set_var("TPL_VAR_SELECTED","");
            }   
            if($this->libFunc->m_checkFileExist($row_menuheader[0]->vImage,"menu"))
            {
                if(!empty($row_menuheader[0]->vImage))
                {
                    $this->ObTpl->parse("link_blk","TPL_LINK_BLK"); 
                }
                else
                {
                    $dspImageBrowse=1;
                }
            }
            else
            {
                $dspImageBrowse=1;
            }


            if($dspImageBrowse==1)
            {
                $this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");   
            }

            $this->ObTpl->set_var("TPL_VAR_MODE","edit");

            $this->ObTpl->set_var("TPL_VAR_HEADERID",$this->request['headerid']);
            $this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_EDIT_MENU);
        }   
        else #IF ADD
        {
            $this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");   
            $this->ObTpl->set_var("TPL_VAR_HEADERID","");
            $this->ObTpl->set_var("TPL_VAR_MODE","Add");
            $this->ObTpl->set_var("TPL_VAR_SELECTED","checked");
            $this->obDb->query = $this->obDb->query = "SELECT count(*) as totalCnt FROM ".MENUHEADERS;
            $row_menuheader1=$this->obDb->fetchQuery();
            if(!isset($this->request['msg']))
            {
                $this->ObTpl->set_var("TPL_VAR_MSG",LBL_TOTAL_MENUS." ".$row_menuheader1[0]->totalCnt);
            }
            elseif($this->request['msg']==1)
            {
                $this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
            }       
            
            $this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_ADD_MENU);
        }   

        #ASSIGNING FORM ACTION                      
        $this->ObTpl->set_var("TPL_VAR_FORMURL", SITE_URL."ecom/adminindex.php?action=ec_menu.menuadd");

        #ASSIGNING FORM VARAIABLES
        $this->ObTpl->set_var("TPL_VAR_MENUTITLE", $this->libFunc->m_displayContent($row_menuheader[0]->vHeader));

        return($this->ObTpl->parse("return","menuform"));
    }

    #FUNCTION USED FOR UPLOADING IMAGES/FILES DURING EDIT PROCESS
    function m_uploadForm()
    {
        $obFile         =new FileUpload();
        $this->ObTpl    =new template();
        $this->ObTpl->set_file("Editor",$this->uploadTemplate);
        $this->ObTpl->set_block("Editor","TPL_IMAGE_BLK", "image_blk");
        $this->ObTpl->set_var("TPL_VAR_DELETELINK","");
        if(!isset($this->request['id']))
        {
            echo "Sorry,Image is not available.";
            exit;
        }
        #DEFAULT TYPE
        if(!isset($this->request['type']))
        {
            $this->request['type']="menuheader";
        }

        if($this->request['type']=="menu")
        {
            $this->obDb->query =$this->obDb->query = "SELECT vImage ";
            $this->obDb->query.=" FROM ".MENUITEMS." WHERE iMenuItemsId='".$this->request['id']."'";
            $rsImage = $this->obDb->fetchQuery();
        }
        else
        {
            $this->obDb->query =$this->obDb->query = "SELECT vImage ";
            $this->obDb->query.=" FROM ".MENUHEADERS." WHERE iHeaderid_PK='".$this->request['id']."'";
            $rsImage = $this->obDb->fetchQuery();
        }

        if($this->libFunc->m_checkFileExist($rsImage[0]->vImage,"menu") && $rsImage[0]->vImage!="")
        {
                $this->ObTpl->set_var("TPL_VAR_IMAGE",
                "<img src=".$this->imageUrl."menu/".$rsImage[0]->vImage." alt='".$rsImage[0]->vImage."' width=100 height=100>");        
                $this->ObTpl->set_var("TPL_VAR_DELETELINK", "<a href=".SITE_URL."ecom/adminindex.php?action=ec_menu.uploadForm&id=".$this->request['id']."&type=".$this->request['type']."&delete=1>Delete</a>");       
                if(isset($this->request['delete']) && $this->request['delete']==1)
                {
                    $source=$this->imagePath."menu/".$rsImage[0]->vImage;
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
        
        $this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");   
        $imgLabel="image";
        if(isset($this->request['status']))
        {
        $this->ObTpl->set_var("TPL_VAR_TOPMSG","".ucfirst($imgLabel)." has been Updated");          
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
        }
        elseif($this->err==1){
            $this->ObTpl->set_var("TPL_VAR_TOPMSG","$this->errMsg");
        }


        $this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
        $this->ObTpl->set_var("TPL_VAR_ID",$this->request['id']);           
        $this->ObTpl->set_var("FORMURL",SITE_URL."ecom/adminindex.php?action=ec_menu.upload&id=".$this->request['id']."&type=".$this->request['type']);
        
        $this->ObTpl->pparse("return","Editor");
        exit;
    }

    
    
    #FUNCTION TO VERIFY DATABASE UPDATION
    function m_verifyEditMenuHeader()
    {
        #14-05-07
        if($this->libFunc->m_isNull($this->request['header'])){
            $this->errMsg.=MSG_TITLE_EMPTY."<br />";
            $this->err=1;
        }
        $this->request['image']=$this->libFunc->ifSet($this->request,"image","");
        if(!$this->libFunc->m_validateUpload($this->request['image'])){
            $this->errMsg.=MSG_VALID_IMAGE."<br />";
            $this->err=1;
        }
        #VALIDATING EXISTING DEPARTMENT TITLE
        $this->obDb->query = "select iHeaderid_PK from ".MENUHEADERS." where vHeader  = '".$this->request['header']."'";
        $row_code = $this->obDb->fetchQuery();
        if($this->obDb->record_count != 0)
        {
            if($row_code[0]->iHeaderid_PK!=$this->request['headerid'])
            {
                $this->errMsg.=MSG_HEADER_EXIST;
                $this->err=1;
            }
        }
        return $this->err;
    }

    #FUNCTION TO VERIFY DATABASE UPDATION
    function m_verifyInsertMenuHeader() {
        if($this->libFunc->m_isNull($this->request['header'])){
            $this->errMsg.=MSG_TITLE_EMPTY."<br />";
            $this->err=1;
        }
        if(!$this->libFunc->m_validateUpload($this->request['image'])){
            $this->errMsg.=MSG_VALID_IMAGE."<br />";
            $this->err=1;
        }
        $this->obDb->query = "select iHeaderid_PK from ".MENUHEADERS." where vHeader  = '".$this->request['header']."'";
        $row_code = $this->obDb->fetchQuery();
        if($this->obDb->record_count != 0)
        {
            $this->errMsg.=MSG_HEADER_EXIST;
            $this->err=1;
        }
        return $this->err;
    }

    #FUNCTION TO DISPLAY MENU ITEMS
    function m_showMenuItem()
    {
        $this->ObTpl=new template();
        $this->ObTpl->set_file("menuItem", $this->menuItemTemplate);

        #INTIALIZING TEMPLATE BLOCKS
        $this->ObTpl->set_block("menuItem","TPL_MENUBTN_BLK","menubtn_blk");
        $this->ObTpl->set_block("menuItem","TPL_MAINTABLE_BLK","maintable_blk");
        $this->ObTpl->set_block("TPL_MAINTABLE_BLK","TPL_MENUITEM_BLK","dspmenuitem_blk");

        $this->ObTpl->set_var("dspmenuitem_blk","");
        $this->ObTpl->set_var("menubtn_blk","");
        $this->ObTpl->set_var("maintable_blk","");

        $this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
        $this->ObTpl->set_var("TPL_HEADER_ID",$this->request['headerid']);

        $this->obDb->query = "SELECT vHeader  FROM ".MENUHEADERS." WHERE iHeaderid_PK=".$this->request['headerid'];
        $resTitle=$this->obDb->fetchQuery();
        $this->ObTpl->set_var("TPL_VAR_HEADERTITLE",$resTitle[0]->vHeader);

        //defining language variables
        
        $this->ObTpl->set_var("LANG_VAR_SORT",LANG_SORT);
        $this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
        $this->ObTpl->set_var("LANG_VAR_SUBMENUS",LANG_SUBMENU);
        $this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
        $this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
        $this->ObTpl->set_var("LANG_VAR_ITEMTITLE",LANG_ITEMTITLE);
        $this->ObTpl->set_var("LANG_VAR_SUBUNDERTXT",LANG_SUBMENUUNDERTXT);
        $this->ObTpl->set_var("LANG_VAR_SUBMENUBUILDER",LANG_SUBMENUBUILDER);
        $this->ObTpl->set_var("LANG_VAR_ADDSUBMENU",LANG_ADDSUBMENU);
        $this->ObTpl->set_var("LANG_VAR_RETURNMENU",LANG_RETURNMENU);
        $this->ObTpl->set_var("LANG_VAR_APPLYCHANGES",LANG_APPLYCHANGES);
        $this->ObTpl->set_var("LANG_VAR_ITEMLINK",LANG_ITEMLINK);
        $this->ObTpl->set_var("LANG_VAR_HREFATTRIBUTES",LANG_HREFATTRIBUTES);
        $this->ObTpl->set_var("LANG_VAR_ITEMIMAGE",LANG_ITEMIMAGE);
        $this->ObTpl->set_var("LANG_VAR_EDITIMAGE",LANG_EDITIMAGE);
        $this->ObTpl->set_var("LANG_VAR_MENUTITLE",LANG_MENUTITLE);
        
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
        
         #QUERY TO GET MENU ITEMS UNDER SELECTED MENU
        $this->obDb->query = "SELECT vItemtitle,iMenuItemsId,iState,iSort,vImage  FROM ".MENUITEMS." WHERE iHeaderid_FK=".$this->request['headerid']." order by iSort";
        $resMenu=$this->obDb->fetchQuery();
        $varCount=$this->obDb->record_count;
        $this->ObTpl->set_var("TPL_VAR_COUNT",$varCount);
        
        if($varCount>0)
        {
            for($i=0;$i<$varCount;$i++)
            {
                if($resMenu[$i]->iState==1)
                {
                    $this->ObTpl->set_var("TPL_VAR_CHECKED","checked = \"checked\"");
                }
                else
                {
                    $this->ObTpl->set_var("TPL_VAR_CHECKED","");
                }   
                $this->ObTpl->set_var("TPL_ITEM_ID",$resMenu[$i]->iMenuItemsId);
                $this->ObTpl->set_var("TPL_VAR_SORTNUM",$resMenu[$i]->iSort);
                $this->ObTpl->set_var("TPL_VAR_ITEMTITLE",$resMenu[$i]->vItemtitle);
                $this->ObTpl->set_var("TPL_VAR_MESSAGE","");
                $this->ObTpl->parse("dspmenuitem_blk","TPL_MENUITEM_BLK",true); 
            }
                $this->ObTpl->parse("menubtn_blk","TPL_MENUBTN_BLK");
                $this->ObTpl->parse("maintable_blk","TPL_MAINTABLE_BLK");   
        }
        else
        {
            $this->ObTpl->set_var("TPL_VAR_MESSAGE",NOMENUHEADER);
        }

        $this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);    
        return($this->ObTpl->parse("return","menuItem"));
    }
        #FUNCTION TO DISPLAY MENU FORM
    function m_formMenuItem()
    {
        $dspImageBrowse=0;
        #INTIALIZING TEMPLATES
        $this->ObTpl=new template();
        $this->ObTpl->set_file("menuform", $this->menuFormTemplate);
        $this->ObTpl->set_block("menuform","TPL_IMAGE_BLK","image_blk");
        $this->ObTpl->set_block("menuform","TPL_LINK_BLK","link_blk");
        
        $this->ObTpl->set_var("image_blk","");
        $this->ObTpl->set_var("link_blk","");
        $this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
        
        //defining language packs
        $this->ObTpl->set_var("LANG_VAR_ONOFF",LANG_ONOFF);
        $this->ObTpl->set_var("LANG_VAR_SUBMENUS",LANG_SUBMENU);
        $this->ObTpl->set_var("LANG_VAR_EDIT",LANG_EDIT);
        $this->ObTpl->set_var("LANG_VAR_DELETE",LANG_DELETE);
        $this->ObTpl->set_var("LANG_VAR_ITEMTITLE",LANG_ITEMTITLE);
        $this->ObTpl->set_var("LANG_VAR_SUBUNDERTXT",LANG_SUBMENUUNDERTXT);
        $this->ObTpl->set_var("LANG_VAR_SUBMENUBUILDER",LANG_SUBMENUBUILDER);
        $this->ObTpl->set_var("LANG_VAR_ADDSUBMENU",LANG_ADDSUBMENU);
        $this->ObTpl->set_var("LANG_VAR_RETURNMENU",LANG_RETURNMENU);
        $this->ObTpl->set_var("LANG_VAR_APPLYCHANGES",LANG_APPLYCHANGES);
        $this->ObTpl->set_var("LANG_VAR_ITEMLINK",LANG_ITEMLINK);
        $this->ObTpl->set_var("LANG_VAR_HREFATTRIBUTES",LANG_HREFATTRIBUTES);
        $this->ObTpl->set_var("LANG_VAR_ITEMIMAGE",LANG_ITEMIMAGE);
        $this->ObTpl->set_var("LANG_VAR_EDITIMAGE",LANG_EDITIMAGE);

        $this->ObTpl->set_var("TPL_SHOPURL",SITE_URL."ecom/");
        $this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
        
        /*CHECKING FOR POST VARIABLES
        IF VARIABLES ARE SET THEN ASSIGNING THEIR VALUE TO VARIABLE SAMEVARIABLE
        AS USED WHEN RETURNED FROM DATABASE
        THIS THING IS USED TO REMOVE REDUNDANCY AND USE SAME FORM FOR EDIT AND INSERT*/
        $row_menuheader[0]->vItemtitle = "";
        $row_menuheader[0]->vImage = "";    
        $row_menuheader[0]->iState="";
        $row_menuheader[0]->vHrefAttributes="";
        $row_menuheader[0]->iMethod="";
        $row_menuheader[0]->vLink="";

        if(count($_POST) > 0)
        {
            if(isset($this->request["item_title"]))
                $row_menuheader[0]->vItemtitle= $this->request["item_title"];
            if(isset($this->request["link"]))
                $row_menuheader[0]->vLink= $this->request["link"];
            if(isset($this->request["href_attributes"]))
                $row_menuheader[0]->vHrefAttributes= $this->request["href_attributes"];
            if(isset($this->request["method"]))
                $row_menuheader[0]->iMethod= $this->request["method"];    
            if(isset($this->request["state"]))
                $row_menuheader[0]->iState=$this->request["state"];
            $row_menuheader[0]->vImage = "";
        }
    
        #IF EDIT MODE SELECTED

        if(isset($this->request['itemid']) && !empty($this->request['itemid']))
        {
            $this->obDb->query = $this->obDb->query = "SELECT vItemtitle,vLink ,vHrefAttributes,iMethod,tmEditDate,iState,vImage FROM ".MENUITEMS." WHERE iMenuItemsId='".$this->request['itemid']."'";
            $row_menuheader=$this->obDb->fetchQuery();
            if(!isset($this->request['msg']) || empty($this->request['msg']))
            {           
                $this->request["tmFormatEditDate"]=date("M d,Y",$row_menuheader[0]->tmEditDate);
                $this->ObTpl->set_var("TPL_VAR_MSG",LBL_LAST_UPDATE.$this->request['tmFormatEditDate']);
            }
            elseif($this->request['msg']==1)
            {
                $this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);                     
            }
            if($row_menuheader[0]->iState==1)
            {
                $this->ObTpl->set_var("TPL_VAR_SELECTED","checked");
            }
            else
            {
                $this->ObTpl->set_var("TPL_VAR_SELECTED","");
            }   
            
              if($row_menuheader[0]->iMethod==0)
            {
                $this->ObTpl->set_var("TPL_VAR_SAMESELECTED","selected=\"selected\"");
            }
            else
            {
                $this->ObTpl->set_var("TPL_VAR_BLANKSELECTED","selected=\"selected\"");
            }   
            
            
            if($this->libFunc->m_checkFileExist($row_menuheader[0]->vImage,"menu"))
            {
                if(!empty($row_menuheader[0]->vImage))
                {
                    $this->ObTpl->parse("link_blk","TPL_LINK_BLK"); 
                }
                else
                {
                    $dspImageBrowse=1;
                }
            }
            else
            {
                $dspImageBrowse=1;
            }


            if($dspImageBrowse==1)
            {
                $this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");   
            }

            $this->ObTpl->set_var("TPL_VAR_MODE","edit");
            $this->ObTpl->set_var("TPL_VAR_ITEMID",$this->request['itemid']);
                                
            $this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_EDIT_ITEM);
        }   
        else #IF ADD
        {
            $this->ObTpl->parse("image_blk","TPL_IMAGE_BLK");   
            $this->ObTpl->set_var("TPL_VAR_ITEMID","");
            $this->ObTpl->set_var("TPL_VAR_MODE","Add");
            $this->ObTpl->set_var("TPL_VAR_SELECTED","checked");
            $this->obDb->query = $this->obDb->query = "SELECT count(*) as totalCnt FROM ".MENUITEMS." WHERE iHeaderid_FK ='".$this->request['headerid']."'";
            $row_menuheader1=$this->obDb->fetchQuery();
            if(!isset($this->request['msg']))
            {
                $this->ObTpl->set_var("TPL_VAR_MSG",LBL_TOTAL_MENUS." ".$row_menuheader1[0]->totalCnt);
            }
            elseif($this->request['msg']==1)
            {
                $this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
            }       
                $this->ObTpl->set_var("TPL_VAR_BTNMESSAGE",LBL_ADD_ITEM);
        }   


        #ASSIGNING FORM ACTION                      
        $this->ObTpl->set_var("TPL_VAR_FORMURL", SITE_URL."ecom/adminindex.php?action=ec_menu.itemadd&headerid=".$this->request['headerid']);

        #ASSIGNING FORM VARAIABLES

        $this->ObTpl->set_var("TPL_VAR_ITEMTITLE", $this->libFunc->m_displayContent($row_menuheader[0]->vItemtitle ));
        $this->ObTpl->set_var("TPL_VAR_ITEMLINK", $this->libFunc->m_displayContent($row_menuheader[0]->vLink));
        $this->ObTpl->set_var("TPL_VAR_ITEMHREF", $this->libFunc->m_displayContent($row_menuheader[0]->vHrefAttributes));
        return($this->ObTpl->parse("return","menuform"));
    }
    
    
    #FUNCTION TO VERIFY DATABASE UPDATION
    function m_verifyEditMenuItem()
    {
        #14-05-07
        if($this->libFunc->m_isNull($this->request['item_title'])){
            $this->errMsg.=MSG_TITLE_EMPTY."<br />";
            $this->err=1;
        }
        if($this->libFunc->m_isNull($this->request['link'])){
            $this->errMsg.=MSG_LINK_EMPTY."<br />";
            $this->err=1;
        }

        $this->request['image']=$this->libFunc->ifSet($this->request,"image","");
        if(!$this->libFunc->m_validateUpload($this->request['image'])){
            $this->errMsg.=MSG_VALID_IMAGE."<br />";
            $this->err=1;
        }
        #VALIDATING EXISTING DEPARTMENT TITLE
        $this->obDb->query = "select iMenuItemsId  from ".MENUITEMS." where  vItemtitle = '".$this->request['item_title']."'";
        $row_code = $this->obDb->fetchQuery();
        if($this->obDb->record_count != 0)
        {
            
            if($row_code[0]->iMenuItemsId !=$this->request['itemid'])
            {
                $this->errMsg.=MSG_ITEM_EXIST."<br />";
                $this->err=1;
            }
        }
        return $this->err;
    }
    
    #FUNCTION TO VALIDATE IMAGE UPLOADED  FROM UPLOAD FORM
    function m_verifyImageUpload(){
        if(!$this->libFunc->m_validateUpload($this->request['image'])){
            $this->errMsg.=MSG_VALID_IMAGE."<br />";
            $this->err=1;
        }
        return $this->err;
    }

    #FUNCTION TO VERIFY DATABASE UPDATION
    function m_verifyInsertMenuItem()
    {
        #14-05-07
        if($this->libFunc->m_isNull($this->request['item_title'])){
            $this->errMsg.=MSG_TITLE_EMPTY."<br />";
            $this->err=1;
        }
        if($this->libFunc->m_isNull($this->request['link'])){
            $this->errMsg.=MSG_LINK_EMPTY."<br />";
            $this->err=1;
        }
        
        if(!$this->libFunc->m_validateUpload($this->request['image'])){
            $this->errMsg.=MSG_VALID_IMAGE."<br />";
            $this->err=1;
        }

        $this->obDb->query = "select iMenuItemsId from ".MENUITEMS." where vItemtitle  = '".$this->request['item_title']."'";
        $row_code = $this->obDb->fetchQuery();
        if($this->obDb->record_count != 0)
        {
            $this->errMsg.=MSG_ITEM_EXIST."<br />";
            $this->err=1;
        }
        
        return $this->err;
    }

}
?>