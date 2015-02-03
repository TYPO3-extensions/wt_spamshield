<?php
namespace TYPO3\CMS\Form\PostProcess;

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
 * WtspamshieldmailPostProcessor
 */
class WtspamshieldmailPostProcessor extends \TYPO3\CMS\Form\PostProcess\MailPostProcessor {

	/**
	 * Constructor
	 *
	 * @param \TYPO3\CMS\Form\Domain\Model\Form $form Form domain model
	 * @param array $typoScript Post processor TypoScript settings
	 */
	public function __construct(\TYPO3\CMS\Form\Domain\Model\Form $form, array $typoScript) {
		parent::__construct($form, $typoScript);
	}

	/**
	 * Add the HTML content
	 *
	 * Add a MimePart of the type text/html to the message.
	 *
	 * @return void
	 */
	public function setHtmlContent() {
		/** @var $view \TYPO3\CMS\Form\View\Mail\Html\HtmlView */
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TRITUM\\WtSpamshield\\Form\\View\\Mail\\Html\\HtmlView', $this->form, $this->typoScript);
		$htmlContent = $view->get();
		$this->mailMessage->setBody($htmlContent, 'text/html');
	}

	/**
	 * Add the plain content
	 *
	 * Add a MimePart of the type text/plain to the message.
	 *
	 * @return void
	 */
	public function setPlainContent() {
		/** @var $view \TYPO3\CMS\Form\View\Mail\Plain\PlainView */
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TRITUM\\WtSpamshield\\Form\\View\\Mail\\Plain\\PlainView', $this->form, 0, $this->typoScript);
		$plainContent = $view->render();
		$this->mailMessage->addPart($plainContent, 'text/plain');
	}
}
