<?php

/************************************************************************/
/* PHP-NUKE: Advanced Content Management System                         */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2023 by Francisco Burzi                                */
/* http://www.phpnuke.coders.exchange                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/**
 * Get a user's field from the DB
 * This function is called by mainfile.php to fill $userinfo
 * and from other files to get specific informations about an user
 * it makes no sense to cache all users (maybe this can be thousands)
 * but for actual page we can make the informations static
 * @author(s) JeFFb68CAM, ReOrGaNiSaTiOn, Ernest Allen Buffington
 * @date 3/17/2023 1:02 PM
 * @param string $field_name The field to retrieve
 * @param string $user Username or User_id
 * @param bool $is_name Is the $user a username
 * @return string
 */
if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    exit('Access Denied');
}

function get_user_field($field_name, $user, $is_name = false) 
{
    global $db, $identify;
    static $actual_user = [];
    $data = [];
	
	if (!$user) {
	return null;
	}

    if ($is_name || !is_numeric($user)) {
        $where  = "`username` = '". str_replace("\'", "''", $user)."'";
        $search = 'username';
    } else {
        $where  = "`user_id` = '".$user."'";
        $search = 'user_id';
    }
    
	if (!isset($actual_user[$user])) {
        $sql = "SELECT * FROM ".USERS_TABLE." WHERE $where";
        $actual_user[$user] = $db->sql_ufetchrow($sql);
		
		if(!isset($actual_user[$user]['user_id']))
		$actual_user[$user]['user_id'] = 1;
        // We also put the groups data in the array.
        $result = $db->sql_query('SELECT g.group_id, 
		                               g.group_name, 
								g.group_single_user 
								  
								  FROM ('.GROUPS_TABLE.' AS g 
								  INNER JOIN '.USER_GROUP_TABLE.' 
								  AS ug ON (ug.group_id=g.group_id AND ug.user_id="'.$actual_user[$user]['user_id'].'" 
								  AND ug.user_pending=0))', true);
								  
        while(list($g_id, $g_name, $single) = $db->sql_fetchrow($result)) {
            $actual_user[$user]['groups'][$g_id] = ($single) ? '' : $g_name;
        }
        $db->sql_freeresult($result);
    }
    if($field_name == '*') {
        $actual_user[$user]['user_ip'] = $identify->get_ip();
        return $actual_user[$user];
    }
    if(is_array($field_name)) {
        $data = array();
        foreach($field_name as $fld) {
            $data[$fld] = $actual_user[$user][$fld];
        }
        return $data;
    }
      return $actual_user[$user][$field_name] ?? '';
}

/**
 * Gets a admin field from the DB
 *
 * @author(s) JeFFb68CAM, Ernest Allen Buffington
 * @date 3/17/2023 1:08 PM
 * @param string $field_name The field to get
 * @param string $admin The admin name/aid
 * @return string
 */
function get_admin_field($field_name, $admin) 
{
    global $db, $debugger;
	static $fields = [];
    
	if (!$admin) {
      return [];
    }

    if(!isset($fields[$admin]) || !is_array($fields[$admin])) {
        $fields[$admin] = $db->sql_ufetchrow("SELECT * FROM "._AUTHOR_TABLE." WHERE `aid` = '" .  str_replace("\'", "''", (string) $admin) . "'");
    }

    if($field_name == '*') {
        return $fields[$admin];
    }
    
	if(is_array($field_name)) {
        $data = [];

        foreach($field_name as $fld) {
            $data[$fld] = $fields[$admin][$fld];
        }
        return $data;
    }
    
	return $fields[$admin][$field_name] ?? '';
}
/**
 * Checks to see if a user is a module admin
 *
 * @author(s) Quake, Ernest Allen Buffington
 * @date 3/17/2023 1:12 AM
 * @param string $module_name Module name
 * @return bool
 */
function is_mod_admin($module_name='super') 
{

    global $db, $aid, $admin;
    static $auth = array();

    if(!is_admin()) {
	  return 0;
	}
    
	if(isset($auth[$module_name])) {
	  return $auth[$module_name];
	}

    if(!isset($aid)) {
        if(!is_array($admin)) {
            $aid = base64_decode($admin);
            $aid = explode(":", $aid);
            $aid = $aid[0];
        } else {
            $aid = $admin[0];
        }
    }
    $admdata = get_admin_field('*', $aid);
    $auth_user = 0;
    if($module_name != 'super') {
        list($admins) = $db->sql_ufetchrow("SELECT `admins` FROM "._MODULES_TABLE." WHERE `title`='$module_name'");
		$adminarray = explode(',', $admins ?? '');
        for ($i=0, $maxi=count($adminarray); $i < $maxi; $i++) {
            if ($admdata['aid'] == $adminarray[$i] && !empty($admins)) {
                $auth_user = 1;
            }
        }
    }
    $auth[$module_name] = ($admdata['radminsuper'] == 1 || $auth_user == 1);
    return $auth[$module_name];

}
/**
 * Get all admins for a module
 *
 * @author(s) Quake, ReOrGaNiSaTiOn, Ernest Allen Buffington (based on is_mod_admin from Quake)
 * @date 3/17/2023
 * @param string $module_name Module name
 * super = only Superuser
 * module_name = only Admins with privileges for this module
 * all with module_name = Superuser + Module-Admins
 * @return array of admin-names with email-address by default only Superuser
 */
function get_mod_admins($module_name='super', $all='') 
{

    global $db;
    static $admins = array();

    if ( $all == '') {
        if(isset($admins[$module_name])) {
		  return $admins[$module_name];
		}
    }

    if($module_name == 'super' || $all != '') {
        $result1 = $db->sql_query("SELECT `aid`, `email` FROM `"._AUTHOR_TABLE."` WHERE `radminsuper`='1'");
        $num = 0;
        while (list($admin, $email) = $db->sql_fetchrow($result1)) {
            $admins[$module_name][$num]['aid'] = $admin;
            $admins[$module_name][$num]['email'] = $email;
            $num++;
        }
        $db->sql_freeresult($result1);
    }

    if($module_name != 'super') {
        list($admin) = $db->sql_ufetchrow("SELECT `admins` FROM `"._MODULES_TABLE."` WHERE `title`='".$module_name."'");
        $adminarray = explode(",", $admin ?? '');
        $num = ($all !='') ? $num : 0;
        for ($i=0, $maxi=count($adminarray); $i < $maxi; $i++) {
            $row = $db->sql_fetchrow($db->sql_query("SELECT `aid`, `email` FROM `"._AUTHOR_TABLE."` WHERE `aid`='".$adminarray[$i]."'"));
            if (!empty($row['aid'])) {
                $admins[$module_name][$num]['aid'] = $row['aid'];
                $admins[$module_name][$num]['email'] = $row['email'];
            }
            $num++;
        }
    }
    return $admins[$module_name];
}
