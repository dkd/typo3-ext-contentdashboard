<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Dkd.Contentdashboard',
		'web',
		'tx_contentdashboard_dashboard',
		'after:template',
		array(
			'Dashboard' => 'index,detail,delete,preserve',
			'Archive' => 'index,restore,deleteRestoreTask',
			'DashboardAjax' => 'lifeCycleData'
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/DashboardModuleIcon.png',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf',
			'navigationComponentId' => 'typo3-pagetree',
			'inheritNavigationComponentFromMainModule' => FALSE
		)
	);
}
