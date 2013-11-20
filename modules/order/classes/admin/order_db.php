<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_orderDb
{
	#CONSTRUCTOR
	function c_orderDb()
	{
		$this->grandTotal			=0;
		$this->giftCertPrice		=0;
		$this->discountedPrice		=0;
		$this->postagePrice			=0;
		$this->cartWeightPrice		=0;
		$this->comFunc				=new c_commonFunctions();
		$this->libFunc				=new c_libFunctions();
		$this->maxPostage			=0;	
		$this->newProductAdded		=0;
	}
	
	#FUNCTION TO UPDATE STATUS FROM HOME
	function m_updateHome()
	{
		if(isset($this->request['status']))
		{
			$cnt=count($this->request['status']);
			for($i=0;$i<$cnt;$i++)
			{
				if($this->request['status'][$i]=='Delete')
				{
					$this->obDb->query="DELETE FROM ".ORDERPRODUCTS." WHERE    iOrderid_FK='".$this->request['orderid'][$i]."'";
					$this->obDb->updateQuery();

					$this->obDb->query="DELETE FROM ".ORDEROPTIONS." WHERE    iOrderid_FK='".$this->request['orderid'][$i]."'";
					$this->obDb->updateQuery();

					$this->obDb->query="DELETE FROM ".ORDERCHOICES." WHERE    iOrderid_FK='".$this->request['orderid'][$i]."'";
					$this->obDb->updateQuery();
					
					$this->obDb->query="DELETE FROM ".ORDERKITS." WHERE    iOrderid_FK='".$this->request['orderid'][$i]."'";
					$this->obDb->updateQuery();

					$this->obDb->query="DELETE FROM ".ORDERS." WHERE    iOrderid_PK='".$this->request['orderid'][$i]."'";
					$this->obDb->updateQuery();
				}
				else
				{
					$this->obDb->query="UPDATE ".ORDERS." set
					 `vStatus`='".$this->request['status'][$i]."' where  iOrderid_PK='".$this->request['orderid'][$i]."'";
					$this->obDb->updateQuery();
				}
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home&mstatus=".$this->request['mstatus']."&orderby=".$this->request['orderby']."&direction=".$this->request['direction']."&page=".$this->request['page']);	
	}	
	
	#FUNCTION TO UPDATE INVOICE
	function m_updateQty() {
		$this->newProductAdded=0;
		$this->comFunc->obDb=$this->obDb;
		
		$this->obDb->query="SELECT iProductid_FK FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['orderid']."'";
		$orderProductId = $this->obDb->fetchQuery();
		$orderProductNumber = $this->obDb->record_count;
				
		for ($i=0;$i<$orderProductNumber;$i++){
		if(!isset($this->request['qty'][$i]) || empty($this->request['qty'][$i])){
			$this->request['qty'][$i]=1;
		}
		$this->request['qty'][$i]=intval($this->request['qty'][$i]);
		$selectedChoices = array();
		$selectedChoicesCt = 0;
		if($this->request['qty'][$i]< 1)	{
			$this->request['qty'][$i]=1;
		}
	
		 //Quantity is updated here
		$this->obDb->query="UPDATE ".ORDERPRODUCTS." SET `iQty`='".$this->request['qty'][$i]."' WHERE  iOrderProductid_PK='".$orderProductId[$i]->iProductid_FK."'";
		$this->obDb->updateQuery();
		//Fetching the item weight to recalculate item weight and item price for updated order
		$this->obDb->query="SELECT P.fItemWeight,OP.iQty FROM ".ORDERPRODUCTS." OP,".PRODUCTS." P,".ORDERS." O WHERE ";
		$this->obDb->query.=" iOrderid_FK=iOrderid_PK AND iProductid_FK=iProdid_PK AND  iOrderid_PK=".(int)$this->request['orderid'];
		$qResult = $this->obDb->fetchQuery();
		//Quantity Multiplied
		foreach($qResult as $q){
			$this->cartWeight+=$q->iQty*$q->fItemWeight;
		}
		$this->cartWeightPrice=$this->cartWeight*DEFAULT_ITEMWEIGHT;
		$this->obDb->query="SELECT vShipCode,iFreeShip,fItemWeight FROM ".PRODUCTS;
		$this->obDb->query.="  WHERE iProdId_PK='".$orderProductId[$i]->iProductid_FK."'";
		$this->queryResult = $this->obDb->fetchQuery();

		if($this->request['iskit']==1)
		{
			$this->obDb->query ="SELECT iProdId_FK FROM ".PRODUCTKITS." PK,".PRODUCTS." P WHERE iProdId_FK=iProdId_PK AND  iKitId='".$orderProductId[$i]->iProductid_FK."'";
			$rsKits=$this->obDb->fetchQuery();
			$kitCount=$this->obDb->record_count;
			#INSERTING KITS*********************************************
			if($kitCount>0)
			{
				for($k=0;$k<$kitCount;$k++)
				{
					#TO UPDATE CHOICES	
					foreach($_POST as $field=>$fieldValue)
					{
						$fArray=explode('_',$field);
						$cnt=count($fArray);

						if($cnt==4)#FOR KITS
						{
							$prodId=$fArray[2];
							$optionId=$fArray[3];#GET OPTION ID
						}

						if($fArray[0]=='option')
						{
							foreach($_POST as $orderOptionidField=>$orderOptionValue)
							{
								$fOrderArray=explode('_',$orderOptionidField);
								if($fOrderArray[0]=='orderoptionid' && $fOrderArray[1]==$prodId && $fOrderArray[2]==$optionId)
								{
									#GETIING PRICE FOR CURRENT OPTION
									$this->obDb->query="SELECT fPrice FROM ".OPTIONVALUES;
									$this->obDb->query.=" WHERE iOptionValueid_PK='".$fieldValue."'";
									$optValPrice=$this->obDb->fetchQuery();

									$this->obDb->query="UPDATE ".ORDEROPTIONS." SET ";
									$this->obDb->query.="fPrice='".$optValPrice[0]->fPrice."',";
									$this->obDb->query.="iOptionid='".$fieldValue."' WHERE ";
									$this->obDb->query.=" iOptionid_PK='".$orderOptionValue."'";
									$this->obDb->updateQuery();
								}#endif
							}#endfor						
						}#end if option
					}
				}
			}
		}
		else
		{
			#TO UPDATE OPTIONS/CHOICES	
			foreach($_POST as $field=>$fieldValue)
			{
				$fArray=explode('_',$field);
				$cnt=count($fArray);

				if($cnt==3)
				{
					$optionId=$fArray[2];#GET OPTION ID
					$prodId=$fArray[1];
				}

				if($fArray[0]=='option')
				{
					foreach($_POST as $orderOptionidField=>$orderOptionValue)
					{
						#TO GIVE PRIMARY KEY FOR OREDEROPTION TABLE (iOptionid_PK)-Sorry for ambiguity
						$fOrderArray=explode('_',$orderOptionidField);
						 $this->obDb->query ="SELECT fPrice  FROM ".OPTIONVALUES." WHERE iOptionValueid_PK='".$fieldValue."'";
						$rsOptions=$this->obDb->fetchQuery();
			
						if($fOrderArray[0]=='orderoptionid')
						{
							if($fOrderArray[1]==$prodId && $fOrderArray[2]==$optionId)
							{
								$this->obDb->query ="SELECT count(*) as cnt FROM ".ORDEROPTIONS." WHERE iOptionid_PK='".$orderOptionValue."'";
								$rsCheck=$this->obDb->fetchQuery();

								if($rsCheck[0]->cnt>0)
								{	
									$this->obDb->query="UPDATE ".ORDEROPTIONS." SET ";
									$this->obDb->query.="iOptionid='".$fieldValue."',fPrice='".$rsOptions[0]->fPrice."' WHERE ";
									$this->obDb->query.=" iOptionid_PK='".$orderOptionValue."'";
									$this->obDb->updateQuery();
								}
								else
								{
									$this->m_updateOptions($prodId,$orderProductId[$i]->iProductid_FK);
								}
							}
							elseif(!isset($fOrderArray[1]) || empty($fOrderArray[1]))
							{
								$this->m_updateOptions($prodId,$orderProductId[$i]->iProductid_FK);
							}
						}#endif
					}#endfor
				}#end if option
			
				if(($fArray[0]=='choice' || $fArray[0]=='choiceqty'))
				{
					$this->obDb->query ="SELECT vDescription,vType,fPrice  FROM ".CHOICES." WHERE iChoiceid_PK='".$optionId."'";
					$rsChoices=$this->obDb->fetchQuery();
					$this->obDb->query ="SELECT count(*) as cnt FROM ".ORDERCHOICES." WHERE iChoiceid_FK='".$optionId."' AND iProductOrderid_FK='".$this->request['orderproductid']."'";
					$rsCheck=$this->obDb->fetchQuery();
					if($rsCheck[0]->cnt>0)
					{	
						#IF QUANTITY IS SELECTED
						if($fArray[0]=='choiceqty')
						{
							$fieldValue=intval($fieldValue);
							$rsChoices[0]->fPrice=$rsChoices[0]->fPrice*$fieldValue;
						}
						if ($fArray[0]=='choice'){			
							
							$selectedChoices[$selectedChoicesCt] =  $fArray[2];
							$selectedChoicesCt++;										
						}
						$this->obDb->query="UPDATE ".ORDERCHOICES." SET 
						vChoiceValue='".$this->libFunc->m_addToDB($fieldValue)."',
						fPrice='".$rsChoices[0]->fPrice."'  							
						WHERE iChoiceid_FK='$optionId' AND iProductOrderid_FK='".$orderProductId[$i]->iProductid_FK."'";
						$this->obDb->updateQuery();
					}
					elseif(!empty($fieldValue))
					{
						#IF QUANTITY IS SELECTED
						if($fArray[0]=='choiceqty')
						{
							$fieldValue=intval($fieldValue);
							$rsChoices[0]->fPrice=$rsChoices[0]->fPrice*$fieldValue;
						}
		
						$this->obDb->query="INSERT INTO ".ORDERCHOICES." SET 
						iOrderid_FK 				='".$this->request['orderid']."',
						iProductid_FK			='".$this->request['productid']."',
						iProductOrderid_FK	='".$orderProductId[$i]->iProductid_FK."',
						iChoiceid_FK			='".$optionId."',
						vChoiceValue			='".$this->libFunc->m_addToDB($fieldValue)."',
						vDescription 			='".$this->libFunc->m_addToDB($rsChoices[0]->vDescription)."',
						fPrice						='".$rsChoices[0]->fPrice."',
						vType 					='".$this->libFunc->m_addToDB($rsChoices[0]->vType)."'";
						$this->obDb->updateQuery();
					}
				}#end choice
			}#endForeach
			$this->obDb->query=" SELECT iChoiceid_PK, iOrderid_FK, vType, iChoiceid_FK FROM ".ORDERCHOICES." WHERE vType='checkbox' AND 
							     iProductOrderid_FK= ".$orderProductId[$i]->iProductid_FK." AND iOrderid_FK = ".$this->request['orderid'];
		
			$prodChoices 	= 	$this->obDb->fetchQuery();
			$prodChoicesCt  = 	$this->obDb->record_count;
			
		 	for ($i=0; $i < $prodChoicesCt; $i++){		 	
		 		if ( !in_array($prodChoices[$i]->iChoiceid_FK,$selectedChoices)){
		 			
		 			$this->obDb->query="UPDATE ".ORDERCHOICES." SET	vChoiceValue='0', fPrice='0'  							
										WHERE iChoiceid_FK='".$prodChoices[$i]->iChoiceid_FK."' AND iProductOrderid_FK='".$orderProductId[$i]->iProductid_FK."'";
					$this->obDb->updateQuery();
		 		}
		 	}			
		}#elseKit

		}
		$this->m_updateInvoiceDetails();
		$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$this->request['orderid']);	
	}

	#FUNCTION TO UPDATE INVOICE STATUS
	function m_updateInvoice() {
		$this->request['pay_status']	=$this->libFunc->ifSet($this->request,"pay_status",0);
		$this->request['complete']		=$this->libFunc->ifSet($this->request,"complete",0);
		$this->request['adminComments'] =$this->libFunc->ifSet($this->request,"adminComments");
				
		if($this->request['status']=='Delete')
		{
			$this->obDb->query="DELETE FROM ".ORDERPRODUCTS." WHERE    iOrderid_FK='".$this->request['orderid']."'";
			$this->obDb->updateQuery();

			$this->obDb->query="DELETE FROM ".ORDEROPTIONS." WHERE    iOrderid_FK='".$this->request['orderid']."'";
			$this->obDb->updateQuery();

			$this->obDb->query="DELETE FROM ".ORDERCHOICES." WHERE    iOrderid_FK='".$this->request['orderid']."'";
			$this->obDb->updateQuery();
			
			$this->obDb->query="DELETE FROM ".ORDERKITS." WHERE    iOrderid_FK='".$this->request['orderid']."'";
			$this->obDb->updateQuery();

			$this->obDb->query="DELETE FROM ".ORDERS." WHERE    iOrderid_PK='".$this->request['orderid']."'";
			$this->obDb->updateQuery();
			$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");	
		}
		else
		{
			$this->obDb->query ="UPDATE ".ORDERS." SET iPayStatus='".$this->request['pay_status']."',";
			$this->obDb->query.="vStatus='".$this->request['status']."',"; 
			$this->obDb->query.="tAdminComments='".$this->request['adminComments']."',";
			$this->obDb->query.="iOrderStatus='".$this->request['complete']."'  WHERE ";
			$this->obDb->query.="iOrderid_PK='".$this->request['orderid']."'";
			$this->obDb->updateQuery();
			if(isset($this->request['mp']) && $this->request['mp']=='1')
			{
				$this->obDb->query = "SELECT fMemberPoints FROM ".CUSTOMERS." WHERE iCustmerid_PK='".$this->request['cid']."'";
				$result = $this->obDb->fetchQuery();
				//points = points - used + earned
				$new = $result[0]->fMemberPoints - $this->request['mpa'] + $this->request['mpe'];
				$this->obDb->query = "UPDATE ".CUSTOMERS." SET fMemberPoints='".$new."' WHERE iCustmerid_PK='".$this->request['cid']."'";
				$this->obDb->updateQuery();
			}
			if(isset($this->request['gc']) && $this->request['gc']=='1')
			{
				$this->obDb->query = "SELECT fRemaining FROM ".GIFTCERTIFICATES." WHERE iGiftcertid_PK='".$this->request['gcid']."'";
				$result = $this->obDb->fetchQuery();
				$new = $result[0]->fRemaining + $this->request['gca'];
				$this->obDb->query = "UPDATE ".GIFTCERTIFICATES." SET fRemaining='".$new."' WHERE iGiftcertid_PK='".$this->request['gcid']."'";
				$this->obDb->updateQuery();
			}
			if($this->request['status']=='Shipped')
			{
				$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.track&orderid=".$this->request['orderid']);	
			}
			else
			{				$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$this->request['invoice']);	
			}
		
			
			if ($this->request['qty']==0)
			{
			$this->obDb->query="DELETE FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['orderid']."'";
			$this->obDb->updateQuery();
			}	
		}
		$this->m_updateQty();
		
	//	$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.home");	
	}

	#FUNCTION TO ADD NEW PRODUCTS
	function m_addProduct()
	{
		$this->newProductAdded=1;
		$this->comFunc->obDb=$this->obDb;
		if(!isset($this->request['qty']) || empty($this->request['qty']))
		{
			$this->request['qty']=1;
		}
		$this->request['qty']=intval($this->request['qty']);
		if($this->request['qty']<1)
		{
			$this->request['qty']=1;
		}
	
		$this->comFunc->productId=$this->request['productid'];
		$this->comFunc->qty=$this->request['qty'];

		$this->obDb->query = "SELECT vTitle,vSku,fPrice,tShortDescription,vSeoTitle,";
		$this->obDb->query.="iTaxable,vShipCode,iFreeShip,fItemWeight,iFreeShip FROM ".PRODUCTS;
		$this->obDb->query.="  WHERE iProdId_PK='".$this->request['productid']."'";
		$this->queryResult = $this->obDb->fetchQuery();

		#VOLUME DISCOUNT on THIS ITEM
		$this->volDiscount=$this->comFunc->m_dspCartProductVolDiscount($this->request['qty']);
		
		$this->obDb->query="INSERT INTO ".ORDERPRODUCTS." SET ";
		$this->obDb->query.="iQty='".$this->request['qty']."',";
		$this->obDb->query.="iOrderid_FK='".$this->request['orderid']."',";
		$this->obDb->query.="iProductid_FK ='".$this->request['productid']."',";
		$this->obDb->query.="vSku='".$this->queryResult[0]->vSku."',";
		$this->obDb->query.="tShortDescription ='".$this->queryResult[0]->tShortDescription ."',";
		$this->obDb->query.="vSeoTitle='".$this->queryResult[0]->vSeoTitle."',";
		$this->obDb->query.="fPrice='".$this->queryResult[0]->fPrice."',";
		$this->obDb->query.="vTitle='".$this->queryResult[0]->vTitle."',";
		$this->obDb->query.="iKit='".$this->request['iskit']."',";
		$this->obDb->query.="iTaxable='".$this->queryResult[0]->iTaxable."',";
		$this->obDb->query.="iFreeship='".$this->queryResult[0]->iFreeShip."',";
		$this->obDb->query.="fDiscount='".$this->volDiscount."'";
		$this->obDb->updateQuery();
		$productOrderId=$this->obDb->last_insert_id;

		if($this->request['iskit']==1)
		{
			$this->obDb->query ="SELECT PK.*,P.vTitle,P.vSku FROM ".PRODUCTKITS." PK,".PRODUCTS." P WHERE iProdId_FK=iProdId_PK AND  iKitId='".$this->request['productid']."'";
			$rsKits=$this->obDb->fetchQuery();
			$kitCount=$this->obDb->record_count;
			#INSERTING KITS*********************************************
			if($kitCount>0)
			{
				for($k=0;$k<$kitCount;$k++)
				{
					$this->obDb->query="INSERT INTO ".ORDERKITS." SET 
					iOrderid_FK 				='".$this->request['orderid']."',
					iProductid_FK			='".$rsKits[$k]->iProdId_FK."',
					iProductOrderid_FK	='".$productOrderId."',
					iKitid	 					='".$rsKits[$k]->iKitId."',
					iKitItem_id				='".$rsKits[$k]->iKitId_PK."',
					iKitgroup 				='".$rsKits[$k]->iKitId."',
					iKitItem_title			='".$this->libFunc->m_addToDB($rsKits[$k]->vTitle."(".$rsKits[$k]->vSku.")")."'";
					$this->obDb->updateQuery();
					$this->m_updateOptions($rsKits[$k]->iProdId_FK,$productOrderId);
				}
			}
		}
		else
		{
			$this->m_updateOptions($this->request['productid'],$productOrderId);
		}
		

		#TO UPDATE CHOICES	
		foreach($_POST as $field=>$fieldValue)
		{
			$fArray=explode('_',$field);
			$cnt=count($fArray);

			if($cnt==3)
			{
				$fieldId=$fArray[2];#GET OPTION ID
			}
		
			if(($fArray[0]=='choice' || $fArray[0]=='choiceqty') && !empty($fieldValue))
			{
				#INSERTING CHOICES
				 $this->obDb->query ="SELECT vDescription,fPrice,iChoiceid_PK,vType  FROM ".CHOICES." WHERE iChoiceid_PK='".$fieldId."'";
				$rsChoices=$this->obDb->fetchQuery();
				$rsChoiceCount=$this->obDb->record_count;
				if($rsChoiceCount>0)
				{
					for($j=0;$j<$rsChoiceCount;$j++)
					{
						#IF QUANTITY IS SELECTED
						if($fArray[0]=='choiceqty')
						{
							$fieldValue=intval($fieldValue);
							$rsChoices[$j]->fPrice=$rsChoices[$j]->fPrice*$fieldValue;
						}
						$this->obDb->query="INSERT INTO ".ORDERCHOICES." SET 
						iOrderid_FK 				='".$this->request['orderid']."',
						iProductid_FK			='".$this->request['productid']."',
						iProductOrderid_FK	='".$productOrderId."',
						iChoiceid_FK			='".$rsChoices[$j]->iChoiceid_PK."',
						vChoiceValue			='".$this->libFunc->m_addToDB($fieldValue)."',
						vDescription 			='".$this->libFunc->m_addToDB($rsChoices[$j]->vDescription)."',
						fPrice						='".$rsChoices[$j]->fPrice."',
						vType 					='".$this->libFunc->m_addToDB($rsChoices[$j]->vType)."'";
						$this->obDb->updateQuery();
					}#endfor
				}#end choicecount
			}#end choice
		}#end choices
		#TO CHECK WEIGHT  ACTIVE
		if(ISACTIVE_ITEMWEIGHT==1)
		{
			$this->cartWeightPrice=$this->queryResult[0]->fItemWeight*DEFAULT_ITEMWEIGHT;
		}
		$this->m_updateInvoiceDetails();
	}

	function m_updateOptions($productId,$prodOrderId) {
		foreach($_POST as $field=>$fieldValue)
		{
			$fArray=explode('_',$field);
			$cnt=count($fArray);

			if($cnt==3)
			{
				$optionId=$fArray[2];#GET OPTION ID
				$prodId=$fArray[1];
			}
			elseif($cnt==4)#FOR KITS
			{
				$prodId=$fArray[2];
				$optionId=$fArray[3];#GET OPTION ID
			}
			if($fArray[0]=='option'  && $productId==$prodId)
			{
				#INSERTING OPTIONS
				$this->obDb->query ="SELECT O.iOptionid_PK,vName,vDescription FROM ".OPTIONS." O,".PRODUCTOPTIONS." PO WHERE iOptionid=O.iOptionid_PK AND iOptionid='".$optionId."' AND iProductid_FK='".$prodId."'";
				$rsOptions=$this->obDb->fetchQuery();
				$optCount=$this->obDb->record_count;
				if($optCount>0)
				{
					for($k=0;$k<$optCount;$k++)
					{
						 $this->obDb->query ="SELECT vItem,fPrice FROM ".OPTIONVALUES." WHERE iOptionid_FK='".$optionId."' AND iOptionValueid_PK='".$fieldValue."'";
						$rsOptionValue=$this->obDb->fetchQuery();

						$this->obDb->query="INSERT INTO ".ORDEROPTIONS." SET 
						iOrderid_FK 				='".$this->request['orderid']."',
						iProductid_FK			='".$prodId."',
						iProductOrderid_FK	='".$prodOrderId."',
						iOptionid					='".$fieldValue."',
						vName 					='".$this->libFunc->m_addToDB($rsOptions[$k]->vDescription)."',
						vItem  					='".$this->libFunc->m_addToDB($rsOptionValue[$k]->vItem)."',
						fPrice						='".$rsOptionValue[$k]->fPrice."'";
						$this->obDb->updateQuery();
					}#END FOR
				}#END IF OPTION
			}#end if option
		}#endfor
	}#ef

	#FUNCTION WILL UPDATE DETAILS EFFECTED AFTER ANY UPDATION IN ADMIN ORDER SECTION
	function m_updateInvoiceDetails() {
		#NOTE: PRICE FOR QTY BOX HAS ADDED AFTER CALCULATION
		#PRICE ACCORDING TO PRODUCT QUANTITY FROM ORDER PRODUCT
		$this->obDb->query= "SELECT SUM((fPrice*iQty)-fDiscount) as subtotal  FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['orderid']."'";
		$rs = $this->obDb->fetchQuery();
		$this->subTotal=$rs[0]->subtotal;

		#TAX TOTAL FROM PRODUCT ORDERED
		$this->obDb->query= "SELECT SUM(fPrice*iQty) as subtotal  FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['orderid']."' AND iTaxable=1";
		$rs1 = $this->obDb->fetchQuery();

		#TAX ON SUB TOTAL ONLY
		$this->taxTotal=$rs1[0]->subtotal;
		#TAX TOTAL FROM PRODUCT ORDERED
		$this->obDb->query= "SELECT SUM(fPrice*iQty) as subtotal  FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['orderid']."' AND iFreeShip!=1";
		$rs1 = $this->obDb->fetchQuery();
		#OMITING FREESHIPED PRODUCT
		$this->postageTotal=$rs1[0]->subtotal;

		#OPTIONS PRICE
		$this->obDb->query= "SELECT SUM(O.fPrice*iQty) as optTotal  FROM ".ORDEROPTIONS." O,".ORDERPRODUCTS." WHERE iProductOrderid_FK=iOrderProductid_PK AND O.iOrderid_FK='".$this->request['orderid']."'";
		$rsopt = $this->obDb->fetchQuery();
	
		#CHOICES PRICE FOR SIMPLE
		$this->obDb->query= "SELECT SUM(O.fPrice*iQty) as optTotal  FROM ".ORDERCHOICES." O,".ORDERPRODUCTS." WHERE iProductOrderid_FK=iOrderProductid_PK AND O.iOrderid_FK='".$this->request['orderid']."'";
		$rschoice = $this->obDb->fetchQuery();
		$totalPrice=$rschoice[0]->optTotal+$rsopt[0]->optTotal;
	
		#OPTIONS TAXABLE AMOUNT
		$this->obDb->query= "SELECT SUM(O.fPrice*iQty) as optTotal  FROM ".ORDEROPTIONS." O,".ORDERPRODUCTS."  WHERE iProductOrderid_FK=iOrderProductid_PK AND iTaxable=1 AND O.iOrderid_FK='".$this->request['orderid']."'";
		$rsoptTax = $this->obDb->fetchQuery();
		
		$this->obDb->query= "SELECT SUM(O.fPrice*iQty) as optTotal  FROM ".ORDERCHOICES." O,".ORDERPRODUCTS." WHERE iProductOrderid_FK=iOrderProductid_PK AND iTaxable=1 AND O.iOrderid_FK='".$this->request['orderid']."'";
		$rschoiceTax = $this->obDb->fetchQuery();
		$totalPriceTax=$rschoiceTax[0]->optTotal+$rsoptTax[0]->optTotal;

		#OPTIONS FREESHIP
		$this->obDb->query= "SELECT SUM(O.fPrice*iQty) as optTotal  FROM ".ORDEROPTIONS." O,".ORDERPRODUCTS."  WHERE iProductOrderid_FK=iOrderProductid_PK AND iFreeShip!=1 AND O.iOrderid_FK='".$this->request['orderid']."'";
		$rsoptNotFree = $this->obDb->fetchQuery();

		
		$this->obDb->query= "SELECT SUM(O.fPrice*vChoiceValue*iQty) as optTotal  FROM ".ORDERCHOICES." O,".ORDERPRODUCTS." WHERE iProductOrderid_FK=iOrderProductid_PK AND iFreeShip!=1 AND O.iOrderid_FK='".$this->request['orderid']."'";
		$rschoiceNotFree = $this->obDb->fetchQuery();

		$totalPriceNotFree=$rschoiceNotFree[0]->optTotal+$rsoptNotFree[0]->optTotal;

		$this->subTotal+=$totalPrice;
		$this->taxTotal+=$totalPriceTax;
		$this->postageTotal+=$totalPriceNotFree;

		#promotions on sub total no postage included
		$this->grandTotal=$this->subTotal;
		
		#PROMOTION DISCOUNTS % ON SUBTOTAL
		$promotionDiscount=$this->comFunc->m_calculatePromotionDiscount($this->subTotal);
		if($promotionDiscount<0)
			$promotionDiscount=0;
		$this->grandTotal-=$promotionDiscount;
 		$this->postageTotal-=$promotionDiscount;
		$this->taxTotal-=$promotionDiscount;
		#SELECTING FROM ORDERS TABLE TO MANPULATE
		$this->obDb->query ="SELECT iInvoice,fShipByWeightPrice,fTaxRate,vDiscountCode,iGiftcert_FK,";
		$this->obDb->query.="vShipMethod_Id,vShipDescription,fMemberPoints,fShipByWeightKg,fCodCharge FROM ".ORDERS;
		$this->obDb->query.=" WHERE iOrderid_PK='".$this->request['orderid']."'";
		$rsOrder= $this->obDb->fetchQuery();
		#UPDATING TOTAL ACC TO MEMBER POINTS
		$this->grandTotal-=$rsOrder[0]->fMemberPoints;
		$this->postageTotal-=$rsOrder[0]->fMemberPoints;	

		$this->comFunc->grandTotal=$this->postageTotal;

		$this->obDb->query= "SELECT COUNT(*) as notFreeCnt  FROM ".ORDERPRODUCTS." WHERE iOrderid_FK='".$this->request['orderid']."' AND iFreeShip!=1";
		$rs1 = $this->obDb->fetchQuery();
		#OMITING FREESHIPED PRODUCT
		$this->notFreeCnt=$rs1[0]->notFreeCnt;

		if($this->notFreeCnt>0)
		{	  
			#TRAVERSING THROUGH ORDERED PRODUCTS
			$this->obDb->query= "SELECT vShipCode,O.iFreeShip,iQty FROM ".ORDERPRODUCTS." O,".PRODUCTS." P WHERE iProdid_PK =iProductid_FK  && iOrderid_FK='".$this->request['orderid']."'";
			#IF CURRENT METHOD IS POSTAGE CODE /HIGHEST POSTAGE
			if(DEFAULT_POSTAGE_METHOD=='codes')
			{
				$rsShip =$this->obDb->fetchQuery();
				$rCount=$this->obDb->record_count;

				if($rCount>0)
				{
					for($i=0;$i<$rCount;$i++)
					{
						if($rsShip[$i]->iFreeShip !=1){
							if(!empty($rsShip[$i]->vShipCode))
							{
								$this->comFunc->qty=$rsShip[$i]->iQty;
								$this->comFunc->postageId=$this->queryResult[$i]->vShipCode;
								$this->postagePrice=$this->comFunc->m_postageCodePrice();
							}	
						}
					}
				}
			}elseif(DEFAULT_POSTAGE_METHOD=='highest'){
				$rsShip =$this->obDb->fetchQuery();
				$rCount=$this->obDb->record_count;

				if($rCount>0)
				{
					for($i=0;$i<$rCount;$i++)
					{
						if($rsShip[$i]->iFreeShip !=1){
							if($this->maxPostage<$rsShip[$i]->fPostagePrice){
								$this->maxPostage=$rsShip[$i]->fPostagePrice;
							}
						}
					}
					$this->postagePrice=$this->maxPostage;
				}
			}

			if($this->postagePrice==0)
			{
				$this->postagePrice=$this->comFunc->m_postagePrice();
			}

			//SPECIAL_POSTAGE
			#IF POSTAGE NAME IS DEFAULT THEN APPLY OTHER METHOD
			if($this->postagePrice==0 || SPECIAL_POSTAGE==1)
			{
				$this->obDb->query ="SELECT vField1,vField2 FROM  ".POSTAGE." P,".POSTAGEDETAILS." PD WHERE iPostId_PK=iPostId_FK AND vKey='special' AND PD.iPostDescId_PK='".$rsOrder[0]->vShipMethod_Id."'";
				$rsPostage=$this->obDb->fetchQuery();
				$rsCount=$this->obDb->record_count;
				if($rsCount>0)
				{
					for($j=0;$j<$rsCount;$j++)
					{
						$addtional=$this->request['qty']-1;
						if($addtional>0)
						{
							$this->specialPrice=$rsPostage[$j]->vField1+($rsPostage[$j]->vField2*$addtional);
						}
						else
						{
							$this->specialPrice=$rsPostage[$j]->vField1;
						}
					}#END FOR
				}#END IF
				if(SPECIAL_POSTAGE==1){
					$this->postagePrice+=$this->specialPrice;
				}else{
					$this->postagePrice=$this->specialPrice;
				}
			}#END IF
		} 
		#CAR WEIGHT UPDATE
		if($this->newProductAdded==1){	
			$this->cartWeight=$rsOrder[0]->fShipByWeightKg+$this->queryResult[0]->fItemWeight;
		}
		#CALCULATE WEIGHT PRICE
		if($this->newProductAdded==1){
			$this->cartWeightPrice+=$rsOrder[0]->fShipByWeightPrice;
		}
   		
		#UPDATING WEIGHT PRICE
		$this->grandTotal+=$this->cartWeightPrice;
		if(VAT_POSTAGE_FLAG)
		$this->taxTotal+=$this->cartWeightPrice;
		if($this->grandTotal<0)
		{
			$this->grandTotal=0;
		}
		#HANDLING FREE SHIP PROMOTION DISCOUNT
		if($rsOrder[0]->vShipDescription!="Free P&amp;P" && $this->notFreeCnt>0)
		{
			$this->grandTotal+=$this->postagePrice;
			if(VAT_POSTAGE_FLAG)
			$this->taxTotal+=$this->postagePrice;
		}		
		
		if(!empty($rsOrder[0]->vDiscountCode))
		{
			$this->discountPrice=$this->comFunc->m_calculateDiscount($rsOrder[0]->vDiscountCode);
		}
		if(!empty($rsOrder[0]->iGiftcert_FK))
		{
			$this->giftCertPrice=$this->comFunc->m_calculateGiftCertPrice($rsOrder[0]->iGiftcert_FK);
		}
		#COD PRICE DISCOUNTABLE
		$this->grandTotal+=$rsOrder[0]->fCodCharge;
		#CHECK FOR DISCOUNTS
		if($this->discountPrice!=0)
		{
			$this->discountedPrice=$this->discountPrice*$this->grandTotal/100;
			$this->grandTotal-=$this->discountedPrice;
			$this->taxTotal-=$this->discountedPrice;
		}
		#CHECK FOR GIFTCERTIFICATES
		if($this->giftCertPrice!=0)
		{
			if($this->grandTotal<$this->giftCertPrice)
			{
				$this->giftCertPrice=$this->grandTotal;
			}
			$this->grandTotal-=$this->giftCertPrice;
			$this->taxTotal-=$this->giftCertPrice;
		}
		#SUBTRACTING MEMBER POINTS FROM VAT TOTAL
		$this->taxTotal-=$rsOrder[0]->fMemberPoints;
		if($this->taxTotal<0)
		{
			$this->taxTotal=0;
		}
		#VAT TAX
		$this->vatTotal=($rsOrder[0]->fTaxRate*$this->taxTotal)/100;
		
		$this->grandTotal+=$this->vatTotal;
		
		if($this->grandTotal<0)
		{
			$this->grandTotal=0;
		}

		$this->obDb->query="UPDATE ".ORDERS." SET ";
		$this->obDb->query.="fShipByWeightKg ='".$this->cartWeight."',";
		$this->obDb->query.="fShipByWeightPrice ='".$this->cartWeightPrice."',";
		$this->obDb->query.="fGiftcertTotal='".$this->giftCertPrice."',";
		$this->obDb->query.="fDiscount='".$this->discountedPrice."',";
		$this->obDb->query.="fTaxPrice ='".$this->vatTotal."',";
		$this->obDb->query.="fPromoValue='".$promotionDiscount."',";
		$this->obDb->query.="fTotalPrice='".$this->grandTotal."',";
		$this->obDb->query.="fShipTotal='".$this->postagePrice."'";
		$this->obDb->query.=" WHERE iOrderid_PK='".$this->request['orderid']."'";
		$this->obDb->updateQuery();

		$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$rsOrder[0]->iInvoice);	
	}
	
	#FUNCTION TO REMOVE CREADIT CARD INFO
	function m_removeCreditInfo()
	{
		 $this->obDb->query ="DELETE FROM ".CREDITCARDS." WHERE iOrderid_FK='".$this->request['orderid']."'";
		 $this->obDb->updateQuery();
		 $this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.dspDetails&orderid=".$this->request['invoice']);	
	}#ef

	#FUNCTION TO UPDATE TRACKING/SHIPPING INFO
	function m_updateTrackingInfo()
	{
		$timeStamp=time();
		if(empty($this->request['shipper']))
		{
			$this->request['shipper']=$this->request['shipper2'];
		}
		if($this->request['mode']=="update")
		{			
			 $this->obDb->query ="UPDATE ".SHIPPINGDETAILS." SET ";
			 $this->obDb->query.="	vShipper		 ='".$this->request['shipper']."',";
			 $this->obDb->query.="	vTracking	 ='".$this->request['tracking']."'";
			 $this->obDb->query.="	 WHERE iOrderid_FK='".$this->request['orderid']."'";
			 $this->obDb->updateQuery();
		}
		else
		{
			 $this->obDb->query ="INSERT INTO ".SHIPPINGDETAILS." SET ";
			 $this->obDb->query.="	iOrderid_FK	 ='".$this->request['orderid']."',";
			 $this->obDb->query.="	vShipper		 ='".$this->request['shipper']."',";
			 $this->obDb->query.="	vTracking	 ='".$this->request['tracking']."',";
			 $this->obDb->query.="	tmShipDate	 ='".$timeStamp."'";
			 $this->obDb->updateQuery();
		}
		if(isset($this->request['notify']))
		{
			$this->m_sendConfirmation();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."order/adminindex.php?action=orders.status&invoice=".$this->request['invoice']);	
	}#ef

	function m_sendConfirmation()
	{
		$this->comFunc->obDb=$this->obDb;
		$this->obDb->query= "SELECT vFirstName,vLastName,vEmail  FROM ".ORDERS." WHERE  iOrderid_PK = '".$this->request['orderid']."'";
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
	//	$uniqID=uniqid (3);
		if($rCount>0) 
		{
			$name =$this->libFunc->m_displayContent($qryResult[0]->vFirstName)." ".$this->libFunc->m_displayContent($qryResult[0]->vLastName);
			$this->ObTpl=new template();
			$this->ObTpl->set_file("TPL_MAIL_FILE",$this->mailTemplate);
			$this->ObTpl->set_var("TPL_VAR_NAME",$name);
			$this->ObTpl->set_var("TPL_VAR_INVOICE",$this->request['invoice']);
			$this->ObTpl->set_var("TPL_VAR_METHOD",$this->request['shipper']);
			$this->ObTpl->set_var("TPL_VAR_TRACKNUM",$this->request['tracking']);
			$orderUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=checkout.receipt&mode=".$this->request['orderid']);
			$this->ObTpl->set_var("TPL_VAR_URL",$orderUrl);
			$this->ObTpl->set_var("TPL_VAR_MAILFOOTER",$this->comFunc->m_mailFooter());
			$message=$this->ObTpl->parse("return","TPL_MAIL_FILE");

			$obMail = new htmlMimeMail();
			$obMail->setReturnPath(ADMIN_EMAIL);
			$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");
			#11-05-07
			$obMail->setSubject("Your order from ".SITE_NAME." has shipped!");
			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);
			$obMail->buildMessage();
			$result = $obMail->send(array($qryResult[0]->vEmail));
		}			
	}

}#CLASS ENDS
?>