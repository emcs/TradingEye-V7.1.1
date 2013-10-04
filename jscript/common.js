
var dayarray=new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday")
var montharray=new Array("Jan","Feb","March","Apr","May","June","July","Aug","Sep","Oct","Nov","Dec")

/*function showHideToggleRow(rowID,blnexpand){

	if(blnexpand == 'true') {	
		document.getElementById('show'+rowID).innerHTML = "-";
		document.getElementById(rowID).className='expandRow';
	} else {

//	alert(document.getElementById(rowID).className);
		if(document.getElementById(rowID).className=='collapseRow'){
			document.getElementById('show'+rowID).innerHTML = "-";
			document.getElementById(rowID).className='expandRow';
		}else{
			document.getElementById('show'+rowID).innerHTML = "+";
			document.getElementById(rowID).className='collapseRow';
		}
	}	
}*/

function swapimage(imagenumber)
{
		document.getElementById('productlargeimage').src = document.getElementById('hidden'+imagenumber).value;
		document.getElementById('viewlargelink').href = document.getElementById('thumbnailimagenumber'+imagenumber).value;	
}

function showHideToggleRow(grpId,blExpand){
	
	var objTable = document.getElementById('grp_'+grpId);
		
		if(blExpand == 'true')	{
			for(var i=0;i<objTable.rows.length;i++){
				if(i>0){
						objTable.rows[i].style.display = '';		
				}
			}			
			document.getElementById('anc_'+grpId).className = 'toggleCollapse';
		}
		else {
			if(document.getElementById('anc_'+grpId).className == 'toggleExpand')	{
				for(var i=0;i<objTable.rows.length;i++){
					if(i>0){
							objTable.rows[i].style.display = '';		
						}
					}			
					document.getElementById('anc_'+grpId).className = 'toggleCollapse';
			}else{
				for(var i=0;i<objTable.rows.length;i++){
					if(i>0){
							objTable.rows[i].style.display = 'none';
						}
					}			
					document.getElementById('anc_'+grpId).className = 'toggleExpand';				
			}
		}
}


function showHideDiv(postid,blShow) {
	
  var whichpost = document.getElementById(postid);
	if(blShow == 1){
		whichpost.className="expandRow";
	}else{		
		whichpost.className="collapseRow";
	}                       
}



function getthedate(){
	var mydate=new Date()
	var year=mydate.getYear()
	if (year < 1000)
		year+=1900
	var day=mydate.getDay()
	var month=mydate.getMonth()
	var daym=mydate.getDate()
	if (daym<10)
		daym="0"+daym
	/*var hours=mydate.getHours()
	var minutes=mydate.getMinutes()
	var seconds=mydate.getSeconds()
	var dn="AM"
	if (hours>=12)
	dn="PM"
	if (hours>12){
	hours=hours-12
	}
	if (hours==0)
	hours=12
	if (minutes<=9)
	minutes="0"+minutes
	if (seconds<=9)
	seconds="0"+seconds
	*/
	//change font size here

	var cdate=dayarray[day]+" "+daym+" "+montharray[month]+" "+year
	//  var cdate = daym + " "+montharray[month]+ " "+year

	//if (document.all)
	//document.all.clock.innerHTML=cdate
//	else if (document.getElementById)
//	document.getElementById("clock").innerHTML=cdate
//	else
//	document.write(cdate)
}

if (!document.all&&!document.getElementById)
	getthedate()
function goforit(){
	if (document.all||document.getElementById)
	getthedate();
	//	setInterval("getthedate()",0)
}


function poptastic(url,ht,wd)
{
	var newwindow;
	newwindow=window.open(url,'name','height='+ht+',width='+wd);
	if (window.focus) {newwindow.focus()}
}

function copy(text2copy) {
	if (window.clipboardData) {
		window.clipboardData.setData("Text",text2copy);
	} else {
		var flashcopier = 'flashcopier';
		if(!document.getElementById(flashcopier)) {
			var divholder = document.createElement('div');
			divholder.id = flashcopier;
			document.body.appendChild(divholder);
		}
		document.getElementById(flashcopier).innerHTML = '';
		var divinfo = '<embed src="'+sSiteUrl+'_clipboard.swf" FlashVars="clipboard='+escape(text2copy)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
		document.getElementById(flashcopier).innerHTML = divinfo;
	}
}

function fn_chk_sys_passwd(username, passwd) { 
	if ((passwd.length < 5) || (passwd.length > 14)) 
	return false; 
	if (passwd.length >= username.length) { 
		if (passwd.indexOf(username, 0) != -1) 
		return false; 
	} 
	if ((passwd.indexOf('\'') != -1) || (passwd.indexOf(' ') != -1)) 
		return false; 
		for (i = passwd.length; i-- > 0;) { 
			if (passwd.charCodeAt(i) > 127) return false; 
		} 
	return true; 
} 


function fn_chk_login(login) { 
	re = /^[a-zA-Z0-9]{1}[A-Za-z0-9_.-]{0,19}$/; 
	return login.search(re) != -1; 
} 

function hideMessage(divId){
//	if(document.getElementById(divId)) {
		document.getElementById(divId).style.display = 'none';
//	}
}

function formatCurrency(num) {

	if(isNaN(num)) {
		return false;
	}	
	
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();

	if(cents<10)
		cents = "0" + cents;

	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+
		
	num.substring(num.length-(4*i+3));
	
	priceVal = ((sign)?'':'-') + num + '.' + cents;
	return true;
}

function disableDrop(selecteddrop){
	if (selecteddrop == 'alphasort')
	{
	document.getElementById('sortbyprice').selectedIndex=0;
	}
	
	if(selecteddrop == 'sortbyprice')  
	{
	document.getElementById('alphasort').selectedIndex=0;
	}
}