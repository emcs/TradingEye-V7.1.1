<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
include(SITE_PATH.'libs/csvimporter/FileReader.php' );
include(SITE_PATH.'libs/csvimporter/CSVReader.php' );
class c_csvImporter
{

	#CONSTRUCTOR
	function c_csvImporter()
	{
		$this->libFunc						=new c_libFunctions();
		$this->imagePath					=SITE_PATH."images/csv/";
		$this->err							=0;
		$this->errMsg						="";
		$this->productTemplatePath	=MODULES_PATH."ecom/templates/main/product/";
		$this->layoutTemplatePath	=MODULES_PATH."default/templates/main/layout/";
	}

	function m_getTitle($ownerid,$type)
	{
		if($ownerid!=0)
		{
			$this->obDb->query = "SELECT vTitle,iOwner_FK,vOwnerType FROM ".DEPARTMENTS." D ,".FUSIONS." F WHERE iDeptid_PK=iSubId_FK and iSubId_FK=".$ownerid." AND vtype='".$type."'" ;
			$row = $this->obDb->fetchQuery();
			if($this->obDb->record_count != 0)
			{
				$_SESSION['dspTitle']=" /".$row[0]->vTitle.$_SESSION['dspTitle'];
				$this->m_getTitle($row[0]->iOwner_FK,$row[0]->vOwnerType);
			}
		}
			return $_SESSION['dspTitle'];
	}

	#FUNCTION USED FOR DISPLAY IMPORT FORM
	function m_uploadFormCsv()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_EDITOR_FILE",$this->csvTemplate);

		$this->ObTpl->set_block("TPL_EDITOR_FILE","TPL_IMPORTER_BLK","import_blk");
		$this->ObTpl->set_block("TPL_IMPORTER_BLK","TPL_OPTIONS_BLK","options_blk");
		$this->ObTpl->set_block("TPL_EDITOR_FILE","TPL_DEPARTMENT_BLK", "dept_blk");
		$this->ObTpl->set_block("TPL_EDITOR_FILE","TPL_TEMPLATE_BLK","template_blk");
		$this->ObTpl->set_block("TPL_EDITOR_FILE","TPL_LAYOUT_BLK","layout_blk");


		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("dept_blk","");
		$this->ObTpl->set_var("options_blk","");
		$this->ObTpl->set_var("layout_blk","");
		$this->ObTpl->set_var("template_blk","");
		$this->ObTpl->set_var("import_blk","");
		$this->ObTpl->set_var("SELECTED2","");
		$this->ObTpl->set_var("TPL_VAR_ERRORMSG","");

		$this->ObTpl->set_var("TPL_VAR_DELIM1","");
		$this->ObTpl->set_var("TPL_VAR_DELIM2","");
		$this->ObTpl->set_var("TPL_VAR_DELIM3","");

		$this->ObTpl->set_var("TPL_VAR_PRODTEMPLATE1","");
		$this->ObTpl->set_var("TPL_VAR_PRODTEMPLATE2","");
		
	   if(isset($this->request['errMsg'])){
                switch($this->request['errMsg']){
                case 1:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Sorry, no customer found! <br>");
                break;
                case 2:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Sorry, no transaction found! <br>");
                break;
                case 3:
                $this->ObTpl->set_var("TPL_VAR_ERRORMSG","Sorry, no enquiry found! <br>");
                break;
                }
        }

		if(!isset($this->request['department']))
		{
			$this->request['department']="";
		}

		if(!isset($this->request['delimiter']))
		{
			$this->request['delimiter']="";
		}

		if(!isset($this->request['template']))
		{
			$this->request['template']="";
		}

		if(!isset($this->request['layout']))
		{
			$this->request['layout']="";
		}
		switch($this->request['delimiter'])
		{
			case "|":
			$this->ObTpl->set_var("TPL_VAR_DELIM1","selected");
			break;
			case ";":
			$this->ObTpl->set_var("TPL_VAR_DELIM2","selected");
			break;
			default:
			$this->ObTpl->set_var("TPL_VAR_DELIM3","selected");
			break;
		}

		if($this->request['template']=="product_package.htm")
		{
			$this->ObTpl->set_var("TPL_VAR_PRODTEMPLATE2","selected");
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_PRODTEMPLATE1","selected");
		}

		if($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_CSV_NOTUPLOADED);
		}
		elseif(isset($this->request['msg']) && $this->request['msg']==5)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_CSV_REQUIRED);
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_CSV_MANDATORY);
		}

		#ARRAY OF FIELD NOT DISPLAYED IN SELECTCT BOX
		$notAllowedArray=array('iProdid_PK','vTemplate','vLayout','iKit','iAdminUser','fPointIncrease','tmBuildDate','tmEditDate','iInventoryMinimum');


		$this->obDb->table=PRODUCTS;
		$resFields=$this->obDb->listFields();
		$columnCount=$this->obDb->count_fields;
		$k=0;
		for ($i = 0; $i < $columnCount; $i++)
		{
			$fieldName=mysql_field_name($resFields, $i);
			if(!in_array($fieldName,$notAllowedArray))
			{
				$field[$k]=$fieldName;
				$k++;
			}
		}

		$noOfSelect=$columnCount-count($notAllowedArray);

		for ($j = 0; $j < $noOfSelect; $j++)
		{
			$this->ObTpl->set_var("options_blk","");
			for ($i = 0; $i < $noOfSelect; $i++)
			{
				$this->ObTpl->set_var("TPL_VAR_SELECTED","");
				if(!isset($this->request['item'][$j]))
				{
					$this->request['item'][$j]="";
				}
				if($field[$i]==$this->request['item'][$j])
				{
					$this->ObTpl->set_var("TPL_VAR_SELECTED","selected");
				}				
				if($field[$i] == "iVendorid_FK") $field[$i]= "vSupplier_name";
				$this->ObTpl->set_var("TPL_VAR_NAME",$field[$i]);
				$this->ObTpl->parse("options_blk","TPL_OPTIONS_BLK",true);
			}
			if($j<9)
				$this->ObTpl->set_var("TPL_VAR_SPACE",'&nbsp;&nbsp;');
			else
				$this->ObTpl->set_var("TPL_VAR_SPACE",'');

			$this->ObTpl->set_var("TPL_VAR_COUNT",$j+1);
			$this->ObTpl->parse("import_blk","TPL_IMPORTER_BLK",true);
		}
		#IMPORTER BLK END

		#START DISPLAY DEPARETMENT BLOCK
		$this->obDb->query = "SELECT vTitle,iDeptId_PK FROM ".DEPARTMENTS.", ".FUSIONS."  WHERE iDeptId_PK=iSubId_FK AND vType='department'";
		$deptResult = $this->obDb->fetchQuery();
		 $recordCount=$this->obDb->record_count;
		#PARSING DEPARTMENT BLOCK
		if($this->request['department']==-1)
			$this->ObTpl->set_var("SELECTED_CSV","selected");
		else
			$this->ObTpl->set_var("SELECTED_CSV","");

		if($recordCount>0)
		{
			for($i=0;$i<$recordCount;$i++)
			{
				$_SESSION['dspTitle']="";		
				if($deptResult[$i]->iDeptId_PK==$this->request['department'])
					$this->ObTpl->set_var("SELECTED2","selected");					
				$this->ObTpl->set_var("TPL_VAR_TITLE",$this->m_getTitle($deptResult[$i]->iDeptId_PK,'department'));
				$this->ObTpl->set_var("TPL_VAR_ID",$deptResult[$i]->iDeptId_PK);
				$this->ObTpl->parse("dept_blk","TPL_DEPARTMENT_BLK",true);
			}
		}
		$this->selectbox_files($this->ObTpl,'TPL_TEMPLATE_BLK','template_blk',$this->productTemplatePath,$this->request['template'],'TPL_VAR_TEMPLATENAME');
		$this->selectbox_files($this->ObTpl,'TPL_LAYOUT_BLK','layout_blk',$this->layoutTemplatePath,$this->request['layout'],'TPL_VAR_LAYOUT');

		return($this->ObTpl->parse("return","TPL_EDITOR_FILE"));
	}#END FUNCTION

	function m_uploadCsv() {
		
			$this->errMsg="";
			$requiredArray=array("vTitle","vSeoTitle","vSku","fPrice");
			foreach($requiredArray as $a)
			{
				if(!in_array($a,$this->request['item']))
				{
					$this->err=1;
					$this->errMsg=MSG_CSV_REQUIRED."<br />";
				}
			}
			$ext=substr($_FILES['import_file']['name'],-3);
			if($ext!="csv")
			{
				$this->err=1;
				$this->errMsg.=MSG_INVALID_CSV;
			}
			if($_FILES["import_file"]["size"]==0)
			{
				$this->err=1;
				$this->errMsg.=MSG_INVALID_CSV;
			}

			if($this->err==1)
			{
				$this->csvTemplate=$this->templatePath."csvImporter.tpl.htm";
				$this->obTpl->set_var("TPL_VAR_BODY",$this->m_uploadFormCsv());
				return false;
			}
			
			$fileUpload = new FileUpload();
			$libFunc=new c_libFunctions();
			if($this->libFunc->checkFileUpload("import_file"))
			{
				$fileUpload->source = $_FILES["import_file"]["tmp_name"];
				$fileUpload->target = $this->imagePath.$_FILES["import_file"]["name"];
				$newName = $fileUpload->upload();
			}
			else
			{
				$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=csv.home&msg=1");
				exit;
			}
			$this->csvImporter($this->imagePath.$newName);
		}	#FUNCTION END

	function csvImporter($csvfile){
			
		$_SESSION['uid']=$this->libFunc->ifSet($_SESSION,'uid',"");
		$itemstring="";
		$log="";
		$itemCount=0;
		$successCount=0;
		
		
		#Initial number of intert and update product
		
		$totalUpdate = 0;
		$totalInsert = 0;
		
		$itemArray=array();
		
		$reader =new CSVReader( new FileReader( $csvfile ) );
		$reader->setSeparator($this->request['delimiter']);
		$line = 0;
				
		#GETTING TOTAL COLUMNS SELECTED
		foreach($this->request['item'] as $item)
		{
			if(!empty($item) && !in_array($item,$itemArray))
			{
				array_push($itemArray,$item);
				if($item == "vSupplier_name") $item = "iVendorid_FK";				
				$itemstring.=$item.",";
				$itemCount++;
			}
		}
		
		
		$itemstring=substr($itemstring,0,-1);
		
					
		$query="INSERT INTO ". PRODUCTS ." ($itemstring,`tmBuildDate`,`iAdminUser`) VALUES(";
			
		# READ THROUGH THE CSV FILE
		while( false != ( $cell = $reader->next() ) )
		{
			
			$queryUPDATE=" UPDATE ".PRODUCTS." SET ";
			
			$skip=0;
			$valueString="";
			$reason="";
			$title="";
			
			#echo"<pre>";
			#print_r($cell)."<br />";
			#echo"</pre><br />";
			#echo "item count =".$itemCount;
			#echo "count cell =".count($cell);
			
			if($itemCount != count($cell))
			{			
					$skip=1;
					$reason="Column Mismatch in the line number ".$line;
			}
			else
			{
				#FOR EACH ELEMENT FROM THE LINE NEED TO BE ASSOCIATE AND ORDERD WITH IMPORTER DROP-DOWN LIST 
				for ( $i = 0; $i < $itemCount; $i++ )
				{
					#echo $cell[$i]." -------> ".$this->request['item'][$i]." ";					
					if($this->request['item'][$i]=='vTitle')
					{
						if(!isset($cell[$i]) || empty($cell[$i]))
						{
							$skip=1;
							$reason="<br /><b>Title empty</b>";
							$title="";
						}
						else
						{
							$title=$cell[$i];
						}
					}
					elseif($this->request['item'][$i]=='vSku')
					{
						if(!isset($cell[$i]) || empty($cell[$i]))
						{
							$skip=1;
							$reason="<br /><b>SKU empty</b>";
						}
						else{
							$sku = $cell[$i];
						}
					}
					elseif($this->request['item'][$i]=='fPrice')
					{							
						if(!isset($cell[$i]) || empty($cell[$i]))
						{
							$skip=1;
							$reason="<br /><b>Price empty or not valid</b>";
						}else{											
							$price = $cell[$i];
						}
						
					}
					elseif($this->request['item'][$i]=='vSeoTitle')#SEOTITLE
					{
						
						if(!isset($cell[$i]) || empty($cell[$i]))
						{
							if(empty($title))
							{
								$skip=1;
								$reason="<br /><b>Seo Title empty</b>";
							}								
						}						
						else
						{
							$seoTitle = $cell[$i];
						}
					}
					
					
					if(isset($cell[$i]))
					{												
						if ($itemArray[$i] == "vSupplier_name") {
							
							# RETRIEVE SUPPLIER ID BASED ON SUPPLIER NAME
							
							$this->obDb->query = " SELECT  iVendorid_PK  FROM ". SUPPLIERS . " WHERE vCompany = '".$this->libFunc->m_addToDB($cell[$i])."'";
							$supRow = $this->obDb->fetchQuery();
														
							if ($this->obDb->record_count > 0){
								$supplierID = 	$supRow[0]->iVendorid_PK;
							} else {
								$supplierID = 0;
							}
							
							$valueString.="'".$this->libFunc->m_addToDB($cell[$i])."',";							
							$queryUPDATE.=" iVendorid_FK = '".$supplierID."',";
							
						}else{
						
						$valueString.="'".$this->libFunc->m_addToDB($cell[$i])."',";							
						$queryUPDATE.=" ".$itemArray[$i]." = '".$this->libFunc->m_addToDB($cell[$i])."',";
						}
						
					}
					else
					{
						$valueString.="' ',";						
					}
				}#END FOR				
			}#END IF SKIP
			if($skip==1)
			{
				$valueString1=substr($valueString,0,-1);
				if(strlen($valueString1)>50)
				{
					$valueString1=substr($valueString1,0,50)."...";
				}
				$log.=$this->libFunc->m_displayContent($valueString1).$reason."<br>";
			}
			else
			{
				$timeStamp=time();
				$valueString1=substr($valueString,0,-1);
				
				# Insert The SESSION ID
				$query1=$query.$valueString1.",".$timeStamp.",'".$_SESSION['uid']."')";
				$successCount++;
				
				$queryUPDATE.= "tmBuildDate =".$timeStamp.",
							    iAdminUser =".$_SESSION['uid']." WHERE ";
				
									
				# CHECK IF THE PRODUCT SEO TITLE ALREADY EXIST IN DATABASE	
				$this->obDb->query = "SELECT *  FROM ".PRODUCTS." 
									  WHERE vSeoTitle='".$this->libFunc->m_addToDB($seoTitle)."'										  										  	
									  ";
				
				# YOU CAN ADD MORE FIELDS TO THE CONDITION AS BELOW
				/*
				  AND 	vSku	= '".$this->libFunc->m_addToDB($sku)."'
				  AND  	vTitle	= '".$this->libFunc->m_addToDB($title)."'
				  AND	fPrice	= ".$this->libFunc->m_addToDB($price)."
				 */
				 				 
				$resCnt = $this->obDb->fetchQuery();
				$resCntCount = $this->obDb->record_count;				
				
				if($resCntCount>0)
				{
					# DO THE UPDATE
					$this->obDb->query = "SELECT iProdid_PK FROM ".PRODUCTS." 
										  WHERE vSeoTitle='".$this->libFunc->m_addToDB($seoTitle)."'";
					$rowPK = $this->obDb->fetchQuery();						
					$queryUPDATE.= " iProdid_PK =".$rowPK[0]->iProdid_PK;	
											
					$this->obDb->query=$queryUPDATE;
					$this->obDb->execQry($this->obDb->query);					
					$totalUpdate++;
																		
				}
				else{
				
					# DO THE INSERT
					$this->obDb->query=$query1;					
					$this->obDb->updateQuery();
					$subObjId=$this->obDb->last_insert_id;
					$this->obDb->query="UPDATE ".PRODUCTS." SET vTemplate='".$this->request['template']."' ,vLayout='".$this->request['layout']."',iCartButton ='1'  WHERE iProdId_PK='$subObjId'";
					$this->obDb->updateQuery();
					
					#Increase totalInsert by 1
					if ($this->obDb->last_insert_id >0)	$totalInsert++;
					
					#GETTING MAXIMUM SOR UNDER INSERTED OWNER
					$this->obDb->query="select MAX(iSort) AS MaxSort
					from ".FUSIONS."	where iOwner_FK = ".$this->request['department']." and vOwnerType = 'department' and vType ='product'";
					$res = $this->obDb->fetchQuery();
					$sort=$res[0]->MaxSort+1;
					
					#INSERTING TO FUSIONS TABLE
					$this->obDb->query="insert into ".FUSIONS."
					(`fusionId`, `iOwner_FK`, `iSubId_FK`,`vtype`, `iSort`, `iState`,`vOwnerType`)
					values('',".$this->request['department'].",'$subObjId','product','$sort','1','department')";
					$this->obDb->updateQuery();
				}				 				
			}			
			$line++;		
			#POINT TO THE NEXT LINE
			$cell = $reader->next();			
				
		}#END WHILE
		unlink($csvfile);
		if($successCount==0)
		{
			$hint="<br />Please ensure that you had selected correct delimiter.<br />Total number of columns in CSV should be greater than or equal to fields selected by you.";
		}
		else
		{
			$hint="";
		}
		$_SESSION['status']=$successCount." products successfully uploaded <br />".$log.$hint;
		$_SESSION['csvUpdatedTotal'] =$totalUpdate;
		$_SESSION['csvInsertedTotal'] =$totalInsert;
												  
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=csv.dspmsg");
		exit;
	}#END FUNCTION



	/* Method to display the error message
	 * 
	 * @return String
	 * @param 
	 */
	function m_dspMessage()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_EDITOR_FILE",$this->messageTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		
		$this->ObTpl->set_var("TPL_VAR_STATUS",$_SESSION['status']);
		
		$this->ObTpl->set_var("LANG_ADMIN_TOTAL_UPDATED_PRODUCTS",LANG_ADMIN_TOTAL_UPDATED_PRODUCTS);
		$this->ObTpl->set_var("LANG_ADMIN_TOTAL_INSERTED_PRODUCTS",LANG_ADMIN_TOTAL_INSERTED_PRODUCTS);
		
		$this->ObTpl->set_var("TPL_TOTAL_UPDATED_PRODUCTS",$_SESSION['csvUpdatedTotal']);
		$this->ObTpl->set_var("TPL_TOTAL_INSERTED_PRODUCTS",$_SESSION['csvInsertedTotal']);
	
		
		return $this->ObTpl->parse("return","TPL_EDITOR_FILE");
	}



/* Method to get file name
  	
  Input:
  @$templateobj template object
  @$block       block name
  @$hblock		Block handler
  @$path		dir path
  @$selected	selected

  Output
	return template with the variable  TPL_VAR_TEMPLATENAME which show all the files of directory with the extension of  htm|html|tpl|tpl.html|tpl.htm.

	SELTEMPLATE is selected or not.
*/
function selectbox_files(&$templateobj,$block,$hblock,$path,$selected,$outputvariable){
	$templateobj->set_var($hblock,"");
	if (is_dir($path)) {
		if ($dh = opendir($path)) {			
			while (($templateName = readdir($dh)) !== false) {

				if($templateName!="." && $templateName!="..") {
					if(preg_match("/([\.htm|html|tpl|tpl.html|tpl.htm])$/",$templateName)){
						if($templateName==$selected) {
							$sel='selected';
						}
						else {
							$sel='';
						}
						$templateobj->set_var('SELTEMPLATE',$sel);
						$templateobj->set_var($outputvariable,$templateName);
						$templateobj->parse($hblock,$block,true);
					}// end of ereg
				}// end of if
			}// end of while
			closedir($dh);
		}// end of if
	}// end of if
}//end of select box

function m_exportCSV(){
 if (isset($this->request['exporttype']))
 {
    switch($this->request['exporttype']){
        case "cu":
        $this->m_exportCustomer();
        break;
        case "tr":
        $this->m_exportTransaction();
        break;
        case "en":
        $this->m_exportEnquiry();
        break;
		 case "pr":
        $this->m_exportProducts();
        break;
    }//end switch
 }//end if
}//end m_exportCSV    

function m_exportCustomer()
{
        $this->err=0;
			
		#QUERY RETRIEVE INFORMATION FOR CUSTOMER TABLE
		$this->obDb->query  = " SELECT distinct '' as accountRef, vLastName,iCustmerid_PK,
							    SUBSTRING(concat(vFirstName,' ',vLastName),1,60) as Name,
								SUBSTRING(vEmail,1,50) as Email,
							    SUBSTRING(vAddress1,1,60) as vAddress1,
							    SUBSTRING(vAddress2,1,60) as vAddress2,
							    SUBSTRING(vCity,1,60) as vCity,
							    SUBSTRING(if(vState,vState,vStateName),1,60) as stateName,
							    SUBSTRING(vZip,1,60) as vZip,
							   	SUBSTRING(concat(vFirstName,' ',vLastName),1,60) as contactName,
							   	SUBSTRING(vPhone,1,30) as vPhone,
								''    as fax,
								''    as analysis1,
								''    as analysis2,
								''    as analysic3,
								0     as departmentNo,
								''    as vatRegistrationNo,
								0.00  as turnoverMTD,
								0.00  as turnoverYID,
								0.00  as priorYID,
								0.00  as creditLimit,
								'' 	  as terms,
								0     as settlementDueDays,
								0.00  as settlementDiscountRate,
								'4000' as nominalCode,
								'T1'  as textCode FROM ".CUSTOMERS." WHERE iStatus = 1";
					    
		$rowCustomer = $this->obDb->fetchQuery();	
		$recordCount = $this->obDb->record_count;
	
		if($this->err==0)
		{						
			
            if($recordCount>0)
				{	
					$csv_output ="";
										
					for($i=0;$i<$recordCount;$i++)
					{										
						# QUERY TO GET STATE
						$this->obDb->query = "SELECT vStateName FROM ".STATES." WHERE iStateId_PK = '".$rowCustomer[$i]->stateName."'";						
						$stateRow = $this->obDb->fetchQuery();	
						$stateRowCt = $this->obDb->record_count;
						
						if ($stateRowCt >0){
						$rowCustomer[$i]->stateName   = $stateRow[0]->vStateName;
						} else {
						$rowCustomer[$i]->stateName   = "";
						}											
						
						$rowCustomer[$i]->accountRef  =  strtoupper(substr($rowCustomer[$i]->vLastName,0,3)).$rowCustomer[$i]->iCustmerid_PK;
						
						$rowCustomer[$i]->nominalCode = SAGE_NOMINAL_CODE;
						
														
						$csv_output .= 	'"'.$rowCustomer[$i]->accountRef.'",';		 
						$csv_output .= 	'"'.$rowCustomer[$i]->Name.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->Email.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->vAddress1.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->vAddress2.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->vCity.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->stateName.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->vZip.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->contactName.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->vPhone.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->fax.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->analysis1.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->analysis2.'",';
						$csv_output .=  '"'.$rowCustomer[$i]->analysic3.'",';
						$csv_output .= 	    $rowCustomer[$i]->departmentNo.',';
						$csv_output .=  '"'.$rowCustomer[$i]->vatRegistrationNo.'",';
						$csv_output .= 	    $rowCustomer[$i]->turnoverMTD.',';
						$csv_output .= 	    $rowCustomer[$i]->turnoverYID.',';
						$csv_output .= 		$rowCustomer[$i]->priorYID.',';
						$csv_output .= 		$rowCustomer[$i]->creditLimit.',';
						$csv_output .= 	'"'.$rowCustomer[$i]->terms.'",';
						$csv_output .= 		$rowCustomer[$i]->settlementDueDays.',';
						$csv_output .= 		$rowCustomer[$i]->settlementDiscountRate.',';						
						$csv_output .= 	'"'.$rowCustomer[$i]->nominalCode.'",';
						$csv_output .= 	'"'.$rowCustomer[$i]->textCode.'",';
						$csv_output .=  " \n";
																    
					}							
					header( "Content-Type: application/save-as" );
					header( 'Content-Disposition: attachment; filename=customers.csv');
				    echo $csv_output;
				    exit;  				
				}		
				else {
					header('Location: '.SITE_URL.'admin/adminindex.php?action=csv.home&errMsg=1;');
				}		
	
        }
}//end Export customer functon


function m_exporttransaction()
	{
	  
		$this->err=0;				
        #QUERY RETRIEVE INFORMATION FOR ORDER TABLE
		$this->obDb->query  = " SELECT distinct '' as transactionType, vLastName, iCustomerid_FK, 
							    iInvoice 		   as bankAccountRef,
								0    	           as departmentNo,
								tmOrderDate   	   as transactionDate,
								iInvoice 		   as invoice,
								''     	 		   as transactionDetail,
								fTotalPrice		   as netAmount,
								''  	           as taxCode,
								'' 		 		   as taxAmount,
								vAltCountry, fTaxPrice, vCountry							
								FROM ".ORDERS." WHERE iOrderStatus = 1";
								
		$rowOrder    = $this->obDb->fetchQuery();	
		$recordCount = $this->obDb->record_count;
		
		
		if($this->err==0)
		{	
			if($recordCount>0)
				{	
					$csv_output ="";					
								
					for($i=0;$i<$recordCount;$i++)
					{										
					
						# QUERY TO GET TAXT CODE FROM SHIPPING COUNTRY
						$this->obDb->query = "SELECT vCountryName, vSageTaxCode, fTax FROM ".COUNTRY." WHERE iCountryId_PK = '".$rowOrder[$i]->vAltCountry."'";
						$countryRow = $this->obDb->fetchQuery();
						
						
						# QUERY TO GET TAXT CODE FROM SHIPPING COUNTRY
						$this->obDb->query = "SELECT vCountryName, vSageTaxCode, fTax FROM ".COUNTRY." WHERE iCountryId_PK = '".$rowOrder[$i]->vCountry."'";
						$billingCountryRow = $this->obDb->fetchQuery();																		
						 	
						$rowOrder[$i]->bankAccountRef   =   strtoupper(substr($rowOrder[$i]->vLastName,0,3)).$rowOrder[$i]->iCustomerid_FK;	
						
								
						$csv_output .= 	'"SI",';		 
						$csv_output .= 	'"'.$rowOrder[$i]->bankAccountRef.'",';
						$csv_output .= 		$rowOrder[$i]->departmentNo.',';
						$csv_output .= 	'"'.$this->libFunc->dateFormat2($rowOrder[$i]->transactionDate).'",';
						$csv_output .= 	'"'.$rowOrder[$i]->invoice.'",';
						$csv_output .= 	'" Web Order #'.$rowOrder[$i]->invoice.'",';
						$csv_output .= 		$rowOrder[$i]->netAmount.',';
						$csv_output .= 	'"'.$countryRow[0]->vSageTaxCode.'",';
						$csv_output .= 		$countryRow[0]->fTax;
						$csv_output .=  " \n";
						
												
						$csv_output .= 	'"SA",';		 
						$csv_output .= 	'"'.$rowOrder[$i]->bankAccountRef.'",';
						$csv_output .= 		$rowOrder[$i]->departmentNo.',';
						$csv_output .= 	'"'.$this->libFunc->dateFormat2($rowOrder[$i]->transactionDate).'",';
						$csv_output .= 	'"'.$rowOrder[$i]->invoice.'",';
						$csv_output .= 	'" Payment - Web Order #'.$rowOrder[$i]->invoice.'",';
						
						$newPrice 	 = 		$rowOrder[$i]->netAmount - $rowOrder[$i]->fTaxPrice;
						$csv_output .= 		$newPrice.',';
						$csv_output .= 	'"'.$billingCountryRow[0]->vSageTaxCode.'",';
						$csv_output .= 		$billingCountryRow[0]->fTax;
						$csv_output .=  " \n";
						
																    
					}	
					//$data=$this->csv->create_csv_file($arr_data,0);
					//$this->csv->forceFileDownload($data,"customer.csv",$type="application/vnd.ms-excel");		
					header( "Content-Type: application/save-as" );
					header( 'Content-Disposition: attachment; filename=transaction.csv');
				    print $csv_output;
				    exit;  				
				}		
				else {
					header('Location: '.SITE_URL.'admin/adminindex.php?action=csv.home&errMsg=2;');
				}		
		}
	
	}# END OF EXPORT TRANSCTION FUNCTION 
function m_exportenquiry()
{
        $this->err=0;				
        #QUERY RETRIEVE INFORMATION FOR ORDER TABLE
		$this->obDb->query  = " SELECT * FROM ".ENQUIRIES;
		$rowEnquiry    = $this->obDb->fetchQuery();	
		$recordCount = $this->obDb->record_count; 
        
        if($this->err==0)
		{			
			if($recordCount>0)
				{	
					$csv_output ="";					
								
					for($i=0;$i<$recordCount;$i++)
					{										
					
                        $csv_output .= 	'"'.$rowEnquiry[$i]->iContactId_PK.'",';		 
						$csv_output .= 	'"'.$rowEnquiry[$i]->vName.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vAddress1.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vAddress2.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vCountry.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vWorkPhone.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vEmail.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vComments.'",';
						$csv_output .= 	'"'.$this->libFunc->dateFormat2($rowEnquiry[$i]->tmAddDate).'",';
						$csv_output .=  " \n";
					}	
					header( "Content-Type: application/save-as" );
					header( 'Content-Disposition: attachment; filename=enquiries.csv');
				    print $csv_output;
				    exit;  				
				}		
				else {
					header('Location: '.SITE_URL.'admin/adminindex.php?action=csv.home&errMsg=3;');
				}		
		}
        
}
function m_exportproducts()
{
        $this->err=0;				
        #QUERY RETRIEVE INFORMATION FOR ORDER TABLE
		$this->obDb->query  = " SELECT * FROM ".PRODUCTS;
		$rowEnquiry    = $this->obDb->fetchQuery();	
		$recordCount = $this->obDb->record_count; 
        
        if($this->err==0)
		{			
			if($recordCount>0)
				{	
					$csv_output ="";					
								
					for($i=0;$i<$recordCount;$i++)
					{										
					
                        $csv_output .= 	'"'.$rowEnquiry[$i]->iProdid_PK.'",';		 
						$csv_output .= 	'"'.$rowEnquiry[$i]->vTitle.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vSeoTitle.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->tShortDescription.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->tShortDescription.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vMetaTitle.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->tMetaDescription.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->tKeywords.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->tContent.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vImage1.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vImage2.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vImage3.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->tImages.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vDownloadablefile.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vSku.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vSupplierSku.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->fListPrice.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->fPrice.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->fRetailPrice.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->fItemWeight.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iInventory.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iInventoryMinimum.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iBackorder.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iUseinventory.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iOnorder.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->tmDuedate.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vShipCode.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->vShipNotes.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iFreeShip.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iTaxable.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iIncVat.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iVendorid_FK.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iAttrValueId_FK.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iDiscount.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iSale.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->fPointIncrease.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iCartButton.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iEnquiryButton.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iKit.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iViewCount.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iAddCount.'",';
						$csv_output .= 	'"'.$rowEnquiry[$i]->iAdminUser.'",';
						$csv_output .=  " \n";
					}	
					header( "Content-Type: application/save-as" );
					header( 'Content-Disposition: attachment; filename=products.csv');
				    print $csv_output;
				    exit;  				
				}		
				else {
					header('Location: '.SITE_URL.'admin/adminindex.php?action=csv.home&errMsg=4;');
				}		
		}
        
}
/*******************************end********************************************/

}#CLASS END
	
?>