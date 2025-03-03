<?php
namespace Extension14v\Imagecredits14v\Controller;

/***
 *
 * This file is part of the "Contentlinkreplace14v" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Oliver Busch <ob@14v.de>, one4vision GmbH
 *
 ***/

use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Extension14v\Imagecredits14v\Domain\Repository\AjaxRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use Psr\Http\Message\ResponseInterface;

/**
 * BackendController
 */
class AjaxController extends ActionController
{

    protected ?AjaxRepository $ajaxRepository = null;

    protected array $responseArray = [];

    /**
     * @var array
     */
    protected $settings = [];

    public function injectAjaxRepository(AjaxRepository $ajaxRepository): void
    {
        $this->ajaxRepository = $ajaxRepository;
    }

    public function __invoke(): ResponseInterface {
        $this->ajaxRepository = GeneralUtility::makeInstance(AjaxRepository::class);
        $action = trim((string) $_REQUEST['action']);
        $result = [];
        $result['action'] = $action;
        if($action === 'saveChanges') {
            $value = trim((string) $_REQUEST['value']);
            $name = trim((string) $_REQUEST['name']);
            $metaUid = (int) $_REQUEST['metaUid'];
            $content = $this->ajaxRepository->saveMetaDataValue($metaUid, $name, $value);
            $result['done'] = $content;
            $result['metaUid'] = $metaUid;
            $result['name'] = $name;
            $result['value'] = $value;
            $this->responseArray['message'] = $result;
        }
        return new JsonResponse($this->responseArray);
    }
}
