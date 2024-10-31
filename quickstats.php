<?php
/*
Plugin Name: Quick Stats
Plugin URI: http://www.symbolengine.com/index.php/2006/07/13/quickstats_v1/
Description: Logs hits with referrer, ip, host, client and url information. RSS tracker feature lets you tracker your visitors in near realtime using an RSS reader software.
Version: 1.1
Author: Jim Qode
Author URI: http://www.symbolengine.com
*/

/*  Copyright 2006  Jim Qode  (email : jim@symbolengine.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


function JQ_register_admin_panel() {
	if (function_exists('add_options_page')) {
		add_options_page('QuickStats', 'Quick Stats', 1, basename(__FILE__), 'JQ_stat_options_subpanel');
		add_submenu_page('edit.php', 'JQStats', 'Show Stats', 1, basename(__FILE__), 'JQ_stats');	
	}
}
add_action('admin_menu', 'JQ_register_admin_panel');

function JQ_stats() {
	$numhits = mysql_fetch_row(mysql_query("SELECT COUNT(id) from `wp_jqstats`"));
	$numhits = $numhits[0];
	$qhits = "SELECT cdate, COUNT(ip), SUM(cnt) FROM ("
		."SELECT DATE(FROM_UNIXTIME(`date`)) as cdate, ip, COUNT( id ) AS cnt FROM wp_jqstats GROUP BY cdate, ip) AS ss "
		."GROUP BY cdate ORDER BY cdate ASC";
	$qwkday = "SELECT wkday, COUNT( ip ) , SUM( cnt ) FROM ("
		."SELECT WEEKDAY( FROM_UNIXTIME( `date` ) ) AS wkday, ip, COUNT( id ) AS cnt "
		."FROM `wp_jqstats` GROUP BY wkday, ip) AS ss "
		."GROUP BY wkday ORDER BY wkday";
	
	
	$qcontent = "SELECT url, COUNT(id) as cnt FROM `wp_jqstats` GROUP BY url ORDER BY cnt DESC";
	$qref = "SELECT referer,COUNT(id) as cnt FROM `wp_jqstats` GROUP BY referer ORDER BY cnt DESC";
	$qbrowser = "SELECT browser,COUNT(id) as cnt FROM `wp_jqstats` GROUP BY browser ORDER BY cnt DESC";
	$qvisit = "SELECT * FROM `wp_jqstats` ORDER BY id DESC";
	?>
	<style>
		table.jqstable {
			font-size: 8pt;
			background-color: #6da6d1;
			width: 100%;
		}
		.jqstable td{
			font-size: 8pt;
			background-color: #FFFFFF;
		}
		.jqstable th{
			text-align:left;
		}
		.moreinfo {
			text-decoration: underline;
		}
	</style>
	<div class=wrap>
		<h2>Usage Statistics</h2>
		<? 
		if (get_option('jqst_general_stats')=='show') {
			$hits =mysql_query($qhits);
			$wkday =mysql_query($qwkday);
			$days = Array ('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
		?>	
		<h4>General Statistics</h4>
		<table border="0" width="100%"><tr><td width="33%" valign="top">
			<table class="jqstable">
			<tr><th>Date</th><th>Unique</th><th>Total</th></tr>
			<? while ($c = mysql_fetch_row($hits)) { 
			$numuniq += $c[1];
			?>
			<tr><td><?=$c[0]?></td><td><?=$c[1]?></td><td><?=$c[2]?></td></tr>
			<? } ?>
			<tr><td><b>Total</b></td><td><b><?=$numuniq?></b></td><td><b><?=$numhits?></b></td></tr>
			</table>
		</td><td width="33%" valign="top">
			<table class="jqstable">
			<tr><th>Day</th><th>Unique</th><th>Total</th><th>% Unique</th><th>% Total</th</tr>
			<? while ($c = mysql_fetch_row($wkday)) { ?>
			<tr><td><?=$days[$c[0]]?></td><td><?=$c[1]?></td><td><?=$c[2]?></td>
			<td><?=number_format($c[1]*100/$numuniq,2)?></td>
			<td><?=number_format($c[2]*100/$numhits,2)?></td></tr>
			<? } ?>
			</table>
		</td><td width="33%" valign="top">
		
		</td></tr></table>

		<? 
		}	
		if (get_option('jqst_content_summary')=='show') {
		$content = mysql_query($qcontent); ?>
		<h4>Content Summary</h4>
		<table class="jqstable">
			<tr><th>Path</th><th width="70"># Hits</th><th width="50">% Hits</th></tr>
			<? while ($c = mysql_fetch_row($content)) { ?>
			<tr><td><?=$c[0]?></td><td align="right"><?=$c[1]?></td><td align="right"><?=number_format(100*$c[1]/$numhits,2)?></td></tr>
			<? } ?>			
		</table>
		<? 
		}
		if (get_option('jqst_referer_summary')=='show') {
		$ref = mysql_query($qref); ?>
		<h4>Referer Summary</h4>
		<table class="jqstable">
			<tr><th>Referer</th><th width="70"># Hits</th><th width="50">% Hits</th></tr>
			<? while ($c = mysql_fetch_row($ref)) { ?>
			<tr><td><?=$c[0]?></td><td align="right"><?=$c[1]?></td><td align="right"><?=number_format(100*$c[1]/$numhits,2)?></td></tr>
			<? } ?>			
		</table>
		<?
		}
		if (get_option('jqst_browser_summary')=='show') {
		$browser = mysql_query($qbrowser); ?>
		<h4>Browser Summary</h4>
		<table class="jqstable">
			<tr><th>Browser</th><th width="70"># Hits</th><th width="50">% Hits</th></tr>
			<? while ($c = mysql_fetch_row($browser)) { ?>
			<tr><td><?=$c[0]?></td><td align="right"><?=$c[1]?></td><td align="right"><?=number_format(100*$c[1]/$numhits,2)?></td></tr>
			<? } ?>			
		</table>
		<? 
		}
		if (get_option('jqst_last_visitors')=='show') {		
		$visit = mysql_query($qvisit); ?>
		<h4>Last Visitors</h4>
		<table class="jqstable">
			<tr><th width="110">Date</th><th>Host</th><th>URL</th></tr>
			<? while ($c = mysql_fetch_row($visit)) { ?>
			<tr><td><?=date("Y-m-d H:i",$c[4])?></td>
			<td><span class="moreinfo" title="<?=$c[2]?> <?=$c[5]?>"><?=$c[3]?></span></td>
			<td><span class="moreinfo" title="Referer: <?=$c[6]?>"><?=$c[1]?></span></td></tr>
			<? } ?>			
		</table>
		<? } ?>
	</div>	
	<?
}

function JQ_stat_options_subpanel() {
	checkinstall();
	if (isset($_POST['add_ip'])) {
		if (!preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/",$_POST['newip'])) {
		?><div class="error"><p><strong><?
		_e('Invalid IP!',
			'Localization name')
			?></strong></p></div><?
		} else {
			$filterIP = get_option("jqst_filterip");
			if (!is_array($filterIP)) $filterIP = Array();
			array_push($filterIP,$_POST['newip']);
			update_option("jqst_filterip",$filterIP);

			?><div class="updated"><p><strong><?
			_e('Added filtered IP',
				'Localization name')
				?></strong></p></div><?
		}
	} 
	if ($_POST['del']>0) {
		$filterIP = get_option("jqst_filterip");
		foreach ($filterIP as $key=>$value) {
			if (strlen($value)==0 || $_POST['del']==$value) unset($filterIP[$key]);
		}		
		update_option("jqst_filterip",$filterIP);
		?><div class="updated"><p><strong><? 
		_e('Deleted filtered IP',
			'Localization name')
			?></strong></p></div><?
	}
	if (isset($_POST['Submit'])) {
		update_option("jqst_maxhits",$_POST['max']);
		update_option("jqst_nostore_admin",$_POST['jqst_nostore_admin']);
		update_option("jqst_nostore_local_ref",$_POST['jqst_nostore_local_ref']);
		update_option("jqst_general_stats",$_POST['jqst_general_stats']);
		update_option("jqst_content_summary",$_POST['jqst_content_summary']);
		update_option("jqst_referer_summary",$_POST['jqst_referer_summary']);
		update_option("jqst_browser_summary",$_POST['jqst_browser_summary']);
		update_option("jqst_last_visitors",$_POST['jqst_last_visitors']);
		update_option("jqst_feed_enabled",$_POST['jqst_feed_enabled']);
		?><div class="updated"><p><strong><? 
		_e('Options saved',
			'Localization name')
			?></strong></p></div><?
	}	
	if (isset($_POST['generate'])) {
		$a = preg_split('#(?<=.)(?=.)#s', "1234567890");
		for ($i=0;$i<32;$i++) $magic_num .= array_rand($a);
		update_option("jqst_magic_number",$magic_num);
	}
	$filterIP = get_option("jqst_filterip"); 
	if (!is_array($filterIP)) $filterIP=Array();
	$maxhits = get_option("jqst_maxhits");
	if (!ctype_digit((string)$maxhits)||strlen($maxhits)==0) {
		$maxhits = 500;
		update_option("jqst_maxhits",$maxhits);
	}
	?>
	<style>
		table.jqstable {
			font-size: 8pt;
			background-color: #6da6d1;
			width: 100%;
		}
		.jqstable td{
			font-size: 8pt;
			background-color: #FFFFFF;
		}
		.jqstable th{
			text-align:left;
	</style>	
	<script>
		function delfip(f,t) {
			f.del.value = t;
			f.submit();
		}
	</script>	
	<div class=wrap>
		<h2>Quick Stats Options</h2>
		<form method="post">
		<input type="hidden" name="del">
		<fieldset class="options">
			<legend>Logging</legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				<tr valign="top"> 
				<th width="33%" scope="row">Store maximum:</th> 
				<td><input type="text" name="max" value="<?form_option('jqst_maxhits');?>" size="3"> hits</td>
				</tr>

				<tr valign="top">
				<th width="33%" scope="row">Do not store hits for:</th> 
				<td><label for="nostore_admin">
				<input name="jqst_nostore_admin" type="checkbox" id="nostore_admin" value="true" <?php checked('true', get_settings('jqst_nostore_admin')); ?> />
				<?php _e('Administrative Pages') ?></label><br />
				<label for="nostore_local_ref">
				<input name="jqst_nostore_local_ref" type="checkbox" id="nostore_local_ref" value="true" <?php checked('true', get_settings('jqst_nostore_local_ref')); ?> />
				<?php _e('Local Referals') ?></label></td>
				</tr>
			</table>
			<legend>Statistics page</legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				<tr valign="top">
				<th width="33%" scope="row">Show:</th> 
				<td><label for="general_stats">
				<input name="jqst_general_stats" type="checkbox" id="general_stats" value="show" <?php checked('show', get_settings('jqst_general_stats')); ?> />
				<?php _e('General Statistics') ?></label><br />
				<label for="content_summary">
				<input name="jqst_content_summary" type="checkbox" id="content_summary" value="show" <?php checked('show', get_settings('jqst_content_summary')); ?> />
				<?php _e('Content Summary') ?></label><br />
				<label for="referer_summary">
				<input name="jqst_referer_summary" type="checkbox" id="referer_summary" value="show" <?php checked('show', get_settings('jqst_referer_summary')); ?> />
				<?php _e('Referer Summary') ?></label><br />
				<label for="browser_summary">
				<input name="jqst_browser_summary" type="checkbox" id="browser_summary" value="show" <?php checked('show', get_settings('jqst_browser_summary')); ?> />
				<?php _e('Browser Summary') ?></label><br />
				<label for="last_visitors">
				<input name="jqst_last_visitors" type="checkbox" id="last_visitors" value="show" <?php checked('show', get_settings('jqst_last_visitors')); ?> />
				<?php _e('Last Visitors') ?></label></td>
				</tr>
			</table>
			<legend>Hit Filter</legend>
			<p>Hits from filtered IPs are not recorded.</p>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				<tr valign="top">
				<th width="33%" scope="row">Filter IP:</th> 
				<td><input type="text" name="newip"  />
				<input type="submit" name="add_ip" value="Add" />
				</td>
				</tr>
				<tr valign="top">
				<th width="33%" scope="row">Filtered IPs:</th> 
				<td>
				<? 
				if (count($filterIP)>0) { 
				foreach($filterIP as $f) { ?>
					<input type="button" value="Remove" onClick="delfip(this.form,'<?=$f?>');">
					<?=$f?><br />
				<? } } else { ?>
					None
				<? } ?>
				</td>
			</table>
			<legend>RSS Feed &nbsp;<a href="../wp-content/plugins/quickstats/rss.php?magic=<?=form_option('jqst_magic_number')?>"><img src="../wp-content/plugins/quickstats/rss.png"></a></legend>
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				<tr valign="top"> 
				<th width="33%" scope="row">Feed Enabled:</th> 
				<td><input name="jqst_feed_enabled" type="checkbox" id="feed_enabled" value="yes" <?php checked('yes', get_settings('jqst_feed_enabled')); ?> /></td>
				</tr>
				<tr valign="top"> 
				<th width="33%" scope="row">Magic Number:</th> 
				<td><input type="text" name="magic" value="<?form_option('jqst_magic_number');?>" size="35">
				<input type="submit" name="generate" value="Generate"></td>
				</tr>
			</table>
		</fieldset>
		<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options') ?> &raquo;" /> 
		</p>			
		</form>
	</div>
	<?
}

function JQ_updateStats()
{
	$urlOwnDomain 	= get_option("siteurl")."/";
	$filterIP 	= get_option("jqst_filterip");
	$maxhits 	= get_option("jqst_maxhits");
	if (!is_array($filterIP)) $filterIP = Array();
	if (!ctype_digit((string)$maxhits)||strlen($maxhits)==0) {
		$maxhits = 500;
		update_option("jqst_maxhits",$maxhits);
	}

	$url 		= $_SERVER['REQUEST_URI'];
	$ipaddress	= $_SERVER['REMOTE_ADDR'];
	$host    	= gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$date 		= time();
	$browser	= $_SERVER['HTTP_USER_AGENT'];
	$referer	= $_SERVER['HTTP_REFERER'];
	$domain   	= parse_url($_SERVER['HTTP_REFERER']);
	$owndomain	= parse_url($urlOwnDomain);
	$is_filtered = in_array($ipaddress, $filterIP);
	$is_local_ref = ($domain[host] == $owndomain[host]) && (get_option("jqst_nostore_local_ref") == 'true');
	$is_administrative = strpos($url,'wp-admin/') && (get_option("jqst_nostore_admin") == 'true');
	$is_qsrss = strpos($url,'/rss.php?magic');
	if(!$is_filtered && !$is_local_ref && !$is_administrative && !$is_qsrss)
	{
		$query = "INSERT INTO wp_jqstats (ip,url,host,date,browser,referer,domain)
			VALUES ('$ipaddress','$url','$host','$date','$browser','$referer','$domain[host]')";
		$result = mysql_query($query) or checkinstall();
		$res = mysql_fetch_row(mysql_query("SELECT max(id) FROM `wp_jqstats`"));
		if ($res[0]>$maxhits) mysql_query("DELETE FROM `wp_jqstats` WHERE id<=".($res[0]-$maxhits));
	}
}

function checkinstall() {
	if (get_option('jqst_v1_installed')!='true') {
		mysql_query("CREATE TABLE `wp_jqstats` ("
			."`id` int(100) NOT NULL auto_increment,"
			."`url` varchar(150) NOT NULL default '',"
			."`ip` varchar(40) NOT NULL default '',"
			."`host` varchar(250) NOT NULL default '',"
			."`date` varchar(15) NOT NULL default '',"
			."`browser` varchar(200) NOT NULL default '',"
			."`time` varchar(20) NOT NULL default '',"
			."`referer` varchar(255) NOT NULL default '',"
			."`domain` varchar(255) default NULL,"
			."PRIMARY KEY  (`id`)"
			.")"
			) or die("Cannot create QuickStats table. Database user does not have CREATE priviledge.");
		update_option('jqst_nostore_admin','true');
		update_option('jqst_browser_summary','show');
		update_option('jqst_referer_summary','show');
		update_option('jqst_content_summary','show');
		update_option('jqst_last_visitors','show');
		update_option('jqst_maxhits','500');
		update_option('jqst_v1_installed','true');
	}
	if (strlen(get_option('jqst_magic_number'))!=32) {
		$a = preg_split('#(?<=.)(?=.)#s', "1234567890");
		for ($i=0;$i<32;$i++) $magic_num .= array_rand($a);
		update_option("jqst_magic_number",$magic_num);		
	}
}
add_action('shutdown', JQ_updateStats);
