<?php

use app\components\Date;
use app\components\Str;
use app\models\Opers;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemIdentifierComments;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\widgets\IdentifierViewWidget;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>
<?php foreach ($value->check_identifier_comments as $check_identifier_comment): ?>
    <?php
    $class = "11border-white";
    if ($check_identifier_comment->status_execution == Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_IN_WORK) {
        $class = "border-warning";
    }
    if ($check_identifier_comment->status_execution == Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_COMPLETED) {
        $class = "border-success";
    }
    if ($check_identifier_comment->status_execution == Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_CANCELED) {
        $class = "border-danger";
    }
    $likes = count($check_identifier_comment->likes);
    ?>
    <div data-identifier-comment="<?= $check_identifier_comment->id ?>">
        <div class="card card-outline border <?= $class ?> card-small mt-2">


            <?php if (!empty($check_identifier_comment->task_id)): ?>
                <div class="card-body" style="background-color: #ededed">
                    <?= Html::a($check_identifier_comment->task->name ?? "-", ['/process/task/view', 'id' => $check_identifier_comment->task_id], ['target' => "_blank"]) ?>

                    <?php if (!empty($check_identifier_comment->identifier_id)): ?>
                        <?= IdentifierViewWidget::widget([
                            'identification' => Yii::$app->user->identity,
                            'task'           => $check_identifier_comment->task,
                            'identifier'     => $check_identifier_comment->identifier,
                            'is_only_view'   => true,
                        ]); ?>
                    <?php endif; ?>
                    <?php if (!empty($check_identifier_comment->step_id)): ?>
                        <div class="card">
                            <div class="card-header px-3 py-2">
                                <i class="fas fa-shoe-prints" style="color: #999999" title=""></i>
                                <?= $check_identifier_comment->step->version->template->name ?? "-" ?> v<?= $check_identifier_comment->step->version->version ?? "-" ?>
                                <span style="font-style: italic; color: #5d5d5d; font-size: small">(шаблон)</span>

                                <div>
                                    <a href="<?= Url::toRoute(['/process/step/view', 'id' => $check_identifier_comment->step_id]) ?>" target="_blank">
                                        <?= $check_identifier_comment->step->name ?? "-" ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="card-body" style="background-color: #ededed">
                    <?= $this->render('/identifiers/info/block_small', ['identifier' => $check_identifier_comment->identifier, 'can_edit' => true]) ?>
                </div>
            <?php endif; ?>


            <div class="card-footer">

                <div style="display: flex; gap: 10px; flex-wrap: wrap">
                    <?php if ($likes > 0): ?>
                        <div style="display: inline-block; background-color: rgb(255 255 255 / 69%); padding: 2px 5px; border-radius: 5px;">
                            <i class="fas fa-thumbs-up" style="color: #21d500"></i> <?= $likes ?>
                        </div>
                    <?php endif; ?>

                    <div style="font-size: small; color: #979797; margin-left: auto">
                        <?= $check_identifier_comment->date_add ?> <span style="color: #b4b4b4; font-size: x-small">(<?= (new Date($check_identifier_comment->date_add))->toRemainingText(1, true) ?>)</span>
                    </div>
                </div>

                <div>
                    <b><?= Opers::getFioOrFioDeletedHtml($check_identifier_comment) ?>:</b> <?= nl2br(Str::toLink($check_identifier_comment->comment)) ?>
                </div>
            </div>

            <div class="card-footer">

                <?php if ($is_editable && !$is_only_view && $can_edit && $check_identifier_comment->isAccessSetStatusExecution(Yii::$app->user->id)): ?>
                    <div class="form-group">
                        <label>Статус выполнения
                            <?php $role = $check_identifier_comment->getResponsibleRole(); ?>
                            <?php if (!empty($role)): ?>
                                <div style="color: #a7a7a7; font-size: small; font-weight: normal">
                                    (Ответственный: <?= Yii::$app->authManager->getRoleDescriptionByName($role) ?>)
                                </div>
                            <?php endif; ?>
                        </label>

                        <?= Select2::widget([
                            'id'            => "check_identifier_comment_execution_{$check_identifier_comment->id}",
                            'name'          => "check_identifier_comment_execution_{$check_identifier_comment->id}",
                            'value'         => $check_identifier_comment->status_execution,
                            'data'          => Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_NAMES,
                            'pluginOptions' => [
                                'placeholder' => "статус выполнения",
                            ],
                            'pluginEvents'  => [
                                "select2:select" => "function(item) {checkIdentifierCommentChangeStatusExecution(item.target, {$check_identifier_comment->id}, item.params.data.id, 1, {$task->id}, {$identifier->id}); }",
                            ]
                        ]); ?>
                    </div>
                <?php else: ?>
                    <div class="mb-2">
                        <b>Статус выполнения: </b> <?= $check_identifier_comment->getStatusExecutionName() ?>
                        <?php $role = $check_identifier_comment->getResponsibleRole(); ?>
                        <?php if (!empty($role)): ?>
                            <div style="color: #a7a7a7; font-size: small; font-weight: normal">
                                (Ответственный: <?= Yii::$app->authManager->getRoleDescriptionByName($role) ?>)
                            </div>
                            <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                                <div style="font-size: small">
                                    <b>Статус может менять ответственный за шаблон: </b> <?= Yii::$app->authManager->getRoleDescriptionByName($role) ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array($check_identifier_comment->status_execution, [Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_CANCELED, Req3TasksDataItemIdentifierComments::STATUS_EXECUTION_COMPLETED])): ?>
                    <?php if ($is_editable && !$is_only_view && $can_edit && $check_identifier_comment->isAccessSetStatusCheck(Yii::$app->user->id)): ?>
                        <div class="form-group">
                            <label>Статус проверки</label>
                            <?= Select2::widget([
                                'id'            => "check_identifier_comment_check_{$check_identifier_comment->id}",
                                'name'          => "check_identifier_comment_check_{$check_identifier_comment->id}",
                                'value'         => $check_identifier_comment->status_check,
                                'data'          => Req3TasksDataItemIdentifierComments::STATUS_CHECK_NAMES,
                                'pluginOptions' => [
                                    'placeholder' => "статус проверки",
                                ],
                                'pluginEvents'  => [
                                    "select2:select" => "function(item) {checkIdentifierCommentChangeStatusExecution(item.target, {$check_identifier_comment->id}, item.params.data.id, 0, {$task->id}, {$identifier->id}); }",
                                ]
                            ]); ?>
                        </div>
                    <?php else: ?>
                        <div class="mb-2">
                            <b>Статус проверки: </b> <?= $check_identifier_comment->getStatusCheckName() ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div>

        </div>
    </div>
<?php endforeach; ?>