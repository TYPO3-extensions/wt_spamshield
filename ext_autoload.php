<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

$extPath = t3lib_extMgm::extPath('wt_spamshield');

$return = array(
'tx_wtspamshield_AkismetObject' => $extPath . 'Classes/System/class.tx_wtspamshield_akismet.php',
'tx_wtspamshield_AkismetHttpClient' => $extPath . 'Classes/System/class.tx_wtspamshield_akismet.php',
'tx_wtspamshield_akismet' => $extPath . 'Classes/System/class.tx_wtspamshield_akismet.php',
'tx_wtspamshield_div' => $extPath . 'Classes/System/class.tx_wtspamshield_div.php',
'tx_wtspamshield_log' => $extPath . 'Classes/System/class.tx_wtspamshield_log.php',
'tx_wtspamshield_mail' => $extPath . 'Classes/System/class.tx_wtspamshield_mail.php',

'tx_wtspamshield_method_abstract' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_abstract.php',
'tx_wtspamshield_method_akismet' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_akismet.php',
'tx_wtspamshield_method_blacklist' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_blacklist.php',
'tx_wtspamshield_method_honeypot' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_honeypot.php',
'tx_wtspamshield_method_httpcheck' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_httpcheck.php',
'tx_wtspamshield_method_namecheck' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_namecheck.php',
'tx_wtspamshield_method_session' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_session.php',
'tx_wtspamshield_method_unique' => $extPath . 'Classes/Methodes/class.tx_wtspamshield_method_unique.php',

'tx_wtspamshield_extensions_abstract' => $extPath . 'Classes/Extensions/class.tx_wtspamshield_extensions_abstract.php',
);

if(t3lib_extMgm::isLoaded('direct_mail_subscription')) {
	$return['user_feAdmin'] = t3lib_extMgm::extPath('direct_mail_subscription') . 'fe_adminLib.inc';
}

return $return;
?>