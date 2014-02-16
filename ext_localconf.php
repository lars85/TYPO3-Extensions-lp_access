<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
	'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:lp_access/Configuration/PageTSConfig/setup.txt">'
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['addEnableColumns']['tx-lpaccess']
	= 'LarsPeipmann\\LpAccess\\Hook\\AddEnableColumnsHook->process';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['get_cache_timeout']['tx-lpaccess']
	= 'LarsPeipmann\\LpAccess\\Hook\\GetCacheTimeoutHook->process';