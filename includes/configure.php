<?php
	if (!defined('_VALID_ADD')) die('Direct access not allowed');
	
	function ifs_seo_simple_configureX() {
		?>
			<script type="text/javascript">
				<!--
					function callBackForSeoSimpleConfiguration(response) {
						//window.alert('Callback for add e-mail');
						resultLocation=document.getElementById('ifsresult');
						if (typeof(resultLocation)=='object') {
							if (response=='waiting') {
								resultLocation.innerHTML='<p class="note">Waiting for result.</p>';
							}
							else {	
								//window.alert(response);
								check=response.substr(0,2);
								//window.alert(displayString);
								if (check=='ok') {	
									displayString=response.substring(2);
									//window.alert('ok');
								}
								else {
									displayString=response;
								}
								resultLocation.innerHTML=displayString;
							}
						}
						else {
							window.alert('Result location not defined.');
						}
					}
				// -->
			</script>
			<?php ifsSeoAjaxScript('callBackForSeoSimpleConfiguration');?>
			<h2>Configuration IFS SEO Simple.</h2>
			<?php
				if (defined('_LOCAL_DEVELOPMENT') {
					echo '<p>';
					$meta=get_user_meta(1);
					var_dump($meta['metaboxhidden_post']);
					echo '</p>';
				}
			?>
			<p>On this page you can configure options for IFS SEO Simple.</p>
			<form action="/" method="post">
				<script type="text/javascript">
					<!--
						function ifsSeoConfigureSubmit() {
							titleObject=document.getElementById('titleid');
							descriptionObject=document.getElementById('descriptionid');
							keywordsObject=document.getElementById('keywordsid');
							noIndexArchiveObject=document.getElementById('noindexarchiveid');
							callBackForSeoSimpleConfiguration('waiting');
							var parameters={
								title: titleObject.value,
								description: descriptionObject.value,
								keywords: keywordsObject.value,
								noindexarchive: noIndexArchiveObject.checked
							}
							ifsSeoAjaxCall('configuremailer',parameters);
						}
					// -->
				</script>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<?php
							$defaultTitle=get_option('ifs_default_site_title');
							if (!$defaultTitle) {
								if (defined('_TITLE')) { // For our own backwards compatibility.
									$defaultTitle=_TITLE; 
								}
								else {
									$defaultTitle=get_bloginfo();
								}
							}
						?>
						<td><p>Default title:</p></td>
						<td><p><input id="titleid" type="text" size="100" name="title" value="<?php echo $defaultTitle;?>"/></p></td>
						<td><p>This is the default site title and will be the title of the homepage. It is the most important item in the site, not only for search engines, but also for humans deciding whether to click or not.</p></td>
					</tr>
					<tr>
						<?php
							$defaultDescription=get_option('ifs_default_site_description');
						?>
						<td style="vertical-align:top"><p>Default description:</p></td>
						<td><textarea id="descriptionid" cols="100" rows="5" name="description"><?php echo $defaultDescription;?></textarea></td>
						<td><p>This is the default description for the site and shown in the 'meta description' tag. We consider this the second most important element in any website as it is also the first thing people see in a search engine.</p></td>
					</tr>
					<tr>
						<?php
							$defaultKeywords=get_option('ifs_default_site_keywords');
						?>
						<td><p>Default keywords:</p></td>
						<td><p><input id="keywordsid" type="text" size="100" name="keywords" value="<?php echo $defaultKeywords;?>"/></p></td>
						<td><p>These are the default keywords for the site and shown in the 'meta keywords' tag.</p></td>
					</tr>
					<tr>
						<?php
							$doNotIndexArchive=get_option('ifs_do_not_index_archive_pages');
						?>
						<td><p>Do not index archive pages:</p></td>
						<td><p><input id="noindexarchiveid" type="checkbox" name="noindexarchive"<?php echo ($doNotIndexArchive==='true')?' checked="checked"':'';?> value="<?php echo ($doNotIndexArchive==='true')?'true':'';?>"/> More detailed options planned.</p></td>
						<td><p>To keep a clean site towards search engines with only the real content pages indexed you may want to tell Google not to index archive pages. An archive page is a category, tag, author or a date based page. So for the technical people: we will add a meta tag &lt;meta name="robots" content="noindex, follow"/&gt; for archive pages.</p></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<p>
								<input type="button" value="Save" onclick="ifsSeoConfigureSubmit();"/>
							</p>
						</td>
					</tr>
				</table>
			</form>
			<h2>Result</h2>
			<div id="ifsresult">
				<p>Nothing submitted yet.</p>
			</div>
			<h2>Notes</h2>
			<p>Please note we don't do check on duplicate meta keywords and meta desctiption tags. This could happen if you have multiple plugins installed for this reason.</p>
			<h2>Technical</h2>
			<p>We found that we had to set our filter hook priority parameter in order to bypass some default Wordpress theme action hooks. We have set it to 20 and that seems fine.</p>
		<?php
	}
?>