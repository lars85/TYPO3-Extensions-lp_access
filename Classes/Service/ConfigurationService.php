<?php
namespace LarsPeipmann\LpAccess\Service;

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

class ConfigurationService implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * modTSConfig per page
	 *
	 * @var array
	 */
	protected $modTSConfigPerPage;

	/**
	 * Returns the modTSConfig for a page.
	 *
	 * @param $pageUid
	 * @return array
	 */
	public function getModTSConfig($pageUid) {
		if (empty($this->modTSConfigPerPage[$pageUid])) {
			$modTSConfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getModTSconfig($pageUid, 'tx_lpaccess');
			$this->modTSConfigPerPage[$pageUid] = $modTSConfig['properties'];
		}
		return $this->modTSConfigPerPage[$pageUid];
	}

	/**
	 * Returns the "days" from modTSConfig as array.
	 *
	 * @param $pageUid integer Page uid
	 * @return array of integers
	 */
	public function getDays($pageUid) {
		$modTSConfig = $this->getModTSConfig($pageUid);
		$days = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $modTSConfig['days'], TRUE);
		return $days;
	}

	/**
	 * Returns the "hours" from modTSConfig as array.
	 *
	 * @param $pageUid integer Page uid
	 * @return array of integers
	 */
	public function getHours($pageUid) {
		$modTSConfig = $this->getModTSConfig($pageUid);
		$hours = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $modTSConfig['hours'], TRUE);
		return $hours;
	}
}
