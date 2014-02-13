<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_shopDb {
	#CONSTRUCTOR
	function c_shopDb() {
		$this->errMsg = "";
		$this->libFunc = new c_libFunctions();
	}

	# FUNCTIOIN TO ADD REVIEW
	function m_reviewAdd() {
		$this->request['display'] = $this->libFunc->ifSet($this->request, "display");
		$timestamp = time();
		$this->obDb->query = "SELECT iProdid_PK FROM " . PRODUCTS . " WHERE iProdid_PK='" . $this->request['productid'] . "'";
		$rs = $this->obDb->fetchQuery();
		if($this->obDb->record_count > 0)
		{
		#inserting to departments
		$this->obDb->query = "SELECT iCustRevid_PK FROM " . REVIEWS . " WHERE iCustomerid_FK='" . $_SESSION['userid'] . "' AND iItemid_FK='" . $this->request['productid'] . "'";
		$rs = $this->obDb->fetchQuery();
		$rsCnt = $this->obDb->record_count;
		if ($rsCnt == 0) {
			$this->obDb->query = "INSERT INTO " . REVIEWS . "
										(vTitle,vComment,vRank,iDisplay,iItemid_FK,tmDateAdd,iCustomerid_FK,iState) 
											values('" . $this->libFunc->m_addToDB($this->request['title']) . "',
											'" . $this->libFunc->m_addToDB($this->request['comment']) . "',
											'" . $this->libFunc->m_addToDB($this->request['rank']) . "',
											'" . $this->request['display'] . "',
											'" . $this->request['productid'] . "',
											'$timestamp','" . $_SESSION['userid'] . "',1)";
			$this->obDb->updateQuery();
		} else {
			$this->obDb->query = "UPDATE " . REVIEWS . " SET vTitle='" . $this->libFunc->m_addToDB($this->request['title']) . "',
										vComment='" . $this->libFunc->m_addToDB($this->request['comment']) . "',
										vRank='" . $this->libFunc->m_addToDB($this->request['rank']) . "',
										iDisplay='" . $this->request['display'] . "',
										iItemid_FK='" . $this->request['productid'] . "',
										tmDateAdd='$timestamp' WHERE iCustRevid_PK='" . $rs[0]->iCustRevid_PK . "'";
			$this->obDb->updateQuery();
		}

		# additional functionality to e-mail admin on new submission of new product review - MCB, 26/09/2008
		
        $this->obDb->query="SELECT vFirstName, vLastName FROM ".CUSTOMERS. " WHERE iCustmerid_PK=".$_SESSION['userid'];
        $name_row = $this->obDb->fetchQuery();
        $customername = $name_row[0]->vFirstName." ".$name_row[0]->vLastName;
        
        $obMail = new htmlMimeMail();
		$obMail->setReturnPath(ADMIN_EMAIL);
		$obMail->setFrom(SITE_NAME . "<".ADMIN_EMAIL.">");
		$obMail->setSubject("New product review");
		$obMail->setCrlf("\n"); //to handle mails in Outlook Express
		
		$message = "Somebody has posted a new product review on your on-line store.<br>Details of this review are listed below.<br><br>";
		$message .= $customername." submitted the following at " . date('g:ia') . " on " . date('l, d F Y') . ":<br><br>";
		$message .= $this->libFunc->m_displayContent($this->request['comment']) . "<br><br>";
		$message .= "To view this comment, please visit the following URL:<br>";
		$message .= "<a href=\"".SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$this->request['mode']."\">".SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$this->request['mode']."</a>";
		
		$htmlcontent = $message;
		$txtcontent = preg_replace("/<([^>]+)>/", "", preg_replace("/<br(\/{0,1})>/", "\r\n", $message));
		
		$obMail->setHtml($htmlcontent, $txtcontent);
		$obMail->buildMessage();
		
		if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", ADMIN_EMAIL)) {
			$result = $obMail->send(array (
				ADMIN_EMAIL
			));
		}
		}
		
		# redirect on posting and e-mailing of comment ...
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $this->request['seotitle']);
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}

	function m_deleteReview() {
		$this->libFunc->obDb = $this->obDb;
		$seoTitle = $this->libFunc->m_getSeoTitle($this->request['id']);
		if(isset($_SESSION['userid']))
		{
			$this->obDb->query = "SELECT iAdminid_PK  FROM " . ADMINUSERS . " WHERE iAdminid_PK='".$_SESSION['uid']."'";
			$rs = $this->obDb->fetchQuery();
			
			if($this->obDb->record_count == 1)
			{
				$this->obDb->query = "SELECT iCustRevid_PK  FROM " . REVIEWS . " WHERE  iCustRevid_PK='" . $this->request['mode'] . "' AND iItemid_FK='" . $this->request['id'] . "'";
				$rs = $this->obDb->fetchQuery();
				$rsCnt = $this->obDb->record_count;
				if ($rsCnt != 0) {
					$this->obDb->query = "DELETE FROM " . REVIEWS . " WHERE iCustRevid_PK='" . $this->request['mode'] . "'";
					$this->obDb->updateQuery();
					$this->obDb->query = "DELETE FROM " . REVIEWHELP . " WHERE iReviewId_FK='" . $this->request['mode'] . "'";
					$this->obDb->updateQuery();
				}
			}
		}
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $seoTitle);
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}

	#FUNCTION TO ADD REVIEW HELP STATUS
	function m_reviewHelp() {
		$this->libFunc->obDb = $this->obDb;
		$seoTitle = $this->libFunc->m_getSeoTitle($this->request['id']);
		$this->obDb->query = "SELECT COUNT(*) as cnt FROM " . REVIEWHELP . " WHERE iCustId_FK='" . $_SESSION['userid'] . "' AND iReviewId_FK='" . $this->request['mode'] . "'";
		$rs = $this->obDb->fetchQuery();
		if ($rs[0]->cnt == 0) {
			$this->obDb->query = "INSERT INTO " . REVIEWHELP . " (iCustId_FK,iReviewId_FK,iStatus) VALUES('" . $_SESSION['userid'] . "','" . $this->request['mode'] . "',1)";
			$this->obDb->updateQuery();
		}
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $seoTitle);
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}

	#FUNCTION TO ADD REVIEW HELP STATUS		
	function m_reviewNoHelp() {
		$this->libFunc->obDb = $this->obDb;
		$seoTitle = $this->libFunc->m_getSeoTitle($this->request['id']);
		$this->obDb->query = "SELECT COUNT(*) as cnt FROM " . REVIEWHELP . " WHERE iCustId_FK='" . $_SESSION['userid'] . "' AND iReviewId_FK='" . $this->request['mode'] . "'";
		$rs = $this->obDb->fetchQuery();
		if ($rs[0]->cnt == 0) {
			$this->obDb->query = "INSERT INTO " . REVIEWHELP . " (iCustId_FK,iReviewId_FK,iStatus) VALUES('" . $_SESSION['userid'] . "','" . $this->request['mode'] . "',0)";
			$this->obDb->updateQuery();
		}
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.pdetails&mode=" . $seoTitle);
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}

	#FUNCTION ADD ALL ITEMS FROM INVOICE TO CART		
	function m_addInvoiceToCart() {
		# EMPTY CURRENT CART BEFORE ADDING ALL ITEMS FROM INVOICE ORDER
		$this->m_emptyCartWithoutRedirect();

		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;

		#QUERY ORDER TABLE
		$this->obDb->query = "SELECT iOrderid_PK,tmOrderDate,vPayMethod,vShipDescription,fShipTotal,";
		$this->obDb->query .= "vFirstName,vLastName,vEmail,vAddress1,vAddress2,vCity,iInvoice,";
		$this->obDb->query .= "vState,vStateName,vCountry,vZip,vCompany,vPhone,vHomepage,";
		$this->obDb->query .= "vAltName,vAltAddress1,vAltAddress2,vAltCity,vAltState,vAltCountry,";
		$this->obDb->query .= "vAltStateName,vAltZip,vAltPhone,fCodCharge,fPromoValue,";
		$this->obDb->query .= "vDiscountCode,fDiscount,iGiftcert_FK,fGiftcertTotal,fMemberPoints,";
		$this->obDb->query .= "fShipByWeightPrice,fShipByWeightKg,iSameAsBilling,";
		$this->obDb->query .= "fTaxRate,fTaxPrice,tComments,vStatus,iPayStatus,fTotalPrice,iEarnedPoints,iCustomerid_FK";
		$this->obDb->query .= " FROM " . ORDERS . " WHERE iOrderid_PK='" . $this->request['mode'] . "'";
		if (isset ($_SESSION['userid']) && !empty ($_SESSION['userid'])) {
			$this->obDb->query .= " AND iCustomerid_FK='" . $_SESSION['userid'] . "'";
		} else {
			$this->obDb->query .= " AND vEmail='" . $_SESSION['email'] . "'";
		}

		$rsOrder = $this->obDb->fetchQuery();
		$rsOrderCount = $this->obDb->record_count;
		if ($rsOrderCount != 1) {
			$errrorUrl = SITE_URL . "index.php?action=error&mode=order";
			$this->libFunc->m_mosRedirect($this->libFunc->m_safeUrl($errrorUrl));
		}

		if ($rsOrderCount > 0) {

			# SAVE INVOICE NUMBER INTO SESSION		    	
			$_SESSION['INVOICE_EDITING'] = $rsOrder[0]->iInvoice;

			$this->obDb->query = "	SELECT  iOrderid_FK, iProductid_FK, iVendorid_FK, iQty, iGiftwrapFK, fDiscount,iKit,iTaxable FROM " . ORDERPRODUCTS;
			$this->obDb->query .= " WHERE iOrderid_FK = '" . $rsOrder[0]->iOrderid_PK . "'";
			$rsProd = $this->obDb->fetchQuery();
			$rsProdCount = $this->obDb->record_count;

			for ($i = 0; $i < $rsProdCount; $i++) {

				$prodId = $rsProd[$i]->iProductid_FK;
				$vDiscoutPerItem = $comFunc->m_dspCartProductVolDiscount($rsProd[$i]->iQty);

				$this->obDb->query = "INSERT INTO " . TEMPCART . " SET ";
				$this->obDb->query .= "vSessionId	='" . SESSIONID . "',";
				$this->obDb->query .= "iProdId_FK	='" . $prodId . "',";
				$this->obDb->query .= "fVolDiscount	='" . $vDiscoutPerItem . "',";
				$this->obDb->query .= "iQty			='" . $rsProd[$i]->iQty . "'";

				$this->obDb->updateQuery();
				$cartId = $this->obDb->last_insert_id;

				#ORDERCHOICES, ORDEROPTIONS
				$this->obDb->query = " SELECT iOrderid_FK,iProductid_FK,iProductOrderid_FK,iOptionid,vName,vItem,fPrice  FROM " .
				ORDEROPTIONS . " WHERE iOrderid_FK = " . $rsOrder[0]->iOrderid_PK . " AND iProductid_FK = " . $prodId;

				$orderOptRows = $this->obDb->fetchQuery();
				$orderOptRowsCount = $this->obDb->record_count;

				if ($orderOptRowsCount > 0) {
					for ($j = 0; $j < $orderOptRowsCount; $j++) {

						$this->obDb->query = " SELECT iOptionid_FK FROM " . OPTIONVALUES . " WHERE iOptionValueid_PK = " . $orderOptRows[$j]->iOptionid;

						$optionValue = $this->obDb->fetchQuery();

						$this->obDb->query = "INSERT INTO " . TEMPOPTIONS . " SET ";
						$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
						$this->obDb->query .= "iProdId_FK='" . $prodId . "',";
						$this->obDb->query .= "iOptId_FK='" . $optionValue[0]->iOptionid_FK . "',";
						$this->obDb->query .= "vOptVal ='" . $orderOptRows[$j]->iOptionid . "'";

						$this->obDb->updateQuery();
					}
				}

				$this->obDb->query = " SELECT iOrderid_FK,iProductid_FK,iProductOrderid_FK,iChoiceid_FK,vChoiceValue,vDescription,fPrice,vType FROM " .
				ORDERCHOICES . " WHERE iOrderid_FK =" . $rsOrder[0]->iOrderid_PK . " AND iProductid_FK = " . $prodId;

				$orderChoicesRows = $this->obDb->fetchQuery();
				$orderChoicesRowsCount = $this->obDb->record_count;

				if ($orderChoicesRowsCount > 0) {
					for ($j = 0; $j < $orderChoicesRowsCount; $j++) {

						$this->obDb->query = "INSERT INTO " . TEMPCHOICES . " SET ";
						$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
						$this->obDb->query .= "iProdId_FK='" . $prodId . "',";
						$this->obDb->query .= "iTmpChoiceId_FK='" . $orderChoicesRows[$j]->iChoiceid_FK . "',";

						if ($orderChoicesRows[0]->vType == 'choiceqty') {
							$this->obDb->query .= "iQty='1',";
						}
						$this->obDb->query .= "vChoiceVal  ='" . $orderChoicesRows[$j]->vChoiceValue . "'";
						$this->obDb->updateQuery();
					}
				}
			} #END FOR					    	
		} # End of order account > 0	

		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}

	#FUNCTION ( ADD TO CART)
	function m_addTocart() {
		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;
		//ADD to basket count.
		$this->obDb->query = "SELECT iAddCount FROM " . PRODUCTS . " WHERE iProdid_PK ='".$this->request['productid']."'";
		$rowAdd = $this->obDb->fetchQuery();
		$AddCount = $rowAdd[0]->iAddCount + 1;
		$this->obDb->query = "UPDATE ".PRODUCTS." SET  iAddCount='".$AddCount."' WHERE iProdId_PK='".$this->request['productid']."'" ;
		$this->obDb->updateQuery();
		
		$this->request['productid'] = intval($this->libFunc->ifSet($this->request, "productid", 0));
		if ($this->request['productid'] < 1) {
			$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
		#FOR OPTIONS
		$prodId = $this->request['productid'];
		$comFunc->productId = $this->request['productid'];
		$this->request['qty'] = intval($this->libFunc->ifSet($this->request, "qty", 0));

		if ($this->request['qty'] < 1) {
			$this->request['qty'] = 1;
		}
		$this->m_getTotalQty($this->request['productid']);
		# Total of product quantity except the the current items quantity.
		$this->m_getTotalQty($this->request['productid'], 1);

		$pid = $this->request['productid'];
		$_SESSION['backorder'][$pid] = 0;
		#MAIN STOCK CHECK -SETTINGS FROM FEATURES
		if (STOCK_CHECK == 1) {
			#TO CHECK STOCK CONTROL ENABLED FOR PRODUCT
			if ($this->iUseinventory == 1 && !$this->is_options($this->request['productid'])) {
				$qtyAvailable = $this->iInventory - $this->totalQtyInTemp;
				if ($qtyAvailable < $this->request['qty']) {
					if ($this->iBackorder == 1) {
						$_SESSION['backorder'][$pid] = 1;
					} else {
						$this->request['qty'] = $qtyAvailable;
						$this->errMsg .= $this->libFunc->m_displayContent($this->vTitle);
					}
				}
				if ($qtyAvailable < 1 && $this->iBackorder != 1) {
					return false;
				}
			}
			$displayOptChoice = 1;
			#QUANTITY CHECK ON OPTIONS
			foreach ($_POST as $field => $fieldValue) {
				$fArray = explode('_', $field);
				$cnt = count($fArray);
				if ($cnt == 3) {
					$fieldId = $fArray[2]; #GET OPTION ID
				}
				elseif ($cnt == 4) #FOR KITS
				{
					$fieldId = $fArray[3]; #GET OPTION ID
					$prodId = $fArray[2]; #GET PROD
				}
				if ($fArray[0] == 'option') {
					if (!isset ($prodId)) {
						$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
						$this->libFunc->m_mosRedirect($retUrl);
						exit;
					}
					$prodoptionarray[$this->request['productid']][] = Array($fieldId,$fieldValue);
					if ($this->request['productid'] == $prodId) {
						$qtyAvailable = $this->m_getOptionQty($prodId, $fieldValue);
						#TO CHECK STOCK CONTROL ENABLED
						if ($this->iUseinventory == 1) {
							if ($qtyAvailable < $this->request['qty']) {
								if ($this->iBackorder == 1) {
									$_SESSION['backorder'][$pid] = 1;
								} else {
									$this->request['qty'] = $qtyAvailable;

									$this->errMsg .= "<li>" . $this->libFunc->m_displayContent($this->vTitle) . " - option(" . $this->libFunc->m_displayContent($this->vOptTitle) . ")</li>";
								}
							}
						}
					}

				}
				if ($fArray[0] == 'choiceqty' && !empty ($fieldValue)) {
					$prodchoicearray[$this->request['productid']][] = Array($fieldId,$fieldValue);
					if ($this->request['productid'] == $prodId) {
						$qtyAvailable = $this->m_getChoiceQty($prodId, $fieldId);
						#TO CHECK STOCK CONTROL ENABLED
						if ($this->iUseinventory == 1) {
							if ($qtyAvailable < $fieldValue) {
								if ($this->iBackorder == 1) {
									$_SESSION['backorder'][$pid] = 1;
								} else {
									$this->request['qty'] = $qtyAvailable;
									$this->errMsg .= "<li>" . $this->vTitle . " - choice(" . $this->vOptTitle . ")</li>";
								}
							}
						}
					}
				}
			}
			if ($this->iBackorder != 1 && !empty ($this->errMsg)) {
				return false;
			}
			if ($this->request['qty'] < 1) {
				$this->request['qty'] = 1;
			}
		} #END MAIN STOCK CHECK
		
		$this->obDb->query = "SELECT iKit FROM " . PRODUCTS . " WHERE iProdid_PK ='".$this->request['productid']."'";
		$KitStatus = $this->obDb->fetchQuery();
		if($KitStatus[0]->iKit == "1"){
		
			$this->obDb->query = "SELECT iKitId_PK,iProdId_FK,iQty FROM " . PRODUCTKITS . " WHERE iKitId ='".$this->request['productid']."'";
			$CheckPackage = $this->obDb->fetchQuery();
			if($CheckPackage[0]->iKitId_PK != ""){
				$this->CheckPackageProducts($CheckPackage);

				if (!empty ($this->errMsg)) {
					return false;
				}
				$this->AddPackageTemp($CheckPackage); 
				$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
				$this->libFunc->m_mosRedirect($retUrl);
				exit;
			}
		}

		#INSERTING TO TEMPEREARY CART
		$this->obDb->query = "SELECT aa.iProdId_FK,aa.iTmpCartId_PK,aa.iQty,bb.iTmpChoiceId_FK,bb.vChoiceVal,cc.iOptId_FK,cc.iTempOptId_PK,cc.vOptVal FROM ".TEMPCART." as aa INNER JOIN (".TEMPCHOICES." as bb) on aa.iTmpCartId_PK = bb.iTmpCartId_FK AND aa.iProdId_FK=bb.iProdId_FK INNER JOIN (".TEMPOPTIONS." as cc) on aa.iTmpCartId_PK = cc.iTmpCartId_FK AND aa.iProdId_FK=cc.iProdId_FK WHERE aa.vSessionId='" . SESSIONID . "' AND aa.iProdId_Fk='".$this->request['productid']."'";
		//if added product has choice
		if(count($prodchoicearray[$this->request['productid']]) > 0)
		{
			FOREACH($prodchoicearray[$this->request['productid']] as $key => $value)
			{
				if($key == 0)
				{
					$this->obDb->query = $this->obDb->query . " AND bb.vChoiceVal='".$prodchoicearray[$this->request['productid']][$key][1]."'";
				}
				else
				{
				$this->obDb->query = $this->obDb->query . " OR bb.vChoiceVal='".$prodchoicearray[$this->request['productid']][$key][1]."' AND aa.vSessionId='" . SESSIONID . "' AND aa.iProdId_Fk='".$this->request['productid']."'";
				}
			}
		}
		//if added product has options
		if(count($prodoptionarray[$this->request['productid']]) > 0)
		{
			
			
			FOREACH($prodoptionarray[$this->request['productid']] as $key => $value)
			{
				if($key == 0)
				{
					if(!empty($prodoptionarray[$this->request['productid']][$key][1]))
					{
						$this->obDb->query = $this->obDb->query . " AND cc.iOptId_FK=".$prodoptionarray[$this->request['productid']][$key][0]." AND cc.vOptVal=".$prodoptionarray[$this->request['productid']][$key][1];
					}
				}
				else
				{
					if(!empty($prodoptionarray[$this->request['productid']][$key][1]))
					{
						$this->obDb->query = $this->obDb->query . " OR cc.iOptId_FK=".$prodoptionarray[$this->request['productid']][$key][0]." AND cc.vOptVal=".$prodoptionarray[$this->request['productid']][$key][1]." AND aa.vSessionId='" . SESSIONID . "' AND aa.iProdId_Fk='".$this->request['productid']."'";
					}
				}
			}
		}
		//$this->obDb->query = "SELECT iTmpCartId_PK  FROM " . TEMPCART;
		//$this->obDb->query .= " WHERE vSessionId ='" . SESSIONID . "'";
		//$this->obDb->query .= " AND iProdId_FK='" . $this->request['productid'] . "'";
		//echo $this->obDb->query;
		$rs = $this->obDb->fetchQuery();
		$rsCnt = $this->obDb->record_count;
		//if($rsCnt >0 && $rsCnt == (count($prodoptionarray[$this->request['productid']]) + count($prodchoicearray[$this->request['productid']])))
		if($rsCnt >0)
		{
			$theqty =$this->request['qty'] + $rs[0]->iQty;
			$this->obDb->query = "UPDATE ".TEMPCART." SET iQty='".$theqty."' WHERE iTmpCartId_PK ='" . $rs[0]->iTmpCartId_PK . "'";
			$this->obDb->updateQuery();
		}
		else
		{
		$vDiscoutPerItem = $comFunc->m_dspCartProductVolDiscount($this->request['qty']);
		$this->obDb->query = "INSERT INTO " . TEMPCART . " SET ";
		$this->obDb->query .= "vSessionId	='" . SESSIONID . "',";
		$this->obDb->query .= "iProdId_FK	='" . $this->request['productid'] . "',";
		$this->obDb->query .= "fVolDiscount	='" . $vDiscoutPerItem . "',";
		$this->obDb->query .= "iQty			='" . $this->request['qty'] . "'";
		$this->obDb->updateQuery();
		$cartId = $this->obDb->last_insert_id;

		foreach ($_POST as $field => $fieldValue) {
			$fArray = explode('_', $field);
			$cnt = count($fArray);

			if ($cnt == 3) {
				$fieldId = $fArray[2]; #GET OPTION ID
			}
			elseif ($cnt == 4) #FOR KITS
			{
				$fieldId = $fArray[3]; #GET OPTION ID
				$prodId = $fArray[2]; #GET PROD
			}
			if ($fArray[0] == 'option') {
				$this->obDb->query = "INSERT INTO " . TEMPOPTIONS . " SET ";
				$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
				$this->obDb->query .= "iProdId_FK='" . $this->request['productid'] . "',";
				$this->obDb->query .= "iOptId_FK='" . $fieldId . "',";
				$this->obDb->query .= "vOptVal ='" . $fieldValue . "'";

				$this->obDb->updateQuery();
			}
			if (($fArray[0] == 'choice' || $fArray[0] == 'choiceqty') && !empty ($fieldValue)) {
				$this->obDb->query = "INSERT INTO " . TEMPCHOICES . " SET ";
				$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
				$this->obDb->query .= "iProdId_FK='" . $this->request['productid'] . "',";
				$this->obDb->query .= "iTmpChoiceId_FK='" . $fieldId . "',";
				if ($fArray[0] == 'choiceqty') {
					$this->obDb->query .= "iQty='1',";
				}
				$this->obDb->query .= "vChoiceVal  ='" . $fieldValue . "'";

				$this->obDb->updateQuery();
			}
		}
		}

		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	} #END ADDTOCART

	#FUNCTION TO ADD MORE ELEMENTS ( ADD TO CART)
	function m_addToMulticart() {
		$libFunc = new c_libFunctions();
		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;
		if (!isset ($this->request['productid'])) {
			$this->request['productid'] = "";
		}
		$cntproducts = count($this->request['productid']);
		if ($cntproducts < 1) {
			$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}

		$qtyError = 0;
		for ($i = 0; $i < $cntproducts; $i++) {
			$display = 1;
			if (!isset ($this->request['productid'][$i])) {
				$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
				$this->libFunc->m_mosRedirect($retUrl);
				exit;
			}
			$comFunc->productId = $this->request['productid'][$i];

			if (!isset ($this->request['qty'][$i]))
				$this->request['qty'][$i] = 1;

			$this->request['qty'][$i] = intval($this->request['qty'][$i]);

			if ($this->request['qty'][$i] < 1) {
				$this->request['qty'][$i] = 1;
			}

			#TOTAL QUANTITY
			$this->m_getTotalQty($this->request['productid'][$i]);

			// Total of product quantity except the the current item;s quantity.
			$this->m_getTotalQty($this->request['productid'][$i], 1);

			$pid = $this->request['productid'][$i];
			#FOR OPTIONS
			$prodId = $this->request['productid'][$i];
			$_SESSION['backorder'][$pid] = 0;
			#MAIN STOCK CHECK -SETTINGS FROM FEATURES
			if (STOCK_CHECK == 1) {
				#TO CHECK STOCK CONTROL ENABLED FOR PRODUCT
				if ($this->iUseinventory == 1 && !$this->is_options($iProdId)) {
					$qtyAvailable = $this->iInventory - $this->totalQtyInTemp;
					if ($qtyAvailable < $this->request['qty'][$i]) {
						if ($this->iBackorder == 1) {
							$_SESSION['backorder'][$pid] = 1;
						} else {
							$this->request['qty'][$i] = $qtyAvailable;
							$this->errMsg .= "<li>" . $this->libFunc->m_displayContent($this->vTitle) . "</li>";
							$qtyError = 1;
							$display = 0;
						}
					}
				}

				$displayOptChoice = 1;
				#QUANTITY CHECK ON OPTIONS
				foreach ($_POST as $field => $fieldValue) {
					$fArray = explode('_', $field);
					$cnt = count($fArray);

					if ($cnt == 3) {
						$fieldId = $fArray[2]; #GET OPTION ID
						$prodId = $fArray[1];
					}
					elseif ($cnt == 4) #FOR KITS
					{
						$fieldId = $fArray[3]; #GET OPTION ID
						$prodId = $fArray[2]; #GET PROD
					}

					if ($fArray[0] == 'option') {
						if ($this->request['productid'][$i] == $prodId) {
							$qtyAvailable = $this->m_getOptionQty($prodId, $fieldValue);
							#TO CHECK STOCK CONTROL ENABLED
							if ($this->iUseinventory == 1) {
								if ($qtyAvailable < $this->request['qty'][$i]) {
									if ($this->iBackorder == 1) {
										$_SESSION['backorder'][$pid] = 1;
									} else {
										$this->request['qty'][$i] = $qtyAvailable;
										$this->errMsg .= "<li>" . $this->libFunc->m_displayContent($this->vTitle) . " - option(" . $this->libFunc->m_displayContent($this->vOptTitle) . ")</li>";
									}
								}
							}
						}
					}

					if ($fArray[0] == 'choiceqty' && !empty ($fieldValue)) {
						if ($this->request['productid'] == $prodId) {
							$qtyAvailable = $this->m_getChoiceQty($prodId, $fieldId);
							#TO CHECK STOCK CONTROL ENABLED
							if ($this->iUseinventory == 1) {
								if ($qtyAvailable < $fieldValue) {
									if ($this->iBackorder == 1) {
										$_SESSION['backorder'][$pid] = 1;
									} else {
										$this->request['qty'] = $qtyAvailable;
										$this->errMsg .= "<li>" . $this->vTitle . " - choice(" . $this->vOptTitle . ")</li>";
									}
								}
							}
						}
					}

				} #END of FOR LOOP
			} #END MAIN STOCK CHECK

			if ($this->iBackorder != 1 && $this->request['qty'][$i] < 1) {
				$display = 0;
			}

			if ($display == 1) {
				$vDiscoutPerItem = $comFunc->m_dspCartProductVolDiscount($this->request['qty'][$i]);
				$this->obDb->query = "INSERT INTO " . TEMPCART . " SET ";
				$this->obDb->query .= "vSessionId='" . SESSIONID . "',";
				$this->obDb->query .= "iProdId_FK='" . $this->request['productid'][$i] . "',";
				$this->obDb->query .= "fVolDiscount='" . $vDiscoutPerItem . "',";
				$this->obDb->query .= "iQty='" . $this->request['qty'][$i] . "'";
				$this->obDb->updateQuery();

				$cartId = $this->obDb->last_insert_id;
				foreach ($_POST as $field => $fieldValue) {
					$fArray = explode('_', $field);
					$cnt = count($fArray);

					if ($cnt == 3) {
						$fieldId = $fArray[2]; #GET OPTION ID
						$prodId = $fArray[1];
					}
					elseif ($cnt == 4) #FOR KITS
					{
						$fieldId = $fArray[3]; #GET OPTION ID
						$prodId = $fArray[2]; #GET PROD
					}

					#INSERTING WITH PROPER CART ID & PRODUCT ID
					//	if($this->request['productid'][$i]==$prodId)
					if ($fArray[0] == 'option' && $this->request['productid'][$i] == $prodId) {
						$this->obDb->query = "INSERT INTO " . TEMPOPTIONS . " SET ";
						$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
						$this->obDb->query .= "iProdId_FK='" . $prodId . "',";
						$this->obDb->query .= "iOptId_FK='" . $fieldId . "',";
						$this->obDb->query .= "vOptVal ='" . $fieldValue . "'";
						$this->obDb->updateQuery();
					}
					if (($fArray[0] == 'choice' || $fArray[0] == 'choiceqty') && !empty ($fieldValue) && $this->request['productid'][$i] == $prodId) {
						$this->obDb->query = "INSERT INTO " . TEMPCHOICES . " SET ";
						$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
						$this->obDb->query .= "iProdId_FK='" . $prodId . "',";
						$this->obDb->query .= "iTmpChoiceId_FK='" . $fieldId . "',";
						if ($fArray[0] == 'choiceqty') {
							$this->obDb->query .= "iQty='1',";
						}
						$this->obDb->query .= "vChoiceVal  ='" . $fieldValue . "'";

						$this->obDb->updateQuery();
					}
					#END CHECK PRODUCTID
				} #END OPTION/CHOICES FOR LOOP
			} #END QTY CHECK

		} #END MULTI CART PRODUCTS
		if (!empty ($this->errMsg)) {
			return false;
		}

		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	} #END ADDTOCART

	#FUNCTION TO UPDATE CART
	function m_updateCart($checkout = '0') {
		$comFunc = new c_commonFunctions();
		$comFunc->obDb = $this->obDb;
		$libFunc = new c_libFunctions();
		$totalQty = 0;
		$cntItems = count($this->request['cartid']); #TOTAL ITEMS IN CART

		for ($i = 0; $i < $cntItems; $i++) {
			$update = 1;
			if (!isset ($this->request['qty'][$i]) || empty ($this->request['qty'][$i]) || !is_numeric($this->request['qty'][$i]) || $this->request['qty'][$i] < 1) {
				$this->request['qty'][$i] = 1;
			}

			$iTmpCartId = $this->request['cartid'][$i];
			$iQty = $this->request['qty'][$i];

			$iProdId = $this->m_getProductId($iTmpCartId);
			$comFunc->productId = $iProdId;
			$this->m_getTotalQty($iProdId);

			#Total of product quantity except the the current item;s quantity.
			$this->m_getTotalQty($iProdId, 1, $iTmpCartId);

			$_SESSION['backorder'][$iProdId] = 0;
			#MAIN STOCK CHECK -SETTINGS FROM FEATURES
			if (STOCK_CHECK == 1) {
				#TO CHECK STOCK CONTROL ENABLED FOR PRODUCT
				if ($this->iUseinventory == 1 && !$this->is_options($iProdId)) {
					$qtyAvailable = $this->iInventory - $this->totalQtyInTemp;
					if ($qtyAvailable < $this->request['qty'][$i]) {
						if ($this->iBackorder == 1) {
							$_SESSION['backorder'][$iProdId] = 1;
						} else {
							$this->request['qty'][$i] = $qtyAvailable;
							$this->errMsg .= "<li>" . $this->libFunc->m_displayContent($this->vTitle) . "</li>";
							$update = 0;
						}
					} #quantity check
					$displayOptChoice = 1;
				} #end inventory check
				$displayOptChoice = 1;
				#QUANTITY CHECK ON OPTIONS
				foreach ($_POST as $field => $fieldValue) {
					$fArray = explode('_', $field);
					$cnt = count($fArray);

					if ($cnt == 2) {
						$fieldId = $fArray[1]; #GET OPTION ID
					}
					elseif ($cnt == 3) #FOR KITS
					{
						$fieldId = $fArray[2]; #GET OPTION ID
						$prodId = $fArray[1]; #GET PROD
					}
					#$iProdId product id according to cartid
					#$prodId  product id according to options/choice
					if ($fArray[0] == 'option') {
						if ($iProdId == $prodId) {
							$qtyAvailable = $this->m_getOptionQty($prodId, $fieldValue, $iTmpCartId);

							#TO CHECK STOCK CONTROL ENABLED
							if ($this->iUseinventory == 1) {
								if ($qtyAvailable < $this->request['qty'][$i] - $this->carttotalqty) {
									if ($this->iBackorder == 1) {
										$_SESSION['backorder'][$iProdId] = 1;
									} else {
										$this->request['qty'][$i] = $qtyAvailable;
										$this->errMsg .= "<li>" . $this->libFunc->m_displayContent($this->vTitle) . " - option(" . $this->libFunc->m_displayContent($this->vOptTitle) . ")</li>";
										$update = 0;
									}
								}
							}
						}
					}

					/*	if($fArray[0]=='choice' && !empty($fieldValue))
						{
							if($iProdId==$prodId)
							{
								$qtyAvailable=$this->m_getChoiceQty($prodId,$fieldId);
								#TO CHECK STOCK CONTROL ENABLED
								if($this->iUseinventory==1)
								{
									if($qtyAvailable<$this->request['qty'][$i])
									{
										if($this->iBackorder==1)
										{
											$_SESSION['backorder'][$iProdId]=1;
										}
										else
										{
											$this->request['qty'][$i]=$qtyAvailable;
											$this->errMsg.="<li>".$this->vTitle." - choice(".$this->vOptTitle.")</li>";
											$update=0;
										}
									}
								}
							}
						}*/
				}
			} #end main stock check

			$totalQty += $this->request['qty'][$i];
			if ($this->request['qty'][$i] < 1) {
				$this->request['qty'][$i] = 1;
			}
			if ($update == 1) {
				$vDiscoutPerItem = $comFunc->m_dspCartProductVolDiscount($this->request['qty'][$i]);
				$this->obDb->query = "UPDATE " . TEMPCART . " SET iQty='" . $this->request['qty'][$i] . "',";
				$this->obDb->query .= "fVolDiscount='" . $vDiscoutPerItem . "'";
				$this->obDb->query .= " WHERE (iTmpCartId_PK='" . $this->request['cartid'][$i] . "')";

				$this->obDb->updateQuery();
			}
		}

		$_SESSION['totalQty'] = $totalQty;

		if (!empty ($this->errMsg)) {
			return false;
		}

		if ($checkout == 1) {
		
			$this->Interface->checkout = 1;
			$this->Interface->template = $this->templatePath."viewcart.tpl.htm";
			$this->Interface->m_viewCart();
			exit;
		} else {
			$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);
			exit;
		}
	} #END UPDATE CART

	#FUNCTION TO DISPLAY STOCK MESSAGE
	function m_dspStockMessage() {

		$libFunc = new c_libFunctions();

		$this->ObTpl = new template();
		$this->ObTpl->set_file("TPL_STOCK_FILE", $this->stockTemplate);
		$this->ObTpl->set_var("TPL_VAR_SITEURL", SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_PRODUCTLIST", $this->errMsg);
		$backUrl = SITE_URL . "index.php";
		$this->ObTpl->set_var("TPL_VAR_BACKURL", $this->libFunc->m_safeUrl($backUrl));
		$cartUrl = SITE_URL . "ecom/index.php?action=ecom.viewcart";
		$this->ObTpl->set_var("TPL_VAR_CARTURL", $this->libFunc->m_safeUrl($cartUrl));
		$shopUrl = SITE_URL . "/index.php";
		$this->ObTpl->set_var("TPL_VAR_SHOPURL", $this->libFunc->m_safeUrl($shopUrl));

		return ($this->ObTpl->parse("return", "TPL_STOCK_FILE"));
	} #END FUNCTION

	#FUNCTION TO DELETE CART
	function m_deleteCart() {
		$libFunc = new c_libFunctions();
		$this->obDb->query = "DELETE FROM " . TEMPCHOICES . " WHERE  (iTmpCartId_FK='" . $this->request['mode'] . "')";
		$this->obDb->updateQuery();

		$this->obDb->query = "DELETE FROM " . TEMPOPTIONS . " WHERE  (iTmpCartId_FK='" . $this->request['mode'] . "')";
		$this->obDb->updateQuery();

		$this->obDb->query = "DELETE FROM " . TEMPCART . " WHERE (iTmpCartId_PK='" . $this->request['mode'] . "')";
		$this->obDb->updateQuery();

		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	} #END DELETE CART

	#FUNCTION TO EMPTY CART
	function m_emptyCart($backOrder = 0) {
		$libFunc = new c_libFunctions();

		$this->obDb->query = "SELECT iTmpCartId_PK  FROM " . TEMPCART;
		$this->obDb->query .= " WHERE (vSessionId ='" . SESSIONID . "')";
		if ($backOrder == 1) {
			$this->obDb->query .= " AND iBackOrder='1'";
		}
		$rsTmpId = $this->obDb->fetchQuery();
		$rsCnt = $this->obDb->record_count;
		if ($rsCnt > 0) {
			for ($i = 0; $i < $rsCnt; $i++) {
				$this->obDb->query = "DELETE FROM " . TEMPCHOICES . " WHERE (iTmpCartId_FK='" . $rsTmpId[$i]->iTmpCartId_PK . "')";
				$this->obDb->updateQuery();

				$this->obDb->query = "DELETE FROM " . TEMPOPTIONS . " WHERE (iTmpCartId_FK='" . $rsTmpId[$i]->iTmpCartId_PK . "')";
				$this->obDb->updateQuery();

				$this->obDb->query = "DELETE FROM " . TEMPCART . " WHERE (iTmpCartId_PK='" . $rsTmpId[$i]->iTmpCartId_PK . "')";
				$this->obDb->updateQuery();
			}
		}
		$_SESSION['INVOICE_EDITING'] = "";
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	} #END DELETE CART

	/* 
	 * Method empty the cart with redirect
	 */
	function m_emptyCartWithoutRedirect($backOrder = 0) {
		$libFunc = new c_libFunctions();

		$this->obDb->query = "SELECT iTmpCartId_PK  FROM " . TEMPCART;
		$this->obDb->query .= " WHERE (vSessionId ='" . SESSIONID . "')";
		if ($backOrder == 1) {
			$this->obDb->query .= " AND iBackOrder='1'";
		}
		$rsTmpId = $this->obDb->fetchQuery();
		$rsCnt = $this->obDb->record_count;
		if ($rsCnt > 0) {
			for ($i = 0; $i < $rsCnt; $i++) {
				$this->obDb->query = "DELETE FROM " . TEMPCHOICES . " WHERE (iTmpCartId_FK='" . $rsTmpId[$i]->iTmpCartId_PK . "')";
				$this->obDb->updateQuery();

				$this->obDb->query = "DELETE FROM " . TEMPOPTIONS . " WHERE (iTmpCartId_FK='" . $rsTmpId[$i]->iTmpCartId_PK . "')";
				$this->obDb->updateQuery();

				$this->obDb->query = "DELETE FROM " . TEMPCART . " WHERE (iTmpCartId_PK='" . $rsTmpId[$i]->iTmpCartId_PK . "')";
				$this->obDb->updateQuery();
			}
		}
	} #END DELETE CART	

	#FUNCTION TO ADD GIFT WRAP
	function m_addgiftwrap() {
		$libFunc = new c_libFunctions();
		$this->obDb->query = "UPDATE " . TEMPCART . " SET iGiftwrap='" . $this->request['id'] . "' WHERE (iTmpCartId_PK='" . $this->request['mode'] . "')";
		$this->obDb->updateQuery();
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.giftwrap&mode=" . $this->request['mode'] . "&msg=1");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}

	#FUNCTION TO REMOVE GIFT WRAP
	function m_removeGift() {
		$libFunc = new c_libFunctions();
		$this->obDb->query = "UPDATE " . TEMPCART . " SET iGiftwrap='0' WHERE (iTmpCartId_PK='" . $this->request['mode'] . "')";
		$this->obDb->updateQuery();
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}

	#FUNCTION GET PRODUCT ID BASED ON CART ID
	function m_getProductId($iCartId) {
		$libFunc = new c_libFunctions();
		$this->obDb->query = "SELECT iProdId_FK FROM " . TEMPCART . " WHERE (iTmpCartId_PK='" . $iCartId . "')";
		$res = $this->obDb->fetchQuery();
		return $res[0]->iProdId_FK;
	}

	function m_getTotalQty($iProductId, $temp = 0, $iCartId = 0) {
		$this->totalQtyInTemp = 0;
		if ($temp) {
			$this->obDb->query = "SELECT SUM(iQty) as totalQty  FROM " . TEMPCART . " WHERE  (vSessionId ='" . SESSIONID . "' AND iProdId_FK='" . $iProductId . "')";
			if ($iCartId != 0) {
				$this->obDb->query .= " AND iTmpCartId_PK != '" . $iCartId . "'";
			}

			$rs = $this->obDb->fetchQuery();
			$this->totalQtyInTemp = $rs[0]->totalQty;
		} else {
			$this->obDb->query = "SELECT iInventory,iUseinventory,iBackorder,vTitle  FROM " . PRODUCTS . " WHERE (iProdId_PK='" . $iProductId . "')";
			$rs = $this->obDb->fetchQuery();

			$this->iInventory = $rs[0]->iInventory;
			$this->iUseinventory = $rs[0]->iUseinventory;
			$this->iBackorder = $rs[0]->iBackorder;
			$this->vTitle = $rs[0]->vTitle;
		}

	} #END STOCK CHECK FUNCTION

	#FUNCTION TO CHECK OPTION QUANTITY
	function m_getOptionQty($iProductId, $iOptionValueId, $iTmpCartId = "") {
		$this->obDb->query = "SELECT SUM(iQty) as totalQty  FROM " . TEMPCART . " AS T," . TEMPOPTIONS . " AS OPT WHERE  (iTmpCartId_PK=iTmpCartId_FK AND vSessionId ='" . SESSIONID . "' AND OPT.iProdId_FK='" . $iProductId . "' AND vOptVal ='" . $iOptionValueId . "' AND iTmpCartId_PK!='" . $iTmpCartId . "')";
		$rsTemp = $this->obDb->fetchQuery();
		$this->obDb->query = "SELECT iInventory,iUseinventory,iBackorder,vItem FROM " . OPTIONVALUES . " WHERE (iOptionValueid_PK ='" . $iOptionValueId . "')";
		$rsOptions = $this->obDb->fetchQuery();
		$qtyAvailable = $rsOptions[0]->iInventory - $rsTemp[0]->totalQty;

		$this->iUseinventory = $rsOptions[0]->iUseinventory;
		$this->iBackorder = $rsOptions[0]->iBackorder;
		$this->vOptTitle = $rsOptions[0]->vItem;
		$this->carttotalqty = $rsTemp[0]->totalQty;
		return $qtyAvailable;
	}
	function is_options($iProductId) {
		$this->obDb->query = "SELECT * FROM " . PRODUCTOPTIONS . " TPO inner join " . OPTIONS . " TSO on(TPO.iOptionid=TSO.iOptionid_PK) where TPO.iProductid_FK='".$iProductId."'";
		$rsOptions1 = $this->obDb->fetchQuery();
		return $this->obDb->record_count;
	}
	#FUNCTION TO CHECK CHOICE QUANTITY
	function m_getChoiceQty($iProductId, $iChoiceId) {
		$this->obDb->query = "SELECT SUM(TC.iQty) as totalQty  FROM " . TEMPCART . " as TC," . TEMPCHOICES . " AS C WHERE  (iTmpCartId_PK=iTmpCartId_FK AND vSessionId ='" . SESSIONID . "' AND C.iProdId_FK='" . $iProductId . "' AND vChoiceVal='" . $iChoiceId . "')";
		$rsTemp = $this->obDb->fetchQuery();

		$this->obDb->query = "SELECT iInventory,iUseinventory,iBackorder,vName  FROM " . CHOICES . " WHERE (iChoiceid_PK ='" . $iChoiceId . "')";
		$rsChoice = $this->obDb->fetchQuery();

		$qtyAvailable = $rsChoice[0]->iInventory - $rsTemp[0]->totalQty;
		$this->iUseinventory = $rsChoice[0]->iUseinventory;
		$this->iBackorder = $rsChoice[0]->iBackorder;
		$this->vChoiceTitle = $rsChoice[0]->vName;
		$this->carttotalqty = $rsTemp[0]->totalQty;
		return $qtyAvailable;
	}

	function m_backOrderSeperate() {
		$libFunc = new c_libFunctions();
		$_SESSION['backOrderSeperate'] = 1;
		if (isset ($this->request['mode']) && !empty ($this->request['mode'])) {
			$this->obDb->query = "UPDATE " . TEMPCART . " SET ";
			$this->obDb->query .= "iBackOrder='1'";
			$this->obDb->query .= " WHERE iTmpCartId_PK='" . $this->request['mode'] . "'";
			$this->obDb->updateQuery();
		}
		$retUrl = $this->libFunc->m_safeUrl(SITE_URL . "ecom/index.php?action=ecom.viewcart");
		$this->libFunc->m_mosRedirect($retUrl);
		exit;
	}
	
	function CheckPackageProducts($PackageProducts){
		
		$this->errMsg = "";
		//Foreach loop on Product Package Starts
		foreach($PackageProducts as $key=>$value){
			$PrdOptionApplied = "";

			#MAIN STOCK CHECK -SETTINGS FROM FEATURES
			if (STOCK_CHECK == 1) {
				$this->m_getTotalQty($value->iProdId_FK,'','');
				#QUANTITY CHECK ON OPTIONS
				foreach ($_POST as $field => $fieldValue) {
					
					$fArray = explode('_', $field);
					$cnt = count($fArray);
					if ($cnt == 3) {
						$fieldId = $fArray[2]; #GET OPTION ID
					}
					elseif ($cnt == 4) #FOR KITS
					{
						$fieldId = $fArray[3]; #GET OPTION ID
						$prodId = $fArray[2]; #GET PROD
					}else{
						$fieldId = ""; #GET OPTION ID
						$prodId = ""; #GET PROD
					}
					$pid = $value->iProdId_FK;
					$_SESSION['backorder'][$pid] = 0;

					if(isset($fieldId) && !empty($fieldId)){
						if($prodId == $value->iProdId_FK){
							
							$PrdOptionApplied = 1;
							//Check for options of products if exists..
							$qtyAvailable = $this->m_getOptionQty($prodId, $fieldValue);

							#TO CHECK STOCK CONTROL ENABLED
							if ($this->iUseinventory == 1) {
								$PackageProdInv = $this->request['qty'] * $value->iQty;
								if ($qtyAvailable < $PackageProdInv) {
									if ($this->iBackorder == 1) {
										$_SESSION['backorder'][$pid] = 1;
									} else {
										$PackageProdInv = $qtyAvailable;

										$this->errMsg .= "<li>".$this->vTitle." - option(" . $this->libFunc->m_displayContent($this->vOptTitle) . "): Only ".$qtyAvailable." in Stock.</li>";
									}
								}
							}
						
						}
					}
				}
				//If option not applied then checks for stock level Of Product.
				if($PrdOptionApplied != "1"){
					//Fetch the stock info for particular product.
					$this->m_getTotalQty($value->iProdId_FK,'','');
					//If stock level on for particular product.
					if($this->iUseinventory){
						//Checks for all products in the package and prints error message for out of stock products.
						$PackageProdInv = $this->request['qty'] * $value->iQty;
						if ($this->iBackorder == 1) {
										$_SESSION['backorder'][$value->iProdId_FK] = 1;
						} else {
							if($PackageProdInv > $this->iInventory){
								$this->errMsg .= "<li>".$this->vTitle.": Only ".$this->iInventory." in Stock.</li>";
							}
						}
						
					}
				}
			}
		}
		//Foreach loop on Product Package Ends
		if (!empty ($this->errMsg)) {
			return false;
		}

	}

	function AddPackageTemp($PackageProducts){
		#INSERTING TO TEMPEREARY CART
		$this->obDb->query = "SELECT iTmpCartId_PK  FROM " . TEMPCART;
		$this->obDb->query .= " WHERE vSessionId ='" . SESSIONID . "'";
		$this->obDb->query .= " AND iProdId_FK='" . $this->request['productid'] . "'";
		$rs = $this->obDb->fetchQuery();
		$rsCnt = $this->obDb->record_count;

		$this->obDb->query = "INSERT INTO " . TEMPCART . " SET ";
		$this->obDb->query .= "vSessionId	='" . SESSIONID . "',";
		$this->obDb->query .= "iProdId_FK	='" . $this->request['productid'] . "',";
		$this->obDb->query .= "fVolDiscount	='" . $vDiscoutPerItem . "',";
		$this->obDb->query .= "iQty			='" . $this->request['qty'] . "'";

		$this->obDb->updateQuery();
		$cartId = $this->obDb->last_insert_id;
		foreach($PackageProducts as $k=>$v){
			$this->request['productid'] = $v->iProdId_FK;
			$flag = "0";
			foreach ($_POST as $field => $fieldValue) {
				$fArray = explode('_', $field);
				$cnt = count($fArray);

				if ($cnt == 3) {
					$fieldId = $fArray[2]; #GET OPTION ID
				}
				elseif ($cnt == 4) #FOR KITS
				{
					$fieldId = $fArray[3]; #GET OPTION ID
					$prodId = $fArray[2]; #GET PROD
				}
				if($prodId == $v->iProdId_FK){
					$flag = "1";
					if ($fArray[0] == 'option') {
						$this->obDb->query = "INSERT INTO " . TEMPOPTIONS . " SET ";
						$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
						$this->obDb->query .= "iProdId_FK='" . $this->request['productid'] . "',";
						$this->obDb->query .= "iOptId_FK='" . $fieldId . "',";
						$this->obDb->query .= "vOptVal ='" . $fieldValue . "'";

						$this->obDb->updateQuery();
					}
					if (($fArray[0] == 'choice' || $fArray[0] == 'choiceqty') && !empty ($fieldValue)) {
						$this->obDb->query = "INSERT INTO " . TEMPCHOICES . " SET ";
						$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
						$this->obDb->query .= "iProdId_FK='" . $this->request['productid'] . "',";
						$this->obDb->query .= "iTmpChoiceId_FK='" . $fieldId . "',";
						if ($fArray[0] == 'choiceqty') {
							$this->obDb->query .= "iQty='1',";
						}
						$this->obDb->query .= "vChoiceVal  ='" . $fieldValue . "'";

						$this->obDb->updateQuery();
					}
				}
			}
			if($flag == "0"){
				$this->obDb->query = "INSERT INTO " . TEMPOPTIONS . " SET ";
				$this->obDb->query .= "iTmpCartId_FK ='" . $cartId . "',";
				$this->obDb->query .= "iProdId_FK='" . $this->request['productid'] . "',";
				$this->obDb->query .= "iOptId_FK='',";
				$this->obDb->query .= "vOptVal =''";

				$this->obDb->updateQuery();
			}
		}

	}


} #CLASS ENDS
?>