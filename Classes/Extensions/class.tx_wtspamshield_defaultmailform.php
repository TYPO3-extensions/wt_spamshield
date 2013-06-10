<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Alexander Kellner <Alexander.Kellner@einpraegsam.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * defaultmailform hook
 *
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class tx_wtspamshield_defaultmailform extends tslib_pibase {

	/**
	 * @var array
	 */
	protected $messages = array();

	/**
	 * @var tx_wtspamshield_div
	 */
	protected $div;

	/**
	 * getDiv
	 * 
	 * @return tx_wtspamshield_div
	 */
	protected function getDiv() {
		if (!isset($this->div)) {
			$this->div = t3lib_div::makeInstance('tx_wtspamshield_div');
		}
		return $this->div;
	}

	/**
	 * Function generateSession() is called if the form is
	 * rendered (generate a session)
	 *
	 * @param string $content
	 * @param array $configuration
	 * @return string
	 */
	public function generateSession($content, array $configuration = NULL) {
		if ( $this->getDiv()->isActivated('standardMailform') ) {
			$this->getDiv()->getExtConf();
			$forceValue = !(isset($configuration['ifOutdated']) && $configuration['ifOutdated']);

				// Set session on form create
			$methodSessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$methodSessionInstance->setSessionTime($forceValue);
		}

		return $content;
	}

	/**
	 * Function sendFormmail_preProcessVariables() is called after
	 * submit - stop mail if needed
	 *
	 * @param object $form Form Object
	 * @param object $obj Parent Object
	 * @param array $legacyConfArray legacy configuration
	 * @return object $form
	 */
	public function sendFormmail_preProcessVariables($form, $obj, $legacyConfArray = array()) {
		if ( $this->getDiv()->isActivated('standardMailform') ) {
			$error = $this->processValidationChain($form);

				// 2c. Redirect and stop mail sending
			if (!empty($error)) {
				$link = (!empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['redirect.']['standardMailform'])
					? $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['redirect.']['standardMailform']
					: t3lib_div::getIndpEnv('TYPO3_SITE_URL'));
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: ' . $link);
				header('Connection: close');
				return FALSE;
			}
		}

		return $form;
	}

	/**
	 * processValidationChain
	 * 
	 * @param array $fieldValues
	 * @return string
	 */
	protected function processValidationChain(array $fieldValues) {
		$error = '';

			// 1a. blacklistCheck
		if (!$error) {
			$methodBlacklistInstance = t3lib_div::makeInstance('tx_wtspamshield_method_blacklist');
			$error .= $methodBlacklistInstance->checkBlacklist($fieldValues);
		}

			// 1b. sessionCheck
		if (!$error) {
			$methodSessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$error .= $methodSessionInstance->checkSessionTime();
		}

			// 1c. httpCheck
		if (!$error) {
			$methodHttpcheckInstance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck');
			$error .= $methodHttpcheckInstance->httpCheck($fieldValues);
		}

			// 1d. uniqueCheck
		if (!$error) {
			$methodUniqueInstance = t3lib_div::makeInstance('tx_wtspamshield_method_unique');
			$error .= $methodUniqueInstance->main($fieldValues);
		}

			// 1e. honeypotCheck
		if (!$error) {
			$honeypotInputName = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['honeypot.']['inputname.']['standardMailform'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$error .= $methodHoneypotInstance->checkHoney($fieldValues);
		}

			// 2a. Safe log file
		if ($error) {
			$methodLogInstance = t3lib_div::makeInstance('tx_wtspamshield_log');
			$methodLogInstance->dbLog('standardMailform', $error, $fieldValues);
		}

			// 2b. Send email to admin
		if ($error) {
			$methodSendEmailInstance = t3lib_div::makeInstance('tx_wtspamshield_mail');
			$methodSendEmailInstance->sendEmail('standardMailform', $error, $fieldValues);
		}

		return $error;
	}
}

if (defined('TYPO3_MODE')
	&& isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_defaultmailform.php'])
) {
	require_once ($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_defaultmailform.php']);
}

?>