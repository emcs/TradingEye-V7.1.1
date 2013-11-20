<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_stateInterface
{
	#CONSTRUCTOR
	function c_stateInterface()
	{
		$this->pageTplPath	=MODULES_PATH."default/templates/admin/";
		$this->pageTplFile	="pager.tpl.htm";
		$this->pageSize		="20";
		$this->err				=0;
		$this->libFunc			=new c_libFunctions();
	}
	
	function m_createStateList()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_STATE_FILE", $this->countryTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		//DEFINING BLOCKS
		$this->ObTpl->set_block("TPL_STATE_FILE","TPL_MAINSTATE_BLK","mainstate_blk");
		$this->ObTpl->set_block("TPL_MAINSTATE_BLK","TPL_STATE_BLK","state_blk");
		$this->ObTpl->set_block("TPL_STATE_FILE","TPL_MSG_BLK","msg_blk");
		$this->ObTpl->set_block("TPL_STATE_FILE","TPL_ERROR_BLK","err_blk");
		$this->ObTpl->set_block("TPL_MAINSTATE_BLK","TPL_PAGING2_BLK","paging2_blk");
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("err_blk","");

		#INTIALIZING DEFAULT VALUES
		$this->ObTpl->set_var("paging1_blk","");
		$this->ObTpl->set_var("paging2_blk","");
		$this->ObTpl->set_var("state_blk","");
		$this->ObTpl->set_var("mainstate_blk","");
		$this->ObTpl->set_var("TPL_VAR_NAME","");
		$this->ObTpl->set_var("TPL_VAR_SHORTNAME","");
		$this->ObTpl->set_var("TPL_VAR_TAX","");
		$this->ObTpl->set_var("TPL_VAR_SHIP","");
		$this->ObTpl->set_var("TPL_VAR_SELECTED","");
		$this->ObTpl->set_var("TPL_VAR_ERROR","");
		$this->ObTpl->set_var("TPL_VAR_MESSAGE","");
		$this->ObTpl->set_var("TPL_VAR_VIEWALL","");
		$this->ObTpl->set_var("PagerBlock1","");
		$this->ObTpl->set_var("PagerBlock2","");

		$this->ObTpl->set_var("TPL_VAR_MODE","add");
		$this->request['msg']=$this->libFunc->ifSet($this->request,"msg");
		$this->request['page']=$this->libFunc->ifSet($this->request,"page");
		$this->request['cid']=$this->libFunc->ifSet($this->request,"cid");
		$this->request['stateid']=$this->libFunc->ifSet($this->request,"stateid");
		$this->ObTpl->set_var("TPL_VAR_CID",$this->request['cid']);
		$this->ObTpl->set_var("TPL_VAR_STATEID",$this->request['stateid']);

		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",$this->errMsg);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}	

		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_STATE_INSERTED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}	
		elseif($this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_STATE_UPDATED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}
		elseif($this->request['msg']==3)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_STATE_DELETED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}
		elseif($this->request['msg']==4)
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR",MSG_NOSTATE_DELETED);
			$this->ObTpl->parse("err_blk","TPL_ERROR_BLK");
		}

		$this->request['page']=$this->libFunc->ifSet($this->request,"page");

		$this->ObTpl->set_var("EXTRASTRING","&page=".$this->request['page']);
		
		$query = "SELECT * FROM ".STATES." WHERE iCountryID_FK='".$this->request['cid']."'";
		if($_POST)
		{
			$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($this->request['name']));
			$this->ObTpl->set_var("TPL_VAR_SHORTNAME",$this->libFunc->m_displayContent($this->request['short_name']));
			$this->ObTpl->set_var("TPL_VAR_TAX",$this->request['tax']);
			$this->ObTpl->set_var("TPL_VAR_SHIP",$this->request['shipCharge']);
		}
		elseif(isset($this->request['stateid']) && !empty($this->request['stateid']))
		{
			$this->ObTpl->set_var("TPL_VAR_MODE","edit");
			$this->ObTpl->set_var("TPL_VAR_VIEWALL","Remove filter");
			$this->request['cid']=intval($this->request['cid']);
			$query.=" AND iStateId_PK='".$this->request['stateid']."'";
			$this->obDb->query=$query;
			$res1=$this->obDb->fetchQuery();
			$this->ObTpl->set_var("TPL_VAR_NAME",$this->libFunc->m_displayContent($res1[0]->vStateName));
			$this->ObTpl->set_var("TPL_VAR_SHORTNAME",$this->libFunc->m_displayContent($res1[0]->vShortName));
			$this->ObTpl->set_var("TPL_VAR_TAX",$res1[0]->fTax);
			$this->ObTpl->set_var("TPL_VAR_SHIP",$res1[0]->fShipCharge);
		}
		

		$query.=" ORDER BY vStateName";
		$pn= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);
		$extraStr	="action=state.home&cid=".$this->request['cid'];
		$pn->formno=3;
		$navArr	= $pn->create($query,$this->pageSize,$extraStr,"top");

		$pn2			= new PrevNext($this->pageTplPath, $this->pageTplFile,$this->obDb);

		$pn2->formno=4;
		$navArr2	= $pn2->create($query, $this->pageSize, $extraStr,"top");
		$res=$navArr['qryRes'];
		$totalCount=$navArr['totalRecs'];
		$rCount=$navArr['selRecs'];

		if($rCount>0)
		{
			for($i=0;$i<$rCount;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_COUNT",$i+1);
				$this->ObTpl->set_var("TPL_VAR_SID",$res[$i]->iStateId_PK);
				$this->ObTpl->set_var("TPL_VAR_SNAME",$res[$i]->vStateName);
				$this->ObTpl->set_var("TPL_VAR_SSHORTNAME",$res[$i]->vShortName );
				$this->ObTpl->set_var("TPL_VAR_STAX",$res[$i]->fTax);
				$this->ObTpl->set_var("TPL_VAR_SHIPCHARGE",$res[$i]->fShipCharge);
				$this->ObTpl->parse("state_blk","TPL_STATE_BLK",true);	
			}
			if($this->pageSize<$totalCount)
			{
				$this->ObTpl->set_var("PagerBlock2", $navArr2['pnContents']);
				$this->ObTpl->parse("paging2_blk","TPL_PAGING2_BLK");
			}
			$this->ObTpl->parse("mainstate_blk","TPL_MAINSTATE_BLK");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MESSAGE","There are no states for this county.");
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}

		return($this->ObTpl->parse("return","TPL_STATE_FILE"));
	}

	
#FUNCTION TO VERIFY DATABASE UPDATION
	function m_verifyEditState()
	{
		$this->errMsg="";
		if(empty($this->request['name']))
		{
			$this->err=1;
			$this->errMsg=MSG_STATENAME_EMPTY."<br>";
		}
		if(empty($this->request['short_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_SHORTSTATE_EMPTY."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iStateId_PK  FROM ".STATES." where vStateName   = '".$this->request['name']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count != 0)
		{
			if($row_code[0]->iStateId_PK !=$this->request['stateid'])
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
	function m_verifyInsertState()
	{
		$this->errMsg="";
		if(empty($this->request['name']))
		{
			$this->err=1;
			$this->errMsg=MSG_STATENAME_EMPTY."<br>";
		}
		if(empty($this->request['short_name']))
		{
			$this->err=1;
			$this->errMsg.=MSG_SHORTSTATE_EMPTY."<br>";
		}
		#VALIDATING EXISTING OPTION TITLE
		$this->obDb->query = "SELECT iStateId_PK  FROM ".STATES." where vStateName   = '".$this->request['name']."'";
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
	}
}	
?>