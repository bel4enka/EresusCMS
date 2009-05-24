<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * $Id$
 */

define('AJAXUI', true);

# ���������� ���� ������� #
$filename = dirname(__FILE__).DIRECTORY_SEPARATOR.'kernel.php';
if (is_file($filename)) include_once($filename); else {
	# TODO: �������� �� JavaScript � ���������� �� ������.
	echo "<h1>Fatal error</h1>\n<strong>Kernel not available!</strong><br />\nThis error can take place during site update.<br />\nPlease try again later.";
	exit;
}

define('AJAX_ANSWER_TEXT', 'text/plain; charset='.CHARSET);
define('AJAX_ANSWER_XML',  'text/xml; charset='.CHARSET);
define('AJAX_ANSWER_JS',   'text/javascript; charset='.CHARSET);


/**
 * ����� ���������� AJAX-����������
 *
 */
class AjaxUI extends WebPage {
 /**
	* ��� ������������ �������
	* @var string
	*/
	var $plugin;
	//------------------------------------------------------------------------------
 /**
	* �����������
	*
	* @access  public
	*/
	function AjaxUI()
	{
		global $plugins;

		parent::WebPage();
		$plugins->preload(array('client'),array('ondemand'));
		$plugins->clientOnStart();

	}
	//------------------------------------------------------------------------------
 /**
	* ��������� �������
	*/
	function process()
	{
		global $Eresus, $plugins;

		$plugin = next($Eresus->request['params']);
		$plugins->load($plugin);
		$plugins->ajaxOnRequest();
		$plugins->items[$plugin]->ajaxProcess();
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ������
	*
	* @param string  ��� ������
	* @param string  ������ ������
	*/
	function answer($type, $data)
	{
		header("Content-type: $type", true);
		die($data);
	}
	//-----------------------------------------------------------------------------
}

#$page = new AjaxUI;
#$page->process();
