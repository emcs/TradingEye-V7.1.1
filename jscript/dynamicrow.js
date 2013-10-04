// FUNCTION TO SET THE FOCUS OF THE CURSOR TO THE SPECIFIED FIELD
 // id  Table id
 // field Field To Set Focus To

function tableFieldFocus(id, field)
 {
 	
  	var table = document.getElementById(id);
  	
  // get number of current rows
  	var rowItems = table.getElementsByTagName("tr");
  	var rowCount = rowItems.length;
    var seq = rowCount-1;
  

//  	document.getElementById(field + seq).focus();
 }
 

  // id Table id
 // no Number of Rows To Add
 function insertCycleTableRow(id,no)
 {
  
 var table = document.getElementById(id);
 
  // get number of current rows
  var rowItems = table.getElementsByTagName("tr");
  var rowCount = rowItems.length;

  var seq = rowCount-1;
  	insertRow(table,rowItems,rowCount,seq,1,0);
  var seq = rowCount-2;
  	insertRow(table,rowItems,rowCount,seq,1,0);
  var seq = rowCount-3;
  	insertRow(table,rowItems,rowCount,seq,1,0);
  var seq = 0;
 	insertRow(table,rowItems,rowCount,seq,1,0);
  
  
  }
  
 
 // id Table id
 // no Number of Rows To Add
 function insertTableRow(id,no)
 {
  
 var table = document.getElementById(id);
 
  // get number of current rows
  var rowItems = table.getElementsByTagName("tr");
  var rowCount = rowItems.length;
  
  // Insert Row 1
    
  if(id=='itemCycle'){
	  var seq = rowCount-1;	  
  	  insertRow(table,rowItems,rowCount,seq,1,0);
  }else{
	  var seq = rowCount-2;
  	  insertRow(table,rowItems,rowCount,seq,0,1);
  }
  // Insert Row 2
  if (no == 2){  		
	  if(id=='itemCycle'){
		   var seq = rowCount-2;
		   insertRow(table,rowItems,rowCount,seq,1,0);
		   var seq = rowCount-3;
		   insertRow(table,rowItems,rowCount,seq,1,0);
	  }else{
		   var seq = rowCount-2;
		   insertRow(table,rowItems,rowCount,seq,0,0);
		   var seq = rowCount-3;
		   insertRow(table,rowItems,rowCount,seq,0,0);
	  }
   }
 }
 
 
// DUPLICATES THE ROW [Called from insertTableRow()]
 function insertRow(table,rowItems,rowCount,seq,cyc,flag) {
 	 	
  // insert the new row
   if(flag==1)
	 {
		  	var newRow = table.insertRow(rowCount-1);
  	
	 }
	 else
	 {
			var newRow = table.insertRow(rowCount);

	 }

	 
 
  // get count of cells for the last row (so we know how many to insert)
  	var cellItems = rowItems[seq].getElementsByTagName("td");
  	
   	var cellCount = cellItems.length;
 	
  
  // loop over the children of the last set of cells
  	for (i=0; i<cellCount; i++){
   	 	// insert an analagous cell and align to the top
   	 	
  	    var cell = newRow.insertCell(i);
  	    
		 var className = cellItems[i].className;
		  cell.className = className;

		   if(cellItems[i].id!="")
				{
				var colIDArray = cellItems[i].id.split("_");
				var no1 = parseInt(colIDArray[1])+1;
				cell.setAttribute("id",colIDArray[0] + "_" + no1);
				}

   
    	// get the children of each cell
		  var kids = cellItems[i].childNodes;
		
		  for (j=0; j<kids.length; j++)
		  {
		    var newChild = kids[j].cloneNode(true);
   			if (newChild.nodeType == 1){
			    // Parse only form fields (elements that have a ID attribute)
				if (newChild.getAttribute("id"))
				{
					// Retrieve the name part of the original ID
					var strIDArray = newChild.getAttribute("id").split("_");
					// Add 1 to the field sequence ID number
					var no = parseInt(strIDArray[1])+1;
					// Compose and append the new ID
					newRow.setAttribute("id","lineitem_" + no);
				    newChild.setAttribute("id",strIDArray[0] + "_" + no);
					var strID = newChild.getAttribute("id");
					if(strID=="ccount_"+no)
					{
						var cnt=rowCount/4
						newChild.innerHTML="<b>Cycle "+(cnt+1)+"</b>";
					}

					if(strID=="iTotal_"+no)
					{
						newChild.innerHTML="<b>&pound;0</b>";
					}

					if(strID=="txtPrice_"+no || strID=="txtSetupPrice_"+no)
					{
						newChild.setAttribute("value", "0");
					}
					else
					{
						newChild.setAttribute("value", "");
					}
				}
   			}

   		  // copy data into this cell
      	  cell.appendChild(newChild);      	  
    	}
  	}
  	 	
 }
 
 
	
 function delRow(table,rowno)
 {
	
		rownum = rowno.split("_");
		//	alert(rownum[1]);
		//len = table.rows.length;
		//if(len>1)table.deleteRow(len-1);
		if(rownum[1] > 0)
			table.deleteRow(rownum[1]);
 }	


/////layer setiings///
function FP_changeProp() {//v1.0
 var args=arguments,d=document,i,j,id=args[0],o=FP_getObjectByID(id),s,ao,v,x;
 d.$cpe=new Array(); if(o) for(i=2; i<args.length; i+=2) { v=args[i+1]; s="o";
 ao=args[i].split("."); for(j=0; j<ao.length; j++) { s+="."+ao[j]; if(null==eval(s)) {
  s=null; break; } } x=new Object; x.o=o; x.n=new Array(); x.v=new Array();
 x.n[x.n.length]=s; eval("x.v[x.v.length]="+s); d.$cpe[d.$cpe.length]=x;
 if(s) eval(s+"=v"); }
}

function FP_getObjectByID(id,o) {//v1.0
 var c,el,els,f,m,n; if(!o)o=document; if(o.getElementById) el=o.getElementById(id);
 else if(o.layers) c=o.layers; else if(o.all) el=o.all[id]; if(el) return el;
 if(o.id==id || o.name==id) return o; if(o.childNodes) c=o.childNodes; if(c)
 for(n=0; n<c.length; n++) { el=FP_getObjectByID(id,c[n]); if(el) return el; }
 f=o.forms; if(f) for(n=0; n<f.length; n++) { els=f[n].elements;
 for(m=0; m<els.length; m++){ el=FP_getObjectByID(id,els[n]); if(el) return el; } }
 return null;
}

function setPos(obj, dest, YOffset)
{
	
	var curleft = curtop = 0;
	if (obj.offsetParent)
	{
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent)
		{
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	//curleft = curleft - 100;
	curtop = curtop + YOffset;
	dest.style.top = curtop;
	//dest.style.left = curleft;
}


function oW(myLink,windowName)
{
	
	if(!window.focus)
	{
		return false;
	}
	var myWin=window.open("",windowName,"left=0,top=0,width=760,height=500,dependent=yes,resizable=yes,scrollbars=no,status=yes");
	myWin.focus();
	myLink.target=windowName;
}
