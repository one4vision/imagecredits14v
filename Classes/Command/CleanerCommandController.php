<?php
namespace Extension14v\Imagecredits14v\Command;

use Extension14v\Imagecredits14v\Domain\Repository\CleanerRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class CleanerCommandController extends AbstractTask {
    public $clean_creator;
    public function execute(): bool
    {
        $cleanerRepository = GeneralUtility::makeInstance(CleanerRepository::class);
        $clean_creator = trim((string)$this->clean_creator);

        if($clean_creator !== '') {
            $cleanerRepository->cleanupCreator(GeneralUtility::trimExplode(',', $clean_creator, true));
        }

        return true;
    }
}