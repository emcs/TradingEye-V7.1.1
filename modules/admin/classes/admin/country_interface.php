<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
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
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		//DEFINING BLOCKS
		//$this->ObTpl->set_block("TPL_COUNTRY_FILE","Message","nMessage");
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_MAINCOUNTRY_BLK","maincountry_blk");
		$this->ObTpl->set_block("TPL_MAINCOUNTRY_BLK","TPL_COUNTRY_BLK","country_blk");
		$this->ObTpl->set_block("TPL_MAINCOUNTRY_BLK","TPL_PAGING2_BLK","paging2_blk");
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_SELECTCOUNTRY_BLK","selectcountry_blk");
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_MSG_BLK","msg_blk");
		$this->ObTpl->set_block("TPL_COUNTRY_FILE","TPL_ERROR_BLK","err_blk");	

		#INTIALIZING DEFAULT VALUES
		$this->ObTpl->set_var("paging1_blk","");
		$this->ObTpl->set_var("paging2_blk","");
		$this->ObTpl->set_var("err_blk","");
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("country_blk","");
		$this->ObTpl->set_var("maincountry_blk","");
		
		$this->ObTpl->set_var("selectcountry_blk","");
		$this->ObTpl->set_var("TPL_VAR_NAME","");
		$this->ObTpl->set_var("TPL_VAR_SHORTNAME","");
		$this->ObTpl->set_var("TPL_VAR_TAX_CODE_AREA_SAGE","");
		$this->ObTpl->set_var("TPL_VAR_TAX","");
		$this->ObTpl->set_var("TPL_VAR_SHIP","");
		$this->ObTpl->set_var("TPL_VAR_SELECTED","");
		$this->ObTpl->set_var("TPL_VAR_ERROR","");
		$this->ObTpl->set_var("TPL_VAR_MESSAGE","");
		$this->ObTpl->set_var("TPL_VAR_VIEWALL","");
		$this->ObTpl->set_var("TPL_VAR_MODE","add");
		$this->ObTpl->set_var("PagerBlock1","");
		$this->ObTpl->set_var("PagerBlock2","");

		$this->request['page']=$this->libFunc->ifSet($this->request,"page");
		$this->ObTpl->set_var("EXTRASTRING","&page=".$this->request['page']);

		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",$this->errMsg);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}	
		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_COUNTRY_INSERTED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}	
		elseif($this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_COUNTRY_UPDATED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}
		elseif($this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_COUNTRY_DELETED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}
		elseif($this->request['msg']==4)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_NOCOUNTRY_DELETED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}

		//DATABASE QUERY
		$query = "SELECT * from ".COUNTRY;
		$query1=$query." ORDER BY iSortFlag,vCountryName";
		$this->obDb->query=$query1;
		$res=$this->obDb->fetchQuery();
		if($this->obDb->record_count>0)
		{
			$rCount=$this->obDb->record_count;
			for($i=0;$i<$rCount;$i++)
			{
				if(isset($this->request['cid']) && !empty($this->request['cid']) && $this->request['cid']==$res[$i]->iCountryId_PK)
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED","selected");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED","");
				}
				$this->ObTpl->set_var("TPL_VAR_CID",$res[$i]->iCountryId_PK);
				$this->ObTpl->set_var("TPL_VAR_CNAME",$this->libFunc->m_displayContent($res[$i]->vCountryName));

				$this->ObTpl->parse("selectcountry_blk","TPL_SELECTCOUNTRY_BLK",true);	
			}
		}

		if(isset($this->request['cid']) && !empty($this->request['cid']))
		{
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","Remove filter");
			$this->ObTpl->set_var("TPL_VAR_CID",$this->request['cid']);
			$this->request['cid']=intval($this->request['cid']);
			$query.=" WHERE iCountryId_PK='".$this->request['cid']."'";
			$this->obDb->query=$query;
			$res1=$this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($res1[0]->vCountryName));
			$this->ObTpl->set_var("TPL_VAR_SHORTNAME",$this->libFunc->m_displayContent($res1[0]->vShortName));
			$this->ObTpl->set_var("TPL_VAR_TAX",$res1[0]->fTax);
			$this->ObTpl->set_var("TPL_VAR_SHIP",$res1[0]->fShipCharge);
			$this->ObTpl->set_var("TPL_VAR_TAX_CODE_AREA_SAGE",$res1[0]->vSageTaxCode);

			//die($query);
		}
		if($_POST)
		{
			$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($this->request['name']));
			$this->ObTpl->set_var("TPL_VAR_SHORTNAME",$this->libFunc->m_displayContent(	$this->request['short_name']));
			$this->ObTpl->set_var("TPL_VAR_TAX",$this->request['tax']);
			$this->ObTpl->set_var("TPL_VAR_SHIP",$this->request['shipCharge']);
			$this->ObTpl->set_var("TPL_VAR_TAX_CODE_AREA_SAGE",$this->libFunc->m_displayContent($this->request['sage_code']));
		}
		$query=$query." ORDER BY iSortFlag,vCountryName";
		$pn= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$extraStr	="action=country.home";
		$pn->formno=1;
		$navArr	= $pn->create($query, $this->pageSize, $extraStr,"top");

		$pn2			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);

		$pn2->formno=2;
		$navArr2	= $pn2->create($query, $this->pageSize, $extraStr,"top");
		$res=$navArr['qryRes'];
		$totalCount=$navArr['totalRecs'];
		$rCount=$navArr['selRecs'];

		if($rCount>0)
		{
			for($i=0;$i<$rCount;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
				$this->ObTpl->set_var("TPL_VAR_CID",$res[$i]->iCountryId_PK);
				$this->ObTpl->set_var("TPL_VAR_CNAME",$this->libFunc->m_displayContent($res[$i]->vCountryName));
				$this->ObTpl->set_var("TPL_VAR_CSHORTNAME",$this->libFunc->m_displayContent($res[$i]->vShortName));
				$this->ObTpl->set_var("TPL_VAR_CTAX",$res[$i]->fTax );
				$this->ObTpl->parse("country_blk","TPL_COUNTRY_BLK",true);	
			}
			if($this->pageSize<$totalCount)
			{
				$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
				$this->ObTpl->parse("paging2_blk","TPL_PAGING2_BLK");
			}
			$this->ObTpl->parse("maincountry_blk","TPL_MAINCOUNTRY_BLK");	
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE","No country exists");
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
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