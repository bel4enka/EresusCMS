MBF/1.0
<?php
/**
 * Procreat Murash 1.0 Project
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

#SET('VERSION', '2.10');

#define('TARGET', '');

class CopyFilesHook extends FunctionHook {
	function ondircopy($allow, $name)
	{
		if (preg_match('!/\.svn$!', $name)) $allow = false;
		return $allow;
	}
	//-----------------------------------------------------------------------------
}



create_target('distrib');

new CopyFilesHook('copy_files_from');
copy_files_from('main');
copy_files_from('lang', '/lang');
copy_files_from('t', '/t');
