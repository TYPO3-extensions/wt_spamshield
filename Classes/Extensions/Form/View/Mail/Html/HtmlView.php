<?php
namespace TRITUM\WtSpamshield\Form\View\Mail\Html;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Ralf Zimmermann <ralf.zimmermann@tritum.de>, TRITUM GmbH
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
 * HtmlView
 */
class HtmlView extends \TYPO3\CMS\Form\View\Mail\Html\HtmlView {

	/**
	 * Constructor
	 */
	public function __construct(\TYPO3\CMS\Form\Domain\Model\Form $model, array $typoscript) {
		parent::__construct($model, $typoscript);
	}

	/**
	 * Start the main DOMdocument for the form
	 * Return it as a string using saveXML() to get a proper formatted output
	 * (when using formatOutput :-)
	 *
	 * @return string XHTML string containing the whole form
	 */
	public function get() {
		if (is_array($this->typoscript['excludeFieldsFromMail.'])) {
			$excludeFields = $this->typoscript['excludeFieldsFromMail.'];
		} else {
			$excludeFields = array();
		}

		$elements = $this->model->getElements();
		$newElements = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('\\TYPO3\\CMS\\Form\\Domain\\Model\\Form');

		foreach ($elements as $element) {
			$_elements = $element->getElements();

			foreach ($_elements as $_element) {
				if (!in_array($_element->getName(), $excludeFields)) {
					$newElements->addElement($_element);
				}
			}
		}
		$this->setData($newElements);

		$node = $this->render('element', FALSE);
		$content = chr(10) . html_entity_decode($node->saveXML($node->firstChild), ENT_QUOTES, 'UTF-8') . chr(10);
		return $content;
	}

}
