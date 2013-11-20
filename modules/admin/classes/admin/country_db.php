<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
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
			// Inserting new record 
			$this->obDb->query = "INSERT INTO ".COUNTRY."(vCountryName,vShortName,fTax,vSageTaxCode,fShipCharge)
				values('".$this->request['name']."', '".$this->request['short_name']."', '".$this->request['tax']."', '".$this->request['sage_code']."','".$this->request['shipCharge']."')";
			$this->obDb->updateQuery();
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=country.home&msg=1");	
		}
	
		function m_updateCountry()
		{
			$this->obDb->query ="UPDATE ".COUNTRY." SET
				vCountryName		=	'".$this->request['name']."',
				fTax				=	'".$this->request['tax']."',
				iStatus			=	'".$this->request['status']."',
				vShortName			=	'".$this->request['short_name']."',
				vSageTaxCode		=	'".$this->request['sage_code']."',
				fShipCharge		=	'".$this->request['shipCharge']."'
				WHERE iCountryId_PK = '".$this->request['cid']."'";
			$this->obDb->updateQuery();
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=country.home&msg=2&cid=".$this->request['cid']);	
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
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=country.home&page=".$this->request['page']."&msg=3");	
			}#END IF
			else
			{
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=state.home&msg=4&cid=".$this->request['cid']);	
			}
		}#END FUNCTION
	}#END CLASS
?>