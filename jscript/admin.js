/*
====================================================================
Description: 	Javascript file for the /admin/
Copyright		Tradingeye 2007
====================================================================
*/

/* GLOBAL VARIABLES */
var numberoftimes = 0;		// Used in onlyonce()
var thetimer1 = 0;			// Used in onlyonce()
var thetimer2 = 0;			// Used in onlyonce()

/* ******************* CORE FUNCTIONS *************************** */

/*
====================================================================
Name:			valMenuBuilder
Description: 	To validate a form on the menu builder page.
====================================================================
*/
function valMenuBuilder(type)
{
	
	blnReturnValue = true;
	objForm = window.document.ThisForm;
	
	if(isNull(objForm.header.value)){
		objForm.header.value = '';
		alert("Please supply a 'Menu Title'");
		blnReturnValue = false;
	}else{
		blnReturnValue = true;
	}
	
	switch(type)
	{
		// Done checking, return back after popup error.
		case "noimage":
			if(!blnReturnValue)
				return false;	// Bad header value, pass back false;
			else
				return true;	// Good to go, move on.
		case "withimage":
			// PASSED the form box validation. Move to the image box
			if(blnReturnValue)	// Only if the header value is good	
			{
				if(onlyonce())	// Do the validations for the button pressing and file types
					return true;	
				else
					return false;
			}
			else
				return false;	// Bad header value, pass back false
	}
	
}
/*
====================================================================
Name:			valMenuBuilderNewItem()
Description: 	To validate a new item form.
====================================================================
*/
function valMenuBuilderNewItem(type)
{
	// Setup
	objForm = window.document.ThisForm;
	msg = "Please correct the following errors\n\n";
	err = "";

	// Do the validations
	if(isNull(document.ThisForm.item_title.value)){
		err = err + "You must enter a title\n";
		document.ThisForm.item_title.value = '';
	}
	if(isNull(document.ThisForm.link.value)){
		err = err + "You must enter a link\n";
		document.ThisForm.link.value = '';
	}
	// If we're in the clear, run onlyonce to make edits to buttons.
	
	if(err.length != 0)
		alert(msg + err);
		

	switch(type)
	{
		// Done checking, return back after popup error.
		case "noimage":
			if(err.length != 0)
				return false;	// Bad header value, pass back false;
			else
				return true;	// Good to go, move on.
		case "withimage":
			// PASSED the form box validation. Move to the image box
			if(err.length == 0)	// Only if the header value is good	
			{
				if(onlyonce())	// Do the validations for the button pressing and file types
					return true;	
				else
					return false;
			}
			else
				return false;	// Bad header value, pass back false
	}
}

/*
====================================================================
Name:			valImporter
Description: 	Validate the product importer form.
====================================================================
*/
function valImporter()
{
	objForm = window.document.frmImporter;
	msg = "Please correct the following errors\n\n";
	err = "";
	
	if(objForm.layout.selectedIndex < 0){
		err = err + "Please select a layout\n";
		alert(msg + err);
		return false;
	}
	if(objForm.template.selectedIndex < 0){
		err = err + "Please select a template\n";
		alert(msg + err);
		return false;
	}
	if(objForm.import_file.value == ""){
		err = err + "Please select a file to import\n";
		alert(msg + err);
		return false;
	}
	if(fileUploadExt(objForm.import_file, '.csv')){
		return true;
	}
	return false;
}
function trim(str){
   return str.replace(/^\s*|\s*$/g,"");
}

/*
====================================================================
Name:			valProductBuilder
Description: 	Used to validate the product inset pages
====================================================================
*/
function valProductBuilder()
{
	clearTitles();
	
	objForm = window.document.ThisForm;
	msg = "Please correct the following errors\n\n";
	err = "";
	var anum=/(^\d+$)|(^\d+\.\d+$)/ ;

	if(isNull(objForm.title.value))
	{
		alert("You must enter a title")
		modState("product_title","red","Product title");
		objForm.title.focus();
		return false; 
	}
	
	if(isNull(objForm.seo_title.value)){
		alert("You must enter a SEO title")
		modState("product_seo_title","red","Product SEO title");
		objForm.seo_title.focus();
		return false; 
	}	
		
	if(isNull(objForm.sku.value))
	{
		alert("You must enter a code value");
		modState("product_id","red","Product code");
		objForm.sku.focus();
		return false; 
	}	
	if(isNull(objForm.price.value) || (!anum.test(objForm.price.value)) || (parseFloat(objForm.price.value) <= 0))
	{
		alert("You must enter a value for price");
		modState("product_price","red","Price");
		objForm.price.focus();
		return false; 
	}
	if(objForm.template.selectedIndex<0)
	{
		alert("You must select a layout");
		modState("product_layout","red","Select a layout");
		objForm.layout.focus();
		return false; 
	}
	if(objForm.template.selectedIndex<0)
	{
		alert("You must select a template");
		modState("product_template","red","Select a template");
		objForm.template.focus();
		return false; 
	}
	if(isdefined(objForm.due_date))
	{
		if(!isNull(objForm.due_date.value))
		{
			if(!checkDate(trim(objForm.due_date.value)))
			{
				alert("Please Enter Valid Date");
				document.ThisForm.due_date.focus();
				return false;
			}
		}
	}
	return true;	
}
		
/*
====================================================================
Name:			onlyonce
Description: 	Used to control the number of times a user clicks a submit button.
====================================================================
*/
function onlyonce() 
{
	if(isdefined(window.document.ThisForm.image))
	{
		if(!validateType())
			return false;
	}
	
	numberoftimes += 1;
	if (numberoftimes > 1) { 
		var themessage = "Please be patient. You will receive a response momentarily.";
		if (numberoftimes >= 3) {
			themessage = "Please be patient. Processing may take up to one minute.";
		}
		alert(themessage);
		return false;
	} else {
		setTimeout('incrementit()',1000);
			return true;
		}
}

/* ******************* UTILITY FUNCTIONS *************************** */
/*
====================================================================
Name:			validateType
Description: 	Check for valid file types on upload of images.
Called From:	onlyonce();
====================================================================
*/
function validateType()
{
	msg = "We only accept .GIF, .JPG files";
	strFileToUp1 = new String(window.document.ThisForm.image.value)
	
	// if image box is empty, let it pass as it's an optional field.
	if(strFileToUp1.length == 0)
		return true;
		
	intFileLength = strFileToUp1.length
	intStartSearch = intFileLength  - 4
	var sExt = strFileToUp1.substring(intStartSearch,intFileLength);
	
	if(sExt.toLowerCase() != '.gif' && sExt.toLowerCase() != '.jpg') 
	{
		alert(msg)
		return false;
	}
	else
		return true;
}		

/*
====================================================================
Name:			incrementit()
Description: 	internal function to increment the counter for submit buttons
Called From:	onlyonce();
====================================================================
*/
function incrementit() 
{
	thetimer1 += 1;
	thespacer = "";
	if (thetimer1 > 59) {
		thetimer2 += 1;
		thetimer1 = 0;
	}
	if (thetimer1 < 10) {thespacer = "0";}
		
	myVar = thetimer2 + ":" + thespacer + thetimer1;
	document.ThisForm.submitbutton.value='Uploading...'+myVar;		
	setTimeout('incrementit()',1000);
}
/*
====================================================================
Name:			modState()
Description: 	internal function change the color of the text
Called From:	valProductBuilder()
====================================================================
*/
function modState(element,color,title)
{
	return true;
	str = element + ".innerHTML = \"<font color='" + color + "'>" + title + "</font>\";"
	eval(str);
}
/*
====================================================================
Name:			clearTitles()
Description: 	internal function to reset titles to "black" 
Called From:	valProductBuilder()
====================================================================
*/
function clearTitles()
{

	modState("product_title","black","Product title");
	modState("product_id","black","Product code");
	modState("product_price","black","Price");
	modState("product_template","black","Select a template");
}

/*
====================================================================
Name:			PopWindow()
Description: 	Used to open a new window.
Called From:	Many Pages, Product Builder, Menu Builder
====================================================================
*/
function PopWindow()
{
	window.open("","windowname","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=425,height=425,top=50,left=50");
}

/*
====================================================================
Name:			PopWindow()
Description: 	Used to open a new window.
Called From:	Help pages
====================================================================
*/
function helpWindow()
{
	window.open("","windowname","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=425,height=425,top=50,left=50");
}

/*
====================================================================
Added:			28th April, 2006 [DPI-GSM: BugFixing Section]
Name:			isNull()
Description: 	Used to trim the string space and check the value should not be blank.
Called From:	Can be called from all pages. Currenty in Department, Product.
====================================================================
*/

function isNull(aStr)
{
	var index;		
	for (index=0; index < aStr.length; index++)
		if (aStr.charAt(index) != ' ')
			return false;
	return true;
}

/*
====================================================================
Added:			6th May, 2006 [DPI-GSM: BugFixing Section]
Name:			fillValue()
Description: 	It fills the value of the field by removing special chars. Used for seo_title field.
Called From:	Can be called from all pages. Currenty in Department, Product.
Parameters:		str: The string that will be converted.
				fillName: fieldName with document structure to whom the value to be filled. 
				fillName = document.formName.fieldName;
====================================================================
*/
function fillValue(str, fillName){
	var pattern =  /[^A-Za-z0-9_-]/g
	str = str.toLowerCase();
	str = str.replace(pattern, "_");
	fillName.value = str;
	return str;
}

/*
====================================================================
Added:			17th May, 2006 [DPI-GSM: BugFixing Section]
Name:			isdefined()
Description: 	Return true/false 
Called From:	Whole site
Parameters:		str: elementName
====================================================================
*/
function isdefined(variable){
    //return (typeof(window[variable]) == "undefined")?  false: true;
	return (typeof(variable) == "undefined")?  false: true;
}  

/*
====================================================================
Added:			23rd May, 2006 [DPI-GSM: BugFixing Section]
Name:			checkEmail()
Description: 	Return true/false 
Called From:	Whole site
Parameters:		sting value
====================================================================
*/
//For checking invalid E-Mail address
var reEmail=/^[0-9a-zA-Z_\.-]+\@[0-9a-zA-Z_\.-]+\.[0-9a-zA-Z_\.-]+$/
function checkEmail(obj){
 	if(!reEmail.test(obj)){
		return false;
	}
	return true;
}


/*
====================================================================
Added:			2nd June, 2006 [DPI-GSM: BugFixing Section]
Name:			fileUploadExt()
Description: 	Return true/false 
Called From:	Admin Whole site
Parameters:		fieldname: eg.(document.formname.fieldname),  
				extAllowed: Extensions Allowed 
====================================================================
*/
function fileUploadExt(fieldname, extAllowed){

	msg = "We only accept (" + extAllowed + ") files";
//	strFileToUp1 = new String(window.document.ThisForm.image.value)
	strValue = fieldname.value;
	
	// if image box is empty, let it pass as it's an optional field.
	if(strValue.length == 0) return true;

	// if upload file is of type application type then upload it anyway.
	if(extAllowed == "any"){
		return true;
	}
		
	var sExt1 = parseInt(strValue.lastIndexOf("."));
	var stExt = "";
	if(sExt1 > 0){
		var stExt = strValue.substring(sExt1);
		stExt = stExt.toLowerCase();
	}

	if(stExt != ''){
		if(extAllowed.lastIndexOf(stExt) == -1){
			alert(msg);
			return false;
		}
	}
	return true;
}		
//For Selecting/ deselecting check boxed
	function selectDeselect(field, isCheck) {
		var boxes = document.getElementsByName(field);
		var boxes_checked = anyChecked();
	
		if(isCheck){
			if(document.getElementsByName(isCheck)[0].checked) setChecks(true);
			else setChecks(false);			
		}else{
			if(!boxes_checked) setChecks(true);
			else setChecks(false);
		}
	
		function setChecks( setting ) {
			for( var i=0; i < boxes.length; i++ ) {
				boxes[ i ].checked = setting;
			}
		}
		function anyChecked() {
			for( var i=0; i < boxes.length; i++ ) {
				if( boxes[i].checked == true) {
					return (true);
				} 
			}
			return (false);
		}
	}

	//To check wheather user have selected box or not
	function anyChecked(field) {
		var boxes = document.getElementsByName(field);
		for( var i=0; i < boxes.length; i++ ) {
			if( boxes[i].checked == true) {
				return (true);
			} 
		}
		return (false);
	}
	
	function isAnySelect()
	{
		varAllId = "";
		if(isdefined(document.frmMember.del.length))
		{
			for(i=0;i<document.frmMember.del.length;i++)
			{
				if(document.frmMember.del[i].checked == true)
				{
					if(varAllId == "")
						varAllId = document.frmMember.del[i].value
					else
						varAllId += "," + document.frmMember.del[i].value
				}
			}
			if(varAllId == "")
				return false
			else
			{
				document.frmMember.chkNothing.value = varAllId
				return true
			}
		}
		else
		{
			return document.frmMember.del.checked;
		}
	}

	function submitform()
	{
		if(isAnySelect())
		{
			if(confirm("Do you really wish to update the selected users?"))
				return true
			else
				return false
		}
		else
		{
			alert("Please select a customer.")

			return false
		}
	}
	function changeStatus(varValue)
	{
		switch(varValue)
		{
			case 'D':
				varMsg = "Do you really wish to delete member(s)?"
				break;
			case 'Y':
				varMsg = "Do you really wish to activate member(s)?"
				break;
			case 'N':
				varMsg = "Do you really wish to de-activate member(s)?"
				break;
		}
		if(isAnySelect())
		{
			if(confirm(varMsg))
			{
				document.frmMember.txtMode.value = varValue
				document.frmMember.submit();						
			}
		}
		else
		{
			alert("Please select a user")
			return false
		}
	}

	function checkMain(frm){
	var j = 0;
	for(i=0; i < frm.del.length; i++){
		if(frm.del[i].checked){
			j = j + 1;	
		}
	}
	if(frm.del.length != j){
		frm.chkNothing.checked = false;
	}else{
		frm.chkNothing.checked = true;
	}
}