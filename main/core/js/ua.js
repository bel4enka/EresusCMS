/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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

var UA = {ID: navigator.userAgent.toLowerCase()}

// *** BROWSER VERSION ***

// Note: On IE5, these return 4, so use is_ie5up to detect IE5.
UA.major = parseInt(navigator.appVersion);
UA.minor = parseFloat(navigator.appVersion);

// Gecko (Mozilla, FireFox, etc)
UA.Gecko  = UA.ID.indexOf('gecko') != -1;
// Presto (Opera)
UA.Opera  = UA.ID.indexOf('opera') != -1;
// Microsoft Internet Explorer
UA.MSIE   = (UA.ID.indexOf('msie') != -1) && (UA.ID.indexOf('opera') == -1) && (UA.ID.indexOf('webtv') == -1);
// Safari
UA.Safari = UA.ID.indexOf('safari') != -1;
// Konqueror
UA.Konqueror = UA.ID.indexOf('konqueror') != -1;

