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

class AddEnableColumnsHook implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Adds where clause tx_lpaccess_hours to enableColumns.
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Frontend\Page\PageRepository $pageRepository
	 * @return string Additional where
	 */
	public function process(array $params, $pageRepository) {
		$query = '';
		if (!empty($params['ctrl']['enablecolumns']['tx_lpaccess_hours']) && empty($params['ignore_array']['tx_lpaccess_hours'])) {
			$table = $params['table'];
			$column = $params['ctrl']['enablecolumns']['tx_lpaccess_hours'];
			$now = $GLOBALS['ACCESS_TIME'];
			$day = date('N', $now);
			$hour = date('H', $now);
			$query = ' AND (' . $table . '.' . $column . ' = "" OR ' . $table . '.' . $column . ' LIKE "%' . $day . $hour . '%")';
		}
		return $query;
	}
}

?>