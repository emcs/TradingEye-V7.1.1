<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.0.5
=======================================================================================
*/
 
 class c_googleInterface
{
	#CONSTRUCTOR
	function c_googleInterface()
	{
		$this->subTotal		=0;
		$this->pageTplPath	=MODULES_PATH."sales/templates/admin/";	
		$this->err				=0;
		$this->libFunc			=new c_libFunctions();
	}

	/* Method m_dspHome: To display the homepage for Google API
	 * @author  	Dave Bui
	 * @version 	6.00
	 * @copyright	Dpivision.com Ltd
	 */
	function m_dspHome()
	{
		$this->ObTpl=new template();
		$this->ObTpl->set_file("TPL_HOME_FILE", $this->googleTemplate);

		#INTIALIZING TEMPLATE BLOCKS
		$this->ObTpl->set_var("GRAPHICSMAINPATH", GRAPHICS_PATH);
		$this->ObTpl->set_var("TPL_VAR_SITEURL",SITE_URL);
		#INTIALIZING BLOCKS
		return($this->ObTpl->parse("return","TPL_HOME_FILE"));
	}
	
	
	
}#End class
?>
