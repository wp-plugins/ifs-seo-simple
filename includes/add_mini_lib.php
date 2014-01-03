<?php

defined( '_VALID_ADD' ) or die( 'Restricted access' );

/*	Copyright by Active Discovery Designs, Inc., 2010-2013
**
**	This library file will hold functions also useful for non-framework php sites
**	It will be standard included in add_main_lib.php.
**
*/

if (!defined('_ADD_MINI_LIB')) {

define('_ADD_MINI_LIB',1);

function displayGooglePlusOne($size="standard") { //standard, small, medium, tall

	global $frameworkVersion;

	?>
	<script type="text/javascript">
	  (function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
	<?php
	echo '<div class="g-plusone">';
	if ($frameworkVersion) {
		if ($frameworkVersion<3.2) { // Does not validate
			echo 'data-size="'.$size.'" data-count="true">';
		}
	}
	echo '</div>';
}
/* $lay-out values: standard,button_count,box_count*/

function displayFacebookLike($layout='standard',$url='',$showFaces='false',$showSend='false',$width='450') {

	global $canonical;
	
	/*
	if (!$url) {
		$url=$canonical;
	}
	*/

	?>
	<div id="fb-root"></div>
	<script type="text/javascript">
		(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) {return;}
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>	
	<?php
		if ($_SERVER['HTTP_USER_AGENT']!="W3C_Validator") { // Well, not a nice solution, but it works...
			?>
				<div class="fb-like" data-href="<?php echo $url;?>" data-send="<?php echo $showSend; ?>" data-layout="<?php echo $layout; ?>" data-width="<?php echo $width; ?>" data-show-faces="<?php echo $showFaces; ?>"></div>
			<?php
		}
}	

function displayIframeElement($url, $width, $height, $style="",$id='') {

	if (true) {
		$styleInt="width:$width"."px;height:$height"."px";
		if ($style) {
			$styleInt.=";$style";
		}

		$idText=($id)?' id="'.$id.'"':'';

		?>
			<!-- Well, not really valid strict html, but at least it appears to be... -->
			<!--[if IE]> 
				<?php 
				
				echo '<iframe'.$idText.' src="'.$url.'" width="'.$width.'" height="'.$height.'"';
				if ($style) {
					echo ' style="'.$styleInt.'"';
				}
				echo ' frameborder="0"></iframe>';
				
				?>
			<![endif]-->  
			<!--[if !IE]> 
				<--> 
					<object<?php echo $idText;?> type="text/html" data="<?php echo $url;?>" width="<?php echo $width;?>" height="<?php echo $height;?>" style="<?php echo $styleInt;?>"></object> 
				<!--> 
			<![endif]--> 
		<?php
	}
	else {
		echo '<iframe'.$idText.' src="'.$url.'" width="'.$width.'" height="'.$height.'"';
		if ($style) {
			echo ' "'.$style.'"';
		}
		echo '></iframe>';
	}
}

function writeVariableToFile($fileName, $var) {
	$handle=fopen($fileName,'w');
	if ($handle) {
		$retval=fwrite($handle,var_export($var,true))."\r\n";
		fclose($handle);
		return $retval;
	}
	else {
		return false;
	}
}

//Write new url in sitemap file, created on Feb.22, 2011
function writeUrlLine($message,$debugOnly=false) { //Based on writeLogLine()

	global $absolutePath, $debug, $appName;

	if (!isset($absolutePath)) {
		if (defined('ABSPATH')) {
			$absolutePath=ABSPATH;
		}
		else {
			$absolutePath='';
		}
	}
	
	if (!$debugOnly&&!$debug) {
		$logfile=$absolutePath."/sitemaps/".$appName."-sitemap.xml";
		$handle=fopen($logfile,'a');		
		if ($handle) {
			$dateTime=strftime("%Y%m%d%H%M%S",gmmktime()); // store in gmtime
			$retval=fwrite($handle, $dateTime.$message."\r\n");
			fclose($handle);
			return $retval;
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}

function unexpectedDatabaseError($echo=true) {

	global $database;

	$string="<p>Unexpected database error. Please contact support. Error: ".$database->getErrorMsg().".</p>";
	if ($echo) {
		echo $string;
	}
	return $string;
}

function onClickString($name,$echo=true) { // Not tested

	$string=" onclick=\"win=window.open(this.href,'$name');win.focus();return false\"";
	if ($echo) {
		echo $string;
	}
	return $string;
}

function onclickPopup($imageSrc, $imageName, $echo=false,$windowName='') {
	if (!$windowName) {
		$windowName=$imageName; // temporary solution, we need to cut out the spaces and stuff for IE
	}
	$string=" onclick=\"window.imageSrc='$imageSrc';window.imageName='$imageName';win=window.open('popup.html','$windowName');win.focus();return false;\"";
	if ($echo) {
		echo $string;	
	}
	return $string;
}

function url($parameterString) { // Not tested
	// creates a url string to the current page and sub
	
	global $page, $sub;
	
	return "index.php?page=$page&amp;sub=$sub&amp;$parameterString";
}

function httpDateFromNow($seconds) { // Added July 9, 2010 to be able to use in other sites
	$expire=time()+$seconds;
	$string=gmstrftime("%a, %d %b %Y %H:%M:%S GMT",$expire);
	return $string;
}

//Will send system errors to the system's email
//edited last march 21, 2012 by jeram - used the ADD class mail to send the messages
function sendErrorEmail($errorMessage) {
	
	global $systemTo,$siteUrl;
	
	if ($errorMessage) {
		$subject = "System error in: ".$siteUrl;
		$headers = "System Error Message";
		
		$newMail = new Mail($systemTo,$errorMessage,$subject);
		if (!$newMail->send()) {
			@mail("jeramray@activediscovery.net","error in sending the error",$errorMessage,$headers);
			return false;
		}
		else {
			return true;
		}
		//old codes
		/* if(!@mail($systemTo,$subject,$errorMessage,$headers)) { // Put the @ as errors are captured with the if, January 24, 2010, Guus
			return false;
		}
		}
		else {
			return false;
		} */
	}
}

//Write message in the system.log file
function writeLogLine($message,$debugOnly=false) { // Changed for parameter $debugOnly July 14, 2010, Guus. Not tested!!!!

	global $absolutePath, $debug, $appName;

	if (!$debugOnly&&!$debug) {
		$logfile=$absolutePath."/system.log";
		$handle=fopen($logfile,'a');		
		if ($handle) {
			date_default_timezone_set('UTC');
			$dateTime=strftime("%Y%m%d%H%M%S",time()); // store in gmtime
			$message=str_replace('\n','',$message); // Added March 25, 2013 by Guus. Not tested.
			$message=str_replace('\r','',$message); // Added March 25, 2013 by Guus. Not tested.
			$retval=fwrite($handle, $dateTime.$message."\r\n");
			fclose($handle);
			return $retval;
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}

//Use to get HTTP Request variables
function getParam($name,$default='',$forceRequestType='') {
	
	if (isset($_REQUEST[$name])) {
		// if ($_REQUEST[$name]) { // Added May 10, 2011 by Guus, and removed May 28, 2011, also by Guus
		///added by jeram, may 31, 2012; for the webservers that magic quoutes are turned on
		if ($forceRequestType) {
			switch (strtolower($forceRequestType)) {
				case 'get': {
					return @checkForMagicQuotes($_GET[$name]);/// @ is to return null if error occurs. Like if method was set to post and you forced it to $_GET
					break;
				}
				case 'post': {
					return @checkForMagicQuotes($_POST[$name]);
					break;
				}
				case 'cookie': {
					return @checkForMagicQuotes($_COOKIE[$name]);
					break;
				}
				default: { ///use $_REQUEST
					break;
				}
			}
			
		}
		return checkForMagicQuotes($_REQUEST[$name]);
	}
	else {
		return $default;
	}
}

///added by jeram, june 18, 2012
function checkForMagicQuotes($value) {
	if (get_magic_quotes_gpc()) {
		//added by pilardo dec 06, 2012
		if(is_array($value)) {
			return $value;
		}
		else {
			return stripslashes($value);
		}
	}
	else {
		return $value;
	}
}

//Will validate email address
function validate_email($email)	{
	if(!preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/',$email)){
		
		return false;
	}
	else {
		return $email;
	}
}

function trimGetParam($name) {
	return trim(getParam($name));
}

//Dont know where this is used
function getPagePermissions( $silent=false, $text='' ) {
	global $my, $majorVersion,$appName;
	
	if (($majorVersion<0)||($appName=='wa')) {
		die;
		$type=$my->gid;
	}
	else {
		$type=$my->usertype;
	}
	
	// Check if logged in
	if( !$type ) {
		if( !$silent ) {
			if( $text ) {
				echo $text;
			} else {
				echo "<h2>Page access denied</h2>You are required to login to access this page.";
			}
		}
		
		return false;
	}
	
	return true;
}

//Still use in WA, came from simpleacl.php file
function getPageAccessPermissions( $allowed_users ) {
	global $my;
	
	//Old application version
	if (_MAJOR_VERSION<0) { 
		$type=intval($my->gid);
	}
	//New version
	else {
		$type=intval($my->usertype);	
	}
	//echo "Type: ".$type;
	// Check if logged in
	if( $type ) {
		// Check access permissions
		if( is_array($allowed_users) ) {
			$allow = in_array( $type, $allowed_users, true );
			// $allow = in_array( $my->gid, $allowed_users, true );
		}
		else {
			$allow = ( $type == $allowed_users );
		}
		//echo $allow;
		return $allow;
	}
	//die();
	return false;
}

function start() { // What's this for?

	$setPHP = mb_language('Neutral');
	$setPHP = mb_internal_encoding("UTF-8");
	$setPHP = mb_http_input("UTF-8");
	$setPHP = mb_http_output("UTF-8");
	
	if (!$setPHP) {
	
	return true;
	}
	else {
		return false;
	}	
}

function skypeLink($phoneNumber){
	global $scripts;
	echo '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>';
	echo "<a href=\"skype:$phoneNumber?call\" onclick=\"return skypeCheck();\">$phoneNumber</a>";
}

function displayVideo($src, $width='618', $height='347') {
	
	global $siteVersion;
	
	if (true||($siteVersion=='local')) {
		?>
		<iframe src="http://player.vimeo.com/video/61077208" width="<?php echo $width;?>" height="<?php echo $height;?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>  
		<!-- Old video: 60816655
			<div style='border: 1px solid #000;background-color: #000;width:<?php echo $width+10;?>px;padding: 5px;'>
				<?php /* <object width="<?php echo $width;?>" height="<?php echo $height;?>" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab"> */ ?>
				<object data="<?php echo $src;?>" type="video/mpeg" width="<?php echo $width;?>" height="<?php echo $height;?>">
					<param name="src" value="<?php echo $src;?>" /> 
					<param name="controller" value="true" />
					<param name="autoplay" value="false" /> 
					<embed src="<?php echo $src;?>" width="<?php echo $width;?>" height="<?php echo $height;?>"
						autoplay="true"
						controller="true"
						pluginspage="http://www.apple.com/quicktime/download/">
					</embed> 
				</object>
			</div>
		-->
		<?php
	}
	else {
		?>
			<div style='border: 1px solid #000;background-color: #000;width: <?php echo $width+1;?>px;padding: 5px;'>
				<video width="<?php echo $width; ?>" height="<?php echo $height; ?>" controls preload tabindex='0'>
					<source src="<?php echo $src; ?>.mp4" type="video/mp4" />
					<source src="<?php echo $src; ?>.ogg" type="video/ogg" />
					<source src="<?php echo $src; ?>.webm" type="video/webm" />
					<object  data="<?php echo $src; ?>.mp4" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
						<embed src="<?php echo $src; ?>.swf" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
					</object> 
					Your browser does not support the <code>video</code> element. 
				</video>
			</div>
		<?php
	}
}

function displayRow($title,$content,$useHtmlspecialchars=true,$useP=true) {

	if ($useHtmlspecialchars) {
		$title=htmlspecialchars($title);
		$content=htmlspecialchars($content);
	}
	$useP1=false;
	if (strpos($useP,'both')===0) {
		$useP1=true;
	}
	else {
		$useP1=false;
	}
	?>
		<tr>
			<td valign="top"><?php echo ($useP1)?'<p>':'<h1>';?><?php echo $title;?><?php echo ($useP1)?'</p>':'</h1>';?></td>
			<td valign="top"><?php echo ($useP)?'<p>':'';?><?php echo $content;?><?php echo ($useP)?'</p>':'';?></td>
		</tr>	
	<?php
}

function makeDefines($fileName) {
	$first=false;
	$fileContent=file($fileName);
	// Might check if there's anything in $fileContent, but there should be, otherwise use of this function does not make sense.
	$fileContent[0]=removeBOM($fileContent[0]); // First have to remove the Byte Order Mark if any
	foreach($fileContent as $element => $content) {
		if ($first) {
			$first=false;
		}
		else {
			if (strpos('//',$content)===0) { // Skip comment lines
				// Do nothing
			}
			else {
				$position=strpos($content,'=');
				if ($position) {
					$defineValue=substr($content,0,$position);
					$contentValue=substr($content,$position+1);
					if (!defined($defineValue)) {
						define($defineValue,rtrim($contentValue,"\r\n"));
					}
				}
			}
		}
	}
}
} // End define
?>