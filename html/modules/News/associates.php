<?php
if(!strpos((string) $_SERVER['PHP_SELF'], 'admin.php')) {
	#show right panel:
	define('INDEX_FILE', true);
}
/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2023 by Francisco Burzi                                */
/* http://www.phpnuke.coders.exchange                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/* Applied rules:
 * ListToArrayDestructRector (https://wiki.php.net/rfc/short_list_syntax https://www.php.net/manual/en/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring)
 * NullToStrictStringFuncCallArgRector
 */
 
if (!defined('MODULE_FILE')) {
	die("You can't access this file directly...");
}

require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$sid = intval($sid);
$query = $db->sql_query("SELECT associated FROM ".$prefix."_stories WHERE sid='$sid'");
[$associated] = $db->sql_fetchrow($query);

if (!empty($associated)) {
	OpenTable();
	echo "<center><b>"._ASSOTOPIC."</b><br><br>";
	$asso_t = explode("-",(string) $associated);
	for ($i=0; $i<sizeof($asso_t); $i++) {
		if (!empty($asso_t[$i])) {
		        $query = $db->sql_query("SELECT topicimage, topictext from ".$prefix."_topics WHERE topicid='".$asso_t[$i]."'");
			[$topicimage, $topictext] = $db->sql_fetchrow($query);
			echo "<a href=\"modules.php?name=$module_name&new_topic=$asso_t[$i]\"><img src=\"".$tipath."/".$topicimage."\" border=\"0\" hspace=\"10\" alt=\"".$topictext."\" title=\"".$topictext."\"></a>";
		}
	}
	echo "</center>";
	CloseTable();
	echo "<br>";
}

?>