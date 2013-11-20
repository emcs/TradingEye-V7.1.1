<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;

class c_shopDb
{
	#CONSTRUCTOR
	function c_shopDb()
	{
		$this->libFunc=new c_libFunctions();
	}		

	# INSERT NEW DEPARTMENT
	function m_insertDept()
	{
		$timestamp=time();
		$state=$this->libFunc->ifSet($this->request,"state",0);
		$displaynav=$this->libFunc->ifSet($this->request,"displaynav",0);
		$MemberPage=$this->libFunc->ifSet($this->request,"member",0);
		
		#FILE UPLOADING START
		if($this->libFunc->checkImageUpload("image1"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image1"]["tmp_name"];
			$fileUpload->target = $this->imagePath."department/".$_FILES["image1"]["name"];
			$newName1 = $fileUpload->upload();
			// 
			if ($this->libFunc->ifSet($this->request,"resample1")) {
				$fileUpload->resampleImage ($this->imagePath."department/".$newName1, UPLOAD_DEPTSMIMAGEWIDTH, UPLOAD_DEPTSMIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
			}
			// [/DRK]
			if($newName1 != false)
				$image1 = $newName1;
		}
		else
		{
			$image1 = "";
		}	
		if($this->libFunc->checkImageUpload("image2"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image2"]["tmp_name"];
			$fileUpload->target = $this->imagePath."department/".$_FILES["image2"]["name"];
			$newName2 = $fileUpload->upload();
			// 
			if ($this->libFunc->ifSet($this->request,"resample1")) {
				$fileUpload->resampleImage ($this->imagePath."department/".$newName1, UPLOAD_DEPTMDIMAGEWIDTH, UPLOAD_DEPTMDIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
			}
			// [/DRK]
			if($newName2 != false)
				$image2 = $newName2;
		}
		else
		{
			$image2 = "";
		}	
		if($this->libFunc->checkImageUpload("image3"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image3"]["tmp_name"];
			$fileUpload->target = $this->imagePath."department/".$_FILES["image3"]["name"];
			$newName3 = $fileUpload->upload();
			if($newName3 != false)
				$image3 = $newName3;
		}
		else
		{
				$image3 = "";
		}	
	
		
		#FILE UPLOADING END
		
		#INSERTING TO DEPARTMENTS
		$this->obDb->query ="INSERT INTO ".DEPARTMENTS."	(`iDeptid_PK`, `vTitle`, `vSeoTitle`,";
		$this->obDb->query.="`tShortDescription`, `vMetaTitle`, `tMetaDescription`,`tKeywords`, ";
		$this->obDb->query.="`tContent`,`vImage1`, `vimage2`, `vimage3`, `vTemplate`, `vLayout`,";
		$this->obDb->query.="`tmBuildDate`, `vAdminUser`,`iDisplayInNav`,`iMember`) ";
		$this->obDb->query.="VALUES('','".$this->libFunc->m_addToDB($this->request['title'])."',";
		$this->obDb->query.="'".$this->libFunc->seoText($this->request['seo_title'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['short_description'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['meta_title'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['meta_description'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['keywords'])."',";
		$this->obDb->query.="'".$this->libFunc->m_addToDB($this->request['content'])."',";
		$this->obDb->query.="'$image1',	'$image2','$image3','".$this->request['template']."',";
		$this->obDb->query.="'".$this->request['layout']."','$timestamp','".$_SESSION['uid']."','".$displaynav."','".$MemberPage."')";
		$this->obDb->updateQuery();
		$subObjId=$this->obDb->last_insert_id;
        

		#GETTING MAXIMUM SOR UNDER INSERTED OWNER
		$this->obDb->query="SELECT MAX(iSort) AS MaxSort FROM ".FUSIONS;
		$this->obDb->query.=" WHERE iOwner_FK = ".$this->request['owner']." AND vType = 'department' ";

		$resMax = $this->obDb->fetchQuery();
		$sort=$resMax[0]->MaxSort+1;

		#INSERTING TO FUSIONS TABLE FOR RELATION
		$this->obDb->query="INSERT INTO ".FUSIONS."  (`fusionId`, `iOwner_FK`, `iSubId_FK`,";
		$this->obDb->query.="`vtype`, `iSort`, `iState`,`vOwnerType`)";
		$this->obDb->query.=" VALUES( '',".$this->request['owner'].",";
		$this->obDb->query.="'$subObjId','department','$sort','$state','department')";
		$this->obDb->updateQuery();
        
       
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&msg=1&id=$subObjId&type=department&owner=".$this->request['owner']);	

	}

	function m_insertProduct()
	{		
	
		$timestamp=time();				
		$vendorid=$this->libFunc->ifSet($this->request,"vendorid",0);
		$backorder=$this->libFunc->ifSet($this->request,"backorder",0);
		$taxable=$this->libFunc->ifSet($this->request,"taxable",0);
		$vdiscount=$this->libFunc->ifSet($this->request,"vdiscount",0);
		$sale=$this->libFunc->ifSet($this->request,"sale",0);
		$free_postage=$this->libFunc->ifSet($this->request,"free_postage",0);
		$state=$this->libFunc->ifSet($this->request,"state",0);
		$cart_button=$this->libFunc->ifSet($this->request,"cart_button",0);
		$use_inventory=$this->libFunc->ifSet($this->request,"use_inventory",0);
		$enquirebutton = $this->libFunc->ifSet($this->request,"enquirebutt",0);

						
		$this->request['option']=$this->libFunc->ifSet($this->request,"option",0);
        
		if(isset($this->request['due_date']) && !empty($this->request['due_date']))
		{
			$arrStartDate=explode("/",$this->request['due_date']);
			if(count($arrStartDate)==3)
			$this->request['due_date']=mktime(0,0,0,$arrStartDate[1],$arrStartDate[0],$arrStartDate[2]);
		}
		
		if($this->libFunc->checkImageUpload("image1"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image1"]["tmp_name"];
			$fileUpload->target = $this->imagePath."product/".$_FILES["image1"]["name"];
			$newName1 = $fileUpload->upload();
			if($newName1 != false) {
				$image1 = $newName1;
				// [DRK]
				if ($this->libFunc->ifSet($this->request,"resample1")) {
					$fileUpload->resampleImage ($this->imagePath."product/".$newName1, UPLOAD_SMIMAGEWIDTH, UPLOAD_SMIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
				}
				// [/DRK]
			}
		}
		else
		{
			$image1 = "";
		}	
		if($this->libFunc->checkImageUpload("image2"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image2"]["tmp_name"];
			$fileUpload->target = $this->imagePath."product/".$_FILES["image2"]["name"];
			$newName2 = $fileUpload->upload();
			if($newName2 != false) {
				$image2 = $newName2;
				// [DRK]
				if ($this->libFunc->ifSet($this->request,"resample2")) {
					$fileUpload->resampleImage ($this->imagePath."product/".$newName2, UPLOAD_MDIMAGEWIDTH, UPLOAD_MDIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
				}
				// [/DRK]
			}
		}
		else
		{
			$image2 = "";
		}	
		if($this->libFunc->checkImageUpload("image3"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image3"]["tmp_name"];
			$fileUpload->target = $this->imagePath."product/".$_FILES["image3"]["name"];
			$newName3 = $fileUpload->upload();
			if($newName3 != false) {
				$image3 = $newName3;
				// [DRK]
				if ($this->libFunc->ifSet($this->request,"resample3")) {
					$fileUpload->resampleImage ($this->imagePath."product/".$newName3, UPLOAD_LGIMAGEWIDTH, UPLOAD_LGIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
				}
				// [/DRK]
			}
		}
		else
		{
			$image3 = "";
		}
		//-------	
		$tImages="";
		for($i=1;$i<7;$i++)
		{
			if($this->libFunc->checkImageUpload("extraimage".$i))
			{
				$fileUpload = new FileUpload();
				$fileUpload->source = $_FILES["extraimage".$i]["tmp_name"];
				$fileUpload->target = $this->imagePath."product/".$_FILES["extraimage".$i]["name"];
				$newName[$i] = $fileUpload->upload();
				if($newName[$i] != false) {
					$image[$i] = $newName[$i];
				}
			}
			else
			{
				$image[$i] = "";
			}	
		
		$tImages.=$image[$i].",";
		}
		$tImages = substr($tImages,0,-1);	
		//----------------	
		
		if($this->libFunc->checkFileUpload("downloadable_file"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["downloadable_file"]["tmp_name"];
			$fileUpload->target = $this->imagePath."files/".$_FILES["downloadable_file"]["name"];
			$down_file = $fileUpload->upload();
			if($down_file != false)
			$downloadfile = $down_file;
		}
		else
		{
			$downloadfile = "";
		}			
               
       
        
		$onOrder=$this->libFunc->ifSet($this->request,"on_order");
		 
		$this->obDb->query="INSERT INTO ".PRODUCTS."(
		`vTitle`, `vSeoTitle`,`tShortDescription`, `vMetaTitle`,
		`tMetaDescription`,`tKeywords`, `tContent`, 
		`vImage1`, `vimage2`,`vimage3`,`tImages`,`vTemplate`, `vLayout`,
		`vDownloadablefile`, `vSku`,`fListPrice`, `fPrice`,`fRetailPrice`,
		`fItemWeight`,`iInventory`, `iBackorder`, 
		`iUseinventory`, `iOnorder`, `tmDuedate`, `vShipCode`, 
		`vShipNotes`,`iFreeShip`,`iEnquiryButton`,`iTaxable`,`iVendorid_FK`,
		`iDiscount`, `iSale`,	`iCartButton`,`tmBuildDate`,`iAdminUser`)
		VALUES(
		'".$this->libFunc->m_addToDB($this->request['title'])."',
		'".$this->libFunc->seoText($this->request['seo_title'])."',
		'".$this->libFunc->m_addToDB($this->request['short_description'])."',
		'".$this->libFunc->m_addToDB($this->request['meta_title'])."',
		'".$this->libFunc->m_addToDB($this->request['meta_description'])."',
		'".$this->libFunc->m_addToDB($this->request['keywords'])."',
		'".$this->libFunc->m_addToDB($this->request['content'])."',
		'$image1','$image2','$image3','$tImages',
		'".$this->request['template']."',
		'".$this->request['layout']."',
		'$downloadfile',
		'".$this->request['sku']."',	
		'".$this->libFunc->checkFloatValue($this->request['list_price'])."',
		'".$this->libFunc->checkFloatValue($this->request['price'])."',
		'".$this->libFunc->checkFloatValue((isset($this->request['retailprice']) ? $this->request['retailprice'] : ''))."',		
		'".$this->libFunc->checkFloatValue($this->request['item_weight'])."',
		'".$this->libFunc->checkWrongValue($this->libFunc->ifSet($this->request,'inventory'))."',	
		'$backorder',
		'".$use_inventory."',
		'".$this->libFunc->checkWrongValue($onOrder)."',
		'".$this->libFunc->ifSet($this->request,'due_date')."',
		'".$this->request['ship_code']."',
		'".$this->libFunc->ifSet($this->request,'ship_notes')."',
		'$free_postage','$enquirebutton','$taxable',
		'$vendorid','$vdiscount','$sale',
		'".$cart_button."',
		'$timestamp','".$_SESSION['uid']."')";
		$this->obDb->execQry($this->obDb->query);
		$subObjId=mysql_insert_id();
        

		
		#INSERT ATTRIBUTE FOR PRODUCT
		$i=0;
		$string = "";		
		while(isset($this->request['attributevalue'][$i]))	 
		{
		  	$string.=$this->request['attributevalue'][$i]."�";
			$i++;
		}
	
		$this->obDb->query = "INSERT INTO ".ATTRIBUTEVALUES." (`iAttributesid_FK`,`tValues`) VALUES (
		'".$this->libFunc->m_addToDB($this->request['attributeid'])."',
		'".$this->libFunc->m_addToDB($string)."')";
		$this->obDb->updateQuery();
		$valueid=$this->obDb->last_insert_id;
		 
		$this->obDb->query = "INSERT INTO ".PRODUCTATTRIBUTES." (`iAttributeid_FK`,`iProductid_FK`,`iValueid_FK`) VALUES (
		'".$this->libFunc->m_addToDB($this->request['attributeid'])."',
		'".$this->libFunc->m_addToDB($subObjId)."',
		'".$this->libFunc->m_addToDB($valueid)."')";
		$this->obDb->updateQuery();
			
		$sort=1;
		$sql="UPDATE ".FUSIONS." set iSort=iSort+1 where iOwner_FK = ".$this->request['owner']."	and vType = 'product' and vOwnerType='".$this->request['type']."'";
		$this->obDb->execQry($sql);

		$this->obDb->query="insert into ".FUSIONS."
		(`fusionId`, `iOwner_FK`, `iSubId_FK`, `vtype`, `iSort`, `iState`,`vOwnerType`)
		values('',".$this->request['owner'].",'$subObjId','product','$sort','$state','".$this->request['type']."')";
		$this->obDb->updateQuery();
	
		
		if($this->request['option']==1)
		{											
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.attachOpt&type=department&productid=". $subObjId ."&owner=".$this->request['owner']."&prtype=option&vdiscount=$vdiscount");	
		}
		elseif($vdiscount==1)
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.vdiscount&productid=".$subObjId."&owner=".$this->request['owner']."&type=".$this->request['type']);	
		}
		else
		{
			$this->m_RSSProductFeed();
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&type=".$this->request['type']."&msg=6&id=$subObjId&owner=".$this->request['owner']);	
			
		}
	}#ef
	
	function m_RSSProductFeed()
	{
		
		//Declaring File Link
		$filename= SITE_PATH."RSS/productRss.xml";
		
		$body="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
			<rss version=\"2.0\">
			<channel>
			<title>".SITE_URL." Latest Products</title>
			<link>".SITE_URL."</link>
			<description>The latest products from ".SITE_URL."</description>
			<copyright>(c) of ".SITE_URL.". All rights reserved.</copyright>";
		
			
			$this->obDb->query = "SELECT vTitle,vSeoTitle,tShortDescription,tContent,vImage1,tmBuildDate  FROM ".PRODUCTS." ORDER BY tmBuildDate DESC";
			$row_rssProd=$this->obDb->fetchQuery();
			$ProductCount = $this->obDb->record_count;
			if ($ProductCount >0)
			{
				for($i=0;$i<$ProductCount;$i++)
				{
					if($i < RSSPRODUCT)
					{
					$productUrl = SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$row_rssProd[$i]->vSeoTitle;
					$productUrl=$this->libFunc->m_safeUrl($productUrl);
					$buildDate = date("d/m/Y",$row_rssProd[$i]->tmBuildDate);
					
					$body .="<item>
					<title>".$row_rssProd[$i]->vTitle."</title>
					<link>".urlencode($productUrl)."</link>
					<description>".$row_rssProd[$i]->tShortDescription."</description>
					<pubDate>".$buildDate."</pubDate>
					</item>";		
					}
					
				}
				$body .="
				</channel>
				</rss>";
				
			}else
			{
				$body .="
				</channel>
				</rss>";
				
			}
			
			//creating xml file
		if (!$handle = fopen($filename, 'w+')) {
  			echo "Please make sure the folder RSS and the files inside have 777 permission";
  			die();
		}
		else 
		{
			fwrite($handle,$body);
			fclose($handle); 	
		}
	}//ef
	
	
	function m_RSSArticleFeed()
	{
		
		//Declaring File Link
		$filename= SITE_PATH."RSS/articleRss.xml";
		
		$body="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
			<rss version=\"2.0\">
			<channel>
			<title>".SITE_URL." Latest news articles</title>
			<link>".SITE_URL."</link>
			<description>The latest products from ".SITE_URL."</description>
			<copyright>(c) of ".SITE_URL.". All rights reserved.</copyright>";
		
			
			$this->obDb->query = "SELECT vTitle,vSeoTitle,tShortDescription,tContent,vImage1,tmBuildDate  FROM ".CONTENTS." ORDER BY tmBuildDate DESC";
			$row_rssArticle=$this->obDb->fetchQuery();
			$ArticleCount = $this->obDb->record_count;
			
			if ($ArticleCount >0)
			{
				for($i=0;$i<RSSARTICLES;$i++)
				{
					$articlUrl = SITE_URL."ecom/index.php?action=ecom.pdetails&amp;mode=".$row_rssArticle[$i]->vSeoTitle;
					$articlUrl=$this->libFunc->m_safeUrl($articlUrl);
					$buildDate = date("d/m/Y",$row_rssArticle[$i]->tmBuildDate);
					
					$body .="<item>
					<title>".$row_rssArticle[$i]->vTitle."</title>
					<link>".urlencode($articlUrl)."</link>
					<description>".$row_rssArticle[$i]->tShortDescription."</description>
					<pubDate>".$buildDate."</pubDate>
					</item>";		
					
				}
				$body .="
				</channel>
				</rss>";
				
			}else
			{
				$body .="
				</channel>
				</rss>";
				
			}
			
			//creating xml file
		if (!$handle = fopen($filename, 'w+')) {
  			echo "file is f'd";
  			die();
		}
		else 
		{
			fwrite($handle,$body);
			fclose($handle); 	
		}
	}//ef

	#FUNCTION TO INSERT CONTENT
	function m_insertContent()
	{
		$timestamp=time();
		$state=$this->libFunc->ifSet($this->request,"state",0);


		#FILE UPLOADING START
		if($this->libFunc->checkImageUpload("image1"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image1"]["tmp_name"];
			$fileUpload->target = $this->imagePath."content/".$_FILES["image1"]["name"];
			$newName1 = $fileUpload->upload();
			// 
			if ($this->libFunc->ifSet($this->request,"resample1")) {
				$fileUpload->resampleImage ($this->imagePath."content/".$newName1, UPLOAD_CONTENTSMIMAGEWIDTH, UPLOAD_CONTENTSMIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
			}
			// [/DRK]
			if($newName1 != false)
				$image1 = $newName1;
		}
		else
		{
			$image1 = "";
		}	
		if($this->libFunc->checkImageUpload("image2"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image2"]["tmp_name"];
			$fileUpload->target = $this->imagePath."content/".$_FILES["image2"]["name"];
			$newName2 = $fileUpload->upload();
			if($newName2 != false)
				$image2 = $newName2;
		}
		else
		{
			$image2 = "";
		}	
		if($this->libFunc->checkImageUpload("image3"))
		{
			$fileUpload = new FileUpload();
			$fileUpload->source = $_FILES["image3"]["tmp_name"];
			$fileUpload->target = $this->imagePath."content/".$_FILES["image3"]["name"];
			$newName3 = $fileUpload->upload();
			if($newName3 != false)
				$image3 = $newName3;
		}
		else
		{
				$image3 = "";
		}	
		#FILE UPLOADING END
		
		#INSERTING TO DEPARTMENTS
		$this->obDb->query="insert into ".CONTENTS."
		(`iContentid_PK`, `vTitle`, `vSeoTitle`,
		 `tShortDescription`, `vMetaTitle`, `tMetaDescription`,
		  `tKeywords`, `tContent`,
		  `vImage1`, `vimage2`, `vimage3`, `vTemplate`, `vLayout`,
			 `tmBuildDate`,`tmEditDate`, `vAdminUser`) 
			values('',
			'".$this->libFunc->m_addToDB($this->request['title'])."',
			'".$this->libFunc->seoText($this->request['seo_title'])."',
			'".$this->libFunc->m_addToDB($this->request['short_description'])."',
			'".$this->libFunc->m_addToDB($this->request['meta_title'])."',
			'".$this->libFunc->m_addToDB($this->request['meta_description'])."',
			'".$this->libFunc->m_addToDB($this->request['keywords'])."',
			'".$this->libFunc->m_addToDB($this->request['content'])."',
			'$image1',
			'$image2',
			'$image3',
			'".$this->request['template']."',
			'".$this->request['layout']."',
			'$timestamp','','".$_SESSION['uid']."')";
			$this->obDb->execQry($this->obDb->query);
			$subObjId=mysql_insert_id();
			#GETTING MAXIMUM SOR UNDER INSERTED OWNER
			$this->obDb->query="select MAX(iSort) AS MaxSort
			from ".FUSIONS."
			where iOwner_FK = ".$this->request['owner']."
			and vType = 'content'and vOwnerType='".$this->request['type']."'";
			$res = $this->obDb->execQry($this->obDb->query);
			$sortnum=mysql_fetch_object($res);
				$sort=$sortnum->MaxSort+1;
			#INSERTING TO FUSIONS TABLE FOR RELATION
			$this->obDb->query="insert into ".FUSIONS."
			(`fusionId`, `iOwner_FK`, `iSubId_FK`,`vtype`, `iSort`, `iState`,`vOwnerType`)
			values('',".$this->request['owner'].",'$subObjId','content','$sort','$state','".$this->request['type']."')";
			$this->obDb->updateQuery();
			
			$this->m_RSSArticleFeed();
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&type=".$this->request['type']."&msg=8&id=$subObjId&owner=".$this->request['owner']);	
	}

	#FUNCTION TO UPDATE DEPARTMENT
	function m_updateDept()
	{
		$timestamp=time();			
		$state=$this->libFunc->ifSet($this->request,"state",0);
		$displaynav=$this->libFunc->ifSet($this->request,"displaynav",0);
		$memberPage=$this->libFunc->ifSet($this->request,"member",0);
		#CHECK FOR DEPARTMENT ID URL TEMPER
		 $this->obDb->query = "select iDeptId_PK,vTitle,vImage1,vImage2,vImage3 from ".DEPARTMENTS." where iDeptId_PK = '".$this->request['deptId']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count == 0)
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&msg=5&owner=".$this->request['owner']);	
		}
		else
		{
			$this->obDb->query="UPDATE ".DEPARTMENTS." SET ";
			$this->obDb->query.="`vTitle`='".$this->libFunc->m_addToDB($this->request['title'])."',";
			$this->obDb->query.="`vSeoTitle`='".$this->libFunc->seoText($this->request['seo_title'])."',";
			$this->obDb->query.="`tShortDescription`='".$this->libFunc->m_addToDB($this->request['short_description'])."',";
			$this->obDb->query.="`vMetaTitle`='".$this->libFunc->m_addToDB($this->request['meta_title'])."',"; 
			$this->obDb->query.="`tMetaDescription`='".$this->libFunc->m_addToDB($this->request['meta_description'])."',";
			$this->obDb->query.="`tKeywords`='".$this->libFunc->m_addToDB($this->request['keywords'])."',";
			$this->obDb->query.="`tContent`='".$this->libFunc->m_addToDB($this->request['content'])."',	";						 
			$this->obDb->query.="`vTemplate`='".$this->request['template']."',";
			$this->obDb->query.="`vLayout`='".$this->request['layout']."',";
			$this->obDb->query.="`tmEditDate`='".$timestamp."',";
			$this->obDb->query.="`vAdminUser` ='".$_SESSION['uid']."',";
			$this->obDb->query.="`iDisplayInNav`='".$displaynav."',";
			$this->obDb->query.="`iMember`='".$memberPage."'";
			$this->obDb->query.=" WHERE iDeptId_PK='".$this->request['deptId']."'";
			$this->obDb->updateQuery();
		
			$this->obDb->query="UPDATE ".FUSIONS." SET ";
			$this->obDb->query.="`iState`='$state' WHERE "; 
			$this->obDb->query.=" vtype='department' AND iSubId_FK='".$this->request['deptId']."'";
			$this->obDb->updateQuery();
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&type=department&msg=2&owner=".$this->request['owner']."&id=".$this->request['deptId']);	
		}
	}

	#FUNCTION TO UPDATE CONTENT
	function m_updateContent()
	{
		$timestamp=time();			
		$state=$this->libFunc->ifSet($this->request,"state",0);

		#CHECK FOR CONTENT ID URL TEMPER
		 $this->obDb->query = "select iContentid_PK,vTitle,vImage1,vImage2,vImage3 from ".CONTENTS." where iContentid_PK = '".$this->request['contentId']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count == 0)
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&msg=5&owner=".$this->request['owner']."&type=".$this->request['type']);	
		}
		else
		{
			$this->obDb->query="UPDATE ".CONTENTS." SET 
			`vTitle`='".$this->libFunc->m_addToDB($this->request['title'])."',
			`vSeoTitle`='".$this->libFunc->seoText($this->request['seo_title'])."',
			`tShortDescription`='".$this->libFunc->m_addToDB($this->request['short_description'])."', 
			`vMetaTitle`='".$this->libFunc->m_addToDB($this->request['meta_title'])."', 
			`tMetaDescription`='".$this->libFunc->m_addToDB($this->request['meta_description'])."',
			`tKeywords`='".$this->libFunc->m_addToDB($this->request['keywords'])."',
			`tContent`='".$this->libFunc->m_addToDB($this->request['content'])."',							 
			`vTemplate`='".$this->request['template']."',
			`vLayout`='".$this->request['layout']."',
			`tmEditDate`='".$timestamp."',
			`vAdminUser` ='".$_SESSION['uid']."'
			where iContentid_PK=".$this->request['contentId'];
			
			$this->obDb->execQry($this->obDb->query);

			 $this->obDb->query = "SELECT count(*) as Cnt FROM ".FUSIONS." where iSubId_FK = '".$this->request['contentId']."' AND vType='content'";
			$rs = $this->obDb->fetchQuery();
			if($rs[0]->Cnt> 0)
			{
				$this->obDb->query="update ".FUSIONS." set
				`iState`='$state' where vtype='content' AND iSubId_FK='".$this->request['contentId']."'";
				$this->obDb->updateQuery();
				$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&type=".$this->request['type']."&msg=9&owner=".$this->request['owner']."&id=".$this->request['contentId']);	
			}
			else
			{
				$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=home.creport");	
			}
		$this->m_RSSArticleFeed();
		}
	}#ef

	#FUNCTION TO UPDATE PRODUCT
	function m_updateProduct()
	{
		$timestamp=time();			
	
		$backorder=$this->libFunc->ifSet($this->request,"backorder","");
		$use_inventory=$this->libFunc->ifSet($this->request,"use_inventory",0);
		$taxable=$this->libFunc->ifSet($this->request,"taxable",0);
		$vdiscount=$this->libFunc->ifSet($this->request,"vdiscount",0);
		$sale=$this->libFunc->ifSet($this->request,"sale",0);
		$free_postage=$this->libFunc->ifSet($this->request,"free_postage",0);
		$state=$this->libFunc->ifSet($this->request,"state",0);
		$cart_button=$this->libFunc->ifSet($this->request,"cart_button",0);
		$enquirebutton = $this->libFunc->ifSet($this->request,"enquirebutt",0);
		$inventory=$this->libFunc->checkWrongValue($this->libFunc->ifSet($this->request,"inventory"));
		$mininventory=$this->libFunc->checkWrongValue($this->libFunc->ifSet($this->request,"min_inventory"));
	
		if(isset($this->request['due_date']) && !empty($this->request['due_date']))
		{
			$arrStartDate=explode("/",$this->request['due_date']);
			$this->request['due_date']=mktime(0,0,0,$arrStartDate[1],$arrStartDate[0],$arrStartDate[2]);
		}
		
		$this->obDb->query = "SELECT * FROM ".PRODUCTATTRIBUTES. " WHERE iProductid_FK = '".$this->request['prodId']."'";
		$existingAttribute = $this->obDb->fetchQuery();
		
		$this->obDb->query = "DELETE FROM ".ATTRIBUTEVALUES." WHERE iValueId_PK = '".$existingAttribute[0]->iValueid_FK."'";
		$this->obDb->updateQuery();
		
		$this->obDb->query = "DELETE FROM ".PRODUCTATTRIBUTES." WHERE iProductid_FK = '".$this->request['prodId']."'";
		$this->obDb->updateQuery();
		
		
		#INSERT ATTRIBUTE FOR PRODUCT
		$i=0;
		$string = "";		
		while(isset($this->request['attributevalue'][$i]))	 
		{
		  	$string.=$this->request['attributevalue'][$i]."�";
			$i++;
		}
	
		$this->obDb->query = "INSERT INTO ".ATTRIBUTEVALUES." (`iAttributesid_FK`,`tValues`) VALUES (
		'".$this->libFunc->m_addToDB($this->request['attributeid'])."',
		'".$this->libFunc->m_addToDB($string)."')";
		$this->obDb->updateQuery();
		$valueid=$this->obDb->last_insert_id;
		 
		$this->obDb->query = "INSERT INTO ".PRODUCTATTRIBUTES." (`iAttributeid_FK`,`iProductid_FK`,`iValueid_FK`) VALUES (
		'".$this->libFunc->m_addToDB($this->request['attributeid'])."',
		'".$this->libFunc->m_addToDB($this->request['prodId'])."',
		'".$this->libFunc->m_addToDB($valueid)."')";
		$this->obDb->updateQuery();
		
		$this->obDb->query = "SELECT iProdid_PK,vTitle,vImage1,vImage2,vImage3,tImages FROM ".PRODUCTS." WHERE iProdid_PK = '".$this->request['prodId']."'";
		$row_code = $this->obDb->fetchQuery();
		if($this->obDb->record_count == 0)
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&type=".$this->request['type']."&msg=5&owner=".$this->request['owner']);	
		}
		else
		{
			if(!isset($this->request['retailprice'])){
				$this->request['retailprice'] = "";
			}
            

			$onOrder=$this->libFunc->ifSet($this->request,"on_order");
			$this->obDb->query="UPDATE ".PRODUCTS." SET 
			vTitle							='".$this->libFunc->m_addToDB($this->request['title'])."',
			vSeoTitle					='".$this->libFunc->seoText($this->request['seo_title'])."',
			tShortDescription			='".$this->libFunc->m_addToDB($this->request['short_description'])."', 
			vMetaTitle					='".$this->libFunc->m_addToDB($this->request['meta_title'])."', 
			tMetaDescription			='".$this->libFunc->m_addToDB($this->request['meta_description'])."',
			tKeywords					='".$this->libFunc->m_addToDB($this->request['keywords'])."',
			tContent					='".$this->libFunc->m_addToDB($this->request['content'])."',			 
			vTemplate					='".$this->request['template']."',
			vLayout						='".$this->request['layout']."',
			vSku						='".$this->request['sku']."',
			fListPrice					='".$this->libFunc->checkFloatValue($this->request['list_price'])."',
			fPrice						='".$this->libFunc->checkFloatValue($this->request['price'])."',
			fRetailPrice 				='".$this->libFunc->checkFloatValue($this->request['retailprice'])."',
			fItemWeight					='".$this->libFunc->checkFloatValue($this->request['item_weight'])."',	
			iInventory					='".$this->libFunc->checkWrongValue($inventory)."',	
			iInventoryMinimum			='".$this->libFunc->checkWrongValue($mininventory)."',	
			iBackorder					='$backorder',
			iUseinventory				='".$use_inventory."',
			iOnorder					='".$this->libFunc->checkWrongValue($onOrder)."',
			tmDuedate					='".$this->libFunc->ifSet($this->request,"due_date")."',
			vShipCode					='".$this->request['ship_code']."',
			vShipNotes					='".$this->request['ship_notes']."',	
			iFreeShip					='$free_postage',
			iTaxable					='$taxable',
			iVendorid_FK				='".$this->libFunc->ifSet($this->request,"vendorid",0)."',
			iAttrValueId_FK 			='".$this->request['attributeid']."',		
			iDiscount					='$vdiscount',
			iSale						='$sale',
			iCartButton					='".$cart_button."',
			iEnquiryButton 			    ='".$enquirebutton."',   
			tmEditDate					='".$timestamp."',
			iAdminUser					='".$_SESSION['uid']."'
			WHERE iProdid_PK=".$this->request['prodId'];			
			$this->obDb->execQry($this->obDb->query);
		
		
		
			$passcode = "";
			if (isset($_REQUEST['passcode']) && ($_REQUEST['passcode'] != "")){			
				$passcode = $_REQUEST['passcode'];
			}
			
			#CHECK IF THERE IS IMAGE FOR IMAGE GALLERY
			$this->obDb->query = " SELECT iImagesPK , vImages, tmTimeInserted FROM ". TEMPIMAGES. " WHERE tmTimeInserted ='". $passcode ."'" ;
			$rowTempImages =  $this->obDb->fetchQuery();
			
				$stringImages = "";	
				if ($this->obDb->record_count>0){
					for ($i=0; $i<$this->obDb->record_count; $i++){				
						$stringImages .= $rowTempImages[$i]->vImages. ",";
					}
					#Remove the last comma from the temp images string
					$stringImages = substr($stringImages, 0 , -1);
					
					if ($stringImages != ""){						
						if( $row_code[0]->tImages!=""){
							$stringImages = $row_code[0]->tImages.",".$stringImages;
						}
						$this->obDb->query = "UPDATE " . PRODUCTS . " SET tImages = '". $stringImages . "' WHERE iProdid_PK = '". $this->request['prodId']. "'";
						$this->obDb->updateQuery();
					}
			}
		
			# CLEAR THE IMAGE TEMP
			$this->obDb->query = " DELETE FROM ". TEMPIMAGES. " WHERE tmTimeInserted ='". $passcode ."'" ;
			$this->obDb->updateQuery();
			
		
			$obFile=new FileUpload();
			$this->imagePath=$this->imagePath."product/";
		
			$this->obDb->query = "SELECT count(*) as Cnt FROM ".FUSIONS." where iSubId_FK = '".$this->request['prodId']."' AND vType='product'";
			$rs = $this->obDb->fetchQuery();
			$this->m_RSSProductFeed();
			if(isset($this->request['fusionall']) && $this->request['fusionall'] == 1)
			{
				$this->ToggleProductAllDepartments($state,$this->request['prodId']);
			}
			if($rs[0]->Cnt> 0)
			{
				$this->obDb->query="UPDATE ".FUSIONS." SET
				`iState`='$state' where vtype='product' AND iSubId_FK='".$this->request['prodId']."' AND iOwner_FK='".$this->request['owner']."' AND vOwnerType='".$this->request['type']."'";
				$this->obDb->updateQuery();
				$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.dspMsg&type=".$this->request['type']."&msg=7&owner=".$this->request['owner']."&id=".$this->request['prodId']);	
			}
			else
			{
				$this->libFunc->m_mosRedirect(SITE_URL."adminindex.php?action=home.preport");	
			}
		}
	}

	#FUNCTION TO UPDATE SORT AND STATE
	function m_updateHomeDept()
	{
		if(isset($this->request['state']))
		{
			$state=$this->request['state'];
		}

		$ownerid=$this->request['ownerid'];

		#DEACTIVATE ALL UNDER OWNER ID SELECTED
		$this->obDb->query="UPDATE ".FUSIONS." set
			 `iState`='0' where vtype='department' AND iOwner_FK='$ownerid'";
				$this->obDb->updateQuery();
		
		if(isset($this->request['sort']))
		{
			$sort=$this->request['sort'];
			foreach($sort as $sortid=>$sortValue)
			{
				$this->obDb->query="UPDATE ".FUSIONS." SET `iSort`='$sortValue' where vtype='department' AND iOwner_FK='$ownerid' AND  iSubId_FK='$sortid'";
				$this->obDb->updateQuery();
			}
		}
		if(isset($state))
		{
			foreach($state as $stateid=>$stateValue)
			{
				$this->obDb->query="UPDATE ".FUSIONS." set
				 `iState`='$stateValue' where vtype='department' AND iOwner_FK='$ownerid' AND iSubId_FK='$stateid'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&type=department&msg=2&owner=".$ownerid);	
	}	
	
	function m_updateHomeProduct()
	{
		#INTIALIZING STATE
		if(isset($this->request['state']))
		{
			$state=$this->request['state'];
		}
		$otype=$this->request['otype'];
		$ownerid=$this->request['ownerid'];

		$this->obDb->query="UPDATE ".FUSIONS." set
			 `iState`='0' where vtype='product' AND iOwner_FK='$ownerid' AND vOwnerType='$otype'";
				
		$this->obDb->updateQuery();

		if(isset($this->request['sort']))
		{
			$sort=$this->request['sort'];
			foreach($sort as $sortid=>$sortValue)
			{
				$this->obDb->query="UPDATE ".FUSIONS." SET `iSort`='$sortValue' where vtype='product' AND iOwner_FK='$ownerid' AND iSubId_FK='$sortid' AND vOwnerType='$otype'";
				$this->obDb->updateQuery();
			}
		}
		if(isset($state))
		{
			foreach($state as $stateid=>$stateValue)
			{
				$this->obDb->query="UPDATE ".FUSIONS." set
				 `iState`='$stateValue' where vtype='product' AND iOwner_FK='$ownerid' AND iSubId_FK='$stateid' AND vOwnerType='$otype'";
				$this->obDb->updateQuery();				
			}			
			
		}	$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&type=".$otype."&msg=2&owner=".$ownerid);	
	}	
	
	function m_updateHomeContent()
	{
		#INTIALIZING STATE
		if(isset($this->request['state']))
		{
			$state=$this->request['state'];
		}
		$ownerid=$this->request['ownerid'];
		$otype=$this->request['otype'];

		$this->obDb->query="UPDATE ".FUSIONS." SET	 `iState`='0' where vtype='content' AND iOwner_FK='$ownerid'";
		$this->obDb->updateQuery();

		if(isset($this->request['sort']))
		{
			$sort=$this->request['sort'];
			foreach($sort as $sortid=>$sortValue)
			{
				$this->obDb->query="UPDATE ".FUSIONS." set
				 `iSort`='$sortValue' where vtype='content' AND iOwner_FK='$ownerid' AND iSubId_FK='$sortid'";
				$this->obDb->updateQuery();

			}
		}
		if(isset($state))
		{  	
			foreach($state as $stateid=>$stateValue)
			{
				$this->obDb->query="UPDATE ".FUSIONS." set
				 `iState`='$stateValue' where vtype='content' AND iSubId_FK='$stateid' AND iOwner_FK='$ownerid'";
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&type=".$otype."&msg=2&owner=".$ownerid);	
	}	
	
	
	function m_uploadImage()
	{

		$fileUpload = new FileUpload();
		if($this->request['type']=="product")
		{
			$this->obDb->query ="SELECT iProdid_PK,vTitle,vImage1,vImage2,vImage3,tImages,vDownloadablefile ";
			$this->obDb->query.=" FROM ".PRODUCTS." where iProdid_PK ='".$this->request['id']."'";
			$this->imagePath=$this->imagePath."product/";
		}
		elseif($this->request['type']=="content")
		{
			$this->obDb->query = "SELECT iContentId_PK,vTitle,vImage1,vImage2,vImage3 FROM ".CONTENTS;
			$this->obDb->query.=" WHERE iContentId_PK ='".$this->request['id']."'";
			$this->imagePath=$this->imagePath."content/";
		}
		else
		{
			$this->obDb->query = "SELECT iDeptId_PK,vTitle,vImage1,vImage2,vImage3 FROM ".DEPARTMENTS;
			$this->obDb->query.=" WHERE iDeptId_PK ='".$this->request['id']."'";
			$this->imagePath=$this->imagePath."department/";
		}
		$rsImage = $this->obDb->fetchQuery();

		if($this->libFunc->checkImageUpload("image1"))
		{
			if(is_file($this->imagePath.$rsImage[0]->vImage1))
			{
				$fileUpload->deleteFile($this->imagePath.$rsImage[0]->vImage1);
			}
			$fileUpload->source	= $_FILES["image1"]["tmp_name"];
			$fileUpload->target	= $this->imagePath.$_FILES["image1"]["name"];
			$newName1				= $fileUpload->upload();
			if($newName1 != false) {
				$image1 = $newName1;
				// 
				if ($this->libFunc->ifSet($this->request,"resample")) {
					// This section is used by product, dept and content:
					switch ($this->request['type']) {
						case "product":
							$fileUpload->resampleImage ($this->imagePath.$newName1, UPLOAD_SMIMAGEWIDTH, UPLOAD_SMIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
							break;
						case "dept":
							$fileUpload->resampleImage ($this->imagePath.$newName1, UPLOAD_DEPTSMIMAGEWIDTH, UPLOAD_DEPTSMIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
							break;
						case "content":
							$fileUpload->resampleImage ($this->imagePath.$newName1, UPLOAD_CONTENTSMIMAGEWIDTH, UPLOAD_CONTENTSMIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
							break;
					}
				}
				// [/DRK]
			}
			$imagename="image1";
		}
		else
		{
			$image1 = $rsImage[0]->vImage1;
			$imagename=$this->request['current_image'];	
		}
				
		if($this->libFunc->checkImageUpload("image2"))
		{
			if(is_file($this->imagePath.$rsImage[0]->vImage2))
			{
				$fileUpload->deleteFile($this->imagePath.$rsImage[0]->vImage2);
			}
			$fileUpload->source = $_FILES["image2"]["tmp_name"];
			$fileUpload->target = $this->imagePath.$_FILES["image2"]["name"];
			$newName2 = $fileUpload->upload();
			if($newName2 != false) {
				$image2 = $newName2;
				// 
				if ($this->libFunc->ifSet($this->request,"resample")) {
					// This section is used by product and dept:
					switch ($this->request['type']) {
						case "product":
							$fileUpload->resampleImage ($this->imagePath.$newName2, UPLOAD_MDIMAGEWIDTH, UPLOAD_MDIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
							break;
						case "dept":
							$fileUpload->resampleImage ($this->imagePath.$newName2, UPLOAD_DEPTMDIMAGEWIDTH, UPLOAD_DEPTMDIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
							break;
					}
				}
				// [/DRK]
			}
			$imagename="image2";
		}
		else
		{
			$image2 = $rsImage[0]->vImage2;
			$imagename=$this->request['current_image'];
		}	
		if($this->libFunc->checkImageUpload("image3"))
		{
			if(is_file($this->imagePath.$rsImage[0]->vImage3))
			{
				$fileUpload->deleteFile($this->imagePath.$rsImage[0]->vImage3);
			}
			$fileUpload->source = $_FILES["image3"]["tmp_name"];
			$fileUpload->target = $this->imagePath.$_FILES["image3"]["name"];
			$newName3 = $fileUpload->upload();
			if($newName3 != false) {
				$image3 = $newName3;				 
				if ($this->libFunc->ifSet($this->request,"resample")) {
					$fileUpload->resampleImage ($this->imagePath.$newName3, UPLOAD_LGIMAGEWIDTH, UPLOAD_LGIMAGEHEIGHT, UPLOAD_JPGCOMPRESSION);
				}				 
			}
			$imagename="image3";	
		}
		else
		{
			$image3 = $rsImage[0]->vImage3;
			$imagename=$this->request['current_image'];	
		}

		if($this->request['type']=="product")
		{
			if($this->libFunc->checkFileUpload("image4"))
			{
				if(is_file($this->imagePath."../files/".$rsImage[0]->vDownloadablefile))
				{
					$fileUpload->deleteFile($this->imagePath."../files/".$rsImage[0]->vDownloadablefile);
				}
				$fileUpload->source = $_FILES["image4"]["tmp_name"];
				$fileUpload->target = $this->imagePath."../files/".$_FILES["image4"]["name"];
				$newName4  = $fileUpload->upload();
				if($newName4 != false)
					$image4 = $newName4;
				$imagename="image4";		
			}
			else
			{
				$image4 = $rsImage[0]->vDownloadablefile;
				$imagename=$this->request['current_image'];
			}	
		}
		
		if($this->request['type']=="product")
 		{
			$extraimages = explode(",",$rsImage[0]->tImages);
			$countExtraImage = count($extraimages);

			if (substr($this->request['current_image'], 0, -1)=='extraimage')
			{
				$j = substr($this->request['current_image'], -1);
				$t=$j-1;				
					if($this->libFunc->checkImageUpload("extraimage".$j))
						{
							if(is_file($this->imagePath.$extraimages[$t]))
							{
								$fileUpload->deleteFile($this->imagePath.$extraimages[$t]);
							}
							$fileUpload->source = $_FILES["extraimage".$j]["tmp_name"];
							$fileUpload->target = $this->imagePath.$_FILES["extraimage".$j]["name"];
							
							$newName[$j] = $fileUpload->upload();
							if($newName[$j] != false) {
								$image[$j] = $newName[$j];				 
							}
							$imagename="extraimage".$j;	
						}
						else
						{
							$image[$j] = $extraimages[$t];
							$imagename=$this->request['current_image'];	
						}
						$extraimages[$t]=$image[$j];
						
			}
						$imageString="";		
						for($i=0;$i<6;$i++)
						{
							$imageString.= $extraimages[$i].",";
						}
			
		}	
		//----
		
		if($this->request['type']=="product")
		{
			 $this->obDb->query="UPDATE ".PRODUCTS." SET 
						 `vImage1`='$image1', `vImage2`='$image2', `vImage3`='$image3',`tImages`= '".$imageString."', vDownloadablefile='$image4',
						 `tmEditDate`='".time()."', `iAdminUser` ='".$_SESSION['uid']."'  where iProdId_PK = ".$this->request['id'];			
		}
		elseif($this->request['type']=="content")
		{
			 $this->obDb->query="UPDATE ".CONTENTS." SET 
						 `vImage1`='$image1', `vImage2`='$image2', `vImage3`='$image3',
						 `tmEditDate`='".time()."', `vAdminUser` ='".$_SESSION['uid']."'  where iContentId_PK = ".$this->request['id'];			
		}
		else
		{
			 $this->obDb->query="UPDATE ".DEPARTMENTS." SET 
						 `vImage1`='$image1', `vImage2`='$image2', `vImage3`='$image3',
						 `tmEditDate`='".time()."', `vAdminUser` ='".$_SESSION['uid']."'  where iDeptId_PK = ".$this->request['id'];			
		}
		$this->obDb->updateQuery();

		if(!isset($imagename))
		{
			$imagename='image1';
		}
		$str = SITE_URL."ecom/adminindex.php?action=ec_show.dspUploadFrm&status=1&image=$imagename&id=".$this->request['id']."&type=".$this->request['type'];
				
		$this->libFunc->m_mosRedirect($str);
	}	
	
	# function to delete an department,product,article	
	function m_delete()
	{
		$obFile=new FileUpload(); 
		if(isset($this->request['id']) && !empty($this->request['id']) && isset($this->request['type']) && !empty($this->request['type']))
		{
			if($this->request['type']=="product")
			{
		        
                    
				$this->obDb->query = "SELECT vSeoTitle,vImage1,vImage2,vImage3,vDownloadablefile FROM ".PRODUCTS." WHERE iProdid_PK=".$this->request['id'];
				$this->imagePath=$this->imagePath."product/";
			}
			elseif($this->request['type']=="content")
			{
				$this->obDb->query = "SELECT vImage1,vImage2,vImage3 FROM ".CONTENTS." WHERE iContentid_PK=".$this->request['id'];
				$this->imagePath=$this->imagePath."content/";
			}
			else
			{
				$this->obDb->query = "SELECT vImage1,vImage2,vImage3 FROM ".DEPARTMENTS." WHERE iDeptid_PK=".$this->request['id'];
				$this->imagePath=$this->imagePath."department/";
			}
			$rs = $this->obDb->fetchQuery();
			$num_rows = $this->obDb->record_count;
			
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage1) && file_exists($this->imagePath.$rs[0]->vImage1))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage1);

				if(!empty($rs[0]->vImage2) && file_exists($this->imagePath.$rs[0]->vImage2))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage2);	
				
				if(!empty($rs[0]->vImage3) && file_exists($this->imagePath.$rs[0]->vImage3))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage3);


				#DELETING DOWNLOADABLE
				if($this->request['type']=="product")
				{
					if(!empty($rs[0]->vDownloadablefile) && file_exists($this->imagePath."../files/".$rs[0]->vDownloadablefile))
					$obFile->deleteFile($this->imagePath."../files/".$rs[0]->vDownloadablefile);	
				}

				#GETTING OWNER OF DELETED DEPARTMENT
				$this->obDb->query = "SELECT iOwner_FK,vOwnerType,iSort FROM ".FUSIONS." WHERE iSubId_FK=".$this->request['id']."  AND vtype='".$this->request['type']."' AND  iOwner_FK= '".$this->request['owner']."'";
				$rsOwner = $this->obDb->fetchQuery();
	
				#DELETING ENTRIES IN MAIN TABLE
				if($this->request['type']=="product")
				{
					#DELETING PRODUCT
					$this->obDb->query = "DELETE FROM ".PRODUCTS." WHERE iProdid_PK=".$this->request['id'];
					$this->obDb->updateQuery();
								
				}
				elseif($this->request['type']=="content")
				{
					#DELETING CONTENT
					$this->obDb->query = "DELETE from ".CONTENTS." WHERE iContentid_PK=".$this->request['id'];
					$this->obDb->updateQuery();
				}
				else
				{
					if($rsOwner[0]->iOwner_FK!="")
					{
						$this->m_deleteDepartment($this->request['id'],$rsOwner[0]->iOwner_FK);
					}
				}
				
				#RESORTING
				$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 WHERE (iSort>".$rsOwner[0]->iSort." AND iOwner_FK=".$rsOwner[0]->iOwner_FK." AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='".$this->request['type']."') ";
				//$this->obDb->updateQuery();

			}
		}
		else
		{
			$this->request['owner']=0;
		}			
		if(!isset($rsOwner[0]->vOwnerType))
		{
			$rsOwner[0]->vOwnerType='department';
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&type=".$rsOwner[0]->vOwnerType."&owner=".$this->request['owner']);	
	}

	#NOTES-PRODUCT & CONTENT WILL NOT BE DELETED BUT CONSIDERED AS ORPHAN PRODUCT
	# function to delete subdepartment
	function m_deleteDepartment($depid,$endDeptId)
	{

	    	
		$obFile=new FileUpload(); 
		$this->obDb->query = "SELECT iSubId_FK from ".FUSIONS." WHERE iOwner_FK='".$depid."'  AND vOwnerType='department' AND vtype='department'";
		$rsChild = $this->obDb->fetchQuery();
		$rsChildCount=$this->obDb->record_count;
		
		if($rsChildCount>0 && !empty($rsChild[0]->iSubId_FK))
		{
			$this->m_deleteDepartment($rsChild[0]->iSubId_FK,$endDeptId);
		}
		
		$this->obDb->query = "SELECT iOwner_FK FROM ".FUSIONS." WHERE iSubId_FK='".$depid."' AND vtype='department'";
		$rsOwner = $this->obDb->fetchQuery();
		$rsOwnCount=$this->obDb->record_count;
		if($rsOwnCount>0)
		{
			$this->obDb->query = "SELECT vTitle,vImage1,vImage2,vImage3 FROM ".DEPARTMENTS." WHERE iDeptid_PK='".$depid."'";
			$rs = $this->obDb->fetchQuery();
			
            
			$num_rows = $this->obDb->record_count;
			if($num_rows==1)
			{
				#DELETING IMAGES 
				if(!empty($rs[0]->vImage1) && file_exists($this->imagePath.$rs[0]->vImage1))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage1);

				if(!empty($rs[0]->vImage2) && file_exists($this->imagePath.$rs[0]->vImage2))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage2);	
				
				if(!empty($rs[0]->vImage3) && file_exists($this->imagePath.$rs[0]->vImage3))
					$obFile->deleteFile($this->imagePath.$rs[0]->vImage3);
			}
	
			#DELETING RELATIONAL ENTRY FROM FUSION
			$this->obDb->query = "DELETE FROM ".FUSIONS." WHERE iSubId_FK='".$depid."' AND vtype='department'";
			$this->obDb->updateQuery();
            


			#DELETING DEPARTMENT
			$this->obDb->query = "DELETE FROM ".DEPARTMENTS." WHERE iDeptid_PK='".$depid."'";
			$this->obDb->updateQuery();

			$this->obDb->query = "DELETE FROM ".FUSIONS." WHERE iOwner_FK='".$depid."' AND vtype='product' AND vOwnerType='department'";
			$this->obDb->updateQuery();
			$this->obDb->query = "DELETE FROM ".FUSIONS." WHERE iOwner_FK='".$depid."' AND vtype='content' AND vOwnerType='department'";
			$this->obDb->updateQuery();

			if($rsOwner[0]->iOwner_FK!=$endDeptId && $rsOwner[0]->iOwner_FK!="")
			{
					$this->m_deleteDepartment($rsOwner[0]->iOwner_FK,$endDeptId);
			}
            
            
            
		}
	}#ef

	# function to delete an department,product,article	
	function m_deleteInstance()
	{
		$obFile=new FileUpload(); 
		if(isset($this->request['id']) && !empty($this->request['id']) && isset($this->request['type']) && !empty($this->request['type']) && isset($this->request['owner']))
		{
				#GETTING OWNER OF DELETED DEPARTMENT
				 $this->obDb->query = "SELECT vOwnerType,iSort FROM ".FUSIONS." where iSubId_FK='".$this->request['id']."' AND vtype='".$this->request['type']."' AND iOwner_FK='".$this->request['owner']."'";
				$rsOwner = $this->obDb->fetchQuery();

				#DELETING RELATIONAL ENTRY FROM FUSION
				$this->obDb->query = "DELETE FROM ".FUSIONS." WHERE (iSubId_FK=".$this->request['id']."  AND vtype='".$this->request['type']."' AND iOwner_FK=".$this->request['owner'].")";
				$this->obDb->updateQuery();

				$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 WHERE (iSort>".$rsOwner[0]->iSort." AND iOwner_FK='".$this->request['owner']."' AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='".$this->request['type']."')";
				$this->obDb->updateQuery();
				
				$this->obDb->query = "SELECT count(*) as cnt FROM ".FUSIONS." WHERE  iSubId_FK=".$this->request['id']." AND vtype='".$this->request['type']."'";
				$rsCnt = $this->obDb->fetchQuery();
				if($rsCnt[0]->cnt==0)
				{
					#DELETING RELATIONAL ENTRY FROM FUSION
					$this->obDb->query = "DELETE FROM ".FUSIONS." WHERE (iOwner_FK=".$this->request['id']." AND vOwnerType='".$this->request['type']."')";
					$this->obDb->updateQuery();
				}
				#RESORTING
				$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 WHERE (iSort>".$rsOwner[0]->iSort." AND iOwner_FK=".$this->request['owner']." AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='".$this->request['type']."')";
				$this->obDb->updateQuery();
		}
		else
		{
			$this->request['owner']=0;
		}			
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&type=".$rsOwner[0]->vOwnerType."&owner=".$this->request['owner']);	
	}

	#FUNCTION TO UPDATE SORTING
	function m_updateSort()
	{
		$sortedArray=explode(',' , $this->request['sorted_list']);
		$cntSortedArray=count($sortedArray);
		for($i=0;$i<$cntSortedArray-1;$i++)
		{
			$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=".($i+1)." where fusionid=".$sortedArray[$i];
			$this->obDb->updateQuery();
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.home&owner=".$this->request['owner']."&type=".$this->request['type']);	
	}

	#FUNCTION TO UPDATE ASSOCIATE- Modified on 04-05-2007
	function m_updateAssociate()
	{
		if(isset($this->request['items'])){
			$items=$this->request['items'];
			$this->obDb->query = "SELECT MAX(iSort) as maxsort from ".FUSIONS." where  vtype='".$this->request['type']."' AND vOwnerType='".$this->request['otype']."' AND iOwner_FK='".$this->request['owner']."'";
			$rsSort=$this->obDb->fetchQuery();
			$sortNum=$rsSort[0]->maxsort;
			foreach($items as $itemselected)
			{	
				$this->obDb->query ="SELECT count(*) as totalCnt from ".FUSIONS." WHERE ";
				$this->obDb->query.="(vOwnerType='".$this->request['otype']."' AND ";
				$this->obDb->query.="iSubId_FK ='".$itemselected."' AND vtype='".$this->request['type']."'";
				$this->obDb->query.="AND iOwner_FK='".$this->request['owner']."')";
				$rsCount = $this->obDb->fetchQuery();

				$recordCount=$rsCount[0]->totalCnt;
				if($recordCount==0)
				{
					$sortNum++;
					#ADD MULTIPLE ITEMS - ADDED BY HSG- 04-05-07
					$this->obDb->query ="INSERT INTO  ".FUSIONS." SET  vOwnerType='".$this->request['otype']."',";
					$this->obDb->query.="iOwner_FK=".$this->request['owner'].",	iSubId_FK ='".$itemselected."', ";
					$this->obDb->query.="iSort=".$sortNum.", vtype='".$this->request['type']."'";
					$this->obDb->query.=",iState='1'" ;
					$this->obDb->updateQuery();
				}
			}
		}#END ITEM SET CHECK
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.associate&otype=".$this->request['otype']."&owner=".$this->request['owner']."&type=".$this->request['type']."&postOwner=".$this->request['postOwner']);	
	}

	function m_delRelation()
	{
		$this->obDb->query = "SELECT iOwner_FK,vOwnerType,iSort FROM ".FUSIONS." WHERE fusionid='".$this->request['fid']."'";
		$rsOwner = $this->obDb->fetchQuery();

		$this->obDb->query = "DELETE FROM  ".FUSIONS." WHERE (fusionid='".$this->request['fid']."')" ;
		$this->obDb->updateQuery();
		
		$this->obDb->query = "UPDATE ".FUSIONS." SET iSort=iSort-1 where (iSort>".$rsOwner[0]->iSort." AND iOwner_FK=".$rsOwner[0]->iOwner_FK." AND vOwnerType='".$rsOwner[0]->vOwnerType."' AND vtype='".$this->request['type']."') ";
		$this->obDb->updateQuery();
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.associate&otype=".$this->request['otype']."&owner=".$this->request['owner']."&type=".$this->request['type']);	
	}

	#FUNCTION TO ADD VOLUMJE DISCOUNT CORRESPONDING TO SELECTED PRODUCT
	function m_addDiscount()
	{
		$timeStamp=time();
	
		if(isset($this->request['itemid']))
		{
			$sort=1;
			$cnt=count($this->request['itemid']);
			for($i=0;$i<$cnt;$i++)
			{
					 $this->obDb->query="UPDATE ".VDISCOUNTS." SET 
					`iRangea`='".$this->libFunc->checkWrongValue($this->request['rangea'][$i])."',
					`iRangeb`='".$this->libFunc->checkWrongValue($this->request['rangeb'][$i])."',
					`fDiscount`='".$this->libFunc->checkFloatValue($this->request['discount'][$i])."',
					`iSort`='".$this->libFunc->checkWrongValue($sort)."'  
					WHERE iDiscountId ='".$this->request['itemid'][$i]."'"; 
					
					$this->obDb->updateQuery();
					$sort++;
			}
		}
		$this->request['newrangea']=intval($this->request['newrangea']);
		if(isset($this->request['newrangea']) && $this->request['newrangea']>0)
		{	
			$this->obDb->query="SELECT max(iSort) as maxsort FROM  ".VDISCOUNTS." WHERE iDiscountId='".$this->request['productid']."'";
			$rsSort=$this->obDb->fetchQuery();
			$sort=$rsSort[0]->maxsort+1;
			#INSERTING TO NEW DISCOUNT VALUES
		
			$this->obDb->query="INSERT INTO ".VDISCOUNTS."
			(`iDiscountId`,`iProductId_FK`,`iRangea`,`iRangeb`,`fDiscount`,`iSort`) 
				values('','".$this->request['productid']."',
				'".$this->libFunc->checkWrongValue($this->request['newrangea'])."',
				'".$this->libFunc->checkWrongValue($this->request['newrangeb'])."',
				'".$this->libFunc->checkFloatValue($this->request['newdiscount'])."',
				'".$this->libFunc->checkWrongValue($sort)."')";
				$this->obDb->updateQuery();

		}	
		if(isset($this->request['del']))
		{
			$del=$this->request['del'];
			foreach($del as $delid=>$delValue)
			{
				$this->obDb->query="DELETE FROM ".VDISCOUNTS." WHERE iDiscountid='".$delid."'"; 
				$this->obDb->updateQuery();
			}
		}
		$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.vdiscount&productid=".$this->request['productid']."&owner=".$this->request['owner']."&type=".$this->request['type']);	
	}

	#FUNCTION TO ATTACH OPTION
	function m_attach()
	{
		$sortedArray=explode(',' , $this->request['sorted_list']);
		$cntSortedArray=count($sortedArray);
		if($this->request['prtype']=="choice")
		{
			$this->obDb->query = "DELETE FROM ".PRODUCTCHOICES." WHERE  iProductid_FK=".$this->request['productid'];
				$this->obDb->updateQuery();
			for($i=0;$i<$cntSortedArray-1;$i++)
			{
				$this->obDb->query = "INSERT INTO ".PRODUCTCHOICES." SET iSort='".($i+1)."',iProductid_FK='".$this->request['productid']."',iChoiceid='".$sortedArray[$i]."'";
				$this->obDb->updateQuery();
			}
		}
		elseif($this->request['prtype']=="option")
		{
				$this->obDb->query = "DELETE FROM ".PRODUCTOPTIONS." WHERE  iProductid_FK=".$this->request['productid'];
				$this->obDb->updateQuery();
			for($i=0;$i<$cntSortedArray-1;$i++)
			{
				$this->obDb->query = "INSERT INTO ".PRODUCTOPTIONS." SET iSort=".($i+1).",iProductid_FK='".$this->request['productid']."',iOptionid='".$sortedArray[$i]."'";
				$this->obDb->updateQuery();
			}

		}
		if($this->request['vdiscount']=='1')
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.vdiscount&productid=".$this->request['productid']."&owner=".$this->request['owner']."&type=".$this->request['type']);	
		}
		else
		{
			$this->libFunc->m_mosRedirect(SITE_URL."ecom/adminindex.php?action=ec_show.attachOpt&productid=".$this->request['productid']."&prtype=".$this->request['prtype']."&owner=".$this->request['owner']."&type=".$this->request['type']."&msg=1");	
		}
	}#ef
	
	function ToggleProductAllDepartments($status,$pid)
	{
		if(isset($status) && is_numeric($status) && isset($pid) && is_numeric($pid))
		{
			$this->obDb->query = "UPDATE ".FUSIONS." SET iState='".$status."' WHERE iSubId_FK='".$pid."'";
			$this->obDb->updateQuery();
		}
	}
}#ec
?>
