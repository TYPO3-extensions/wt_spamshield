<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "wt_spamshield".
 *
 * Auto generated 13-11-2013 00:28
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Spamshield',
	'description' => 'Spam shield without captcha to avoid spam in powermail, ve_guestbook, comments, t3_blog, direct_mail_subscription and standard TYPO3 mailforms. Session check, Link check, Time check, Akismet check, Name check, Honeypot check (see manual for details)',
	'category' => 'services',
	'shy' => 0,
	'version' => '1.2.0-dev',
	'dependencies' => '',
	'conflicts' => 'mf_akismet,wt_calculating_captcha',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Alex Kellner, Bjoern Jacob, Ralf Zimmermann',
	'author_email' => 'alexander.kellner@in2code.de, bj@tritum.de, rz@tritum.de',
	'author_company' => 'in2code, TRITUM',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-0.0.0',
			'typo3' => '4.5.0-6.1.99',
		),
		'conflicts' => array(
			'mf_akismet' => '0.0.0-9.9.9',
			'wt_calculating_captcha' => '0.0.0-0.0.0',
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:42:{s:9:"ChangeLog";s:4:"b4fc";s:16:"ext_autoload.php";s:4:"d41e";s:21:"ext_conf_template.txt";s:4:"ef26";s:12:"ext_icon.gif";s:4:"8a2a";s:17:"ext_localconf.php";s:4:"887c";s:14:"ext_tables.php";s:4:"db6a";s:14:"ext_tables.sql";s:4:"a9c5";s:65:"Classes/Extensions/class.tx_form_System_Validate_Wtspamshield.php";s:4:"4a03";s:53:"Classes/Extensions/class.tx_wtspamshield_comments.php";s:4:"7e29";s:60:"Classes/Extensions/class.tx_wtspamshield_defaultmailform.php";s:4:"dc98";s:60:"Classes/Extensions/class.tx_wtspamshield_ke_userregister.php";s:4:"15e2";s:54:"Classes/Extensions/class.tx_wtspamshield_powermail.php";s:4:"0575";s:51:"Classes/Extensions/class.tx_wtspamshield_t3blog.php";s:4:"89e1";s:57:"Classes/Extensions/class.tx_wtspamshield_ve_guestbook.php";s:4:"e333";s:74:"Classes/Extensions/class.user_tx_wtspamshield_direct_mail_subscription.php";s:4:"35f7";s:44:"Classes/Extensions/WtspamshieldValidator.php";s:4:"db52";s:58:"Classes/Methodes/class.tx_wtspamshield_method_abstract.php";s:4:"e1d9";s:57:"Classes/Methodes/class.tx_wtspamshield_method_akismet.php";s:4:"9d6c";s:59:"Classes/Methodes/class.tx_wtspamshield_method_blacklist.php";s:4:"663a";s:58:"Classes/Methodes/class.tx_wtspamshield_method_honeypot.php";s:4:"3533";s:59:"Classes/Methodes/class.tx_wtspamshield_method_httpcheck.php";s:4:"0912";s:59:"Classes/Methodes/class.tx_wtspamshield_method_namecheck.php";s:4:"9b92";s:57:"Classes/Methodes/class.tx_wtspamshield_method_session.php";s:4:"8350";s:56:"Classes/Methodes/class.tx_wtspamshield_method_unique.php";s:4:"7d7e";s:52:"Classes/Methodes/class.tx_wtspamshield_processor.php";s:4:"0ba3";s:48:"Classes/System/class.tx_wtspamshield_akismet.php";s:4:"8323";s:59:"Classes/System/class.tx_wtspamshield_akismet_httpclient.php";s:4:"8883";s:55:"Classes/System/class.tx_wtspamshield_akismet_object.php";s:4:"7688";s:44:"Classes/System/class.tx_wtspamshield_div.php";s:4:"05dd";s:44:"Classes/System/class.tx_wtspamshield_log.php";s:4:"7eec";s:45:"Classes/System/class.tx_wtspamshield_mail.php";s:4:"4e53";s:31:"Configuration/TCA/Blacklist.php";s:4:"6cf5";s:25:"Configuration/TCA/Log.php";s:4:"c461";s:38:"Configuration/TypoScript/constants.txt";s:4:"644a";s:34:"Configuration/TypoScript/setup.txt";s:4:"f523";s:61:"Configuration/TypoScript/Extensions/defaultmailform/setup.txt";s:4:"036b";s:70:"Configuration/TypoScript/Extensions/direct_mail_subscription/setup.txt";s:4:"59da";s:40:"Resources/Private/Language/locallang.xml";s:4:"9e6d";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"6f1c";s:52:"Resources/Public/Icons/tx_wtspamshield_blacklist.gif";s:4:"f140";s:46:"Resources/Public/Icons/tx_wtspamshield_log.gif";s:4:"f140";s:14:"doc/manual.sxw";s:4:"9481";}',
);

?>