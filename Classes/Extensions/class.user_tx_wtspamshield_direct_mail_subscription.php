<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Ralf Zimmermann <ralf.zimmermann@tritum.de>
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
*/

/**
 * direct_mail_subscription hook
 *
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class user_tx_wtspamshield_direct_mail_subscription extends user_feAdmin {

	/**
	 * @var string
	 */
	public $prefixInputName = 'FE[tt_address]';

	/**
	 * @var string
	 */
	public $spamshieldDisplayError;

	/**
	 * @var tx_wtspamshield_extensions_abstract
	 */
	protected $abstract;

	/**
	 * getAbstract
	 * 
	 * @return tx_wtspamshield_div
	 */
	protected function getAbstract() {
		if (!isset($this->abstract)) {
			$this->abstract = t3lib_div::makeInstance('tx_wtspamshield_extensions_abstract');
		}
		return $this->abstract;
	}

	/**
	 * displayCreateScreen
	 * 
	 * @return mixed
	 */
	public function displayCreateScreen() {

		if ( $this->getAbstract()->isActivated('direct_mail_subscription') ) {
			$honeypotInputName =
				$GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['honeypot.']['inputname.']['direct_mail_subscription'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$methodHoneypotInstance->prefixInputName = $this->prefixInputName;
			$this->markerArray['###HIDDENFIELDS###'] .= $methodHoneypotInstance->createHoneypot();
			if ($this->spamshieldDisplayError) {
				$this->markerArray['###HIDDENFIELDS###'] .= $this->spamshieldDisplayError;
			}
		}

		return parent::displayCreateScreen();
	}

	/**
	 * save
	 * 
	 * @return	mixed
	 */
	public function save() {
		$error = '';

		if ( $this->getAbstract()->isActivated('direct_mail_subscription') ) {
			$validateArray = $this->dataArr;
			$error = $this->processValidationChain($validateArray);

			if (!empty($error)) {
					// error handling
				$methodLogInstance = t3lib_div::makeInstance('tx_wtspamshield_log');
				$methodLogInstance->dbLog('direct_mail_subscription', $error, $validateArray);
					// $this->error='###TEMPLATE_NO_PERMISSIONS###';
				$this->saved = 0;
				$this->cmd = 'create';
				$this->spamshieldDisplayError = $error;
			} else {
				return parent::save();
			}
		}

		return parent::save();
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
			$honeypotInputName =
				$GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['honeypot.']['inputname.']['direct_mail_subscription'];
			$honeypotInputName = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$honeypotInputName->inputName = $honeypotInputName;
			$error .= $honeypotInputName->checkHoney($fieldValues);
		}

		return $error;
	}
}

if (defined('TYPO3_MODE')
	&& isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.user_tx_wtspamshield_direct_mail_subscription.php'])
) {
	require_once ($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.user_tx_wtspamshield_direct_mail_subscription.php']);
}

?>