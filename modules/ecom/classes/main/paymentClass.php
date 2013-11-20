<?php
/*
=======================================================================================
Copyright: Electronic and Mobile Commerce Software Ltd
Product: TradingEye
Version: 7.1.0
=======================================================================================
*/
defined('_TEEXEC') or die;
	class c_paymentGateways
	{
	/*	$retArray = array();
		$serverUrl	= "https://ukvpstest.protx.com/VPSDirectAuth/PaymentGateway.asp";
		$headers	= 1;
		$requestBody	= array();*/
		
		/**	sendHttpRequest
			Sends a HTTP request to the specified server with the body and headers passed
			Input:	$requestBody
					$serverUrl
					$headers
			Output:	The HTTP Response as a String
		*/
		function sendHttpRequest($requestBody, $serverUrl, $headers="")
		{
			
			//initialise a CURL session
			$connection = curl_init();
			//set the server we are using (could be Sandbox or Production server)
			curl_setopt($connection, CURLOPT_URL, $serverUrl);
			
			//stop CURL from verifying the peer's certificate
			curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
			
			//set the headers using the array of headers
			curl_setopt($connection, CURLOPT_HEADER, $headers);
			
			//set method as POST
			curl_setopt($connection, CURLOPT_POST, 1);
			
			//set the XML body of the request
			curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
			
			//set it to return the transfer as a string from curl_exec
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			
			//Send the Request
			$response = curl_exec($connection);
			//close the connection
			curl_close($connection);
			
			//$this->fnRetStatus($response);
			//return the response
			return $response;
		}

		function fnRetStatus($response){
			switch($this->payMethod){
				/*****************************************************************************
				authToken	= dbsettings.PayPal_Transaction_Token.smalltext>
				txToken		= GET['url.tx'];
				query		= "cmd=_notify-synch&tx=$txToken&at=$authToken";
				https://www.paypal.com/cgi-bin/webscr?#query#  method="GET"
				******************************************************************************/
				case "paypal_pdt":
					$this->pgRetStatus = substr($response,0,7);
					if($this->pgRetStatus == "SUCCESS")
					{
						$this->retStatus	= "success";
						$this->payStatus	= 1;
						$this->errMsg		= '';
					}
					else
					{
						$this->retStatus	= "error";
						$this->payStatus	= 2;
						$this->errMsg		= 'Authorization token doesn\'t match.';
					}
					break;
				/*****************************************************************************
				https://secure.authorize.net/gateway/transact.dll method="post"
				$requestBody['X_LOGIN']			= '';
				$requestBody['X_VERSION']		= '';
				$requestBody['X_PASSWORD']		= '';
				$requestBody['X_METHOD']		= '';
				$requestBody['X_CARD_NUM']		= '';
				$requestBody['X_EXP_DATE']		= '';
				$requestBody['X_AMOUNT']		= '';
				$requestBody['X_INVOICE_NUM']	= '';
				$requestBody['X_TYPE']			= '';
				$requestBody['X_LAST_NAME']		= '';
				$requestBody['X_FIRST_NAME']	= '';
				$requestBody['X_ADDRESS']		= '';
				$requestBody['X_CITY']			= '';
				$requestBody['X_STATE']			= '';
				$requestBody['X_ZIP']			= '';
				$requestBody['X_description']	= '';
				$requestBody['X_cust_id']		= '';
				$requestBody['X_Delim_Data']	= '';
				*****************************************************************************/
				case 'authorizenet':
					$arResponse=explode("|",$response);
					$this->payStatus=$arResponse[0];
					$this->errMsg=$arResponse[2]." ".$arResponse[3];	
					$this->transactionId=$arResponse[6];	
					$this->orderNumber=$arResponse[7];	
				break;
				case 'securetrading':
					 $this->transactionId= $this->streference;	
					 $this->orderNumber= $this->OrderId;	
					 die();
				break;
				default:
				break;

			}#end switch
	}#end func
	function protex_redirect_issuingbank_protx($arr_response,$defaultTerminalURL){
 		echo "<SCRIPT LANGUAGE=\"Javascript\"> function OnLoadEvent() {document.form.submit(); }</SCRIPT>";  
 		echo "<html><head><title>3D Secure Verification</title></head>" .    "<body OnLoad=\"OnLoadEvent();\">" .    "<FORM name=\"form\" action=\"" . $arr_response['ACSURL'] . "\"method=\"POST\">"; 
 		echo "<input type=\"hidden\" name=\"PaReq\" value=\"" .$arr_response['PAReq']. "\"/>";  
 		echo "<input type=\"hidden\" name=\"MD\" value=\"" . $arr_response['MD']."\"/>";  
 		echo   "<input type=\"hidden\" name=\"TermUrl\" value=\"" .$defaultTerminalURL . "\"/>";  
		echo "<NOSCRIPT><center><p>Please click button below to Authenticate yourcard</p><input type=\"submit\" value=\"Go\"/></p></center></NOSCRIPT>".    "</form></body></html>"; 
	}// end of function

	function arr_convert_response($response){
 		$result=array(); 
 		$response=preg_split('/\\n/',$response);   
		foreach($response as $key => $value){    $value=trim($value);
 			if($value != ""){      
   				$result=preg_split('/=/',$value,2);   
   				$arr_response[$result[0]]=$result[1]; 
   			}  
 		}// end of foreach   
		return $arr_response;
 	}
	function fnProtxStatus($response){
		$this->retArray=$this->arr_convert_response($response);
		switch(strtoupper(trim($this->retArray['Status'])))	{
			case "MALFORMED":
			case "INVALID":
			case "ERROR":
			case "NOTAUTHED":
			case 'REJECTED':
			case '3DAUTH':
				$this->retStatus	= "error";
				$this->payStatus	= 0;
				$this->errMsg		= $this->retArray['Status']." ".$this->retArray['StatusDetail'];
				$this->transactionId="";
			break;
			case "OK":
					$this->retStatus		= "success";
					$this->payStatus		= 1;
					$this->errMsg			= '';
					$this->transactionId	=trim($this->retArray['VPSTxId']);
			break;	
			default:
				$this->retStatus	= "error";
				$this->payStatus	= 0;
				$this->errMsg		= $this->retArray['Status']." ".$this->retArray['StatusDetail'];
				$this->transactionId="";
		}						
	} #End of protx status
	function fnProtx3DSecureStatus($response){
		$this->retArray=$this->arr_convert_response($response);
		       
        switch(strtoupper(trim($this->retArray['3DSecureStatus'])))	{
			case 'NOAUTH':
			case 'CANTAUTH':
			case 'NOTAUTHED':
			case "MALFORMED":
			case "INVALID":
			case "ERROR":
			case 'NOTCHECKED':
				$this->retStatus	= "error";
				$this->payStatus	= 0;
				$errmsg_protx_3dsecure=unserialize(PROTX_ERROR_MSG);
				$this->errMsg		=$this->retArray['StatusDetail'].'<br />';
				$this->errMsg		.= $this->retArray['3DSecureStatus'].": ".$errmsg_protx_3dsecure[$this->retArray['3DSecureStatus']];
				$this->transactionId=trim($this->retArray['VPSTxId']);
			break;
            case "ATTEMPTONLY":
            case "OK":
					$sagapayStatus = strtoupper(trim($this->retArray['Status']));
                    if($sagapayStatus == 'OK')
                    {        
                        $this->retStatus		= "success";
                        $this->payStatus		= 1;
                        $this->errMsg			= '';
                        $this->transactionId	=trim($this->retArray['VPSTxId']);
                    }else{
                        $this->retStatus	= "error";
                        $this->payStatus	= 0;
                        $this->errMsg		= $this->retArray['Status']." ".$this->retArray['StatusDetail'];
                        $this->transactionId="";
                    }    
			break;	
			default:
				$this->retStatus	= "error";
				$this->payStatus	= 0;
				$this->errMsg		= $this->retArray['Status']." ".$this->retArray['StatusDetail'];
				$this->transactionId="";
            break;    
		}						
	} #End of protx 3DSecureStatus
	
		//Function: For XML parsing..
	function xml2array($contents, $get_attributes=1, $priority = 'tag') {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return;

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();
        
        if(isset($value)) {
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
            foreach($attributes as $attr => $val) {
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } else { //There was another element with the same tag name

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }

                }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

            } else { //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    if($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;

                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) {
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }
                        
                        if($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }
    
    return($xml_array);
	}  

	function propay_response($arr){
		
		switch($arr['XMLResponse']['XMLTrans']['status']){
							
			case "59":
				$this->errMsg="User not authenticated";
				$this->flag="FAIL";
			break;
			case "58":
				$this->errMsg="Credit card declined";
				$this->flag="FAIL";
			break;
			case "49":
				$this->errMsg="Invalid Expiry Date.";
				$this->flag="FAIL";
			break;
			case "48":
				$this->errMsg="Invalid Credit Card Number.";
				$this->flag="FAIL";
			break;
			case "50":
				$this->errMsg="Invalid CVV2 Number.";
				$this->flag="FAIL";
			break;
			case "60":
				$this->errMsg="Credit card authorization timed out; retry at a later time.";
				$this->flag="FAIL";
			break;
			case "61":
				$this->errMsg="Amount exceeds single transaction limit.";
				$this->flag="FAIL";
			break;
			case "62":
				$this->errMsg="Amount exceeds monthly volume limit.";
				$this->flag="FAIL";
			break;
			case "63":
				$this->errMsg="Insufficient funds in account.";
				$this->flag="FAIL";
			break;
			case "64":
				$this->errMsg="Over credit card use limit.";
				$this->flag="FAIL";
			break;
			case "65":
				$this->errMsg="Miscellaneous error.";
				$this->flag="FAIL";
			break;
			case "66":
				$this->errMsg="Denied a ProPay account(You are requested to fill out ProPay exceptions form and submit it.).";
				$this->flag="FAIL";
			break;
			case "69":
				$this->errMsg="Duplicate invoice number (Transaction succeeded in a prior attempt within the previous 24 hours.  Please try again later.)";
				$this->flag="FAIL";
			break;
			case "00":
				$this->errMsg="";
				$this->flag="SUCCESS";
			break;
			default:
				$this->errMsg=$arr['XMLResponse']['XMLTrans']['status']."Error:Unable to process the order. Please check the credit card information and try again.";
				$this->flag="FAIL";
			break;
		}	
	}
	
	}#end class
?>