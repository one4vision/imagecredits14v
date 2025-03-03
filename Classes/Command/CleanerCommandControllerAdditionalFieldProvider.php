<?php
namespace Extension14v\Imagecredits14v\Command;

use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\CMS\Scheduler\Task\Enumeration\Action;

class CleanerCommandControllerAdditionalFieldProvider extends AbstractAdditionalFieldProvider {
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $schedulerModule): array
    {
        $currentSchedulerModuleAction = $schedulerModule->getCurrentAction();
        if (empty($taskInfo['clean_creator'])) {
            $taskInfo['clean_creator'] = $currentSchedulerModuleAction->equals(Action::EDIT) ? $task->clean_creator : '';
        }

        // Creator
        $Creator_fieldName = 'tx_scheduler[clean_creator]';
        $Creator_fieldId = 'clean_creator';
        $Creator_fieldValue = trim((string)$taskInfo['clean_creator']);
        $Creator_fieldHtml = '<input id="'.$Creator_fieldId.'" name="'.$Creator_fieldName.'" type="text" class="form-control" value="'.$Creator_fieldValue.'" />';

        $additionalFields[$Creator_fieldId] = array(
            'code' => $Creator_fieldHtml,
            'label' => 'Creator: Entferne folgende Werte (komma-sepatiert)',
            'cshKey' => '_MOD_tools_txschedulerM1',
            'cshLabel' => $Creator_fieldId
        );

        return $additionalFields;
    }

    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $schedulerModule): bool
    {
        return true;
    }

    public function saveAdditionalFields(array $submittedData, AbstractTask $task): void {
        $task->clean_creator = $submittedData['clean_creator'];
    }
}