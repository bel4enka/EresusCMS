<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package Eresus
 *
 * $Id$
 */

/**
 * ��������� ����������
 */
define('LOCALE_CHARSET', 'CP1251');
setlocale(LC_ALL, 'ru_RU.'.LOCALE_CHARSET);
define('CHARSET','windows-1251');

/* ������� �������������� */
$GLOBALS['translit_table'] = array(
	'�'=> 'a', '�'=> 'b', '�'=> 'v', '�'=> 'g', '�'=> 'd', '�'=> 'e', '�'=> 'yo', '�'=> 'zh', '�'=> 'z', '�'=> 'i', '�'=> 'y', '�'=> 'k', '�'=> 'l', '�'=> 'm', '�'=> 'n', '�'=> 'o', '�'=> 'p', '�'=> 'r', '�'=> 's', '�'=> 't', '�'=> 'u', '�'=> 'f', '�'=> 'h', '�'=> 'tc', '�'=> 'ch', '�'=> 'sh', '�'=> 'sch', '�'=> '', '�'=> 'y', '�'=> '', '�'=> 'e', '�'=> 'yu', '�'=> 'ya',
	'�'=> 'a', '�'=> 'b', '�'=> 'v', '�'=> 'g', '�'=> 'd', '�'=> 'e', '�'=> 'yo', '�'=> 'zh', '�'=> 'z', '�'=> 'i', '�'=> 'y', '�'=> 'k', '�'=> 'l', '�'=> 'm', '�'=> 'n', '�'=> 'o', '�'=> 'p', '�'=> 'r', '�'=> 's', '�'=> 't', '�'=> 'u', '�'=> 'f', '�'=> 'h', '�'=> 'tc', '�'=> 'ch', '�'=> 'sh', '�'=> 'sch', '�'=> '', '�'=> 'y', '�'=> '', '�'=> 'e', '�'=> 'yu', '�'=> 'ya'
);

/* ���� � ����� */
define('MONTH_00', '00');
define('MONTH_01', '������');
define('MONTH_02', '�������');
define('MONTH_03', '�����');
define('MONTH_04', '������');
define('MONTH_05', '���');
define('MONTH_06', '����');
define('MONTH_07', '����');
define('MONTH_08', '�������');
define('MONTH_09', '��������');
define('MONTH_10', '�������');
define('MONTH_11', '������');
define('MONTH_12', '�������');
/* ������� ������������� ����/�������
* Y - ���, ������ ����� (2004)
* y - ���, ��� ����� (04)
* M - ����� �� ����� (���)
* m - ����� ��� ����� (05)
* D - ���� ��� ����������� ���� (7)
* d - ����, ��� ����� (07)
* H - ���� ��� ����������� ���� (7)
* h - ���� (07)
* i - ������ (05)
* s - ������� (05)
*/
define('TIME_LONG', 'h:i:s');
define('TIME_SHORT', 'H:i');
define('DATE_LONG', 'D M Y');
define('DATE_SHORT', 'd.m.y');

define('DATETIME', 'D M Y, H:i:s');
define('DATETIME_LONG', TIME_LONG.', '.DATE_LONG);
define('DATETIME_SHORT', TIME_SHORT.', '.DATE_SHORT);
define('DATETIME_NORMAL', TIME_SHORT.', '.DATE_LONG);

define('DATETIME_UNKNOWN', '���� � ����� ����������');

define('ACCESSLEVEL0', '�����������');
define('ACCESSLEVEL1', '������� �������������');
define('ACCESSLEVEL2', '�������������');
define('ACCESSLEVEL3', '��������');
define('ACCESSLEVEL4', '������������');
define('ACCESSLEVEL5', '�����');

define('RESTRICTION0', '��� �����������');
define('RESTRICTION1', '�������������');
define('RESTRICTION2', '������ ������');
define('strNoRestrictions', '��� �����������');
define('strPredModeration', '�������������');
define('strReadOnly', '������ ������');

/* ������ */

# �����
define('errNotice', '���������');
define('errWarning', '��������������');
define('errError', '������');
define('errUnknownError', '����������� ������');
define('errUnknownAction', '����������� ��������');
define('errUnknownFile', '����������� ����');
define('errUnknownLine', '����������� ������');
# ����������
define('errInternalError', '���������� ������');
define('errNoMainPage', '� ������ ������� �� ������� �������� �������� � ������ "main"');
define('errContentPluginNotFound', '�� ������� ������ ��������� ���� �������� "%s"');
define('errClassNotFound', '����� "%s" �� ������.');
define('errMethodNotFound', '����� "%s" �� ������ � ������ "%s".');
# ��������
define('errFileNotFound', '���� �� ������');
define('errFileOpening', '�������� ����� "%s"');
define('errFileWriteError', '������ ������ � ����');
define('errFileWriting', '������ � ���� "%s"');
define('errFileWrite', '�� ������� �������� � ���� "%s"');
define('errFileCopy', '�� ������� ����������� ���� "%s" � "%s"');
define('errFileMove', '�� ������� ����������� ���� "%s" � "%s"');
define('errLibNotFound', '�� ������� ���������� "%s"');
define('errTemplateNotFound', '������ "%s" �� ������');
# �������� ������
define('errUploadSizeINI', '������ ����� "%s" ��������� ����������� ���������� ������ '.ini_get('upload_max_filesize').'.');
define('errUploadSizeFORM', '������ ����� "%s" ��������� ����������� ���������� ������ ��������� � �����.');
define('errUploadPartial', '���� "%s" ������� ������ ��������.');
define('errUploadNoFile', '���� "%s" �� ��� ��������.');

define('errInvalidPassword', '�������� ��� ������������ ��� ������');
define('errAccountNotActive', errInvalidPassword);
define('errAccountNotExists', errInvalidPassword);
define('errTooEarlyRelogin',"����� �������� ���������� ������ ������ ������ �� ����� %s ������!");
# �����
define('errFormUnknownType', '����������� ��� ���� "%s" � ����� "%s"');
define('errFormFieldHasNoName', '�� ������� ��� ��� ���� ���� "%s" � ����� "%s"');
define('errFormHasNoName', '�� ������� ��� �����');
define('errFormPatternError', '��������� �������� � ���� "%s" �� ������������� ���������� ������� "%s"');
define('errFormBadConfirm', '������ � ������������� �� ���������!');
define('errAccessDenied', '������ � ������� ������� ��������!');
define('errNonexistedDomain', '�������������� �����: "%s"');

# �������
define('errContentType', '�������� ��� �������� "%s"');

define('errItemWithSameName', '������� � ������ "%s" ��� ����������.');

/* �������� ������� HTTP */
define('HTTP_CODE_403', '������ � ������������ ������� ��������');
define('HTTP_CODE_404', '����������� �������� �� �������');

/* ����������� */
define('strOk', 'OK');
define('strApply', '���������');
define('strCancel', '��������');
define('strReset', '�������');
define('strAdd', '��������');
define('strEdit', '��������');
define('strDelete', '�������');
define('strMove', '�����������');
define('strReturn', '���������');
define('strProperties', '��������');
define('strYes', '��');
define('strNo', '���');
define('strExit', '�����');

/* ���������� */
define('strNotification', "����������");
define('strNotifyTemplate', "%s (%s)<br />������: <a href=\"%s\">%s</a><br /><hr>%s<hr>");

/* ���������� ���������� */
define('strPages', '��������: ');
define('strPrevPage', '���������� ��������');
define('strNextPage', '��������� ��������');
define('strFirstPage', '������ ��������');
define('strLastPage', '��������� ��������');
/* ������ */
define('strMainMenu', '���������');
define('strAuthorisation', '�����������');
define('strLogin', '�����');
define('strRegistration', '�����������');
define('strRemind', '���������');
define('strPassword', '������');
define('strAutoLogin', '���������');
define('strEnterSite', '�����');
define('strExitSite', '�����');
define('strLastVisit', '��������� �����');
define('strURL', '�����');
define('strViewTopic', '�������� ���������');
define('strInformation', '����������');


/***************** ����������������� *******************/
define('admTDiv', ' - ');

define('admAdd', '��������');
define('admAdded', '���������');
define('admDelete', '�������');
define('admDeleted', '�������');
define('admEdit', '��������');
define('admActivate', '��������');
define('admActivated', '������������');
define('admDeactivate', '���������');
define('admDeactivated', '��������������');
define('admSortPosition', '�� �������');
define('admSortAscending', '�� �����������');
define('admSortDescending', '�� ��������');
define('admUp', '�����');
define('admDown', '����');
define('admUpdated', '��������');

define('admNA', '(�� ������)');
define('admPlugin', '������');
define('admPlugins', '������ ����������');
define('admSettings', '���������');
define('admContent', '�������');
define('admControls', '����������');
define('admConfiguration', '������������');
define('admStructure', '������� �����');
define('admUsers', '������������');
define('admThemes', '����������');
define('admExtensions', '����������');
define('admLanguages', '�����');
define('admFileManager', '�������� ��������');
define('admDescription', '��������');
define('admVersion', '������');
define('admType', '���');
define('admAccessLevel', '������� �������');
define('admPosition', '�������');
define('admChanges', '���������');
/* ���� �������������� */
define('admStructureHint', '���������� ���������� �����, ���� � ����������');
define('admPluginsHint', '���������� �������� ����������');
define('admUsersHint', '���������� ��������������');
define('admThemesHint', '���������� ��������� ������� � �������');
define('admConfigurationHint', '������������ �����');
define('admLanguagesHint', '���������� ��������� �����������');
define('admFileManagerHint', '���������� �������');
/* ������������ */
define('admSettingsMain', '��������');
define('admSettingsMail', '�����');
define('admSettingsFiles', '�����');
define('admSettingsOther', '������');

define('admConfigMailSettings', '��������� �����');
define('admConfigNotifications', '����������');
define('admConfigPostInformation', '���������� � ������');
define('admConfigSecurity', '������������');
define('admConfigSiteName', '�������� �����');
define('admConfigSiteTitle', '��������� �����');
define('admConfigTitleReverse', '�������� � �������� �������');
define('admConfigTitleDivider', '�����������');
define('admConfigSiteKeywords', '�������� �����');
define('admConfigSiteDescription', '�������� �����');
define('admConfigMailFromAddr', '����� �����������');
define('admConfigMailFromName', '��� �����������');
define('admConfigMailFromOrg', '����������� �����������');
define('admConfigMailReplyTo', '�������� �����');
define('admConfigMailCharset', '��������� ������');
define('admConfigMailSign', '������� ��� �������');
define('admConfigSendNotifyTo', '���������');
define('admConfigPostsShowInfo', '���������� ���������� � ������');
define('admConfigPostsDateFormat', '������ ����');
define('admConfigPostsShowIP', '���������� IP-������ ������� (��������������� � ����������)');
define('admConfigPostsResolveIP', '���������� ����� ������');
define('admConfigAccessPolicy', '��� ����������������');
define('admConfigSiteNameHint', '�������� �������� �����. ����� �������� ����� ������ $(siteName)');
define('admConfigSiteTitleHint', '��������� �������. ����� �������� ����� ������ $(siteTitle)');
define('admConfigTitleDividerHint', '����������� ��������� ��������� �����');
define('admConfigKeywordsHint', '������ ���������� �������� ����. ����� �������� ����� ������ $(siteKeywords)');
define('admConfigDescriptionHint', '�������� ����� (META-���). ����� �������� ����� ������ $(siteDescription)');
define('admConfigMailFromAddrHint', '��: ��� <�����> (����������)');
define('admConfigMailFromNameHint', '��: ��� <�����> (����������)');
define('admConfigMailFromOrgHint', '��: ��� <�����> (�����������)');
define('admConfigSendNotifyToHint', '������ ��� �������� ���������������� ���������');
define('admConfigFiles', '�������� ��������');
define('admConfigFilesOwnerSetOnUpload', '������������� ��������� ����������� ������ (������ ��� �����������������)');
define('admConfigFilesOwnerDefault', '��������');
define('admConfigFilesModeSetOnUpload', '������������� �������� �� ����������� �����');
define('admConfigFilesModeDefault', '��������');
define('admConfigTranslitNames', '����������������� ����� ����������� ������');
define('admConfigStructure', '��������� �����');
define('admConfigDefaultContentType', '��� �������� �� ���������');
define('admConfigDefaultPageTamplate', '������ �������� �� ���������');
define('admConfigClientPagesAtOnce', '����������');
define('admConfigClientPagesAtOnceComment', '��������� � ������������� �������');

/* ���������� */
define('admThemesTabWidth', '180px');
define('admThemesTemplate', '������');
define('admThemesTemplates', '������� �������');
define('admThemesFilenameLabel', '��� �����');
define('admThemesDescriptionLabel', '��������');
define('admThemesStyles', '����� ������');
define('admThemesStyleLabel', '�������������� ����� ������');
define('admThemesStandard', '����������� �������');

define('admPluginsAdd', '�������� ������');
define('admPluginsTableHint', "����: <strong>user</strong> - �������� � �����-�����, <strong>admin</strong> - �������� � ���-�����, <strong>content</strong> - ������ ��������, <strong>ondemand</strong> - ����������� ������ ��� �������������");
define('admPluginsFound', '��������� �������');
define('admPluginsInvalidFile', '���� �� �������� ������� ����������');
define('admPluginsInvalidVersion', '��������� ���� %s ��� ����.');
define('admPluginsAdded', '��������� ����� ������');
define('admPluginTopicTable', '������� �������');

define('admUsersName', '���');
define('admUsersLogin', '�����');
define('admUsersAccountState', '������� ������ �������');
define('admUsersAccessLevelShort', '����.');
define('admUsersLoginErrors', '������ �����');
define('admUsersLoginErrorsShort', '����.');
define('admUsersMail', 'e-mail');
define('admUsersLastVisit', '��������� �����');
define('admUsersLastVisitShort', '��������� �����');
define('admUsersPassword', '������');
define('admUsersConfirmation', '�������������');
define('admUsersChangeUser', '�������� ������� ������');
define('admUsersChangePassword', '�������� ������');
define('admUsersPasswordChanged', '������� ������');
define('admUsersNameInvalid', '��������� ������������ �� ����� ���� ������.');
define('admUsersLoginInvalid', '����� �� ����� ���� ������ � ������ �������� ������ �� ���� a-z, ���� � ������� �������������.');
define('admUsersLoginExists', '������������ � ����� ������� ��� ����������.');
define('admUsersMailInvalid', '������� ������ �������� �����.');
define('admUsersConfirmInvalid', '������ � ������������� �� ���������.');
define('admUsersCreate', '������� ������������');
define('admUsersAdded', '��������� ������� ������');

define('admPagesMove', '����������� �����');
define('admPagesRoot', '������');
define('admPagesContentDefault', '�� ���������');
define('admPagesContentList', '������ �����������');
define('admPagesContentURL', 'URL');
define('admPagesThisURL', 'URL ���� ��������');
define('admPagesID', 'ID ��������');
define('admPagesName', '��� ��������');
define('admPagesNameInvalid', '��� �������� �� ����� ���� ������ � ����� �������� �� ��������� ����, ���� � ������� �������������.');
define('admPagesTitle', '��������� ��������');
define('admPagesTitleInvalid', '��������� �������� �� ����� ���� ������');
define('admPagesCaption', '�������� ������ ����');
define('admPagesCaptionInvalid', '����� ���� �� ����� ���� ������');
define('admPagesDescription', '��������');
define('admPagesKeywords', '�������� �����');
define('admPagesHint', '���������');
define('admPagesContentType', '��� ��������');
define('admPagesTemplate', '������');
define('admPagesActive', '�������');
define('admPagesVisible', '�������');
define('admPagesCreated', '���� ��������');
define('admPagesUpdated', '���� ����������');
define('admPagesUpdatedAuto', '�������� ���� ��������� �������������');
define('admPagesOptions', '�������������� �����');
define('admPagesContent', '������� ��������');

define('admTemplList', '������ �������� ������ ��������');
define('admTemplListLabel', '������ ������ ��������. ����������� ������ $(items) ��� ������� ������. ��� ��������� ���������� ��������� ������ �������� ��� �������� ������ <a href="'.httpRoot.'admin.php?mod=themes&section=std">'.admTemplList.'</a>');
define('admTemplListItemLabel', '������ �������� ������ ��������. ������� <strong>$(title)</strong> - ���������; <strong>$(caption)</strong> - ����� ����; <strong>$(description)</strong> - ��������; <strong>$(hint)</strong> - ���������; <strong>$(link)</strong> - ������.');

define('admTemplPageSelector', '������ ������������� �������');
define('admTemplPageSelectorLabel', '������ ������� �� 5-� ������, ����������� ������� ������� (---):<ol><li>������������� �������, ������ $(pages) ����� ��������� ������������� �����������.</li><li>������ ��������� ��������, $(number) - ����� ��������, $(href) - ������</li><li>������ ������� ��������, $(number) - ����� ��������, $(href) - ������</li><li>������ �������� � ������ ��������, $(href) - ������</li><li>������ �������� � ��������� ��������, $(href) - ������</li></ol>');

define('templPosted', '$(posted)');

define('ERR_PLUGIN_NOT_AVAILABLE', '������ ���������� "%s" �� ���������� ��� ��������.');