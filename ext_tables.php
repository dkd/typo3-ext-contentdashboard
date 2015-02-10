<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Dkd.Contentdashboard',
		'file',
		'tx_contentdashboard_dashboard',
		'after:file',
		array(
			'Dashboard' => 'index,detail',
			'Archive' => 'index'
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/DashboardModuleIcon.png',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf',
			'navigationComponentId' => FALSE,
			'inheritNavigationComponentFromMainModule' => FALSE
		)
	);
}
