<?php
/**
 * Eresus 2.11
 *
 * ������������ ����������
 *
 * ���� ���� �������� ������ ������������� ����������, � ��� �� �� ���������.
 * ���������� ����������� � ���������� ext-3rd, ������ � ��������� �������������.
 * � ���������� ���������� ������ ���������� ���� eresus-conntecor.php, ��������������
 * �������������� ����� ���������� � Eresus.
 *
 * ��� ���������� ������� �� �������, ������������ ������� ���������� ����������. ������
 * ������ ��� ��� �� ������� �� ��������, ������� ��� ���������.
 *
 * ������ ����������� ������� � ���� �������������� ������� ������������� ��� �������������
 * ����������. � �������� ����� ������ �������������� ��� ����������, � ������� �����������
 * ����������. ������ ������ ���� �� ��������, ����������� �������� null.
 *
 * ������� ���������� ��������� Eresus� 2
 * � 2004-2007, ProCreat Systems, http://procreat.ru/
 * � 2007-2008, Eresus Group, http://eresus.ru/
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */


$GLOBALS['Eresus']->conf['extensions'] = array(
	# ���������� ������������ ���� �����
	'forms' => array(
		# ���������� ����� ���� memo
		'memo_syntax' => array(
			'codepress' => null,
		),
		# ���������� ����� ���� html
		'html' => array(
			'xinha' => null,
			'tiny_mce' => null,
		),
	),
);

?>