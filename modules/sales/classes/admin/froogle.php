<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
#CRON RELATED TO THIS -/scheduler/froogle.php
#Please modify all the changes related to csv generation in scheduler file also
# Class to generate the feed file for froogle
class c_froogleClass
{
	# Class constructor
	function c_froogleClass()
	{
		$this->filePath=SITE_PATH."froogle/";
		$this->libFunc=new c_libFunctions();
		$this->err=0;
		$this->errMsg="";
		$this->fileUpload="tradingeye.txt";
	}

	# Function to generate tab delimited text file
	function m_generateFroogleFeedFile()
	{
		$Column_Separator="\t";
		$Line_Separator="\r\n";

		///////////////////////////////////////////////////////////////////////////////////
		// Set this to 1 to prevent products in hidden departments from being included.  //
		// NOTE: This only goes up one level for now!                                    //
		$exclude_hidden = 1;                                                             //
		//                                                                               //
		// Set this to the "condition" either "new", "used" or "refurbished"             //
		$condition = "new";                                                              //
		///////////////////////////////////////////////////////////////////////////////////
## DPI-RKT Edit for changing Header text
/*
		$strData = 'product_url'.$Column_Separator.'name'.$Column_Separator.'description'.$Column_Separator.'image_url'.$Column_Separator.'category'.$Column_Separator.'price'.$Column_Separator.'product_id'.$Column_Separator.'sku'.$Line_Separator;
		*/
		$strData = 'link'.$Column_Separator.'title'.$Column_Separator.'description'.$Column_Separator.'image_link'.$Column_Separator.'price'.$Column_Separator.'condition'.$Column_Separator.'id'.$Column_Separator.'upc'.$Line_Separator;
## DPI-RKT - END
//select DISTINCT f1.*  from t21_tbfusion f1,t21_tbfusion f2 where f1.iOwner_FK=f2.iOwner_FK and f1.iState=1 and f1.iSubId_FK=8
		$this->obDb->query ="SELECT vTitle, iProdid_PK, vSeoTitle, tShortDescription, fPrice, iTaxable, vImage1, vSku FROM ".PRODUCTS;
		$rs_product=$this->obDb->fetchQuery();
		$inRecordCount=$this->obDb->record_count;

		for($i=0;$i<$inRecordCount;$i++)
		{
			if ($exclude_hidden == 0) {
				$status_flag = $this->check_for_active_prod($rs_product[$i]->iProdid_PK);
			} else {
				$status_flag = $this->check_for_active_dept($rs_product[$i]->iProdid_PK);
			}

			if ($status_flag == true) {
				$productUrl=SITE_URL."ecom/index.php?action=ecom.pdetails&mode=".$rs_product[$i]->vSeoTitle;
				$product_url=$this->libFunc->m_safeUrl($productUrl);
				$title=trim($this->libFunc->m_displayContent($rs_product[$i]->vTitle));
				$productid=trim($this->libFunc->m_displayContent($rs_product[$i]->iProdid_PK));
				$description=$this->libFunc->m_displayContent(trim($rs_product[$i]->tShortDescription));
				$description=str_replace("\n","",$description);
				$description=str_replace("\t","",$description);
				$description=str_replace("\r","",$description);
				$description=str_replace("&nbsp;"," ",$description);
				$sku=trim($this->libFunc->m_displayContent($rs_product[$i]->vSku));

				if(trim($rs_product[$i]->vImage1)!="" && file_exists(SITE_PATH.'images/product/'.trim($rs_product[$i]->vImage1)))
				{
					$image=SITE_URL.'images/product/'.trim($rs_product[$i]->vImage1);
				}
				else
				{
					$image="";
				}
				if (($rs_product[$i]->iTaxable==1) && (NETGROSS != 1))
				{
					$taxPrice=round($rs_product[$i]->fPrice*DEFAULTVATTAX/100,2);
					$rs_product[$i]->fPrice+=$taxPrice;
				}

				$price=number_format($rs_product[$i]->fPrice,2);
				$strData .=$product_url.$Column_Separator;
				$strData .=$title.$Column_Separator;
				$strData .=$description.$Column_Separator;
				$strData .=$image.$Column_Separator;
				$strData .=$price.$Column_Separator;
				$strData .=$condition.$Column_Separator;
				$strData .=$productid.$Column_Separator;
				$strData .=$sku.$Line_Separator;
			}
		}

		$this->strData=$strData;
		$this->forceFileDownload();
	}

	function m_ftpUpload()
	{
		$this->obDb->query ="SELECT vServer,vUsername,vPassword FROM ".FROOGLE_SETTINGS;
		$rs_settings=$this->obDb->fetchQuery();

		$ftp_server=$rs_settings[0]->vServer;
		$ftp_user_name=$rs_settings[0]->vUsername;
		$ftp_user_pass=$rs_settings[0]->vPassword;
		chmod(SITE_PATH."froogle/".$this->fileUpload,0777);
		$file=SITE_PATH."froogle/".$this->fileUpload;

		$remote_file=$this->fileUpload;
		// set up basic connection
		$conn_id = ftp_connect($ftp_server);

		// login with username and password
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

		if (ftp_put($conn_id, $remote_file,$file,FTP_ASCII))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=froogleHome&msg=1");
		}
		else
		{
			$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=froogleHome&msg=2");
		}

		// close this connection
		ftp_close($conn_id);

	}

	function forceFileDownload()
	{
		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))
		  ini_set('zlib.output_compression', 'Off');

		$filename=$this->libFunc->seoText(SITE_NAME);

		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: text/plain; charset=\"iso-8859-1\"\r\n");
		// added quotes to allow spaces in filenames
		header("Content-Disposition: attachment; filename=\"".$filename.".txt\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($this->strData));
		echo $this->strData;
		exit();
	}


	function m_getDepartmentName($id)
	{
		 $this->obDb->query = "SELECT vTitle FROM ".DEPARTMENTS." WHERE iDeptId_PK='$id'";
		$rs_title=$this->obDb->fetchQuery();
		if($id==0)
		{
			return "Home Page";
		}
		return $this->libFunc->m_displayContent($rs_title[0]->vTitle);
	}

	#FUNCTION TO DISPLAY FROOGLE HOME
	function m_froogleHome()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_FROOGLE_FILE", $this->froogleTemplate);
		$this->ObTpl->set_block("TPL_FROOGLE_FILE","TPL_MSG_BLK","msg_blk");
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("msg_blk","");
		$this->request['msg']=$libFunc->ifSet($this->request,'msg',"");
		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",TPL_VAR_FROOGLEUPLOADSUCCESS);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		elseif($this->request['msg']==2)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",TPL_VAR_FROOGLENOUPLOAD);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		return($this->ObTpl->parse("return","TPL_FROOGLE_FILE"));
	}

	#FUNCTION TO DISPLAY FROOGLE FORM
	function m_froogleForm()
	{
		$libFunc=new c_libFunctions();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_FROOGLE_FILE", $this->froogleTemplate);
		$this->ObTpl->set_block("TPL_FROOGLE_FILE","TPL_MSG_BLK","msg_blk");
		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->request['msg']=$libFunc->ifSet($this->request,'msg',"");

		if($this->request['msg']==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",TPL_VAR_FROOGLESUCCESS);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		elseif($this->err==1)
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",$this->errMsg);
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		if(count($_POST)>0)
		{
			$row_setting[0]->vServer=$libFunc->ifSet($this->request,'server',"");
			$row_setting[0]->vUsername=$libFunc->ifSet($this->request,'username',"");
			$row_setting[0]->vPassword=$libFunc->ifSet($this->request,'password',"");
		}
		else
		{
			$this->obDb->query = "SELECT vServer,vUsername,vPassword  FROM ".FROOGLE_SETTINGS;
			$row_setting=$this->obDb->fetchQuery();
		}
		$this->ObTpl->set_var("TPL_VAR_SERVER",$libFunc->m_displayContent($row_setting[0]->vServer));
		$this->ObTpl->set_var("TPL_VAR_USERNAME",$libFunc->m_displayContent($row_setting[0]->vUsername));
		$this->ObTpl->set_var("TPL_VAR_PASSWORD",$libFunc->m_displayContent($row_setting[0]->vPassword));

		return($this->ObTpl->parse("return","TPL_FROOGLE_FILE"));
	}

	#FUNCTION TO UPDATE FROOGLE SETTINGS
	function m_updateFroogle()
	{
		$libFunc=new c_libFunctions();
		#INTIALIZING VALUES
		$this->request['server']=$libFunc->ifSet($this->request,'server');
		$this->request['username']=$libFunc->ifSet($this->request,'username');
		$this->request['password']=$libFunc->ifSet($this->request,'password');

		$this->obDb->query ="UPDATE ".FROOGLE_SETTINGS." SET ";
		$this->obDb->query.="vServer		='".$libFunc->m_addToDB($this->request['server'])."',";
		$this->obDb->query.="vUsername	='".$libFunc->m_addToDB($this->request['username'])."',";
		$this->obDb->query.="vPassword	='".$libFunc->m_addToDB($this->request['password'])."'";
		$this->obDb->updateQuery();

		$this->libFunc->m_mosRedirect(SITE_URL."sales/adminindex.php?action=froogle.form&msg=1");
	}

	#FUNCTION TO VALIDATE FROOGLE SETTINGS
	function m_validateFroogleSettings()
	{
		$this->errMsg="";

		if(empty($this->request['server']))
		{
			$this->err=1;
			$this->errMsg.=MSG_FROOGLESERVER_EMPTY."<br>";
		}

		if(empty($this->request['username']))
		{
			$this->err=1;
			$this->errMsg.=MSG_FROOGLEUSER_EMPTY."<br>";
		}

		if(empty($this->request['password']))
		{
			$this->err=1;
			$this->errMsg.=MSG_FROOGLEPASS_EMPTY."<br>";
		}
		return $this->err;
	}
	function check_for_active_prod($prod_id){
		 $this->obDb->query="SELECT * FROM ".FUSIONS." WHERE iSubId_FK = " . $prod_id . " AND vtype = 'product' AND iState = 1";

		$rsproduct=$this->obDb->fetchQuery();
		$inRecCount=$this->obDb->record_count;
		if ($inRecCount > 0) {
			return true;
		} else {
			return false;
		}
	}
	function check_for_active_dept($prod_id){
		 $this->obDb->query="SELECT * FROM ".FUSIONS." WHERE iSubId_FK = " . $prod_id . " AND vtype = 'product' AND iState = 1";

		$rsproduct=$this->obDb->fetchQuery();
		$inRecCount=$this->obDb->record_count;
		if ($inRecCount > 0) {
			for($i=0;$i<$inRecCount;$i++)
			{
				$this->obDb->query="SELECT * FROM ".FUSIONS." WHERE iSubId_FK = " . $rsproduct[$i]->iOwner_FK . " AND vtype = '" . $rsproduct[$i]->vOwnerType . "' AND iState = 1";
				$rsproduct=$this->obDb->fetchQuery();
				$inRecCount2=$this->obDb->record_count;
				if ($inRecCount2 > 0) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
}# End of class
?>
