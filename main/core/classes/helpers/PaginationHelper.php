<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Помощник нумерации
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@eresus.ru>
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Помощник нумерации
 *
 * @package EresusCMS
 *
 * @since 2.14
 */
class PaginationHelper
implements Iterator
{
	/**
	 * Общее количество страниц
	 *
	 * @var int
	 * @since 2.14
	 */
	private $total;

	/**
	 * Номер текущей страницы
	 *
	 * @var int
	 * @since 2.14
	 */
	private $current;

	/**
	 * Путь к шаблону
	 *
	 * @var string
	 * @since 2.14
	 */
	private $templatePath = null;

	/**
	 * Размер переключателя в количестве выводимых страниц
	 *
	 * @var int
	 */
	private $size = 10;

	/**
	 * Номер итерации
	 *
	 * Часть реализации интерфейса Iterator
	 *
	 * @var int
	 */
	private $iteration = 0;

	/**
	 * Требуемое количество итераций
	 *
	 * Часть реализации интерфейса Iterator
	 *
	 * @var int
	 */
	private $totalIterations = 0;

	/**
	 * Номер первый отображаемой страницы
	 *
	 * @var int
	 */
	private $first;

	/**
	 * Номер последней отображаемой страницы
	 *
	 * @var int
	 */
	private $last;

	/**
	 * Создаёт нового помощника
	 *
	 * Принимаемые параметры можно указать и позднее, при помощи соответствующих методов setXXX.
	 *
	 * @param int $total[optional]    Общее количество страниц.
	 * @param int $current[optional]  Номер текущей страницы. По умолчанию 1.
	 *
	 * @return PaginationHelper
	 *
	 * @since 2.14
	 */
	public function __construct($total = null, $current = 1)
	{
		$this->setTotal($total);
		$this->setCurrent($current);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает общее количество страниц
	 *
	 * @param int $value
	 * @return void
	 *
	 * @since 2.14
	 */
	public function setTotal($value)
	{
		$this->total = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает общее количество страниц
	 *
	 * @return int
	 *
	 * @since 2.14
	 */
	public function getTotal()
	{
		return $this->total;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает номер текущей страницы
	 *
	 * @param int $value
	 * @return void
	 *
	 * @since 2.14
	 */
	public function setCurrent($value)
	{
		$this->current = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает номер текущей страницы
	 *
	 * @return int
	 *
	 * @since 2.14
	 */
	public function getCurrent()
	{
		return $this->current;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает путь к шаблону
	 *
	 * @param string $value
	 * @return void
	 *
	 * @since 2.14
	 */
	public function setTemplate($value)
	{
		$this->templatePath = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к шаблону
	 *
	 * @return string
	 *
	 * @since 2.14
	 */
	public function getTemplate()
	{
		return $this->templatePath;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текущий элемент списка страниц
	 *
	 * @return array
	 *
	 * @see Iterator::current()
	 * @internal
	 */
	public function current()
	{
		$item = array(
			'title' => $this->first + $this->iteration - 1,
			'url' => null
		);

		switch (true)
		{
			case $this->iteration == 1 && $this->first != 1:
				$item['title'] = '&larr;';
			break;

			case $this->iteration == $this->totalIterations && $this->last != $this->total:
				$item['title'] = '&rarr;';
			break;
		}

		return $item;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает номер итерации
	 *
	 * @return int
	 * @see Iterator::key()
	 * @internal
	 */
	public function key()
	{
		return $this->iteration;
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Iterator::next()
	 * @internal
	 */
	public function next()
	{
		$this->iteration++;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Подготовливает объект к первой итерации
	 *
	 * @return void
	 * @see Iterator::rewind()
	 * @internal
	 */
	public function rewind()
	{
		/*
		 * Если страниц больше чем задано показывать за один раз, то будем показывать только часть
		 * страниц, наиболее близких к текущей.
		 */
		if ($this->total > $this->size)
		{
			// Начинаем показ с текущей, минус половину видимых
			$this->first = (int) floor($this->current - $this->size / 2);
			if ($this->first < 1)
			{
				// Страниц меньше 1-й не существует
				$this->first = 1;
			}
			$this->last = $this->first + $this->size - 1;
			if ($this->last > $this->total)
			{
				// Но если это больше чем страниц всего, вносим исправления
				$this->last = $this->total;
				$this->first = $this->last - $this->size + 1;
			}

			$this->totalIterations = $this->size;
			if ($this->first > 1)
			{
				$this->totalIterations++;
			}
			if ($this->last < $this->total)
			{
				$this->totalIterations++;
			}

		}
		else
		{
			$this->first = 1;
			$this->last = $this->total;
			$this->totalIterations = $this->total;
		}

		$this->iteration = 1;
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Iterator::valid()
	 * @internal
	 */
	public function valid()
	{
		return $this->iteration <= $this->totalIterations;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт разметку переключателя страниц
	 *
	 * @return string  HTML
	 *
	 * @since 2.14
	 */
	public function render()
	{
		$tmpl = new Template($this->getTemplate());

		$data = array('pagination' => $this);

		return $tmpl->compile($data);
	}
	//-----------------------------------------------------------------------------
}