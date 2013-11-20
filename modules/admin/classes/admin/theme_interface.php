<?php
defined('_TEEXEC') or die;
class c_ThemeInterface
{
	function c_ThemeInterface()
	{
		$this->libFunc=new c_libFunctions();
		$this->pluginInterface=new pluginInterface();
	}
	function m_dspThemes()
	{
		//Need to add ability to choose admin theme
		//Add functionality to detect mod file
		//Add functions to apply and remove a mod.
	
	
		//grabs all folders containing the file theme.xml
		$temparray = scandir(SITE_PATH."themes");
		//Adds Default Template
		$themes[] = Array("default","Default Theme","Tradingeye","images/theme1.gif","");
		$themesa[] = Array("default","Default Theme","Tradingeye","images/theme1a.gif","");
		foreach($temparray as $key => $value)
		{
			if(is_dir(SITE_PATH."themes/".$value) && file_exists(SITE_PATH."themes/".$value."/theme.xml"))
			{
				$themes[] = Array($value);
			}
			if(is_dir(SITE_PATH."themes/".$value) && file_exists(SITE_PATH."themes/".$value."/admin_theme.xml"))
			{
				$themesa[] = Array($value);
			}
		}
		//reads remaining info from theme.xml (name, author, picture1, picture2)
		foreach($themes as $key => $value)
		{
			if($themes[$key][0] != "default")
			{
				$tempxml = simplexml_load_file(SITE_PATH."themes/".$themes[$key][0]."/theme.xml");
				//print_r($tempxml);
				$themes[$key][1] = (string)$tempxml->name;
				$themes[$key][2] = (string)$tempxml->author;
				$themes[$key][3] = (string)$tempxml->image1;
				$themes[$key][4] = (string)$tempxml->image2;
				$themes[$key][5] = (string)$tempxml->modfile;
			}
		}
		foreach($themesa as $key => $value)
		{
			if($themesa[$key][0] != "default")
			{
				$tempxml = simplexml_load_file(SITE_PATH."themes/".$themesa[$key][0]."/admin_theme.xml");
				//print_r($tempxml);
				$themesa[$key][1] = (string)$tempxml->name;
				$themesa[$key][2] = (string)$tempxml->author;
				$themesa[$key][3] = (string)$tempxml->image1;
				$themesa[$key][4] = (string)$tempxml->image2;
				$themesa[$key][5] = (string)$tempxml->modfile;
			}
		}
		//gets the current activated template
		$this->obDb->query = "SELECT vSmallText FROM ".SITESETTINGS." WHERE vDatatype='ActiveTheme'";
		$result = $this->obDb->fetchQuery();
		if(!empty($result[0]->vSmallText))
		{
			$selected = $result[0]->vSmallText;
		}
		$this->obDb->query = "SELECT vSmallText FROM ".SITESETTINGS." WHERE vDatatype='AdminActiveTheme'";
		$result = $this->obDb->fetchQuery();
		if(!empty($result[0]->vSmallText))
		{
			$selecteda = $result[0]->vSmallText;
		}
		//displays the page
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_THEME_FILE", $this->themeTemplate);
		$this->ObTpl->set_block("TPL_THEME_FILE","TPL_THEME_BLK", "theme_blk");
		$this->ObTpl->set_block("TPL_THEME_FILE","TPL_THEMEa_BLK", "themea_blk");
		//set selected vars
		//print_r($themes);
		$this->ObTpl->set_var("theme_blk","");
		$this->ObTpl->set_var("themea_blk","");
		foreach($themes as $key => $value)
		{	
			if($selected == $themes[$key][0])
			{
				$this->ObTpl->set_var("TPL_VAR_SEL_NAME",htmlspecialchars($themes[$key][1]));
				$this->ObTpl->set_var("TPL_VAR_SEL_AUTHOR",htmlspecialchars($themes[$key][2]));
				if(isset($themes[$key][3]) && !empty($themes[$key][3]))
				{
					if($themes[$key][0] != "default")
					{
						$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE1","<img src='".SITE_URL."themes/".$themes[$key][0]."/".$themes[$key][3]."'/>");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE1","<img src='".SITE_URL.$themes[$key][3]."'/>");
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE1","");
				}
				if(isset($themes[$key][4]) && !empty($themes[$key][4]))
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE2","<img src='".SITE_URL."themes/".$themes[$key][0]."/".$themes[$key][4]."'/>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE2","");
				}
				if(isset($themes[$key][5]) && !empty($themes[$key][5]))
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_MOD","Yes");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_MOD","No");
				}
				$this->ObTpl->set_var("TPL_VAR_SEL_VALUE",$themes[$key][0]);
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_TEMP_NAME",htmlspecialchars($themes[$key][1]));
				$this->ObTpl->set_var("TPL_VAR_TEMP_AUTHOR",htmlspecialchars($themes[$key][2]));
				if(isset($themes[$key][3]) && !empty($themes[$key][3]))
				{
					if($themes[$key][0] != "default")
					{
						$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE1","<img src='".SITE_URL."themes/".$themes[$key][0]."/".$themes[$key][3]."'/>");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE1","<img src='".SITE_URL.$themes[$key][3]."'/>");
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE1","");
				}
				if(isset($themes[$key][4]) && !empty($themes[$key][4]))
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE2","<img src='".SITE_URL."themes/".$themes[$key][0]."/".$themes[$key][4]."'/>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE2","");
				}
				if(isset($themes[$key][5]) && !empty($themes[$key][5]))
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_MOD","Yes");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_MOD","No");
				}
				$this->ObTpl->set_var("TPL_VAR_TEMP_VALUE",$themes[$key][0]);
				$this->ObTpl->parse("theme_blk","TPL_THEME_BLK",true);
			}
		}
		foreach($themesa as $key => $value)
		{
			if($selecteda == $themesa[$key][0])
			{
				$this->ObTpl->set_var("TPL_VAR_SEL_NAMEa",htmlspecialchars($themesa[$key][1]));
				$this->ObTpl->set_var("TPL_VAR_SEL_AUTHORa",htmlspecialchars($themesa[$key][2]));
				if(isset($themesa[$key][3]) && !empty($themesa[$key][3]))
				{
					if($themesa[$key][0] != "default")
					{
						$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE1a","<img src='".SITE_URL."themes/".$themesa[$key][0]."/".$themesa[$key][3]."'/>");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE1a","<img src='".SITE_URL.$themesa[$key][3]."'/>");
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE1a","");
				}
				if(isset($themesa[$key][4]) && !empty($themesa[$key][4]))
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE2a","<img src='".SITE_URL."themes/".$themesa[$key][0]."/".$themesa[$key][4]."'/>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_IMAGE2a","");
				}
				if(isset($themesa[$key][5]) && !empty($themesa[$key][5]))
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_MODa","Yes");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SEL_MODa","No");
				}
				$this->ObTpl->set_var("TPL_VAR_SEL_VALUEa",$themesa[$key][0]);
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_TEMP_NAMEa",htmlspecialchars($themesa[$key][1]));
				$this->ObTpl->set_var("TPL_VAR_TEMP_AUTHORa",htmlspecialchars($themesa[$key][2]));
				if(isset($themesa[$key][3]) && !empty($themesa[$key][3]))
				{
					if($themesa[$key][0] != "default")
					{
						$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE1a","<img src='".SITE_URL."themes/".$themesa[$key][0]."/".$themesa[$key][3]."'/>");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE1a","<img src='".SITE_URL.$themesa[$key][3]."'/>");
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE1a","");
				}
				if(isset($themesa[$key][4]) && !empty($themesa[$key][4]))
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE2a","<img src='".SITE_URL."themes/".$themesa[$key][0]."/".$themesa[$key][4]."'/>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_IMAGE2a","");
				}
				if(isset($themesa[$key][5]) && !empty($themesa[$key][5]))
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_MODa","Yes");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_TEMP_MODa","No");
				}
				$this->ObTpl->set_var("TPL_VAR_TEMP_VALUEa",$themes[$key][0]);
				$this->ObTpl->parse("themea_blk","TPL_THEMEa_BLK",true);
			}
		}
		$this->ObTpl->set_var("TPL_VAR_FORM_URL",SITE_URL."admin/adminindex.php?action=themes.update");
		return($this->ObTpl->parse("return","TPL_THEME_FILE"));
	}
	function m_updateThemes()
	{
		if(isset($this->request['theme']) && !empty($this->request['theme']))
		{
			$this->obDb->query = "SELECT vSmallText FROM ".SITESETTINGS." WHERE vDatatype='ActiveTheme'";
			$result = $this->obDb->updateQuery();
			if($this->request['theme'] != "default" && isset($result[0]->vSmallText) && file_exists(SITE_PATH."plugins/activated/".$result[0]->vSmallText.".bak"))
			{
			$this->pluginInterface->deactivatemod($result[0]->vSmallText);
			}
			if($this->request['theme'] != "default" && file_exists(SITE_PATH."themes/".$this->request['theme']."/mod.php"))
			{
				$this->pluginInterface->activatemodt($this->request['theme']);
			}			
			$this->obDb->query = "UPDATE ".SITESETTINGS." SET vSmallText='".$this->request['theme']."' WHERE vDatatype='ActiveTheme'";
			$result = $this->obDb->updateQuery();
			if($this->request['theme'] == "default")
			{
				$value = SITE_URL;
				$value2 = MODULES_PATH;
			}
			else
			{
				$value = SITE_URL."themes/".$this->request['theme']."/";
				$value2 = SITE_PATH."themes/".$this->request['theme']."/";
			}
			//echo $value . "|" . $value2;
			$this->obDb->query = "UPDATE ".SITESETTINGS." SET vSmallText='".$value."' WHERE vDatatype='ThemeUrlPath'";
			$result = $this->obDb->updateQuery();
			$this->obDb->query = "UPDATE ".SITESETTINGS." SET vSmallText='".$value2."' WHERE vDatatype='ThemeRealPath'";
			$result = $this->obDb->updateQuery();
		}
		elseif(isset($this->request['atheme']) && !empty($this->request['atheme']))
		{
			$this->obDb->query = "SELECT vSmallText FROM ".SITESETTINGS." WHERE vDatatype='AdminActiveTheme'";
			$result = $this->obDb->updateQuery();
			if($this->request['atheme'] != "default")
			{
			$this->pluginInterface->deactivatemod($result[0]->vSmallText);
			}
			if($this->request['atheme'] != "default" && file_exists(SITE_PATH."themes/".$this->request['atheme']."/mod.php"))
			{
				$this->pluginInterface->activatemodt($this->request['atheme']);
			}
			$this->obDb->query = "UPDATE ".SITESETTINGS." SET vSmallText='".$this->request['atheme']."' WHERE vDatatype='AdminActiveTheme'";
			$result = $this->obDb->updateQuery();
			if($this->request['atheme'] == "default")
			{
				$value = SITE_URL;
				$value2 = MODULES_PATH;
			}
			else
			{
				$value = SITE_URL."themes/".$this->request['atheme']."/";
				$value2 = SITE_PATH."themes/".$this->request['atheme']."/";
			}
			//echo $value . "|" . $value2;
			$this->obDb->query = "UPDATE ".SITESETTINGS." SET vSmallText='".$value."' WHERE vDatatype='AdminThemeUrlPath'";
			$result = $this->obDb->updateQuery();
			$this->obDb->query = "UPDATE ".SITESETTINGS." SET vSmallText='".$value2."' WHERE vDatatype='AdminThemeRealPath'";
			$result = $this->obDb->updateQuery();
		}
		$retUrl=SITE_URL.'admin/adminindex.php?action=themes.home';
		$this->libFunc->m_mosRedirect($retUrl);
	}
}

?>