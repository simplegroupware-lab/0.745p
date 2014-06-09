<?php
/**
 * Simple Groupware
 * http://www.simple-groupware.de
 * Copyright (C) 2002-2011 by Thomas Bley
 *
 *
 * Archive_Tar 1.3.7
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 1997-2008,
 * Vincent Blavet <vincent@phpconcept.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @license     http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link        http://pear.php.net/package/Archive_Tar
 */

@set_time_limit(1800);
if (!ini_get('display_errors')) @ini_set('display_errors','1');

header("Content-Type: text/html; charset=utf-8");
header("Cache-Control: private, max-age=1, must-revalidate");
header("Pragma: no-cache");
out_header();

$phpversion = "5.2.0";
if (version_compare(PHP_VERSION, $phpversion, "<")) {
  out_exit(sprintf("Setup needs php with at least version %s ! (".PHP_VERSION.")",$phpversion),3);
}

$extensions_sys = get_loaded_extensions();
foreach(array("pcre", "zlib") as $key => $val) {
  if (in_array($val, $extensions_sys)) continue;
  out_exit(sprintf("[0] Setup needs php-extension with name %s !", $val));
}

$memory_min = 4000000;
$memory_min_str = str_replace("000000","M",$memory_min);
$memory = ini_get("memory_limit");
if (!empty($memory)) {
  $memory_int = (int)str_replace("m","000000",strtolower($memory));
  if ($memory_int < $memory_min) out_exit(sprintf("[1] Please modify your php.ini or add an .htaccess file changing the setting '%s' to '%s' (current value is '%s') !", "memory_limit", $memory_min_str, $memory));
}

$settings = array("safe_mode"=>0);
foreach($settings as $key => $val) {
  $setting = ini_get($key);
  if ($setting != $val) out_exit(sprintf("[2] Please modify your php.ini or add an .htaccess file changing the setting '%s' to '%s' (current value is '%s') !", $key, $val, $setting));
}

if (is_dir("./simple_cache/") and !is_dir("./simple_store/")) dirs_delete_all("./simple_cache/");

clearstatcache();
$exclude = array("sgs_installer.php","simple_store","build","README.txt","license.txt");
if (($dh = opendir("./"))) {
  while (($file = readdir($dh)) !== false) {
  	if (!in_array($file[0],array(".","_")) and !in_array($file,$exclude) and !strpos($file,".tar.gz")) {
	  out_exit(sprintf("[3] Please remove the folder %s", realpath($file)));
} } }
if (!is_writable("./")) {
  $message = sprintf("[4] Please give write access to %s",realpath("./"));
  if (strpos(PHP_OS,"WIN")===false) {
	$message .= "<br>".sprintf("If file system permissions are ok, please check the configs of %s if present.", "SELinux, suPHP, Suhosin");
  }
  out_exit($message);
}

if (empty($_REQUEST["release"]) and empty($_REQUEST["cfile"])) {
  out("Downloading list ...<br/>");
  $url = "http://sourceforge.net/export/rss2_projnews.php?group_id=96330";
  $ctx = stream_context_create(array("http" => array("timeout" => 5))); 
  $data = @file_get_contents($url,0,$ctx);
  if ($data!="" and strpos($data, "Simple Groupware")) {
	preg_match_all("!<title>simple groupware ([^ ]+) released.*?</title>.*?<pubdate>([^<]+)!msi", $data, $match);
  } else {
	$url = "http://code.google.com/feeds/p/simplegroupware/downloads/basic";
	$data = @file_get_contents($url);
	preg_match_all("!simplegroupware_(.+?)\.tar\.gz.+?<updated>([^<]+)!msi", $data, $match);
  }

  if (!empty($match[1]) and $data!="") {
    foreach ($match[1] as $key=>$item) {

	  if ($key > 3) break;
	  if (!empty($match[3][$key]) and strtotime($match[3][$key])+3600 > time()) continue;
	  $check = true;
	  
	  if (!empty($match[2][$key])) {
		preg_match("/php (\d+\.\d+\.\d+)/i", $match[2][$key], $match2);
		if (!empty($match2[1]) and version_compare(PHP_VERSION, $match2[1], "<")) {
	      out(sprintf("Setup needs php with at least version %s !", $match2[1]));
		  $check = false;
		}
	  }
	  if ($check) {
		out("<a href='sgs_installer.php?release=".$item."'>I n s t a l l</a> ", false);
		out("(<a href='sgs_installer.php?mirror&release=".$item."'>Mirror</a>) Simple Groupware ", false);
	  }
	  out($item." (<a target='_blank' href='http://www.simple-groupware.de/cms/Release-".str_replace(".","-",$item)."'>Changelog</a>)<br>");
	}
  } else {
    out("Error: connection failed ".$url."<br/>".strip_tags($data,"<br><p><h1><center>"));
  }
  out("<br/>Package from local file system (.tar.gz):<br/>current path: ".str_replace("\\","/",getcwd())."/<br/>");
  
  $dir = opendir("./");
  while (($file=readdir($dir))) {
    if ($file!="." and $file!=".." and preg_match("|^SimpleGroupware\_.*?.tar\.gz\$|i",$file)) {
	  out("<a href='sgs_installer.php?cfile=".$file."'>I n s t a l l</a>&nbsp; ".$file."<br/>");
	}
  }
  closedir($dir);

  out("<form method='GET'><input type='text' name='cfile' value='/tmp/SimpleGroupware_0.xyz.tar.gz' style='width:300px;'>&nbsp;
	   <input type='submit' class='submit' value='I n s t a l l'></form>");
  out_footer();   
  exit;
} else if (!empty($_REQUEST["cfile"])) {
  $source = $_REQUEST["cfile"];
  if (!file_exists($source) or filesize($source) < 3*1024*1024) out_exit("[5] Error: file-check [0] ".$source);
} else {
  $source = "http://sourceforge.net/projects/simplgroup/files/simplegroupware/{$_REQUEST["release"]}/SimpleGroupware_{$_REQUEST["release"]}.tar.gz/download";
  if (isset($_REQUEST["mirror"])) {
	$source = "http://simplegroupware.googlecode.com/files/SimpleGroupware_{$_REQUEST["release"]}.tar.gz";
  }
}

$temp_folder = "simple_cache/installer/";
sys_mkdir($temp_folder);
if (!is_dir($temp_folder)) out_exit(sprintf("[4b] Please give write access to %s",realpath("./")));

$target = $temp_folder."SimpleGroupware.tar";

out("Download: ".$source." ...");
if ($fz = gzopen($source,"r") and $fp = fopen($target,"w")) {
  $i = 0;
  while (!gzeof($fz)) {
    $i++;
    out(".",false);
	if ($i % 160 == 0) out();
    fwrite($fp,gzread($fz, 16384));
  }
  gzclose($fz);
  fclose($fp);
} else out_exit("[6] Error: gzopen [1] ".$source);

out();
if (!file_exists($target) or filesize($target) < 5*1024*1024) {
  out_exit("[7] Error: file-check [2] ".$target);
}

out(sprintf("Processing %s ...",basename($target)));

$tar_object = new Archive_Tar($target);
$tar_object->extract($temp_folder);

$file_list = $tar_object->ListContent();
if (!is_array($file_list) or !isset($file_list[0]["filename"]) or !is_dir($temp_folder.$file_list[0]["filename"])) out_exit("[7] Error: tar [3] ".$target);
foreach ($file_list as $file) sys_chmod($temp_folder.$file["filename"]);
@unlink($target);

$base = "./";
out(sprintf("Processing %s ...","Folders"));

$path = $temp_folder.$file_list[0]["filename"];
$dir = opendir($path);
while (($file=readdir($dir))) {
  if (!in_array($file,array(".","..","simple_cache"))) rename($path.$file,$base.$file);
}
dirs_delete_all($path);

out("<br/><a href='index.html'>C O N T I N U E</a><br/>");
out_footer();
if (function_exists("memory_get_usage") and function_exists("memory_get_peak_usage")) {
  out("<!-- ".memory_get_usage()." - ".memory_get_peak_usage()." -->");
}

function out($str="",$nl=true) {
  echo str_replace(array("{t"."}","{"."/t}"),array("",""),$str);
  if ($nl) echo "<br/>\n";
  flush();
  @ob_flush();
}

function out_exit($str) {
  out($str);
  out("<br/><a href='sgs_installer.php'>Relaunch Installer</a><br/>");
  out_footer();
  exit;
}

function out_header() {
  out("<html><head>
	<title>Simple Groupware Installer</title>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<style>
	body, h2, img, div, table.data, a { background-color: #FFFFFF; color: #666666; font-size: 13px; font-family: Arial, Helvetica, Verdana, sans-serif; }
	a,input { color: #0000FF; }
	input { font-size: 11px; background-color: #F5F5F5; border: 1px solid #AAAAAA; height: 18px; vertical-align: middle; padding-left: 5px; padding-right: 5px;
			-moz-border-radius:10px; -webkit-border-radius:8px; border-radius:10px; }
	.checkbox, .radio { border: 0px; background-color: transparent; }
	.submit { color: #0000FF; background-color: #FFFFFF; width: 125px; font-weight: bold; }
	.border { border-bottom: 1px solid black; }
	.headline { letter-spacing: 2px; font-size: 18px; font-weight: bold; }
	form { margin: 0px; }
	</style></head>
	<body>
	<div class='border headline'>Simple Groupware Installer v8</div>
  ");
}
function out_footer() {
  out("<div style='border-top: 1px solid black; padding-top:2px;'>COPYRIGHT |
	   <a href='http://www.simple-groupware.de/cms/Main/Installation' target='_blank'>Installation manual</a> |
	   <a href='http://www.simple-groupware.de/cms/Main/Documentation' target='_blank'>Documentation</a> |
	   <a href='http://www.simple-groupware.de/cms/Main/FAQ' target='_blank'>FAQ</a>
	   </div></div></body></html>", false);
}

function sys_mkdir($dir) {
  $old_umask = @umask(0);
  $result = @mkdir($dir,0777,true);
  @umask($old_umask);
  return $result;
}

function sys_chmod($file_dir) {
  if (is_dir($file_dir)) $mode = 0777; else $mode = 0666;
  chmod($file_dir, $mode);
}

function dirs_delete_all($path,$olderthan=0,$remove=true) {
  $my_dir = opendir($path);
  while (($file=readdir($my_dir))) {
    if ($file!="." and $file!="..") {
	  if (is_dir($path."/".$file)) {
		dirs_delete_all($path."/".$file,$olderthan);
	  } else {
	    if (file_exists($path."/".$file) and filectime($path."/".$file)+$olderthan < time()) @unlink($path."/".$file);
  } } }
  closedir($my_dir);
  if ($remove) @rmdir($path);
}

/*
 +----------------------------------------------------------------------+
 | Archive_Tar 1.3.7                                                    |
 |----------------------------------------------------------------------+
 | Author: Vincent Blavet <vincent@phpconcept.net>                      |
 +----------------------------------------------------------------------+
*/
define ('ARCHIVE_TAR_ATT_SEPARATOR', 90001);
define ('ARCHIVE_TAR_END_BLOCK', pack("a512", ''));
class Archive_Tar
{
	var $_tarname='';
	var $_compress=false;
	var $_compress_type='none';
	var $_separator=' ';
	var $_file=0;
	var $_temp_tarname='';
	var $_ignore_regexp='';
	function __construct($p_tarname, $p_compress = null) {
		
		$this->_compress = false;
		$this->_compress_type = 'none';
		if (($p_compress === null) || ($p_compress == '')) {
			if (@file_exists($p_tarname)) {
				if (($fp = @fopen($p_tarname, "rb"))) {
					$data = fread($fp, 2);
					fclose($fp);
					if ($data == "\37\213") {
						$this->_compress = true;
						$this->_compress_type = 'gz';
					} elseif ($data == "BZ") {
						$this->_compress = true;
						$this->_compress_type = 'bz2';
					}
				}
			} else {
				if (substr($p_tarname, -2) == 'gz') {
					$this->_compress = true;
					$this->_compress_type = 'gz';
				} elseif ((substr($p_tarname, -3) == 'bz2') ||
						  (substr($p_tarname, -2) == 'bz')) {
					$this->_compress = true;
					$this->_compress_type = 'bz2';
				}
			}
		} else {
			if (($p_compress === true) || ($p_compress == 'gz')) {
				$this->_compress = true;
				$this->_compress_type = 'gz';
			} else if ($p_compress == 'bz2') {
				$this->_compress = true;
				$this->_compress_type = 'bz2';
			} else {
				$this->_error("Unsupported compression type '$p_compress'\n".
					"Supported types are 'gz' and 'bz2'.\n");
				return;
			}
		}
		$this->_tarname = $p_tarname;
		if ($this->_compress) { // assert zlib or bz2 extension support
			if ($this->_compress_type == 'gz')
				$extname = 'zlib';
			else if ($this->_compress_type == 'bz2')
				$extname = 'bz2';
			if (!extension_loaded($extname)) {
				$this->_error("The extension '$extname' couldn't be found.\n".
					"Please make sure your version of PHP was built ".
					"with '$extname' support.\n");
				return;
			}
		}
	}
	function _Archive_Tar() {
		$this->_close();
		if ($this->_temp_tarname != '')
			@unlink($this->_temp_tarname);
		
	}
	function extract($p_path='') {
		return $this->extractModify($p_path, '');
	}
	function listContent() {
		$v_list_detail = array();
		if ($this->_openRead()) {
			if (!$this->_extractList('', $v_list_detail, "list", '', '')) {
				unset($v_list_detail);
				$v_list_detail = 0;
			}
			$this->_close();
		}
		return $v_list_detail;
	}
	function extractModify($p_path, $p_remove_path) {
		$v_result = true;
		$v_list_detail = array();
		if (($this->_openRead())) {
			$v_result = $this->_extractList($p_path, $v_list_detail,
											"complete", 0, $p_remove_path);
			$this->_close();
		}
		return $v_result;
	}
	function extractList($p_filelist, $p_path='', $p_remove_path='') {
		$v_result = true;
		$v_list_detail = array();
		if (is_array($p_filelist))
			$v_list = $p_filelist;
		elseif (is_string($p_filelist))
			$v_list = explode($this->_separator, $p_filelist);
		else {
			$this->_error('Invalid string list');
			return false;
		}
		if (($v_result = $this->_openRead())) {
			$v_result = $this->_extractList($p_path, $v_list_detail, "partial",
											$v_list, $p_remove_path);
			$this->_close();
		}
		return $v_result;
	}
	function _error($p_message) {
		$this->raiseError($p_message);
	}
	function _isArchive($p_filename=NULL) {
		if ($p_filename == NULL) {
			$p_filename = $this->_tarname;
		}
		clearstatcache();
		return @is_file($p_filename) && !@is_link($p_filename);
	}
	function _openRead() {
		if (strtolower(substr($this->_tarname, 0, 7)) == 'http://') {
		  if ($this->_temp_tarname == '') {
			  $this->_temp_tarname = uniqid('tar').'.tmp';
			  if (!$v_file_from = @fopen($this->_tarname, 'rb')) {
				$this->_error('Unable to open in read mode \''
							  .$this->_tarname.'\'');
				$this->_temp_tarname = '';
				return false;
			  }
			  if (!$v_file_to = @fopen($this->_temp_tarname, 'wb')) {
				$this->_error('Unable to open in write mode \''
							  .$this->_temp_tarname.'\'');
				$this->_temp_tarname = '';
				return false;
			  }
			  while (($v_data = @fread($v_file_from, 1024)))
				  @fwrite($v_file_to, $v_data);
			  @fclose($v_file_from);
			  @fclose($v_file_to);
		  }
		  $v_filename = $this->_temp_tarname;
		} else
		  $v_filename = $this->_tarname;
		if ($this->_compress_type == 'gz')
			$this->_file = @gzopen($v_filename, "rb");
		else if ($this->_compress_type == 'bz2')
			$this->_file = @bzopen($v_filename, "r");
		else if ($this->_compress_type == 'none')
			$this->_file = @fopen($v_filename, "rb");
		else
			$this->_error('Unknown or missing compression type ('
						  .$this->_compress_type.')');
		if ($this->_file == 0) {
			$this->_error('Unable to open in read mode \''.$v_filename.'\'');
			return false;
		}
		return true;
	}
	function _close() {
		if (is_resource($this->_file)) {
			if ($this->_compress_type == 'gz')
				@gzclose($this->_file);
			else if ($this->_compress_type == 'bz2')
				@bzclose($this->_file);
			else if ($this->_compress_type == 'none')
				@fclose($this->_file);
			else
				$this->_error('Unknown or missing compression type ('
							  .$this->_compress_type.')');
			$this->_file = 0;
		}
		if ($this->_temp_tarname != '') {
			@unlink($this->_temp_tarname);
			$this->_temp_tarname = '';
		}
		return true;
	}
	function _readBlock() {
	  $v_block = null;
	  if (is_resource($this->_file)) {
		  if ($this->_compress_type == 'gz')
			  $v_block = @gzread($this->_file, 512);
		  else if ($this->_compress_type == 'bz2')
			  $v_block = @bzread($this->_file, 512);
		  else if ($this->_compress_type == 'none')
			  $v_block = @fread($this->_file, 512);
		  else
			  $this->_error('Unknown or missing compression type ('
							.$this->_compress_type.')');
	  }
	  return $v_block;
	}
	function _jumpBlock($p_len=null) {
	  if (is_resource($this->_file)) {
		  if ($p_len === null)
			  $p_len = 1;
		  if ($this->_compress_type == 'gz') {
			  @gzseek($this->_file, gztell($this->_file)+($p_len*512));
		  }
		  else if ($this->_compress_type == 'bz2') {
			  for ($i=0; $i<$p_len; $i++)
				  $this->_readBlock();
		  } else if ($this->_compress_type == 'none')
			  @fseek($this->_file, $p_len*512, SEEK_CUR);
		  else
			  $this->_error('Unknown or missing compression type ('
							.$this->_compress_type.')');
	  }
	  return true;
	}
	function _readHeader($v_binary_data, &$v_header) {
		if (strlen($v_binary_data)==0) {
			$v_header['filename'] = '';
			return true;
		}
		if (strlen($v_binary_data) != 512) {
			$v_header['filename'] = '';
			$this->_error('Invalid block size : '.strlen($v_binary_data));
			return false;
		}
		if (!is_array($v_header)) {
			$v_header = array();
		}
		$v_checksum = 0;
		for ($i=0; $i<148; $i++)
			$v_checksum+=ord(substr($v_binary_data,$i,1));
		for ($i=148; $i<156; $i++)
			$v_checksum += ord(' ');
		for ($i=156; $i<512; $i++)
		   $v_checksum+=ord(substr($v_binary_data,$i,1));
		$v_data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/"
						 ."a8checksum/a1typeflag/a100link/a6magic/a2version/"
						 ."a32uname/a32gname/a8devmajor/a8devminor",
						 $v_binary_data);
		$v_header['checksum'] = OctDec(trim($v_data['checksum']));
		if ($v_header['checksum'] != $v_checksum) {
			$v_header['filename'] = '';
			if (($v_checksum == 256) && ($v_header['checksum'] == 0))
				return true;
			$this->_error('Invalid checksum for file "'.$v_data['filename']
						  .'" : '.$v_checksum.' calculated, '
						  .$v_header['checksum'].' expected');
			return false;
		}
		$v_header['filename'] = $v_data['filename'];
		if ($this->_maliciousFilename($v_header['filename'])) {
			$this->_error('Malicious .tar detected, file "' . $v_header['filename'] .
				'" will not install in desired directory tree');
			return false;
		}
		$v_header['mode'] = OctDec(trim($v_data['mode']));
		$v_header['uid'] = OctDec(trim($v_data['uid']));
		$v_header['gid'] = OctDec(trim($v_data['gid']));
		$v_header['size'] = OctDec(trim($v_data['size']));
		$v_header['mtime'] = OctDec(trim($v_data['mtime']));
		if (($v_header['typeflag'] = $v_data['typeflag']) == "5") {
		  $v_header['size'] = 0;
		}
		$v_header['link'] = trim($v_data['link']);
		return true;
	}
	function _maliciousFilename($file) {
		if (strpos($file, '/../') !== false) {
			return true;
		}
		if (strpos($file, '../') === 0) {
			return true;
		}
		return false;
	}
	function _readLongHeader(&$v_header) {
	  $v_filename = '';
	  $n = floor($v_header['size']/512);
	  for ($i=0; $i<$n; $i++) {
		$v_content = $this->_readBlock();
		$v_filename .= $v_content;
	  }
	  if (($v_header['size'] % 512) != 0) {
		$v_content = $this->_readBlock();
		$v_filename .= trim($v_content);
	  }
	  $v_binary_data = $this->_readBlock();
	  if (!$this->_readHeader($v_binary_data, $v_header))
		return false;
	  $v_filename = trim($v_filename);
	  $v_header['filename'] = $v_filename;
		if ($this->_maliciousFilename($v_filename)) {
			$this->_error('Malicious .tar detected, file "' . $v_filename .
				'" will not install in desired directory tree');
			return false;
	  }
	  return true;
	}
	function _extractList($p_path, &$p_list_detail, $p_mode,
						  $p_file_list, $p_remove_path) {
	$v_nb = 0;
	$v_extract_all = true;
	$v_listing = false;
	$p_path = $this->_translateWinPath($p_path, false);
	if ($p_path == '' || (substr($p_path, 0, 1) != '/'
		&& substr($p_path, 0, 3) != "../" && !strpos($p_path, ':'))) {
	  $p_path = "./".$p_path;
	}
	$p_remove_path = $this->_translateWinPath($p_remove_path);
	if (($p_remove_path != '') && (substr($p_remove_path, -1) != '/'))
	  $p_remove_path .= '/';
	$p_remove_path_size = strlen($p_remove_path);
	switch ($p_mode) {
	  case "complete" :
		$v_extract_all = TRUE;
		$v_listing = FALSE;
	  break;
	  case "partial" :
		  $v_extract_all = FALSE;
		  $v_listing = FALSE;
	  break;
	  case "list" :
		  $v_extract_all = FALSE;
		  $v_listing = TRUE;
	  break;
	  default :
		$this->_error('Invalid extract mode ('.$p_mode.')');
		return false;
	}
	clearstatcache();
	while (strlen($v_binary_data = $this->_readBlock()) != 0)
	{
	  $v_extract_file = FALSE;
	  $v_extraction_stopped = 0;
	  $v_header = FALSE;
	  if (!$this->_readHeader($v_binary_data, $v_header))
		return false;
	  if ($v_header['filename'] == '') {
		continue;
	  }
	  if ($v_header['typeflag'] == 'L') {
		if (!$this->_readLongHeader($v_header))
		  return false;
	  }
	  if ((!$v_extract_all) && (is_array($p_file_list))) {
		$v_extract_file = false;
		for ($i=0; $i<sizeof($p_file_list); $i++) {
		  if (substr($p_file_list[$i], -1) == '/') {
			if ((strlen($v_header['filename']) > strlen($p_file_list[$i]))
				&& (substr($v_header['filename'], 0, strlen($p_file_list[$i]))
					== $p_file_list[$i])) {
			  $v_extract_file = TRUE;
			  break;
			}
		  }
		  elseif ($p_file_list[$i] == $v_header['filename']) {
			$v_extract_file = TRUE;
			break;
		  }
		}
	  } else {
		$v_extract_file = TRUE;
	  }
	  if (($v_extract_file) && (!$v_listing))
	  {
		if (($p_remove_path != '')
			&& (substr($v_header['filename'], 0, $p_remove_path_size)
				== $p_remove_path))
		  $v_header['filename'] = substr($v_header['filename'],
										 $p_remove_path_size);
		if (($p_path != './') && ($p_path != '/')) {
		  while (substr($p_path, -1) == '/')
			$p_path = substr($p_path, 0, strlen($p_path)-1);
		  if (substr($v_header['filename'], 0, 1) == '/')
			  $v_header['filename'] = $p_path.$v_header['filename'];
		  else
			$v_header['filename'] = $p_path.'/'.$v_header['filename'];
		}
		if (file_exists($v_header['filename'])) {
		  if (   (@is_dir($v_header['filename']))
			  && ($v_header['typeflag'] == '')) {
			$this->_error('File '.$v_header['filename']
						  .' already exists as a directory');
			return false;
		  }
		  if (   ($this->_isArchive($v_header['filename']))
			  && ($v_header['typeflag'] == "5")) {
			$this->_error('Directory '.$v_header['filename']
						  .' already exists as a file');
			return false;
		  }
		  if (!is_writeable($v_header['filename'])) {
			$this->_error('File '.$v_header['filename']
						  .' already exists and is write protected');
			return false;
		  }
		}
		elseif (($this->_dirCheck(($v_header['typeflag'] == "5"
									?$v_header['filename']
									:dirname($v_header['filename'])))) != 1) {
			$this->_error('Unable to create path for '.$v_header['filename']);
			return false;
		}
		if ($v_extract_file) {
		  if ($v_header['typeflag'] == "5") {
			if (!@file_exists($v_header['filename'])) {
				if (!sys_mkdir($v_header['filename'])) {
					$this->_error('Unable to create directory {'
								  .$v_header['filename'].'}');
					return false;
				}
			}
		  } elseif ($v_header['typeflag'] == "2") {
			  if (@file_exists($v_header['filename'])) {
				  @unlink($v_header['filename']);
			  }
			  if (!@symlink($v_header['link'], $v_header['filename'])) {
				  $this->_error('Unable to extract symbolic link {'
								.$v_header['filename'].'}');
				  return false;
			  }
		  } else {
			  if (($v_dest_file = @fopen($v_header['filename'], "wb")) == 0) {
				  $this->_error('Error while opening {'.$v_header['filename']
								.'} in write binary mode');
				  return false;
			  } else {
				  $n = floor($v_header['size']/512);
				  for ($i=0; $i<$n; $i++) {
					  $v_content = $this->_readBlock();
					  fwrite($v_dest_file, $v_content, 512);
				  }
			if (($v_header['size'] % 512) != 0) {
			  $v_content = $this->_readBlock();
			  fwrite($v_dest_file, $v_content, ($v_header['size'] % 512));
			}
			@fclose($v_dest_file);
			@touch($v_header['filename'], $v_header['mtime']);
			if ($v_header['mode'] & 0111) {
				$mode = fileperms($v_header['filename']) | (~umask() & 0111);
				@chmod($v_header['filename'], $mode);
			}
		  }
		  clearstatcache();
		  if (filesize($v_header['filename']) != $v_header['size']) {
			  $this->_error('Extracted file '.$v_header['filename']
							.' does not have the correct file size \''
							.filesize($v_header['filename'])
							.'\' ('.$v_header['size']
							.' expected). Archive may be corrupted.');
			  return false;
		  }
		  }
		} else {
		  $this->_jumpBlock(ceil(($v_header['size']/512)));
		}
	  } else {
		  $this->_jumpBlock(ceil(($v_header['size']/512)));
	  }
	  if ($v_listing || $v_extract_file || $v_extraction_stopped) {
		if (($v_file_dir = dirname($v_header['filename']))
			== $v_header['filename'])
		  $v_file_dir = '';
		if ((substr($v_header['filename'], 0, 1) == '/') && ($v_file_dir == ''))
		  $v_file_dir = '/';
		$p_list_detail[$v_nb++] = $v_header;
		if (is_array($p_file_list) && (count($p_list_detail) == count($p_file_list))) {
			return true;
		}
	  }
	}
		return true;
	}
	function _dirCheck($p_dir) {
		clearstatcache();
		if ((@is_dir($p_dir)) || ($p_dir == ''))
			return true;
		$p_parent_dir = dirname($p_dir);
		if (($p_parent_dir != $p_dir) &&
			($p_parent_dir != '') &&
			(!$this->_dirCheck($p_parent_dir)))
			 return false;
		if (!sys_mkdir($p_dir)) {
			$this->_error("Unable to create directory '$p_dir'");
			return false;
		}
		return true;
	}
	function _translateWinPath($p_path, $p_remove_disk_letter=true) {
	  if (defined('OS_WINDOWS') && OS_WINDOWS) {
		  if (   ($p_remove_disk_letter)
			  && (($v_position = strpos($p_path, ':')) != false)) {
			  $p_path = substr($p_path, $v_position+1);
		  }
		  if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0,1) == '\\')) {
			  $p_path = strtr($p_path, '\\', '/');
		  }
	  }
	  return $p_path;
	}
}