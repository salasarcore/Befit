   var http_request = false;
   var spanID="";
   function makePOSTRequest(url, parameters,spID) 
   {
   spanID=spID;
      document.getElementById(spanID).innerHTML = "<br><img src='../images/loading.gif' alt='Loading..'><br> Please Wait ..";
      http_request = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
         	 http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {
	
			}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      
      http_request.onreadystatechange = alertContents;
      http_request.open('POST', url, true);
      http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      http_request.setRequestHeader("Content-length", parameters.length);
      http_request.setRequestHeader("Connection", "close");
      http_request.send(parameters);
   }

   function alertContents() 
   {
      if (http_request.readyState == 4) 
	  {
         if (http_request.status == 200) 
		 {
           // alert(http_request.responseText);
            result = http_request.responseText;
            document.getElementById(spanID).innerHTML = result;            
         } else {
            alert('There was a problem with the request.');
         }
      }
   }
   
   
