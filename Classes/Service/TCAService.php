<?php
namespace LarsPeipmann\LpAccess\Service;

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

class TCAService implements \TYPO3\CMS\Core\SingletonInterface {

	static public function addHoursFieldToTable($table, $palette = 'access', $insertionPosition = 'before:fe_group') {
		$tempColumns = array(
			'tx_lpaccess_hours' => array(
				'exclude' => 1,
				'label' => 'LLL:EXT:lp_access/Resources/Private/Language/locallang.xlf:column.label',
				'config' => array(
					'type' => 'user',
					'userFunc' => 'LarsPeipmann\LpAccess\UserFunction\HoursSelectionUserFunction->process',
				)
			),
		);

		$GLOBALS['TCA'][$table]['ctrl']['enablecolumns']['tx_lpaccess_hours'] = 'tx_lpaccess_hours';

		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $tempColumns);
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette($table, $palette, '--linebreak--, tx_lpaccess_hours, --linebreak--', $insertionPosition);
	}

}

?>