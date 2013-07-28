<?php
namespace LarsPeipmann\LpAccess\UserFunction;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Lars Peipmann <lp@lightwerk.com>, Lightwerk
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

class HoursSelectionUserFunction implements \TYPO3\CMS\Core\SingletonInterface {

	protected $modTSConfigs;

	/**
	 * Renders the cropping link and fancybox.
	 *
	 * @param array $PA
	 * @param $fObj
	 * @return string HTML output
	 */
	public function process($params, $parentObject) {
		$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('lp_access');
		$extensionRelativePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('lp_access');
		$modTSConfig = $this->getModTSConfig($params['row']['uid']);

		/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

		/** @var \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer */
		$pageRenderer = $objectManager->get('TYPO3\\CMS\\Core\\Page\\PageRenderer');

		$pageRenderer->addJsFile($extensionRelativePath . 'Resources/Public/JavaScript/lp_access.js');
		$pageRenderer->addCssFile($extensionRelativePath . 'Resources/Public/Stylesheets/lp_access.css');

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = $objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename($extensionPath . 'Resources/Private/Templates/HoursSelectionUserFunction/Process.html');
		$view->setLayoutRootPath($extensionPath . 'Resources/Private/Layouts/');
		$view->setPartialRootPath($extensionPath . 'Resources/Private/Partials/');

		$days = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $modTSConfig['days'], TRUE);
		sort($days);
		$hours = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $modTSConfig['hours'], TRUE);
		sort($hours);

		$activeValues = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $params['itemFormElValue'], TRUE);

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

	protected function getModTSConfig($pageUid) {
		$modTSConfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getModTSconfig($pageUid, 'tx_lpaccess');
		return $modTSConfig['properties'];
	}
}

?>