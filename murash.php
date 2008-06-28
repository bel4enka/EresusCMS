<?php
/**
 * ProCreat Murash
 *
 * PHP Project Build Tool
 *
 * @version 1.0
 *
 * @copyright 2008, ProCreat Systems, http://procreat.ru/
 * @license   http://www.gnu.org/licenses/gpl.txt  GPL License 3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Mikhail Krasilnikov <mk@procreat.ru>, <mk@dvaslona.ru>
 */

prepare_project();
build_project();
cleanup();

/**
 * Prepare project
 */
function prepare_project()
{
	$GLOBALS['MURASH']['ROOT'] = dirname(realpath(__FILE__)).'/';
}
//-----------------------------------------------------------------------------
/**
 * Build project
 */
function build_project()
{
	$build_php = file('build.php');
	array_shift($build_php);
	$build_php = implode('', $build_php);
	$build_php = str_replace(array('<?php', '?>'), '', $build_php);
	eval($build_php);
}
//-----------------------------------------------------------------------------
/**
 * Attempt cleanup
 */
function cleanup()
{
	;
}
//-----------------------------------------------------------------------------


/*
 * MBF/1.0 Workset
 *
 */

/**
 * Abstract Hook
 *
 */
class Hook {
	/**
	 * Unique hook ID
	 *
	 * @var string
	 */
	protected $hookID;
	/**
	 * PHP5
	 *
	 */
	function __construct()
	{
		$this->hookID = uniqid();
		$GLOBALS['HOOKS'][$this->hookID] = $this;
	}
	//-----------------------------------------------------------------------------
}

class FunctionHook extends Hook {
	/**
	 * PHP5
	 * @param string $function_name
	 */
	function __construct($function_name)
	{
		parent::__construct();
		$GLOBALS['FHOOKS'][$function_name][] = $this->hookID;
	}
	//-----------------------------------------------------------------------------
	}

/**
 * Call hooks
 */
function function_hooks($function_name, $method, $return = null)
{
	$args = func_get_args();
	$method = 'on'.$method;
	array_shift($args);array_shift($args);array_shift($args);
	array_unshift($args, $return);
	$registered = $GLOBALS['FHOOKS'][$function_name];
	if ($registered) foreach($registered as $hookID) {
		$hook = $GLOBALS['HOOKS'][$hookID];
		if ($hook && method_exists($hook, $method))
			$args[0] = call_user_func_array(array($hook, $method), $args);
		return $args[0];
	}
	return $return;
}
//-----------------------------------------------------------------------------


/**
 * Create target directory
 *
 * @param string $target  Directory name
 */
function create_target($target)
{
	$GLOBALS['MURASH']['TARGET'] = $target;
	if (is_dir($target)) die('Target exists.');
	mkdir($target, 0755);
}
//-----------------------------------------------------------------------------
/**
 * Recursivly copy files from source to TARGET
 *
 * @param string $source  Source directory
 * @param string $target  Target directory relative to TARGET
 */
function copy_files_from($source, $target = '')
{
	$prefix = strpos($source, DIRECTORY_SEPARATOR) !== false ? substr($source, 0, strpos($source, DIRECTORY_SEPARATOR)) : $source;

	if (is_dir($source)) {
		$list = array_merge(glob($source.'/*'), glob($source.'/.*'));
		if ($list) foreach($list as $from) if (!preg_match('!/\.{1,2}$!', $from)) {
			switch (true) {
				case is_file($from):
					$to = $GLOBALS['MURASH']['ROOT'].$GLOBALS['MURASH']['TARGET'].$target.substr($from, strlen($prefix));
					$from = $GLOBALS['MURASH']['ROOT'].$from;
					if (!is_dir(dirname($to))) mkdir(dirname($to), 0755, true);
					copy($from, $to);
					function_hooks(__FUNCTION__, 'filecopied', null, $to);
				break;
				case is_dir($from) && function_hooks(__FUNCTION__, 'dircopy', true, $from):
					$to = $GLOBALS['MURASH']['ROOT'].$GLOBALS['MURASH']['TARGET'].$target.substr($from, strlen($prefix));
					mkdir($to, 0755, true);
					copy_files_from($from, $target);
				break;
			}
		}
	}
}
//-----------------------------------------------------------------------------
function substitute($filename)
{
	$contents = file_get_contents($filename);
	if (isset($GLOBALS['MURASH']['SUBST']) && strpos($contents, '{$M{')) {
		foreach ($GLOBALS['MURASH']['SUBST'] as $key => $value)
			$contents = str_replace($key, $value, $contents);
		file_put_contents($filename, $contents);
	}
}
//-----------------------------------------------------------------------------
function SET($name, $value)
{
	$GLOBALS['MURASH']['VARS'][$name] = $value;
	$GLOBALS['MURASH']['SUBST']['{$M{'.$name.'}}'] = $value;
}
//-----------------------------------------------------------------------------
