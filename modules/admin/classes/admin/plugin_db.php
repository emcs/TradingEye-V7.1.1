<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_pluginDb
{
	#CONSTRUCTOR
	function c_pluginDb()
	{
		$this->libFunc=new c_libFunctions();
		$this->pluginInterface=new pluginInterface();
	}

	function m_deactivate()
	{
		
		$plugins;
		
		$tempxml = simplexml_load_file(SITE_PATH."plugins/".$this->request['id']."/plugin.xml");
		$plugins[0][1] = (string)$tempxml->name;
		$plugins[0][2] = (string)$tempxml->description;
		$plugins[0][3] = (string)$tempxml->author;
		$plugins[0][4] = (string)$tempxml->version;
		$plugins[0][5] = (string)$tempxml->image;
		if(file_exists(SITE_PATH."plugins/".$this->request['id']."/mod.php"))
		{
			$plugins[0][6] = 1;
		}
		else
		{
			$plugins[0][6] = 0;
		}
		#INSERTING PLUGINS
		$this->obDb->query="DELETE FROM ".PLUGINS." WHERE vAppName='".$plugins[0][1]."'";
			$this->obDb->updateQuery();
			if($plugins[0][6] == 1)
			{
				$this->pluginInterface->deactivatemod($this->request['id']);
			}
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=plugin.home");	
	}
	//copys a plugin to the database. If a mod exists, it activates the mod
	function m_activate()
	{
		
		$plugins;
		
		$tempxml = simplexml_load_file(SITE_PATH."plugins/".$this->request['id']."/plugin.xml");
		$plugins[0][1] = (string)$tempxml->name;
		$plugins[0][2] = (string)$tempxml->description;
		$plugins[0][3] = (string)$tempxml->author;
		$plugins[0][4] = (string)$tempxml->version;
		$plugins[0][5] = (string)$tempxml->image;
		if(file_exists(SITE_PATH."plugins/".$this->request['id']."/mod.php"))
		{
			$plugins[0][6] = 1;
		}
		else
		{
			$plugins[0][6] = 0;
		}
		#INSERTING PLUGINS
		$this->obDb->query="INSERT INTO ".PLUGINS."
		(vAppName,vTemplate,vDescription,vVersion,iState,iMod) 
			values(
			'".$this->libFunc->m_addToDB($plugins[0][1])."',
			'".$this->libFunc->m_addToDB($this->request['id'])."',
			'".$this->libFunc->m_addToDB($plugins[0][2])."',
			'".$this->libFunc->m_addToDB($plugins[0][4])."',		
			'".$this->libFunc->m_addToDB(1)."',		
			'".$this->libFunc->m_addToDB($plugins[0][6])."')";
			$this->obDb->updateQuery();
			if($plugins[0][6] == 1)
			{
				$this->pluginInterface->activatemod($this->request['id'],$this->request['id']);
			}
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=plugin.home");	
	}

	function m_delete()
	{
		$plugins;
		
		$tempxml = simplexml_load_file(SITE_PATH."plugins/".$this->request['id']."/plugin.xml");
		$plugins[0][1] = (string)$tempxml->name;
		$plugins[0][2] = (string)$tempxml->description;
		$plugins[0][3] = (string)$tempxml->author;
		$plugins[0][4] = (string)$tempxml->version;
		$plugins[0][5] = (string)$tempxml->image;
		if(file_exists(SITE_PATH."plugins/".$plugins[0][1]."/mod.php"))
		{
			$plugins[0][6] = 1;
		}
		else
		{
			$plugins[0][6] = 0;
		}
		#INSERTING PLUGINS
		$this->obDb->query="DELETE FROM ".PLUGINS." WHERE vAppName='".$plugins[0][1]."'";
			$this->obDb->updateQuery();
			if($plugins[0][6] == 1)
			{
				$this->pluginInterface->deactivatemod($this->request['id']);
			}
			unlink(SITE_PATH."plugins/".$this->request['id']."/plugin.xml");
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=plugin.home");	
	}		
}#CLASS ENDS
?>