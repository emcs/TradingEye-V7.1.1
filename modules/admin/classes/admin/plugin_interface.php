<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;

class c_pluginInterface
{
	#CONSTRUCTOR
	function c_pluginInterface()
	{
		$this->err=0;
		$this->libFunc=new c_libFunctions();
	}
	#FUNCTION TO LIST PLUGINS
	function m_dspPlugins()
	{
		#INTIALIZING TEMPLATES
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_PLUGIN_FILE", $this->pluginTemplate);
		$this->ObTpl->set_block("TPL_PLUGIN_FILE","TPL_PLUGIN_BLK", "plugin_blk");
		$this->ObTpl->set_block("TPL_PLUGIN_FILE","TPL_PLUGIN_OFF_BLK", "plugin_off_blk");
		$this->ObTpl->set_var("GRAPHICSMAINPATH",GRAPHICS_PATH);	
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("plugin_blk","");
		$this->ObTpl->set_var("plugin_off_blk","");
		$this->ObTpl->set_var("TPL_VAR_STATE","");		
		$this->ObTpl->set_var("TPL_VAR_MSG","");		
		$this->ObTpl->set_var("TPL_VAR_PLUGINPATH","");		
			
		#ASSIGNING FORM VARAIABLES

		$this->obDb->query = "SELECT *  FROM ".PLUGINS;
		$this->obDb->query.=" ORDER BY vAppName";
		$row_plugin = $this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		
		
		if($recordCount>0)
		{
			
	
			for($i=0;$i<$recordCount;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_DIRECTORY",$row_plugin[$i]->vTemplate);
				$this->ObTpl->set_var("TPL_VAR_ID",$row_plugin[$i]->iPluginid_PK);
				$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($row_plugin[$i]->vAppName));
				$this->ObTpl->set_var("TPL_VAR_DESCRIPTION",$this->libFunc->m_displayContent($row_plugin[$i]->vDescription));
				$this->ObTpl->set_var("TPL_VAR_VERSION",$this->libFunc->m_displayContent($row_plugin[$i]->vVersion));
							
				if($row_plugin[$i]->iState==1)
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","<span class=\"statusActive\">Active</span>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFF","<span class=\"statusInactive\">Off</span>");
				}
							
				if($row_plugin[$i]->iMod==1)
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFFMOD","<span class=\"statusActive\">Yes</span>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_ONOFFMOD","<span class=\"statusInactive\">No</span>");
				}		
				$this->ObTpl->set_var("TPL_VAR_PLUGINPATH",SITE_URL."plugins/".$row_plugin[$i]->vTemplate."/index.php");		
				$this->ObTpl->parse("plugin_blk","TPL_PLUGIN_BLK",true);
			}#endfor
		}#endif
		else
		{
			$this->ObTpl->parse("message_blk","TPL_MESSAGE_BLK");
			
		}
		$plugins;
		$temparray = scandir(SITE_PATH."plugins");
		foreach($temparray as $key => $value)
		{
			if(is_dir(SITE_PATH."plugins/".$value) && file_exists(SITE_PATH."plugins/".$value."/plugin.xml"))
			{
				$plugins[] = Array($value);
			}
		}
		if(isset($plugins[0][0]))
		{
			foreach($plugins as $key => $value)
			{
				if(file_exists(SITE_PATH."plugins/".$plugins[$key][0]."/plugin.xml"))
				{
					$tempxml = simplexml_load_file(SITE_PATH."plugins/".$plugins[$key][0]."/plugin.xml");
					$plugins[$key][1] = (string)$tempxml->name;
					$plugins[$key][2] = (string)$tempxml->description;
					$plugins[$key][3] = (string)$tempxml->author;
					$plugins[$key][4] = (string)$tempxml->version;
					$plugins[$key][5] = (string)$tempxml->image;
					$plugins[$key][6] = file_exists(SITE_PATH."plugins/".$plugins[$key][0]."/mod.php");
				}
			}
		//print_r($plugins);
			foreach($plugins as $key => $value)
			{
				$skip = 0;
				foreach($row_plugin as $key2 => $value2)
				{
					if($row_plugin[$key2]->vTemplate == $plugins[$key][0])
					{
						$skip = 1;
					}
				}
				if($skip == 0)
				{
					$this->ObTpl->set_var("TPL_VAR_OFF_DIRECTORY",$plugins[$key][0]);
					$this->ObTpl->set_var("TPL_VAR_OFF_NAME",$plugins[$key][1]);
					$this->ObTpl->set_var("TPL_VAR_OFF_DESCRIPTION",$plugins[$key][2]);
					$this->ObTpl->set_var("TPL_VAR_OFF_AUTHOR",$plugins[$key][3]);
					$this->ObTpl->set_var("TPL_VAR_OFF_VERSION",$plugins[$key][4]);
					$this->ObTpl->set_var("TPL_VAR_OFF_IMAGE",$plugins[$key][5]);
					$this->ObTpl->set_var("TPL_VAR_OFF_PLUGINPATH",$plugins[$key][0]);
					$this->ObTpl->set_var("TPL_VAR_OFF_ONOFF","<span class=\"statusInactive\">Off</span>");
					if($plugins[$key][6])
					{
						$this->ObTpl->set_var("TPL_VAR_OFF_ONOFFMOD","<span class=\"statusActive\">Yes</span>");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_OFF_ONOFFMOD","<span class=\"statusInactive\">No</span>");
					}
					$this->ObTpl->parse("plugin_off_blk","TPL_PLUGIN_OFF_BLK",true);
				}
			}
		}
		return($this->ObTpl->parse("return","TPL_PLUGIN_FILE"));
	}#ef
}#END CLASS
?>