This store contains a modified version of Simple Groupware 0.745

 release		for tested releases
 branches		for different author's branches
 trunk			for current source code at work (not tested)
 patches		patch collection
 documentation		user manuals and another text documentation


**Migrate from PHP 5.4 to PHP 5.5

pack(), unpack()
----------------
 changed unpack("a"...) to unpack("Z"...)

MySQL uses already MySQLi
-------------------------
nothing done

preg_replace() /e modfier
-------------------------
trigger.php: line 500
IMAP.php: line 1753
mimeDecode.php: line 597
mimePart.php: line 409

pmwiki.php:
line 85, 115, 329, 345, 647, 655, 704, 782, 788, 795, 805, 833, etc
created function PCCF, PPRE, PPRA

pmwiki/scripts/markupexpr.php
pmwiki/scripts/pagelist.php
pmwiki/scripts/pagerev.php
pmwiki/scripts/stdmarkup.php
pmwiki/scripts/upload.php
pmwiki/scripts/wikistyles.php
pmwiki/scripts/xlpage-utf-8.php
modules/lib/imap.php
modules/lib/smtp.php


intl deprecations
-----------------
not used, nothing done

mcrypt deprecations
-------------------
not used, nothing done


**Migrate from PHP 5.3 to PHP 5.4

Safe Mode
---------
is no longer supported
this should be ok

Magic Quotes
-------------
has been removed

register_globals and register_long_arrays
-----------------------------------------
nothing done, should not be a problem

mbstring.script_encoding
------------------------
should be set in the php.ini

call by reference
-----------------
some calls by reference are still there with php-functions: eg array(&$var), etc
especially in pmwiki.php. needs to be tested.

break statement
---------------
there should exist no break statement with $argument, only one "break 2" statement.
there should exist no continue statement with $argument.

array_combine()
---------------
all usage are with arrays with literals, so should be no problem, except maybe for icons. No change.

htmlentities()
--------------
should only be a problem with asian character sets. No change.

ob_start()
----------
is only used in this form ob_start without boolean erase flag. No change.

New reserved keywords
---------------------
are not used. No change

define_syslog_variables()
-------------------------
not used

import_request_variables()
--------------------------
not used

session_is_registered()
-----------------------
not used

session_register()
------------------
not used

session_unregister()
--------------------
not used

mysqli_bind_param()
-------------------
not used

mysqli_bind_result()
--------------------
not used

mysqli_client_encoding()
------------------------
not used

mysqli_fetch()
--------------
not used

mysqli_param_count()
--------------------
not used

mysqli_get_metadata()
---------------------
not used

mysqli_send_long_data()
-----------------------
not used

mysqli::client_encoding()
-------------------------
not used

mysqli_stmt::stmt()
-------------------
not used

