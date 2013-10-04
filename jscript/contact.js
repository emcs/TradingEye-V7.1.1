/*
	Modified:	06/06/2006
	Function:	Display the contact form
	Company:	Tradingeye
*/

function submitContact(frm){
	if(isNull(frm.sName.value)){
		alert("Please enter the Name");
		frm.sName.focus();
		return false;
	}
	
	if(!checkEmail(frm.sEmail.value)){
		alert("Please enter valid Email");
		frm.sEmail.focus();
		return false;
	}

	if(frm.sCountry.value <= 0){
		alert("Please select the Country");
		frm.sCountry.focus();
		return false;
	}
	return true;
}