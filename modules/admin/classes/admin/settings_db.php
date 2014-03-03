<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
defined('_TEEXEC') or die;
class c_settingsDb
{
	#CONSTRUCTOR
	function c_settingsDb()
	{
		$this->libFunc=new c_libFunctions();
		$this->imagePath = SITE_PATH."images/";
	}

	#FUNCTION TO UPDATE COMPANY INFO
	function m_updateCompInfo()
	{
		if(!isset($this->request['bill_state_id']) || empty($this->request['bill_state_id']))
		{
			$this->request['bill_state_id']="";
		}
		else
		{
			$this->request['bill_state']="";
		}
		
		#FILE UPLOADING START
		if($this->libFunc->checkImageUpload("image1") && $_FILES["image1"]["tmp_name"]!="")
		{
			$fileUpload = new FileUpload();
			
			$fileUpload->source = $_FILES["image1"]["tmp_name"];
			$fileUpload->target = $this->imagePath."company/".$_FILES["image1"]["name"];
			
			$newName1 = $fileUpload->upload();
			$fileUpload->resampleImage ($this->imagePath."company/".$newName1,250,250,100);
			// [/DRK]
			if($newName1 != false)
				$image1 = $newName1;
		}
		else
		{
			$this->obDb->query="SELECT vLogo FROM ".COMPANYSETTINGS;
			$logo = $this->obDb->fetchQuery();
			$image1 = $logo[0]->vLogo;
		}	
		
		#INSERTING COMPANY DETAILS
		$this->obDb->query="UPDATE ".COMPANYSETTINGS." SET 
		vCname ='".$this->libFunc->m_addToDB($this->request['storeName'])."',
		vAddress ='".$this->libFunc->m_addToDB($this->request['storeAddress'])."',
		vCity ='".$this->libFunc->m_addToDB($this->request['storeCity'])."',
		vState='".$this->libFunc->m_addToDB($this->request['bill_state_id'])."',
		vStateName='".$this->libFunc->m_addToDB($this->request['bill_state'])."',
		vCountry='".$this->libFunc->m_addToDB($this->request['bill_country_id'])."',
		vZip='".$this->libFunc->m_addToDB($this->request['storeZip'])."',
		vFax  ='".$this->libFunc->m_addToDB($this->request['storeFax'])."',
		vPhone ='".$this->libFunc->m_addToDB($this->request['storePhone'])."',
		vFreePhone  ='".$this->libFunc->m_addToDB($this->request['storeTollFree'])."',
		vVatNumber  ='".$this->libFunc->m_addToDB($this->request['vatNumber'])."',
		vRNumber  ='".$this->libFunc->m_addToDB($this->request['companyNumber'])."',
		vSlogan  ='".$this->libFunc->m_addToDB($this->request['companySlogan'])."',
		vLogo  ='".$image1."'";
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.company&msg=1");	
	}
	
	#UPDATEING ORDER INFO
	function m_updateOrderInfo()
	{
		#MODIFIED ON 12-04-07 BY NSI
		#INTIALIZING VALUES
		$this->request['cartAlternateShipping']  =$this->libFunc->ifSet($this->request,'cartAlternateShipping');
		$this->request['locvat']  =$this->libFunc->ifSet($this->request,'locvat');
		$this->request['termsopt']  =$this->libFunc->ifSet($this->request,'termsopt');
		$this->request['cartPayCC']=$this->libFunc->ifSet($this->request,'cartPayCC');
		$this->request['cartPayCCp']=$this->libFunc->ifSet($this->request,'cartPayCCp');
		$this->request['cartPayEFT']=$this->libFunc->ifSet($this->request,'cartPayEFT');
		$this->request['cartPayMail']=$this->libFunc->ifSet($this->request,'cartPayMail');
		$this->request['rrptext'] = $this->libFunc->ifSet($this->request,'rrptext','R.R.P.');
		$this->request['rrptext'] =$this->libFunc->ifNullSetdefalutValue ($this->request['rrptext'],'R.R.P.');
		$this->request['vTaxName'] =$this->libFunc->ifSet($this->request,'vTaxName','V.A.T.');
		$this->request['vatbaserate']=$this->libFunc->ifSet($this->request,'vatbaserate');
		$this->request['hidenovat']=$this->libFunc->ifSet($this->request,'hidenovat');
		$this->request['incvat']=$this->libFunc->ifSet($this->request,'incvat');
		$this->request['IncVatTextFlag']=$this->libFunc->ifSet($this->request,'incvattextcheck');
		$this->request['netgross']=$this->libFunc->ifSet($this->request,'netgross');
		$this->request['vTaxName'] =$this->libFunc->ifNullSetdefalutValue($this->request['vTaxName'],'V.A.T.');
		$this->request['cartCCTypeVisa']=$this->libFunc->ifSet($this->request,'cartCCTypeVisa');
		$this->request['cartCCTypeVisaDelta']=$this->libFunc->ifSet($this->request,'cartCCTypeVisaDelta');
		$this->request['cartCCTypeVisaElectron']=$this->libFunc->ifSet($this->request,'cartCCTypeVisaElectron');
		$this->request['cartCCTypeMC']=$this->libFunc->ifSet($this->request,'cartCCTypeMC');
		$this->request['cartCCTypeAmex']=$this->libFunc->ifSet($this->request,'cartCCTypeAmex');
		$this->request['cartCCTypeDiscover']=$this->libFunc->ifSet($this->request,'cartCCTypeDiscover');
		$this->request['cartCCTypeDiners']=$this->libFunc->ifSet($this->request,'cartCCTypeDiners');
		$this->request['cartCCTypeSolo']=$this->libFunc->ifSet($this->request,'cartCCTypeSolo');
		$this->request['cartCCTypeSwitch']=$this->libFunc->ifSet($this->request,'cartCCTypeSwitch');
		$this->request['cartCCTypeMaestro']=$this->libFunc->ifSet($this->request,'cartCCTypeMaestro');
		$this->request['postagevatonoff'] =$this->libFunc->ifSet($this->request,'postageVAT');
		$this->request['wholesale'] =$this->libFunc->ifSet($this->request,'wholesale');
		$this->request['marginpercent'] =$this->libFunc->ifSet($this->request,'marginpercent');
		
		
		
		/*if($this->request['cartPayCC']!='1')
		{
			$this->request['cartCCTypeSolo']="0";
			$this->request['cartCCTypeSwitch']="0";
			$this->request['cartCCTypeMaestro']="0";
		}*/
		foreach($this->request as $fieldname=>$value)
		{	
			
			if(substr($fieldname,0,3)=="txt")
			{
				$fieldname=substr($fieldname,3);
				$this->obDb->query="UPDATE ".SITESETTINGS." SET 
				vSmalltext ='".$this->libFunc->m_addToDB($value)."' WHERE vDatatype='".$fieldname."'";
				$this->obDb->updateQuery();
			}
			elseif($fieldname=='rrptext' || $fieldname=='vTaxName'){
				$this->obDb->query="UPDATE ".SITESETTINGS." SET 
				vSmalltext ='".$this->libFunc->m_addToDB($value)."' WHERE vDatatype='".$fieldname."'";
				$this->obDb->updateQuery();
			}
			else
			{
				$this->obDb->query="UPDATE ".SITESETTINGS." SET 
				nNumberdata ='".$value."' WHERE vDatatype='".$fieldname."'";
				$this->obDb->updateQuery();
			}
		}
		
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.order&msg=1");	
	}

	#UPDATEING ORDER INFO
	function m_updatePaymentInfo()
	{
		$this->request['gatewayTestmode']=$this->libFunc->ifSet($this->request,'gatewayTestmode');
		$this->request['PROTX_APPLY_AVS_CV2']=$this->libFunc->ifSet($this->request,'PROTX_APPLY_AVS_CV2');
		$this->request['PROTX_3D_SECURE_STATUS']=$this->libFunc->ifSet($this->request,'PROTX_3D_SECURE_STATUS');
		$this->request['txtpropay_canada']=$this->libFunc->ifSet($this->request,'txtpropay_canada');
		#MODIFIED ON 12-04-07 BY NSI
		foreach($this->request as $fieldname=>$value)
		{			
			if(substr($fieldname,0,3)=="txt")
			{
				$fieldname=substr($fieldname,3);
				 $this->obDb->query="UPDATE ".SITESETTINGS." SET 
				vSmalltext ='".$this->libFunc->m_addToDB($value)."' WHERE vDatatype='".$fieldname."'";
				$this->obDb->updateQuery();
			}
			else
			{
				$this->obDb->query="UPDATE ".SITESETTINGS." SET 
				nNumberdata ='".$value."' WHERE vDatatype='".$fieldname."'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.payment&msg=1");	
	}


	
	/* FUNCTION TO UPDATE DESIGN SETTINGS
	 * @author: Dave Bui
	 * @copyright: Dpivision.com Ltd
	 * @version: 6.00 
	 */
	function m_updateDesign(){
		
		
		$this->request['iTreeMenu']  =$this->libFunc->ifSet($this->request,'iTreeMenu');
		
		$this->request['homeLayout'] =$this->libFunc->ifSet($this->request,'homeLayout');
		$this->request['mainLayout'] =$this->libFunc->ifSet($this->request,'mainLayout');
		
		$this->request['deptlimit']          		  =$this->libFunc->ifSet($this->request,'deptlimit');	
		$this->request['imgUploadJPGCompression']     =$this->libFunc->ifSet($this->request,'imgUploadJPGCompression');
		$this->request['imgUploadSmallWidth']         =$this->libFunc->ifSet($this->request,'imgUploadSmallWidth');
		$this->request['imgUploadSmallHeight']        =$this->libFunc->ifSet($this->request,'imgUploadSmallHeight');
		$this->request['imgUploadMediumWidth']        =$this->libFunc->ifSet($this->request,'imgUploadMediumWidth');
		$this->request['imgUploadMediumHeight']       =$this->libFunc->ifSet($this->request,'imgUploadMediumHeight');
		$this->request['imgUploadLargeWidth']         =$this->libFunc->ifSet($this->request,'imgUploadLargeWidth');
		$this->request['imgUploadLargeHeight']        =$this->libFunc->ifSet($this->request,'imgUploadLargeHeight');
		$this->request['imgUploadDeptSmallWidth']     =$this->libFunc->ifSet($this->request,'imgUploadDeptSmallWidth');
		$this->request['imgUploadDeptSmallHeight']    =$this->libFunc->ifSet($this->request,'imgUploadDeptSmallHeight');
		$this->request['imgUploadDeptMediumWidth']    =$this->libFunc->ifSet($this->request,'imgUploadDeptMediumWidth');
		$this->request['imgUploadDeptMediumHeight']   =$this->libFunc->ifSet($this->request,'imgUploadDeptMediumHeight');
		$this->request['imgUploadContentSmallWidth']  =$this->libFunc->ifSet($this->request,'imgUploadContentSmallWidth');
		$this->request['imgUploadContentSmallHeight'] =$this->libFunc->ifSet($this->request,'imgUploadContentSmallHeight');
		
		$this->request['imgGalleryThumbnailWidth'] =$this->libFunc->ifSet($this->request,'imgGalleryThumbnailWidth');
		$this->request['imgGalleryThumbnailHeight'] =$this->libFunc->ifSet($this->request,'imgGalleryThumbnailHeight');
		$this->request['imgGalleryThumbnailWidth'] =$this->libFunc->ifSet($this->request,'imgGalleryThumbnailWidth');
		$this->request['imgGalleryThumbnailHeight'] =$this->libFunc->ifSet($this->request,'imgGalleryThumbnailHeight');
		
		
		foreach($this->request as $fieldname=>$value){	
				if($fieldname==='mainLayout' || $fieldname==='homeLayout'|| $fieldname==='deptlimit')
					$this->obDb->query="UPDATE ".SITESETTINGS." SET 
					vSmalltext ='".$this->libFunc->m_addToDB($value)."' WHERE vDatatype='".$fieldname."'";
				else
					$this->obDb->query="UPDATE ".SITESETTINGS." SET 
					nNumberdata ='".$value."' WHERE vDatatype='".$fieldname."'";	
				$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.design&msg=1");
		
	}

	#FUNCTION TO UPDATE FEATURE SETTINGS
	function m_updateFeature()
	{
		#INTIALIZING VALUES
		
		$this->request['cartGiftWrapping'] =$this->libFunc->ifSet($this->request,'cartGiftWrapping');
		$this->request['topsellers']       =$this->libFunc->ifSet($this->request,'topsellers');
		$this->request['shopbybrand']      =$this->libFunc->ifSet($this->request,'shopbybrand');
		$this->request['recent']           =$this->libFunc->ifSet($this->request,'recent');
		$this->request['rssarticles']      =$this->libFunc->ifSet($this->request,'rssarticles');
		$this->request['rssproducts']      =$this->libFunc->ifSet($this->request,'rssproducts');
		$this->request['newsletternav']    =$this->libFunc->ifSet($this->request,'newsletternav');
		$this->request['captcha_registration']    =$this->libFunc->ifSet($this->request,'captcha_registration');
		$this->request['captcha_contactus']    =$this->libFunc->ifSet($this->request,'captcha_contactus');
		$this->request['cartMailList']     =$this->libFunc->ifSet($this->request,'cartMailList');		
		$this->request['inventory']        =$this->libFunc->ifSet($this->request,'inventory');
		$this->request['customerReviews']  =$this->libFunc->ifSet($this->request,'customerReviews');
		$this->request['wishlist']         =$this->libFunc->ifSet($this->request,'wishlist');
		$this->request['usecompare']       =$this->libFunc->ifSet($this->request,'usecompare');
		$this->request['dropshipFeature']  =$this->libFunc->ifSet($this->request,'dropshipFeature');
		$this->request['membership']       =$this->libFunc->ifSet($this->request,'membership');
		$this->request['Language']         =$this->libFunc->ifSet($this->request,'Language');	
		
		$updatefield = 'nNumberdata';
		foreach($this->request as $fieldname=>$value){				
			if ($fieldname =='Language'){
				$updatefield = 'vSmalltext';
			}
			if($fieldname != "analyticsCode"){
				$this->obDb->query="UPDATE ".SITESETTINGS." SET ". 
				$updatefield." ='".$value."' WHERE vDatatype='".$fieldname."'";	
				$this->obDb->updateQuery();
			}
		}
		
		$this->request['analyticsCode']=$this->libFunc->ifSet($this->request,'analyticsCode');
		$this->obDb->query="UPDATE " . SITESETTINGS . " SET tLargetext ='" . $this->libFunc->m_addToDB($this->request['analyticsCode']) . "', vSmalltext = NULL WHERE vDatatype='analyticsCode' LIMIT 1;";
		$this->obDb->updateQuery();
		
		$this->request['speciald']=$this->libFunc->ifSet($this->request,'speciald');
		$this->obDb->query="UPDATE " . SITESETTINGS . " SET nNumberdata ='" . $this->libFunc->m_addToDB($this->request['speciald']) . "' WHERE vDatatype='SpecialPostage';";
		$this->obDb->updateQuery();
		
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.features&msg=1");	
	}

	#FUNCTION TO UPDATE TEXTAREAS DETAILS
	function m_updateTextarea()
	{
			$this->obDb->query="UPDATE ".SITESETTINGS." SET 
			tLargetext ='".$this->libFunc->m_addToDB($this->request['content'])."' WHERE vDatatype='".$this->request['datatype']."'";
			$this->obDb->updateQuery();
			if(isset($this->request['index_body']))
            { 			
			$this->request['metatitle']=$this->libFunc->ifSet($this->request,'metatitle');
			$this->request['metadescription']=$this->libFunc->ifSet($this->request,'metadescription');
			$this->request['metakeyword']=$this->libFunc->ifSet($this->request,'metakeyword');
            }
			foreach($this->request as $fieldname=>$value)
			{			
					$this->obDb->query="UPDATE ".SITESETTINGS." SET 
					tLargetext ='".$this->libFunc->m_addToDB($value)."' WHERE vDatatype='".$fieldname."'";
					$this->obDb->updateQuery();
			}		
	   $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.textarea_edit&which=".$this->request['datatype']."&msg=1");	
	}

	#FUNCTION TO UPDATE POSTAGE HOME
	function m_updateHomePostage()
	{
		$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='0'"; 
		$this->obDb->updateQuery();

		if(isset($this->request['flatrate']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='1'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['range']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='5'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['peritem']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='3'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['pweight']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='8'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['zip']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='15'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['codes']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='4'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['free']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='14'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['options']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='13'"; 
			$this->obDb->updateQuery();
		}

		if(isset($this->request['postageid']))
		{
			$this->obDb->query="UPDATE ".POSTAGE."  SET iStatus='1' WHERE iPostId_PK='".$this->request['postageid']."'"; 
			$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageHome&msg=1");	
	}

	#FUNCTION TO UPDATE POSTAGE DETAILS
	function m_updatePostage()	{
		switch($this->request['shipping_method'])
		{
			case "flat":
				$this->obDb->query="UPDATE ".POSTAGEDETAILS." SET vField1='".$this->request['shipping_field1']."' WHERE iPostId_FK=1"; 
				$this->obDb->updateQuery();
			break;
			case "range":
				$this->obDb->query="DELETE FROM ".POSTAGEDETAILS." WHERE iPostId_FK=5"; 
				$this->obDb->updateQuery();
				foreach($this->request['description'] as $k => $v)
				{
					$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET iPostId_FK=5,vDescription='".$this->request['description'][$k]."',vField1='".$this->request['field1'][$k]."',vField2='".$this->request['field2'][$k]."',vField3='".$this->request['field3'][$k]."'"; 
					$this->obDb->updateQuery();
				}
				if(!empty($this->request['newdescription']) && !empty($this->request['newfield1']) && !empty($this->request['newfield2']) && !empty($this->request['newfield3']))
				{
					$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET iPostId_FK=5,vDescription='".$this->request['newdescription']."',vField1='".$this->request['newfield1']."',vField2='".$this->request['newfield2']."',vField3='".$this->request['newfield3']."'"; 
					$this->obDb->updateQuery();
				}
			break;
			case "peritem":
				$this->obDb->query="DELETE FROM ".POSTAGEDETAILS." WHERE iPostId_FK=3"; 
				$this->obDb->updateQuery();
				$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET vField1='".$this->request['shipping_field1']."',vField2='".$this->request['shipping_field2']."',iPostId_FK=3"; 
				$this->obDb->updateQuery();
			break;
			case "pweight":
				$this->obDb->query="DELETE FROM ".POSTAGEDETAILS." WHERE iPostId_FK=8"; 
				$this->obDb->updateQuery();
				$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET vField1='".$this->request['shipping_field1']."',iPostId_FK=8"; 
				$this->obDb->updateQuery();
			break;
			case "zip":
				$this->obDb->query="DELETE FROM ".POSTAGEDETAILS." WHERE iPostId_FK=15"; 
				$this->obDb->updateQuery();
				foreach($this->request['field1'] as $k => $v)
				{
					if(!isset($this->request['del'][$this->request['codeid'][$k]]))
					{
						$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET iPostId_FK=15,vField1='".$this->request['field1'][$k]."',vField2='".$this->request['field2'][$k]."',vField3='".$this->request['field3'][$k]."'"; 
						$this->obDb->updateQuery();
					}
				}
				if(!empty($this->request['newfield1']) && !empty($this->request['newfield2']) && !empty($this->request['newfield3']))
				{
					$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET iPostId_FK=15,vField1='".$this->request['newfield1']."',vField2='".$this->request['newfield2']."',vField3='".$this->request['newfield3']."'"; 
					$this->obDb->updateQuery();
				}
			break;
			case "codes":
				$this->obDb->query="UPDATE ".POSTAGE." SET fBaseRate='".$this->request['base_rate']."' WHERE iPostId_PK=4"; 
				$this->obDb->updateQuery();
				$this->obDb->query="DELETE FROM ".POSTAGEDETAILS." WHERE iPostId_FK=4"; 
				$this->obDb->updateQuery();
				foreach($this->request['description'] as $k => $v)
				{
					$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET iPostId_FK=4,vDescription='".$this->request['description'][$k]."',vField1='".$this->request['field1'][$k]."',vField2='".$this->request['field2'][$k]."'"; 
					$this->obDb->updateQuery();
				}
				if(!empty($this->request['newdescription']) && !empty($this->request['newfield1']) && !empty($this->request['newfield2']))
				{
					$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET iPostId_FK=4,vDescription='".$this->request['newdescription']."',vField1='".$this->request['newfield1']."',vField2='".$this->request['newfield2']."'"; 
					$this->obDb->updateQuery();
				}
			break;
			case "free":
				$this->obDb->query="DELETE FROM ".POSTAGEDETAILS." WHERE iPostId_FK=14"; 
				$this->obDb->updateQuery();
				$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET vField1='".$this->request['shipping_field1']."',vField2='".$this->request['shipping_field2']."',vField3='".$this->request['shipping_field3']."',iPostId_FK=14"; 
				$this->obDb->updateQuery();
			break;
			case "options":
				$this->obDb->query="DELETE FROM ".POSTAGEDETAILS." WHERE iPostId_FK=13"; 
				$this->obDb->updateQuery();
				foreach($this->request['description'] as $k => $v)
				{
					$this->obDb->query="UPDATE ".POSTAGEDETAILS." SET iPostId_FK=13,vDescription='".$this->request['description'][$k]."',vField1='".$this->request['field1'][$k]."' WHERE iPostDescId_PK='".$this->request['codeid'][$k]."'"; 
					$this->obDb->updateQuery();
				}
				if(!empty($this->request['newdescription']) && !empty($this->request['newfield1']))
				{
					$this->obDb->query="INSERT INTO ".POSTAGEDETAILS." SET iPostId_FK=13,vDescription='".$this->request['newdescription']."',vField1='".$this->request['newfield1']."'"; 
					$this->obDb->updateQuery();
				}
			break;
		}
		
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageEditor&mode=".$this->request['shipping_method']);	
	}#FUNCTION END

	#FUNCTION TO UPDATE SYSTEM INFO-SYSTEM SETTINGS
	function m_updateSystemInfo()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING VALUES
		$this->request['dsn']=$this->libFunc->ifSet($this->request,'dsn');
		$this->request['dbServer']=$this->libFunc->ifSet($this->request,'dbServer');
		$this->request['dbType']=$this->libFunc->ifSet($this->request,'dbType');
		$this->request['dbUserName']=$this->libFunc->ifSet($this->request,'dbUserName');
		$this->request['dbPassword']=$this->libFunc->ifSet($this->request,'dbPassword');
		$this->request['SMTP_USERNAME']=$this->libFunc->ifSet($this->request,'SMTP_USERNAME');
		$this->request['SMTP_PASSWORD']=$this->libFunc->ifSet($this->request,'SMTP_PASSWORD');
		$this->request['SMTP_HOST']=$this->libFunc->ifSet($this->request,'SMTP_HOST');
		
		$somecontent='<?php
		define("DATABASE_HOST","'.$this->request['dbServer'].'");
		define("DATABASE_USERNAME","'.$this->request['dbUserName'].'");
		define("DATABASE_PASSWORD","'.$this->request['dbPassword'].'");
		define("DATABASE_NAME","'.$this->request['dsn'].'");
		define("DATABASE_PORT","3306");
		$Prefix="'.$this->request['dbPrefix'].'";
		?>';
		$filename=DBCONFIG_PATH;
			$msg="4";
		if (is_writable($filename)) 
		{
		    if (!$handle = fopen($filename, 'w')) 
			{
				$msg="1";	
			}
		    if (!fwrite($handle, $somecontent)) 
			{
				$msg="2";	
			}
        } 
		else 
		{
			$msg="3";		
		}
		
		$siteurl=$this->libFunc->canonicalizeUrl($this->libFunc->ifSet($this->request,'SITEURL'));
 		$sitepath=$this->libFunc->path_converter($this->libFunc->ifSet($this->request,'SITEPATH'));
 		$sitesecurepath=$this->libFunc->canonicalizeUrl($this->libFunc->ifSet($this->request,'cartSecureServer'));


		$this->request['upsDSN']=$this->libFunc->ifSet($this->request,'upsDSN');
		$this->request['cartSecureServer']=$sitesecurepath;
		$this->request['SITEPATH']=$sitepath;
		$this->request['SITENAME']=$this->libFunc->ifSet($this->request,'SITENAME');
		$this->request['SITETITLE']=$this->libFunc->ifSet($this->request,'SITETITLE');
		$this->request['ADMINEMAIL']=$this->libFunc->ifSet($this->request,'ADMINEMAIL');
		$this->request['CURRENCY']=$this->request['CURRENCY'];
		$this->request['SITEURL']=$siteurl;
		
		$this->request['systemstate']=$this->libFunc->ifSet($this->request,'systemstate');
		$this->request['SMTP_AUTH']=$this->libFunc->ifSet($this->request,'SMTP_AUTH');
		$this->request['SMTP_USERNAME']=$this->libFunc->ifSet($this->request,'SMTP_USERNAME');
		$this->request['SMTP_PASSWORD']=$this->libFunc->ifSet($this->request,'SMTP_PASSWORD');
		$this->request['SMTP_HOST']=$this->libFunc->ifSet($this->request,'SMTP_HOST');
		$this->request['cencoding']=$this->libFunc->ifSet($this->request,'cencoding');
		
		
		foreach($this->request as $fieldname=>$value){			
			if($fieldname != "newlicense"){
				$this->obDb->query="UPDATE ".SITESETTINGS." SET 
				vSmalltext ='".$this->libFunc->m_addToDB($value)."' WHERE vDatatype='".$fieldname."'";
				$this->obDb->updateQuery();
			}
		}
		$this->request['newlicense']=$this->libFunc->ifSet($this->request,'newlicense');
		$this->license=new licenseCheck($this->obDb, $this->libFunc);
		$licenseinfo = $this->license->DolicenseCheck($this->request['newlicense'], '');
		if(empty($licenseinfo)){
			die("LICENSE ERROR. LICENSE FUNCTION HAS BEEN REMOVED!");
		} else {
			if($licenseinfo['status'] == "Active")
			{
				if(isset($licenseinfo['localkey']))
				{
					//update both
					$this->obDb->query="UPDATE ".SITESETTINGS." SET tLargeText ='".$licenseinfo['localkey']."' WHERE vDatatype='LocalLicense'";
					$this->obDb->updateQuery();
					$this->obDb->query="UPDATE ".SITESETTINGS." SET vSmalltext ='".$this->request['newlicense']."' WHERE vDatatype='LicenseKey'";
					$this->obDb->updateQuery();
				}
			}
		}		
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.system&msg=4");
	}
		
	
	
		
	function m_deletezone()
	{
	$this->obDb->query = "DELETE FROM ".POSTAGEZONE. " WHERE iZoneId=".$this->request['id'];
	$this->obDb->updateQuery();
	$this->obDb->query ="DELETE FROM ".POSTAGEZONEDETAILS. " WHERE iZoneId=".$this->request['id']; 
	$this->obDb->updateQuery();
	$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageEditor&mode=zones");
	exit;
	}
	
	function m_updatePostageZoneRange()
    {
    $this->obDb->query="UPDATE ". POSTAGEZONEDETAILS. " SET ";
        
        $this->obDb->query.="fMinweight =".$this->request['minweight'].",";
        $this->obDb->query.="fMaxWeight =".$this->request['maxweight'].",";
        $this->obDb->query.="fCost =".$this->request['cost'].",";
        $this->obDb->query.="fSpecialDelivery =".$this->request['specialdelivery'].",";
        $this->obDb->query.="iZoneId =".$this->request['zoneid'];
        $this->obDb->query.=" WHERE iRangeId='".$this->request['rangeid']."'";
        $this->obDb->updateQuery();
        $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.setupcost&id=".$this->request['zoneid']);
        exit;
    }
    
    function m_updateCityRange()
    {
    $this->obDb->query="UPDATE ". POSTAGECITYDETAILS. " SET ";
        
        $this->obDb->query.="fMinweight =".$this->request['minweight'].",";
        $this->obDb->query.="fMaxWeight =".$this->request['maxweight'].",";
        $this->obDb->query.="fCost =".$this->request['cost'].",";
        $this->obDb->query.="fSpecialDelivery =".$this->request['specialdelivery'].",";
        $this->obDb->query.="fCityId =".$this->request['cityid'];
        $this->obDb->query.=" WHERE iRangeId='".$this->request['rangeid']."'";
        $this->obDb->updateQuery();
        $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.setupcitycost&id=".$this->request['cityid']);
        exit;
    }
	
	function m_deleterange(){
    $this->obDb->query ="DELETE FROM ".POSTAGEZONEDETAILS. " WHERE iRangeId=".$this->request['id']; 
    $this->obDb->updateQuery();
    $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.setupcost&id=".$this->request['zoneid']);
    exit;
    }
    
    function m_deletecityrange(){
    $this->obDb->query ="DELETE FROM ".POSTAGECITYDETAILS. " WHERE iRangeId=".$this->request['id']; 
    $this->obDb->updateQuery();
    $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.setupcitycost&id=".$this->request['cityid']);
    exit;
    }
	
	
	
	function m_addPostageZoneRange(){
        $this->obDb->query="INSERT INTO ". POSTAGEZONEDETAILS. " SET ";
        $this->obDb->query.="`fMinweight` ='".$this->request['minweight']."',";
        $this->obDb->query.="`fMaxWeight` ='".$this->request['maxweight']."',";
        $this->obDb->query.="`fCost` ='".$this->request['cost']."',";
        $this->obDb->query.="`fSpecialDelivery` ='".$this->request['specialdelivery']."',";
        $this->obDb->query.="`iZoneId` ='".$this->request['zoneid']."'";
    
        $this->obDb->updateQuery();
        
        $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.setupcost&id=".$this->request['zoneid']);
        exit;
    } 
    
    function m_addCityRange(){
        $this->obDb->query="INSERT INTO ". POSTAGECITYDETAILS. " SET ";
        $this->obDb->query.="`fMinweight` ='".$this->request['minweight']."',";
        $this->obDb->query.="`fMaxWeight` ='".$this->request['maxweight']."',";
        $this->obDb->query.="`fCost` ='".$this->request['cost']."',";
        $this->obDb->query.="`fSpecialDelivery` ='".$this->request['specialdelivery']."',";
        $this->obDb->query.="`fCityId` ='".$this->request['cityid']."'";
    
        $this->obDb->updateQuery();
        
        $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.setupcitycost&id=".$this->request['cityid']);
        exit;
    } 
	 
    function m_addPostagezone()
    {
        if (!isset($this->request['row'])) // ADD INITIAL ZONES
        {
        $this->obDb->query = "SELECT * FROM ".POSTAGEZONE ." WHERE vZonename LIKE '".trim($this->request['zonename'])."'";
        $this->obDb->fetchQuery();
        $zonesearchcount = $this->obDb->record_count;
        
        if($zonesearchcount!=0){
                $_SESSION['postageerror']=1; //check if a zone name is already exist
                if (isset($this->request['update'])){ // if request mode is turned on, allow to keep same zone name
                    unset($_SESSION['postageerror']);
                }
        }
        
        if($zonesearchcount!=0 && !isset($this->request['update'])){
                $_SESSION['postageerror']=1; //check if a zone name is already exist
            }
        elseif(!isset($this->request['coutries'])){
                $_SESSION['postageerror']=2; //a country has not been selected
            }
        elseif($this->request['zonename']==""){
                $_SESSION['postageerror']=3; //a zone name has not been inputed
            } 
        elseif (isset($this->request['coutries'])&& $this->request['zonename']!="")
            {
                $coutries=$this->request['coutries'];
                            
                $this->obDb->query = "SELECT * FROM  ".POSTAGEZONE;  
                $zonelist = $this->obDb->fetchQuery();  // list of zone in the database
                $zonelistcount =$this->obDb->record_count;  //number of zone in the database
                
                $string=""; 
                for($j=0;$j<sizeof($coutries);$j++)  // scan through countries from the request 
                {
                    for ($i=0;$i<$zonelistcount;$i++) // scan through zones in database
                    {
                    $dbcountryid = explode(",",$zonelist[$i]->vCountryId); // get individual countries in each zone in database
                    $dbcountrycount = count($dbcountryid);
                        for ($k=0;$k<$dbcountrycount;$k++) // scan through countries in each zone in database
                        {
                            if($coutries[$j] == $dbcountryid[$k] && !isset($this->request['update']) )  
                            {
                            $_SESSION['postageerror']=4;
                            break;
                            }   
                        } 
                    } 
                }
                
                if (!isset($_SESSION['postageerror']))
                {   
                    for($i=0;$i<sizeof($coutries);$i++)
                    {
                        $string.= $coutries[$i].",";
                    }
                    $string=substr_replace($string ,"",-1);
                    
                if (isset($this->request['update']) && $this->request['update']==1)
                    {
                    //$this->obDb->query = "UPDATE ".POSTAGEZONE. " SET `vZonename`=,`vCountryId`) VALUES ('".$this->request['zonename']."','".$string."')";
                    $this->obDb->query = "UPDATE ".POSTAGEZONE. " SET `vZonename`='".$this->request['zonename']."',`vCountryId`='".$string."' WHERE iZoneId=".$this->request['id'];
            
                    }else{
                    $this->obDb->query = "INSERT INTO ".POSTAGEZONE. " (`vZonename`,`vCountryId`) VALUES ('".$this->request['zonename']."','".$string."')";
                    }
                    $this->obDb->updateQuery();
                    $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageEditor&mode=zones");
                 }
                
            } # END IF
        }else // TO CREATE THE REST OF THE WORLD
        {
            $this->obDb->query = "SELECT * FROM ".POSTAGEZONE;
            $zonerow = $this->obDb->fetchQuery();
            $zonesearchcount = $this->obDb->record_count;
                
                if ($zonesearchcount==0) // check if there is no zone has been create
                {
                $_SESSION['postageerror']=5;    
                }else 
                {
                $stringcountryid="";        
                    for ($i=0;$i<$zonesearchcount;$i++)
                    {
                        $stringcountryid.=$zonerow[$i]->vCountryId.",";
                    }
                    $stringcountryid=substr_replace($stringcountryid,"",-1);
                    
                    $this->obDb->query = "SELECT iCountryId_PK FROM  ".COUNTRY." WHERE iCountryId_PK NOT IN (".$stringcountryid.")";
                    
                    $restoftheworld = $this->obDb->fetchQuery();
                    $restoftheworld_count =$this->obDb->record_count;
                    $restoftheworld_string = "";
                    for ($i=0;$i<$restoftheworld_count;$i++)
                    {
                        $restoftheworld_string.=$restoftheworld[$i]->iCountryId_PK.",";
                    }
                    $restoftheworld_string = substr_replace($restoftheworld_string,"",-1);
                    $this->obDb->query = "INSERT INTO ".POSTAGEZONE. " (`vZonename`,`vCountryId`) VALUES ('Rest of the World','".$restoftheworld_string."')";
                    $this->obDb->updateQuery();
                    
                }
        } 
    $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageEditor&mode=zones");
    exit;
    }# FUNCTION TO ADD POSTAGE ZONE 
    
    # FUNCTION TO ADD POSTAGE CITY
        function m_addPostageCity()
    {
        if(empty($this->request['type'])) {
            if(!isset($this->request['country'])){
                $_SESSION['postageerror']=2; //a country has not been selected
            } else {
                $this->obDb->query = "SELECT * FROM ".POSTAGECITY." WHERE `vStateId` = '".$this->request['state']."' AND `vCountryId` = '".$this->request['country']."'";
                $info = $this->obDb->fetchQuery();
                $row_count = $this->obDb->record_count;
                
                if ($row_count != "0") {
                    $_SESSION['postageerror']=1; //a city exists for state
                } else {
                    
                    
                    if (!isset($_SESSION['postageerror'])) {   
                        if (isset($this->request['update']) && $this->request['update']==1) {
                            $this->obDb->query = "UPDATE ".POSTAGECITY. " SET `vCountryId`='".$this->request['country']."',`vStateId`='".$this->request['state']."' WHERE iCityId=".$this->request['id'];
                        }else{
                            $this->obDb->query = "INSERT INTO ".POSTAGECITY. " (`vCountryId`,`vStateId`) VALUES ('".$this->request['country']."','".$this->request['state']."')";
                        }
                        $this->obDb->updateQuery();
                    }
                        
                } # END IF
            }
        } elseif($this->request['type'] == "state") {
            $this->obDb->query = "SELECT iCityId FROM ".POSTAGECITY." WHERE AND `vStateId` = '".$this->request['state']."' AND `vCountryId` = '".$this->request['country']."'";
            $state_info = $this->obDb->fetchQuery();
            $state_count = $this->obDb->record_count;
            if(!isset($this->request['country'])){
                $_SESSION['postageerror']=2; //please select country
            } elseif ($state_count != "0"){
                $_SESSION['postageerror']=6; //a rest of state price
            } else {
                $this->obDb->query = "INSERT INTO ".POSTAGECITY. " (``vCountryId`,`vStateId`) VALUES ('".$this->request['country']."','".$this->request['state']."')";
                $this->obDb->updateQuery();
            }
        } elseif($this->request['type'] == "country") {
            $this->obDb->query = "SELECT iCityId FROM ".POSTAGECITY." WHERE `vStateId` = '0' AND `vCountryId` = '".$this->request['country']."'";
            $state_info = $this->obDb->fetchQuery();
            $state_count = $this->obDb->record_count;
            if(!isset($this->request['country'])){
                $_SESSION['postageerror']=2; //please select country
            } elseif ($state_count != "0"){
                $_SESSION['postageerror']=5; //a rest of country price
            } else {
                $this->obDb->query = "INSERT INTO ".POSTAGECITY. " (`vCountryId`,`vStateId`) VALUES ('".$this->request['country']."','0')";
                $this->obDb->updateQuery();
            }
        } elseif($this->request['type'] == "world") {
            $this->obDb->query = "SELECT iCityId FROM ".POSTAGECITY." WHERE `vStateId` = '0' AND `vCountryId` = '0'";
            $state_info = $this->obDb->fetchQuery();
            $state_count = $this->obDb->record_count;
            if($state_count != "0"){
                $_SESSION['postageerror']=4; //a rest of world price
            } else {
                $this->obDb->query = "INSERT INTO ".POSTAGECITY. " (`vCountryId`,`vStateId`) VALUES ('0','0')";
                $this->obDb->updateQuery();
            }
        }
    $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageEditor&mode=cities");
    exit;
    }# FUNCTION TO ADD POSTAGE CITY
    
    
    # FUNCTION TO DELETE POSTAGE CITY
        function m_deleteCity()
    {
    $this->obDb->query = "DELETE FROM ".POSTAGECITY. " WHERE iCityId=".$this->request['id'];
    $this->obDb->updateQuery();
    $this->obDb->query ="DELETE FROM ".POSTAGECITYDETAILS. " WHERE fCityId=".$this->request['id']; 
    $this->obDb->updateQuery();
    $this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.postageEditor&mode=cities");
    exit;
    } # FUNCTION TO DELETE POSTAGE CITY
	
	
	
	// [DRK]
	#FUNCTION TO UPDATE ANALYTICS SETTINGS
	function m_updateAnalytics() {
		$libFunc=new c_libFunctions();
		#INTIALIZING VALUES
		$this->request['analyticsCode']=$this->libFunc->ifSet($this->request,'analyticsCode');
		$this->obDb->query="UPDATE " . SITESETTINGS . " SET 
							tLargetext ='" . $this->libFunc->m_addToDB($this->request['analyticsCode']) . "'
							WHERE vDatatype='analyticsCode'
							LIMIT 1;";
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=settings.analytics&msg=1");	
	}
	// [/DRK]
}#CLASS ENDS
?>