<?php
/*
Plugin Name: IFS Seo Simple
Plugin URI: http://www.inspiration-for-success.com/plugins/
Description: IFS module for SEO in a very simple way
Tags: seo, search engine optimization, simple, simple seo
Version: 0.2
Stable tag: 0.1
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
	
	if (is_home()) { // For home we set some default title
		$defaultTitle=get_option('ifs_default_site_title');
		if ($defaultTitle) {
			$title=$defaultTitle;
		}
		else {
			if (defined('_TITLE')) { // Backwards compatibility
				$title=_TITLE;
			}
			else { // Don't do anything.
				$title=$titleIn;
			}
		}
	}
	else {
		$titles=get_post_custom_values('title');
		if (count($titles)) {
			$title=$titles[0];
		}
		else {
			$title=$titleIn;
		}
	}
	return $title;
}

// Back end stuff
function ifs_seo_simple_meta_action() { // Currently copied from success theme.
	// Add description and keywords meta tags
	// No check for duplicate yet.
	$descriptions=get_post_custom_values('description');
	if (defined('_KEYWORDS')) {
		$keywords=_KEYWORDS;
	}
	if (defined('_DESCRIPTION')) {
		$description=_DESCRIPTION;		
	}
	if (!is_home()) {
		if (isset($descriptions[0])) {
			$description=$descriptions[0];
		}
		$keywords=get_post_custom_values('keywords');
		if (isset($keywords[0])) {
			$keywords=$keywords[0];
		}
	}
	if (isset($description)) {
		echo '<meta name="description" content="'.$description.'"/>';
		echo "\r\n";
	}
	if (isset($keywords)) {
		echo '<meta name="keywords" content="'.$keywords.'"/>';
	}
}

function ifs_seo_simple() {
	?>
		<h1>SEO Simple</h1>
		<h2>Background</h2>
		<p>We believe that on page SEO can be very simple and don't need very advanced extende plugins, so for our site <a href="http://www.inspiration-for-success.com/" target="_blank">Inspiration for Success</a> we use some very simple rules and ways to make the site search engine friendly.</p>
		<p>As we think Wordpress by default is not specifically search engine friendly we made some changes to the theme that we now want to make available in the form of a plugin.</p>
		<p>So as we are making this anyhow, why not make it available to you.</p>
		<h2>Basic ideas and setup</h2>
		<p>The basis comes from some other plugin which we could not find anymore, so that's why we created our own, with some additional options. And the basic idea of the other simple SEO plugin was very simple: use standard Wordpress custom fields to set title, keywords and description. So that's what we basically do and is the core of our IFS SEO Simple plugin.</p>
		<p>Custom fields are turned off by default in the Wordpress edit screen, so you have to turn them on to use most of the features of this plugin: go to the 'edit post' and/or 'edit page' screen, go to 'screen options' you see at the right top and check the field 'custom fields'. That's all. Now you will see some stuff below the post or page edit fields.</p>
		<h2>Suggested steps</h2>
		<p>Suggested steps for initial setup are:</p>
		<ol>
			<li>Configure the default site title, default site description and default site keywords in the <a href="<?php echo admin_url().'admin.php?page=ifs-seo-configure';?>">IFS SEO Simple configuration screen</a>.</li>
			<li>Turn on <span style="font-style:italic">Custom fields</span> for <span style="font-weight:bold">posts</span> in the screen <a href="<?php echo admin_url().'post-new.php';?>">add new <span style="font-weight:bold">post</span></a>. Click on the button <span style="font-style:italic">Screen Options</span> (right top) and check the box <span style="font-style:italic">Custom Fields</span>.</li>
			<li>Turn on <span style="font-style:italic">Custom fields</span> for <span style="font-weight:bold">pages</span> in the screen <a href="<?php echo admin_url().'post-new.php?post_type=page';?>">add new <span style="font-weight:bold">page</span></a>. Click on the button <span style="font-style:italic">Screen Options</span> (right top) and check the box <span style="font-style:italic">Custom Fields</span>.</li>
		</ol>
		<p>Continuous action for each post and page:</p>
		<ul>
			<li>Create a custom field <span style="font-style:italic;font-weight:bold">description</span> for each post and page.</li>
			<li>Create a custom field <span style="font-style:italic;font-weight:bold">keywords</span> for each post and page.</li>
			<li>Create a custom field <span style="font-style:italic;font-weight:bold">title</span> for a post or page if you want the title to be different from the post title as set in the default title field.</li>
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
		<h2>Credits</h2>
		<p>The origin for this plugin lie in the project <a href="http://www.inspiration-for-success.com/" target="_blank">Inspiration for Success</a>.</p>
		<p>Initial code was made by Guus who is the owner of <a href="http://designs.activediscovery.net/" target="_blank">Active Discovery Designs</a>.</p>
	<?php
}

function ifs_seo_simple_menu () {
	add_menu_page('IFS SEO Simple','IFS SEO Simple','manage_options','ifs-seo-simple','ifs_seo_simple');
	add_submenu_page('ifs-seo-simple','Configure SEO Simple options','Configure SEO Simple','manage_options','ifs-seo-configure','ifs_seo_simple_configure');
}

function ifs_seo_simple_configure() {
	require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/configure.php'); // Still thinking about performance AND readability...
	ifs_seo_simple_configureX();
}

// Back end actions
add_action('admin_menu','ifs_seo_simple_menu');
add_action('wp_ajax_ifs_seo_action', 'ifs_seo_action');

// Front end actions
add_action('wp_head','ifs_simple_seo_wp_head_action');
add_action('wp_body','ifs_simple_seo_wp_body_action');
add_filter('wp_title','ifs_seo_simple_title_filter',20,3); // We want to be higher than the theme hook
add_action('wp_head','ifs_seo_simple_meta_action');
?>