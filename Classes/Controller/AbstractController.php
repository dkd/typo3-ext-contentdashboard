<?php
namespace Dkd\Contentdashboard\Controller;

use Dkd\CmisService\Factory\CmisObjectFactory;
use Dkd\PhpCmis\SessionInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class AbstractController
 */
abstract class AbstractController extends ActionController
{
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
}
