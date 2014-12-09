
* FILE:		readme.txt
* Date:		2014-11-30
* Changed:	2014-12-09
* Author:	Vlad Zaritsky

**INSTALLATION**

    Way #1
	- unpack original SimpleGroupware_0.745.tar.gz;
	- unpack and overwrite files from today patch file named SimpleGroupware_0.745.patch.XX.YYYY-MM-DD.tar.gz;
	- you need last day archive only (all changes accumulated);
	- to remove the <sgs>/simple_store/config.php and make new install of Simple Groupware using your browser.

    Way #2
	- unpack compound archive from /versions/xxxx
	- to remove the <sgs>/simple_store/config.php and make new install of Simple Groupware using your browser.



**1 Directory description**

 Today's tar archive consist Simple Groupware patches with keeping original tree structure
 Version SGW 0.745




**2 Notes**

*2.1  I recommend to delete/rename the file /bin/console.php if you do not use the
 administration of your server through the web interface.

*2.2 Patch 3.2, 3.3 solve ticket #396: http://sourceforge.net/p/simplgroup/support-requests/396/
 #396 Superuser switching to anonymous after selecting folder (on new install) 

*2.3 Applied the mysqli related patches from file SimpleGroupware_0.745p.tar.gz (http://github.com/simplegroupware-lab/0.745p)



**3 Files and comments**

 All patches marked in each file.

*3.1 /SimpleGroupware_0.745/lang/uk.lang
    Refine Ukrainian Language translating errors (for example twice Thusday's in calendar short names).

*3.2 /SimpleGroupware_0.745/src/modules/core/pgsql.sql
    To change field names to match PostgreSQL 9.2 'pid' and 'query' affected.

*3.3 /SimpleGroupware_0.745/src/modules/schema_sys/session.xml
    Length of "id" field changed from 40 to 64 - need to store real generated data length.
    I do not know why length ID changed but it's work after change.
    Ticket #396 related change.

*3.4 /SimpleGroupware_0.745/src/modules/schema_sys/nodb_gdocs.xml
    Truncate length of "id" field changed from 40 to 64

*3.5 /SimpleGroupware_0.745.my/src/core/classes/validate.php
    To remove preg_match check of USERNAME field for pass full user name.

    This need for true people identification in chat. Otherwice we can see people login which not
    always to conform to corporative situation, e.g. login of head of the enterprise will be 'fly1965'.

    Another thing in simplify people registration. Enter only real first name and second name like 'John Doe'
    May be we need to change chat module for true identify too.

*3.6 /SimpleGroupware_0.745/src/modules/lib/cifs.php
    Back urldecode cache record filename for allow long names to store in file cahce.
    Otherwice a long non ASCII filename urlcoded is too long for file system (tested on SUSE 13.1 ext3).
    On test system I have 320 chars long output wich make a system error in Detailed view CIFS mounted directory.

    Need to be tested for Windows server and how cache to be cleaned.

*3.7 /SimpleGroupware_0.745/src/upload.php
    sha1 changed by md5 while temporary cache link generated for short output length.
    This changes to resolve previous described problem with filename length.
    When file will cached it name length growed by sha1 signature, md5 may be short.

*3.8 /SimpleGroupware_0.745/src/download.php
    The same as previous.

*3.9 /SimpleGroupware_0.745/src/core/function.php
    The same as previous.

*3.10 /SimpleGroupware_0.745/src/core/setup.php
    Security patches:
    - default CHMOD_DIR value changed to 755, the default CHMOD_FILE value changed to 644
    - default values of ENABLE_ANONYMOUS and ENABLE_ANONYMOUS_CMS set to zero

*3.11 /SimpleGroupware_0.745/src/core/functions.php
    Append check of ID field type and extract numbers only to eliminate SQL error while click on filename (CIFS dir).




**4 Known bugs/problems**

*4.1 The CIFS directory file list Download icon do not work properly and generate 'Access denied' message.
    I can to download file from CIFS shared folder by clicking the Download icon at Details view of folder only.
    Single CIFS shared file downloading requires three mouse clicks. If you want to download 50 files is too much
    work. This operations are usually performed in drag-and-drop mode.

*4.2 Chat module show users login but not users name.

*4.3 Single language installation make a request write process too slow. I can not say entity names in English
    while screen work e.g. in Ukrainian.


*eof*
