<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
	class c_countryDb
	{
		function c_countryDb()
		{
			$this->libFunc=new c_libFunctions();
		}
		
		function m_insertCountry()
		{
			//NEED TO DO ENABLED POSTAGE OPTIONS TOO
			$shipoptions ="";
			if(isset($this->request['theoptions']))
			{
			foreach($this->request['theoptions'] as $k=>$v)
			{
				$shipoptions = $shipoptions . ',' . $k;
			}
			}
			$shipoptions = ltrim($shipoptions,",");
			$this->obDb->query = "INSERT INTO ".COUNTRY."(vCountryName,vShortName,fTax,iso3,iStatus,fShipCharge,vShipOptions)
				values('".$this->request['thecountry']."','".$this->request['theshortname']."','".$this->request['thevat']."','".$this->request['theiso']."','".$this->request['theonoff']."','".$this->request['thepostage']."','".$shipoptions."')";
			$this->obDb->updateQuery();
			$_SESSION['msg'] = 'Your new country has been created.';
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=country.home");	
		}
	
		function m_updateCountry()
		{
			if(isset($this->request['cname']))
			{
				foreach($this->request['cname'] as $k=>$v)
				{
					if($k != "new")
					{
						$query = "UPDATE ".COUNTRY." SET
						vCountryName = '".$this->request['cname'][$k]."',
						fTax = '".$this->request['vat'][$k]."',
						iStatus = '".$this->request['enabled'][$k]."',
						iso3 = '".$this->request['iso'][$k]."',
						vShortName = '".$this->request['sname'][$k]."',
						fShipCharge = '".$this->request['postage'][$k]."'
						WHERE iCountryId_PK = '".$k."'";
						$this->obDb->updateQuery();
					}
				}
				$_SESSION['msg'] = 'All countries were updated.';
			}
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=country.home&cid=".$this->request['cid']);	
		}
	
		function m_deleteCountry()
		{
			if(isset($this->request['selection']))
			{
				$selection=$this->request['selection'];
				foreach($selection as $cid)
				{
					#UPDATATING CUSTOMERS
					$this->obDb->query= "UPDATE ".CUSTOMERS." SET vCountry = '-1' WHERE vCountry =".$cid;
					$this->obDb->updateQuery();
				
					$this->obDb->query = "DELETE FROM ".COUNTRY." WHERE iCountryId_PK='".$cid."'";
					$this->obDb->updateQuery();
					
					$this->obDb->query = "SELECT iStateId_PK  FROM ".STATES." where iCountryId_FK   = '".$cid."'";
					$row_state = $this->obDb->fetchQuery();
					$rCount=$this->obDb->record_count;
					for($i=0;$i<$rCount;$i++)
					{
						$this->obDb->query= "UPDATE ".CUSTOMERS." SET vState = '-1' WHERE vState ='".$row_state[$i]->iStateId_PK."'";
						$this->obDb->updateQuery();
						$this->obDb->query = "DELETE FROM ".STATES." WHERE iStateId_PK='".$row_state[$i]->iStateId_PK."'" ;
						$this->obDb->updateQuery();
					}
				}#END FOREACH
				$_SESSION['msg'] = 'Selected countries were deleted.';
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=country.home&page=".$this->request['page']);	
			}#END IF
			else
			{
				$_SESSION['msg'] = 'Please choose the countries to be deleted before clicking the delete button.';
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=state.home&cid=".$this->request['cid']);
			}
		}#END FUNCTION
	}#END CLASS
?>