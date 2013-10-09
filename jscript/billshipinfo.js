<script language="JavaScript" type="text/javascript">
		function disableField(field,stat) {
			field.disabled = stat;
		}		
		function isEmail(s){
			var i = 1;
			var sLength = s.length;
			while ((i < sLength) && (s.charAt(i) != "@")){ i++ }
			if ((i >= sLength) || (s.charAt(i) != "@")) return false;
				else i += 2;
			while ((i < sLength) && (s.charAt(i) != ".")){ i++ }
			if ((i >= sLength - 1) || (s.charAt(i) != ".")) return false;
				else return true;
		}
		function validateForm(form)
		{
			if(isNull(form.first_name.value) || form.first_name.value=='first name'){
				alert("Please enter your First Name...");
				form.first_name.focus();
				return false;
			}
			if(isNull(form.last_name.value) || form.last_name.value=='last name'){
				alert("Please enter your Last Name...");
				form.last_name.focus();
				return false;
			}
			if(!checkEmail(form.email.value)){
				alert("Please enter a valid Email Address...");
				form.email.focus();
				return false;
			}
			if(isNull(form.address1.value) || form.address1.value=='address 1'){
				alert("Please enter your Address 1...");
				form.address1.focus();
				return false;
			}
			if(isNull(form.city.value) || form.city.value=='city'){
				alert("Please enter your City...");
				form.city.focus();
				return false;
			}
			index = form.bill_country_id.value;
			if(stateName[index].length > 1){
				if(isNull(form.bill_state_id.value) || form.bill_state_id.value == -1){
				alert("Please select your County/State...");
				form.bill_state_id.focus();
				return false;
				}
			}else{
					if(isNull(form.bill_state.value) || form.bill_state.value==''){
					alert("Please enter your County/State...");
					form.bill_state.focus();
					return false;
				  }
				}
			if(isNull(form.zip.value) || form.zip.value=='postcode/zip'){
				alert("Please enter Postcode/Zip...");
				form.zip.focus();
				return false;
			}
			if(isNull(form.phone.value) || form.phone.value=='telephone'){
				alert("Please enter your Telephone...");
				form.phone.focus();
				return false;
			}
			if(isdefined(form.alt_name))
			{
				// Alternative address validations.
				if(isNull(form.alt_name.value) || form.alt_name.value=='full name'){
					alert("Please enter your Full Name...");
					form.alt_name.focus();
					return false;
				}
				if(isNull(form.alt_address1.value) || form.alt_address1.value=='address 1'){
					alert("Please enter your Delivery Address 1...");
					form.alt_address1.focus();
					return false;
				}
				if(isNull(form.alt_city.value) || form.alt_city.value=='city'){
					alert("Please enter your Delivery City...");
					form.alt_city.focus();
					return false;
				}
				index = form.ship_country_id.value;
				if(stateName[index].length > 1){
					if(isNull(form.ship_state_id.value) || form.ship_state_id.value == -1){
					alert("Please select your shipping County/State...");
					form.ship_state_id.focus();
					return false;
					}
				}else{
						if(isNull(form.ship_state.value) || form.ship_state.value==''){
						alert("Please enter your shipping County/State...");
						form.ship_state.focus();
						return false;
					  }
					}
				if(isNull(form.alt_zip.value) || form.alt_zip.value=='postcode/zip'){
					alert("Please enter your Delivery Postcode/Zip...");
					form.alt_zip.focus();
					return false;
				}
				if(isNull(form.alt_phone.value) || form.alt_phone.value=='telephone'){
					alert("Please enter your Delivery Telephone...");
					form.alt_phone.focus();
					return false;
				}
			}	
			if(!form.terms_agree.checked){
				alert("You must agree to the terms and conditions...");
				form.terms_agree.focus();
				return false;
			}
			return true;
		}
	function getValue(frm, fieldname){
		var field = eval("document." + frm + "."+fieldname);
		if(isdefined(field)){
			return field.value;
		}else{
			return false;
		}
	}
	function trimchar(pstr)
	{
		var lenstr = pstr.length;
		for(var i = 0 ; pstr.charAt(i) == " "; i++);
		for(var j = pstr.length - 1; pstr.charAt(j)== " "; j--);
		j++;
		if (i > j)
			pstr = "";
		else
			pstr = pstr.substring(i,j);
		return pstr;
	}
	function mod10 (strNum) {
	   var nCheck = 0;
	   var nDigit = 0;
	   var bEven = false;
	   
	   for (n = strNum.length - 1; n >= 0; n--)
	   {
		  var cDigit = strNum.charAt (n);
		  if (isDigit (cDigit))
		  {
			 var nDigit = parseInt(cDigit, 10);
			 if (bEven)
			 {
				if ((nDigit *= 2) > 9)
				   nDigit -= 9;
			 }
			 nCheck += nDigit;
			 bEven = ! bEven;
		  }
		  else if (cDigit != ' ' && cDigit != '.' && cDigit != '-')
		  {
			 return false;
		  }
	   }
	   return (nCheck % 10) == 0;
	}
	function expired( month, year )
	{
		var now = new Date();
		var expiresIn = new Date(year,month,0,0,0);
		expiresIn.setMonth(expiresIn.getMonth()+1);
		if( now.getTime() < expiresIn.getTime() ) return false;
		return true;								
	}
	//29-05-06:RUS added this function to validate start date
	function isBeforeNow(cardStartMonth, cardStartYear)
	{
		var now = new Date();
		var startsIn = new Date(cardStartYear,cardStartMonth,0,0,0);
		startsIn.setMonth(startsIn.getMonth()-1);
		if(startsIn.getTime() < now.getTime() ) 
		{
			return false;
		}
		return true;
	}
	function validatefields(pstCardStartMonth,pstCardStartYear,pstIssuenumber) {
		if (pstIssuenumber !="") { // filled in, so we need to verify
			if(isNaN(parseInt(pstIssuenumber)))	{
				alert("Sorry! Please enter a valid issue number.");
				return false;
			}else if(pstIssuenumber.length!=2) {
				alert("Sorry! Please enter a valid issue number.");
				return false;
			}
		}
		if (pstCardStartMonth!="" && pstCardStartYear !="")
		{ // filled in, so we need to verify
				if (isBeforeNow(pstCardStartMonth, pstCardStartYear))
				{	
					alert("Sorry! The start date you have entered would make this card invalid.");
					return false;
				}
		}
		return true;	
	}
	//29-05-06:RUS updated this function to add parameters for start date and issue number
	function validateCard(cc_number,cardType,cardMonth,cardYear,cardStartMonth, cardStartYear, issuenumber, pstCardType, cv2) {
		if( cc_number.length == 0 ) {				
			alert("Please enter a valid card number.");
			return false;				
		}
		for( var i = 0; i < cc_number.length; ++i ) {	
			var c = cc_number.charAt(i);
			if( c < '0' || c > '9' ) {
				alert("Please enter a valid card number. Use only digits. do not use spaces or hyphens.");
				return false;
			}
		}
		var length = cc_number.length;
			switch( cardType ) {
			case 'm':
			case 'MC':
			case 'Mastercard':
				if( length != 16 ) {
					alert("Please enter a valid MasterCard number.");
					return;
				}
				var prefix = parseInt( cc_number.substring(0,2));
				if( prefix < 51 || prefix > 55) {
					alert("Please enter a valid MasterCard Card number.");
					return;
				}
			break;
			case 'v':
			case 'VISA':
				if(length != 16 && length != 13) {
					alert("Please enter a valid Visa Card number.");
					return;
				}
				var prefix = parseInt( cc_number.substring(0,1));											
				if( prefix != 4 ) {
					alert("Please enter a valid Visa Card number.");
					return;
				}
				// 26-06-06: DPI:GSM added validation for cv2
				if(isNull(cv2) || !isNumeric(cv2)){
					alert("Please enter a valid security code.");
					return false;
				}
			break;
			case 'a':											
			case 'AMEX':
				if( length != 15 ) {
					alert("Please enter a valid American Express Card number.");
					return;
				}
				var prefix = parseInt( cc_number.substring(0,2));											
				if(prefix != 34 && prefix != 37 ) {
					alert("Please enter a valid American Express Card number.");
					return;
				}
				break;
			case 'd':
			case 'DISCOVER':
				if( length != 16 ) {
					alert("Please enter a valid Discover Card number.");
					return;
				}
				var prefix = parseInt( cc_number.substring(0,4));											
				if( prefix != 6011 ) {
					alert("Please enter a valid Discover Card number.");
					return;
				}
				break;
			case 'DinnersClub':
			if( length != 14 ) {
				alert("Please enter a valid Dinners Club Card number.");
				return;
			}
			var prefix = parseInt( cc_number.substring(0,3));											
			if((prefix < 300 || prefix > 305) || (prefix != 36 && prefix != 38 )) {
				alert("Please enter a valid Discover Card number.");
				return;
			}
			break;
		
		}
		if( !mod10( cc_number ) ) { 	
			alert("Sorry! this is not a valid credit card number.");
			return false;
		}
		// 26-06-06: DPI:GSM added validation for cv2
		if(isNull(cv2) || !isNumeric(cv2)){
			alert("Please enter a valid security code.");
			return false;
		}
		//29-05-06:RUS added this code to check the expiry date is not empty
		if(cardMonth == "" || cardYear == "")
		{
			alert("Please enter the expiry date for this card.");
			return false;
		}
		else
		{
			if( expired( cardMonth, cardYear ) ) {	
				alert("Sorry! The expiry date you have entered would make this card invalid.");
				return false;
			}
		}
		//END	
		/*29-05-06:RUS added this code to check start date and/or issue number depending upon card type
		if  card type is maestro/switch/solo*/
		if (pstCardType == "SWITCH" || pstCardType == "Maestro" || pstCardType == "SOLO") 
		{
			if ((cardStartMonth !="" && cardStartYear !="") || issuenumber !="") 
			{ 
				if( validatefields(cardStartMonth,cardStartYear,issuenumber)){
				 return true;
				}
				else {
					return false;
				}	
			}
			else
			{
				alert("Please enter an issue number or start date for this card.");
				return false;
			}
		return true;			
		}
		else
		{
				if( validatefields(cardStartMonth,cardStartYear,issuenumber)){
				 return true;
				}
				else {
					return false;
				}
		}
		return true;
	}
function getCKDRadio(radio) {
	var selected = false;
	var ckVal = "none";
	if(radio.length)
	{
		for (var i = 0; i < radio.length; i++) 
		{
			if (radio[i].checked) 
			{
				selected = true;
				ckVal = radio[i].value;
				break;
			}
		}
	}
	else
	{
		selected = true;
	}
	<!-- BEGIN TPL_NOMETHOD_BLK1 -->
	if(radio.paymethods.value=='no')
	{
		selected = true;
		ckVal='notpay';
	}
	<!-- END TPL_NOMETHOD_BLK1 -->
	if (selected == false)
	{
		alert("Please select a payment option.");
	}
	return ckVal;
}
function checkPAY(form){
	if(getCKDRadio(form)!="none"){
		var ckVal = getCKDRadio(form);
		if(ckVal == "cc"){
			if(form.cc_number.value != ""){

			if(!isdefined(form.cc_type)){
				cardType='norequired';
			}else{
				var cardType = form.cc_type[form.cc_type.selectedIndex].value;
				}
			if(cardType != "")
			{
				if(!isdefined(form.cc_start_month))
				{
					ccstartmonth="";
					ccstartyear="";
					issuenumber="";
				}
				else
				{
						ccstartmonth=form.cc_start_month.value;
						ccstartyear=form.cc_start_year.value
						issuenumber=trimchar(form.issuenumber.value);
				}
				if (validateCard(
					form.cc_number.value,
					cardType,
					form.cc_month.options[form.cc_month.selectedIndex].value,
					form.cc_year.options[form.cc_year.selectedIndex].value,
					ccstartmonth,
					ccstartyear,
					issuenumber,cardType,
					form.cv2.value
					)) {
						return true;
					} else {
						return false;
					}
		
			} else {
					alert("Please select a credit card type.");
					form.cc_type.focus();
					return false;
				}
			} else {
				alert("Please enter a Credit Card Number");
				form.cc_number.focus();
				return false;
			}
		} 
		else if(ckVal == "eft")
		{
			if(form.acct.value != "")
			{
				if(form.aba.value != "")
				{
					return true;
				}
				else 
				{
					alert("Please enter your ABA Account number.");
					form.aba.focus();
					return false;
				}
			}
			else 
			{
				alert("Please enter Checking Account number.");
				form.acct.focus();
				return false;
			}
		} else return true;
	} else return false;
}
  	function validateForm(form){
	if(checkPAY(form)) return true;
		else return false;
}
//END
//-->
</script>