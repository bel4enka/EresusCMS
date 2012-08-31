<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Система управления контентом Eresus™
# Версия 2.00
# © 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Русский языковой файл
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
define('CHARSET','windows-1251');

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

define('RESTRICTION0', 'нет ограничений');
define('RESTRICTION1', 'Предмодерация');
define('RESTRICTION2', 'Только чтение');
define('strNoRestrictions', 'нет ограничений');
define('strPredModeration', 'Предмодерация');
define('strReadOnly', 'Только чтение');

/* ОШИБКИ */
define('errNotice', 'Замечание');
define('errWarning', 'Предупреждение');
define('errError', 'Ошибка');
define('errUnknownError', 'Неизвестная ошибка');
define('errUnknownAction', 'Неизвестное действие');
define('errUnknownFile', 'Неизвестный файл');
define('errUnknownLine', 'Неизвестная строка');
define('errInternalError', 'Внутренняя ошибка');
define('errFileNotFound', 'Файл не найден');
define('errFileOpening', 'Открытие файла "%s"');
define('errFileWriteError', 'Ошибка записи в файл');
define('errFileWriting', 'Запись в файл "%s"');
define('errInvalidPassword', 'Неверный пароль');
define('errAccountNotActive', 'Учетная запись "%s" отключена администратором сайта');
define('errAccountNotExists', 'Учетная запись "%s" отсутствует. Проверьте правильность ввода');
define('errTooEarlyRelogin',"Перед попыткой повторного логина должно пройти не менее %s секунд!");
define('errNoMainPage', 'В списке страниц не найдена корневая страница с именем "main"');
define('errFormUnknownType', 'Неизвестный тип поля "%s" в форме "%s"');
define('errFormFieldHasNoName', 'Не указано имя для поля типа "%s" в форме "%s"');
define('errFormHasNoName', 'Не указано имя формы');
define('errFormPatternError', 'Введенное значение в поле "%s" не соответствует требуемому формату "%s"');
define('errFormBadConfirm', 'Пароль и подтверждение не совпадают!');
define('errAccessDenied', 'Доступ к данному разделу запрещен!');
define('errNonexistedDomain', 'Несуществующий домен: "%s"');
define('errContentPluginNotFound', 'Не найдено модуля поддержки типа контента "%s"');
define('errMethodNotFound', 'Метод "%s" не найден в классе "%s".');
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
define('strReturn', 'Вернуться');
define('strYes', 'Да');
define('strNo', 'Нет');
define('strExit', 'Выход');

/* ОПОВЕЩЕНИЯ */
define('strNotification', "Оповещение");
define('strNotifyTemplate', "%s (%s)<br>Раздел: <a href=\"%s\">%s</a><br><hr>%s<hr>");

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
define('admTDiv', ' - ');

define('admAdd', 'Добавить');
define('admAdded', 'Добавлено');
define('admDelete', 'Удалить');
define('admDeleted', 'Удалено');
define('admEdit', 'Изменить');
define('admActivate', 'Включить');
define('admActivated', 'Активировано');
define('admDeactivate', 'Отключить');
define('admDeactivated', 'Деактивировано');
define('admSortPosition', 'По порядку');
define('admSortAscending', 'По возрастанию');
define('admSortDescending', 'По убыванию');
define('admUp', 'Вверх');
define('admDown', 'Вниз');
define('admUpdated', 'Изменено');

define('admPlugin', 'Плагин');
define('admPlugins', 'Модули расширения');
define('admSettings', 'Настройки');
define('admContent', 'Контент');
define('admControls', 'Управление');
define('admConfiguration', 'Конфигурация');
define('admStructure', 'Страницы');
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
define('admConfigKeywordsHint', 'Список глобальных ключевых слов. Будет доступен через макрос $(siteKeywords)');
define('admConfigDescriptionHint', 'Описание сайта (META-тэг). Будет доступно через макрос $(siteDescription)');
define('admConfigMailFromAddrHint', 'От: имя <АДРЕС> (оргнизация)');
define('admConfigMailFromNameHint', 'От: ИМЯ <адрес> (оргнизация)');
define('admConfigMailFromOrgHint', 'От: имя <адрес> (ОРГАНИЗАЦИЯ)');
define('admConfigSendNotifyToHint', 'Адреса для рассылки административных сообщений');
define('admConfigFiles', 'Файловые операции');
define('admConfigFilesOwnerSetOnUpload', 'Устанавливать владельца загружаемых файлов');
define('admConfigFilesOwnerDefault', 'Владелец');
define('admConfigFilesModeSetOnUpload', 'Устанавливать атрибуты на загружаемые файлы');
define('admConfigFilesModeDefault', 'Атрибуты');
define('admConfigStructure', 'Структура сайта');
define('admConfigDefaultContentType', 'Тип контента по умолчанию');
define('admConfigDefaultPageTamplate', 'Тип контента по умолчанию');

/* Оформление */
define('admThemesTabWidth', '180px');
define('admThemesTemplate', 'Шаблон');
define('admThemesTemplates', 'Шаблоны страниц');
define('admThemesFilenameLabel', 'Имя файла');
define('admThemesDescriptionLabel', 'Описание');
define('admThemesStyles', 'Файлы стилей');
define('admThemesStyleLabel', 'Редактирование файла стилей');
define('admThemesStandard', 'Стандартные шаблоны');

define('admPluginsAdd', 'Добавить плагин');
define('admPluginsTableHint', "Типы: <strong>user</strong> - работает с фронт-эндом, <strong>admin</strong> - работает с бэк-эндом, <strong>content</strong> - плагин контента, <strong>ondemand</strong> - загружается только при необходимости");
define('admPluginsFound', 'Найденные плагины');
define('admPluginsInvalidFile', 'Файл не является модулем расширения');
define('admPluginsAdded', 'Подключен новый плагин');

define('admUsersName', 'Имя');
define('admUsersLogin', 'Логин');
define('admUsersAccountState', 'Учетная запись активна');
define('admUsersAccessLevelShort', 'Дост.');
define('admUsersLoginErrors', 'Ошибок входа');
define('admUsersLoginErrorsShort', 'Ошиб.');
define('admUsersViolations', 'Нарушения');
define('admUsersViolationsShort', 'Наруш.');
define('admUsersRestriction', 'Ограничение');
define('admUsersRestrictionTill', 'Действует до');
define('admUsersRestrictionTillShort', 'До');
define('admUsersMail', 'e-mail');
define('admUsersHideMail', 'Не показывать другим адрес');
define('admUsersLastVisit', 'Последний визит');
define('admUsersLastVisitShort', 'Последний визит');
define('admUsersPassword', 'Пароль');
define('admUsersConfirmation', 'Подтверждение');
define('admUsersChangeUser', 'Изменить учетную запись');
define('admUsersChangePassword', 'Изменить пароль');
define('admUsersPasswordChanged', 'Изменен пароль');
define('admUsersNameInvalid', 'Псевдоним пользователя не может быть пустым.');
define('admUsersLoginInvalid', 'Логин не может быть пустым и должен состоять только из букв a-z, цифр и символа подчеркивания.');
define('admUsersMailInvalid', 'Неверно указан почтовый адрес.');
define('admUsersConfirmInvalid', 'Не совпадают пароль и подтверждение.');
define('admUsersPostCount', 'Пост.');
define('admUsersEndless', 'Бессрочно');
define('admUsersCreate', 'Создать пользователя');
define('admUsersAdded', 'Добавлена учетная запись');

define('admPagesAddInto', 'Добавить страницу в...');
define('admPagesMoveTo', 'Переместить ветку в...');
define('admPagesDeleteBrunch', 'Удалить ветку');
define('admPagesRoot', 'КОРЕНЬ');
define('admPagesContentDefault', 'По умолчанию (встроен.)');
define('admPagesContentList', 'Список подразделов (встроен.)');
define('admPagesContentURL', 'URL (встроен.)');
define('admPagesThisURL', 'URL этой страницы');
define('admPagesID', 'ID страницы');
define('admPagesName', 'Имя страницы');
define('admPagesNameInvalid', 'Имя страницы не может быть пустым и может состоять из латинских букв, цифр и символа подчеркивания.');
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

define('admTemplList', 'Шаблон элемента списка разделов');
define('admTemplListLabel', 'Шаблон списка разделов. Используйте макрос $(items) для вставки списка. Для изменения оформления элементов списка создайте или измените шаблон <a href="'.httpRoot.'admin.php?mod=themes&section=std">'.admTemplList.'</a>');
define('admTemplListItemLabel', 'Шаблон элемента списка разделов. Макросы <strong>$(title)</strong> - заголовок; <strong>$(caption)</strong> - пункт меню; <strong>$(description)</strong> - описание; <strong>$(hint)</strong> - подсказка; <strong>$(link)</strong> - ссылка.');

define('templPosted', ' <img src="'.httpRoot.'core/img/time.gif" alt="" title="$(posted)">');
?>