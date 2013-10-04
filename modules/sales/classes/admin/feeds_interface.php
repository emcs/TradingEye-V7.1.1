<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

class feed_interface
{
 	# Class constructor
	function feed_interface()
	{
		$this->libFunc=new c_libFunctions();	
		$this->err=0;
	}
	function m_dspFeedshome()
	{
		$libFunc=new c_libFunctions();
		$moduleNameArray=array();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_FEEDS_FILE", $this->Feedtemplate);
		$this->ObTpl->set_block("TPL_FEEDS_FILE","TPL_FROOGLE_BLK","froogle_blk");
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
	
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
		
		if(in_array("froogle",$moduleNameArray))
		{
		
			$this->ObTpl->parse("froogle_blk","TPL_FROOGLE_BLK");			
		}else {
			$this->ObTpl->set_var("froogle_blk","");
		}
	
		return($this->ObTpl->parse("return","TPL_FEEDS_FILE"));	
	}
	
	function m_dspProductFeed()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_RSSPROD_FILE", $this->rssProdTemplate);
		
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_PRODUCTS",RSSPRODUCT);
		
		return($this->ObTpl->parse("return","TPL_RSSPROD_FILE"));
	}
	
	function m_dspArticleFeed()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_RSSARTICLE_FILE", $this->rssArticleTemplate);
		
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_ARTICLE",RSSARTICLES);
		

		return($this->ObTpl->parse("return","TPL_RSSARTICLE_FILE"));
	}
	
	function m_updateProductFeed()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING VALUES
		//$this->request['rssproducts']=$libFunc->ifSet($this->request,'rssproducts');		
		foreach($this->request as $fieldname=>$value) {	
			if( $fieldname != 'action' ) {
				 $this->obDb->query="UPDATE ".SITESETTINGS." SET 
					nNumberdata ='".$value."' WHERE vDatatype='".$fieldname."'";	
				$this->obDb->updateQuery();
			}
		}
		$obUpdateDb=new c_shopDb();
		$obUpdateDb->obDb = $this->obDb;
		$obUpdateDb->m_RSSProductFeed();
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=feeds.home");	
	}
	
	function m_updateArticleFeed()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING VALUES
		//$this->request['rssarticles']=$libFunc->ifSet($this->request,'rssarticles');		
		foreach($this->request as $fieldname=>$value){
				if( $fieldname != 'action' ) {
				$this->obDb->query="UPDATE ".SITESETTINGS." SET 
				nNumberdata ='".$value."' WHERE vDatatype='".$fieldname."'";	
				$this->obDb->updateQuery();
			}		
		}	
		$obUpdateDb=new c_shopDb();
		$obUpdateDb->obDb = $this->obDb;
		$obUpdateDb->m_RSSArticleFeed();	
		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=feeds.home");	
	}

}# End of class	
?>