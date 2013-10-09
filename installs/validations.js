//For checking Null values
function isNull(aStr)
{
	var index;		
	for (index=0; index < aStr.length; index++)
		if (aStr.charAt(index) != ' ')
			return false;
	return true;
}

function PopWindow()
{
	window.open("","windowname","toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=425,height=425,top=50,left=50");
}

	
//For checking invalid E-Mail address
var reEmail=/^[0-9a-zA-Z_\.-]+@[0-9a-zA-Z_\.-]+\..{2,8}$/;

function checkEmail(str){
	var at="@";
	var dot=".";
	var lat=str.indexOf(at);
	var lstr=str.length;
	var ldot=str.indexOf(dot);
	if (str.indexOf(at)==-1){
	   return false;
	}

	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
	   return false;
	}

	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		return false;
	}

	 if (str.indexOf(at,(lat+1))!=-1){
		return false;
	 }

	 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		return false;
	 }

	 if (str.indexOf(dot,(lat+2))==-1){
		return false;
	 }
	
	 if (str.indexOf(" ")!=-1){
		return false;
	 }
	 return true;
}

function checkphone(val){
	str = "^0";	
	var reg = new RegExp(str);
		return reg.test(val);
}

// For checking and allowing only certain numeric values for Quantity
function isNumeric(val,allow_dec,allow_neg){
	var str = "";
	if(allow_neg)         //value can be negative
	{
		str += "^-";
	}		
	if(allow_dec)         //value can be decimal 
	{
		str += "[0-9]{1,}\.{0,1}"; 
	}	
	str += "^[0-9]{1,}$";				
	var reg = new RegExp(str);
	return reg.test(val);
}
	
function isAlphaNumeric(varData){
	varRegExp = new RegExp("^[A-Za-z0-9_]+$");
	if(!varRegExp.test(varData))
	{
		return true
	}	
	return false
}

function IsValidImageName(strVal){
	nNoOfArguments = IsValidImageName.arguments.length;

	//if parameter is not supplied
	if(nNoOfArguments < 1){
		return false;
	}
		
	//valid characters a supplied string value can have
	var sValidChars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-";
	strVal = new String(strVal);	//convert the value to a string object
	
	var bReturn = true	
	var i = new Number(0);
	while ((bReturn) && (i < strVal.length)){
		bReturn = (sValidChars.indexOf(strVal.charAt(i)) >= 0)
		i++
	}
	return (bReturn);	
}

/* 
	Date Should be in MM/DD/YY
	date 1 > date 2 return 1
	date 1 < date 2 return -1
	date 1 = date 2 return 0

*/

function compareDates(dt1,dt2)
{
	var datepart1 = dt1.split("/");
	var datepart2 = dt2.split("/");
	
	for(i=0;i<datepart1.length;i++)
	{
		datepart1[i] = parseInt(parseFloat(datepart1[i]));
		datepart2[i] = parseInt(parseFloat(datepart2[i]));		
	}	
	
	if(datepart1[2] > datepart2[2])
		return 1;
	else if(datepart1[2] < datepart2[2])	 
		return -1;
	else if(datepart2[2] == datepart1[2])	 	
	{
		if(datepart1[0] > datepart2[0])
			return 1;
		else if(datepart1[0] < datepart2[0])	
			return -1;
		else if(datepart1[0] == datepart2[0])					 
		{
			if(datepart1[1] > datepart2[1])
				return 1;
			else if(datepart1[1] < datepart2[1])	
				return -1;			
		}
	}
		return 0;	
}


function isdefined(variable){
    //return (typeof(window[variable]) == "undefined")?  false: true;
	return (typeof(variable) == "undefined")?  false: true;
}  

