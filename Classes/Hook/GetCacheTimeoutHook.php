<?php
namespace LarsPeipmann\LpAccess\Hook;

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
use \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class GetCacheTimeoutHook implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Calculates cache timeout in seconds for enable column tx_lpaccess_hours.
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $pageRepository
	 * @return integer Cache timeout in seconds
	 */
	public function process(array $params, TypoScriptFrontendController $pageRepository) {
		$cacheTimeout = $params['cacheTimeout'];
		$tablesToConsider = $this->getCurrentPageCacheConfiguration($pageRepository);
		$now = $GLOBALS['ACCESS_TIME'];

		foreach ($tablesToConsider as $tableDef) {
			$tempCacheTimeout = $this->getLowerCacheTimeoutForRecord($tableDef, $now, $pageRepository, $cacheTimeout);
			if ($tempCacheTimeout) {
				$cacheTimeout = $tempCacheTimeout;
			}
		}

		return $cacheTimeout;
	}

	/**
	 * Find cate timeout in seconds for one table and page for the enable column tx_lpaccess_hours.
	 *
	 * @param string $tableDef Table definition (format tablename:pid)
	 * @param integer $now "Now" time value
	 * @return boolean|integer Cache timeout in seconds, FALSE if timeout is not lower then the parameter $cacheTimeout
	 */
	protected function getLowerCacheTimeoutForRecord($tableDef, $now, TypoScriptFrontendController $pageRepository, $cacheTimeout) {
		list($tableName, $pageUid) = GeneralUtility::trimExplode(':', $tableDef);
		$showHidden = $tableName === 'pages' ? $pageRepository->showHiddenPage : $pageRepository->showHiddenRecords;
		$enableFields = $pageRepository->sys_page->enableFields($tableName, $showHidden, array('tx_lpaccess_hours' => TRUE));

		if (empty($GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns']['tx_lpaccess_hours'])) {
			return FALSE;
		}

		$hoursField = $GLOBALS['TCA'][$tableName]['ctrl']['enablecolumns']['tx_lpaccess_hours'];
		$day = (int) date('N', $now);
		$hour = (int) date('G', $now);
		$cacheTimeoutHours = floor($cacheTimeout/60/60) + 1;

		$values = array();
		for ($i = 0; $i <= $cacheTimeoutHours; $i++) {
			$tempDay = ($day + floor(($hour + $i) / 24)) % 7;
			$tempHour = ($hour + $i) % 24;
			$values[] = (int) ($tempDay . str_pad($tempHour, 2, 0, STR_PAD_LEFT));
		}
		if (empty($values)) {
			return FALSE;
		}

		$where = $hoursField . ' REGEXP "' . implode('|', $values) . '" AND pid = ' . $pageUid . $enableFields;
		$res = $this->getDatabaseConnection()->exec_SELECTquery($hoursField, $tableName, $where);

		$number = count($values);
		while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
			$fieldValues = GeneralUtility::intExplode(',', $row[$hoursField], TRUE);
			$number = $this->getFirstChange($values, $fieldValues, $number);
		}
		if ($number == count($values)) {
			return FALSE;
		}

		$newCacheTimeoutDate = $this->getNextDateFromValue($values[$number], $now);
		$newCacheTimeout = $newCacheTimeoutDate->getTimestamp() - $now;
		if ($newCacheTimeout >= $cacheTimeout) {
			return FALSE;
		}

		return $newCacheTimeout;
	}

	/**
	 * Returns the next date.
	 *
	 * @param $value string Day hour combo
	 * @param $now integer Timestamp (seconds)
	 * @return \DateTime
	 */
	protected function getNextDateFromValue($value, $now) {
		$valueDay = (int) floor($value / 100);
		$valueHour = $value % 100;

		$date = new \DateTime();
		$date->setTimestamp($now);

		$currentDay = (int) $date->format('N');

		if ($valueDay > $currentDay) {
			$date->add(new \DateInterval('P' . ($valueDay - $currentDay) . 'D'));
		} elseif ($valueDay < $currentDay) {
			$date->sub(new \DateInterval('P' . (7 - $currentDay + $valueDay) . 'D'));
		}

		$date->setTime($valueHour, 0);

		return $date;
	}

	/**
	 * Returns the first change in $fieldValues.
	 *
	 * @param $values array of day hour values (continuous)
	 * @param $fieldValues array of day hour values from database
	 * @param $maxNumber integer Maximum number
	 * @return integer Number of the first change in $fieldValues
	 */
	protected function getFirstChange($values, $fieldValues, $maxNumber) {
		$state = in_array($values[0], $fieldValues);
		foreach ($values as $number => $value) {
			if ($number >= $maxNumber) {
				return $maxNumber;
			}
			if (in_array($value, $fieldValues)) {
				if ($state == FALSE) {
					return $number;
				}
			} else {
				if ($state == TRUE) {
					return $number;
				}
			}
		}
		return $maxNumber;
	}

	/**
	 * Lars Peipmann: Function $pageRepository->getCurrentPageCacheConfiguration is protected..
	 * Because of that I had to copy the code into my own class.
	 *
	 * Obtains a list of table/pid pairs to consider for page caching.
	 *
	 * TS configuration looks like this:
	 *
	 * The cache lifetime of all pages takes starttime and endtime of news records of page 14 into account:
	 * config.cache.all = tt_news:14
	 *
	 * The cache lifetime of page 42 takes starttime and endtime of news records of page 15 and addresses of page 16 into account:
	 * config.cache.42 = tt_news:15,tt_address:16
	 *
	 * @param \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController $pageRepository
	 * @return array Array of 'tablename:pid' pairs. There is at least a current page id in the array
	 * @see tslib_fe::calculatePageCacheTimeout()
	 */
	protected function getCurrentPageCacheConfiguration(TypoScriptFrontendController $pageRepository) {
		$result = array('tt_content:' . $pageRepository->id);
		if (isset($pageRepository->config['config']['cache.'][$pageRepository->id])) {
			$result = array_merge(
				$result,
				GeneralUtility::trimExplode(',', $pageRepository->config['config']['cache.'][$pageRepository->id])
			);
		}
		if (isset($pageRepository->config['config']['cache.']['all'])) {
			$result = array_merge(
				$result,
				GeneralUtility::trimExplode(',', $pageRepository->config['config']['cache.']['all'])
			);
		}
		return array_unique($result);
	}

	/**
	 * Returns the database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}

