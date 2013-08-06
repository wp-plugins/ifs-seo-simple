<?php

	if (!defined('_VALID_ADD')) die('Direct access not allowed');

	require_once(ABSPATH.'/wp-content/plugins/ifs-seo-simple/includes/add_mini_lib.php');
	
	function ifs_seo_action() {
		$title=getParam('title');
		$description=getParam('description');
		$keywords=getParam('keywords');
		$noIndexArchive=getParam('noindexarchive');
		$currentTitleOption=get_option('ifs_default_site_title');
		$currentDescriptionOption=get_option('ifs_default_site_description');
		$currentKeywordsOption=get_option('ifs_default_site_keywords');
		$noIndexArchiveOption=get_option('ifs_do_not_index_archive_pages');
		if ($title==$currentTitleOption) {
			echo '<p class="note">Title not changed.</p>';
		}
		else {
			if (update_option('ifs_default_site_title',$title)) {
				echo '<p class="note">Site default title changed from <span style="font-style:italic">'.$currentTitleOption.'</span> to <span style="font-style:italic">'.$title.'</span>.</p>'; 
			}
			else {
				echo '<p class="error">Error setting new default site title. Please contact support.</p>';
			}
		}
		if ($description==$currentDescriptionOption) {
			echo '<p class="note">Default meta description not changed.</p>';
		}
		else {
			if (update_option('ifs_default_site_description',$description)) {
				echo '<p class="note">Site default meta description changed from<p>';
				echo '<p style="margin-left:5%;font-style:italic">'.$currentDescriptionOption.'</p>';
				echo '<p style="margin-left:5%">to</p>';
				echo '<p style="margin-left:5%;font-style:italic">'.$description.'.</p>'; 
			}
			else {
				echo '<p class="error">Error setting new default site meta description. Please contact support.</p>';
			}
		}
		if ($keywords==$currentKeywordsOption) {
			echo '<p class="note">Site default meta keywords not changed.</p>';
		}
		else {
			if (update_option('ifs_default_site_keywords',$keywords)) {
				echo '<p class="note">Site default meta keywords changed from <span style="font-style:italic">'.$currentDescriptionOption.'</span><br/>to <span style="font-style:italic">'.$description.'</span>.</p>'; 
			}
			else {
				echo '<p class="error">Error setting new default site meta keywods. Please contact support.</p>';
			}
		}
		//echo '<p>$noIndexArchive: '.$noIndexArchive.'</p>';
		//echo '<p>$noIndexArchiveOption: '.$noIndexArchiveOption.'</p>';
		if ($noIndexArchive=='true') {
			if ($noIndexArchiveOption=='true') { // It was set before
				echo '<p class="note">Do not archive indexing setting did not change and is and was set to \'do not index archive pages\'. This is the recommended setting.</p>';
			}
			else { // Value changed from false to true
				if (update_option('ifs_do_not_index_archive_pages','true')) {
					echo '<p class="note">Do not archive indexing turned on. This is the recommended setting.</p>'; 			
				}
				else {
					echo '<p class="error">Error setting archive indexing option. Please contact support.</p>';
				}
			}
		}
		else { // $noIndexArchive is false
			if ($noIndexArchiveOption=='false') {
				echo '<p class="note">Do not archive indexing setting did not change and is and was set to \'do index archive pages\'. This is NOT the recommended setting.</p>';
			}
			else {
				if (update_option('ifs_do_not_index_archive_pages','false')) {
					echo '<p class="note">Do not archive indexing turned off. This is NOT the recommended setting.</p>'; 			
				}
				else {
					echo '<p class="error">Error setting archive indexing option. Please contact support.</p>';
				}
			}
		}
		die;
	}
	
	function ifsSeoAjaxScript($jsCallbackFunctionName='myAlert') {
		?>
			<script type="text/javascript">
				<!--
					function myAlert(response) {
						alert('Got this from the server: ' + response);				
					}
					function merge_objects(obj1,obj2){
						var obj3 = {};
						for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
						for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
						return obj3;
					}
					
					function ifsSeoAjaxCall(task,parameters) {
						//window.alert(parameters.name);
						if (typeof(task)=='undefined') {
							task='';
						}
						jQuery(document).ready(function($) {
							var data = {
								action: 'ifs_seo_action',
								task: task,
								whatever: 1234
							};
							//window.alert(typeof(parameters));
							if (typeof(parameters=='object')) {
								data=merge_objects(data,parameters);
							}
							// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
							$.post(ajaxurl, data, function(response) {
								<?php echo $jsCallbackFunctionName;?>(response);
							});
						});
					}
				// -->
			</script>
		<?php
	}
?>