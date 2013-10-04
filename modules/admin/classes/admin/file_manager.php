<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/

class c_fileManager
{
	#CONSTRUCTOR
	function c_fileManager()
	{
		$this->libFunc	=new c_libFunctions();
		$this->errMsg="";
		$this->err=0;
	}

	function defaultValues()
	{
		if($this->request['dir']=="admin" || $this->request['dir']=="site" )
		{
			$this->imageUrl=SITE_URL."graphics/".$this->request['dir']."/blue/";
			$this->imagePath=SITE_PATH."graphics/";
		}
		elseif($this->request['dir']=="admin/blue")
		{
			$this->imageUrl=SITE_URL."graphics/".$this->request['dir']."/";
			$this->imagePath=SITE_PATH."graphics/";
		}
		else
		{
			$this->imageUrl=SITE_URL."images/".$this->request['dir']."/";
			$this->imagePath=SITE_PATH."images/";
		}
	}
	#FUNCTION TO DISPLAY HOMEPAGE
	function m_dspHome()
	{
		$this->ObTpl=new template();
		$_SESSION['KCFINDER']['disabled'] = false;
		$_SESSION['KCFINDER']['uploadURL'] = "../";
		$_SESSION['KCFINDER']['uploadDir'] = SITE_PATH;
		$this->ObTpl->set_file("TPL_BROWSE_FILE", $this->browseTemplate);
		$this->ObTpl->set_block("TPL_BROWSE_FILE","TPL_FILE_BLK", "file_blk");
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_OPENACTION",SITE_URL."admin/adminindex.php?action=file.home");
		$this->ObTpl->set_var("TPL_VAR_CKEDITOR","");
		$this->ObTpl->set_var("TPL_VAR_ERROR","");
		if(isset($_SESSION['error']))
		{
			$this->ObTpl->set_var("TPL_VAR_ERROR","<h3 style='color:red;'>".$_SESSION['error']."</h3>");
			unset($_SESSION['error']);
		}
		if(isset($this->request['filetoedit']) && !empty($this->request['filetoedit']))
		{
		$this->request['filetoedit'] = SITE_PATH . substr(urldecode($_REQUEST['filetoedit']),2);
		if($this->checkFile($this->request['filetoedit']))
		{
			//FILE BEING EDITED
			$this->ObTpl->set_var("TPL_VAR_FILEPATH",$this->filenameis($this->request['filetoedit']));
			//FORM ACTION URL
			$this->ObTpl->set_var("TPL_VAR_ACTION",SITE_URL."admin/adminindex.php?action=file.save");
			//FILE CONTENTS
			$this->ObTpl->set_var("TPL_VAR_CONTENT",file_get_contents($this->request['filetoedit']));
			$this->ObTpl->parse("file_blk","TPL_FILE_BLK");
		}
		}
		else
		{
			$this->ObTpl->set_var("file_blk","");
		}
		return($this->ObTpl->parse("return","TPL_BROWSE_FILE"));
	}#END FUNCTION

	function m_saveFile($filetosave)
	{
		if(isset($filetosave) && !empty($filetosave) && isset($this->request['filecontent']))
		{
			file_put_contents($filetosave,$this->request['filecontent']);
		}
	}	
	
	//Makes sure file isnt a banned file type
	function checkFile($filepath)
	{
		$length = strlen($filepath);
		$filetype = substr($filepath,$length-3);
		if($filetype == "css" || $filetype == "htm" || $filetype=="xml")
		{
			if($filetype == "htm")
			{
				$this->ObTpl->set_var("TPL_VAR_CKEDITOR","<script type='text/javascript'>CKEDITOR.replace('filecontent');</script>");
			}
			elseif($filetype == "css")
			{
				$html = '<script type="text/javascript" >
						$(document).ready(function() {
							$("#filecontent").markItUp(mySettings);
						});
						</script>
					   ';
				$this->ObTpl->set_var("TPL_VAR_CKEDITOR",$html);
			}
			else
			{
			
			}
			$_SESSION['filetosave'] = $filepath;
			return true;
		}
		else
		{
			$_SESSION['error']="Invalided file type";
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.home");
			die("Invalid file type.");
		}
	}
	
	//returns the filename portion of a filepath
	function filenameis($filepath)
	{
		$last = strrpos($filepath,"/");
		$filename = substr($filepath,$last+1);
		if(isset($filename) && !empty($filename))
		{
			return $filename;
		}
		else
		{
			return $filepath;
		}
	}
	
	#FUNCTION TO LIST FILES
	function m_fileList()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_BROWSE_FILE", $this->browseTemplate);
		$this->ObTpl->set_block("TPL_BROWSE_FILE","TPL_MSG_BLK", "msg_blk");
		$this->ObTpl->set_block("TPL_BROWSE_FILE","TPL_LIST_BLK", "list_blk");
		#INTIALIZE
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		$this->ObTpl->set_var("list_blk","");
		$this->ObTpl->set_var("msg_blk","");
		$this->ObTpl->set_var("TPL_VAR_COUNT","");

		if(!isset($this->request['dir']) || empty($this->request['dir']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.home&msg=1");
		}
		$this->defaultValues();
		if(isset($this->request['msg']) && !empty($this->request['msg']))
		{
			if($this->request['msg']==1)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FILE_DELETED);
			}
			elseif($this->request['msg']==2)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NOFILE_DELETED);
			}
			elseif($this->request['msg']==3)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_FILE_UPLOADED);
			}
			elseif($this->request['msg']==4)
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NOFILE_UPLOADED);
			}	
			$this->ObTpl->parse("msg_blk","TPL_MSG_BLK");
		}
		if($this->request['dir']=="admin")
		{
			$this->request['dir']="admin/blue";
		}
		$this->ObTpl->set_var("TPL_VAR_DIR",$this->request['dir']);
		$this->imagePath=$this->imagePath.$this->request['dir']."/";
		if (is_dir($this->imagePath)) 
		{
			$count=0;
			if ($dh = opendir($this->imagePath))
			{			
				while (($file = readdir($dh)) !== false) 
				{
					if($file!="." && $file!="..")
					{
						$count++;
						$this->ObTpl->set_var("TPL_VAR_IMAGEURL",$this->imageUrl);
						$this->ObTpl->set_var("TPL_VAR_FILENAME",$file);
						$this->ObTpl->set_var("TPL_VAR_FILETYPE",filesize($this->imagePath . $file));
						$this->ObTpl->parse("list_blk","TPL_LIST_BLK",true);
					}
				}
				$this->ObTpl->set_var("TPL_VAR_COUNT",$count." files found");
				closedir($dh);
			}
			else
			{
				$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NODIRECTORY_PERMIT);
			}
		}
		else
		{
			$this->ObTpl->set_var("TPL_VAR_MSG",MSG_NODIRECTORY_EXIST);
		}
		return($this->ObTpl->parse("return","TPL_BROWSE_FILE"));
	}#END FUNCTION


	#FUNCTION USED FOR UPLOADING IMAGES/FILES DURING EDIT PROCESS
	function m_uploadForm()
	{
		if(!isset($this->request['dir']) && empty($this->request['dir']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.home&msg=1");
		}
		$this->defaultValues();
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_EDITOR_FILE",$this->browseTemplate);
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		$this->ObTpl->set_var("TPL_VAR_DIR",$this->request['dir']);
		$this->ObTpl->set_var("TPL_VAR_IMAGE","");
		$this->ObTpl->set_var("TPL_VAR_MSG","");
		if($this->err==1){
			$this->ObTpl->set_var("TPL_VAR_MSG","<P>".$this->errMsg."</P>");
		}
		
		return($this->ObTpl->parse("return","TPL_EDITOR_FILE"));
	}#END FUNCTION

	#FUNCTION TO VALIDATE IMAGE UPLOADED  FROM UPLOAD FORM
	function m_verifyImageUpload(){
		if(!$this->libFunc->m_validateUpload($this->request['thisfile'])){
			$this->errMsg.=MSG_VALID_IMAGE."<br />";
			$this->err=1;
		}
		return $this->err;
	}#EF

	function m_uploadFile()
	{
		$fileUpload = new FileUpload();
		$libFunc=new c_libFunctions();
		if(!isset($this->request['dir']) || empty($this->request['dir']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.home&msg=1");
			exit;
		}
		$this->defaultValues();
		$directory=$this->request['dir'];	
		
		if($this->libFunc->checkImageUpload("thisfile"))
		{
			//chmod($this->imagePath.$directory,0644);
			$fileUpload->source = $_FILES["thisfile"]["tmp_name"];
			$fileUpload->target = $this->imagePath.$directory."/".$_FILES["thisfile"]["name"];
			$newName1 = $fileUpload->upload();
		}
		else
		{
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.list&msg=4&dir=".$directory);
		}
		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.list&msg=3&dir=".$directory);
	}	
	function m_deleteFile()
	{
		if(!isset($this->request['dir']) && empty($this->request['dir']))
		{
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.home&msg=1");
		}
		$this->defaultValues();
		$fileUpload = new FileUpload();
		$directory=$this->request['dir'];		
		$fname=$this->request['fname'];		
		if(is_file($this->imagePath.$directory."/".$fname))
		{
			$fileUpload->deleteFile($this->imagePath.$directory."/".$fname);
		}
		else
		{
			$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.list&msg=2&dir=".$directory);
		}

		$this->libFunc->m_mosRedirect(SITE_URL."admin/adminindex.php?action=file.list&msg=1&dir=".$directory);
	}#EF	
}	#EC
?>