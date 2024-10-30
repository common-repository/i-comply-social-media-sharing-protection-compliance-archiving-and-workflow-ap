<?php
/* Plugin Name: SocialSmart
 * Plugin URI: http://wordpress.org/plugins/socialsmart/
 * Description: Publish posts automatically from your blog to Social Smart site.
 * Author: Notetech
 * Version: 1.0.2
 */
?>
<?php 
register_activation_hook( __FILE__, 'icomply_setup' );
include_once ('socialsmart_setup.php');
include_once ('socialsmartapi.php');
register_deactivation_hook( __FILE__, 'icomply_uninstall' );
if(!session_id()) {
    session_start();
}

add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myStartSession');
add_action( 'wp_ajax_ic_activate', 'ic_activate_callback' );
add_action( 'wp_ajax_ic_settings', 'ic_settings_callback' );

function ic_settings_callback()
{
	include_once ('socialsmartsettings.php');
	die();
}
function myStartSession() {
    if(!session_id()) {
        session_start();
    }
	if ( is_user_logged_in() && !isset($_SESSION['wp_icomply_auth']) && !isset($_SESSION['wp_icomply_invalid'])) {
	    $user = wp_get_current_user();
	    ic_login($user->user_login,$user);
    }
}

function myEndSession() {
    session_destroy();
}

add_action('admin_init', 'my_plugin_redirect');

function my_plugin_redirect() {
    if (get_option('my_plugin_do_activation_redirect', false)) {
        delete_option('my_plugin_do_activation_redirect');
        if(!isset($_GET['activate-multi']))
        {
            wp_redirect("options-general.php?page=icomplysettings");
        }
    }
}

add_action( 'admin_enqueue_scripts', 'child_add_scripts' );
add_action( 'admin_print_footer_scripts', 'footer_js' );

/**
 * Register and enqueue a script that does not depend on a JavaScript library.
*/
function child_add_scripts() {
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_style ( 'jquery-ui-lightness', plugins_url('css/jquery-ui.css', __FILE__) );
    ?>
    <style>
    .ui-widget-overlay {background-color: #000;background-image:none!important;opacity: .5;filter: Alpha(Opacity=50);}
    .new-wp-dialog{z-index:300101!important;-webkit-box-shadow: 0 5px 15px rgba(0,0,0,0.7);box-shadow:0 5px 15px rgba(0,0,0,0.7);padding:0px!important;}
    .new-wp-dialog .ui-widget-header {background: #000;color: #fff;font-weight: normal;}</style>
    <?php
}

function footer_js() {
    ?>
    <script type="text/javascript">
    <?php global $post;
    if (get_post_type($post) == 'post' || $_REQUEST['action']=="ic_settings") {?>
    	jQuery(document).ready(function() {
    	jQuery("#publish" ).hide();
        jQuery( "#publishing-action .spinner" ).hide();        
          var dateToday = new Date();
          jQuery('.cdatepicker').datepicker(
                  {
                      minDate:dateToday,
                	  onSelect: function(date){

                		  jQuery("#ic_enddatepicker").datepicker("option","minDate", date)

                        }
                  });
          jQuery( "#dialog" ).dialog({
              autoOpen: false,
              resizable: false,
              width:330,
              modal: true,
              position: "center",
              'dialogClass'   : 'new-wp-dialog',
              close : function() {
        	  	  if(jQuery( "#icomply_draft" ).val() != "draft")
              	  {
            	  jQuery( "form#post").submit(); 
              	  }              	   	  		   	  		
              },
              buttons: {
                "Ok": function() {
        	  		jQuery("#icomply_draft").val("draft");
        	  		jQuery( this ).dialog( "close" );
        	  		if(jQuery("#icomply_message").length)
        	  			jQuery( "form#post #publish" ).click();
        	  		else
        	  			jQuery("#newpublish").click();
        	  		jQuery( "#publishing-action .spinner" ).hide();
        	  		return false;     
        	  	},
                "Cancel": function() {
                	jQuery("#icomply_draft").val("");
                	jQuery( this ).dialog( "close" );
                }
              }
            });
          jQuery( "form#post #save-post" ).after('<input type="hidden" name="icomply_draft" id="icomply_draft">'); 
          jQuery( "#save-post" ).click(function() {
              
        	  jQuery( this ).removeClass('button-disabled');
        	  if(jQuery( "#post_status" ).val() == "draft")
              {
            	  jQuery( "#dialog" ).dialog( "open" );
        	      return false;
              }
              else
                  return true;
          });

          jQuery('#ic_occurrence').blur(function() {
          	var ocrnce = jQuery('#ic_occurrence').val();
          	if (ocrnce != parseInt(ocrnce)) {
          		jQuery('#ic_occurrence').val('');
          		return false;
          	}
          	var startDate = jQuery('#ic_datepicker').val();
          	var actualDate = jQuery.datepicker.parseDate( "mm/dd/yy", startDate ); //new Date(startDate);
          	var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate());
          	var recur = jQuery('input[name=sched]:checked').val();
          	if(recur == "daily") {
          		newDate.setDate(actualDate.getDate() + ocrnce * 1);
          	} else if(recur == "weekly") {
          		newDate.setDate(actualDate.getDate() + (ocrnce * 7));
          	} else if(recur == "monthly") {
          		newDate.setMonth(actualDate.getMonth() + ocrnce * 1);
          	}
          	jQuery('#ic_enddatepicker').datepicker("setDate", newDate);
          });
    });
    
    
    function isNumber(event) {
        if (event) {
           var charCode = (event.which) ? event.which : event.keyCode;
           if (charCode != 190 && charCode > 31 && 
              (charCode < 48 || charCode > 57) && 
              (charCode < 96 || charCode > 105) && 
              (charCode < 37 || charCode > 40) && 
               charCode != 110 && charCode != 8 && charCode != 46 )
              return false;
        }
        return true;
    }
    
    function toggle_edit() {
        var publishon = document.getElementById("icomplypublishon");
        var linktext = document.getElementById("viewedit");
        if(linktext.innerHTML == "Done") {						         
	         jQuery("#icomplypublishon" ).hide('slow');
	         linktext.innerHTML = "Edit";					         
        }					         
        else {    					         
	         jQuery("#icomplypublishon" ).show('slow');
	         linktext.innerHTML = "Done";
	         var val=jQuery("form#post #title").val();				            
            if(jQuery("#icomply_message").val() == "")
       	 {
               var val=jQuery("form#post #title").val();        				                
               jQuery("#icomply_message").val(val);
       	 }
        }
    }
    
    function shedule() {
        jQuery("#viewpublish" ).hide('slow');
        jQuery("#icomplyschedule" ).show('slow');               
        } 
           
    function hide_shedule() {
        jQuery("#viewpublish" ).show('slow');
        jQuery("#icomplyschedule" ).hide('slow');                            
        var date = document.getElementById('ic_datepicker').value;
        var label = document.getElementById("immediately");
        if(date!='')
          label.innerHTML = "Scheduled"; 
        else
        	label.innerHTML = "immediately";                    
       }

    function checkhr(){
        var ele = document.getElementById('icomply_hr');

        if(parseInt(ele.value , 10) > 12)
        {
            ele.value ='';
            ele.style.borderColor="red";
            return false;
        }
        else
        {
        	ele.style.borderColor='#dddddd';
        		ele.value =('0' + ele.value).slice(-2);
        }
    }
    
    function checkmin(){
        var ele = document.getElementById('icomply_min');

        if(parseInt(ele.value , 10) > 59)
        {
            ele.value ='';
            ele.style.borderColor="red";
            return false;
        }
        else
        {
        	ele.style.borderColor='#dddddd';
        	ele.value =('0' + ele.value).slice(-2);
        }
    }           
    <?php }?>
    
    </script>
<?php if (get_post_type($post) == 'post') {?>    
<div id="dialog" title="Send draft to Social Smart?">
      <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Send draft to Social Smart for approval now?</p>
</div>
<?php 
    }
}

add_action( 'publish_post', 'notify_post' );

function notify_post($post_id) {
    global $wpdb;
    if( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) )
    {
        include('connect.php');
        $rest_url=get_rest_url();

        $data = array();
        $icomply_auth= get_icomply_authtoken($rest_url);

        $postid = get_the_ID();  
        $post_title = get_the_title($postid);
        $permalink  = get_permalink($postid);
         
        wp_update_post( array( 'ID' => $postid, 'post_status' => 'draft' ) );
        $msg=$_POST['icomply_message'];      
       
        if(trim($msg)=='')
            $msg=$post_title;
        
        $pos = strpos($msg, "{PERMALINK}");
        if ($pos === false)
        {
            $msg=$msg.' '.$permalink; 
        }
        $msg=str_replace("{PERMALINK}",$permalink,$msg);
        $msg=str_replace("{POST_TITLE}",$post_title,$msg);
        
        $_SESSION['ic_social']=$_POST['icomply_social'];
        $data = array(
            'userId'			=> $icomply_auth['userId'],
            'content'			=> $msg,
            'socialNetworkIds'	=> $_POST['icomply_social'],
            'wpBlogId'          => $postid,
        );
        if(($_POST['icomply_hr']!='') && ($_POST['icomply_min']!='') && ($_POST['ic_datepicker']!=''))
        {  
            $schedule= array(
                'timesched'  =>  $_POST['icomply_hr'].":".$_POST['icomply_min']." ".$_POST['icomply_period'],
                'datesched'  =>  $_POST['ic_datepicker'],
                'recurrence' =>  $_POST['sched'],
                );
             
             if($_POST['sched']!="none")
             {   
                 $nonrepeat= array(                   
                    'dateuntil'  =>  $_POST['ic_enddatepicker']
                ); 
               $schedule = array_merge((array)$schedule, (array)$nonrepeat);
             }
            
            $data = array_merge((array)$data, (array)$schedule);
        }
        $response = set_icomply_post($rest_url,$data,$icomply_auth['token'],'POST');   
         
    }
}

// metabox
add_action( 'post_submitbox_misc_actions', 'icomply_meta_box' );

function icomply_meta_box() {
    global $post;
    if (get_post_type($post) == 'post') {
        echo '<div class="misc-pub-section misc-pub-section-last" style="border-top: 1px solid #eee;">';
        wp_nonce_field( plugin_basename(__FILE__), 'icomply_meta_box_nonce' );
        ?>
        <div>
        	<img src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/social_smart.png';?>" height="25" />
       			<?php 
        			global $wpdb;
        			$user_ID = get_current_user_id();
        			$table_name = $wpdb->prefix . "icomplydetails";
        			$user = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_ID");        
        			$url= admin_url( 'options-general.php?page=socialsmartsettings' , 'http' );        
        			if($user &&  $user->icomply_username!='')
        			{        			   
        			   ?><span style="vertical-align: top; padding-left: 10px; line-height: 2em;float:right">
        			   <a id="displayText" href="<?php echo $url?>">Settings</a>
							<?php if($user &&  isset($_SESSION['wp_icomply_auth']))
    		{?> | <a id="viewedit"  href="javascript:toggle_edit();" >Edit</a><?php }?></span>         			        
					            	    
        		    <?php 
        			}
        			else
        			{
        				 echo '<span style="vertical-align: top;font-weight:bolder; padding-left: 4px; line-height: 2em;">';
        			     echo '<br/>Set Your Social Smart details&nbsp;</span><a id="displayText" style="float:right;padding-top:4px;" href="'.$url.'">Click</a>';
        			    
        			}
        			?>
			</div>
    <div align="center">
    	<table width="100%">
<?php 
    		if($user &&  $user->icomply_username!='')
    		{
    		    ?>
    		<tr>
    			<td align="left"><b>Username: </b><font style="font-weight:normal"><?php echo $user->icomply_username ?></font></td>
    		</tr>
    		<?php 
    		}
    		if($user &&  $user->icomply_email!='')
    		{
    		    ?>
    
    		<tr>
    			<td align="left" ><b>Email: </b><font style="font-weight:normal"><?php echo $user->icomply_email;?></font></td>
    		</tr>    		
    		<?php 
    		}
    		?>
    		<tr style="display: none;" id="icomplypublishon"><td>
    		<?php
    		
    		if($user &&  isset($_SESSION['wp_icomply_auth']))
    		{ 
    		    ?>    
	    		<table width="100%" cellpadding="0" cellspacing="0">    		    
	    		<tr>
	                <td>
	                    <table width="100%" cellpadding="0" cellspacing="0">
	                             <tr align="left"><td valign="middle" width="90%"><b>Custom Message</b></td><td><img  src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/blue_question_mark.jpg'; ?>" title="{POST_TITLE}=>Title of post&#013;{PERMALINK}=>Permalink to your post"  onclick="alert(' {POST_TITLE}=>Title of post \n {PERMALINK}=>Permalink to your post');"  /></td> </tr>
	                             <tr><td colspan="2" ><textarea id="icomply_message" name="icomply_message" rows="3" cols="28" style="width:100%;font-size:12px;font-weight:normal"></textarea></td></tr>
	                    
	                    </table>
	                </td>
	            </tr>   
	           <tr>             
                    <td  height="30">
                           Publish <label id="immediately"><b> Immediately</b> </label>  <a id="viewpublish" href="javascript:shedule();"> Edit</a> 
                     </td>
                </tr>  
                <tr id="icomplyschedule"  style="display: none;"> 
                    <td>
                        <table style="border:1px solid Gainsboro;width:100%;font-weight:normal "  align="center"  >
                            <tr >
                                 <td style="font-size:15px; " > <b>Schedule</b> </td>
                                             
                            </tr>
                            <tr>
                                <td >
                                 <table style="padding-left:10px;" align="center">
                                 <tr>
                                 <td>  Time: <input id="icomply_hr" type="text" autocomplete="off" maxlength="2" size="1"  name="icomply_hr" style="height:20px;" onkeydown="return isNumber(event);" onchange="javascript:checkhr();"/> : <input id="icomply_min" type="text" autocomplete="off" maxlength="2" size="1"  name="icomply_min" style="height:20px;" onkeydown="return isNumber(event);" onchange="javascript:checkmin();"/>
                                   <select name="icomply_period" id="icomply_period" style="height:20px;font-size:12px;padding-top:0px;" >
                                     <option value="AM">AM</option>
                                     <option value="PM" >PM</option>
                                   </select>
                                </td>                                             
                            </tr>
                            <tr>
                                <td>Date: <input type="text" id="ic_datepicker" class="cdatepicker" size="8" style="height:20px;" name="ic_datepicker" readonly></td>                                             
                            </tr>
                            <tr align="center"> 
                                 <td>
                                     <table>
                                          <tr>
                                              <td>                       
                                                 <input type="radio" name="sched" id="sched" value="none" checked>No repeat<br/>
                                                 <input type="radio" name="sched" id="sched" value="daily">Daily
                                              </td>
                                              <td>           
                                                 <input type="radio" name="sched" id="sched" value="weekly">Weekly <br/>
                                                 <input type="radio" name="sched" id="sched" value="monthly">Monthly
                                              </td>
                                          </tr>
                                     </table>
                                 </td>
                            </tr>
                            <tr>
                                <td>End Date: <input type="text" id="ic_enddatepicker"  class="cdatepicker" name="ic_enddatepicker" width="100" size="8" style="height:20px;" readonly></td>                                             
                            </tr>
                            <tr>
                                <td>End after occurrence: <input type="text" id="ic_occurrence" name="ic_occurrence" onkeydown="return isNumber(event);" size="2" style="height:20px;" />
                                </td> 
                          </tr>
                          </table>
                                </td>
                                </tr>
                          <tr>
                             <td align="right">
                                  <a id="hidepublish" href="javascript:hide_shedule();"> Done</a>
                             </td>
                          </tr>                          
                    </table>  
                </td>  
             </tr>
	    		<tr>
	    			<td align="left">
	    			<b>Publish On :</b> </td>
	    		</tr>
	    		<script>
	    		jQuery(document).ready(function() {
	    			jQuery('#ic_selectall').click(function(event) {  
	    		        if(this.checked) { 
	    		        	jQuery('.icomplycheckb').each(function() { 
	    		              this.checked = true;	    		             
	    		                                
	    		            });
	    		        }else{
	    		        	jQuery('.icomplycheckb').each(function() { 
	    		              this.checked = false;
	    		                               
	    		            });        
	    		        }
	    		    });
	    		   
	    		});
	    		</script>
	    		<tr>	    		
	    			<td><table width="100%">
	    			   <tr><td><input type="checkbox" name="ic_selectall" id="ic_selectall" /></td><td colspan="2"><label id="ic_select_label">select all </label></td></tr>
	    			<?php foreach($_SESSION['wp_icomply_auth']['social'] as $id=>$listof){?>
	    					<tr  align="left">
	    					    
	    						<td><input type="checkbox" class="icomplycheckb" name="icomply_social[]" <?php echo (isset($_SESSION['ic_social']) && in_array($listof['id'],$_SESSION['ic_social'])? 'checked=true': (isset($_SESSION['ic_social'])? '' : 'checked')); ?>  value="<?php echo $listof['id']?>" /></td>
	    							<td><img src="<?php echo (((strpos($listof['pictureUrl'],"https://")!==FALSE) || (strpos($listof['pictureUrl'],"http://")!==FALSE))?$listof['pictureUrl']:'https://icomply02.isocialsmart.com/icomply-web/'.$listof['pictureUrl']);?>" width="28" height="28"/>
								</td>
	    						<td width="100%"><?php echo $listof['name']?></td>
	    						<td><img src="https://icomply02.isocialsmart.com/icomply-web/img/<?php echo strtolower($listof['networkType']);?>-16.png"/></td>
	    					</tr>
	    			<?php } ?>		
	    				</table></td>
	    		</tr>
	    		</table>
    		<?php 
    		}
    		?>
           </td>
    		</tr>
    	</table>    	
    </div>
    <?php 
    if($user)
    { ?>
    <div style="text-align:right;"><a href="http://icomply02.isocialsmart.com/icomply-web/queue" style="font-weight:normal;font-size:12px;" target="_blank">Open Social Smart</a></div>
<?php 
    }
    echo '</div>';
    }
}

add_action('admin_footer', 'sb_post_validation');
function sb_post_validation() {
	global $post;
    if (get_post_type($post) == 'post' && ( basename($_SERVER['PHP_SELF']) == 'post-new.php' || basename($_SERVER['PHP_SELF'])=='post.php'))
    {       
        global $wpdb;
        $user_ID = get_current_user_id();
        $table_name = $wpdb->prefix . "icomplydetails";
        $user = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_ID");

        if(!$user || !isset($_SESSION['wp_icomply_auth']) || $_SESSION['wp_icomply_auth']=='')
        {
            $url= plugins_url( 'socialsmartsettings.php' , __FILE__ ) ;
            ?>
             <script type="text/javascript">
                 var urlpath = "<?php echo add_query_arg( array(
    'action' => 'ic_settings'
  ), 'admin-ajax.php'); ?>";
                 jQuery( "form#post #publish" ).hide();
                 jQuery( "form#post #publish" ).after("<a id='newpublish' href="+urlpath+"&TB_iframe=true&width=1000&height=650' class='thickbox'> <input type='button' value='Publish' class='sb_publish button-primary' style='display:none;'/> <span class='sb_js_errors'> </span></a>"); 
             </script>
             
<?php 
        }
    }
    else
        return;

}

add_action('admin_menu', 'register_icomplysetting_submenu_page');

function register_icomplysetting_submenu_page() {
    add_submenu_page( 'options-general.php', 'Social Smart Settings', 'Social Smart Settings', 'manage_options', 'socialsmartsettings', 'icomplysetting_submenu_page_callback' );
}

function icomplysetting_submenu_page_callback() {
        include_once ('socialsmartsettings.php');

}


function ic_login($user_login,$user) {
	global $wpdb;
	include('connect.php');	
	if(isset($_SESSION['wp_icomply_auth']))
	{
		unset($_SESSION['wp_icomply_auth']);
	}
	$table_name = $wpdb->prefix . "icomplydetails";
	$user_ID = $user->id;
	$ic_user = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_ID");
	$ic_depassword= mc_decrypt($ic_user->icomply_password,$ic_user->icomply_key.$user_ID );	
	if($ic_user)
	{
		$data = array('userName'=> $ic_user->icomply_username,'password'=>$ic_depassword);		
		
		$rest_url = $ic_user->icomply_wp_resturl;
		$_SESSION['wp_resturl']=$rest_url;
		
		if($icomply_auth = get_icomply_authtoken($rest_url,$data))
		{
			$_SESSION['wp_icomply_auth']=$icomply_auth;			
		}	
  	}
  	else {
  		$_SESSION['wp_icomply_invalid']=true;
  	}
}
add_action('wp_login', 'ic_login',10,2);
?>