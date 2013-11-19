<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/



class c_commonFunctions
{
	#CONSTRUCTOR
	function c_commonFunctions()
	{
		$this->libFunc=new c_libFunctions();
	}
	
	function checkDatabase()
	{
		$result = @mysql_pconnect($this->db_host.":".$this->db_port, $this->db_user, $this->db_password);
		
		if(mysql_errno() == 1203)
		{
		  // 1203 == ER_TOO_MANY_USER_CONNECTIONS (mysqld_error.h)
		  print "Your request could not be processed due to high server traffic.<br /><br />&nbsp;Please try refreshing the page again, by using your browser's refresh/ reload button, or <a href='javascript:window.location.reload();'>Click Here</a> to refresh the page.";
		  exit;
		}
		
		if(! $result)
		{
			return "\n".mysql_error(); 
		}
		$result=mysql_select_db($this->db_name);
		if(! $result)
		{
			return "\n".mysql_error(); 
		}
		
		return 1;
	}
  function m_checkCustomerType(){
		 mysql_connect(DATABASE_HOST.":".DATABASE_PORT,DATABASE_USERNAME,DATABASE_PASSWORD);
  		 mysql_select_db(DATABASE_NAME);
		if(isset($_SESSION['userid']))
		{
		$query="SELECT vRetail FROM ".CUSTOMERS." WHERE iCustmerid_PK='".$_SESSION['userid']."'";
		$customertype = mysql_query($query);
		$customertype = mysql_fetch_row($customertype);
	
		if ($customertype[0]=='t')
			{
			return 1;   
			}else{
			return 0; 
			}
		}else{
			return 0;
		}
	}
	function m_checkPrefix()
	{
	/* commented by 621 due to no permission to delete tables
		$testTable=$this->testTable;
		$result=mysql_query("CREATE TABLE $testTable (`test` text)");
		if(! $result)
		{
			return "\n".mysql_error()." (Please check Database prefix)"; 
		}
		else
		{
			$result=mysql_query("DROP TABLE $testTable");
		}
		*/
		
		return 1;
	}

    function m_postageCityCountry($countryid){
        $this->obDb->query = "SELECT * FROM  ".STATES." WHERE `iCountryID_FK` = '$countryid' ORDER BY `vStateName` ASC";
        $state = $this->obDb->fetchQuery();
        foreach($state as $stateinfo) {
            if (empty($stateinfo->iStateId_PK)){
                echo("<option value='0'>Other</option>");
            } else {
                echo("<option value='".$stateinfo->iStateId_PK."'>".$stateinfo->vStateName."</option>");
            }
        }
        break;
    }

	function m_recalculate_postage($countryid, $stateid='0'){

        if(DEFAULT_POSTAGE_METHOD=='zones') {			
    		$this->obDb->query = "SELECT * FROM  ".POSTAGEZONE." as pz INNER JOIN ".POSTAGEZONEDETAILS." as pzd ON pz.iZoneId = pzd.iZoneId and pz.vCountryId = '".$countryid."' and pzd.fMinweight <= ".$_SESSION['cartweight']."  and pzd.fMaxweight >= ".$_SESSION['cartweight'];
    		
    		$zonelist = $this->obDb->fetchQuery();  // list of zones
    		if($zonelist[0]->fCost != ""){
    			$postageRecalculated[0] = $zonelist[0]->fCost;
    			$postageRecalculated[1] = $zonelist[0]->fSpecialDelivery;
    		}
    		
    		if($zonelist[0]->iZoneId == ""){
    			//Find if Country exists in rest of World....
    			$this->obDb->query = "SELECT iZoneId,vCountryId FROM  ".POSTAGEZONE ;
    			$restOfCountry = $this->obDb->fetchQuery();	
    			foreach($restOfCountry as $k=>$v){
    				$countrylist = explode(",",$v->vCountryId);
    				if (in_array($countryid, $countrylist)) {
    					$this->obDb->query = "SELECT * FROM  ".POSTAGEZONEDETAILS." WHERE iZoneId = '".$v->iZoneId."' and fMinweight <= ".$_SESSION['cartweight']." and fMaxweight >= ".$_SESSION['cartweight'];
    					$restOfCountryDetails = $this->obDb->fetchQuery();
    					$postageRecalculated[0] = $restOfCountryDetails[0]->fCost;
    					$postageRecalculated[1] =$restOfCountryDetails[0]->fSpecialDelivery;
    					break;
    				}
    			}
    			
            }
		} elseif (DEFAULT_POSTAGE_METHOD=='cities'){
		    $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." as pz INNER JOIN ".POSTAGECITYDETAILS." as pzd ON pz.iCityId = pzd.fCityId and pz.vCountryId = '".$countryid."' and pz.vStateId = '".$stateid."' and pzd.fMinweight <= ".$_SESSION['cartweight']."  and pzd.fMaxweight >= ".$_SESSION['cartweight'];
            
            $citylist = $this->obDb->fetchQuery();  // list of zones
            if($citylist[0]->fCost != ""){
                $postageRecalculated[0] = $ctylist[0]->fCost;
                $postageRecalculated[1] = $citylist[0]->fSpecialDelivery;
            }
            
            if($citylist[0]->iCityId == ""){
                //Find if Country exists in rest of World....
                $this->obDb->query = "SELECT iCityId,vCountryId,vStateId FROM  ".POSTAGECITY ;
                $restOfCountry = $this->obDb->fetchQuery(); 
                foreach($restOfCountry as $k=>$v){
                    $countrylist = explode(",",$v->vCountryId);
                    if (in_array($countryid, $countrylist)) {
                        $this->obDb->query = "SELECT * FROM  ".POSTAGECITYDETAILS." WHERE fCityId = '".$v->iCityId."' and fMinweight <= ".$_SESSION['cartweight']." and fMaxweight >= ".$_SESSION['cartweight'];
                        $restOfCountryDetails = $this->obDb->fetchQuery();
                        $postageRecalculated[0] = $restOfCountryDetails[0]->fCost;
                        $postageRecalculated[1] =$restOfCountryDetails[0]->fSpecialDelivery;
                        break;
                    }
                }
                
            }
		}
		return $postageRecalculated;
	 }

  #FUNCTION WILL RETURN OPTIONS ATTACHED WITH PRODUCTID
	function m_getOptions($kit,$selectedarray="",$selectedOrderidArray="",$adminSection=0)
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_OPTFILE",THEMEPATH."ecom/templates/main/options.tpl.htm");
		$this->ObTpl->set_block("TPL_VAR_OPTFILE","TPL_MAINOPTIONS_BLK","optionsmain_blk");
		$this->ObTpl->set_block("TPL_MAINOPTIONS_BLK","TPL_OPTIONS_BLK","options_blk");
		$this->ObTpl->set_block("TPL_OPTIONS_BLK","TPL_OPTIONIMG_BLK","optimg_blk");
		$this->ObTpl->set_block("TPL_OPTIONS_BLK","TPL_OPTIONVALUE_BLK","optvalue_blk");
		$this->ObTpl->set_var("optionsmain_blk","");
		$this->ObTpl->set_var("options_blk","");
		$this->ObTpl->set_var("optvalue_blk","");
		$this->ObTpl->set_var("optimg_blk","");	
        $this->ObTpl->set_var("LANG_VAR_OPTIONS", LANG_OPTIONS);
		$this->obDb->query ="SELECT iOptionid,iOptionid_PK FROM ".PRODUCTOPTIONS." WHERE iProductid_FK ='".$this->productId."' ORDER BY iSort";
		$rsPoptions=$this->obDb->fetchQuery();
		$optPCount=$this->obDb->record_count;
		if($optPCount>0)
		{
			for($i=0;$i<$optPCount;$i++)
			{
				$this->obDb->query ="SELECT vName,vDescription,iOptionid_PK,iState FROM ".OPTIONS." WHERE iOptionid_PK='".$rsPoptions[$i]->iOptionid."'";
					$rsOptions=$this->obDb->fetchQuery();
					$optCount=$this->obDb->record_count;
					if($optCount>0)
					{
						for($j=0;$j<$optCount;$j++)
						{
						#PRODUCT(TABLE) OPTION ID
							if($kit==1)
							{
								$this->ObTpl->set_var("TPL_VAR_FIELDNAME","option_kit_".$this->productId."_".$rsPoptions[$i]->iOptionid);	
							}
							else
							{
								$this->ObTpl->set_var("TPL_VAR_FIELDNAME","option_".$this->productId."_".$rsPoptions[$i]->iOptionid);	
							}
							if($rsOptions[$j]->iState){
								$this->ObTpl->set_var("TPL_VAR_MANDATORY_CLASS","mandatory");
								$this->ObTpl->set_var("STAR","*");
							}else{
								$this->ObTpl->set_var("TPL_VAR_MANDATORY_CLASS","formField");
								$this->ObTpl->set_var("STAR","");
							}

							$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsOptions[$j]->vDescription));
              $this->obDb->query ="SELECT vItem,vOptSku,iOptionValueid_PK,vImage,iUseInventory,";
              $this->obDb->query.="fPrice,iInventory,iBackorder   FROM ".OPTIONVALUES." WHERE "; $this->obDb->query.="iOptionid_FK='".$rsOptions[$j]->iOptionid_PK."' ORDER BY iSort";
              $rsOptionvalue=$this->obDb->fetchQuery();

							$optValCount=$this->obDb->record_count;
							
							if($optValCount>0)
							{
								$this->ObTpl->set_var("optvalue_blk","");
								$this->ObTpl->set_var("optimg_blk","");
								$arrOptId=0;
								
								for($k=-1;$k<$optValCount;$k++)
								{
									if($k != "-1"){
										$img=$this->libFunc->m_checkFile($rsOptionvalue[$k]->vImage,"options",$this->libFunc->m_displayContent($rsOptionvalue[$k]->vItem),0);
										if(!$img || $adminSection==1)
										{
											$img=	"";			
										}
									}else{
										$img=	"";
									}
									if($k == "-1"){
										$optName ="";
									}else{
										$optName =$this->libFunc->m_displayContent($rsOptionvalue[$k]->vItem);
									}
									if($k == "-1"){
										$optName.="----Please Select----";
									}
									if($k != "-1"){
										if($rsOptionvalue[$k]->vOptSku!="")
										{
											$optName.=" - ".($rsOptionvalue[$k]->vOptSku);
										}
										
										if($rsOptionvalue[$k]->fPrice>0)
										{
											$optName.=" - ". LANG_ADD . CONST_CURRENCY.number_format($rsOptionvalue[$k]->fPrice,2);
										}
										if($rsOptionvalue[$k]->iInventory>=0)
										{
											if(($rsOptionvalue[$k]->iUseInventory > 0) && (STOCK_CHECK==1)){ 
												$optName.=" (".$rsOptionvalue[$k]->iInventory." ".LANG_QUANTITY.")"; 
											}
										}
									}
									$this->ObTpl->set_var("TPL_VAR_SELECTED","");
									if($k != "-1"){
										if(count($selectedarray)>0 && is_array($selectedarray) && array_key_exists($rsOptions[$j]->iOptionid_PK,$selectedarray))
										{
											if($rsOptionvalue[$k]->iOptionValueid_PK==$selectedarray[$rsOptions[$j]->iOptionid_PK])
											{
												$this->ObTpl->set_var("TPL_VAR_SELECTED","selected");
											}
											else
											{
												$this->ObTpl->set_var("TPL_VAR_SELECTED","");
											}
										}
									}
								
									if(count($selectedOrderidArray)>0 && is_array($selectedOrderidArray) && array_key_exists($rsOptions[$j]->iOptionid_PK,$selectedOrderidArray))
									{
										$this->ObTpl->set_var("TPL_VAR_PRODUCTID",$this->productId);
										$this->ObTpl->set_var("TPL_VAR_OPTIONID",$rsOptions[$j]->iOptionid_PK);	
										$this->ObTpl->set_var("TPL_VAR_ORDEROPTIONID",$selectedOrderidArray[$rsOptions[$j]->iOptionid_PK]);
									}
									else
									{		
										$this->ObTpl->set_var("TPL_VAR_PRODUCTID","");
										$this->ObTpl->set_var("TPL_VAR_OPTIONID","");
										$this->ObTpl->set_var("TPL_VAR_ORDEROPTIONID","");
									}
									$this->ObTpl->set_var("TPL_VAR_OPTNAME",$optName);
									if($k != "-1"){
										$this->ObTpl->set_var("TPL_VAR_OPTID",$rsOptionvalue[$k]->iOptionValueid_PK);
									}else{
										$this->ObTpl->set_var("TPL_VAR_OPTID","");
									}
									$this->ObTpl->set_var("TPL_VAR_IMGURL",$img);	
																			
									$this->ObTpl->set_var("TPL_VAR_ID",$arrOptId);	
									$this->ObTpl->parse("optimg_blk","TPL_OPTIONIMG_BLK",true);	
									$this->ObTpl->parse("optvalue_blk","TPL_OPTIONVALUE_BLK",true);
									$arrOptId++;
									
								}#END FOR K LOOP
								if($arrOptId>0)
								{
									$this->ObTpl->parse("options_blk","TPL_OPTIONS_BLK",true);	
								}
							}#END IF VALUE OPT
						
						}#END FOR J LOOP
					}#END IF OPT
			}#END FOR I LOOP
			if($optCount>0)
			{
				$this->ObTpl->parse("optionsmain_blk","TPL_MAINOPTIONS_BLK");
			}
		}#END IF

		return $this->ObTpl->parse("optfile_blk","TPL_VAR_OPTFILE");	

	}#ENF OPTIONS FUNCTION

	#FUNCTION WILL RETURN CHOICES RELATED TO PRODUCT
	function m_getChoices($selectedarray="")
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_CHOICEFILE",THEMEPATH."ecom/templates/main/choices.tpl.htm");
		$this->ObTpl->set_block("TPL_VAR_CHOICEFILE","TPL_MAINCHOICE_BLK","choicemain_blk");
	
		$this->ObTpl->set_var("choicemain_blk","");
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);
		$this->obDb->query ="SELECT C.iChoiceid_PK,vName,vDescription,fPrice,vImage,";
		$this->obDb->query.="vType,iInventory,iUseInventory,iBackorder ";
		$this->obDb->query.=" FROM ".PRODUCTCHOICES." P,".CHOICES."  C ";
		$this->obDb->query.="WHERE C.iChoiceid_PK=P.iChoiceid AND iProductid_FK ='".$this->productId."'";
		$this->obDb->query.=" AND iState=1 order by iSort";
		$rsOptions=$this->obDb->fetchQuery();
		$optPCount=$this->obDb->record_count;
		if($optPCount>0)
		{
			for($i=0;$i<$optPCount;$i++)
			{
				if(count($selectedarray)>0 && is_array($selectedarray) && array_key_exists($rsOptions[$i]->iChoiceid_PK,$selectedarray))
				{
					$filedValue=$this->libFunc->m_displayContent($selectedarray[$rsOptions[$i]->iChoiceid_PK]);
				}
				else
				{
					$filedValue="";
				}
				$this->ObTpl->set_var("TPL_VAR_FIELDNAME","choice_".$this->productId."_".$rsOptions[$i]->iChoiceid_PK);
				$title="";
				if($rsOptions[$i]->fPrice>0)
				{
					$title=" (".LANG_ADD.CONST_CURRENCY.number_format($rsOptions[$i]->fPrice,2).")";
				}
				$this->ObTpl->set_var("TPL_VAR_TITLE",$this->libFunc->m_displayContent($rsOptions[$i]->vDescription).$title);
				$this->ObTpl->set_var("TPL_VAR_ID",$rsOptions[$i]->iChoiceid_PK);

				if($rsOptions[$i]->vType =='qty')
				{
					$this->ObTpl->set_var("TPL_VAR_CHOICEFIELD","<input type='text' id='choiceqty_".$this->productId."_".$rsOptions[$i]->iChoiceid_PK."'  name='choiceqty_".$this->productId."_".$rsOptions[$i]->iChoiceid_PK."' value='".$filedValue."' class='formFieldShort'  maxlength='3' />");
				}
				elseif($rsOptions[$i]->vType =='checkbox')
				{
					
					
					if($selectedarray[$rsOptions[$i]->iChoiceid_PK]==1)
					{
						$this->ObTpl->set_var("TPL_VAR_CHOICEFIELD","<input type='checkbox' name='choice_".$this->productId."_".$rsOptions[$i]->iChoiceid_PK."' value='1' class='formFieldShort' checked />");
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_CHOICEFIELD","<input type='checkbox' name='choice_".$this->productId."_".$rsOptions[$i]->iChoiceid_PK."' value='1' class='formFieldShort' />");
					}					
				}
				elseif($rsOptions[$i]->vType =='textarea')
				{
					$this->ObTpl->set_var("TPL_VAR_CHOICEFIELD","<textarea  name='choice_".$this->productId."_".$rsOptions[$i]->iChoiceid_PK."' class='formField'>".$filedValue."</textarea>");
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_CHOICEFIELD","<input type='text' name='choice_".$this->productId."_".$rsOptions[$i]->iChoiceid_PK."' value='".$filedValue."' class='formField'  maxlength='10' />");
				}

				#DISPLAY IMAGE
				if(!empty($rsOptions[$i]->vImage))
				{
					 $img=$this->libFunc->m_checkFile($rsOptions[$i]->vImage,"options",$this->libFunc->m_displayContent($rsOptions[$i]->vDescription));
					if($img)
					{
						$this->ObTpl->set_var("TPL_VAR_IMAGE",$img);
					}
					else
					{
						$this->ObTpl->set_var("TPL_VAR_IMAGE","");
					}
				}
				else
				{
					$this->ObTpl->set_var("TPL_VAR_IMAGE","");
				}
  
				if($rsOptions[$i]->iUseInventory==1 && $rsOptions[$i]->iBackorder!=1 && $rsOptions[$i]->iInventory<1)
				{
					$noparse=1;
				}
				else
				{
					$this->ObTpl->parse("choicemain_blk","TPL_MAINCHOICE_BLK",true);
				}
			}#END FOR I LOOP
			
		}#END IF

		return $this->ObTpl->parse("return","TPL_VAR_CHOICEFILE");	

	}#ENF CHOICES FUNCTION


	#FUNCTION WILL RETURN VOLUME DISCOUNT RELATED TO PRODUCT
	function m_getVolDiscount()
	{
		$libFunc=new c_libFunctions();

		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_VAR_DISCOUNTFILE",THEMEPATH."ecom/templates/main/volDiscount.tpl.htm");
		$this->ObTpl->set_block("TPL_VAR_DISCOUNTFILE","TPL_MAINVOLDISCOUNT_BLK","mainvoldiscount_blk");
		$this->ObTpl->set_block("TPL_MAINVOLDISCOUNT_BLK","TPL_VOLDISCOUNT_BLK","voldiscount_blk");
		$this->ObTpl->set_var("mainvoldiscount_blk","");
		$this->ObTpl->set_var("voldiscount_blk","");
		$this->ObTpl->set_var("TPL_VAR_CURRENCY",CONST_CURRENCY);

		 $this->obDb->query ="SELECT iRangea,iRangeb,fDiscount FROM ".VDISCOUNTS." WHERE iProductid_FK ='".$this->productId."'";
		$rsOptions=$this->obDb->fetchQuery();
		$optPCount=$this->obDb->record_count;
		if($optPCount>0)
		{
			for($i=0;$i<$optPCount;$i++)
			{
				$this->ObTpl->set_var("TPL_VAR_RANGEA",$rsOptions[$i]->iRangea);
				if(intval($rsOptions[$i]->iRangeb)==0){
					$rsOptions[$i]->iRangeb=$rsOptions[$i]->iRangea.'+';
				}
				$this->ObTpl->set_var("TPL_VAR_RANGEB",$rsOptions[$i]->iRangeb);				
				$this->ObTpl->set_var("TPL_VAR_PRICE",number_format($rsOptions[$i]->fDiscount,2));
			
				$this->ObTpl->parse("voldiscount_blk","TPL_VOLDISCOUNT_BLK",true);
			}#END FOR I LOOP
			$this->ObTpl->parse("mainvoldiscount_blk","TPL_MAINVOLDISCOUNT_BLK");
		}#END IF

		return $this->ObTpl->parse("return","TPL_VAR_DISCOUNTFILE");	

	}#ENF DISCOUNT FUNCTION

	#FUNCTION DISPLAY PRODUCT OPTIONS IN CART
	function m_dspCartProductOptions()
	{
		$strOptions="";
		 $this->obDb->query ="SELECT iOptId_FK,vName,vDescription,vOptVal FROM ".OPTIONS.", ".TEMPOPTIONS." WHERE iOptId_FK=iOptionid_PK AND iTmpCartId_FK='".$this->cartId."'";
		$rsOptions=$this->obDb->fetchQuery();
		$optCount=$this->obDb->record_count;
		if($optCount>0)
		{
			for($i=0;$i<$optCount;$i++)
			{
				 if(!empty($rsOptions[$i]->vOptVal)){
					 $this->obDb->query ="SELECT vItem,vOptSku,fPrice FROM ".OPTIONVALUES.", ".TEMPOPTIONS." WHERE  iOptionValueid_PK=vOptVal AND vOptVal ='".$rsOptions[$i]->vOptVal."' AND iOptionid_FK='".$rsOptions[$i]->iOptId_FK."' ";
					$rsOptionValue=$this->obDb->fetchQuery();
					$strOptions.="<input type='hidden' name='option_".$this->productId."_".$rsOptions[$i]->iOptId_FK."' value='".$rsOptions[$i]->vOptVal."' />";
					#MODIFIED on 29-03-07 -Changed $rsOptionValue[$i]->vItem to $rsOptionValue[0]->vItem
					$strOptions.=$this->libFunc->m_displayContent($rsOptions[$i]->vDescription).": ".$this->libFunc->m_displayContent($rsOptionValue[0]->vItem)." Sku code: ".$this->libFunc->m_displayContent($rsOptionValue[0]->vOptSku);

					if(!empty($rsOptionValue[0]->fPrice))
					{
						$this->price+=$rsOptionValue[0]->fPrice;
						$strOptions.="&nbsp;(".LANG_ADD.CONST_CURRENCY.number_format($rsOptionValue[0]->fPrice,2).")";
					}
					$strOptions.="<br />";
				 }
			}
		}
		return $strOptions;
	}
	#FUNCTION DISPLAY PRODUCT CHOICES IN CART
	function m_dspCartProductChoices()
	{
		$strChoices="";
		$this->obDb->query ="SELECT iChoiceid_PK,vDescription,fPrice,iQty  FROM ".CHOICES.", ".TEMPCHOICES." WHERE iTmpChoiceId_FK=iChoiceid_PK AND iTmpCartId_FK='".$this->cartId."' ";
		$rsChoices=$this->obDb->fetchQuery();
		$optCount=$this->obDb->record_count;
		if($optCount>0)
		{
			for($i=0;$i<$optCount;$i++)
			{
				$totalfPrice=$rsChoices[$i]->fPrice;
				//vChoiceVal removed from function as there is no field with that name. Original authors are intentions are unknown as this time
				//if($rsChoices[$i]->iQty==1)
				//{
				//	$totalfPrice=$rsChoices[$i]->vChoiceVal*$rsChoices[$i]->fPrice;
				//}
				$strChoices.="<input type='hidden' name='choice_".$this->productId."_".$rsChoices[$i]->iChoiceid_PK."' value='".$rsChoices[$i]->vChoiceVal."' />";
				$strChoices.=$this->libFunc->m_displayContent($rsChoices[$i]->vDescription).": ".nl2br($this->libFunc->m_displayContent($rsChoices[$i]->vChoiceVal));
				if(!empty($rsChoices[$i]->fPrice))
				{
					$strChoices.=" (".LANG_ADD.CONST_CURRENCY.number_format($rsChoices[$i]->fPrice,2);
					//if($rsChoices[$i]->iQty==1)
					//{
					//	$strChoices.="&nbsp;Total ".CONST_CURRENCY.number_format($totalfPrice,2).")";
					//	$this->price+=$totalfPrice;
					//}
					//else
					//{
						$this->price+=$rsChoices[$i]->fPrice;
						$strChoices.=")";
					//}
				}
				$strChoices.="<br />";
			}
		}
		return $strChoices;
	}

	#FUNCTION TO GET VOLUME DISCOUNT
	function m_dspCartProductVolDiscount($qty)
	{
		 $this->obDb->query ="SELECT fDiscount FROM ".VDISCOUNTS." WHERE iRangea<='$qty' AND  (iRangeb>='$qty' OR iRangeb='0') AND iProductId_FK='".$this->productId."'";
		$rsDiscount=$this->obDb->fetchQuery();
		return $rsDiscount[0]->fDiscount;
	}

	#FUNCTION TO GET POSTAGE PRICE(EXCEPT postage code & special rate)
	function m_postagePrice()
	{
		$this->obDb->query ="SELECT vField1,vField2,vField3,fBaseRate FROM  ".POSTAGE.",".POSTAGEDETAILS." WHERE iPostId_PK=iPostId_FK AND vKey='".DEFAULT_POSTAGE_METHOD."'";
		$rsPostage=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;

		if(DEFAULT_POSTAGE_METHOD=='flat')
		{
			return $rsPostage[0]->vField1;
		}#END FLAT
		elseif(DEFAULT_POSTAGE_METHOD=='percent')
		{
			$postPrice=($rsPostage[0]->vField1*$this->grandTotal)/100;
			if($rsPostage[0]->fBaseRate>$postPrice)
			{
				return $rsPostage[0]->fBaseRate;
			}
			else
			{
				return $postPrice;
			}
		}#END PERCENT
		elseif(DEFAULT_POSTAGE_METHOD=='range')
		{

			for($i=0;$i<$rsCount;$i++)
			{
				#IF POSTAGE IS UNLIMITED
				//echo $rsPostage[$i]->vField1."|".$this->grandTotal."|".$rsPostage[$i]->vField2;
				if($rsPostage[$i]->vField1<=$this->grandTotal && ($rsPostage[$i]->vField2=='0' || $rsPostage[$i]->vField2=='unlimited'))
				{
					return $rsPostage[$i]->vField3;
				}
				#CHECKING RANGES
				if($rsPostage[$i]->vField1<=$this->grandTotal && $rsPostage[$i]->vField2>=$this->grandTotal)
				{
					return $rsPostage[$i]->vField3;
				}
			}	#ENF FOR LOOP
			
		}#END RANGE
		elseif(DEFAULT_POSTAGE_METHOD=='peritem') {
				return $rsPostage[0]->vField1+$rsPostage[0]->vField2*($this->totalQty-$this->postageQty-1);
		}#END PERITEM
		elseif(DEFAULT_POSTAGE_METHOD=='codes')
		{
			return $rsPostage[0]->fBaseRate;			
		}#END PERITEM
		else
		{
			return 0;
		}

	}#END POSTAGE CALCULATION METHOD

	#FUNCTION TO GET POSTAGE PRICE(postage code )
	function m_postageCodePriceMultiplyQty()
	{
		$this->obDb->query ="SELECT vField2 FROM ".POSTAGEDETAILS." WHERE iPostDescId_PK ='".$this->postageId."'";
		$rsPostage=$this->obDb->fetchQuery();
		return $rsPostage[0]->vField2*$this->qty;
	}#END FUNCTION
	function m_postageCodePrice()
	{
		$this->obDb->query ="SELECT vField2 FROM ".POSTAGEDETAILS." WHERE iPostDescId_PK ='".$this->postageId."'";
		$rsPostage=$this->obDb->fetchQuery();
		return $rsPostage[0]->vField2;
	}#END FUNCTION


	#FUNCTION DISPLAY PRODUCT OPTIONS IN CART
	function m_dspCartProductKitOptions()
	{
		$strOptions="";
		 $this->obDb->query ="SELECT vName,vDescription,vOptVal FROM ".OPTIONS.", ".TEMPOPTIONS." WHERE iOptId_FK=iOptionid_PK AND iProdId_Fk='".$this->kitProductId."' AND iTmpCartId_FK='".$this->cartId."'";
		$rsOptions=$this->obDb->fetchQuery();
		$optCount=$this->obDb->record_count;
		if($optCount>0)
		{
			for($i=0;$i<$optCount;$i++)
			{
				 $this->obDb->query ="SELECT vItem,fPrice FROM ".OPTIONVALUES.", ".TEMPOPTIONS." WHERE  iOptionValueid_PK=vOptVal AND vOptVal ='".$rsOptions[$i]->vOptVal."'";
				$rsOptionValue=$this->obDb->fetchQuery();
				$strOptions.=$rsOptions[$i]->vDescription.": ".$rsOptionValue[$i]->vItem;
				if(!empty($rsOptionValue[0]->fPrice))
				{
					$this->price+=$rsOptionValue[$i]->fPrice;
					$strOptions.=" (".LANG_ADD.CONST_CURRENCY.number_format($rsOptionValue[$i]->fPrice,2).")";
				}
				$strOptions.="<br />";
			}
		}
		return $strOptions;
	}#END FUNCTION
	
	#FUNCTION DISPLAY GIFTWRAP IN CART
	function m_dspGiftWrap($giftid,$prodid=0)
	{
		$libFunc=new c_libFunctions();
		$editUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.giftwrap&mode=".$prodid);
		$removeUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.removeGift&mode=".$prodid);
		$strGift="";
		 $this->obDb->query ="SELECT vTitle,fPrice FROM ".GIFTWRAPS." WHERE iGiftwrapid_PK ='".$giftid."'";
		$rsGift=$this->obDb->fetchQuery();
		$giftCount=$this->obDb->record_count;
		if($giftCount>0)
		{
			$this->price+=$rsGift[0]->fPrice;
			$strGift=$rsGift[0]->vTitle.LANG_ADD.CONST_CURRENCY.number_format($rsGift[0]->fPrice,2);
			$strGift.=" <a href=".$editUrl.">Edit</a> - <a href=".$removeUrl.">Remove</a><br />";
		}
		return $strGift;
	}#END FUNCTION


	#FUNCTION TO CALCULATE DISCOUNT ACCORDING TO DISCOUNT CODES
	function m_calculateDiscount($discountCode,$totalorder)
	{
		$libFunc=new c_libFunctions();
		$curTime=time();
		
		$this->obDb->query ="SELECT fDiscount,fFixamount,fMinimum  FROM ".DISCOUNTS." WHERE vCode='".$discountCode."' AND tmStartDate<$curTime AND tmEndDate>$curTime AND iState=1";
		$rsDiscount=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		$returnstring = "";
		$discountvalue =0;
		if($rsCount>0)
		{
			if($rsDiscount[0]->fDiscount>0){
		    $offertype="percent";
		    $discountvalue=	$rsDiscount[0]->fDiscount;
			}
			elseif($rsDiscount[0]->fFixamount>0){
			$offertype="fix";	
			$discountvalue=$rsDiscount[0]->fFixamount;
			}
			$returnstring = $discountvalue.",".$offertype.",".$rsDiscount[0]->fMinimum;
		  	return $returnstring;
		}
		else 
		{	
			return 0;
		}
		
	}#END FUNCTION

	#FUNCTION TO CALCULATE GIFT CERTIFICATES ACCORDING TO DISCOUNT CODES
	function m_calculateGiftCertPrice($giftcode)
	{
		$libFunc=new c_libFunctions();
//		$curTime=time();
		$this->obDb->query ="SELECT fRemaining FROM ".GIFTCERTIFICATES." WHERE vGiftcode ='".$giftcode."'";
		$rsDiscount=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		if($rsCount>0)
		{
			return $rsDiscount[0]->fRemaining;
		}
		else 
		{	
			return 0;
		}
		
	}#END FUNCTION

	#FUNCTION TO CALCULATE PROMOTION DISCOUNT ON SUBTOTAL
	function m_calculatePromotionDiscount($subTotal)
	{
		$libFunc=new c_libFunctions();
		$this->flatDiscount=0;
		$this->rangeDiscount=0;

		$curTime=time();
		$this->obDb->query ="SELECT vPromotype,fDiscount,iRangefield  FROM ".PROMOTIONS." WHERE tmStartDate<='".$curTime."' AND tmEndDate>='".$curTime."' AND fCarttotal<='".$subTotal."' ORDER BY iSort,fCarttotal DESC";
		$rsDiscount=$this->obDb->fetchQuery();
		 $rsCount=$this->obDb->record_count;
		if($rsCount>0)
		{
			for($i=0;$i<$rsCount;$i++)
			{
				if($rsDiscount[$i]->vPromotype=='free')
				{
					$this->PromotionDesc="Free P&amp;P";
					$_SESSION['freeShip']=1;
					return 0;
				}
				elseif($rsDiscount[$i]->vPromotype=='flat')
				{
					$_SESSION['freeShip']=0;
					$this->flatDiscount=$rsDiscount[$i]->fDiscount*$subTotal/100;
					$this->PromotionDesc="";
					return $this->flatDiscount;
				}
				elseif($rsDiscount[$i]->vPromotype=='range')
				{
					$_SESSION['freeShip']=0;
					$this->PromotionDesc=$rsDiscount[$i]->iRangefield;
					$this->rangeDiscount=$rsDiscount[$i]->fDiscount*$subTotal/100;
					return $this->rangeDiscount;
				}
			}

		}#end rcount
		else
		{
			$_SESSION['freeShip']=0;	
			return -1;
		}
		
	}#END FUNCTION

	#FUNCTION WILL RETURN NAME ACCORDING TO POSTAGE VALUE
	function m_paymentMethod($postValue,$cod=0)
	{
		if($postValue=='cc')
		{
			return 'Credit/Debit Card';
		}
		elseif($postValue=='cc_phone')
		{
			return 'Credit/Debit Card by phone';
		}
		elseif($postValue=='eft')
		{
			return 'E-Check (EFT)';
		}
		elseif($postValue=='paypal')
		{
			return 'PayPal';
		}
		elseif($postValue=='secpay')
		{
			return 'SECPay';
		}
		elseif($postValue=='securetrading') 
		{
			return 'SecureTrading';
		}
        #(BEGIN) SAGEPAY INTEGRATION
        elseif($postValue=='sagepayform')
		{
			return 'Sagepay';
		}
        #(END) SAGEPAY INTEGRATION
		elseif($postValue=='hsbc')
		{
			return 'HSBC';
		}
		elseif($postValue=='worldpay')
		{
			return 'Worldpay';
		}
		elseif($postValue=='barclay')
		{
			return 'Barclay';
		}
		elseif($postValue=='mail')
		{
			return 'Cheque';
		}
		elseif($postValue=='cod')
		{
			return 'Cash on delivery - Additional '.CONST_CURRENCY.$cod;
		}
		else
		{
			return 'None';
		}

	}#END FUNCTION

	#FUNCTION SEND EMAILS FOR REGISTRATIONS	
	function m_sendDetails($email,$pass,$admin=0)
	{
		$libFunc=new c_libFunctions();
		$this->obDb->query= "SELECT vFirstName,vLastName,vEmail,vPassword  FROM ".CUSTOMERS." WHERE vEmail = '".$email."'";
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
	//	$uniqID=uniqid (3);
		$message="";
		if($rCount>0) 
		{
			if($admin==1)
			{
				$subject	 ="Login details from ".SITE_NAME;
				$heading	 ="Please find your requested login details from ".SITE_NAME." below.";
			}
			else
			{
				$subject	 ="Welcome to ".SITE_NAME;
				$heading	 ="Thank you for signing up to ".SITE_NAME.". Please find your login details below.";
			}
			$message.="<br /><a href='malto:".ADMIN_EMAIL."'>".ADMIN_EMAIL."</a>";
			$message ="========================================<br />";
			$message.=$subject."<br />";
			$message.="========================================<br />";
			$message.="Hi ".$this->libFunc->m_displayContent($qryResult[0]->vFirstName)." ".$this->libFunc->m_displayContent($qryResult[0]->vLastName).",";
			$message.="<br /><br />".$heading;
			$message.="<br /><br>Username:&nbsp;".$qryResult[0]->vEmail;
			$message.="<br />Password:&nbsp;".$pass;
			$message.="<br /><br>Kind Regards";
			$message.="<br /><a href='".SITE_URL."'>".SITE_NAME."</a>";
			$message.="<br />".SITE_PHONE;
			$message.="<br /><a href='mailto:".ADMIN_EMAIL."'>".ADMIN_EMAIL."</a>";

			$obMail = new htmlMimeMail();
			$obMail->setReturnPath(ADMIN_EMAIL);
			$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");

			$obMail->setSubject($subject);

			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);
			$obMail->buildMessage();
			$result = $obMail->send(array($qryResult[0]->vEmail));
		}			
	}

	#FUNCTION SEND EMAILS FOR REGISTRATIONS	To TRADE CUSTOMERS
	function m_sendDetails_trade($email,$pass,$admin=0)
	{
		$libFunc=new c_libFunctions();
		$this->obDb->query= "SELECT vFirstName,vLastName,vEmail,vPassword  FROM ".CUSTOMERS." WHERE vEmail = '".$email."'";
		$qryResult = $this->obDb->fetchQuery();
		$rCount=$this->obDb->record_count;
		
	//	$uniqID=uniqid (3);
		$message="";
		if($rCount>0) 
		{
			if($admin==1)
			{
				$subject	 ="Login details from ".SITE_NAME;
				$heading	 ="Please find your requested login details from ".SITE_NAME." below.";
			}
			else
			{
				$subject	 ="Welcome to ".SITE_NAME;
				$heading	 ="Thank you for signing up to ".SITE_NAME.". Please find your login details below.You will be able to login to your account, after being activated by Admin.";
			}
			$message.="<br /><a href='malto:".ADMIN_EMAIL."'>".ADMIN_EMAIL."</a>";
			$message ="========================================<br />";
			$message.=$subject."<br />";
			$message.="========================================<br />";
			$message.="Hi ".$this->libFunc->m_displayContent($qryResult[0]->vFirstName)." ".$this->libFunc->m_displayContent($qryResult[0]->vLastName).",";
			$message.="<br /><br />".$heading;
			$message.="<br /><br>Username:&nbsp;".$qryResult[0]->vEmail;
			$message.="<br />Password:&nbsp;".$pass;
			$message.="<br /><br>Kind Regards";
			$message.="<br /><a href='".SITE_URL."'>".SITE_NAME."</a>";
			$message.="<br />".SITE_PHONE;
			$message.="<br /><a href='mailto:".ADMIN_EMAIL."'>".ADMIN_EMAIL."</a>";

			$obMail = new htmlMimeMail();
			$obMail->setReturnPath(ADMIN_EMAIL);
			$obMail->setFrom(SITE_NAME."<".ADMIN_EMAIL.">");

			$obMail->setSubject($subject);

			$obMail->setCrlf("\n"); //to handle mails in Outlook Express
			$htmlcontent=$message;
			$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
			$obMail->setHtml($htmlcontent,$txtcontent);
			$obMail->buildMessage();
			$result = $obMail->send(array($qryResult[0]->vEmail));
		}			
	}

	#FUNCTION TO DISPLAY FOOTER-COMPANY DETAILS
	function m_mailFooter()
        {
            $mailFooter=""; //LOCAL SCOPE
            $this->libFunc=new c_libFunctions();
            $this->obDb->query ="SELECT vAddress,vCity,vZip,vState,vStateName,vCountry FROM  ".COMPANYSETTINGS;
            $rsCompany=$this->obDb->fetchQuery();
            if(!$this->libFunc->m_isNull(SITE_NAME)){
                $mailFooter.=SITE_NAME."<br />";			
            }	
            if(!$this->libFunc->m_isNull($rsCompany[0]->vAddress)){
                $mailFooter.=nl2br($this->libFunc->m_displayContent($rsCompany[0]->vAddress))."<br />";
            }
            if(!$this->libFunc->m_isNull($rsCompany[0]->vCity)){
                $mailFooter.=$this->libFunc->m_displayContent($rsCompany[0]->vCity)."<br />";
            }
            if($rsCompany[0]->vState>1) {
                $this->obDb->query = "SELECT vStateName FROM ".STATES." where iStateId_PK  = '".$rsCompany[0]->vState."'";
                $row_state = $this->obDb->fetchQuery();
                $mailFooter.=$this->libFunc->m_displayContent($row_state[0]->vStateName)."<br />";
            } elseif(!$this->libFunc->m_isNull($rsCompany[0]->vStateName)) {
                $mailFooter.=$this->libFunc->m_displayContent($rsCompany[0]->vStateName)."<br />";
            }
            if(!$this->libFunc->m_isNull($rsCompany[0]->vZip)){
                $mailFooter.=$this->libFunc->m_displayContent($rsCompany[0]->vZip)."<br />";
            }
            $this->obDb->query = "SELECT vCountryName FROM ".COUNTRY." where iCountryId_PK  = '".$rsCompany[0]->vCountry."'";
            $row_country = $this->obDb->fetchQuery();
            if(!$this->libFunc->m_isNull($row_country[0]->vCountryName)){
                $mailFooter.=$this->libFunc->m_displayContent($row_country[0]->vCountryName)."<br/>";
            }
            if(!$this->libFunc->m_isNull(ADMIN_EMAIL)){
                $mailFooter.="Email: <a href='mailto: ".ADMIN_EMAIL."'>".ADMIN_EMAIL."</a><br />";
            }
            if(!$this->libFunc->m_isNull(SITE_PHONE)){
                $mailFooter.="Tel: ".$this->libFunc->m_displayContent(SITE_PHONE)."<br />";
            }
            if(!$this->libFunc->m_isNull(COMPANY_VATNUMBER)) {
                $mailFooter.="<br/>".VAT_TAX_TEXT." Registration No: ".COMPANY_VATNUMBER."<br />";
            } 

            if(!$this->libFunc->m_isNull(COMPANY_REGISTERNUMBER)) {
                $mailFooter.="Company Registration No.: ".COMPANY_REGISTERNUMBER."<br />";
            } 

            return $mailFooter;
        }

	#FUNCTION TO DISPLAY ORDER RECIPET-ORDER DETAILS
	#FUNCTION DISPLAY PRODUCT OPTIONS IN RECIEPT
	function m_orderProductOptions()
	{
		$this->selectedOptions=array();
		$this->selectedOrderOptionId=array();
		$strOptions="";
		$this->obDb->query ="SELECT iOptionid_PK,iOptionValueid_PK,OO.vName,OO.fPrice,OO.vItem,OO.vOptSku,iOptionid_FK FROM ".ORDEROPTIONS." OO,".OPTIONVALUES." OV WHERE iOptionid=iOptionValueid_PK AND  iProductOrderid_FK='".$this->orderProductId."' AND iOrderid_FK='".$this->orderId."'";
		$rsOptions=$this->obDb->fetchQuery();
		$optCount=$this->obDb->record_count;
		if($optCount>0)
		{
			for($i=0;$i<$optCount;$i++)
			{
				$this->selectedOptions[$rsOptions[$i]->iOptionid_FK]=$rsOptions[$i]->iOptionValueid_PK;
				$this->selectedOrderOptionId[$rsOptions[$i]->iOptionid_FK]=$rsOptions[$i]->iOptionid_PK;
				if(!empty($rsOptions[$i]->vOptSku))
				{
				$sku = "<br/>Option Sku: ".$rsOptions[$i]->vOptSku;
				}
				else
				{
				$sku = "";
				}
				$strOptions.=$rsOptions[$i]->vName.": ".$rsOptions[$i]->vItem.$sku;
				
				if(!empty($rsOptions[$i]->fPrice))
				{
					$this->price+=$rsOptions[$i]->fPrice;
					$strOptions.=" (".LANG_ADD .CONST_CURRENCY.number_format($rsOptions[$i]->fPrice,2).")";
				}
				$strOptions.="<br />";
			}
		}
		return $strOptions;
	}

	#FUNCTION DISPLAY PRODUCT CHOICES IN RECIEPT
	function m_orderProductChoices()
	{
		$this->selectedChoices=array();
		$strChoices="";
		 $this->obDb->query ="SELECT vDescription,iChoiceid_FK,vChoiceValue,fPrice  FROM ".ORDERCHOICES." WHERE iProductOrderid_FK='".$this->orderProductId."' AND iOrderid_FK='".$this->orderId."'";
		$rsChoices=$this->obDb->fetchQuery();
		$optCount=$this->obDb->record_count;
		if($optCount>0)
		{
			for($i=0;$i<$optCount;$i++)
			{
				$this->selectedChoices[$rsChoices[$i]->iChoiceid_FK]=$rsChoices[$i]->vChoiceValue;
				$strChoices.=$rsChoices[$i]->vDescription.": ".$rsChoices[$i]->vChoiceValue;
				if(!empty($rsChoices[$i]->fPrice))
				{
					$this->price+=$rsChoices[$i]->fPrice;
					$strChoices.=" (".LANG_ADD.CONST_CURRENCY.number_format($rsChoices[$i]->fPrice,2).")";
				}
				$strChoices.="<br />";
			}
		}
		return $strChoices;
	}

	#FUNCTION DISPLAY KIT OPTIONS IN RECIEPT
	function m_orderKitProductOptions()
	{
		$this->selectedOptions=array();
		$this->selectedOrderOptionId=array();
		$strOptions="";
		 $this->obDb->query ="SELECT  iOptionid_PK,iOptionValueid_PK,OO.vName,OO.fPrice,OO.vItem,iOptionid_FK FROM ".ORDEROPTIONS." OO,".OPTIONVALUES." OV WHERE iOptionid=iOptionValueid_PK AND  iProductid_FK='".$this->kitProductId."' AND iProductOrderid_FK='".$this->orderProductId."' AND iOrderid_FK='".$this->orderId."'";
		$rsOptions=$this->obDb->fetchQuery();
		$optCount=$this->obDb->record_count;
		if($optCount>0)
		{
			for($i=0;$i<$optCount;$i++)
			{
				$this->selectedOptions[$rsOptions[$i]->iOptionid_FK]=$rsOptions[$i]->iOptionValueid_PK;
				$this->selectedOrderOptionId[$rsOptions[$i]->iOptionid_FK]=$rsOptions[$i]->iOptionid_PK;
				$strOptions.=$rsOptions[$i]->vName.": ".$rsOptions[$i]->vItem;
				if(!empty($rsOptions[$i]->fPrice))
				{
					$this->price+=$rsOptions[$i]->fPrice;
					$strOptions.=" (".LANG_ADD.CONST_CURRENCY.number_format($rsOptions[$i]->fPrice,2).")";
				}
				$strOptions.="<br />";
			}
		}
		return $strOptions;
	}
	
	function m_checkShoppingCart()
	{
		if($_SESSION['RATESDEFINED'] == "NO"){
			$retUrl = $this->libFunc->m_safeUrl(SITE_SAFEURL . "ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);
		}
		$libFunc=new c_libFunctions();
		$this->obDb->query="SELECT count(*) as cartCount  FROM ".TEMPCART." WHERE  vSessionId ='".SESSIONID."'";
		$rsCnt=$this->obDb->fetchQuery();

		if($rsCnt[0]->cartCount<=0)
		{
			$retUrl=$this->libFunc->m_safeUrl(SITE_URL."ecom/index.php?action=ecom.viewcart");
			$this->libFunc->m_mosRedirect($retUrl);	
		}		
	}
	
	#FUNCTION WILL RETURN VOLUME DISCOUNT RELATED TO PRODUCT
	function m_dspError()
	{
		$libFunc=new c_libFunctions();

		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_ERROR_TFILE",THEMEPATH."default/templates/main/error.tpl.htm");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);	
		if($this->request['mode']=='department')
		{
			$this->ObTpl->set_var("TPL_VAR_HEAD","Department Status");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NODEPARTMENT);
		}
		elseif($this->request['mode']=='product')
		{
			$this->ObTpl->set_var("TPL_VAR_HEAD","Product Status");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOPRODUCT);
		}
		elseif($this->request['mode']=='content')
		{
			$this->ObTpl->set_var("TPL_VAR_HEAD","Page not Found");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOCONTENT);
		}
		elseif($this->request['mode']=='order')
		{
			$this->ObTpl->set_var("TPL_VAR_HEAD","Order number not found");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOORDERNUMBER);
		}
		elseif($this->request['mode']=='downloadlimit')
		{
			$this->ObTpl->set_var("TPL_VAR_HEAD","Limit Exceeded");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_DOWNLOADLIMIT);
		}
		elseif($this->request['mode']=='fileexist')
		{
			$this->ObTpl->set_var("TPL_VAR_HEAD","File Not Exits");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOFILE);
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_HEAD","Page not Found");
			$this->ObTpl->set_var("TPL_VAR_MESSAGE",MSG_NOCONTENT);
		}
		return $this->ObTpl->parse("return","TPL_ERROR_TFILE");	

	}#ENF DISCOUNT FUNCTION

/*	#FUNTION TO CHECK STOCK***
	function m_checkStock($qty)
	{
	$this->obDb->query="SELECT iInventory,iUseinventory,iBackorder FROM ".PRODUCTS." WHERE   iProdId_PK='".$this->productId."'";
	$rs=$this->obDb->fetchQuery();

		#TO CHECK STOCK CONTROL ENABLED
		if($rs[0]->iUseinventory==1)
		{
			if($rs[0]->iInventory<$qty)
			{
				if($rs[0]->iBackorder==1)
				{
					$_SESSION['backorder'][$this->productId]=1;
				}
				else
				{
					$_SESSION['backorder'][$this->productId]=0;
					return false;
				}
			}
		}
		
		return true;
	}#END STOCK CHECK FUNCTION
	
	*/
	
	function m_sendStockMail()
	{
		$this->obDb->query="SELECT vSku,vTitle,iInventory,iUseinventory FROM ".PRODUCTS." WHERE   iProdId_PK='".$this->productId."'";
		$rs=$this->obDb->fetchQuery();

		#TO CHECK STOCK CONTROL ENABLED
		if($rs[0]->iUseinventory==1)
		{
			if($rs[0]->iInventory<1)
			{
					$message ="========================================<br />";
					$message.="Low stock levels for ".$this->libFunc->m_displayContent($rs[0]->vTitle)."<br />";
					$message.="========================================<br />";
					$message.="The quantity for the following product has reached reorder levels:<br />";

					$message.="<br>Product Code: ".$this->libFunc->m_displayContent($rs[0]->vSku);
					$message.="<br>Product Title: ".$this->libFunc->m_displayContent($rs[0]->vTitle);
					$message.="<br>Quantity on hand: ".$rs[0]->iInventory;


					$obMail = new htmlMimeMail();
					$obMail->setReturnPath(ADMIN_EMAIL);
					$obMail->setFrom(SITE_NAME."<".ORDER_EMAIL.">");
					$obMail->setSubject("Stocks Low for ".$rs[0]->vTitle);
					$obMail->setCrlf("\n"); //to handle mails in Outlook Express
					$htmlcontent=$message;
					$txtcontent=preg_replace("/<([^>]+)>/","",preg_replace("/<br(\/{0,1})>/","\r\n",$message));
					$obMail->setHtml($htmlcontent,$txtcontent);

					$obMail->buildMessage();
					$result = $obMail->send(array(ADMIN_EMAIL));
			}			
		}
		
	}#END STOCK CHECK FUNCTION
	
	#FUNCTION TO DIPSLAY CREDIT CARD INFO/IF AVAILABLE
	function m_dspCreditCardInfo()
	{
		$strInfo="";
		 $this->obDb->query ="SELECT vCCnumber,vCCtype,vCCyear,vCCmonth  FROM ".CREDITCARDS." WHERE iOrderid_FK='".$this->orderId."'";
		$rsCreditInfo=$this->obDb->fetchQuery();
		$rsCount=$this->obDb->record_count;
		if($rsCount>0)
		{
			$strInfo="<br />Type: ".$rsCreditInfo[0]->vCCtype;
			$strInfo.="<br />Num: ".$rsCreditInfo[0]->vCCnumber;
			$strInfo.="<br />Exp: ".$rsCreditInfo[0]->vCCmonth."/".$rsCreditInfo[0]->vCCyear;
		}
		return $strInfo;
	}
	
	function m_calVatCust($uid){

		$this->obDb->query ="Select tc.fTax from ".CUSTOMERS." as tu inner join ".STATES." as tc on tu.vState = tc.iStateId_PK where tu.iCustmerid_PK = '".$uid."' and tu.vState != ''";
		$vatPerState = $this->obDb->fetchQuery();
		if($vatPerState[0]->fTax > 0){
			return $vatPerState[0]->fTax;
		}else{
			$this->obDb->query ="Select tc.fTax from ".CUSTOMERS." as tu inner join ".COUNTRY." as tc on tu.vCountry = tc.iCountryId_PK where tu.iCustmerid_PK = '".$uid."'";
			$vatPer=$this->obDb->fetchQuery();
			return $vatPer[0]->fTax;
		}
	}

	function m_reviewVat($cid,$sid=""){
		$this->obDb->query ="Select fTax from ".COUNTRY." where iCountryId_PK = '".$cid."'";
		$vatPerCountry = $this->obDb->fetchQuery();
		
		if($sid != ""){
			$this->obDb->query ="Select fTax from ".STATES." where iStateId_PK = '".$sid."'";
			$vatPerState = $this->obDb->fetchQuery();
		}

		if(($sid != "") && ($vatPerState[0]->fTax > 0)){
			return $reviewVat = $vatPerState[0]->fTax;
		}else{
			return $reviewVat = $vatPerCountry[0]->fTax;
		}
	}

	function m_selectstates($cid){
		$this->obDb->query ="Select iStateId_PK,vStateName from ".STATES." where iCountryID_FK = '".$cid."' order by vStateName asc";
		$StatesInfo = $this->obDb->fetchQuery();
		if($StatesInfo[0]->iStateId_PK == ""){
			echo $dropdownStates="";exit;
		}else{
		$dropdownStates = '<label>State:</label><select  class="formSelect" id="stateValue"  name="stateValue" onchange="javascript:calState(this.value);"><option value="">Select a State</option><option disabled="">---------------</option>';
		foreach($StatesInfo as $k=>$v){
			$dropdownStates .= '<option value="'.$v->iStateId_PK.'">'.$v->vStateName.'</option>';
		}
		$dropdownStates .= '</select>';
		}
		echo $dropdownStates;exit;
	}
	
	
  	#FUNCTION TO CALCULATE INTERNATIONAL POSTAGE COST
    function m_postageZonePrice($cartweight,$countryid='',$granttotal,$shipestimate,$ordertotal,$grandsubTotal,$global_vat,$stateid='')
        {   
        $_SESSION['RATESDEFINED'] = "";
        $rates_defined = "";
        if(ZONECOSTTYPE=='granttotal'){
               $cartweight = $ordertotal;
        }        
        if(DEFAULT_POSTAGE_METHOD=='zones'){ 
            if($countryid != "" && $stateid == ""){
            if(DEFAULT_POSTAGE_METHOD=='zones'){    
                    $this->obDb->query = "SELECT * FROM  ".POSTAGEZONE;
                    $zonelist = $this->obDb->fetchQuery();  // list of zones
                    $zonelistcount =$this->obDb->record_count;  //number of zones
                    $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                    $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                    //Calculate VAT Here...
                    $this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK='".$countryid."'";
                    $vat_cal = $this->obDb->fetchQuery();
                    
    
                if($countryid!=0 && $zonelistcount>0){  
                    for ($i=0;$i<$zonelistcount;$i++) // scan through zones 
                        {
                        $dbcountryid = split(",",$zonelist[$i]->vCountryId); // get individual countries in each zone
                        $dbcountrycount = count($dbcountryid);
                            for ($k=0;$k<$dbcountrycount;$k++) // scan through countries in each zone
                            {
                                if($dbcountryid[$k]==$countryid )
                                {
                                $this->obDb->query = "SELECT * FROM ".POSTAGEZONEDETAILS. " WHERE iZoneId='".$zonelist[$i]->iZoneId."'";
                                $rangerow = $this->obDb->fetchQuery();
                                $rangecount = $this->obDb->record_count;
    
                                for($j=0;$j<$rangecount;$j++)
                                {
                                    if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
                                            
                                            if (isset($shipestimate) && $shipestimate ==1){
                                            $granttotal = $rangerow[$j]->fCost+$granttotal;
                                            $_SESSION['ship_country_id']= $countryid;
                                            $_SESSION['postagePrice']= $rangerow[$j]->fCost;
                                            if ($rangerow[$j]->fSpecialDelivery >= 0){
                                                $_SESSION['zoneSpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
                                            }
                                            if( VAT_POSTAGE_FLAG == "1"){
                                                $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                                                $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                                $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                                            }else{
                                                $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                                $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                                                $granttotal =$grandsubTotalVat + $calculated_vat;
                                            }
                                            
                                            //$granttotal = $granttotal - $global_vat_added + $calculated_vat;
                                            
                                            $rates_defined = "yes";
                                            echo $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]->fTax."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
                                            break;
                                            }else{
                                            return $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
                                            break;
                                            }
                                        
                                    }
                                }
                                }
                            }
                        }
                        if($rates_defined == ""){
                            echo "<span style='color:red;'>No rates defined</span>*postageprice";
                            $_SESSION['RATESDEFINED'] = "NO";
                        }
                }else{     #IF NO COUNTRY IS SELECTED
                  echo "0.00*postageprice*".number_format($granttotal,2)."*grantotal";
                  /*unset($_SESSION['ship_country_id']);
                  unset($_SESSION['postagePrice']);
                  unset($_SESSION['zoneSpecialDelivery']);*/
                  exit; 
                }
                exit;
                }else{
                exit;   
            }
            }else if($stateid != "" && $countryid == ""){
            /*State Tax/Vat and shipping Calculation*/
                if(DEFAULT_POSTAGE_METHOD=='zones' && POSTAGEVATCS=='1'){   
                    
                    $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                    $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                    //Fetches Tax and shipp charge
                    $this->obDb->query = "SELECT fTax,fShipCharge FROM ".STATES." WHERE iStateId_PK='".$stateid."'";
                    $state_cal = $this->obDb->fetchQuery();
                    $vatPer = $state_cal[0]->fTax;
                    //$ShipCharge: Just defined not used in code yet.Now only $_SESSION['postagePrice'] is used.
                    $ShipCharge = $state_cal[0]->fShipCharge;
    
    
                    if( VAT_POSTAGE_FLAG == "1"){
                        $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                        $calculated_vat = (($vatPer/100)*$vatAmountApplied);
                        $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                    }else{
                        $calculated_vat = (($vatPer/100)*$vatAmountApplied);
                        $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                        $granttotal =$grandsubTotalVat + $calculated_vat;
                    }
    
                    echo $_SESSION['postagePrice']."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vatPer."*vatpercent*";exit;
                }
            /*State Tax/Vat and shipping Calculation*/
            }
        } 
    }

#FUNCTION TO CALCULATE INTERNATIONAL POSTAGE COST
    function m_postageCityPrice($cartweight,$countryid='0',$granttotal,$shipestimate,$ordertotal,$grandsubTotal,$global_vat,$stateid='0')
    {   
        $_SESSION['RATESDEFINED'] = "";
        $rates_defined = "";
        if(CITYCOSTTYPE=='granttotal'){
               $cartweight = $ordertotal;
        }
        if(DEFAULT_POSTAGE_METHOD=='cities'){
            $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." WHERE `vCountryId` = '$countryid' AND `vStateId` = '$stateid'";
            $citylist = $this->obDb->fetchQuery();  // list of cities
            $citylistcount =$this->obDb->record_count;  //number of cities

            if ( $citylistcount != "0") {
                $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                //Calculate VAT Here...
                $this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK='".$countryid."'";
                $vat_cal = $this->obDb->fetchQuery();
        
                $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$citylist[0]->iCityId."'";
                $rangerow = $this->obDb->fetchQuery();
                $rangecount = $this->obDb->record_count;
                
                for($j=0;$j<$rangecount;$j++) {
                    if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
                        
                        if (isset($shipestimate) && $shipestimate ==1){
                            $granttotal = $rangerow[$j]->fCost+$granttotal;
                            $_SESSION['ship_country_id']= $countryid;
                            $_SESSION['ship_state_id']= $stateid;
                            $_SESSION['postagePrice']= $rangerow[$j]->fCost;
                            if ($rangerow[$j]->fSpecialDelivery >= 0){
                                $_SESSION['citySpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
                            }
                            if( VAT_POSTAGE_FLAG == "1"){
                                $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                                $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                            }else{
                                $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                                $granttotal =$grandsubTotalVat + $calculated_vat;
                            }
                            
                            //$granttotal = $granttotal - $global_vat_added + $calculated_vat;
                            
                            $rates_defined = "yes";
                            echo $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]->fTax."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
                            //echo "State*postageprice*".$countryid."*grantotal*";
                            break;
                        } else {
                            return $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
                            break;
                        }
                            
                    }
                }
            } else {
                $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." WHERE `vCountryId` = '$countryid' AND `vStateId` = '0'";
                $citylist = $this->obDb->fetchQuery();  // list of cities
                $citylistcount =$this->obDb->record_count;  //number of cities
                $rates_defined = "";
                if ( $citylistcount != "0") {
                    $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                    $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                    //Calculate VAT Here...
                    $this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK='".$countryid."'";
                    $vat_cal = $this->obDb->fetchQuery();
            
                    $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$citylist[0]->iCityId."'";
                    $rangerow = $this->obDb->fetchQuery();
                    $rangecount = $this->obDb->record_count;
                    
                    for($j=0;$j<$rangecount;$j++) {
                        if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
                            
                            if (isset($shipestimate) && $shipestimate ==1){
                                $granttotal = $rangerow[$j]->fCost+$granttotal;
                                $_SESSION['ship_country_id']= $countryid;
                                $_SESSION['ship_state_id']= $stateid;
                                $_SESSION['postagePrice']= $rangerow[$j]->fCost;
                                if ($rangerow[$j]->fSpecialDelivery >= 0){
                                    $_SESSION['citySpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
                                }
                                if( VAT_POSTAGE_FLAG == "1"){
                                    $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                                    $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                    $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                                }else{
                                    $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                    $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                                    $granttotal =$grandsubTotalVat + $calculated_vat;
                                }
                                
                                //$granttotal = $granttotal - $global_vat_added + $calculated_vat;
                                
                                $rates_defined = "yes";
                                echo $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]->fTax."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
                                //echo $stateid."*postageprice*".$countryid."*grantotal*";
                                break;
                            } else {
                                return $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
                                break;
                            }
                                
                        }
                    }
                } else {
                    $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." WHERE `vCountryId` = '0' AND `vStateId` = '0'";
                    $citylist = $this->obDb->fetchQuery();  // list of cities
                    $citylistcount =$this->obDb->record_count;  //number of cities
                    if ( $citylistcount != "0") {
                        $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                        $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                        //Calculate VAT Here...
                        $this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK='".$countryid."'";
                        $vat_cal = $this->obDb->fetchQuery();
                
                        $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$citylist[0]->iCityId."'";
                        $rangerow = $this->obDb->fetchQuery();
                        $rangecount = $this->obDb->record_count;
                        
                        for($j=0;$j<$rangecount;$j++) {
                            if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
                                
                                if (isset($shipestimate) && $shipestimate ==1){
                                    $granttotal = $rangerow[$j]->fCost+$granttotal;
                                    $_SESSION['ship_country_id']= $countryid;
                                    $_SESSION['ship_state_id']= $stateid;
                                    $_SESSION['postagePrice']= $rangerow[$j]->fCost;
                                    if ($rangerow[$j]->fSpecialDelivery >= 0){
                                        $_SESSION['citySpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
                                    }
                                    if( VAT_POSTAGE_FLAG == "1"){
                                        $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                                        $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                        $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                                    }else{
                                        $calculated_vat = (($vat_cal[0]->fTax/100)*$vatAmountApplied);
                                        $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                                        $granttotal =$grandsubTotalVat + $calculated_vat;
                                    }
                                    
                                    //$granttotal = $granttotal - $global_vat_added + $calculated_vat;
                                    
                                    $rates_defined = "yes";
                                    echo $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]->fTax."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
                                    //echo $stateid."*postageprice*".$countryid."*grantotal*";
                                    break;
                                } else {
                                    return $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
                                    break;
                                }
                                    
                            }
                        }
                    }
                }
            }
            if($rates_defined == ""){
                      echo "<span style='color:red;'>No rates defined</span>*postageprice*";
                        $_SESSION['RATESDEFINED'] = "NO";
            }
        }
        
    }

	
	function m_UpdateViewCart()
	{
		//$country = isset($_REQUEST['country']);
		//$state = isset($_REQUEST['state']);
		//$discount = isset($_REQUEST['discount']);
		
		
		//$this->comFunc=new c_commonFunctions();
		//$this->comFunc->obDb=$obDatabase;
		
		/////////////////////UPDATE POSTAGE COST/////////////////////////
		// if(isset($_REQUEST['bill_country_id']) & !empty($_REQUEST['bill_country_id']) & isset($_REQUEST['getpostagecost']) & $_REQUEST['getpostagecost'] == 1 & isset($_REQUEST['bill_state_id']))
		// {
			// $thisarray = array("","");
			// $thisarray = $this->m_recalculate_postage($_REQUEST['bill_country_id'],$_REQUEST['bill_state_id']);
			// $resultstring = $resultstring . "&POSTAGECOST->" . $thisarray[0];
		// }
		$resultstring ="";
		if(isset($_REQUEST['bill_country_id']) & !empty($_REQUEST['bill_country_id']))
		{
			$_SESSION['bill_country_id'] = $_REQUEST['bill_country_id'];
		}
		if(isset($_REQUEST['bill_state_id']) & !empty($_REQUEST['bill_state_id']))
		{
			$_SESSION['bill_state_id'] = $_REQUEST['bill_state_id'];
		}
		if(isset($_REQUEST['bill_country_id']) & !empty($_REQUEST['bill_country_id']) & isset($_REQUEST['getpostagecost']) & $_REQUEST['getpostagecost'] == 1)
		{
		 
			 if(DEFAULT_POSTAGE_METHOD=='cities')
			 {
				$cartweight = $_SESSION['cartweight'];
				$countryid = $_REQUEST['bill_country_id'];
				$granttotal = $_SESSION['grandTotal'];
				$shipestimate = 1;
				$ordertotal = $_SESSION['subtotal'];
				$grandsubTotal = $_SESSION['grandsubTotal'];
				$stateid = $_REQUEST['bill_state_id'];
				$global_vat = $this->m_reviewVat($countryid,$stateid);
				$_SESSION['RATESDEFINED'] = "";
        $rates_defined = "";
        if(CITYCOSTTYPE=='granttotal'){
               $cartweight = $ordertotal;
        }
        if(DEFAULT_POSTAGE_METHOD=='cities'){
            $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." WHERE `vCountryId` = '$countryid' AND `vStateId` = '$stateid'";
            $citylist = $this->obDb->fetchQuery();  // list of cities
            $citylistcount =$this->obDb->record_count;  //number of cities

            if ( $citylistcount != "0") {
                $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                //Calculate VAT Here...
                $this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK='".$countryid."'";
                $vat_cal = $this->obDb->fetchQuery();
				$vat_cal[0] = $global_vat;
        
                $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$citylist[0]->iCityId."'";
                $rangerow = $this->obDb->fetchQuery();
                $rangecount = $this->obDb->record_count;
                
                for($j=0;$j<$rangecount;$j++) {
                    if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
                        
                        if (isset($shipestimate) && $shipestimate ==1){
                            $granttotal = $rangerow[$j]->fCost+$granttotal;
                            $_SESSION['ship_country_id']= $countryid;
                            $_SESSION['ship_state_id']= $stateid;
                            $_SESSION['postagePrice']= $rangerow[$j]->fCost;
                            if ($rangerow[$j]->fSpecialDelivery >= 0){
                                $_SESSION['citySpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
                            }
                            if( VAT_POSTAGE_FLAG == "1"){
                                $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                                $calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
                                $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                            }else{
                                $calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
                                $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                                $granttotal =$grandsubTotalVat + $calculated_vat;
                            }
                            
                            //$granttotal = $granttotal - $global_vat_added + $calculated_vat;
                            
                            $rates_defined = "yes";
                            $resultstring = $resultstring . "&POSTAGECOST->" .   $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
                            //echo "State*postageprice*".$countryid."*grantotal*";
                            break;
                        } else {
                            $resultstring = $resultstring . "&POSTAGECOST->" .   $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
                            break;
                        }
                            
                    }
                }
            } else {
                $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." WHERE `vCountryId` = '$countryid' AND `vStateId` = '0'";
                $citylist = $this->obDb->fetchQuery();  // list of cities
                $citylistcount =$this->obDb->record_count;  //number of cities
                $rates_defined = "";
                if ( $citylistcount != "0") {
                    $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                    $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                    //Calculate VAT Here...
                    $this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK='".$countryid."'";
                    $vat_cal = $this->obDb->fetchQuery();
				$vat_cal[0] = $global_vat;
            
                    $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$citylist[0]->iCityId."'";
                    $rangerow = $this->obDb->fetchQuery();
                    $rangecount = $this->obDb->record_count;
                    
                    for($j=0;$j<$rangecount;$j++) {
                        if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
                            
                            if (isset($shipestimate) && $shipestimate ==1){
                                $granttotal = $rangerow[$j]->fCost+$granttotal;
                                $_SESSION['ship_country_id']= $countryid;
                                $_SESSION['ship_state_id']= $stateid;
                                $_SESSION['postagePrice']= $rangerow[$j]->fCost;
                                if ($rangerow[$j]->fSpecialDelivery >= 0){
                                    $_SESSION['citySpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
                                }
                                if( VAT_POSTAGE_FLAG == "1"){
                                    $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                                    $calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
                                    $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                                }else{
                                    $calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
                                    $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                                    $granttotal =$grandsubTotalVat + $calculated_vat;
                                }
                                
                                //$granttotal = $granttotal - $global_vat_added + $calculated_vat;
                                
                                $rates_defined = "yes";
                                $resultstring = $resultstring . "&POSTAGECOST->" .   $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
                                //echo $stateid."*postageprice*".$countryid."*grantotal*";
                                break;
                            } else {
                                $resultstring = $resultstring . "&POSTAGECOST->" .   $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
                                break;
                            }
                                
                        }
                    }
                } else {
                    $this->obDb->query = "SELECT * FROM  ".POSTAGECITY." WHERE `vCountryId` = '0' AND `vStateId` = '0'";
                    $citylist = $this->obDb->fetchQuery();  // list of cities
                    $citylistcount =$this->obDb->record_count;  //number of cities
                    if ( $citylistcount != "0") {
                        $grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
                        $vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
                        //Calculate VAT Here...
                        $this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK='".$countryid."'";
                        $vat_cal = $this->obDb->fetchQuery();
				$vat_cal[0] = $global_vat;
                
                        $this->obDb->query = "SELECT * FROM ".POSTAGECITYDETAILS. " WHERE fCityId='".$citylist[0]->iCityId."'";
                        $rangerow = $this->obDb->fetchQuery();
                        $rangecount = $this->obDb->record_count;
                        
                        for($j=0;$j<$rangecount;$j++) {
                            if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
                                
                                if (isset($shipestimate) && $shipestimate ==1){
                                    $granttotal = $rangerow[$j]->fCost+$granttotal;
                                    $_SESSION['ship_country_id']= $countryid;
                                    $_SESSION['ship_state_id']= $stateid;
                                    $_SESSION['postagePrice']= $rangerow[$j]->fCost;
                                    if ($rangerow[$j]->fSpecialDelivery >= 0){
                                        $_SESSION['citySpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
                                    }
                                    if( VAT_POSTAGE_FLAG == "1"){
                                        $vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
                                        $calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
                                        $granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
                                    }else{
                                        $calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
                                        $grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
                                        $granttotal =$grandsubTotalVat + $calculated_vat;
                                    }
                                    
                                    //$granttotal = $granttotal - $global_vat_added + $calculated_vat;
                                    
                                    $rates_defined = "yes";
                                    $resultstring = $resultstring . "&POSTAGECOST->" .   $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
                                    //echo $stateid."*postageprice*".$countryid."*grantotal*";
                                    break;
                                } else {
                                    $resultstring = $resultstring . "&POSTAGECOST->" .   $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
                                    break;
                                }
                                    
                            }
                        }
                    }
                }
            }
            if($rates_defined == ""){
                      $resultstring = $resultstring . "&POSTAGECOST->" .   "<span style='color:red;'>No rates defined</span>*postageprice*";
                        $_SESSION['RATESDEFINED'] = "NO";
            }
        }
        
    
			 }
			 elseif(DEFAULT_POSTAGE_METHOD=='zones')
			 {
				$cartweight = $_SESSION['cartweight'];
				$countryid = $_REQUEST['bill_country_id'];
				$granttotal = $_SESSION['grandTotal'];
				$shipestimate = 1;
				$ordertotal = $_SESSION['subtotal'];
				$grandsubTotal = $_SESSION['grandsubTotal'];
				$global_vat = $this->m_reviewVat($countryid,"");
				$stateid == $_REQUEST['bill_state_id'];

				
				$_SESSION['RATESDEFINED'] = "";
				$rates_defined = "";
				if(ZONECOSTTYPE=='granttotal'){
					   $cartweight = $ordertotal;
				}        
				if(DEFAULT_POSTAGE_METHOD=='zones'){ 
					if($countryid != "" && $stateid == ""){
					if(DEFAULT_POSTAGE_METHOD=='zones'){  
							$this->obDb->query = "SELECT * FROM  ".POSTAGEZONE;
							$zonelist = $this->obDb->fetchQuery();
							$zonelistcount =$this->obDb->record_count;
							$grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
							$vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
							$this->obDb->query = "SELECT fTax FROM ".COUNTRY." WHERE iCountryId_PK=".$countryid;
							$vat_cal = $this->obDb->fetchQuery();
				$vat_cal[0] = $global_vat;
							
			
						if($countryid!=0 && $zonelistcount>0){ 
							for ($i=0;$i<$zonelistcount;$i++)
								{
								$dbcountryid = split(",",$zonelist[$i]->vCountryId);
								$dbcountrycount = count($dbcountryid);
									for ($k=0;$k<$dbcountrycount;$k++)
									{
										if($dbcountryid[$k]==$countryid )
										{
										$this->obDb->query = "SELECT * FROM ".POSTAGEZONEDETAILS. " WHERE iZoneId='".$zonelist[$i]->iZoneId."'";
										$rangerow = $this->obDb->fetchQuery();
										$rangecount = $this->obDb->record_count;
			
										for($j=0;$j<$rangecount;$j++)
										{
											if(($cartweight >=$rangerow[$j]->fMinweight) && ($cartweight <= $rangerow[$j]->fMaxWeight)){
													
													if (isset($shipestimate) && $shipestimate ==1){
													$granttotal = $rangerow[$j]->fCost+$granttotal;
													$_SESSION['ship_country_id']= $countryid;
													$_SESSION['postagePrice']= $rangerow[$j]->fCost;
													if ($rangerow[$j]->fSpecialDelivery >= 0){
														$_SESSION['zoneSpecialDelivery']=$rangerow[$j]->fSpecialDelivery;   
													}
													if( VAT_POSTAGE_FLAG == "1"){
														$vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
														$calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
														$granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
													}else{
														$calculated_vat = (($vat_cal[0]/100)*($vatAmountApplied-$_REQUEST['novattotal']));
														$grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
														$granttotal =$grandsubTotalVat + $calculated_vat;
													}
													
													
													$rates_defined = "yes";
													$resultstring = $resultstring . "&POSTAGECOST->" .  $rangerow[$j]->fCost."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vat_cal[0]."*vatpercent*".$rangerow[$j]->fSpecialDelivery."*specialdelivery";
													}else{
													$resultstring = $resultstring . "&POSTAGECOST->" .  $rangerow[$j]->fCost.",".$rangerow[$j]->fSpecialDelivery;
													}
												
											}
										}
										}
									}
								}
								if($rates_defined == ""){
									$resultstring = $resultstring . "&POSTAGECOST->" .  "<span style='color:red;'>No rates defined</span>*postageprice";
									$_SESSION['RATESDEFINED'] = "NO";
								}
						}else{   
						  $resultstring = $resultstring . "&POSTAGECOST->" .  "0.00*postageprice*".number_format($granttotal,2)."*grantotal";
						}
						}else
						{
						}
					}else if($stateid != "" && $countryid == ""){
						if(DEFAULT_POSTAGE_METHOD=='zones' && POSTAGEVATCS=='1'){   
							
							$grandsubTotal = $grandsubTotal-$_SESSION['promotionVatCal']-$_SESSION['promotionVatNotCal'];
							$vatAmountApplied = $grandsubTotal-$_SESSION['promotionVatNotCal'];
							$this->obDb->query = "SELECT fTax,fShipCharge FROM ".STATES." WHERE iStateId_PK=".$stateid;
							$state_cal = $this->obDb->fetchQuery();
							$vatPer = $state_cal[0]->fTax;
				$vatPer = $global_vat;
							$ShipCharge = $state_cal[0]->fShipCharge;
			
			
							if( VAT_POSTAGE_FLAG == "1"){
								$vatAmountApplied = $vatAmountApplied + $_SESSION['postagePrice'];
								$calculated_vat = (($vatPer/100)*($vatAmountApplied-$_REQUEST['novattotal']));
								$granttotal =$grandsubTotal + $calculated_vat + $_SESSION['postagePrice'];
							}else{
								$calculated_vat = (($vatPer/100)*($vatAmountApplied-$_REQUEST['novattotal']));
								$grandsubTotalVat = $grandsubTotal + $_SESSION['postagePrice'];
								$granttotal =$grandsubTotalVat + $calculated_vat;
							}
			
							$resultstring = $resultstring . "&POSTAGECOST->" .  $_SESSION['postagePrice']."*postageprice*".number_format($granttotal,2)."*grantotal*".number_format($calculated_vat,2)."*vatprice*".$vatPer."*vatpercent*";
						}
					}
				}
			 }
		}
		/////////////////////UPDATE STATE LIST/////////////////////////
		elseif(isset($_REQUEST['bill_country_id']) & !empty($_REQUEST['bill_country_id']) & $_REQUEST['getpostagecost'] == 0)
		{
			$tempstring ="";
			$countryid = $_REQUEST['bill_country_id'];
			$this->obDb->query = "SELECT * FROM  ".STATES." WHERE `iCountryID_FK` = '$countryid' ORDER BY `vStateName` ASC";
			$state = $this->obDb->fetchQuery();
			foreach($state as $stateinfo) {
				if (empty($stateinfo->iStateId_PK)){
					$tempstring = $tempstring . ("<option value='0'>Other</option>");
				} else {
					$tempstring = $tempstring . ("<option value='".$stateinfo->iStateId_PK."'>".$stateinfo->vStateName."</option>");
				}
			}
			$resultstring = $resultstring . "&STATELIST->" . $tempstring;
		}
		/////////////////////GIFT CERTIFICATES/////////////////////////
		if(isset($_REQUEST['giftcert']) & !empty($_REQUEST['giftcert']) & $_REQUEST['getpostagecost'] == 3)
		{
			$libFunc=new c_libFunctions();
			$this->obDb->query ="SELECT fRemaining FROM ".GIFTCERTIFICATES." WHERE vGiftcode ='".$_REQUEST['giftcert']."'";
			$rsDiscount=$this->obDb->fetchQuery();
			$rsCount=$this->obDb->record_count;
			$_SESSION['giftCertCode']=htmlentities($_REQUEST['giftcert']);
			if($rsCount>0)
			{
				$_SESSION['giftCertPrice'] = $rsDiscount[0]->fRemaining;
				$resultstring = $resultstring . "&" . "giftCertPrice->" . $rsDiscount[0]->fRemaining;
			}
			else 
			{	
				$_SESSION['giftCertPrice'] = 0;
				$resultstring = $resultstring . "&" . "giftCertPrice->" . 0;
			}
			$resultstring = $resultstring . "&" . "giftCertCode->" . $_SESSION['giftCertCode'];
		}
		elseif(isset($_SESSION['giftCertPrice']) && isset($_SESSION['giftCertCode']))
		{
			$resultstring = $resultstring . "&" . "giftCertPrice->" . $_SESSION['giftCertPrice'];
			$resultstring = $resultstring . "&" . "giftCertCode->" . $_SESSION['giftCertCode'];
		}
		/////////////////////DISCOUNT CODES/////////////////////////
		if(isset($_REQUEST['discount']) & !empty($_REQUEST['discount']) & $_REQUEST['getpostagecost'] == 4)
		{
			$libFunc=new c_libFunctions();
			$curTime=time();
			$this->obDb->query ="SELECT fDiscount,fFixamount,fMinimum  FROM ".DISCOUNTS." WHERE vCode='".htmlentities($_REQUEST['discount'])."' AND tmStartDate<$curTime AND tmEndDate>$curTime AND iState=1";
			$rsDiscount=$this->obDb->fetchQuery();
			$rsCount=$this->obDb->record_count;
			$returnstring = "";
			$discountvalue =0;
			$_SESSION['discountCode'] = htmlentities($_REQUEST['discount']);
			$_SESSION['discountPrice'] = 0;
			$_SESSION['discountType'] = 'none';
			$_SESSION['discountMini'] = 0;
			$offertype="none";
			$discountvalue=	0;
			$olddiscountcode = $_SESSION['discountCode'];
			if($rsCount>0)
			{
				if($rsDiscount[0]->fDiscount>0){
				$_SESSION['discountCode'] = htmlentities($_REQUEST['discount']);
				$offertype="percent";
				$discountvalue=	$rsDiscount[0]->fDiscount;
				}
				elseif($rsDiscount[0]->fFixamount>0){
				$_SESSION['discountCode'] = htmlentities($_REQUEST['discount']);
				$offertype="fix";	
				$discountvalue=$rsDiscount[0]->fFixamount;
				}
				$returnstring = $discountvalue.",".$offertype.",".$rsDiscount[0]->fMinimum;
				$resultstring = $resultstring . "&DISCOUNTPRICE->" . $returnstring;
				$_SESSION['discountPrice'] = $discountvalue;
				$_SESSION['discountType'] = $offertype;
				$_SESSION['discountMini'] = $rsDiscount[0]->fMinimum;
			}
			//elseif(isset($_SESSION['discountCode']) && isset($_SESSION['discountPrice']) && isset($_SESSION['discountType']) && isset($_SESSION['discountMini']))
			//{
			//	$returnstring = $_SESSION['discountPrice'].",".$_SESSION['discountType'].",".$_SESSION['discountMini'];
			//	$resultstring = $resultstring . "&DISCOUNTPRICE->" . $returnstring;
			//}
			else
			{	
				$resultstring = $resultstring . "&DISCOUNTPRICE->0,none,0&DISCOUNTCODE->" . htmlentities($_REQUEST['discount']);
			}
		}
		elseif(isset($_SESSION['discountCode']) && isset($_SESSION['discountPrice']) && isset($_SESSION['discountMini']) && isset($_SESSION['discountType']))
		{
			$resultstring = $resultstring . "&DISCOUNTPRICE->".$_SESSION['discountPrice'].",".$_SESSION['discountType'].",".$_SESSION['discountMini']."&DISCOUNTCODE->" . $_SESSION['discountCode'];
		}
		//elseif(isset($_SESSION['discountCode']))
		//{
		//$resultstring = $resultstring . "&DISCOUNTCODE->" . $_SESSION['discountCode'];
		//}
	$resultstring = substr( $resultstring, 1 );
	$mpoints = "0";
	if(isset($_SESSION['userid']) && $_SESSION['userid'] !=0)
		$this->obDb->query = "SELECT fMemberPoints FROM ".CUSTOMERS." WHERE iCustmerid_PK  ='".$_SESSION['userid']."'";
		$row_customer=$this->obDb->fetchQuery();
		$recordCount=$this->obDb->record_count;
		if($recordCount==1)
		{
			$mpoints = $row_customer[0]->fMemberPoints;
		}
	echo $resultstring . "&VATPOSTAGEFLAG->" . VAT_POSTAGE_FLAG . "&DEFAULTVAT->" . DEFAULTVATTAX . "&MEMBERPOINTS->" . OFFERMPOINT ."|".MPOINTVALUE."|".$mpoints. "&CURRENCY->".CONST_CURRENCY."&WEBSITEHTML->"; 
	}
	//UNFINISHED
	//Calculates totals (total discount, total transaction cost, total shipping cost, etc. Returns array.
	function CalcTotals($subtotal,$postageid = 0,$taxableitemtotal)
	{
		//Array(subtotal,pdiscount,volume discount,postage weight cost,postage base cost,discount code,giftcert,vattotal,grand total);
		$returnarray = Array();
		$returnarray['subtotal'] = $subtotal;
		$returnarray['pdiscount'] = $this->m_calculatePromotionDiscount($subTotal);
	}
	
	
}#END COMMON CLASS 

?>
