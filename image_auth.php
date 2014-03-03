<?php
/*
=======================================================================================
Copyright: TradingEye
Version: 7.1.1
=======================================================================================
*/
class captcha
{
	var $inNumChar = 6;	
	var $numChars = 3;
	var $w;
	var $h = 80;
	var $colBG = "255 220 131";
	var $colTxt = "153 0 0";
	var $colBorder = "0 128 192";
	var $charx = 30;
	var $numCirculos = 10;
	var $texto;
	
	function __construct()
	{			
	}		
	function m_GenImage($num){
		if (($num != '')&&($num > $this->numChars)) $this->numChars = $num;		
		$this->texto = $this->m_GenString();
 		$_SESSION['image_auth_string'] = $this->texto;
	}
	function m_GenString()
	{
		rand(0,time());
		//$possible="ahlftdsknprvy24579";
		$possible="abcdefghijklmnopqrstuvwxyz";
//		$possible="AHLFTDSKNPRVY24579";
		$str="";
		while(strlen($str)<$this->numChars)
		{
			$str.=substr($possible,(rand()%(strlen($possible))),1);
		}
		$txt = $str;
		return $txt;
	}
	function m_GetImage()
	{					
		global $_CONF;		
		$this->im=imagecreatefromjpeg(LIB_IMAGES_PATH."captcha_image.jpg");
		$ident = 15;
		$black = imagecolorallocate($this->im, 0, 0, 0);

		for ($i=0;$i<$this->numChars;$i++){
			$char = substr($this->texto, $i, 1);			
			$font = LIB_IMAGES_PATH."fonts/verdana.ttf";			
			$y = round(($this->h-10)/2);
			$t = rand(2,20);
			$size=rand(15,20);
			$col = $this->m_GetColor($this->colTxt);
			if (($i%2) == 0)
			{
				imagettftext($this->im, $size, 0, $ident, $y+$t, $black, $font, $char);
			}
			else
			{
				imagettftext($this->im, $size, 10, $ident, $y+$t, $black, $font, $char);
			}
			$ident = $ident+(rand (25, 30));
		}			
	}
	function m_GetColor($var){
		$rgb = explode(" ",$var);
		$col = imagecolorallocate ($this->im, $rgb[0], $rgb[1], $rgb[2]);
		return $col;
	}
	function m_ShowImage()
	{
		header("Content-type: image/jpeg");
		ImageJpeg($this->im);
		//Imagegif($this->im);		
	}
	function m_CheckCode($stEnteredCode)
	{
		//if (isset($this->postCode)) $this->loadCodes();		
		if ($stEnteredCode == $_SESSION['image_auth_string'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
}
?>