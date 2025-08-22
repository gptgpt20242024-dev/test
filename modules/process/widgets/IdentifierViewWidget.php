<?php

namespace app\modules\process\widgets;

use app\models\Opers;
use app\modules\lists\models\ListsItems;
use app\modules\process\assets\ProcessWorkRaterAsset;
use app\modules\process\dto\data\TaskDataDto;
use app\modules\process\dto\data\TaskDataItemDto;
use app\modules\process\dto\data\TaskDataItemValueDto;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\dto\TaskDataLikesDto;
use app\modules\process\dto\WorkConfirmationsDto;
use app\modules\process\factories\DataValueDtoFactory;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3TaskIdentifierClone;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataComments;
use app\modules\process\models\task_data\Req3TasksDataItemIdentifierComments;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_reports\Req3TaskReportHideIdentifier;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\models\work_raters\Req3WorkRaterOperQualification;
use app\modules\process\services\ProcessTaskService;
use app\modules\process\services\ProcessWorkRaterService;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * @property Opers|integer   $identification
 * @property Req3Tasks       $task
 * @property Req3Identifiers $identifier
 */
class IdentifierViewWidget extends Widget
{
    public                       $identification;
    public                       $task;
    public                       $identifier;
    public                       $is_only_view                    = false;
    public                       $forced_show_remarks_executor_id = false;
    public                       $form                            = null;
    public                       $forced_data                     = null;
    public ?RuleDataDto          $ruleData                        = null;
    public ?TaskDataDto          $taskData                        = null;
    public ?TaskDataLikesDto     $likesData                       = null;
    public ?WorkConfirmationsDto $workConfirmations               = null;

    public function init()
    {
        if (!$this->taskData && $this->task) {
            $taskService = Yii::$container->get(ProcessTaskService::class);
            $this->taskData = $taskService->getData($this->task);
        }

        parent::init();
    }

    public function run()
    {
        if (!$this->identifier) {
            return $this->render('identifier-deleted');
        }

        $oper = Opers::getOperByData($this->identification);

        $can_edit = !$this->is_only_view && $this->task && $oper && $this->task->isAccessAction($oper);
        $canLike = !$this->is_only_view && (($this->task && $oper && $this->task->isAccessView($oper)) || ($oper && Yii::$app->authManager->checkAccess($oper->oper_id, "business.like")));

        $setting = $this->identifier->getSettingArray();
        $setting_limit_days = null;
        $setting_ignore_items = [];
        if (!empty($setting['days'])) {
            $setting_limit_days = $setting['days'];
        }

        $is_use_in_step = $this->task && $this->identifier->isUseInRoles($this->task);
        if ($is_use_in_step && $this->identifier->type == Req3Identifiers::TYPE_OPER_ROLE) {
            $setting_ignore_items = $setting[Req3Identifiers::SETTING_OPER_LIST_ITEMS_IGNORE] ?? [];
            $setting_ignore_items = count($setting_ignore_items) > 0 ? ListsItems::find()->id($setting_ignore_items)->select('value')->column() : [];
        }

        $is_multi = $this->identifier->is_multi;
        $is_required = $this->task && $this->identifier->isRequired($this->task, $this->ruleData);
        $is_editable = $is_required || ($this->task && $this->identifier->isEditable($this->task, $this->ruleData));
        $is_deviation = $this->task && $this->identifier->isDeviationBlock($this->task, $this->ruleData, $this->taskData);

        $is_custom_editable = $this->identifier->isCustomEdit();

        $non_block = $this->identifier->getSettingByKey(Req3Identifiers::SETTING_NON_BLOCK, 0) == 1;

        $exists_rejected = false;
        if (!empty($this->identifier->work_rated_id)) {
            if ($can_edit && $is_editable && !$this->is_only_view && $oper) {
                if (!$this->workConfirmations) {
                    $workRaterService = Yii::$container->get(ProcessWorkRaterService::class);
                    $this->workConfirmations = $workRaterService->getAll([$this->identifier->work_rated_id], $oper->oper_id, Req3WorkRaterOperConfirmation::STATUS_REJECTED, $this->task->id ?? null, true);
                }
                $exists_rejected = $this->workConfirmations->get($this->identifier->work_rated_id, Req3WorkRaterOperConfirmation::STATUS_REJECTED, $oper->oper_id, $this->task->id ?? -1, false);
                if ($exists_rejected && !$non_block) {
                    $can_edit = false;
                }
            }
        }

        $is_edited_in_previous_step = false;//был отредактирован на предыдущем шагу
        $is_edited_in_current_step = false;//был отредактирован на этом шагу

        $values = [];
        $itemInfo = null;
        if (is_array($this->forced_data)) {
            $itemInfo = new TaskDataItemDto($this->identifier->id, $this->identifier->type, $is_multi);
            $tempValues = array_filter($this->forced_data, fn(Req3TasksDataItems $it) => $it->type == $this->identifier->type);
            foreach ($tempValues as $value) {
                $itemInfo->add(DataValueDtoFactory::createFromData($value));
            }
        } else {
            if ($this->taskData)
                $itemInfo = $this->taskData->getItem($this->identifier->id);
        }

        $canLike = $canLike && $oper && ($oper->oper_id != ($itemInfo ? $itemInfo->getOperId() : -1));

        if ($itemInfo) {
            switch ($itemInfo->getStepBackSet()) {
                case 1:
                    $is_edited_in_previous_step = true;
                    break;
                case 0:
                    $is_edited_in_current_step = true;
                    break;
            }
        }

        //пока временно
        $dataValues = $itemInfo ? $itemInfo->values : [];
        $values = array_map(fn(TaskDataItemValueDto $it) => $it->itemDB, $dataValues);

        //-------------------------------------------
        if ($this->identifier->type == Req3Identifiers::TYPE_SERVICE_BASKET) {
            $values = count($values) > 0 ? [$values[0]] : [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_SERVICE_BASKET, 'identifier_id' => $this->identifier->id])];
            if ($itemInfo) $itemInfo->lastTimeAdd = null;
        }
        if ($this->identifier->type == Req3Identifiers::TYPE_PROJECT_TREE) {
            $values = count($values) > 0 ? [$values[0]] : [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_PROJECT_TREE, 'identifier_id' => $this->identifier->id])];
            if ($itemInfo) $itemInfo->lastTimeAdd = null;
        }
        if ($this->identifier->type == Req3Identifiers::TYPE_CRASH) {
            $values = count($values) > 0 ? [$values[0]] : [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_CRASH, 'identifier_id' => $this->identifier->id])];
            if ($itemInfo) $itemInfo->lastTimeAdd = null;
        }
        $is_checklist = false;
        if ($this->identifier->type == Req3Identifiers::TYPE_LIST) {
            if ($this->identifier->getSettingByKey(Req3Identifiers::SETTING_IS_CHECKLIST, 0) == 1) {
                $values = count($values) > 0 ? $values : [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_LIST, 'identifier_id' => $this->identifier->id])];
                $is_checklist = true;
            }
        }
        if ($this->identifier->type == Req3Identifiers::TYPE_LIST_TREE) {
            $values = count($values) > 0 ? $values : [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_LIST_TREE, 'identifier_id' => $this->identifier->id])];
        }

        if ($this->identifier->type == Req3Identifiers::TYPE_REWARDS) {
            $values = count($values) > 0 ? $values : [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_REWARDS, 'identifier_id' => $this->identifier->id])];
        }
        if ($this->task && $this->identifier->type == Req3Identifiers::TYPE_CALL_STATUS && count($this->task->calls_statuses ?? []) > 0) {
            $values = [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_CALL_STATUS, 'identifier_id' => $this->identifier->id])];
        }
        if ($this->identifier->type == Req3Identifiers::TYPE_GHOST) {
            $values = [new Req3TasksDataItems(['type' => Req3Identifiers::TYPE_GHOST, 'identifier_id' => $this->identifier->id])];
        }

        //-------------------------------------------

        $is_has_history = false;
        if ($this->forced_data === null && $this->task) {
            foreach ($this->task->data_history as $value) {
                if ($value->identifier_id == $this->identifier->id && $value->type == $this->identifier->type) {
                    $is_has_history = true;
                    break;
                }
            }
        }

        $work_rated = null;
        $work_rated_qualification = null;
        $work_rated_confirmation = null;
        if (!empty($this->identifier->work_rated_id) && $this->identifier->work_rated) {
            $work_rated = $this->identifier->work_rated;
            if ($oper) {
                $work_rated_qualification = Req3WorkRaterOperQualification::findOrCreate($oper->oper_id, $work_rated->id);
                if ($work_rated_qualification->status == Req3WorkRaterOperQualification::STATUS_LEARNING && $this->task) {
                    $work_rated_confirmation = Req3WorkRaterOperConfirmation::findOrCreateByTask($oper->oper_id, $work_rated->id, $this->task->id, $this->identifier->id);
                }
            }
        }

        $menu = [];
        if ($this->identifier->type == Req3Identifiers::TYPE_SERVICE_BASKET && !$values[0]->isNewRecord) {
            $menu[] = [
                'title' => '#' . $values[0]->value_id, 'icon' => 'far fa-lightbulb', 'color' => '#6d6d6d',
            ];
            if ($this->task) {
                $menu[] = [
                    'title' => 'История изменения', 'icon' => 'fas fa-history', 'color' => '#6d6d6d',
                    'click' => "showDialogBasketHistory({$this->task->id}, {$values[0]->value_id})",
                ];
            }
        }

        $remarks = $this->task ? $this->task->getRemarksByIdentifierId($this->identifier->id) : [];
        if (count($remarks) > 0) {
            $menu[] = [
                'title' => 'История улучшений', 'icon' => 'fas fa-exclamation-circle', 'color' => '#6d6d6d',
                'click' => "showDialogRemarkHistory({$this->task->id}, {$this->identifier->id})",
            ];
        }

        if (in_array($this->identifier->type, [Req3Identifiers::TYPE_LIST])) {
            $menu[] = [
                'title' => 'Информация о списке', 'icon' => 'fas fa-clipboard-list', 'color' => '#6d6d6d',
                'click' => "showDialogInfo('Информация о списке', '/lists/groups/view', {id: {$this->identifier->type_info}}, undefined, '[data-list]', BootstrapDialog.TYPE_PRIMARY, BootstrapDialog.SIZE_LARGE)",
            ];
        }
        if (in_array($this->identifier->type, [Req3Identifiers::TYPE_LIST_TREE])) {
            $menu[] = [
                'title' => 'Информация о списке', 'icon' => 'fas fa-clipboard-list', 'color' => '#6d6d6d',
                'click' => "showDialogInfo('Информация о списке', '/lists/groups-tree/view', {id: {$this->identifier->type_info}}, undefined, '[data-list]', BootstrapDialog.TYPE_PRIMARY, BootstrapDialog.SIZE_LARGE)",
            ];
        }

        if ($is_editable && $can_edit && !$this->is_only_view && (!$is_custom_editable || $is_checklist) && $this->task) {
            if (count($values) > 0) {
                $menu[] = [
                    'title' => 'Очистить', 'icon' => 'fas fa-trash-alt', 'color' => '#d34141',
                    'click' => "clearIdentifier(this, 'i_{$this->identifier->id}', {$this->task->id}, {$this->identifier->id})",
                ];
            }
        }

        if ($work_rated && $this->task) {
            ProcessWorkRaterAsset::register($this->view);
            if (!empty($work_rated->standard_manual_id)) {
                $menu[] = [
                    'title' => 'Стандарт', 'icon' => 'fas fa-file-word', 'color' => '#6d6d6d',
                    'click' => "openLinkManual('" . Url::toRoute(['/manual/doc/view', 'id' => $work_rated->standard_manual_id]) . "')",
                ];
            } else {
                $menu[] = [
                    'title' => 'Стандарт', 'icon' => 'fas fa-quote-left', 'color' => '#6d6d6d',
                    'click' => "showDialogBlockText('Стандарт', '#work_rater_{$work_rated->id}_" . Req3WorkRaterOperConfirmation::TYPE_STANDARD . "_text')"
                ];
            }

            if (!empty($work_rated->regulations_manual_id)) {
                $menu[] = [
                    'title' => 'Регламент', 'icon' => 'fas fa-file-word', 'color' => '#6d6d6d',
                    'click' => "openLinkManual('" . Url::toRoute(['/manual/doc/view', 'id' => $work_rated->regulations_manual_id]) . "')",
                ];
            } else {
                $menu[] = [
                    'title' => 'Регламент', 'icon' => 'fas fa-quote-left', 'color' => '#6d6d6d',
                    'click' => "showDialogBlockText('Регламент', '#work_rater_{$work_rated->id}_" . Req3WorkRaterOperConfirmation::TYPE_REGULATIONS . "_text')"
                ];
            }
        }

        $can_add_remark = $this->task && $oper && $this->task->canAddRemark($oper, $this->identifier);
        if ($can_add_remark && !$this->is_only_view && $this->task) {
            $menu[] = [
                'title' => 'Предложить улучшение', 'icon' => 'fas fa-highlighter', 'color' => '#d34141',
                'click' => "addIdentifierRemark(this, 'i_{$this->identifier->id}', {$this->task->id}, {$this->identifier->id})",
            ];
        }

        if ($this->identifier->type == Req3Identifiers::TYPE_PROJECT_TREE) {
            $menu[] = [
                'title' => 'Открыть в окне', 'icon' => 'far fa-window-maximize', 'color' => '#2150a1',
                'click' => "showDialogProjectTree(this, {$this->task->id}, {$this->identifier->id})",
            ];
        }

        if (!$is_editable && $this->task && $oper) {
            $is_my_zone_responsibility = $this->task->isMyZoneResponsibility($oper);

            $oper_roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
            $is_responsibly_template = isset($oper_roles[$this->task->template->responsible_role ?? "-"]);
            $is_access = Yii::$app->authManager->checkAccess($oper->oper_id, "business.hide_identifier");
            if ($is_my_zone_responsibility || $is_responsibly_template || $is_access) {
                $is_visible_another = $this->identifier->isVisibleNotSimpleSettingStep($this->task);
                if (!$is_visible_another) {
                    $is_visible_step = $this->identifier->isVisibleSimpleSettingStep($this->task);
                    if ($is_visible_step) {
                        $menu[] = [
                            'title' => 'Скрыть на шаге', 'icon' => 'fas fa-eye-slash', 'color' => '#cfcfcf',
                            'click' => "showDialogChangeVisibleIdentifier(this, {$this->task->id}, {$this->task->step_id}, {$this->identifier->id}, " . Req3TaskReportHideIdentifier::ACTION_HIDE . ")",
                        ];
                    } else {
                        $menu[] = [
                            'title' => 'Включить отображение на шаге', 'icon' => 'fas fa-eye', 'color' => '#09cd3c',
                            'click' => "showDialogChangeVisibleIdentifier(this, {$this->task->id}, {$this->task->step_id}, {$this->identifier->id}, " . Req3TaskReportHideIdentifier::ACTION_SHOW . ")",
                        ];
                    }
                }
            }
        }

        $remarks = [];
        if ($is_editable || $this->forced_show_remarks_executor_id) {
            $last_oper_id = $itemInfo->lastOperId ?? null;
            $remarks = $this->task ? $this->task->getRemarksByIdentifierId($this->identifier->id, false, $this->forced_show_remarks_executor_id !== false ? $this->forced_show_remarks_executor_id : $last_oper_id) : [];
        }

        $task_check_identifier = null;
        $task_check_work = null;
        if (!$this->is_only_view) {
            $data_check = Req3TasksDataItemIdentifierComments::getIdentifiersAndWorksCheckWork();
            $task_check_identifier = $data_check['identifier_ids'][$this->identifier->id] ?? null;
            $task_check_work = $data_check['work_rated_ids'][$this->identifier->work_rated_id] ?? null;
        }

        $comment_count = $this->task ? Req3TasksDataComments::find()->andWhere(['task_id' => $this->task->id, 'identifier_id' => $this->identifier->id ?? -1])->count() : 0;

        if (!$this->likesData && $this->task && !$this->task->isNewRecord) {
            $processService = Yii::$container->get(ProcessTaskService::class);
            $this->likesData = $processService->getLikes($this->task->id, $this->identifier->id);
        }

        $likes = $this->likesData ? $this->likesData->getAll($this->identifier->id) : [];
        $likesCount = count($likes);
        $isLikedByCurrentOper = false;

        foreach ($likes as $like) {
            if ($oper && $like->getOperAddId() == $oper->oper_id) {
                $isLikedByCurrentOper = true;
                break;
            }
        }

        $clones = $this->task ? ArrayHelper::index(Req3TaskIdentifierClone::getClones($this->task->id, $this->identifier->id), 'clone_task_id') : [];

        return $this->render('identifier-view', [
            'task'                 => $this->task,
            'ruleData' => $this->ruleData,
            'identifier'           => $this->identifier,
            'values'               => $values,
            'remarks'              => $remarks,
            'clones'               => $clones,
            'comment_count'        => $comment_count,
            'likesCount'           => $likesCount,
            'menu'                 => $menu,
            'itemInfo'             => $itemInfo,
            'setting_limit_days'   => $setting_limit_days,
            'setting_ignore_items' => $setting_ignore_items,

            'form' => $this->form,

            'is_deviation'               => $is_deviation,
            'is_editable'                => $is_editable,
            'is_required'                => $is_required,
            'is_only_view'               => $this->is_only_view,
            'is_custom_editable'         => $is_custom_editable,
            'is_has_history'             => $is_has_history,
            'is_edited_in_previous_step' => $is_edited_in_previous_step,
            'is_edited_in_current_step'  => $is_edited_in_current_step,
            'isLikedByCurrentOper'       => $isLikedByCurrentOper,
            'is_use_in_step'             => $is_use_in_step,

            'work_rated'               => $work_rated,
            'work_rated_qualification' => $work_rated_qualification,
            'work_rated_confirmation'  => $work_rated_confirmation,

            'can_edit'       => $can_edit,
            'can_add_remark' => $can_add_remark,
            'canLike'        => $canLike,

            'non_block'       => $non_block,
            'exists_rejected' => $exists_rejected,

            'task_check_identifier' => $task_check_identifier,
            'task_check_work'       => $task_check_work,


        ]);
    }
}
