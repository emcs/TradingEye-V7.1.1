<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
	class c_stateDb
	{
		function c_stateDb()
		{
			$this->libFunc=new c_libFunctions();
		}
		
		function m_insertState()
		{
			// Inserting new record 
			$this->obDb->query = "INSERT INTO ".STATES."(vStateName,vShortName,iCountryID_FK,fTax,fShipCharge)
				values('".$this->libFunc->m_addToDB($this->request['name'])."', '".$this->libFunc->m_addToDB($this->request['short_name'])."', '".$this->request['cid']."', '".$this->request['tax']."','".$this->request['shipCharge']."')";
			$this->obDb->updateQuery();
			if(isset($this->request['applyall']))
			{
				$this->obDb->query ="UPDATE ".STATES." set
				fTax =	'".$this->request['tax']."',
				fShipCharge =	'".$this->request['shipCharge']."'
				where iCountryID_FK = '".$this->request['cid']."'";
				$this->obDb->updateQuery();	
			}
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=state.home&msg=1&cid=".$this->request['cid']);	
		}
	
		function m_updateState()
		{
			$libFunc=new c_libFunctions();

			$this->obDb->query ="UPDATE ".STATES." set
				vStateName =	'".		$this->libFunc->m_addToDB($this->request['name'])."',
				vShortName =	'".$this->libFunc->m_addToDB($this->request['short_name'])."',
				fTax =	'".$this->request['tax']."',
				fShipCharge =	'".$this->request['shipCharge']."'
				where iStateId_PK = '".$this->request['stateid']."'";
			$this->obDb->updateQuery();

			
			if(isset($this->request['applyall']))
			{
				$this->obDb->query = "SELECT iCountryID_FK FROM ".STATES." where iStateId_PK = '".$this->request['stateid']."'"; 
				$country_id = $this->obDb->fetchQuery();
				$cid = $country_id[0]->iCountryID_FK;

				$this->obDb->query = "SELECT iStateId_PK FROM ".STATES." where iCountryID_FK = '".$cid."'";
				$associated_states = $this->obDb->fetchQuery();
				foreach($associated_states as $k=>$v){
					$this->obDb->query ="UPDATE ".STATES." set
					fTax =	'".$this->request['tax']."'
					where iStateId_PK = '".$associated_states[$k]->iStateId_PK."'";
					$this->obDb->updateQuery();
				}

				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=state.home&msg=2&cid=".$this->request['cid']);	
			}
			else
			{
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=state.home&msg=2&cid=".$this->request['cid']."&stateid=".$this->request['stateid']);	
			}
		}
	
		function m_deleteState()
		{
			if(isset($this->request['selection']))
			{
				$selection=$this->request['selection'];
				foreach($selection as $sid)
				{
					$this->obDb->query= "UPDATE ".CUSTOMERS." SET vState = '-1' WHERE vState =".$sid;
					$this->obDb->updateQuery();
					
					$this->obDb->query = "DELETE FROM ".STATES." WHERE iStateId_PK=".$sid;
					$this->obDb->updateQuery();
				}#END FOREACH
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=state.home&page=".$this->request['page']."&msg=3&cid=".$this->request['cid']);	
			}#END IF
			else
			{
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=state.home&msg=4&cid=".$this->request['cid']);	
			}
		}#END FUNCTION
}#END CLASS
?>