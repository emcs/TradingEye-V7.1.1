<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;

class c_cardsaveSuccess{
	#CUNSTRUCTOR
	function c_cardsaveSuccess(){
		$this->templatePath = THEMEPATH . "ecom/templates/main/";
		$this->pageTplPath = THEMEPATH . "default/templates/main/";
	}
	
	function m_cardsave_success(){
		//$comFunc=new c_commonFunctions();
		//$comFunc->obDb=$this->obDb;
		////print($comFunc);
		//print_r($comFunc->obDb);
		//die;
		//$this->obDb->query ="update ".ORDERS." set iOrderStatus=1 where iOrderid_PK=".$_REQUEST['OrderID'];
		return true;
	}
}
?>