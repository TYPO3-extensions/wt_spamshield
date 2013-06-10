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
 * powermail hook
 *
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class tx_wtspamshield_powermail extends tslib_pibase {

	/**
	 * @var string
	 */
	public $prefixInputName = 'tx_powermail_pi1';

	/**
	 * @var tx_wtspamshield_extensions_abstract
	 */
	protected $abstract;

	/**
	 * getAbstract
	 * 
	 * @return	tx_wtspamshield_div
	 */
	protected function getAbstract() {
		if (!isset($this->abstract)) {
			$this->abstract = t3lib_div::makeInstance('tx_wtspamshield_extensions_abstract');
		}
		return $this->abstract;
	}

	/**
	 * Function PM_FormWrapMarkerHook() to manipulate whole formwrap
	 *
	 * @param array $outerMarkerArray Marker Array out of the loop from powermail
	 * @param array &$subpartArray subpartArray Array from powermail
	 * @param array $conf ts configuration from powermail
	 * @param array $obj Parent Object
	 * @return void
	 */
	public function PM_FormWrapMarkerHook($outerMarkerArray, &$subpartArray, $conf, $obj) {

		if ( $this->getAbstract()->isActivated('powermail') ) {
				// 1. check Extension Manager configuration
			$this->getAbstract()->getDiv()->checkConf();

				// 2. Set session on form create
			$methodSessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$methodSessionInstance->setSessionTime();

				// 3. Add Honeypot
			$honeypotInputName = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['honeypot.']['inputname.']['powermail'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$methodHoneypotInstance->prefixInputName = $this->prefixInputName;
			$subpartArray['###POWERMAIL_CONTENT###'] .= $methodHoneypotInstance->createHoneypot();
		}
	}


	/**
	 * Function PM_FieldWrapMarkerHook() to manipulate Fieldwraps
	 *
	 * @param array $obj Parent Object
	 * @param array $markerArray Marker Array from powermail
	 * @param array $sessiondata Values from powermail Session
	 * @return string If not false is returned, powermail will show
	 *                an error. If string is returned, powermail will
	 *                show this string as errormessage
	 */
	public function PM_SubmitBeforeMarkerHook($obj, $markerArray = array(), $sessiondata = array()) {
		$error = '';

		if ( $this->getAbstract()->isActivated('powermail') ) {

			$error = $this->processValidationChain($sessiondata);

				// 2c. Return Error message if exists
			if (!empty($error)) {
				return '<div class="wtspamshield-errormsg">' . $error . '</div>';
			}
		}

		return FALSE;
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
			$honeypotInputName = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['honeypot.']['inputname.']['powermail'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$error .= $methodHoneypotInstance->checkHoney($fieldValues);
		}

			// 1f. Akismet Check
		if (!$error) {
				// get GPvars, downwards compatibility
			$t3Version = class_exists('t3lib_utility_VersionNumber')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);
			if ($t3Version < 4006000) {
				$form = t3lib_div::GPvar('tx_powermail_pi1');
			} else {
				$form = t3lib_div::_GP('tx_powermail_pi1');
			}
			$methodAkismetInstance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet');
			$error .= $methodAkismetInstance->checkAkismet($form, 'powermail');
		}

			// 2a. Safe log file
		if ($error) {
			$methodLogInstance = t3lib_div::makeInstance('tx_wtspamshield_log');
			$methodLogInstance->dbLog('powermail', $error, $fieldValues);
		}

			// 2b. Send email to admin
		if ($error) {
			$methodSendEmailInstance = t3lib_div::makeInstance('tx_wtspamshield_mail');
			$methodSendEmailInstance->sendEmail('powermail', $error, $fieldValues);
		}

		return $error;
	}
}

if (defined('TYPO3_MODE')
	&& isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_powermail.php'])
) {
	require_once ($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_powermail.php']);
}

?>