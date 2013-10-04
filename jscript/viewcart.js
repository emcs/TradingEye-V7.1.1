	var memberflag = 0;

   var http = false;

   var http2 = false;

		if (window.XMLHttpRequest) {

			http = new XMLHttpRequest();

			http2 = new XMLHttpRequest();

		}

		else {

		  if (window.ActiveXObject) {

			 http = new ActiveXObject('Msxml2.XMLHTTP.6.0');

			 http2 = new ActiveXObject('Msxml2.XMLHTTP.6.0');

		  }

		}

	function to2decimals(mynumber)

	{

		mynumber = parseFloat(mynumber);

		mynumber = mynumber.toFixed(2);

		return mynumber;

	}

	function updateCheckout(xtra){

		var data = jQuery("form").serialize();

		data = data + "&getpostagecost=" + xtra + "&novattotal={TPL_VAR_NOVATTOTAL}";

		http.open('POST','{TPL_VAR_SITEURL}ecom/index.php?action=ecom.updateviewcart');

		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		http.setRequestHeader("Content-length", data.length);

		http.setRequestHeader("Connection", "close");

		http.send(data);

		http.onreadystatechange = function()

				{

					if (http.readyState==4)

					{

						var response = http.responseText;

						var update = new Array();

						update = response.split('&');

						//alert(response);

						var temparray;

						var cityselects = 0;

						var postagevalues = 0;

						var discountvalues = 0;

						var discountcoderesponse = 0;

						var giftcertvalues = 0;

						var subtotal = 0;

						var grandtotal = 0;

						var taxtotal = 0;

						var taxrate = 0;

						var postageamount = 0;

						var discountamount = 0;

						var discounttype = 0;

						var discountminimum = 0;

						var giftcertamount = 0;

						var vatpostageflag = 0;

						var specialdelivery = 0;

						var promotionaldiscount = 0;

						var bulkdiscount = 0;

						var postageweightprice = 0;

						var defaultvat;
						
						var giftcertcode = "";

						var x = 0;

						while(x < update.length)

						{

							temparray = update[x].split('->');

							if(temparray[0] == "STATELIST")

							{

								cityselects = temparray[1];

							}

							if(temparray[0] == "DISCOUNTPRICE")

							{

								discountvalues = temparray[1];

							}

							if(temparray[0] == "DISCOUNTCODE")

							{

								discountcoderesponse = temparray[1];

							}

							if(temparray[0] == "giftCertCode")

							{

								giftcertcode = temparray[1];

							}

							if(temparray[0] == "giftCertPrice")

							{

								giftcertvalues = temparray[1];

							}

							if(temparray[0] == "POSTAGECOST")

							{

								postagevalues = temparray[1];

							}

							if(temparray[0] == "VATPOSTAGEFLAG")

							{

								vatpostageflag = temparray[1];

							}

							if(temparray[0] == "DEFAULTVAT")

							{

								defaultvat = temparray[1];

							}
							
							

							if(temparray[0] == "CURRENCY")

							{

								var currency = '{TPL_VAR_CURRENCY}';

							}

							if(temparray[0] == "MEMBERPOINTS")

							{

								var memberpointarray = temparray[1].split("|");

								var memberpts = memberpointarray[2];

								var memberptval = memberpointarray[1];

								memberflag= memberpointarray[0];

								var membervaltotal = parseFloat(memberptval) * parseFloat(memberpts);

							}

							x = x + 1;

						}

						if(cityselects != 0 && xtra == 0)

						{

							if(navigator.appName == "Microsoft Internet Explorer") {

								jQuery("#state").after("<div id='tempdiv'></div>");

								jQuery("#state").remove();

								jQuery("#tempdiv").after('<select class="formSelect" id="state" name="bill_state_id" onchange="updateCheckout();">' + cityselects + '</select>');

								jQuery("#tempdiv").remove();

							} else {

								document.getElementById('state').innerHTML= cityselects;

							}

							document.getElementById('state').disabled = false;

							updateCheckout(1)

						}

						//if(jQuery("#giftcertprice").length && giftcertprice == 0)

						//{

						//	giftcertprice = jQuery('#giftcertprice').html();

						//	giftcertprice = parseFloat(giftcertprice.substring(2,giftcertprice.length));

						//}

						thediscountcode = "(" + jQuery("#discount").val() + ")";

						if(xtra != 0)

						{

						if(postagevalues != 0)

						{

							if(postagevalues.indexOf('*' != -1)) { 

							 temparray2 = postagevalues.split('*');

								//document.getElementById(temparray2[1]).innerHTML= temparray2[0];

								//document.getElementById(temparray2[3]).innerHTML= temparray2[2];

								//document.getElementById(temparray2[5]).innerHTML= temparray2[4];

								//document.getElementById(temparray2[7]).innerHTML= temparray2[6];

								//10.00*postageprice*20.45*grantotal*1.74*vatprice*20.00*vatpercent*15.00*specialdelivery

								taxrate = temparray2[6];

								postageamount = temparray2[0];

								specialdelivery = temparray2[8];

							}

						}

						else

						{

							taxrate = jQuery('#taxratebackup').html();

							if(taxrate == null)

							{

							taxrate = 0;

							}

							else

							{

							taxrate = taxrate.substring(taxrate.lastIndexOf("@")+2,taxrate.length);

							}

							postageamount = String(jQuery('#postageprice').html());

							postageamount = postageamount.substring(1,postageamount.length);

							specialdelivery = "?";

						}

						if(discountvalues != 0 && discountvalues != "0" && discountvalues != null)

						{

							//DISCOUNTPRICE->1,fix,1

							//value,type,minimum

							temparray2 = discountvalues.split(',');

							discountamount = parseFloat(temparray2[0]);

							discounttype = temparray2[1];

							discountminimum = parseFloat(temparray2[2]);
							
							if(discountamount > 0)
							{
							}
							else
							{
								discountamount = 0;
								discounttype = "fix";
								discountminimum = 0;
								thediscountcode = thediscountcode + " not found";
							}

						}

						if(giftcertvalues != 0 && giftcertvalues != "0")

						{

							//GIFTCERTPRICE->1

							//This is last one to be added due to it being able to pay for anything.

							giftcertamount = giftcertvalues;

						}

						//TOTAL IT ALL UP

						//First Set Remaining Values

						//subtotal,pdiscounts,volumed,weightprice,postageprice,discountprice,giftcertprice,taxprice,grandtotal

						subtotal = parseFloat({TPL_VAR_SUBTOTAL});

						if((jQuery('#weightprice').length && jQuery('#weightprice').html().length > 0))

						{

							postageweightprice = parseFloat(jQuery('#weightprice').html());

						}

						else

						{

							postageweightprice = 0;

						}

						if((jQuery('#pdiscounts').length && jQuery('#pdiscounts').html().length > 0))

						{

							promotionaldiscount = jQuery('#pdiscounts').html();

							promotionaldiscount = parseFloat(promotionaldiscount.substring(2,promotionaldiscount.length));

						}

						else

						{

							promotionaldiscount = 0;

						}

						if((jQuery('#volumed').length && jQuery('#volumed').html().length > 0))

						{

						

							bulkdiscount = jQuery('#volumed').html();

							bulkdiscount = parseFloat(bulkdiscount.substring(2,bulkdiscount.length));

						}

						else

						{

							

							bulkdiscount = 0;

						}

						//BEGIN CALCULATION

						//subtotal,pdiscounts,volumed,weightprice,postageprice,discountprice,giftcertprice,taxprice,grandtotal

						taxtotal = 0;

						var oldgrandtotal = jQuery('#grandtotal').html();

						oldgrandtotal = parseFloat(oldgrandtotal.substring(1,oldgrandtotal.length));

						grandtotal = 0;

						var runningtotal = subtotal;

						//alert(runningtotal);

						runningtotal = runningtotal - parseFloat(promotionaldiscount) - parseFloat(bulkdiscount) + parseFloat(postageweightprice);

							//alert(runningtotal);

						if(postageamount != "<span style='color:red;'>No rates defined</span>")

						{
							//alert(runningtotal);
							runningtotal = runningtotal + parseFloat(postageamount);

							//alert(runningtotal);

						}

						var newhtml ="<dt>{LANG_VAR_SUBTOTAL}</dt><dd id='subtotal'>{TPL_VAR_CURRENCY}{TPL_VAR_SUBTOTAL}</dd>";

						if(promotionaldiscount > 0)

						{

							newhtml = newhtml + "<dt>{TPL_VAR_PROMOTIONDESC}</dt><dd id='pdiscounts'>" + "-" + currency + to2decimals(promotionaldiscount) + "</dd>";

						}

						if(bulkdiscount > 0)

						{

							newhtml = newhtml + "<dt>{LANG_VAR_VOLUMEDISCOUNT}</dt><dd id='volumed'>" + "-" + currency + to2decimals(bulkdiscount) + "</dd>";

						}

						if(postageweightprice > 0)

						{

							newhtml = newhtml + "<dt>{LANG_VAR_PRODUCTWEIGHT} ({TPL_VAR_WEIGHT} kg)</dt><dd id='weightprice'>" + currency + to2decimals(postageweightprice) + "</dd>";

						}

						if(postageamount != "<span style='color:red;'>No rates defined</span>")

						{

							newhtml = newhtml + "<dt>{LANG_VAR_POSTAGEMETHOD} ({TPL_VAR_POSTAGENAME})</dt><dd id='postageprice'>" + currency + to2decimals(postageamount) + "</dd>";

						}

						else

						{

							newhtml = newhtml + "<dt>{LANG_VAR_POSTAGEMETHOD} ({TPL_VAR_POSTAGENAME})</dt><dd id='postageprice'>" + postageamount + "</dd>";

						}

						//alert(discounttype + "||" + discountminimum + "||" + discountamount);
						if(discounttype == "fix" && parseFloat(discountminimum) <= parseFloat(runningtotal))

						{
							if(discountamount < runningtotal)
							{
							runningtotal = runningtotal - parseFloat(discountamount);

							newhtml = newhtml + "<dt>Discount Code " + thediscountcode +"</dt><dd id='discountprice'>-" + currency + to2decimals(discountamount) + "</dd>";
							}
							else
							{
							discountamount = runningtotal;
							runningtotal = runningtotal - parseFloat(discountamount);

							newhtml = newhtml + "<dt>Discount Code " + thediscountcode +"</dt><dd id='discountprice'>-" + currency + to2decimals(discountamount) + "</dd>";
							}
							//alert(runningtotal + "2");
						}

						else if(discounttype == "percent" && parseFloat(discountminimum) <= runningtotal)

						{

							var tempdiscount = parseFloat(discountamount)/100*parseFloat(runningtotal);

							//alert(tempdiscount);
							//alert(runningtotal);
							runningtotal = parseFloat(runningtotal) - parseFloat(tempdiscount);

							//alert(runningtotal);

							newhtml = newhtml + "<dt>Discount Code " + thediscountcode +"</dt><dd id='discountprice'>" + currency + to2decimals(tempdiscount) + "</dd>";

							//newhtml = newhtml + "<dt>Discount Code " + thediscountcode +"</dt><dd id='discountprice'>" + currency + to2decimals((((100-parseFloat(discountamount))/100) * runningtotal)) + "</dd>";

						}

						else if(discounttype == "none")

						{
			
							newhtml = newhtml + "<dt>Discount Code (" + discountcoderesponse +")<small style='color:red;'> Invalid code</small></dt><dd id='discountprice'>-" + currency + "0.00" + "</dd>";

						}
						//alert(runningtotal);
						//alert(giftcertamount + "|" + runningtotal);
						if(giftcertcode != "")

						{

							if(giftcertamount >= parseFloat(runningtotal))

							{

								newhtml = newhtml + "<dt>Gift Certificate (" + giftcertcode + ")</dt><dd id='giftcertprice'>" + "-" + currency + to2decimals(runningtotal) + "</dd>";

								runningtotal = 0;

							}

							else

							{

								runningtotal = runningtotal - parseFloat(giftcertamount);

								newhtml = newhtml + "<dt>Gift Certificate (" + giftcertcode + ")</dt><dd id='giftcertprice'>" + currency + to2decimals((0 - parseFloat(giftcertamount))) + "</dd>";

							}

							

						}
						//alert(runningtotal);
						//members points

						logstatus = {TPL_VAR_LOGSTATUS};

						if(memberflag == 1 && logstatus == 1)

						{

							if(document.getElementById('memptsbox').checked)

							{

								if(parseFloat(membervaltotal) > runningtotal)

								{
									var memberused;
									var pttotal = runningtotal;
									if(parseFloat(memberptval) > 0)
									{
									memberused = (runningtotal) / parseFloat(memberptval);
									}
									else
									{
									memberused = runningtotal;
									}

									runningtotal = 0;
									//alert(runningtotal + "|" + memberptval + "|" + membervaltotal + "|" + memberused);
								}

								else

								{
									if(parseFloat(membervaltotal) > 0 && parseFloat(memberptval) > 0)
									{
									var memberused = parseFloat(membervaltotal) / parseFloat(memberptval);
									}
									else
									{
									var memberused = 0;
									}

									runningtotal = runningtotal - parseFloat(membervaltotal);

									var pttotal = parseFloat(membervaltotal);
									//alert(runningtotal + "|" + memberptval + "|" + membervaltotal + "|" + memberused);

								}

								newhtml = newhtml + "<dt>{LANG_VAR_REWARDPOINTS}("+memberused+")</dt><dd id='memberspts'>" + currency + to2decimals(pttotal) + "</dd>";

							}

						}

						//calc tax

						var temprunningtotal = runningtotal-parseFloat({TPL_VAR_NOVATTOTAL});

						if(temprunningtotal < 0)

						{

						temprunningtotal = 0;

						}

						if(vatpostageflag == "0")

						{

							if(postageamount != "<span style='color:red;'>No rates defined</span>")

							{

								taxtotal = to2decimals((parseFloat(taxrate)/100) * (temprunningtotal-postageweightprice-postageamount));

							}

							else

							{

								taxtotal = to2decimals((parseFloat(taxrate)/100) * (temprunningtotal-postageweightprice));

							}

						}

						else

						{

							taxtotal = to2decimals((parseFloat(taxrate)/100) * (temprunningtotal));

						}

							//alert(runningtotal);

						runningtotal = parseFloat(runningtotal) + parseFloat(taxtotal);

							//alert(runningtotal);

						grandtotal = to2decimals(runningtotal);

						if(taxrate != 0 && taxtotal > 0)

						{

						newhtml = newhtml + "<dt id='taxratebackup'>{TPL_VAR_TAXNAME} @ {TPL_VAR_VAT}%</dt><dd id='taxprice'>" + currency + taxtotal+"</dd>";

						}

						newhtml = newhtml + "<dt>{LANG_VAR_CURRENTTOTAL}</dt><dd id='grandtotal'>" + currency + grandtotal+"</dd></dt>";

						document.getElementById("total").innerHTML = newhtml;

						return false;

					}}

				}

				

	}

	

	function updateMemPoints()

	{

		

			updateCheckout(10);

	}

	function checkout()

	{

		if(memberflag == 0)

		{

			window.location = '{TPL_VAR_SAFESITEURL}ecom/index.php?action=checkout.billing';

		}

		else

		{

			window.location = '{TPL_VAR_SAFESITEURL}ecom/index.php?action=checkout.billing&member_points='+jQuery("#memptsbox").val();

		}

	}