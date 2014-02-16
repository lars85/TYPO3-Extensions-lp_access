<?php
namespace LarsPeipmann\LpAccess\UserFunction;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Lars Peipmann <Lars@Peipmann.de>
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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class HoursSelectionUserFunction implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Object Manager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Configuration Manager
	 *
	 * @var \LarsPeipmann\LpAccess\Service\ConfigurationService
	 * @inject
	 */
	protected $configurationService;

	/**
	 * Page Renderer
	 *
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @inject
	 */
	protected $pageRenderer;

	/**
	 * Renders the HTML output for the "Hours Access" field.
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Backend\Form\FormEngine $parentObject
	 * @return string Rendered HTML output for the field
	 */
	public function process($params, $parentObject) {
		$this->initializeObject();

		$pageUid = $params['row']['pid'];
		$view = $this->getView();
		$activeValues = GeneralUtility::intExplode(',', $params['itemFormElValue'], TRUE);
		$days = $this->configurationService->getDays($pageUid);
		$hours = $this->configurationService->getHours($pageUid);

		$rows = array();
		foreach ($days as $day) {
			foreach ($hours as $hour) {
				$value = $day . str_pad($hour, 2, 0, STR_PAD_LEFT);
				$rows[$day][$hour] = array(
					'active' => in_array($value, $activeValues),
					'value' => $value,
				);
			}
		}

		$view->assignMultiple(
			array(
				'params' => $params,
				'rows' => $rows,
				'hours' => $hours,
				'days' => $days,
			)
		);

		return $view->render();
	}

	/**
	 * Returns a fluid view.
	 *
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView $view
	 */
	protected function getView() {
		$extensionPath = ExtensionManagementUtility::extPath('lp_access');
		$extensionRelativePath = ExtensionManagementUtility::extRelPath('lp_access');

		$this->pageRenderer->addJsFile($extensionRelativePath . 'Resources/Public/JavaScript/LpAccess.js');
		$this->pageRenderer->addCssFile($extensionRelativePath . 'Resources/Public/Stylesheets/LpAccess.css');

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename($extensionPath . 'Resources/Private/Templates/HoursSelectionUserFunction/Process.html');
		$view->setLayoutRootPath($extensionPath . 'Resources/Private/Layouts/');
		$view->setPartialRootPath($extensionPath . 'Resources/Private/Partials/');

		return $view;
	}

	/**
	 * This function injects the needed objects.
	 * Automatic injects with PHPDoc (at)inject are not working so far.
	 *
	 * @return void
	 */
	protected function initializeObject() {
		if (!$this->objectManager) {
			$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		}
		if (!$this->configurationService) {
			$this->configurationService = $this->objectManager->get('LarsPeipmann\\LpAccess\\Service\\ConfigurationService');
		}
		if (!$this->pageRenderer) {
			$this->pageRenderer = $this->objectManager->get('TYPO3\\CMS\\Core\\Page\\PageRenderer');
		}
	}
}
