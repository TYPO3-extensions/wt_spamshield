<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Bjoern Jacob <bjoern.jacob@tritum.de>
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
 * t3blog hook
 *
 * @author Bjoern Jacob <bjoern.jacob@tritum.de>
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class tx_wtspamshield_t3blog extends tslib_pibase {

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
	* Implementation of Hook "insertNewComment" from t3_blog
	* 
	* @param array &$params The parameters
	* @param object &$reference The refering object
	* @return void
	*/
	public function insertNewComment(&$params, &$reference) {
		$error = '';

		$validateArray = $params['data'];

		if ( $this->getAbstract()->isActivated('t3_blog') ) {

			$error = $this->processValidationChain($validateArray);

			if (!empty($error)) {
					// Right now we cannot set errorMessage because it is
					// protected, see forge.typo3.org #42615
					// $reference->errorMessage = $error;
					// Mark as spam
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery($params['table'], 'uid = ' . $params['commentUid'], array('spam' => 1));
			}
		}
	}

	/**
	 * processValidationChain
	 * 
	 * @param array $fieldValues
	 * @return string
	 */
	protected function processValidationChain(array $fieldValues) {
		$error = '';

			// 1c. httpCheck
		if (!$error) {
			$methodHttpcheckInstance = t3lib_div::makeInstance('tx_wtspamshield_method_httpcheck');
			$error .= $methodHttpcheckInstance->httpCheck($fieldValues);
		}

			// 1f. Akismet Check
		if (!$error) {
			$methodAkismetInstance = t3lib_div::makeInstance('tx_wtspamshield_method_akismet');
			$error .= $methodAkismetInstance->checkAkismet($fieldValues, 't3_blog');
		}

			// 2a. Safe log file
		if ($error) {
			$methodLogInstance = t3lib_div::makeInstance('tx_wtspamshield_log');
			$methodLogInstance->dbLog('t3_blog', $error, $fieldValues);
		}

			// 2b. Send email to admin
		if ($error) {
			$methodSendEmailInstance = t3lib_div::makeInstance('tx_wtspamshield_mail');
			$methodSendEmailInstance->sendEmail('t3_blog', $error, $fieldValues);
		}

		return $error;
	}
}

if (defined('TYPO3_MODE')
	&& isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_t3blog.php'])
) {
	require_once ($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_spamshield/Classes/Extensions/class.tx_wtspamshield_t3blog.php']);
}

?>