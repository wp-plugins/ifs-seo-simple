<?php
/*
Plugin Name: IFS Seo Simple
Plugin URI: http://www.inspiration-for-success.com/plugins/
Description: IFS module for SEO in a very simple way
Tags: seo, search engine optimization, simple, simple seo
Version: 1.31
Stable tag: 1.31
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

require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/main-lib.php');

//register_activation_hook( __FILE__, 'ifs_install' );

if (!defined('_LOCAL_DEVELOPMENT')) define('LOCAL_DEVEOPMENT',0);

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
	
	if (is_home()) { // For home we set some default title
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
	
	echo '<!-- Add meta data. Post id is '.$post->ID.'. -->';
	
	if (defined('_LOCAL_DEVELOPMENT')) {
		echo '<!--';
		print_r($post);
		echo '-->';
	}
	
	// New version
	$defaultDescription=get_option('ifs_default_site_description');
	$defaultKeywords=get_option('ifs_default_site_keywords');		
	if (is_home()) {
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
		<h2>Current settings</h2>
		<p>Current settings will be displayed here in the next version.</p>
		<p>You can see and configure IFS SEO Simple in the <a href="<?php echo admin_url().'admin.php?page=ifs-seo-configure';?>">IFS SEO Simple configuration screen</a>.</p>
		<p>Of course you can also check <a href="http://wordpress.org/support/plugin/ifs-seo-simple" target="_blank">Wordpress IFS SEO Simple</a> on the Wordpress site.</p>
		<h2>Documentation</h2>
		<p>Read about IFS SEO Simple in the <a href="<?php echo admin_url().'admin.php?page=ifs-seo-documentation';?>">IFS SEO Simple documentation screen</a>.</p>
		<h2>Support</h2>
		<p>For support you can just post your issues in <a href="http://wordpress.org/support/plugin/ifs-seo-simple" target="_blank">Wordpress IFS SEO Simple</a> support area.</p>
		<p>You can also just send an e-mail to Guus at <a href="mailto:guus@activediscovery.net">guus@activediscovery.net</a> to report any bugs or request changes or new features.</p>
	<?php
}

function ifs_seo_simple_documentation() {
	?>
		<h1>SEO Simple documentation</h1>
		<h2>Background</h2>
		<p>We believe that on page SEO can be very simple and don't need very advanced extende plugins, so for our site <a href="http://www.inspiration-for-success.com/" target="_blank">Inspiration for Success</a> we use some very simple rules and ways to make the site search engine friendly.</p>
		<p>As we think Wordpress by default is not specifically search engine friendly we made some changes to the theme that we now want to make available in the form of a plugin.</p>
		<p>So as we are making this anyhow, why not make it available to you.</p>
		<h2>Basic ideas and setup</h2>
		<p>The basis comes from some other plugin which we could not find anymore, so that's why we created our own, with some additional options. And the basic idea of the other simple SEO plugin was very simple: use standard Wordpress custom fields to set title, keywords and description. So that's what was the core of our IFS SEO Simple plugin.</p>
		<p>With version 1.2 we created our own fields for title, meta description and meta keywords tags, so no need to turn on and add custom fields manually. Of course we provide backwards compatibility.</p>
		<p>Custom fields are turned off by default in the Wordpress edit screen, so you have to turn them on to use most of the features of this plugin: go to the 'edit post' and/or 'edit page' screen, go to 'screen options' you see at the right top and check the field 'custom fields'. That's all. Now you will see some stuff below the post or page edit fields.</p>
		<h2>Suggested steps</h2>
		<p>Suggested step for initial setup is:</p>
		<ol>
			<li>Configure the default site title, default site description and default site keywords in the <a href="<?php echo admin_url().'admin.php?page=ifs-seo-configure';?>">IFS SEO Simple configuration screen</a>.</li>
		</ol>
		<p>No need to turn on the custom fields anymore as in previous versions. We do provide backword compatibility, but please note we don't convert the old custom fields data. So just leave the old custom fields.</p>
		<p>Continuous action for each post and page:</p>
		<ul>
			<li>Create a custom field <span style="font-style:italic;font-weight:bold">description</span> for each post and page.</li>
			<li>Create a custom field <span style="font-style:italic;font-weight:bold">keywords</span> for each post and page.</li>
			<li>Create a custom field <span style="font-style:italic;font-weight:bold">title</span> for a post or page if you want the title to be different from the post title as set in the default title field or the default Wordpress title.</li>
		</ul>
		<p>Graphics on the setup to follow.</p>
		<h2>SEO Simple</h2>
		<h3>Title tag</h3>
		<p>As we believe the title tag is the most important item in a web page from an SEO point of view we think there is only one way to set it: manually.</p>
		<p>And it needs to be set on a 'per page' basis, so we want various options to set it.</p>
		<p>Normallly Wordpress uses the blog title as the page title, which in most cases certainly works very well, but we still wanted to have better control.</p>
		<h3>Description meta tag</h3>
		<p>The description meta tag is the second most important item in a web page as it is normally displayed by Google in the search results and will help people decide whether to go to a page or not.</p>
		<p>So we believe that for each page that is to be found through Google the description meta tag should get a lot of attention.</p>
		<h3>Keywords meta tag</h3>
		<p>Many people believe the keywords meta tag is not being used anymore by Google. This may be true, but we believe something: if it doesn't help it doesn't hurt either if filled in properly.</p>
		<p>So yes, we include features for the keywords meta tag and also always use it in our sites.</p>
		<h2>Planned changes</h2>
		<p>Planned changes are:</p>
		<ul>
			<li>Handling of titles of archive screens.</li>
			<li>Showing current options in plugin home screen.</li>
			<li>More detailed options for noindex of archive pages.</li>
			<li>Store IFS version number to provide better support for upgrades and conversions in the future.</li>
			<li>Check for duplicate meta description and meta keywords tags.</li>
			<li>Improve the documentation.</li>
			<li>Set option for priority to determine the location of the fields in the edit screen.</li>
		</ul>
		<h2>Credits</h2>
		<p>The origin for this plugin lie in the project <a href="http://www.inspiration-for-success.com/" target="_blank">Inspiration for Success</a>.</p>
		<p>Initial code was made by Guus who is the owner of <a href="http://designs.activediscovery.net/" target="_blank">Active Discovery Designs</a>.</p>
	<?php
}

function ifs_seo_simple_menu () {
	add_menu_page('IFS SEO Simple','IFS SEO Simple','manage_options','ifs-seo-simple','ifs_seo_simple');
	add_submenu_page('ifs-seo-simple','SEO Simple documentation','Documentation SEO Simple','manage_options','ifs-seo-documentation','ifs_seo_simple_documentation');
	add_submenu_page('ifs-seo-simple','Configure SEO Simple options','Configure SEO Simple','manage_options','ifs-seo-configure','ifs_seo_simple_configure');
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