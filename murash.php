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
 * MBF/1.0 Instructions
 *
 */

/**
 * Create target directory
 *
 * @param string $target  Directory name
 */
function create_target($target)
{
	$GLOBALS['MURASH']['TARGET'] = $target;
	if (!is_dir($target)) mkdir($target, 0644);
}
//-----------------------------------------------------------------------------
/**
 * Recursivly copy files from source to TARGET
 *
 * @param string $source  Source directory
 */
function copy_files_from($source)
{
	if (is_dir($source)) {
		$list = array_merge(glob($source.'/*'), glob($source.'/.*'));
		print_r($list);
	}
}
//-----------------------------------------------------------------------------


