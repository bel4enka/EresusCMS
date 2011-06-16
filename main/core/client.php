<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package BusinessLogic
 *
 * $Id$
 */

/**
 * Страница клиентского интерфейса
 *
 * @package UI
 */
class TClientUI extends WebPage
{
	/**
	 * Текущий раздел сайта
	 *
	 * @var Eresus_Model_Section
	 * @since 2.16
	 */
	public $section;

	/**
	 * Контент страницы
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * Имя файла шаблона страницы
	 *
	 * @var string
	 */
	public $templateName;

	function httpError($code)
	{
	global $KERNEL;

		if (isset($KERNEL['ERROR'])) return;
		$ERROR = array(
			'400' => array('response' => 'Bad Request'),
			'401' => array('response' => 'Unauthorized'),
			'402' => array('response' => 'Payment Required'),
			'403' => array('response' => 'Forbidden'),
			'404' => array('response' => 'Not Found'),
			'405' => array('response' => 'Method Not Allowed'),
			'406' => array('response' => 'Not Acceptable'),
			'407' => array('response' => 'Proxy Authentication Required'),
			'408' => array('response' => 'Request Timeout'),
			'409' => array('response' => 'Conflict'),
			'410' => array('response' => 'Gone'),
			'411' => array('response' => 'Length Required'),
			'412' => array('response' => 'Precondition Failed'),
			'413' => array('response' => 'Request Entity Too Large'),
			'414' => array('response' => 'Request-URI Too Long'),
			'415' => array('response' => 'Unsupported Media Type'),
			'416' => array('response' => 'Requested Range Not Satisfiable'),
			'417' => array('response' => 'Expectation Failed'),
		);

		Header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$ERROR[$code]['response']);

		if (defined('HTTP_CODE_'.$code)) $message = constant('HTTP_CODE_'.$code);
		else $message = $ERROR[$code]['response'];

		$this->section = array(siteTitle, $message);
		$this->title = $message;
		$this->description = '';
		$this->keywords = '';
		$this->caption = $message;
		$this->hint = '';
		$this->access = GUEST;
		$this->visible = true;
		$this->type = 'default';
		if (file_exists(Eresus_CMS::app()->getRootDir() . '/templates/std/'.$code.'.tmpl'))
		{
			$this->template = 'std/'.$code;
			$this->content = '';
		} else {
			$this->template = 'default';
			$this->content = '<h1>HTTP ERROR '.$code.': '.$message.'</h1>';
		}
		$this->render();
		exit;
	}
	//------------------------------------------------------------------------------

	/**
	 * Отправляет созданную страницу пользователю.
	 *
	 * @return string  HTML
	 *
	 * @since ?.??
	 */
	public function render()
	{
		$event = new Eresus_CMS_Event('clientOnStart');
		$event->dispath();

		$this->section = $this->loadPage();
		if ($this->section)
		{
			/*if (count($Eresus->request['params']))
			{
				if (preg_match('/p[\d]+/i', $Eresus->request['params'][0]))
					$this->subpage = substr(array_shift($Eresus->request['params']), 1);

				if (count($Eresus->request['params']))
					$this->topic = array_shift($Eresus->request['params']);
			}*/
		}
		else
		{
			$this->httpError(404);
		}

		// Выбираем шаблон для страницы
		$this->templateName = $this->section->template;

		// Отрисовываем контент
		$this->content = $this->section->getContent();

		$event = new Eresus_CMS_Event('clientOnContentRender');
		$event->content = $this->content;
		$event->dispath();
		$this->content = $event->content;

		/*if (isset($_SESSION['msg']['information']) && count($_SESSION['msg']['information']))
		{
			$messages = '';
			foreach ($_SESSION['msg']['information'] as $message)
			{
				$messages .= InfoBox($message);
			}
			$content = $messages.$content;
			$_SESSION['msg']['information'] = array();
		}
		if (isset($_SESSION['msg']['errors']) && count($_SESSION['msg']['errors']))
		{
			$messages = '';
			foreach ($_SESSION['msg']['errors'] as $message)
			{
				$messages .= ErrorBox($message);
			}
			$content = $messages.$content;
			$_SESSION['msg']['errors'] = array();
		}*/

		$vars = array(
			'page' => new Eresus_Helper_ArrayAccessDecorator($this),
		);

		$tmpl = Eresus_Template::fromFile('templates/' . $this->templateName . '.html');
		$html = $tmpl->compile($vars);

		$event = new Eresus_CMS_Event('clientOnPageRender');
		$event->content = $html;
		$event->dispath();
		$html = $event->content;

		$html = preg_replace('|(.*)</head>|i', '$1'.$this->renderHeadSection()."\n</head>", $html);

		// Замена макросов
		//$result = $this->replaceMacros($result);

		//if (count($this->headers)) foreach ($this->headers as $header) Header($header);

		//$result = $Eresus->plugins->clientBeforeSend($result);
		$event = new Eresus_CMS_Event('clientBeforeSend');
		$event->content = $html;
		$event->dispath();
		$html = $event->content;

		//if (!$Eresus->conf['debug']['enable']) ob_start('ob_gzhandler');
		echo $html;
		//if (!$Eresus->conf['debug']['enable']) ob_end_flush();
	}
	//------------------------------------------------------------------------------
}
