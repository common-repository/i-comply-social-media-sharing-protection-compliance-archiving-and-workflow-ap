<?php

function ic_activate_callback()
{
		if($_POST['icomply_user']!='' && $_POST['icomply_pass']!='')
		{
			global $wpdb;
			include('connect.php');
			if(isset($_SESSION['wp_icomply_auth']))
			{
				unset($_SESSION['wp_icomply_auth']);
			}
			$table_name = $wpdb->prefix . "icomplydetails";
			$user_ID = get_current_user_id();
			$user = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_ID");
			$key=generateRandomKey();
			$ic_password = mc_encrypt($_POST['icomply_pass'],$key);	
			if($user)
				$wpdb->update( $table_name, array( 'icomply_username' => $_POST['icomply_user'],'icomply_password' =>$ic_password ,'icomply_key' => $key),array('userid'=>$user_ID));
			else
				$wpdb->insert( $table_name, array( 'icomply_username' =>$_POST['icomply_user'] ,'icomply_password' =>$ic_password,'icomply_key' => $key,'icomply_email' => '','userid' =>$user_ID  ) );
			
			$data = array('userName'=> $_POST['icomply_user'],'password'=>$_POST['icomply_pass']);
			
			
			$rest_url = $user->icomply_wp_resturl;
			$_SESSION['wp_resturl']=$rest_url;
			if($icomply_auth = get_icomply_authtoken($rest_url,$data))
			{
				echo 'done';
			}
			else 
				echo 'error';
		}
		elseif($_POST['icomply_email']!='')
		{
			global $wpdb;	
			if(isset($_SESSION['wp_resturl']))
			{
				unset($_SESSION['wp_resturl']);
				unset($_SESSION['wp_icomply_auth']);
			}
				
			$table_name = $wpdb->prefix . "icomplydetails";
			$user_ID = get_current_user_id();
			$user = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_ID");	
			include('connect.php');	
			if($rest_url=get_rest_url($_POST['icomply_email']))
			{
				if($user)
					$wpdb->update( $table_name, array( 'icomply_username' =>'' ,'icomply_password' =>'','icomply_email' => $_POST['icomply_email'],'icomply_wp_resturl'=>$rest_url),array('userid'=>$user_ID));
				else
					$wpdb->insert( $table_name, array( 'icomply_username' =>'', 'icomply_password' =>'','icomply_email' => $_POST['icomply_email'],'icomply_wp_resturl'=>$rest_url,'userid' =>$user_ID  ) );
				
				echo 'done';
			}
			else 
				echo 'error';
		}
		elseif($_POST['icomply_reset']!='')
		{
		    global $wpdb;
		    unset($_SESSION['wp_resturl']);
		    unset($_SESSION['wp_icomply_auth']);
		
		    $table_name = $wpdb->prefix . "icomplydetails";
		    $user_ID = get_current_user_id();
		    $wpdb->delete($table_name,array('userid'=>$user_ID));
		    echo 'done';
		}
		else 
			echo 'error';
		die();
}

function generateRandomKey($length = 32) {
		$characters = "0123456789abcdefABCDEF";
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
}
?>