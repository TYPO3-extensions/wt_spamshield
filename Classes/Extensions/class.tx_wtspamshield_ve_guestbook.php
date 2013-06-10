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
 * ve_guestbook hook
 *
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class tx_wtspamshield_ve_guestbook extends tslib_pibase {

	/**
	 * @var string
	 */
	public $prefixInputName = 'tx_veguestbook_pi1';

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
	 * Function is called if form is rendered (set tstamp in session)
	 *
	 * @param array &$markerArray Array with markers
	 * @param array $row Values from database
	 * @param array $config configuration
	 * @param object &$obj parent object
	 * @return array $markerArray
	 */
	public function extraItemMarkerProcessor(&$markerArray, $row, $config, &$obj) {

		if (
			$obj->code == 'FORM' &&
			$this->getDiv()->isActivated('ve_guestbook')
		) {
				// 1. check Extension Manager configuration
			$this->getDiv()->getExtConf();

				// 2. Session check - generate session entry
			$methodSessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$methodSessionInstance->setSessionTime();

				// 3. Honeypot check - generate honeypot Input field
			$honeypotInputName = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['honeypot.']['inputname.']['ve_guestbook'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$methodHoneypotInstance->prefixInputName = $this->prefixInputName;
			$obj->templateCode = str_replace('</form>', $methodHoneypotInstance->createHoneypot() . '</form>', $obj->templateCode);
		}
		return $markerArray;
	}

	/**
	 * Function preEntryInsertProcessor is called from a guestbook hook
	 * and gives the possibility to disable the db entry of the GB
	 *
	 * @param array $saveData Values to save
	 * @param object &$obj parent object
	 * @return array $saveData
	 */
	public function preEntryInsertProcessor($saveData, &$obj) {
		$cObj = $GLOBALS['TSFE']->cObj;
		$error = '';

		if ( $this->getDiv()->isActivated('ve_guestbook') ) {
				// get GPvars, downwards compatibility
			$t3Version = class_exists('t3lib_utility_VersionNumber')
				? t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version)
				: t3lib_div::int_from_ver(TYPO3_version);
			if ($t3Version < 4006000) {
				$validateArray = t3lib_div::GPvar('tx_veguestbook_pi1');
			} else {
				$validateArray = t3lib_div::_GP('tx_veguestbook_pi1');
			}
			$error = $this->processValidationChain($validateArray);

				// 2c. Truncate ve_guestbook temp table
			if ($error) {
				mysql_query('TRUNCATE TABLE tx_wtspamshield_veguestbooktemp');
			}

				// 2d. Redirect if error happens
			if (!empty($error)) {
				$saveData = array('tstamp' => time());
				$obj->strEntryTable = 'tx_wtspamshield_veguestbooktemp';
				$obj->config['notify_mail'] = '';
				$obj->config['feedback_mail'] = FALSE;
				if ( intval($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['redirect.']['ve_guestbook']) > 0) {
					$obj->config['redirect_page'] =
						intval($GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['redirect.']['ve_guestbook']);
				} else {
					$obj->config['redirect_page'] = $GLOBALS['TSFE']->tmpl->rootLine[0]['uid'];
				}
				unset($obj->tt_news);
			}
		}
		return $saveData;
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

			// 1b. nameCheck
		if (!$error) {
			$methodNamecheckInstance = t3lib_div::makeInstance('tx_wtspamshield_method_namecheck');
			$error .= $methodNamecheckInstance->nameCheck($fieldValues['firstname'], $fieldValues['surname']);
		}

			// 1c. httpCheck
		if (!$error) {
			$methodHttpcheckInstance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck');
			$error .= $methodHttpcheckInstance->httpCheck($fieldValues);
		}

			// 1d. sessionCheck
		if (!$error) {
			$methodSessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$error .= $methodSessionInstance->checkSessionTime();
		}

			// 1e. honeypotCheck
		if (!$error) {
			$honeypotInputName = $GLOBALS['TSFE']->tmpl->setup['plugin.']['wt_spamshield.']['honeypot.']['inputname.']['ve_guestbook'];
			$methodHoneypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');
			$methodHoneypotInstance->inputName = $honeypotInputName;
			$error .= $methodHoneypotInstance->checkHoney($fieldValues);
		}

			// 1f. Akismet Check
		if (!$error) {
			$methodAkismetInstance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet');
			$error .= $methodAkismetInstance->checkAkismet($fieldValues, 've_guestbook');
		}

			// 2a. Safe log file
		if ($error) {
			$methodLogInstance = t3lib_div::makeInstance('tx_wtspamshield_log');
			$methodLogInstance->dbLog('ve_guestbook', $error, $fieldValues);
		}

			// 2b. Send email to admin
		if ($error) {
			$methodSendEmailInstance = t3lib_div::makeInstance('tx_wtspamshield_mail');
			$methodSendEmailInstance->sendEmail('ve_guestbook', $error, $fieldValues);
		}

		return $error;
	}
}

if (defined('TYPO3_MODE')
	&& isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_ve_guestbook.php'])
) {
	require_once ($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_ve_guestbook.php']);
}

?>