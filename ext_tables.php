<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

//\LarsPeipmann\LpAccess\Service\TCAService::addHoursFieldToTable('pages');

\LarsPeipmann\LpAccess\Service\TCAService::addHoursFieldToTable('tt_content');