<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
error_reporting(E_ALL);
include_once("../configuration.php");
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
		$this->logFile="log.txt";
		$this->fileUpload=$this->libFunc->seoText(SITE_NAME).".txt";
		$obDatabase = new database();
		$obDatabase->db_host = DATABASE_HOST;
		$obDatabase->db_user = DATABASE_USERNAME;
		$obDatabase->db_password = DATABASE_PASSWORD;
		$obDatabase->db_port = DATABASE_PORT;
		$obDatabase->db_name = DATABASE_NAME;
		$this->obDb=$obDatabase;
		$this->m_generateFroogleFeedFile();
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

		$strData = 'link'.$Column_Separator.'title'.$Column_Separator.'description'.$Column_Separator.'image_link'.$Column_Separator.'price'.$Column_Separator.'condition'.$Column_Separator.'id'.$Column_Separator.'upc'.$Line_Separator;

## DPI-RKT - END
		$obFile=fopen($this->filePath.$this->fileUpload,"w");
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
		fwrite($obFile, $strData);
		fclose( $obFile);
		$this->m_ftpUpload();
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

		// print the current directory
		//echo ftp_pwd($conn_id); // /
		$obFile=fopen($this->filePath.$this->logFile,"a");

		// upload a file
		$timestamp=time();
		if (ftp_put($conn_id, $remote_file,$file,FTP_ASCII))
		{
			$strData="Uploaded successfully on ".strftime("%B %d, %Y  %r",$timestamp)."\n\n";
		}
		else
		{
			$strData="Not uploaded successfully  ".strftime("%B %d, %Y  %r",$timestamp)."\n\n";
		}

		fwrite($obFile, $strData);
		fclose( $obFile);

		// close this connection
		ftp_close($conn_id);
	}#ef

	function m_getDepartmentName($id)
	{
		 $this->obDb->query = "SELECT vTitle FROM ".DEPARTMENTS;
		$rs_title=$this->obDb->fetchQuery();
		return $this->libFunc->m_displayContent($rs_title[0]->vTitle);
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

$obFroogle=new c_froogleClass();
?>