<?php
	/**************************************************************************\
	* Simple Groupware 0.743                                                   *
	* http://www.simple-groupware.de                                           *
	* Copyright (C) 2002-2012 by Thomas Bley                                   *
	* ------------------------------------------------------------------------ *
	\**************************************************************************/

define("NOCONTENT",true);

require("index.php");
@set_time_limit(1800);

if (!sys_is_super_admin($_SESSION["username"])) sys_die("Not allowed. Please log in as super administrator.");

setup::out('
<html>
<head>
<title>Simple Groupware</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
  body, h2, img, div, table.data, a {
	background-color: #FFFFFF; color: #666666; font-size: 13px; font-family: Arial, Helvetica, Verdana, sans-serif;
  }
  a,input { color: #0000FF; }
  input {
	font-size: 11px; background-color: #F5F5F5; border: 1px solid #AAAAAA; height: 18px;
	vertical-align: middle; padding-left: 5px; padding-right: 5px; border-radius: 10px;
  }
  .checkbox { border: 0px; background-color: transparent; }
  .submit { color: #0000FF; background-color: #FFFFFF; width: 125px; font-weight: bold; }
  .border {
    border-bottom: 1px solid black;
  }
  .headline {
	letter-spacing: 2px;
	font-size: 18px;
	font-weight: bold;
  }
</style>
</head>
<body>
<div class="border headline">Simple Groupware '.CORE_VERSION_STRING.'</div>
');
setup::out("<a href='index.php'>Back</a><br>");


$folders = array("../","../old/","../docs/","../lang/","../import/","../src/","../bin/");
foreach ($folders as $folder) {
  if (!is_writable($folder)) setup::out_exit(sprintf("[1] Please give write access to %s",$folder));
}

$temp_folder = SIMPLE_CACHE."/update_to_php74/";
sys_mkdir($temp_folder);

if (empty($_REQUEST["install"])) {
	
  setup::out("
	<div style='color:#ff0000;'>
	<b>Warning</b>:<br><br>
	<strong>- Update SGS v0.745 from PHP 5.3.29 to PHP 7.4</strong><br><br>

	- Please make a complete backup of your database (e.g. using phpMyAdmin)<br>
	- Please make a complete backup of your sgs folder (e.g. /var/www/htdocs/sgs/)<br>
	- Make sure both backups are complete!<br><br>

	<strong>- Unpack latest archive from <a href='http://simplegroupware-lab.github.io/0.745p/'>http://simplegroupware-lab.github.io/0.745p</a> to folder simple_cache/update_php74!</strong><br>
    </div>
  ");

  setup::out("<br>Click here to <a href='update_v0745_to_php74.php?install=true'>C O N T I N U E</a><finished>");
  setup::out();
  setup::out_exit('<div style="border-top: 1px solid black;">Powered by Simple Groupware.</div></div></body></html>');
}


setup::out();

// adjust access permissions to files and folders
$fileiterator = new RecursiveDirectoryIterator($temp_folder);
foreach(new RecursiveIteratorIterator($fileiterator) as $file) {
	sys_chmod((string)$file);
}


chdir("../old/");

$base = "../";
setup::out(sprintf("Processing %s ...","Folders"));
$folders = array("src","bin","lang","import","docs");
foreach ($folders as $folder) {
  if (file_exists($base.$folder."/") and !file_exists($base."old/".$folder."_".CORE_VERSION."/")) {
    if (!empty($_REQUEST["nobackup"])) {
	  dirs_delete_all($base.$folder."/");
	} else {
	  rename($base.$folder."/",$base."old/".$folder."_".CORE_VERSION."/");
} } }
if (is_dir($base."src/") or is_dir($base."bin/")) sys_die("Error: rename [4]");

$source_folder = $temp_folder;
foreach ($folders as $folder) {
  if (is_dir($source_folder.$folder."/") and !is_dir($base.$folder."/")) {
    rename($source_folder.$folder."/",$base.$folder."/");
  }
}
if (!is_dir($base."src/") or !is_dir($base."bin/")) sys_die("Error: rename [5]");

dirs_delete_all($source_folder);

setup::out(sprintf("Processing %s ...","config.php"));

$old = SIMPLE_STORE."/config_old.php";
if (file_exists($old)) rename($old,SIMPLE_STORE."/config_".time().".php");
rename(SIMPLE_STORE."/config.php",$old);
touch($old);

setup::out(sprintf("Processing %s ...","translations"));
setup::build_trans(SETUP_LANGUAGE,"../src/","../bin/");

chdir("../bin/");
setup::out(sprintf("Processing %s ...","customizations"));
setup::build_customizing(SIMPLE_CUSTOM."customize.php");

$dir = opendir(SIMPLE_EXT);
while (($file=readdir($dir))) {
  if ($file!="." and $file!=".." and file_exists(SIMPLE_EXT.$file."/update.php")) {
    setup::out(sprintf("Processing %s ...",SIMPLE_EXT.$file."/update.php"));
	require(SIMPLE_EXT.$file."/update.php");
  }
}

setup::out("
	<div style='color:#ff0000;'>
	<b><br><br>Warning</b>:<br><br>
	<strong>- NOW switch your php environement to PHP 7.4 BEFORE CONTINUING!!</strong><br><br>
	- and then call SGS normally or click CONTINUE below<br><br>
	
    </div>"
);

setup::out("<br><a href='index.php'>C O N T I N U E</a><br>");
setup::out_exit('<div style="border-top: 1px solid black;">Powered by Simple Groupware.</div></div></body></html>');


