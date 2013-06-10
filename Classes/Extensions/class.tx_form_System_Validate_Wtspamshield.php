<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Ralf Zimmermann <Ralf.Zimmermann@tritum.de>
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
 * tx_wtspamshield hook
 *
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class tx_form_System_Validate_Wtspamshield extends tx_form_System_Validate_Abstract {

	/**
	 * @var tx_wtspamshield_div
	 */
	protected $div;

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 * @return void
	 */
	public function __construct($arguments) {
		parent::__construct($arguments);
	}

	/**
	 * getDiv
	 * 
	 * @return	tx_wtspamshield_div
	 */
	protected function getDiv() {
		if (!isset($this->div)) {
			$this->div = t3lib_div::makeInstance('tx_wtspamshield_div');
		}
		return $this->div;
	}

	/**
	 * Returns TRUE if submitted value validates according to rule
	 *
	 * @return	boolean
	 * @see tx_form_System_Validate_Interface::isValid()
	 */
	public function isValid() {

		if ( $this->getDiv()->isActivated('standardMailform') ) {
			$error = '';

			if ($this->requestHandler->has($this->fieldName)) {
				$value = $this->requestHandler->getByMethod($this->fieldName);
				$validateArray = array(
					$this->fieldName => $value
				);
				$error = $this->processValidationChain($validateArray);
			}

			if (!empty($error)) {
				$this->setError('', strip_tags($error));
				return FALSE;
			}
		}

		return TRUE;
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

			// 1e. honeypotCheck
		if (!$error) {
			$tsConf = $this->getDiv()->getTsConf();
			$honeypotInputName = $tsConf['honeypot.']['inputname.']['standardMailform'];
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
	&& isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_form_System_Validate_Wtspamshield.php'])
) {
	require_once ($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_form_System_Validate_Wtspamshield.php']);
}

?>