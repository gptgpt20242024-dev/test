<?php

use app\components\Date;
use app\components\Str;
use app\modules\process\components\HelperOper;
use app\modules\process\dto\data\TaskDataItemDto;
use app\modules\process\dto\RuleDataDto;
use app\modules\process\dto\WorkConfirmationDto;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3TaskIdentifierClone;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_data\Req3TasksDataRemarks;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\models\work_raters\Req3WorkRaterOperQualification;
use app\modules\process\models\work_raters\Req3WorkRaters;
use app\modules\process\widgets\WorkRaterBackgroundLearnWidget;
use app\modules\process\widgets\WorkRaterWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */

/* @var $task Req3Tasks */
/* @var $ruleData ?RuleDataDto */
/* @var $identifier Req3Identifiers */
/* @var $values Req3TasksDataItems[] */
/* @var $remarks Req3TasksDataRemarks[] */
/* @var $clones Req3TaskIdentifierClone[] */
/* @var $comment_count integer */
/* @var $likesCount integer */
/* @var $menu array */
/* @var $itemInfo ?TaskDataItemDto */
/* @var $setting_limit_days int */
/* @var $setting_ignore_items string[] */

/* @var $form ActiveForm */

/* @var $is_deviation boolean */
/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */
/* @var $is_custom_editable boolean */
/* @var $is_has_history boolean */
/* @var $is_edited_in_previous_step boolean */
/* @var $is_edited_in_current_step boolean */
/* @var $isLikedByCurrentOper boolean */
/* @var $is_use_in_step boolean */

/* @var $work_rated Req3WorkRaters */
/* @var $work_rated_qualification Req3WorkRaterOperQualification */
/* @var $work_rated_confirmation Req3WorkRaterOperConfirmation */

/* @var $can_edit boolean */
/* @var $can_add_remark boolean */
/* @var $canLike boolean */

/* @var $exists_rejected null|false|WorkConfirmationDto */
/* @var $non_block boolean */

/* @var $task_check_identifier integer */
/* @var $task_check_work integer */


$class_card = "";
if ($is_deviation) $class_card = "card-info";
elseif ($is_required) $class_card = "card-danger";
elseif ($is_editable) $class_card = "card-warning";
?>

<div id="i_<?= $identifier->id ?>" data-identifier="<?= $identifier->id ?>" class="mb-3">

    <?php if ($work_rated && empty($work_rated->regulations_manual_id)): //для меню?>
        <div style="display: none" id="work_rater_<?= $work_rated->id ?>_<?= Req3WorkRaterOperConfirmation::TYPE_REGULATIONS ?>_text">
            <?= Str::toLink(nl2br($work_rated->regulations_text)) ?>
        </div>
    <?php endif; ?>

    <?php if ($work_rated && empty($work_rated->standard_manual_id)): //для меню?>
        <div style="display: none" id="work_rater_<?= $work_rated->id ?>_<?= Req3WorkRaterOperConfirmation::TYPE_STANDARD ?>_text">
            <?= Str::toLink(nl2br($work_rated->standard_text)) ?>
        </div>
    <?php endif; ?>

    <div class="card card-outline <?= $class_card ?> m-0">

        <i class="fas fa-pen-fancy btn-correct left" onclick="showDialogCorrect(event, <?= $identifier->id ?>, <?= Req3Corrections::LINK_TYPE_IDENTIFIER ?>)"></i>

        <?php if ($itemInfo->lastTimeAdd ?? null): ?>
            <div style="font-size: x-small; color: #6e6e6e; text-align: right; margin: 0 0.5rem -0.5rem 0.5rem;" title="Когда и кто ввел эти данные">
                <?= HelperOper::getFioById($itemInfo->lastOperId) ?>
                <?php $date = new Date($itemInfo->lastTimeAdd) ?>
                <span style="color: #979797" data-toggle="tooltip" title="<?= $date->format(Date::FORMAT_DATE_TIME) ?>">(<?= $date->toRemainingText(1, true) ?>)</span>
            </div>
        <?php endif; ?>

        <div class="card-header px-3 py-2">

            <?php if (count($menu) > 0): ?>
                <div class="dropleft float-right">
                    <button class="btn btn-xs btn-link ml-2" type="button" id="menu_i_<?= $identifier->id ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                    <div class="dropdown-menu" aria-labelledby="menu_i_<?= $identifier->id ?>">
                        <?php foreach ($menu as $item): ?>
                            <a href="javascript:void (0);" class="dropdown-item  <?= !isset($item['click']) ? "disabled" : "" ?>" onclick="<?= $item['click'] ?? "" ?>">
                                <i class="<?= $item['icon'] ?> mr-1" style="color: <?= $item['color'] ?>; width: 16px; height: 16px"></i> <?= $item['title'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$is_only_view && $is_editable && !$is_custom_editable && $can_edit): ?>
                <button class="btn btn-xs btn-light float-right ml-2" onclick="editIdentifier(this, <?= $task->id ?>, <?= $identifier->id ?>)"><i class="fas fa-pencil-alt"></i></button>
            <?php endif; ?>

            <?php if ($is_has_history): ?>
                <button class="btn btn-xs btn-light float-right ml-2" onclick="showDialogTaskDataHistory(<?= $task->id ?>, <?= $identifier->id ?>)"><i class="fas fa-history" title="История изменений"></i></button>
            <?php endif; ?>

            <?php if ($is_edited_in_previous_step): ?>
                <i class="fas fa-circle float-right ml-2" style="font-size: 0.75rem; line-height: 2; color: #98caff" title="Данные введены на предыдущем шаге" data-toggle="tooltip"></i>
            <?php endif; ?>

            <?php if ($is_edited_in_current_step): ?>
                <i class="fas fa-circle float-right ml-2" style="font-size: 0.75rem; line-height: 2; color: #d6d6d6" title="Данные введены на текущем шаге" data-toggle="tooltip"></i>
            <?php endif; ?>

            <?php if ($task): ?>
                <?php if ($comment_count > 0): ?>
                    <button class="btn btn-xs btn-success float-right ml-2" onclick="showDialogTaskDataComments(<?= $task->id ?>, <?= $identifier->id ?>)"><i class="far fa-comments" title="Комментарии"></i> <?= $comment_count ?></button>
                <?php else: ?>
                    <button class="btn btn-xs btn-light float-right ml-2" onclick="showDialogTaskDataComments(<?= $task->id ?>, <?= $identifier->id ?>)"><i class="far fa-comments" title="Комментарии"></i></button>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($task && $itemInfo && $itemInfo->getOperId() !== null && $itemInfo->getOperId() != -1 && $identifier->work_rated_id !== null): ?>
                <button data-likes-count="<?= $likesCount ?>" class="btn btn-xs float-right ml-2 <?= ($canLike) ? "" : "disabled" ?>" <?= ($canLike) ? "" : "disabled" ?> onclick="toggleLike(this, <?= $task->id ?>, <?= $identifier->id ?>, <?= $itemInfo->values[array_key_first($itemInfo->values)]->getId() ?>)">
                    <i class="<?= $isLikedByCurrentOper ? "fas" : "far" ?> fa-heart" style="color: #ff2424;"></i>
                    <span>
                        <?php if ($likesCount > 0): ?>
                            +<?= $likesCount ?>
                        <?php endif; ?>
                    </span>
                </button>
            <?php endif; ?>

            <?= $this->render('/identifiers/_icon_type', ['type' => $identifier->type, 'id' => $identifier->id]) ?>

            <?php if ($is_required): ?>
                <span style="color: red" title="Обязательные данные">*</span>
            <?php endif; ?>

            <?= $identifier->name ?>

            <span style="font-style: italic; color: #5d5d5d; font-size: small">(<?= $identifier->getTypeName() ?>)</span>


        </div>


        <div style="position: relative;">
            <?= $this->render('/task-data/view/data/content', [
                'task'       => $task,
                'ruleData' => $ruleData,
                'identifier' => $identifier,
                'values'     => $values,

                'is_editable'        => $is_editable,
                'is_required'        => $is_required,
                'is_only_view'       => $is_only_view,
                'is_custom_editable' => $is_custom_editable,

                'can_edit' => $can_edit,
            ]) ?>

            <?php if ($is_custom_editable && !$is_only_view && $is_editable && $can_edit && $work_rated): ?>
                <?= WorkRaterBackgroundLearnWidget::widget([
                    'work_rated'               => $work_rated,
                    'work_rated_qualification' => $work_rated_qualification,
                    'work_rated_confirmation'  => $work_rated_confirmation,
                ]); ?>
            <?php endif; ?>
        </div>

        <?php if ($identifier->type == Req3Identifiers::TYPE_CALL_STATUS && ($task->step->is_calls ?? false)): ?>
            <?php $oper_calls = $task->getExecutorCalls() ?>
            <?php if (count($oper_calls) > 0): ?>
                <div class="card-footer px-3 py-2">
                    <span style="font-size: small; font-weight: bold; color: #6b6b6b">Обзвонщики:</span>
                    <?php $fio = ArrayHelper::getColumn($oper_calls, 'fio'); ?>
                    <?php foreach ($fio as $f): ?>
                        <span class="badge badge-secondary"><?= $f ?></span>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card-footer px-2 py-2">
                    <div class="alert alert-default-danger m-0">
                        Обзвонщиков нет
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($is_custom_editable && !$is_only_view && $is_editable && $can_edit && $work_rated): ?>
            <div class="card-footer px-3 py-2">
                <?= WorkRaterWidget::widget([
                    'work_rated'               => $work_rated,
                    'work_rated_qualification' => $work_rated_qualification,
                    'work_rated_confirmation'  => $work_rated_confirmation,
                ]); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty(Str::trim($identifier->description))): ?>
            <div class="card-footer px-3 py-2" style="position: relative">
                <div style="font-style: italic; color: #5d5d5d; font-size: small; white-space:pre-wrap; "><?= (Str::toLink($identifier->description)) ?></div>
                <i class="fas fa-pen-fancy btn-correct" onclick="showDialogCorrect(event, <?= $identifier->id ?>, <?= Req3Corrections::LINK_TYPE_IDENTIFIER_DESCRIPTION ?>)"></i>
            </div>
        <?php endif; ?>

        <?php if ($setting_limit_days): ?>
            <div class="card-footer px-3 py-2">
                <div style="font-style: italic; color: #5d5d5d; font-size: small">
                    Стоит ограничение даты, можно выбирать до <b><?= (new Date())->addDays($setting_limit_days)->format(Date::FORMAT_DATE_TIME) ?></b> (<?= Date::minutesToText($setting_limit_days * 24 * 60) ?>)
                </div>
            </div>
        <?php endif; ?>

        <?php if ($is_use_in_step): ?>
            <div class="card-footer px-3 py-2">
                <div style="color: #5d5d5d; font-size: small">
                    <span style="color: red">*</span> <span style="font-weight: bold">Используется в настройках шага</span> (исполнители/ответственные/действующие)
                    <?php if (count($setting_ignore_items)): ?>
                        <div style="font-style: italic">
                            Игнорируются исполнители выбравшие <?= Str::getNumberText(count($setting_ignore_items), "пункт", "пункты", "пункты") ?>: <?= implode(", ", $setting_ignore_items) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="js_container_remarks">
            <?php if (count($remarks) > 0): ?>
                <?= $this->render('/task-data/view/remark/remarks', [
                    'task'       => $task,
                    'identifier' => $identifier,
                    'remarks'    => $remarks
                ]) ?>
            <?php endif; ?>
        </div>

        <?php if ($task_check_identifier || $task_check_work): ?>
            <div class="card-footer pb-2 px-2 pt-0">
                <div class="alert alert-default-info px-2 py-1 mb-0 mt-2">
                    <i class="fas fa-biohazard"></i> Внимание !!! Запущена
                    <?php if ($task_check_identifier): ?>
                        <?= Html::a("задача", ['/process/task/view', 'id' => $task_check_identifier], ['target' => '_blank', 'style' => 'color: #3c53c5;']) ?> по добавлению работы к данному идентификатору.
                    <?php endif; ?>
                    <?php if ($task_check_work): ?>
                        <?= Html::a("задача", ['/process/task/view', 'id' => $task_check_work], ['target' => '_blank', 'style' => 'color: #3c53c5;']) ?> на изменение работы данного идентификатора.
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-info btn-xs" data-spoiler data-container=".alert" data-content="[data-spoiler-content]" onclick="loadCheckComments(this, <?= $task->id ?>, <?= $identifier->id ?>)" data-load="0">
                        <i class="far fa-plus-square mr-1" data-close="1"></i>
                        <i class="far fa-minus-square mr-1" style="display: none" data-open="1"></i>
                        Комментарии
                    </button>

                    <div data-spoiler-content="1" style="display: none">

                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($exists_rejected): ?>
            <div class="card-footer pb-2 px-2 pt-0">
                <div class="alert alert-default-danger px-2 py-1 mb-0 mt-2">
                    <?php if ($non_block): ?>
                        <i class="fas fa-user-graduate"></i> Проверка вашей работы отклонена. <?= Html::a("Подробнее", ['/process/task/view', 'id' => $exists_rejected->checkTaskId], ['target' => '_blank', 'style' => 'color: #3c53c5;']) ?>.
                    <?php else: ?>
                        <i class="fas fa-user-graduate"></i> Проверка вашей работы отклонена, до решения проблем с вашим обучением вам запрещено редактировать идентификаторы этой работы. <?= Html::a("Подробнее", ['/process/task/view', 'id' => $exists_rejected->checkTaskId], ['target' => '_blank', 'style' => 'color: #3c53c5;']) ?>.
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <?php if (count($clones) > 0): ?>
        <div class="mx-3 px-2 py-1" style="font-size: small; background-color: white; border-radius: 0 0 5px 5px;">
            <div style="color: #6d6d6d">По этому идентификатору были проверки на дубликаты:</div>
            <?php foreach ($clones as $clone): ?>
                <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">
                    - <a href="<?= Url::toRoute(['/process/task/view', 'id' => $clone->clone_task_id]) ?>" target="_blank"><?= $clone->clone_task->name ?? "task_{$clone->clone_task_id}" ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <script>
        $(function () {
            $('[data-identifier="<?= $identifier->id ?>"] [data-toggle="tooltip"]').bootstraptooltip();
        });
    </script>
</div>