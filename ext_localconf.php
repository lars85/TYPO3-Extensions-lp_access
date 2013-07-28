<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['addEnableColumns']['tx-lpaccess'] = 'LarsPeipmann\\LpAccess\\Hook\\AddEnableColumnsHook->process';