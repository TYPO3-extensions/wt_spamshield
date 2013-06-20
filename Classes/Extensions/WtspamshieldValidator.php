<?php
namespace TYPO3\CMS\Form\Validation;

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
 * wtspamshield rule
 *
 * @author Ralf Zimmermann <ralf.zimmermann@tritum.de>
 * @package tritum
 * @subpackage wt_spamshield
 */
class WtspamshieldValidator extends \TYPO3\CMS\Form\Validation\AbstractValidator {

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
	 * Constructor
	 *
	 * @param array $arguments
	 * @return	void
	 */
	public function __construct($arguments) {
		$this->tsConf = $this->getDiv()->getTsConf();
		parent::__construct($arguments);
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
	 * Returns TRUE if submitted value validates according to rule
	 *
	 * @return boolean
	 * @see tx_form_System_Validate_Interface::isValid()
	 */
	public function isValid() {

		if ( $this->getDiv()->isActivated($this->tsKey) ) {
			$error = '';

			if ($this->requestHandler->has($this->fieldName)) {
				$value = $this->requestHandler->getByMethod($this->fieldName);
				$validateArray = array(
					$this->fieldName => $value
				);
				$error = $this->validate($validateArray);
			}

			if (!empty($error)) {
				$this->setError('', strip_tags($error));
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * validate
	 * 
	 * @param array $fieldValues
	 * @return string
	 */
	protected function validate(array $fieldValues) {

		$processor = $this->getDiv()->getProcessor();
		$processor->tsKey = $this->tsKey;
		$processor->fieldValues = $fieldValues;
		$processor->additionalValues = $this->additionalValues;
		$processor->maxPoints = $this->tsConf['maxPoints'];
		$processor->methodes =
			array(
				'blacklistCheck',
				'httpCheck',
				'honeypotCheck',
			);
		$error = $processor->validate();
		return $error;
	}

}
?>