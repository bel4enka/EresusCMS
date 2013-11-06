<?php
/**
 * ${product.title}
 *
 * Русские сообщения
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
 */

/**
 * Кодировка документов
 */
define('LOCALE_CHARSET', 'UTF-8');
setlocale(LC_ALL, 'ru_RU.'.LOCALE_CHARSET);
define('CHARSET', 'UTF-8');

/* Таблица транслитерации */
$GLOBALS['translit_table'] = array(
	'а'=> 'a', 'б'=> 'b', 'в'=> 'v', 'г'=> 'g', 'д'=> 'd', 'е'=> 'e', 'ё'=> 'yo', 'ж'=> 'zh',
	'з'=> 'z', 'и'=> 'i', 'й'=> 'y', 'к'=> 'k', 'л'=> 'l', 'м'=> 'm', 'н'=> 'n', 'о'=> 'o', 'п'=> 'p',
	'р'=> 'r', 'с'=> 's', 'т'=> 't', 'у'=> 'u', 'ф'=> 'f', 'х'=> 'h', 'ц'=> 'tc', 'ч'=> 'ch',
	'ш'=> 'sh', 'щ'=> 'sch', 'ь'=> '', 'ы'=> 'y', 'ъ'=> '', 'э'=> 'e', 'ю'=> 'yu', 'я'=> 'ya',
	'А'=> 'a', 'Б'=> 'b', 'В'=> 'v', 'Г'=> 'g', 'Д'=> 'd', 'Е'=> 'e', 'Ё'=> 'yo', 'Ж'=> 'zh',
	'З'=> 'z', 'И'=> 'i', 'Й'=> 'y', 'К'=> 'k', 'Л'=> 'l', 'М'=> 'm', 'Н'=> 'n', 'О'=> 'o', 'П'=> 'p',
	'Р'=> 'r', 'С'=> 's', 'Т'=> 't', 'У'=> 'u', 'Ф'=> 'f', 'Х'=> 'h', 'Ц'=> 'tc', 'Ч'=> 'ch',
	'Ш'=> 'sh', 'Щ'=> 'sch', 'Ь'=> '', 'Ы'=> 'y', 'Ъ'=> '', 'Э'=> 'e', 'Ю'=> 'yu', 'Я'=> 'ya'
);

/* ДАТА И ВРЕМЯ */
define('MONTH_00', '00');
define('MONTH_01', 'января');
define('MONTH_02', 'февраля');
define('MONTH_03', 'марта');
define('MONTH_04', 'апреля');
define('MONTH_05', 'мая');
define('MONTH_06', 'июня');
define('MONTH_07', 'июля');
define('MONTH_08', 'августа');
define('MONTH_09', 'сентября');
define('MONTH_10', 'октября');
define('MONTH_11', 'ноября');
define('MONTH_12', 'декабря');
/* Символы форматировния даты/времени
* Y - год, четыре цифры (2004)
* y - год, две цифры (04)
* M - месяц по имени (мая)
* m - месяц две цифры (05)
* D - день без лидирующего нуля (7)
* d - день, две цифры (07)
* H - часы без лидирующего нуля (7)
* h - часы (07)
* i - минуты (05)
* s - секунды (05)
*/
define('TIME_LONG', 'h:i:s');
define('TIME_SHORT', 'H:i');
define('DATE_LONG', 'D M Y');
define('DATE_SHORT', 'd.m.y');

define('DATETIME', 'D M Y, H:i:s');
define('DATETIME_LONG', TIME_LONG.', '.DATE_LONG);
define('DATETIME_SHORT', TIME_SHORT.', '.DATE_SHORT);
define('DATETIME_NORMAL', TIME_SHORT.', '.DATE_LONG);

define('DATETIME_UNKNOWN', 'Дата и время неизвестны');

define('ACCESSLEVEL0', 'Неизвестный');
define('ACCESSLEVEL1', 'Главный администратор');
define('ACCESSLEVEL2', 'Администратор');
define('ACCESSLEVEL3', 'Редактор');
define('ACCESSLEVEL4', 'Пользователь');
define('ACCESSLEVEL5', 'Гость');

/* ОШИБКИ */

# Общие
define('errNotice', 'Замечание');
define('errWarning', 'Предупреждение');
define('errError', 'Ошибка');
define('errUnknownError', 'Неизвестная ошибка');
define('errUnknownAction', 'Неизвестное действие');
define('errUnknownFile', 'Неизвестный файл');
define('errUnknownLine', 'Неизвестная строка');
# Внутренние
define('errInternalError', 'Внутренняя ошибка');
define('errNoMainPage', 'В списке страниц не найдена корневая страница с именем "main"');
define('errContentPluginNotFound', 'Не найдено модуля поддержки типа контента "%s"');
define('errClassNotFound', 'Класс "%s" не найден.');
define('errMethodNotFound', 'Метод "%s" не найден в классе "%s".');
# Файловые
define('errFileNotFound', 'Файл не найден');
define('errFileOpening', 'Открытие файла "%s"');
define('errFileWriteError', 'Ошибка записи в файл');
define('errFileWriting', 'Запись в файл "%s"');
define('errFileWrite', 'Не удается записать в файл "%s"');
define('errFileCopy', 'Не удается скопировать файл "%s" в "%s"');
define('errFileMove', 'Не удается переместить файл "%s" в "%s"');
define('errLibNotFound', 'Не найдена библиотека "%s"');
define('errTemplateNotFound', 'Шаблон "%s" не найден');
# Загрузка файлов
define('errUploadSizeINI',
	'Размер файла "%s" превышает максимально допустимый размер '.ini_get('upload_max_filesize').'.');
define('errUploadSizeFORM',
	'Размер файла "%s" превышает максимально допустимый размер указанный в форме.');
define('errUploadPartial', 'Файл "%s" получен только частично.');
define('errUploadNoFile', 'Файл "%s" не был загружен.');

define('ERR_PASSWORD_INVALID', 'Неверное имя пользователя или пароль');
define('ERR_ACCOUNT_NOT_ACTIVE', ERR_PASSWORD_INVALID);
define('ERR_LOGIN_FAILED_TOO_EARLY',
	"Перед попыткой повторного логина должно пройти не менее %s секунд!");
# Формы
define('errFormUnknownType', 'Неизвестный тип поля "%s" в форме "%s"');
define('errFormFieldHasNoName', 'Не указано имя для поля типа "%s" в форме "%s"');
define('errFormHasNoName', 'Не указано имя формы');
define('errFormPatternError',
	'Введенное значение в поле "%s" не соответствует требуемому формату "%s"');
define('errFormBadConfirm', 'Пароль и подтверждение не совпадают!');
define('errAccessDenied', 'Доступ к данному разделу запрещен!');
define('errNonexistedDomain', 'Несуществующий домен: "%s"');

# Разделы
define('errContentType', 'Неверный тип контента "%s"');

define('errItemWithSameName', 'Элемент с именем "%s" уже существует.');

/* Описание ответов HTTP */
define('HTTP_CODE_403', 'Доступ к запрошенному ресурсу запрещен');
define('HTTP_CODE_404', 'Запрошенная страница не найдена');

/* СТАНДАРТНЫЕ */
define('strOk', 'OK');
define('strApply', 'Применить');
define('strCancel', 'Отменить');
define('strReset', 'Вернуть');
define('strAdd', 'Добавить');
define('strEdit', 'Изменить');
define('strDelete', 'Удалить');
define('strMove', 'Переместить');
define('strReturn', 'Вернуться');
define('strProperties', 'Свойства');
define('strYes', 'Да');
define('strNo', 'Нет');
define('strExit', 'Выход');

/* ОПОВЕЩЕНИЯ */
define('strNotification', "Оповещение");
define('strNotifyTemplate', "%s (%s)<br />Раздел: <a href=\"%s\">%s</a><br /><hr>%s<hr>");

/* Управление страницами */
define('strPages', 'Страницы: ');
define('strPrevPage', 'Предыдущая страница');
define('strNextPage', 'Следующая страница');
define('strFirstPage', 'Первая страница');
define('strLastPage', 'Последняя страница');
/* Разное */
define('strMainMenu', 'Навигация');
define('strAuthorisation', 'Авторизация');
define('strLogin', 'Логин');
define('strRegistration', 'Регистрация');
define('strRemind', 'Напомнить');
define('strPassword', 'Пароль');
define('strAutoLogin', 'Запомнить');
define('strEnterSite', 'Войти');
define('strExitSite', 'Выйти');
define('strLastVisit', 'Последний визит');
define('strURL', 'Адрес');
define('strViewTopic', 'Показать полностью');
define('strInformation', 'Информация');


/***************** АДМИНИСТРИРОВАНИЕ *******************/
define('ADM_T_DIV', ' - ');

define('ADM_ADD', 'Добавить');
define('ADM_ADDED', 'Добавлено');
define('ADM_DELETE', 'Удалить');
define('ADM_DELETED', 'Удалено');
define('ADM_EDIT', 'Изменить');
define('ADM_ACTIVATE', 'Включить');
define('ADM_ACTIVATED', 'Активировано');
define('ADM_DEACTIVATE', 'Отключить');
define('ADM_DEACTIVATED', 'Деактивировано');
define('ADM_SORT_POS', 'По порядку');
define('ADM_SORT_ASC', 'По возрастанию');
define('ADM_SORT_DESC', 'По убыванию');
define('ADM_UP', 'Вверх');
define('ADM_DOWN', 'Вниз');
define('ADM_UPDATED', 'Изменено');

define('ADM_NA', '(не задано)');
define('admPlugin', 'Плагин');
define('admPlugins', 'Модули расширения');
define('admSettings', 'Настройки');
define('admContent', 'Контент');
define('admControls', 'Управление');
define('admConfiguration', 'Конфигурация');
define('admStructure', 'Разделы сайта');
define('admUsers', 'Пользователи');
define('admThemes', 'Оформление');
define('admExtensions', 'Расширения');
define('admLanguages', 'Языки');
define('admFileManager', 'Файловый менеджер');
define('admDescription', 'Описание');
define('admVersion', 'Версия');
define('admType', 'Тип');
define('admAccessLevel', 'Уровень доступа');
define('admPosition', 'Позиция');
define('admChanges', 'Изменения');
/* Меню администратора */
define('admStructureHint', 'Управление структурой сайта, меню и страницами');
define('admPluginsHint', 'Управление модулями расширения');
define('admUsersHint', 'Управление пользователями');
define('admThemesHint', 'Управление шаблонами страниц и стилями');
define('admConfigurationHint', 'Конфигурация сайта');
define('admLanguagesHint', 'Управление языковыми параметрами');
define('admFileManagerHint', 'Управление файлами');
/* Конфигурация */
define('admSettingsMain', 'Основное');
define('admSettingsMail', 'Почта');
define('admSettingsFiles', 'Файлы');
define('admSettingsOther', 'Прочее');

define('admConfigMailSettings', 'Настройки почты');
define('admConfigNotifications', 'Оповещения');
define('admConfigPostInformation', 'Информация о постах');
define('admConfigSecurity', 'Безопасность');
define('admConfigSiteName', 'Название сайта');
define('admConfigSiteTitle', 'Заголовок сайта');
define('admConfigTitleReverse', 'Выводить в обратном порядке');
define('admConfigTitleDivider', 'Разделитель');
define('admConfigSiteKeywords', 'Ключевые слова');
define('admConfigSiteDescription', 'Описание сайта');
define('admConfigMailFromAddr', 'Адрес отправителя');
define('admConfigMailFromName', 'Имя отправителя');
define('admConfigMailFromOrg', 'Организация отправителя');
define('admConfigMailReplyTo', 'Обратный адрес');
define('admConfigMailCharset', 'Кодировка письма');
define('admConfigMailSign', 'Подпись под письмом');
define('admConfigSendNotifyTo', 'Оповещать');
define('admConfigPostsShowInfo', 'Показывать информацию о постах');
define('admConfigPostsDateFormat', 'Формат даты');
define('admConfigPostsShowIP', 'Показывать IP-адреса авторов (администраторам и редакторам)');
define('admConfigPostsResolveIP', 'Определять имена хостов');
define('admConfigAccessPolicy', 'Для неавторизованных');
define('admConfigSiteNameHint', 'Короткое название сайта. Будет доступно через макрос $(siteName)');
define('admConfigSiteTitleHint', 'Заголовок страниц. Будет доступен через макрос $(siteTitle)');
define('admConfigTitleDividerHint', 'Разделитель элементов заголовка сайта');
define('admConfigKeywordsHint',
	'Список глобальных ключевых слов. Будет доступен через макрос $(siteKeywords)');
define('admConfigDescriptionHint',
	'Описание сайта (META-тэг). Будет доступно через макрос $(siteDescription)');
define('admConfigMailFromAddrHint', 'От: имя <АДРЕС> (оргнизация)');
define('admConfigMailFromNameHint', 'От: ИМЯ <адрес> (оргнизация)');
define('admConfigMailFromOrgHint', 'От: имя <адрес> (ОРГАНИЗАЦИЯ)');
define('admConfigSendNotifyToHint', 'Адреса для рассылки административных сообщений');
define('admConfigFiles', 'Файловые операции');
define('admConfigFilesOwnerSetOnUpload',
	'Устанавливать владельца загружаемых файлов (только для суперпользователя)');
define('admConfigFilesOwnerDefault', 'Владелец');
define('admConfigFilesModeSetOnUpload', 'Устанавливать атрибуты на загружаемые файлы');
define('admConfigFilesModeDefault', 'Атрибуты');
define('admConfigTranslitNames', 'Транслитерировать имена загружаемых файлов');
define('admConfigStructure', 'Структура сайта');
define('admConfigDefaultContentType', 'Тип контента по умолчанию');
define('admConfigDefaultPageTamplate', 'Шаблон страницы по умолчанию');
define('admConfigClientPagesAtOnce', 'Показывать');
define('admConfigClientPagesAtOnceComment', 'элементов в переключателе страниц');

/* Оформление */
define('ADM_THEMES_TAB_WIDTH', '180px');
define('ADM_THEMES_TEMPLATE', 'Шаблон');
define('ADM_THEMES_TEMPLATES', 'Шаблоны страниц');
define('ADM_THEMES_FILENAME_LABEL', 'Имя файла');
define('ADM_THEMES_DESC_LABEL', 'Описание');
define('ADM_THEMES_STYLES', 'Файлы стилей');
define('ADM_THEMES_STYLE_LABEL', 'Редактирование файла стилей');
define('ADM_THEMES_STANDARD', 'Стандартные шаблоны');
define('ADM_THEMES_FILENAME_FILTERED',
	'Указанное Вами имя файла содержало недопустимые символы и было изменено на "%s"');

define('admPluginsAdd', 'Добавить плагин');
define('admPluginsTableHint',
	"Типы: <strong>user</strong> - работает с фронт-эндом, <strong>admin</strong> - работает с ' .
	'бэк-эндом, <strong>content</strong> - плагин контента, <strong>ondemand</strong> - загружается' .
	' только при необходимости");
define('admPluginsFound', 'Найденные плагины');
define('admPluginsInvalidFile', 'Файл не является модулем расширения');
define('admPluginsInvalidVersion', 'Требуется ядро %s или выше.');
define('admPluginsAdded', 'Подключен новый плагин');
define('admPluginTopicTable', 'Таблица топиков');

define('admUsersName', 'Имя');
define('admUsersLogin', 'Логин');
define('admUsersAccountState', 'Учетная запись активна');
define('admUsersAccessLevelShort', 'Дост.');
define('admUsersLoginErrors', 'Ошибок входа');
define('admUsersLoginErrorsShort', 'Ошиб.');
define('admUsersMail', 'e-mail');
define('admUsersLastVisit', 'Последний визит');
define('admUsersLastVisitShort', 'Последний визит');
define('admUsersPassword', 'Пароль');
define('admUsersConfirmation', 'Подтверждение');
define('admUsersChangePassword', 'Изменить пароль');
define('admUsersPasswordChanged', 'Изменен пароль');
define('admUsersNameInvalid', 'Псевдоним пользователя не может быть пустым.');
define('admUsersLoginInvalid',
	'Логин не может быть пустым и должен состоять только из букв a-z, цифр и символа подчеркивания.');
define('admUsersLoginExists', 'Пользователь с таким логином уже существует.');
define('admUsersMailInvalid', 'Неверно указан почтовый адрес.');
define('admUsersConfirmInvalid', 'Пароль и подтверждение не совпадают.');
define('admUsersCreate', 'Создать пользователя');
define('admUsersAdded', 'Добавлена учетная запись');

define('admPagesMove', 'Переместить ветку');
define('admPagesRoot', 'КОРЕНЬ');
define('admPagesContentDefault', 'По умолчанию');
define('admPagesContentList', 'Список подразделов');
define('admPagesContentURL', 'URL');
define('admPagesThisURL', 'URL этой страницы');
define('admPagesID', 'ID страницы');
define('admPagesName', 'Имя страницы');
define('admPagesNameInvalid',
	'Имя страницы не может быть пустым и может состоять из латинских букв, цифр и символа ' .
	'подчеркивания.');
define('admPagesTitle', 'Заголовок страницы');
define('admPagesTitleInvalid', 'Заголовок страницы не может быть пустым');
define('admPagesCaption', 'Название пункта меню');
define('admPagesCaptionInvalid', 'Пункт меню не может быть пустым');
define('admPagesDescription', 'Описание');
define('admPagesKeywords', 'Ключевые слова');
define('admPagesHint', 'Подсказка');
define('admPagesContentType', 'Тип страницы');
define('admPagesTemplate', 'Шаблон');
define('admPagesActive', 'Активна');
define('admPagesVisible', 'Видимая');
define('admPagesCreated', 'Дата создания');
define('admPagesUpdated', 'Дата обновления');
define('admPagesUpdatedAuto', 'Обновить дату изменения автоматически');
define('admPagesOptions', 'Дополнительные опции');
define('admPagesContent', 'Контент страницы');

define('admTemplList', 'Шаблон элемента списка разделов');
define('admTemplListLabel',
	'Шаблон списка разделов. Используйте макрос $(items) для вставки списка. Для изменения ' .
	'оформления элементов списка создайте или измените шаблон <a href="'.httpRoot.
	'admin.php?mod=themes&section=std">'.admTemplList.'</a>');
define('admTemplListItemLabel',
	'Шаблон элемента списка разделов. Макросы <strong>$(title)</strong> - заголовок; <strong>' .
	'$(caption)</strong> - пункт меню; <strong>$(description)</strong> - описание; <strong>$(hint)' .
	'</strong> - подсказка; <strong>$(link)</strong> - ссылка.');

define('admTemplPageSelector', 'Шаблон переключателя страниц');
define('admTemplPageSelectorLabel',
	'Шаблон состоит из 5-х секций, разделяемых тройным дефисом (---):<ol><li>Переключатель страниц,' .
	' макрос $(pages) задаёт положение генерируемого содержимого.</li><li>Шаблон отдельной страницы' .
	', $(number) - номер страницы, $(href) - ссылка</li><li>Шаблон текущей страницы, $(number) - ' .
	'номер страницы, $(href) - ссылка</li><li>Шаблон перехода к первой странице, $(href) - ссылка' .
	'</li><li>Шаблон перехода к последней странице, $(href) - ссылка</li></ol>');

define('templPosted', '$(posted)');

define('ERR_PLUGIN_NOT_AVAILABLE', 'Модуль расширения "%s" не установлен или отключен.');