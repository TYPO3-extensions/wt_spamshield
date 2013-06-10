<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Lina Wolf <2010@lotypo3.de>
*  based on Code of Alexander Kellner <Alexander.Kellner@einpraegsam.net>
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
 * tx_comments hook
 * 
 * @author Lina Wolf <2010@lotypo3.de>
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class tx_wtspamshield_comments extends tslib_pibase {

	/**
	 * @var tx_wtspamshield_div
	 */
	protected $div;

	/**
	 * @var string
	 */
	public $prefixInputName = 'tx_comments_pi1';

	/**
	 * @var int
	 */
	public $points;

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
	* Implementation of Hook "form" from tx_comments (when the form is rendered)
	* Adds the Honeypot input field to the marker ###JS_USER_DATA###
	* (part of the default template)
	* 
	* @param mixed $params array of 'pObject' => Name of extension 'markers'
	* 								array of markers 'template' the template
	* @param mixed $pObj 
	* @return mixed $markers the changed marker array
	*/
	public function form($params, $pObj) {
		$template = $params['template'];
		$markers = $params['markers'];

		if ( $this->getDiv()->isActivated('comments') ) {

				// 1. check Extension Manager configuration
			$this->getDiv()->getExtConf();

				// 2. Session check - generate session entry
			$methodSessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$methodSessionInstance->setSessionTime();

				// 3. Honeypot check - generate honeypot Input field
			$tsConf = $this->getDiv()->getTsConf();
			$honeypotInputName = $tsConf['honeypot.']['inputname.']['comments'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$methodHoneypotInstance->prefixInputName = $this->prefixInputName;
			$markers['###JS_USER_DATA###'] = $methodHoneypotInstance->createHoneypot() . $markers['###JS_USER_DATA###'];
		}
		return $markers;
	}

	/**
	* Implementation of Hook "externalSpamCheck" from tx_comments 
	* Test for spam and addd 1000 spampoints for each Problem found
	* 
	* @param mixed $params array of 'pObject' => Name of extension 'form'
	* 							array of fields in the form 'points' excistent spam points
	* @param mixed $pObj 
	* @return integer $this->points number of spam points increased
	* 									by 100 for every problem that was found
	*/
	public function externalSpamCheck($params, $pObj) {
		$cObj = $GLOBALS['TSFE']->cObj;
		$error = '';
		$validateArray = $params['formdata'];
		$this->points = $params['points'];

		if ( $this->getDiv()->isActivated('comments') ) {
			$error = $this->processValidationChain($validateArray);
		}

		return $this->points;
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

			if (!empty($tempError)) {
				$this->points += 1000;
			}
			$error .= $tempError;
		}

			// 1b. nameCheck
		if (!$error) {
			$methodNamecheckInstance = t3lib_div::makeInstance('tx_wtspamshield_method_namecheck');
			$tempError = $methodNamecheckInstance->nameCheck($fieldValues['firstname'], $fieldValues['lastname']);

			if (!empty($tempError)) {
				$this->points += 1000;
			}
			$error .= $tempError;
		}

			// 1c. httpCheck
		if (!$error) {
			$methodHttpcheckInstance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck');
			$tempError = $methodHttpcheckInstance->httpCheck($fieldValues);

			if (!empty($tempError)) {
				$this->points += 1000;
			}
			$error .= $tempError;
		}

			// 1d. sessionCheck
		if (!$error) {
			$methodSessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$tempError = $methodSessionInstance->checkSessionTime();

			if (!empty($tempError)) {
				$this->points += 1000;
			}
			$error .= $tempError;
		}

			// 1e. honeypotCheck
		if (!$error) {
			$tsConf = $this->getDiv()->getTsConf();
			$honeypotInputName = $tsConf['honeypot.']['inputname.']['comments'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$tempError = $methodHoneypotInstance->checkHoney($fieldValues);

			if (!empty($tempError)) {
				$this->points += 1000;
			}
			$error .= $tempError;
		}

			// 1f. Akismet Check
		if (!$error) {
			$methodAkismetInstance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet');
			$tempError =  $methodAkismetInstance->checkAkismet($fieldValues, 'comments');

			if (!empty($tempError)) {
				$this->points += 1000;
			}
			$error .= $tempError;
		}

			// 2a. Safe log file
		if ($error) {
			$methodLogInstance = t3lib_div::makeInstance('tx_wtspamshield_log');
			$methodLogInstance->dbLog('comments', $error, $fieldValues);
		}

			// 2b. Send email to admin
		if ($error) {
			$methodSendEmailInstance = t3lib_div::makeInstance('tx_wtspamshield_mail');
			$methodSendEmailInstance->sendEmail('comments', $error, $fieldValues);
		}

		return $error;
	}
}

if (defined('TYPO3_MODE')
	&& isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_comments.php'])
) {
	require_once ($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_comments.php']);
}

?>