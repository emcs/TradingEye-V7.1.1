<?
$tdate = date("Y-m-d");
$expdate = $month.$year;
$ccnumber = "4111111111111111";
$expdate = "0108";
$donamt = "0.02";

$host="test-payflow.verisign.com";
$port = 443;

/*$str='USER=skincancer&VENDOR=skincancer&PARTNER=VeriSign&PWD=melanoma27&TRXTYPE=S&TENDER=C&ACCT=".$ccnumber."&EXPDATE=".$expdate."&AMT=".$donamt."&ZIP=$donzip&PONUM=donation&DESC=donation&COMMCARD=U&TAXAMT=0.0 ';*/
$str="USER=drylandtraining&VENDOR=drylandtraining&PARTNER=verisign&PWD=buckeye1&TRXTYPE=S&TENDER=C&ACCT=$ccnumber&EXPDATE=$expdate&AMT=$donamt&VERBOSITY=MEDIUM";

exec("perl execute.pl $host $port '$str'",$varans);

print_r ($varans);
die();
$RESULT = explode("RESULT=",$varans[0]);
$RESULT = explode("&",$RESULT[1]);
$RESULT = $RESULT[0];
$PNREF = explode("PNREF=",$varans[0]);
$PNREF = explode("&",$PNREF[1]);
$PNREF = $PNREF[0];



if($RESULT == 0)
{
if($inner == "") $inner = 0;

$query = "insert into mtb_donation values('','$tdate','$how','$honorname','$ackname','$ackaddr', '$ackcity','$ackstate','$ackzip','$donname','$donaddr','$donphone','$doncity','$donstate', '$donzip','$donamt','$donemail','$PNREF','$inner')";
//updateQuery($query);

$donname = urlencode($donname);
print ("location:../catalog/thanks.php?donname=$donname&vstatus=$RESULT");

}
else
{
$low = urlencode($low);
$honorname = urlencode($honorname);
$ackname = urlencode($ackname);
$ackaddr = urlencode($ackaddr);
$ackcity = urlencode($ackcity);
$ackstate = urlencode($ackstate);
$ackzip = urlencode($ackzip);
$donname = urlencode($donname);
$donaddr = urlencode($donaddr);
$doncity = urlencode($doncity);
$donstate = urlencode($donstate);
$donzip = urlencode($donzip);
$donemail = urlencode($donemail);
$donphone = urlencode($donphone);
print ("location:../catalog/donation.php?enter=0&low=$low&honorname=$honorname&ackname=$ackname&ackaddr=$ackaddr&ackcity=$ackcity&ackstate=$ackstate&ackzip=$ackzip&donname=$donname&donaddr=$donaddr&doncity=$doncity&donstate=$donstate&donzip=$donzip&donemail=$donemail&donphone=$donphone&inner=$inner&vstatus=$RESULT");
}

?>