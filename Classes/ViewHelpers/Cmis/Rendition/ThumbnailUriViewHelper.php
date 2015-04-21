<?php
namespace Dkd\Contentdashboard\ViewHelpers\Cmis\Rendition;

use Dkd\CmisService\Factory\CmisObjectFactory;
use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ThumbnailViewHelper
 */
class ThumbnailUriViewHelper extends AbstractViewHelper {

	/**
	 * @param CmisObjectInterface $cmisObject
	 * @return string
	 */
	public function render(CmisObjectInterface $cmisObject) {
		$thumbnailPath = '';
		$thumbnailRendition = $this->getThumbnailRendition($cmisObject);
		if ($thumbnailRendition === NULL) {
			$cmisObject = $this->fetchObjectWithRendition($cmisObject->getId());
		}
		$thumbnailRendition = $this->getThumbnailRendition($cmisObject);

		if ($thumbnailRendition !== NULL) {
			$thumbnailPath = GeneralUtility::tempnam(
				'contentdashboard-rendition-' . $thumbnailRendition->getTitle(),
				'.' . str_replace('image/', '', $thumbnailRendition->getMimeType())
			);
			GeneralUtility::writeFileToTypo3tempDir(
				$thumbnailPath,
				$thumbnailRendition->getContentStream()
			);
			$thumbnailPath = str_replace(realpath(PATH_site), '', realpath($thumbnailPath));
		}

		return $thumbnailPath;
	}

	protected function getThumbnailRendition(CmisObjectInterface $object) {
		$thumbnailRendition = NULL;

		foreach ($object->getRenditions() as $rendition) {
			if ($rendition->getKind() === 'cmis:thumbnail') {
				$thumbnailRendition = $rendition;
				break;
			}
		}

		return $thumbnailRendition;
	}

	/**
	 * @param string $objectId
	 * @return CmisObjectInterface
	 */
	protected function fetchObjectWithRendition($objectId) {
		$objectFactory = new CmisObjectFactory();
		$session = $objectFactory->getSession();
		$context = $session->getDefaultContext();
		$context->setRenditionFilter(array('cmis:thumbnail'));

		return $session->getObject($session->createObjectId($objectId), $context);
	}
}
