<?php
/*
Plugin Name: IFS Seo Simple
Plugin URI: http://www.inspiration-for-success.com/plugins/
Description: IFS module for SEO in a very simple way
Tags: seo, search engine optimization, simple, simple seo
Version: 1.52
Stable tag: 1.52
Author: Guus Ellenkamp
Author URI: http://designs.activediscovery.net/
License: GPLv2


Copyright 2013 Guus Ellenkamp
 
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('_VALID_ADD')) define('_VALID_ADD',1);

if (!defined('_LOCAL_DEVELOPMENT')) define('_LOCAL_DEVELOPMENT',0);

require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/add_mini_lib.php');
require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/main-lib.php');

//register_activation_hook( __FILE__, 'ifs_install' );


// Front end stuff

global $ifsInHead, $ifsInBody;
$ifsInHead=false;
$ifsInBody=false;

function showIfsInfo() {

	global $ifsInHead, $ifsInBody;

	?>
		<?php if ($ifsInHead) echo '<-- '; ?>
		<p>In head: <?php echo ($ifsInHead)?'true':'false';?></p>
		<p>In body: <?php echo ($ifsInBody)?'true':'false';?></p>
		<?php if ($ifsInHead) echo '--> '; ?>
	<?php
}

function ifs_simple_seo_wp_head_action() {
	global $ifsInHead;
	$ifsInHead=true;
	$doNotIndexArchive=get_option('ifs_do_not_index_archive_pages');

	if ($doNotIndexArchive==='true') {
		if (is_archive()) { 
			echo '<meta name="robots" content="noindex, follow"/>';
		}
	}
}

function ifs_simple_seo_wp_body_action() {
	global $ifsInBody,$ifsInHead;
	$ifsInHead=false;
	$ifsInBody=true;
}

function ifs_seo_simple_title_filter($titleIn,$separator,$separatorLocation) {
	
	global $post;
	
	if (is_home()||gettype($post)!='object') { // For home we set some default title
		$defaultTitle=get_option('ifs_default_site_title');
		if ($defaultTitle) {
			$title=htmlspecialchars($defaultTitle);
		}
		else {
			$title=$titleIn;
		}
	}
	else {
		$title=get_post_meta($post->ID,'_ifs-title',true);
		if (!$title) { // Support for old version
			$titles=get_post_custom_values('title');
			if (count($titles)) {
				$title=htmlspecialchars($titles[0]);
			}
			else {
				if (get_option('ifs-use-basic-titles')=='true') {
					// Need to still check archive pages
					if (!is_archive()) {
						$title=htmlspecialchars($post->post_title);
					}
					else {
						$title=$titleIn;
					}
				}
				else {
					$title=$titleIn;
				}
			}
		}
	}
	return $title;
}

// Back end stuff
function ifs_seo_simple_meta_action() { // Currently copied from success theme.
	
	global $post;
	
	// Add description and keywords meta tags
	// No check for duplicate yet.
	
	if (isset($post->ID))
		echo '<!-- Add meta data. Post id is '.$post->ID.'. -->';
	else {
		echo '<!-- No post ID available. -->';
	}
	
	/* causes problem when there is an end comment in the post data.
	if (defined('_LOCAL_DEVELOPMENT')) {
		echo '<!--';
		print_r($post);
		echo '-->';
	}
	*/
	
	// New version
	$defaultDescription=get_option('ifs_default_site_description');
	$defaultKeywords=get_option('ifs_default_site_keywords');		
	if (is_home()||(!isset($post->ID))) {
		$description=$defaultDescription;
		$keywords=$defaultKeywords;
	}
	else {
		$description=get_post_meta($post->ID,'_ifs-meta-description',true);
		$keywords=get_post_meta($post->ID,'_ifs-meta-keywords',true);

		if (!$description) { // See if there is an old version meta description
			$descriptions=get_post_custom_values('description');
			if (isset($descriptions[0])) {
				$description=$descriptions[0];
			}
		}
		if (!$keywords) { // See if there is an old version meta keywords
			$keywords=get_post_custom_values('keywords');
			if (isset($keywords[0])) {
				$keywords=$keywords[0];
			}
		}
		
	}
	if ($description) {
		echo '<meta name="description" content="'.htmlspecialchars($description).'"/>';
		echo "\r\n";
	}
		
	if ($keywords) {
		echo '<meta name="keywords" content="'.htmlspecialchars($keywords).'"/>';
		echo "\r\n";
	}
}

function ifs_seo_simple() {
	?>
		<h1>SEO Simple</h1>
		<?php
			$installedConfirmation=get_option('ifs-seo-install-confirmed');
			if ($installedConfirmation!='true') {
				echo '<h2>Confirmation</h2>';
				echo '<p>You did not confirm your installation yet. Please confirm your installation of IFS SEO Simple on the <a href="'.admin_url().'admin.php?page=ifs-seo-feedback">feedback page</a>.</p>';
			}
		?>
		<h2>Why SEO Simple</h2>
		<p>We believe on-page optimization starts with something very simple:</p>
		<ul style="margin-left:5%">
			<li>Make sure each page has a good and proper title.</li>
			<li>Make sure each page has a good and proper meta description.</li>
		</ul>
		<p>So that's why we made this plugin, simple. Read more in the <a href="<?php echo admin_url().'admin.php?page=ifs-seo-documentation';?>">IFS SEO Simple documentation screen</a>.</p>
		<h2>Current settings</h2>
		<?php 
			$defaultTitle=get_option('ifs_default_site_title');
			if ($defaultTitle) {
				echo '<p>Your current default title is: <span style="font-weight:bold">'.$defaultTitle.'</span>.</p>';
			}
			else {
				echo '<p>You did not set the default title for the IFS SEO Simple plugin. Please configure it in the <a href="'.admin_url().'admin.php?page=ifs-seo-configure">IFS SEO Simple configuration screen</a>.</p>';
			}

			$defaultDescription=get_option('ifs_default_site_description');
			if ($defaultDescription) {
				echo '<p>Your current default meta description is: <span style="font-weight:bold">'.$defaultDescription.'</span>.</p>';
			}
			else {
				echo '<p>You did not set the default meta description for the IFS SEO Simple plugin. Please configure it in the <a href="'.admin_url().'admin.php?page=ifs-seo-configure">IFS SEO Simple configuration screen</a>.</p>';
			}

			$defaultKeywords=get_option('ifs_default_site_keywords');
			if ($defaultDescription) {
				echo '<p>Your current default meta keywords are: <span style="font-weight:bold">'.$defaultKeywords.'</span>.</p>';
			}
			else {
				echo '<p>You did not set the default meta keywords for the IFS SEO Simple plugin. Please configure it in the <a href="'.admin_url().'admin.php?page=ifs-seo-configure">IFS SEO Simple configuration screen</a>.</p>';
			}

			$useBasicTitles=get_option('ifs-use-basic-titles');
			if ($useBasicTitles=='true') {
				echo '<p>Your current basic title setting is to show basic titles. This is the recommended setting.</p>';
			}
			else {
				echo '<p>Your current basic title setting is to <span style="font-weight:bold">NOT</span> show basic titles. This is <span style="font-weight:bold">NOT</span> the recommended setting.</p>';
			}

			$doNotIndexArchive=get_option('ifs_do_not_index_archive_pages');
			if ($doNotIndexArchive=='true') {
				echo '<p>Your current archive page index setting is to not index archive pages. This is the recommended setting.</p>';
			}
			else {
				echo '<p>Your current archive page index setting is to index archive pages. This is <span style="font-weight:bold">NOT</span> the recommended setting.</p>';
			}
		?>
		<h2>Configure</h2>
		<p>You can configure IFS SEO Simple configuration options in the <a href="<?php echo admin_url().'admin.php?page=ifs-seo-configure';?>">IFS SEO Simple configuration screen</a>.</p>
		<p>Of course you can also check <a href="http://wordpress.org/support/plugin/ifs-seo-simple" target="_blank">Wordpress IFS SEO Simple</a> on the Wordpress site.</p>
		<h2>Documentation</h2>
		<p>Read about IFS SEO Simple in the <a href="<?php echo admin_url().'admin.php?page=ifs-seo-documentation';?>">IFS SEO Simple documentation screen</a>.</p>
		<h2>Support</h2>
		<p>For support you can just post your issues in <a href="http://wordpress.org/support/plugin/ifs-seo-simple" target="_blank">Wordpress IFS SEO Simple</a> support area.</p>
		<p>You can also just send an e-mail to Guus at <a href="mailto:guus@activediscovery.net">guus@activediscovery.net</a> to report any bugs or request changes or new features.</p>
	<?php
}

function ifs_seo_simple_documentation() {
	require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/documentation.php'); // Still thinking about performance AND readability...
	ifsSeoDocumentation();
}

function ifs_seo_simple_feedback() {
	require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/feedback.php'); // Still thinking about performance AND readability...
	ifsSeoFeedback();
}

function ifs_seo_simple_menu () {
	add_menu_page('IFS SEO Simple','IFS SEO Simple','manage_options','ifs-seo-simple','ifs_seo_simple');
	add_submenu_page('ifs-seo-simple','SEO Simple documentation','Documentation SEO Simple','manage_options','ifs-seo-documentation','ifs_seo_simple_documentation');
	add_submenu_page('ifs-seo-simple','Configure SEO Simple options','Configure SEO Simple','manage_options','ifs-seo-configure','ifs_seo_simple_configure');
	add_submenu_page('ifs-seo-simple','Feedback SEO Simple options','Feedback about SEO Simple','manage_options','ifs-seo-feedback','ifs_seo_simple_feedback');
}

function ifs_seo_simple_configure() {
	require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/configure.php'); // Still thinking about performance AND readability...
	ifs_seo_simple_configureX();
}

function ifs_seo_save_meta() {
	echo '<p>SEO save meta.</p>';
}

function ifs_seo_meta_box_html($post,$meta) {

	/*
	echo '<pre>';
	print_r($post);
	echo '</pre>';
	*/ 
	
	$title=get_post_meta($post->ID,'_ifs-title',true);
	$description=get_post_meta($post->ID,'_ifs-meta-description',true);
	$keywords=get_post_meta($post->ID,'_ifs-meta-keywords',true);
	if (!$title) { // See if there is an old version meta keywords
		$titles=get_post_custom_values('title');
		if (isset($titles[0])) {
			$title=$titles[0];
		}
	}
	if (!$description) { // See if there is an old version meta description
		$descriptions=get_post_custom_values('description');
		if (isset($descriptions[0])) {
			$description=$descriptions[0];
		}
	}
	if (!$keywords) { // See if there is an old version meta keywords
		$keywords=get_post_custom_values('keywords');
		if (isset($keywords[0])) {
			$keywords=$keywords[0];
		}
	}

	echo '<table>';
	echo '<tr>';
	echo '<td style="vertical-align:top">Meta description:&nbsp;</td>';
	echo '<td style="vertical-align:top">';
	echo '<textarea rows="3" cols="80" name="ifsmetadescription">'.$description.'</textarea>&nbsp;';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td style="vertical-align:top">Meta keywords:&nbsp;</td>';
	echo '<td>';
	echo '<textarea cols="80" rows="2" name="ifsmetakeywords">'.$keywords.'</textarea>';
	echo '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td style="vertical-align:top">Title override:&nbsp;</td>';
	echo '<td>';
	echo '<input size="80" name="ifstitle" type="text" value="'.$title.'"/>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
}

function ífs_seo_add_meta_boxes() {
	add_meta_box('ifs-meta-boxes-post','IFS SEO meta data','ifs_seo_meta_box_html','post');
	add_meta_box('ifs-meta-boxes-page','IFS SEO meta data','ifs_seo_meta_box_html','page');
}

function my_update_post_meta($postId,$key,$value) {
	// returns false if real error, otherwhise true
	$current=get_post_meta($postId,$key,'single');
	if ($value==$current) {
		return true;
	}
	else {
		return update_post_meta($postId,$key,$value);
	}
}

function ifs_save_seo_meta_data($postId) {

	if (defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE) {
		return $postId;
	}
	else {
		$title=getParam('ifstitle');
		$description=getParam('ifsmetadescription');
		$keywords=getParam('ifsmetakeywords');
		my_update_post_meta($postId,'_ifs-title',$title);
		my_update_post_meta($postId,'_ifs-meta-description',$description);
		my_update_post_meta($postId,'_ifs-meta-keywords',$keywords);
	}
}

// Back end actions
add_action('admin_menu','ifs_seo_simple_menu');
add_action('wp_ajax_ifs_seo_action', 'ifs_seo_action');
add_action('add_meta_boxes','ífs_seo_add_meta_boxes');
add_action('save_post','ifs_save_seo_meta_data');

// Front end actions
add_action('wp_head','ifs_simple_seo_wp_head_action');
add_action('wp_body','ifs_simple_seo_wp_body_action');
add_filter('wp_title','ifs_seo_simple_title_filter',20,3); // We want to be higher than the theme hook
add_action('wp_head','ifs_seo_simple_meta_action');
?>