<?php 
function get_rest_url($email='')
{   
    
    if($email=='')
    {
	    if(isset( $_SESSION['wp_resturl']) &&  $_SESSION['wp_resturl']!='')
	    {
	        return  $_SESSION['wp_resturl'];
	    }
	    else 
	    	return false;
    }
    $response = connect_icomply(
                    'https://icomply02.isocialsmart.com/icomply-activation/rest/account/activate-email',
                    array('email'=>$email)
                );

    $response_array = (array) json_decode($response);
    if(isset($response_array['rest-url']) && $response_array['rest-url']!='')
    {
	    $wp_resturl = $response_array['rest-url'];
	    $_SESSION['wp_resturl'] = $wp_resturl;
	    return $wp_resturl;
    }
    return false;
}


function get_icomply_authtoken($wp_resturl,$data='')
{
    
    if(!is_array($data))
    {
	    if(isset( $_SESSION['wp_icomply_auth']) &&  $_SESSION['wp_icomply_auth']['userId']!='')
	    {
	        return  $_SESSION['wp_icomply_auth'];
	    }
	    else 
	    	return false;
    }
    $response = connect_icomply(
        $wp_resturl."/icomplyuser",
        json_encode($data),
        '',
        'POST'
    );
    $response_array = (array) json_decode($response);
    if(isset($response_array['token']) && $response_array['token']!='')
    {
	    $wp_icomply_auth['token'] = base64_encode($response_array['userId'].":".$response_array['token']);    
	    $wp_sn = (array) $response_array['socialNetworks'];    
	    $wp_icomply_snetworks=array();
	    foreach($wp_sn as $arr)
	    {
	        $arr = (array) $arr;
	        $wp_icomply_snetworks[$arr['id']] = $arr;
	    }
	    $wp_icomply_auth['social']=$wp_icomply_snetworks;
	    $wp_icomply_auth['userId'] = $response_array['userId'];
	    $_SESSION['wp_icomply_auth'] = $wp_icomply_auth;
	    return $wp_icomply_auth;
    }
    return false;
}


function set_icomply_post($wp_resturl,$data,$wp_token)
{
    $response = connect_icomply(
                    $wp_resturl."/multipost", 
                    json_encode($data),
                    "Basic ".$wp_token,
                    'POST'
                );
    return $response_array = (array) json_decode($response);
}


function connect_icomply($url,$data,$token='',$method='GET')
{
    $ch = curl_init();    
    
    if($method=='POST')
    {    
        curl_setopt($ch,CURLOPT_POST,count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }
    else
    {
        $fields_string = '';        
        foreach($data as $key=>$value){
            $fields_string[]=$key.'='.urlencode($value); 
        }
        $url = $url.'?'.implode('&amp;',$fields_string);
    }
    
    curl_setopt($ch,CURLOPT_URL,$url);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    
    $headers = array(
        'Authorization:'.$token,
        'Content-Type: application/json;'
    );
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if($token!='')
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

get_currentuserinfo();

function mc_encrypt($encrypt, $key){
	global $current_user;
	$key=$key.$current_user->ID;
	$encrypt = serialize($encrypt);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
	$key = pack('H*', $key);
	$mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
	$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
	$encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
	return $encoded;
}

function mc_decrypt($decrypt, $key){
	$decrypt = explode('|', $decrypt);
    if(count($decrypt)<=1)
        return;
	$decoded = base64_decode($decrypt[0]);
	$iv = base64_decode($decrypt[1]);
	$key = pack('H*', $key);
	$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
	$mac = substr($decrypted, -64);
	$decrypted = substr($decrypted, 0, -64);
	$calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
	if($calcmac!==$mac){ return false; }
	$decrypted = unserialize($decrypted);
	return $decrypted;
}
?>