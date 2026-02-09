<?php
namespace Extension14v\Imagecredits14v\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

#[Autoconfigure(public: true)]
class AjaxController
{
    protected array $responseArray = [];

    protected array $settings = [];

    public function updateCopyrightAction(ServerRequestInterface $request): ResponseInterface {
        $query = $request->getParsedBody();
        $action = trim((string) $query['action']);
        $result = [];
        $result['action'] = $action;
        if($action === 'saveChanges') {
            $value = trim((string) $query['value']);
            $name = trim((string) $query['name']);
            $metaUid = (int) $query['metaUid'];
            $content = $this->saveMetaDataValue($metaUid, $name, $value);
            $result['done'] = $content;
            $result['metaUid'] = $metaUid;
            $result['name'] = $name;
            $result['value'] = $value;
            $this->responseArray['message'] = $result;
        }
        return new JsonResponse($this->responseArray);
    }

    private function saveMetaDataValue(int $metaUid, string $fieldName, $fieldValue): bool
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $table = 'sys_file_metadata';
        $queryBuilder = $connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->update($table)
            ->where($queryBuilder->expr()->eq('uid', $metaUid))->set($fieldName, $fieldValue)->executeStatement();
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $persistenceManager->persistAll();
        return true;
    }
}
