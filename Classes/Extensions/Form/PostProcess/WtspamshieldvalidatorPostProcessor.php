<?php
namespace TYPO3\CMS\Form\PostProcess;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 Ralf Zimmermann <ralf.zimmermann@tritum.de>, TRITUM GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * WtspamshieldvalidatorPostProcessor
 */
class WtspamshieldvalidatorPostProcessor implements \TYPO3\CMS\Form\PostProcess\PostProcessorInterface {

	/**
	 * @var tx_wtspamshield_div
	 */
	protected $div;

	/**
	 * @var mixed
	 */
	public $additionalValues = array();

	/**
	 * @var string
	 */
	public $tsKey = 'standardMailform';

	/**
	 * @var mixed
	 */
	public $tsConf;

	/**
	 * @var \TYPO3\CMS\Form\Domain\Model\Form
	 */
	public $form;

	/**
	 * @var array
	 */
	public $typoScript;

	/**
	 * @var \TYPO3\CMS\Form\Request
	 */
	public $requestHandler;

	/**
	 * Constructor
	 *
	 * @param \TYPO3\CMS\Form\Domain\Model\Form $form Form domain model
	 * @param array $typoScript Post processor TypoScript settings
	 */
	public function __construct(\TYPO3\CMS\Form\Domain\Model\Form $form, array $typoScript) {
		$this->form = $form;
		$this->typoScript = $typoScript;
		$this->requestHandler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Form\\Request');

		$this->tsConf = $this->getDiv()->getTsConf();
		$honeypotInputName = $this->tsConf['honeypot.']['inputname.'][$this->tsKey];
		$this->additionalValues['honeypotCheck']['prefixInputName'] = 'tx_form';
		$this->additionalValues['honeypotCheck']['honeypotInputName'] = $honeypotInputName;

	}

	/**
	 *
	 * @return void
	 */
	public function process() {
		if ( $this->getDiv()->isActivated($this->tsKey) ) {
			$error = '';

			$this->requestHandler->setMethod('session');
			if ($this->requestHandler->hasRequest()) {
				$validateArray = $this->requestHandler->getSession();
				$error = $this->validate($validateArray);
			} else if ($this->requestHandler->getPost()) {
				$validateArray = $this->requestHandler->getPost();
				$error = $this->validate($validateArray);
			} else {
				$error = 'no form data';
			}

			$urlConf = array('parameter' => 0);

			if (strlen($error) > 0) {
				if ($this->typoScript['errorDestination']) {
					$urlConf = array('parameter' => $this->typoScript['errorDestination']);
				}

				$this->requestHandler->destroySession();
				$destination = $GLOBALS['TSFE']->cObj->typoLink_URL($urlConf);
				\TYPO3\CMS\Core\Utility\HttpUtility::redirect($destination);
			}
		}
	}

	/**
	 * getDiv
	 * 
	 * @return tx_wtspamshield_div
	 */
	protected function getDiv() {
		if (!isset($this->div)) {
			$this->div = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_wtspamshield_div');
		}
		return $this->div;
	}

	/**
	 * validate
	 * 
	 * @param array $fieldValues
	 * @return string
	 */
	protected function validate(array $fieldValues) {

		$availableValidators =
			array(
				'blacklistCheck',
				'httpCheck',
				'honeypotCheck',
				'akismetCheck'
			);

		$tsValidators = $this->getDiv()->commaListToArray($this->tsConf['validators.'][$this->tsKey . '_new.']['enable']);

		$processor = $this->getDiv()->getProcessor();
		$processor->tsKey = $this->tsKey;
		$processor->fieldValues = $fieldValues;
		$processor->additionalValues = $this->additionalValues;
		$processor->failureRate = intval($this->tsConf['validators.'][$this->tsKey . '_new.']['how_many_validators_can_fail']);
		$processor->methodes = array_intersect($tsValidators, $availableValidators);

		$error = $processor->validate();
		return $error;
	}
}
