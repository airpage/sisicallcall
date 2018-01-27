<?   
//이곳에 부여 받은 토큰 정보를 입력합니다.   
$myToken = "MYTOKENMYTOKEN";
 
//이곳에 개발자 등록당시 사용한 호스트 정보를 입력합니다.
$myHost = "airpage.org";    
 
//전화걸기 요청을 보내기 위한 방법 입니다.
        //내전화번호, 내ISO코드, 전화요청 대상 전화번호, 전화요청 대상 ISO코드, 전화요청 대상에게 표시될 메시지, 요청명령
send_sisicallcall_service("01010041004","kr","01011111111","kr","조금 까다롭던 그 고객", "callback");
 
//전송한 전화걸기 요청을 취소하기 위한 방법 입니다.
        //내전화번호, 내ISO코드, 전화요청 대상 전화번호, 전화요청 대상 ISO코드, 취소명령
//send_sisicallcall_service("01010041004","kr", "01011111111","kr","", "cancel");
 
class GCrypt
{   
        var $sKey = "";     
             
				function setKey($KEY)
				{
            $this->sKey = $KEY;
				}                       
                                                                             
				function encrypt ($value)
				{                
				    $padSize = 16 - (strlen ($value) % 16) ;
				    $value = $value . str_repeat (chr ($padSize), $padSize) ;
				    $output = mcrypt_encrypt (MCRYPT_RIJNDAEL_128, $this->sKey, $value, MCRYPT_MODE_CBC, str_repeat(chr(0),16)) ;                
				    return base64_encode ($output);
				}
 
				function getmillis()
				{
						date_default_timezone_set("GMT+0");
    				list($u_sec, $sec) = explode(' ', microtime());
    				return (int) ((int) $sec * 1000 + ((float) $u_sec * 1000));
				}
}
         
    
 
function send_sisicallcall_service($p_myphonenumber, $p_myiso, $p_phonenumber, $p_iso, $p_mymsg, $p_how)
{       
      global $myHost, $myToken;
         
      $mcrypt = new GCrypt();
      $mcrypt->setKey($myToken);
                                             
    	$now = $mcrypt->getmillis();         
                             
      $data = array(
	        "myaction" => $mcrypt->encrypt($p_how),
	        "mymsg" => $mcrypt->encrypt($p_mymsg),
			    "iso" => $mcrypt->encrypt($p_iso), //target   
			    "phonenumber" => $mcrypt->encrypt($p_phonenumber), //target
			    "myiso" => $mcrypt->encrypt($p_myiso), //my   
			    "myphonenumber" => $mcrypt->encrypt($p_myphonenumber), //my 
			    "mytoken" => $mcrypt->encrypt($myToken), //my 
			    "foot" => $mcrypt->encrypt($now)
			);  
 
		  $content = json_encode($data);                                  
		    
		  $post = curl_init();
		 
		  curl_setopt($post, CURLOPT_REFERER, 'http://' . $myHost .'/');          
		  curl_setopt($post, CURLOPT_URL, 'http://airpage.org/callback/callback_api.php');
		  curl_setopt($post, CURLOPT_POST, true);
		  curl_setopt($post, CURLOPT_HTTPHEADER, array("Content-type: application/json"));        
		  curl_setopt($post, CURLOPT_POSTFIELDS, $content);       
		  curl_setopt($post, CURLOPT_RETURNTRANSFER, true);
		 
		  $result = curl_exec($post);
		  echo $result;
		                    
		  $json_list= json_decode($result, true);
		  curl_close($post);
		         
			if(strcmp($json_list['result'],"success") == 0) return 0; //success
		 
			return 1; //failed
}   
?>