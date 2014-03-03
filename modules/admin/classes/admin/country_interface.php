<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;

class c_countryInterface
{
	#CONSTRUCTOR
	function c_countryInterface()
	{
		$this->pageTplPath	=MODULES_PATH."default/templates/admin/";
		$this->pageTplFile	="pager.tpl.htm";
		$this->pageSize		="20";
		$this->err				=0;
		$this->libFunc			=new c_libFunctions();
	}
	
	function m_createCountryList()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_COUNTRY_FILE", $this->countryTemplate);
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_COUNTRY_BLK","maincountry_blk");
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_ERROR_BLK","err_blk");
		
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_POSTAGE_OPTION_BLK","option_blk");
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_NEW_COUNTRY_POSTAGE_OPTIONS_BLK","new_country_option_blk");
		$this->ObTpl->set_block("TPL_COUNTRY_BLK","TPL_COUNTRY_POSTAGE_OPTIONS_BLK","country_option_blk");
		
		$this->ObTpl->set_var("maincountry_blk","");
		$this->ObTpl->set_var("option_blk","");
		$this->ObTpl->set_var("new_country_option_blk","");
		$this->ObTpl->set_var("country_option_blk","");
		$this->ObTpl->set_var("err_blk","");
		$this->ObTpl->set_var("TPL_VAR_ERROR","");
		if(isset($_SESSION['msg']))
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",$_SESSION['msg']);
			unset($_SESSION['msg']);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}
		$sort = " ORDER BY iSortFlag";
		$alpha = "";
		
		
		$this->ObTpl->set_var("TPL_VAR_SORT1","&amp;sort=1");
		$this->ObTpl->set_var("TPL_VAR_SORT2","&amp;sort=3");
		$this->ObTpl->set_var("TPL_VAR_SORT3","&amp;sort=5");
		$this->ObTpl->set_var("TPL_VAR_SORT4","&amp;sort=7");
		$this->ObTpl->set_var("TPL_VAR_SORT5","&amp;sort=9");
		$this->ObTpl->set_var("TPL_VAR_SORT6","&amp;sort=11");
		
		if(isset($this->request['sort']))
		{
			switch($this->request['sort'])
			{
				case "1":
					$sort = " ORDER BY iStatus ASC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT1","&amp;sort=2");
				break;
				case "2":
					$sort = " ORDER BY iStatus DESC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT1","&amp;sort=1");
				break;
				case "3":
					$sort = " ORDER BY vCountryName ASC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT2","&amp;sort=4");
				break;
				case "4":
					$sort = " ORDER BY vCountryName DESC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT2","&amp;sort=3");
				break;
				case "5":
					$sort = " ORDER BY vShortName ASC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT3","&amp;sort=6");
				break;
				case "6":
					$sort = " ORDER BY vShortName DESC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT3","&amp;sort=5");
				break;
				case "7":
					$sort = " ORDER BY iso3 ASC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT4","&amp;sort=8");
				break;
				case "8":
					$sort = " ORDER BY iso3 DESC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT4","&amp;sort=7");
				break;
				case "9":
					$sort = " ORDER BY fShipCharge ASC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT5","&amp;sort=10");
				break;
				case "10":
					$sort = " ORDER BY fShipCharge DESC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT5","&amp;sort=9");
				break;
				case "11":
					$sort = " ORDER BY fTax ASC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT6","&amp;sort=12");
				break;
				case "12":
					$sort = " ORDER BY fTax DESC";
					$_SESSION['countrysort'] = $sort;
					$this->ObTpl->set_var("TPL_VAR_SORT6","&amp;sort=11");
				break;
				case "13":
					if(isset($this->request['by']) && strlen($this->request['by']) === 1)
					{
						$alpha = " WHERE LEFT(vCountryName,1) = '".substr($this->request['by'],0,1)."'";
					}
				break;
			}
		}
		
			$this->ObTpl->set_var("TPL_VAR_ALPHA_SORT",'<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=a">A</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=b">B</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=c">C</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=d">D</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=e">E</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=f">F</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=g">G</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=h">H</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=i">I</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=j">J</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=k">K</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=l">L</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=m">M</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=n">N</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=o">O</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=p">P</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=q">Q</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=r">R</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=s">S</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=t">T</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=u">U</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=v">V</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=w">W</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=x">X</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=y">Y</a>
			<a href="adminindex.php?action=country.home&amp;sort=13&amp;by=z">Z</a>
			<a href="adminindex.php?action=country.home">All</a>');
			if(isset($this->request['by']))
			{
			$this->ObTpl->set_var("TPL_VAR_ALPHA_SORT2",'<script type="text/javascript">
				jQuery(".alpha > a").each(function(){
					if(jQuery(this).text().toLowerCase() == "'.$this->request['by'].'")
					{
						jQuery(this).addClass("selected");
						jQuery(this).attr("href","adminindex.php?action=country.home");
					}
				});
			</script>');
			}
			else
			{
			$this->ObTpl->set_var("TPL_VAR_ALPHA_SORT2","");
			}
		
		$this->obDb->query="SELECT * FROM ".POSTAGEDETAILS." WHERE iPostId_FK='6'";
		$postage=$this->obDb->fetchQuery();
		$postagecount = $this->obDb->record_count;
			foreach($postage as $k=>$v)
			{
				$this->ObTpl->set_var("TPL_VAR_SORT","");
				$this->ObTpl->set_var("TPL_VAR_POSTAGE_OPTION",$postage[$k]->vDescription);
				$this->ObTpl->parse("option_blk","TPL_POSTAGE_OPTION_BLK",true);	
				$this->ObTpl->set_var("TPL_VAR_POSTAGE_OPTION_ID",$postage[$k]->iPostDescId_PK);
				$this->ObTpl->parse("new_country_option_blk","TPL_NEW_COUNTRY_POSTAGE_OPTIONS_BLK",true);	
			}
		$this->obDb->query="SELECT * FROM ".COUNTRY.$alpha.$sort;
		$res=$this->obDb->fetchQuery();
		if($this->obDb->record_count>0)
		{
			foreach($res as $k=>$v)
			{
				$this->ObTpl->set_var("TPL_VAR_CID",$res[$k]->iCountryId_PK);
				$this->ObTpl->set_var("TPL_VAR_CHECKED","");
				$this->ObTpl->set_var("TPL_VAR_CHECKED3","");
				if($res[$k]->iStatus == 1)
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED"," selected='true'");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHECKED3"," selected='true'");
				}
				$this->ObTpl->set_var("TPL_VAR_NAME",$res[$k]->vCountryName);
				$this->ObTpl->set_var("TPL_VAR_SHORTNAME",$res[$k]->vShortName);
				$this->ObTpl->set_var("TPL_VAR_ISO",$res[$k]->iso3);
				$this->ObTpl->set_var("TPL_VAR_SHIP",$res[$k]->fShipCharge);
				$this->ObTpl->set_var("TPL_VAR_TAX",$res[$k]->fTax);
				$this->ObTpl->set_var("country_option_blk","");
				if($postagecount > 0)
				{
					$temparray = explode(",",$res[$k]->vShipOptions);
					foreach($postage as $k2=>$v2)
					{
						$this->ObTpl->set_var("TPL_VAR_POSTAGE_OPTION_ID",$postage[$k2]->iPostDescId_PK);
						if(in_array($postage[$k2]->iPostDescId_PK,$temparray))
						{
							$this->ObTpl->set_var("TPL_VAR_CHECKED2"," checked='true'");
						}
						else
						{
							$this->ObTpl->set_var("TPL_VAR_CHECKED2","");
						}
						$this->ObTpl->parse("country_option_blk","TPL_COUNTRY_POSTAGE_OPTIONS_BLK",true);
					}
				}
				else
				{
					$this->ObTpl->set_var("country_option_blk","");
				}
				$this->ObTpl->parse("maincountry_blk","TPL_COUNTRY_BLK",true);	
			}
		}
		return($this->ObTpl->parse("return","TPL_COUNTRY_FILE"));
	}
	
	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditCountry()
	{
		$this->errMsg="";
		if(empty($this->request['name']))
		{
			$this->err=1;
			$this->errMsg=MSG_COUNTRYNAME_EMPTY."<br>";
		}
		if(empty($this->request['short_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_SHORTCOUNTRY_EMPTY."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iCountryId_PK  FROM ".COUNTRY." where vCountryName   = '".$this->request['name']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iCountryId_PK !=$this->request['cid'])
			{
				$this->err=1;
				$this->errMsg.=MSG_COUNTRYNAME_EXIST."<br>";
			}
		}
		if($this->err==1)
		{
			return 2;
		}
		else
		{
			return 1;
		}
	}

	#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyInsertCountry()
	{
		$this->errMsg="";
		if(empty($this->request['name']))
		{
			$this->err=1;
			$this->errMsg=MSG_COUNTRYNAME_EMPTY."<br>";
		}
		if(empty($this->request['short_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_SHORTCOUNTRY_EMPTY."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iCountryId_PK  FROM ".COUNTRY." where vCountryName   = '".$this->request['name']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
				$this->err=1;
				$this->errMsg.=MSG_COUNTRYNAME_EXIST."<br>";
		}
		if($this->err==1)
		{
			return 2;
		}
		else
		{
			return 1;
		}
	}#EF
}	#EC
?>