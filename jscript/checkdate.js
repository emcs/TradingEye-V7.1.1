function checkDate(dt)
{
	//var reg = new RegExp("[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$");
	var reg = /^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/;
	if(reg.test(dt))
	{
		var datepart = dt.split("/");
		for(i=0;i<datepart.length;i++)
			datepart[i] = parseInt(parseFloat(datepart[i]));
		if(datepart[0] > 31 || datepart[1] > 12)
			return false;
		else if((datepart[1] == 4 || datepart[1] == 6 || datepart[1] == 9 || datepart[1] == 11) && datepart[0] == 31)
			return false;	
		else if(datepart[1] == 2)
		{
			if(datepart[0] > 29)
				return false;
			if(!LeapYear(datepart[2]) && datepart[0] == 29)
				return false;
		}
		return true;						
	}
	return false;			
}

function LeapYear(intYear) {
	if (intYear % 100 == 0) {
		if (intYear % 400 == 0) { return true; }
	}
	else { 
		if ((intYear % 4) == 0) { return true; }
	}
	return false;
}

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
		if(datepart1[1] > datepart2[1])
			return 1;
		else if(datepart1[1] < datepart2[1])	
			return -1;
		else if(datepart1[1] == datepart2[1])					 
		{
			if(datepart1[0] > datepart2[0])
				return 1;
			else if(datepart1[0] < datepart2[0])	
				return -1;			
		}
	}
	return 0;	
}