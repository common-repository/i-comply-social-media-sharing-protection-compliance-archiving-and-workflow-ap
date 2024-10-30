<?php

$blockhtmlwidth='50'; 
if ($_REQUEST['action']=="ic_settings")
{
	$blockhtmlwidth='95';	
	global $hook_suffix;
	_wp_admin_html_begin();
	wp_enqueue_style( 'colors' );
	wp_enqueue_style( 'ie' );
	$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);
	wp_enqueue_script('utils');
	wp_enqueue_script( 'svg-painter' );

	?>
    <script type="text/javascript">
    addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
    </script>
    <?php
    do_action( 'admin_enqueue_scripts');
    do_action( 'admin_print_footer_scripts');
    
    
    do_action( "admin_print_scripts-$hook_suffix" );
    do_action( 'admin_print_scripts' );
    do_action( "admin_print_styles-$hook_suffix" );
    
    
    /**
     * Print styles for all admin pages.
     *
     * @since 2.6.0
     */
    do_action( 'admin_print_styles' );
    if ( get_user_setting('mfold') == 'f' )
        $admin_body_class .= ' folded';
    
    if ( !get_user_setting('unfold') )
        $admin_body_class .= ' auto-fold';
    
    if ( is_admin_bar_showing() )
        $admin_body_class .= ' admin-bar';
    
    if ( is_rtl() )
        $admin_body_class .= ' rtl';
    
    if ( $current_screen->post_type )
        $admin_body_class .= ' post-type-' . $current_screen->post_type;
    
    if ( $current_screen->taxonomy )
        $admin_body_class .= ' taxonomy-' . $current_screen->taxonomy;
    
    $admin_body_class .= ' branch-' . str_replace( array( '.', ',' ), '-', floatval( $wp_version ) );
    $admin_body_class .= ' version-' . str_replace( '.', '-', preg_replace( '/^([.0-9]+).*/', '$1', $wp_version ) );
    $admin_body_class .= ' admin-color-' . sanitize_html_class( get_user_option( 'admin_color' ), 'fresh' );
    $admin_body_class .= ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );
    
    if ( wp_is_mobile() )
        $admin_body_class .= ' mobile';
    
    if ( is_multisite() )
        $admin_body_class .= ' multisite';
    
    if ( is_network_admin() )
        $admin_body_class .= ' network-admin';
    
    $admin_body_class .= ' no-customize-support no-svg';

?>
</head>
<body
	class="wp-admin wp-core-ui no-js <?php echo apply_filters( 'admin_body_class', '' ) . " $admin_body_class"; ?>">
	<style>.button{padding:5px 20px!important} input{padding:0px 5px!important;font-size:13px} input[type=radio]{margin:2px!important;}</style>
<?php }?>
<?php
global $wpdb;
$user_ID = get_current_user_id();
$table_name = $wpdb->prefix . "icomplydetails";
$user = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_ID"); ?>
<div id="main">
		<div align="center">
			<img
				src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/social_smart.png';?>"
				height="50" />
		</div>
		<span id="icomplyLogo"></span>
		<div id="icomplyWelcome" align="center">
			<b>Welcome to Social Smart !</b>
		</div>
		<div class="menu" style="padding-bottom: 5%; text-align: center;">Thank
			you for downloading the Social Smart Wordpress plugin. Please select from
			the following options.</div>
		<div
			class="menu" id="icomplymessage"
			style="padding-bottom: 5px; text-align: center;"></div>
		<div id="settingDiv" align="center" style="display: block;">
			<TABLE CELLPADDING="15" align="center">
				<tbody>
					<TR bgcolor="#EBFAFF">
						<TD>I already have an Social Smart account. activate my plugin now.</TD>
						<TD ALIGN="CENTER"><input id="activatePluginButton" type="button"
							value="Activate Plugin Now" class=" button   button-primary "
							onclick="javascript:activation()" /></TD>
					</TR>
					<TR bgcolor="#EBFAFF">
						<TD>Setup my wordpress in Social Smart now.</TD>
						<TD ALIGN="CENTER"><a href="http://icomply02.isocialsmart.com/icomply-web/wordpress" target="_blank"><input id="signUpForiComplyButton" type="button" value="Setup wordpress in Social Smart" class="button button-primary" /></a>
						</TD>
					</TR>
					<TR bgcolor="#EBFAFF">
						<TD>I do not currently have an Social Smart account. Sign up for
							Social Smart now.</TD>
						<TD ALIGN="CENTER"><a href="http://isocialsmart.com/trial-2/" target="_blank"><input id="signUpForiComplyButton" type="button" value="Sign up for Social Smart" class="button button-primary" /></a>
						</TD>
					</TR>					
					<TR bgcolor="#EBFAFF">
						<TD>I need more information about Social Smart before I Sign up</TD>
						<TD ALIGN="CENTER"><a href="http://isocialsmart.com/contact-us/" target="_blank"><input id="contactUsButton" type="button"
							value="Contact Us" class=" thickbox button button-primary" /></a> <a href="http://isocialsmart.com/help/" target="_blank"><input
							id="helpButton" type="button" value="Help"
							class="button button-primary " /></a></TD>
					</TR>
				</tbody>
			</TABLE>
		</div>
		<div id="activationDiv" style="display: none;">
			<TABLE CELLPADDING="5" align="center"
				width="<?php echo $blockhtmlwidth;?>%">
				<tbody>
					<TR bgcolor="#EBFAFF">
						<TD align="right"><label for="email" style="padding-right: 10px">Activation
								Email<span style="color: red">*</span>
						</label></TD>
						<TD align="left"><input id="icomplyactivationemail"
							name="icomplyactivationemail" type="email" size="32"
							placeholder="Activation Email"
							VALUE="<?php echo  $user->icomply_email ?>" required /></TD>
					</TR>
					<TR align="center" bgcolor="#EBFAFF">
						<TD align="center" colspan="2"><input id="activationButton"
							type="button" value="Activate" class="button button-primary"
							onclick="javascript:do_activation()" /> <input id="cancelButtonA"
							type="button" value="Cancel" class="button "  onclick="do_cancel('activation')" /></TD>
						</div>
					</TR>
					<TR align="left" bgcolor="#EBFAFF">
						<TD colspan="2"><b></b>&lt;&lt; <a href="#" id="backButtonA"
							onclick="javascript:backsetting()">Go Back </a></TD>
					</TR>
				</tbody>
			</TABLE>
		</div>
		<div id="loginDiv" style="display: none;" align="center">
			<TABLE CELLPADDING="5" align="center"
				width="<?php echo $blockhtmlwidth;?>%">
				<tbody>
					<TR bgcolor="#EBFAFF">
						<TD align="right"><label for="username" style="padding-right: 10px">Username<span style="color: red">*</span></label>
						</TD>
						<TD align="left"><input id="icomplyusername" name="icomplyusername" type="text" size="32" placeholder="Username"
							VALUE="<?php echo  $user->icomply_username ?>" required />
						</TD>
					</TR>
					<TR bgcolor="#EBFAFF">
						<TD align="right"><label for="password"
							style="padding-right: 10px">Password<span style="color: red">*</span>
						</label></TD>
						<TD align="left"><input id="icomplypass" name="icomplypass"
							type="password" size="32" placeholder="Password"
							autocomplete="off" required /></TD>
						</div>
					</TR>
					<TR align="center" bgcolor="#EBFAFF">
						<TD colspan="2"><input id="loginButton" type="button"
							value="Login" class="button button-primary"
							onclick="do_authentication()" /> <input id="cancelButtonL"
							type="button" value="Cancel" onclick="do_cancel('authentication')" class="button " /></TD>
					</TR>
					<TR align="left" bgcolor="#EBFAFF">
						<TD><a href="#" id="resetButtonL"> </span> <span id="resetText"
								onclick="javascript:do_reset();">Reset</span>
						</a></TD>
						<TD align="right"><a href="#" id="resetButtonL"><a href="http://icomply02.isocialsmart.com/icomply-web/wordpress" style="font-weight:normal;font-size:12px;" target="_blank">Setup Wordpress in Social Smart</a></TD>
					</TR>
				</tbody>
			</TABLE>
		</div>
		<div id="detailsDiv" style="display: none;" align="center">
			<TABLE CELLPADDING="0" align="center" 
				width="<?php echo $blockhtmlwidth;?>%">
				<tbody>					
					<?php
            			if ($blockhtmlwidth!='50')
            			{
            			?>
					<tr ><td colspan="2" align="center"><table width="100%" bgcolor="#fff"  CELLPADDING="3">
					<tr><td><b>Username : </b><?php echo  $user->icomply_username ?></td></tr>
					<tr><td><b>Email : </b><?php echo $user->icomply_email?></td></tr>
					<tr><td id="publishon" >
	    		<table width="100%" cellpadding="0" cellspacing="0">    		    
	    		<tr>
	                <td>
	                    <table width="100%" cellpadding="0" cellspacing="0">
	                             <tr align="left"><td valign="middle" width="45%"><b>Custom Message</b><td><img  src="<?php echo plugins_url('social_smart', 'social_smart' ).'/blue_question_mark.jpg'; ?>" title="{POST_TITLE}=>Title of post&#013;{PERMALINK}=>Permalink to your post"  onclick="alert(' {POST_TITLE}=>Title of post \n {PERMALINK}=>Permalink to your post');"  /></td> </tr>
	                             <tr><td colspan="2" ><textarea id="icomply_message" name="icomply_message" rows="3" cols="28" style="width:100%;font-size:12px;font-weight:normal"></textarea></td></tr>
	                    
	                    </table>
	                </td>
	            </tr> 
	            <tr>             
                    <td  height="30">
                           Publish <label id="immediately"><b> Immediately</b> </label>  <a id="viewpublish" href="javascript:shedule();"> Edit</a>
                           <script type="text/javascript" language="javascript">// <![CDATA[
                            
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
                            // ]]></script>              
                            
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
                                 <td>  Time: <input id="icomply_hr" type="text" autocomplete="off" maxlength="2" size="1"  name="icomply_hr" style="height:20px;" onkeydown="return isNumber(event);" onchange="javascript:checkhr();"></input> : <input id="icomply_min" type="text" autocomplete="off" maxlength="2" size="1"  name="icomply_min" style="height:20px;" onkeydown="return isNumber(event);" onchange="javascript:checkmin();"></input>
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
                                                 <input type="radio" class="icomplycheckb icomplyradiob" name="sched" id="sched" value="none" checked>No repeat</br>
                                                 <input type="radio" class="icomplycheckb icomplyradiob" name="sched" id="sched" value="daily">Daily
                                              </td>
                                              <td>           
                                                 <input type="radio" class="icomplycheckb icomplyradiob" name="sched" id="sched" value="weekly">Weekly </br>
                                                 <input type="radio" class="icomplycheckb icomplyradiob" name="sched" id="sched" value="monthly">Monthly
                                              </td>
                                          </tr>
                                     </table>
                                 </td>
                            </tr>
                            <tr>
                                <td>End Date: <input type="text" id="ic_enddatepicker"  class="cdatepicker" name="ic_enddatepicker" width="100" size="8" style="height:20px;" readonly></td>                                             
                            </tr>
                            <tr>
                                <td>End after occurrence: <input type="text" id="ic_occurrence" name="ic_occurrence" onkeydown="return isNumber(event);" size="2" style="height:20px;"/>
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
             <script>
	    		jQuery(document).ready(function() {
	    			jQuery('#ic_selectall').click(function(event) {  
	    		        if(this.checked) { 
	    		        	jQuery('.icomplycheckbtable .icomplycheckb').each(function() { 
		    		        		
	    		              this.checked = true;    		             
	    		            });
	    		        }else{
	    		        	jQuery('.icomplycheckbtable .icomplycheckb').each(function() { 
	    		              this.checked = false;
	    		                               
	    		            });        
	    		        }
	    		    });
	    		   
	    		});
	    		</script>
	    	 <tr>	    		
	    			<td align="left">
	    			<b>Publish On :</b> </td>
	    		</tr>
	    		<tr>
	    			<td><table width="100%" class="icomplycheckbtable">
	    				<tr><td><input type="checkbox" name="ic_selectall" id="ic_selectall" /></td><td colspan="2"><label id="ic_select_label">select all </label></td></tr>
	    				
	    			<?php foreach($_SESSION['wp_icomply_auth']['social'] as $id=>$listof){?>
	    					<tr  align="left">
	    						<td><input type="checkbox" class="icomplycheckb" name="icomply_social[]"  value="<?php echo $listof['id']?>" <?php echo (isset($_SESSION['ic_social'])? '' : 'checked');?>/></td>
	    							<td><img src="<?php echo (((strpos($listof['pictureUrl'],"https://")!==FALSE) || (strpos($listof['pictureUrl'],"http://")!==FALSE))?$listof['pictureUrl']:'https://icomply02.isocialsmart.com/icomply-web/'.$listof['pictureUrl']);?>" width="28" height="28"/>
								</td>
	    						<td width="100%"><?php echo $listof['name']?></td>
	    						<td><img src="https://icomply02.isocialsmart.com/icomply-web/img/<?php echo strtolower($listof['networkType']);?>-16.png"/></td>
	    					</tr>
	    			<?php } ?>		
	    				</table></td>
	    		</tr>
	    		</table></td>
    		</tr>
    		<TR align="center" bgcolor="#fff">
						<TD colspan="2"><input type="button"
							value="Post To Social Smart" class="button button-primary"
							onclick="do_publish()" /></TD>
					</TR>
    		</table></td></tr>    		
					<?php }else{ ?>
					<TR bgcolor="#fff">
						<TD align="right"><label for="username"
							style="padding-right: 10px">Username</label></TD>
						<TD align="left"><label for="username" style="padding-right: 10px"
							id="set_ic-user"><?php echo  $user->icomply_username ?> </label>
					
					</TR>
					<TR bgcolor="#fff">
						<TD align="right"><label for="email" style="padding-right: 10px">Email</label>
						</TD>
						<TD align="left"><label for="email" style="padding-right: 10px"
							id="set_ic-email"><?php echo $user->icomply_email?> </label></TD>
						</div>
					</TR>

					<TR align="left" bgcolor="#fff">
						<TD><a href="#" id="resetButtonL">  <span id="resetText"
								onclick="javascript:do_reset()">Reset</span>
						</a></TD>
						<TD align="right"><a href="#" id="resetButtonL"><a href="http://icomply02.isocialsmart.com/icomply-web/wordpress" style="font-weight:normal;font-size:12px;" target="_blank">Setup Wordpress in Social Smart</a></TD>
					</TR>
					<?php } ?>
				</tbody>
			</TABLE>
			<br>
			 <script type="text/javascript" language="javascript">// <![CDATA[
					         function toggle_read() {
					         var read = document.getElementById("read");
					         var readlink = document.getElementById("viewread");
					         if(read.style.display == "block") {
					        	 read.style.display = "none";
					        	 readlink.innerHTML = "Read on!";
					        	 }
					        	 else {
					        		 read.style.display = "block";
					        		 readlink.innerHTML = "Hide";
					        	 }
					         }
					         // ]]></script>    
			<TABLE CELLPADDING="3" align="center" bgcolor="#fff"
				width="<?php echo $blockhtmlwidth;?>%">
				<tbody>
					<TR>
						<TD align="center"><h2>How Social Smart Plugin works</h2></TD>
					</TR>
					<TR>
						<TD align="center"><p>This guide provides instructions for how to connect your WordPress blog to Social Smart social networking services. Once connected to a service, all of your WordPress posts will be shared with that service automatically.</p></TD>
					</TR>
					<TR>
						<TD align="left">Need more information or having trouble with connecting to a particular service?<br/></TD>
					</TR>
					<TR>
					    <TD align="right"><a id="viewread"  href="javascript:toggle_read();" >Read on!</a>
					    </TD>
					</TR>
					<TR>
						<TABLE id="read" style="display: none;" CELLPADDING="3" align="center" bgcolor="#fff" width="<?php echo $blockhtmlwidth;?>%">
    					<TR >
    						<TD align="left"><h4>1) Using Social Smart</h4>
    						<p>Once you activated Social Smart plugin, you'll see an Social Smart section in the Publish box on your post writing screen each time you write a new post.<br><img src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/screens/settings1.png';?>" width="240" /> <img src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/screens/settings2.png';?>"  width="240" /></p>
    						<p>When you publish your post as usual, you'll see it show up on the services you've enabled. If you want to opt out from any of the services for a specific post, click the <a>"Edit"</a> link next to the Social Smart as shown above.
    					You can then uncheck whichever services you don't want your post to appear on. You can also customize the message that introduces your post on your services.</p></TD>
    					</TR>
    					<TR >
    						<TD align="left"><img src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/screens/published.png';?>"  width="500" /></TD>
    					</TR>
    					<TR >
    						<TD align="left"><h4>2) Social Smart Settings</h4>
    						<p>To set up Social Smart on your WordPress site, please go to Settings -> Social Smart Settings in your site Dashboard.</p>
    						<img src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/screens/settings.png';?>" /></TD>
    					</TR>
    					<TR >
    						<TD align="left"><h4>3) Wordpress Settings in Social Smart </h4>
    						<p>To publish our post using Social Smart,we need to enter authentication details of Wordpress in Social Smart site. To Setup Wordpress in Social Smart, click on <a href="http://icomply02.isocialsmart.com/icomply-web/wordpress" target="_blank">this link </a></p>
    						<img src="<?php echo plugins_url( 'social_smart', 'social_smart' ).'/screens/setup.png';?>" width="500" /></TD>
    					</TR>
						</TABLE>			
				</tbody>
			</TABLE>
		</div>
</div>
<script type="text/javascript" language="javascript">// <![CDATA[
     function activation() {
             document.getElementById('activationDiv').style.display = "block";
             document.getElementById('settingDiv').style.display = "none";
             document.getElementById('loginDiv').style.display = "none";
             document.getElementById('detailsDiv').style.display = "none";    
     }
     
     function backsetting() {
           	document.getElementById('activationDiv').style.display = "none";
          	document.getElementById('settingDiv').style.display = "block";
          	document.getElementById('loginDiv').style.display = "none";
          	document.getElementById('detailsDiv').style.display = "none";
	 }
     
    function login() {
          	document.getElementById('activationDiv').style.display = "none";
          	document.getElementById('settingDiv').style.display = "none";
          	document.getElementById('loginDiv').style.display = "block";
          	document.getElementById('detailsDiv').style.display = "none";

	}
	
    function details() {
          	document.getElementById('activationDiv').style.display = "none";
          	document.getElementById('settingDiv').style.display = "none";
          	document.getElementById('loginDiv').style.display = "none";
          	document.getElementById('detailsDiv').style.display = "block";

	}
	
    function do_cancel(canceltype)
    {
		if(canceltype == 'activation')
		{
			document.getElementById('icomplyactivationemail').value='';
        }
		else if(canceltype == 'authentication')
		{
			document.getElementById('icomplyusername').value='';
			document.getElementById('icomplypass').value='';
		}
		return false;
    }
    
    function do_activation()
    {
		if(document.getElementById('icomplyactivationemail').value=='')
		{
			jQuery('#icomplymessage').css('color','red');
            jQuery('#icomplymessage').html('Please enter required fields!!');
            return false;
		}
    	var data = {  'icomply_email': document.getElementById('icomplyactivationemail').value };
    	do_ajax(data,'activation');
    }
    
    function do_authentication()
    {
    	if(document.getElementById('icomplyusername').value=='' || document.getElementById('icomplypass').value=='')
		{
			jQuery('#icomplymessage').css('color','red');
            jQuery('#icomplymessage').html('Please enter required fields!!');
            return false;
		}

		var data = {
            		'icomply_user'	: document.getElementById('icomplyusername').value,
            		'icomply_pass'	: document.getElementById('icomplypass').value,
            	   };
    	do_ajax(data,'authentication');
    }

    function do_reset(){
    	var data = {
        		     'icomply_reset'	: 'yes'
        	       };
    	do_ajax(data,'data reset');
    }
    
    function do_publish()
    {
    	window.parent.jQuery( "#icomplypublishon" ).html(jQuery( "#publishon" ).html());
    	window.parent.jQuery( "#icomplypublishon #icomply_message" ).val(jQuery( "#icomply_message" ).val());
    	window.parent.jQuery( "#icomplypublishon #icomply_hr" ).val(jQuery( "#icomply_hr" ).val());
    	window.parent.jQuery( "#icomplypublishon #icomply_min" ).val(jQuery( "#icomply_min" ).val());
    	window.parent.jQuery( "#icomplypublishon #icomply_period" ).val(jQuery( "#icomply_period" ).val());
    	window.parent.jQuery( "#icomplypublishon #ic_datepicker" ).val(jQuery( "#ic_datepicker" ).val());    	
    	window.parent.jQuery( "#icomplypublishon #ic_enddatepicker" ).val(jQuery( "#ic_enddatepicker" ).val());
    	window.parent.jQuery( "#icomplypublishon #ic_occurrence" ).val(jQuery( "#ic_occurrence" ).val());    	
    	window.parent.jQuery( ".icomplycheckb" ).attr("checked",false);
    	jQuery('.icomplycheckb').each(
    	    	function(){ 
        	    	if (jQuery(this).is(":checked")){
        	    		newval = jQuery(this).val();
        	    		window.parent.jQuery('.icomplycheckb').each(
        	        	    	function(){ 
        	            	    	if (window.parent.jQuery(this).val()==newval){
        	            	    		window.parent.jQuery(this).attr("checked",true);
        	            	    	}}
        	        	    	);        	    		
        	    	}}
    	    	);
    	window.parent.jQuery( "form#post #publish" ).click();
    }
    
	function do_ajax(data,posttype)
	{
		data['action']='ic_activate';
		jQuery.ajax({
			type   : "POST",
			url    : "<?php echo admin_url( 'admin-ajax.php' );?>",			
			data   : data,
			success: function (response) {
  				if(response === "done") {
	            	jQuery('#icomplymessage').css('color','green');
	                jQuery('#icomplymessage').html('Social Smart '+posttype+' - Success!!');
	                if(posttype=='activation')
	                {
	                	login();
	                }
	                else if(posttype=='authentication') {
  		            	<?php
            			if ($blockhtmlwidth!='50')
            			{
            			?>   
            			location.reload();
                    <?php }else{?>
                    details();
                    jQuery('#set_ic-user').html(document.getElementById('icomplyusername').value);
                    jQuery('#set_ic-email').html(document.getElementById('icomplyactivationemail').value);
                    <?php }?>                                                                
                    }
	                else if(posttype=='data reset')
	                {
	                	backsetting();
	                	do_cancel('activation');
	                	do_cancel('authentication');
	                }
                    
	            } else if(response === "error") {
	            	jQuery('#icomplymessage').css('color','red');
	            	jQuery('#icomplymessage').html('Social Smart '+posttype+' - Failed!!');
	            }
	        }
		});  			  	  		
	}
  		<?php 
  		global $wpdb;
        $user_ID = get_current_user_id();
        $table_name = $wpdb->prefix . "icomplydetails";
        $user = $wpdb->get_row("SELECT * FROM $table_name WHERE userid = $user_ID");
  		 if($user &&  $_SESSION['wp_icomply_auth']!='')
  		{?> details();
      	<?php }
      	if($user && $_SESSION['wp_icomply_auth']=='')
      	{
      	?>login();
      	<?php }?>
        // ]]></script>
<?php
if ($blockhtmlwidth!='50')
{
?>
<script>
jQuery(document).ready(function() {
 jQuery( "#icomply_message" ).val(window.parent.jQuery("form#post #title").val());   				                
    
});
</script>
</body>
<?php }?>
