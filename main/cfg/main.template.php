<?php
/**
 * ${product.title} ${product.version}
 *
 * ���� ������������
 *
 * @package Core
 *
 * $Id$
 */

// �������� ��� ��������� ����� �������. �� ���������: false
$Eresus->conf['debug']['enable'] = false;

//-------------------------------------------------------------------------------
//  ��������� ��������� ������ (����)
//-------------------------------------------------------------------------------

// FIXME �����������������!
Eresus_Config::set('eresus.cms.dsn', 'mysql://user:password@localhost/database');
//Eresus_Config::set('eresus.cms.dsn.prefix', 'prefix_');

//-------------------------------------------------------------------------------
//  ������������ ���������
//-------------------------------------------------------------------------------

// ������ �� ���������.
//Eresus_Config::set('eresus.cms.locale', 'ru_RU');

// ��������� ���� �� ��������� (��� PHP 5.1.0+)
//$Eresus->conf['timezone'] = 'Europe/Moscow';

//-------------------------------------------------------------------------------
//  ��������� ������
//-------------------------------------------------------------------------------

// ������� ������ � �������. �� ���������: 30
//$Eresus->conf['session']['timeout'] = 30;

//-------------------------------------------------------------------------------
//  ���������� � URL
//-------------------------------------------------------------------------------

// �������� ���������� �����. ���������������� ���� Eresus �� ����� ���������� ���� ��������������
//$Eresus->froot = '/usr/home/site.tld/htdocs/';

// ���� �����. ���������������� ���� Eresus �� ����� ���������� ��� ��������������
//$Eresus->host = 'example.org';

// ���� �� ����� �� ����� �����. ���������������� ���� Eresus �� ����� ���������� ��� ��������������
//$Eresus->path = '/site_path/';

//-------------------------------------------------------------------------------
//  ������� � ������
//-------------------------------------------------------------------------------

// $Eresus->conf['debug']['mail'] - ���������� �������� �����
// = false       - ���������
// = true        - ���������� ��� ������
// = <���_�����> - ���������� � ����

if ($Eresus->conf['debug']['enable'])
{
	ini_set('display_errors', true);
	error_reporting(E_ALL);
	$Eresus->conf['debug']['mail'] = realpath(dirname(__FILE__)).'/../data/.sent';
}
else
{
	ini_set('display_errors', false);
	error_reporting(0);
}
