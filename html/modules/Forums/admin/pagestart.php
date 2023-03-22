<?php

/***************************************************************************
 *                               pagestart.php
 *                            -------------------
 *   begin                : Thursday, Aug 2, 2001
 *   copyright            : (C) 2001 The phpBB Group
 *   email                : support@phpbb.com
 *
 *   Id: pagestart.php,v 1.1.2.10 2006/01/22 17:11:09 grahamje Exp $
 *
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
 
/* Applied rules:
 * NullToStrictStringFuncCallArgRector
 */

if (!defined('IN_PHPBB'))
{
        die("Hacking attempt");
}

define('IN_ADMIN', true);
define('FORUM_ADMIN', true);
define("PHPBB_PHPEX", $phpEx);
include("../../../mainfile.php");
$phpbb_root_path = './../';
$phpEx = PHPBB_PHPEX;
include($phpbb_root_path.'common.'.$phpEx);

include(NUKE_BASE_DIR.'ips.php');

if(isset($ips) && is_array($ips)) {
    $ip_check = implode('|^',$ips);
    if (!preg_match("/^".$ip_check."/",$_SERVER['REMOTE_ADDR']))
    {
        unset($aid);
        unset($admin);
        global $cookie;
        $name = (isset($cookie[1]) && !empty($cookie[1])) ? $cookie[1] : _ANONYMOUS;
        log_write('admin', $name.' used invalid IP address attempted to access the forum admin area', 'Security Breach');
        die('Invalid IP<br />Access denied');
    }
    define('ADMIN_IP_LOCK',true);
}
//
// Do a check to see if the nuke user is still valid.
//

global $admin, $userdata, $prefix, $db, $cookie, $nukeuser, $user;
$admin = base64_decode((string) $admin);
$admin = explode(":", $admin);
$aid = "$admin[0]";
$row = $db->sql_fetchrow($db->sql_query("SELECT title, admins FROM ".$prefix."_modules WHERE title='Forums'"));
$row2 = $db->sql_fetchrow($db->sql_query("SELECT name, pwd, radminsuper FROM ".$prefix."_authors WHERE aid='$aid'"));
$admins = explode(",", (string) $row['admins']);
$auth_user = 0;
for ($i=0; $i < sizeof($admins); $i++) {
    if ($row2['name'] == "$admins[$i]" AND $row['admins'] != "") {
        $auth_user = 1;	
    }
}



$user = addslashes(base64_decode((string) $user));

if(!isset($cookie)) {
  $cookie = explode(":", $user);
}

$un = "SELECT `username` FROM `".USERS_TABLE."` WHERE username='$cookie[1]' ";

$result3 = $db->sql_query($un);

if(!$result3) {
    message_die(GENERAL_ERROR, 'Could not query user account', '', __LINE__, __FILE__, $sql);
}
$row3 = $db->sql_fetchrow($result3);
if ((is_admin()) AND ($admin[1] == $row2['pwd'] && !empty($row2['pwd'])) AND ($row2['radminsuper'] == 1 or $auth_user == 1)) {
} elseif ((is_user()) AND ($cookie[2] == $row3['user_password'] && $row3['user_password'] != "") AND ($row3['user_level'] == 2)) {
    $nukeuser = $user;
} else {
    unset($user);
    unset($cookie);
    message_die(GENERAL_MESSAGE, "You are not authorised to administer this board");
}

//
// Start session management
//
$userdata = session_pagestart($user_ip, PAGE_INDEX, $nukeuser);
init_userprefs($userdata);
if ( empty($no_page_header) )
{
        // Not including the pageheader can be neccesarry if META tags are
        // needed in the calling script.
        include('./page_header_admin.'.$phpEx);
}

