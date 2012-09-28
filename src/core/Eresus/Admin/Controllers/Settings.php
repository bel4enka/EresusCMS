<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package Eresus
 *
 * $Id$
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Eresus
 */
class Eresus_Admin_Controllers_Settings
{
	/**
	 * Минимальный требуемый уровень доступа
	 * @var int
	 */
	private $access = ADMIN;

	/**
	 * Создаёт строку параметра для записи в файл
	 *
	 * @param string $name     имя параметра
	 * @param string $type     тип: string, bool или int
	 * @param array  $options  опции: nobr (true/false), savebr (true/false)
	 *
	 * @return string
	 */
	private function mkstr($name, $type = 'string', $options = array())
	{
		$result = "  define('$name', ";
		$quot = "'";
		$value = isset($_POST[$name]) ? $_POST[$name] : null;

		if (isset($options['nobr']) && $options['nobr'])
		{
			$value = str_replace(array("\n", "\r"), ' ', $value);
		}

		if (isset($options['savebr']) && $options['savebr'])
		{
			$value = addcslashes($value, "\n\r\"");
			$quot = '"';
		}

		switch ($type)
		{
			case 'string':
				$value = str_replace(
					array('\\', $quot),
					array('\\\\', '\\' . $quot),
					$value
				);
				$value = $quot . $value . $quot;
			break;
			case 'bool':
				$value = $value ? 'true' : 'false';
			break;
			case 'int':
					$value = intval($value);
			break;
		}
		$result .= $value . ");\n";
		return $result;
	}

	/**
	 * Записывает настройки
	 * @return Response
	 */
	private function update()
	{
		$settings = "<?php\n";

		$settings .= $this->mkstr('siteName', 'string');
		$settings .= $this->mkstr('siteTitle', 'string');
		$settings .= $this->mkstr('siteTitleReverse', 'bool');
		$settings .= $this->mkstr('siteTitleDivider', 'string');
		$settings .= $this->mkstr('siteKeywords', 'string', array('nobr'=>true));
		$settings .= $this->mkstr('siteDescription', 'string', array('nobr'=>true));
		$settings .= $this->mkstr('mailFromAddr', 'string');
		$settings .= $this->mkstr('mailFromName', 'string');
		$settings .= $this->mkstr('mailFromOrg', 'string');
		$settings .= $this->mkstr('mailReplyTo', 'string');
		$settings .= $this->mkstr('mailFromSign', 'string', array('savebr'=>true));
		$settings .= $this->mkstr('filesModeSetOnUpload', 'bool');
		$settings .= $this->mkstr('filesModeDefault', 'string');
		$settings .= $this->mkstr('filesTranslitNames', 'bool');
		$settings .= $this->mkstr('contentTypeDefault', 'string');
		$settings .= $this->mkstr('pageTemplateDefault', 'string');

		file_put_contents(filesRoot.'cfg/settings.php', $settings);
		return new RedirectResponse($_SERVER['HTTP_REFERER']);
	}

	/**
	 * Возвращает HTML-код раздела
	 * @return string  HTML
	 * @uses Eresus_UI_Form
	 * @uses Eresus_Templates
	 */
	private function renderForm()
	{
		$template = Eresus_Kernel::app()->getPage()->getUITheme()->
			getResource('SiteSettings/form.html');
		$form = new Eresus_UI_Form($template);
		/* Основные */
		$form->setValue('siteName', option('siteName'));
		$form->setValue('siteTitle', option('siteTitle'));
		$form->setValue('siteTitleReverse', option('siteTitleReverse'));
		$form->setValue('siteTitleDivider', option('siteTitleDivider'));
		$form->setValue('siteKeywords', option('siteKeywords'));
		$form->setValue('siteDescription', option('siteDescription'));
		/* Почта */
		$form->setValue('mailFromAddr', option('mailFromAddr'));
		$form->setValue('mailFromName', option('mailFromName'));
		$form->setValue('mailFromOrg', option('mailFromOrg'));
		$form->setValue('mailReplyTo', option('mailReplyTo'));
		$form->setValue('mailFromSign', option('mailFromSign'));
		/* Файлы */
		$form->setValue('filesModeSetOnUpload', option('filesModeSetOnUpload'));
		$form->setValue('filesModeDefault', option('filesModeDefault'));
		$form->setValue('filesTranslitNames', option('filesTranslitNames'));

		/* Создаем список типов контента */
		$contentTypes = array(
			array('name' => 'default','caption' => ADM_PAGES_CONTENT_DEFAULT),
			array('name' => 'list','caption' => ADM_PAGES_CONTENT_LIST),
			array('name' => 'url','caption' => ADM_PAGES_CONTENT_URL)
		);

		foreach (Eresus_CMS::getLegacyKernel()->plugins->items as $plugin)
		{
			if ($plugin instanceof Eresus_Extensions_ContentPlugin)
			{
				$contentTypes []= array('name' => $plugin->name, 'caption' => $plugin->title);
			}
		}

		$form->setValue('contentTypes', $contentTypes);
		$form->setValue('contentTypeDefault', option('contentTypeDefault'));

		/* Загружаем список шаблонов */
		$templates = new Eresus_Templates();
		$list = $templates->enum();
		$templates = array();
		foreach ($list as $key => $value)
			$templates []= array('name' => $key, 'caption' => $value);

		$form->setValue('templates', $templates);
		$form->setValue('pageTemplateDefault', option('pageTemplateDefault'));

		$html = $form->compile();
		return $html;
	}

	/**
	 * Отрисовка контента
     *
	 * @return Response|string
	 */
	function adminRender()
	{
		if (!UserRights($this->access))
		{
			return '';
		}

		/** @var Eresus_HTTP_Request $req */
		$request = Eresus_Kernel::get('request');

		if ($request->getMethod() == 'POST')
		{
			return $this->update();
		}

		$html = $this->renderForm();
		return $html;
	}
}
