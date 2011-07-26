<?php
/**
 * ${product.title}
 *
 * Работа с URI
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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


/**
 * URI
 *
 * Согласно {@link http://tools.ietf.org/html/rfc3986 RFC 3986} URI состоит из следующих частей:
 *
 * - схема — самая первая часть (до первого «:»), например «http:…» или «urn:…»
 * - информация о пользователе — часть между схемой и хостом, например «…//username:password@…»
 * - хост — доменное имя или IP-адрес, например «example.org» или «127.0.0.1»
 * - порт — номер порта, следует после хоста
 * - путь — путь, например «/some/path» или «some:path»
 * - запрос — набор пар имя-значение, например «a=b&c=d»
 * - фрагмент — часть после «#», например «…#part1»
 *
 * @link http://tools.ietf.org/html/rfc3986 RFC 3986
 *
 * @package Eresus
 */
class Eresus_URI
{
	/**
	 * Набор символов «unsereved»
	 *
	 * @var string
	 */
	const UNRESERVED = '[:alnum:]\-\._~';

	/**
	 * Набор символов для шестнадцатиричного кодирования
	 *
	 * @var string
	 */
	const PCT_ENCODED = '%[:xdigit:]';

	/**
	 * Основные разделители
	 *
	 * @var string
	 */
	const GEN_DELIMS = ':\/?#\[\]@';

	/**
	 * Дополнительные разделители
	 *
	 * @var string
	 */
	const SUB_DELIMS = '!$&\'()*+,;=';

	/**
	 * Части URI
	 *
	 * @var array
	 */
	private $uri = array();

	/**
	 * Создаёт новый URI
	 *
	 * @param string $uri  исходный URI
	 *
	 * @return Eresus_URI
	 *
	 * @since 2.16
	 */
	public function __construct($uri = null)
	{
		if ($uri)
		{
			$this->parseURI($uri);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Строковое представление URI
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function __toString()
	{
		$scheme = $this->getScheme();
		$query = strval($this->getQuery());
		$fragment = $this->getFragment();
		$userinfo = $this->getUserinfo();
		$port = $this->getPort();
		$authority = ($userinfo ? $userinfo . '@' : '') . $this->getHost() . ($port ? ':' . $port : '');
		$hierPart = ($authority ? '//' . $authority : '') . $this->getPath();

		$uri =
			($scheme ? $scheme . ':' : '') .
			$hierPart .
			($query ? '?' . $query : '') .
			($fragment ? '#' . $fragment : '');
		return $uri;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает схему
	 *
	 * @param string $scheme  схема
	 *
	 * @throws InvalidArgumentException если схема не соответствует {@link
	 *   http://tools.ietf.org/html/rfc3986 RFC 3986}
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see getScheme()
	 */
	public function setScheme($scheme)
	{
		if (!preg_match('/^\w[\w\d+\-\.]*$/i', $scheme) && !is_null($scheme))
		{
			throw new InvalidArgumentException('Invalid URI scheme: ' . $scheme);
		}

		$this->uri['scheme'] = strtolower($scheme);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает схему запроса
	 *
	 * @return string|null
	 *
	 * @since 2.16
	 * @see setScheme()
	 */
	public function getScheme()
	{
		return isset($this->uri['scheme']) ? $this->uri['scheme'] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает информацию о пользователе
	 *
	 * @param string $userinfo  информация о пользователе
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see getUserinfo()
	 */
	public function setUserinfo($userinfo)
	{
		$userinfo = strval($userinfo);
		$allowedChars = self::UNRESERVED . self::PCT_ENCODED . self::SUB_DELIMS . ':';
		if (!preg_match('/^[' . $allowedChars . ']*$/i', $userinfo))
		{
			throw new InvalidArgumentException('Invalid URI userinfo: ' . $userinfo);
		}

		$this->uri['userinfo'] = $userinfo;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает информацию о пользователе
	 *
	 * @return string|null
	 *
	 * @since 2.16
	 * @see setUserinfo()
	 */
	public function getUserinfo()
	{
		return isset($this->uri['userinfo']) ? $this->uri['userinfo'] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает хост
	 *
	 * @param string $host  хост
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see getHost()
	 */
	public function setHost($host)
	{
		$host = strval($host);
		$this->uri['host'] = strtolower($host);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает хост
	 *
	 * @return string|null
	 *
	 * @since 2.16
	 * @see setHost()
	 */
	public function getHost()
	{
		return isset($this->uri['host']) ? $this->uri['host'] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает порт
	 *
	 * @param int $port  порт
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see getPort()
	 */
	public function setPort($port)
	{
		if (!is_numeric($port) && !is_null($port))
		{
			throw new InvalidArgumentException('Invalid URI port: ' . strval($port));
		}
		$this->uri['port'] = $port;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает порт
	 *
	 * @return string|null
	 *
	 * @since 2.16
	 * @see setPort()
	 */
	public function getPort()
	{
		return isset($this->uri['port']) ? $this->uri['port'] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает путь
	 *
	 * @param string $path  путь
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see getPath()
	 */
	public function setPath($path)
	{
		$path = strval($path);
		$this->uri['path'] = $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь
	 *
	 * @return string|null
	 *
	 * @since 2.16
	 * @see setPath()
	 */
	public function getPath()
	{
		return isset($this->uri['path']) ? $this->uri['path'] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает запрос
	 *
	 * @param Eresus_URI_Query|string $query  запрос
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see getQuery()
	 */
	public function setQuery($query)
	{
		if (is_string($query))
		{
			$query = new Eresus_URI_Query($query);
		}
		$this->uri['query'] = $query;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает запрос
	 *
	 * @return Eresus_URI_Query
	 *
	 * @since 2.16
	 * @see setQuery()
	 */
	public function getQuery()
	{
		if (!isset($this->uri['query']))
		{
			$this->uri['query'] = new Eresus_URI_Query();
		}
		return $this->uri['query'];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает фрагмент
	 *
	 * @param string $fragment  фрагмент
	 *
	 * @return void
	 *
	 * @since 2.16
	 * @see getFragment()
	 */
	public function setFragment($fragment)
	{
		$fragment = strval($fragment);
		$this->uri['fragment'] = $fragment;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает фрагмент
	 *
	 * @return string|null
	 *
	 * @since 2.16
	 * @see setFragment()
	 */
	public function getFragment()
	{
		return isset($this->uri['fragment']) ? $this->uri['fragment'] : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Заменяет части URI указанными значениями
	 *
	 * Массив $parts может содержать ключи:
	 * - <b>scheme</b> — схема
	 * - <b>userinfo</b> — информация о пользователе
	 * - <b>host</b> — хост
	 * - <b>port</b> — порт
	 * - <b>path</b> — путь
	 * - <b>query</b> — запрос
	 * - <b>fragment</b> — фрагмент
	 *
	 * @param array $parts  ассоциативный массив значений
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function replace(array $parts)
	{
		$keys = array('scheme', 'userinfo', 'host', 'port', 'path', 'query', 'fragment');
		foreach ($keys as $key)
		{
			if (isset($parts[$key]))
			{
				$setter = 'set' . $key;
				$this->$setter($parts[$key]);
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Производит разбор URI
	 *
	 * @param string $uri
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	protected function parseURI($uri)
	{
		$url = parse_url($uri);
		$keys = array('scheme', 'host', 'port', 'path', 'query', 'fragment');
		foreach ($keys as $key)
		{
			if (isset($url[$key]))
			{
				$setter = 'set' . $key;
				$this->$setter($url[$key]);
			}
		}
		if (isset($url['user']))
		{
			$this->setUserinfo($url['user'] . (isset($url['pass']) ? ':' . $url['pass'] : ''));
		}
	}
	//-----------------------------------------------------------------------------
}

