//======FUNCTION CURRENCY===================================
var isCurrency_re    = /^\s*((\d+(\.\d\d)?)|(\.\d\d))\s*$/; 
function CHKCurrency (s) { 
var x=s;

   return String(x).search (isCurrency_re) != -1 
}
//=======================================
function CHKInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}
//======================================
function trim(s){   var i;
	var returnString = "";
	for (i = 0; i < s.length; i++)
	{   
		// Check that current character isn't whitespace.
		var c = s.charAt(i);
		if (c != " ") returnString += c;
	}
	return returnString;
}
//========================================

function CHKtime(thetime) {
	var h,m,s,a,b,c,f,err=0;
	a=thetime;
	if (a.length != 8) err=1;
	h = a.substring(0, 2);
	c = a.substring(2, 3); 
	m = a.substring(3, 5); 
	d = a.substring(5, 6);
	s = a.substring(6, 8);
 //alert (h+":"+m+":"+s);
	if (/\D/g.test(h)) err=1; //not a number
	if (/\D/g.test(m)) err=1; 
	if (/\D/g.test(s)) err=1;
	if (h<0 || h>23) err=1;
	if (m<0 || m>59) err=1;
	if (s<0 || s>59) err=1;
	if (c != ':') err=1;
	if (d != ':') err=1;
	if (err==1) {
		alert ('That is not a valid time.\nPlease re-enter in format HH:MM:SS');
		//thetime.select();
		return false;
		
	}
  else
  return true;
}
//==== DATE==============
var dtCh= "-";
var minYear=1900;
var maxYear=2100;

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strYear=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strDay=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : yyyy-mm-dd")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || CHKInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}

function checkemail()
{
var str=document.regfrm.email.value;
var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
if (filter.test(str))
testresults=true;
else{
alert("Please input a valid email address!");
testresults=false;

}
return (testresults);
}