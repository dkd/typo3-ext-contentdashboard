<?php
namespace Dkd\Contentdashboard\Controller;

use Dkd\CmisService\Factory\CmisObjectFactory;
use Dkd\Contentdashboard\Registry;
use Dkd\PhpCmis\SessionInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class AbstractController
 */
abstract class AbstractController extends ActionController
{
	const MIDDLEWARE_URL = 'http://pofmiddleware:8080/server/rest-api/';
	const CMIS_SERVER_ID = 'dkd';
	const REGISTRY_KEY_RESTORE_TASK = 'tx_contentdashboard_task';

	/**
	 * @var CmisObjectFactory
	 */
	protected $cmisObjectFactory;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->cmisObjectFactory = new CmisObjectFactory();
	}

	/**
	 * @return SessionInterface
	 */
	protected function getCmisSession() {
		return $this->cmisObjectFactory->getSession();
	}

	/**
	 * @return Registry
	 */
	protected function getRegistry() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Dkd\\Contentdashboard\\Registry');
	}
}
