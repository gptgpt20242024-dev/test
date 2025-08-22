<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\widgets\IdentifierViewWidget;
use app\modules\process\widgets\WorkRaterDocumentBtnWidget;
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

$class = "border-warning";
$qualification = null;
if ($value->work_rater_oper_confirmation) {
    if ($value->work_rater_oper_confirmation->status == Req3WorkRaterOperConfirmation::STATUS_REJECTED) {
        $class = "border-danger";
    }
    if ($value->work_rater_oper_confirmation->status == Req3WorkRaterOperConfirmation::STATUS_CREDITED) {
        $class = "border-success";
    }
    if ($value->work_rater_oper_confirmation->status == Req3WorkRaterOperConfirmation::STATUS_IMPOSSIBLE) {
        $class = "border-secondary";
    }
    $qualification = $value->work_rater_oper_confirmation->getQualification();
}
?>

<?php if ($value->work_rater_oper_confirmation): ?>

    <div style="color: #ababab; font-size: small">Работа:</div>

    <?php if ($value->work_rater_oper_confirmation->work_rater): ?>
        <?php $work_rated = $value->work_rater_oper_confirmation->work_rater; ?>

        <?php if ($work_rated->id && Yii::$app->user->canMulti(['business.work_rater.index'])): ?>
            <a href="<?= Url::to(['/process/work-rater/view', 'work_id' => $work_rated->id]) ?>" target="_blank">
                <?= $work_rated->name ?? "-" ?>
            </a>
        <?php else: ?>
            <?= $work_rated->name ?? "-" ?>
        <?php endif ?>


        <?= WorkRaterDocumentBtnWidget::widget([
            'oper_id'    => Yii::$app->user->id,
            'work_rated' => $work_rated,
            'type'       => Req3WorkRaterOperConfirmation::TYPE_STANDARD,
        ]); ?>

        <?= WorkRaterDocumentBtnWidget::widget([
            'oper_id'    => Yii::$app->user->id,
            'work_rated' => $work_rated,
            'type'       => Req3WorkRaterOperConfirmation::TYPE_REGULATIONS,
        ]); ?>

        <?= WorkRaterDocumentBtnWidget::widget([
            'oper_id'    => Yii::$app->user->id,
            'work_rated' => $work_rated,
            'type'       => Req3WorkRaterOperConfirmation::TYPE_CONTROL,
        ]); ?>
        <div style="color: #ababab; font-size: small">
            Количество проверок:
        </div>
        <?= $work_rated->confirmation_count ?>
    <?php endif; ?>

    <div style="color: #ababab; font-size: small">Исполнитель:</div>
    <?= $value->work_rater_oper_confirmation->oper->fio ?? "-" ?>

    <a class="btn btn-xs btn-light" href="<?= Url::toRoute(['/process/work-rater/oper', 'oper_id' => $value->work_rater_oper_confirmation->oper_id, 'work_rater_id' => $value->work_rater_oper_confirmation->work_rater_id]) ?>" target="_blank">
        Все задачи
    </a>

    <div style="color: #ababab; font-size: small">Дата ознакомления со стандартом:</div>
    <?= $value->work_rater_oper_confirmation->date_open_standard ?>

    <div style="color: #ababab; font-size: small">Дата ознакомления с регламентом:</div>
    <?= $value->work_rater_oper_confirmation->date_open_regulations ?>

    <div style="color: #ababab; font-size: small">Дата подтверждения:</div>
    <?= $value->work_rater_oper_confirmation->date_confirmation ?>

    <?php if (!empty($value->work_rater_oper_confirmation->comment)): ?>
        <div style="color: #ababab; font-size: small">Комментарий проверяющего:</div>
        <?= $value->work_rater_oper_confirmation->comment ?>
    <?php endif; ?>

    <?php if (!empty($qualification->count_reject_all)): ?>
        <div style="color: #ababab; font-size: small">Всего отклонений у исполнителя по работе:</div>
        <?= $qualification->count_reject_all ?>
    <?php endif; ?>

    <?php if ($value->work_rater_oper_confirmation->link_type == Req3WorkRaterOperConfirmation::LINK_TYPE_TASK): ?>
        <div class="card card-outline card-small border <?= $class ?> mt-2 ">
            <div class="card-header">
                <?= Html::a($value->work_rater_oper_confirmation->task->name ?? "Задача удалена", ['/process/task/view', 'id' => $value->work_rater_oper_confirmation->link_id], ['target' => "_blank"]) ?>
                <div style="color: #ababab; font-size: small">Задача</div>
            </div>

            <div class="card-body" style="background-color: #ededed">
                <?= IdentifierViewWidget::widget([
                    'identification'                  => Yii::$app->user->identity,
                    'task'                            => $value->work_rater_oper_confirmation->task,
                    'identifier'                      => $value->work_rater_oper_confirmation->identifier,
                    'is_only_view'                    => true,
                    'forced_show_remarks_executor_id' => $value->work_rater_oper_confirmation->oper_id,
                ]); ?>
            </div>

            <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                <?php if (in_array($value->work_rater_oper_confirmation->status, [Req3WorkRaterOperConfirmation::STATUS_NOT_CONFIRMED, Req3WorkRaterOperConfirmation::STATUS_CONFIRMED])): ?>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="showDialogConfirmationChangeStatusCredited(this, <?= $value->value_id ?>, <?= $task->id ?>, <?= $identifier->id ?>)">Работа принята</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showDialogConfirmationChangeStatusRejected(this, <?= $value->value_id ?>, <?= $task->id ?>, <?= $identifier->id ?>)">Работа не принята</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="showDialogConfirmationChangeStatusImpossible(this, <?= $value->value_id ?>, <?= $task->id ?>, <?= $identifier->id ?>)">Невозможно проверить</button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>


    <?php endif; ?>

    <?php if ($value->work_rater_oper_confirmation->link_type == Req3WorkRaterOperConfirmation::LINK_TYPE_ORDER && $value->work_rater_oper_confirmation->order): ?>
        <div class="card card-outline card-small border <?= $class ?> mt-2 ">
            <div class="card-header">
                <?php $name = ""; ?>
                <?php if ($value->work_rater_oper_confirmation->order->project && $value->work_rater_oper_confirmation->order->project->isUserType()): ?>
                    <?php $name = "На абонента (" . ($value->work_rater_oper_confirmation->order->project->user->fio ?? "user_{$value->work_rater_oper_confirmation->order->project->user_id}") . " по проекту"; ?>
                <?php else: ?>
                    <?php $name = $value->work_rater_oper_confirmation->order->generateAddressText() ?>
                <?php endif; ?>
                <?= Html::a($name, ['/order/task/view', 'id' => $value->work_rater_oper_confirmation->link_id], ['target' => "_blank"]) ?>
                <div style="color: #ababab; font-size: small">Наряд</div>
            </div>

            <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                <?php if (in_array($value->work_rater_oper_confirmation->status, [Req3WorkRaterOperConfirmation::STATUS_NOT_CONFIRMED, Req3WorkRaterOperConfirmation::STATUS_CONFIRMED])): ?>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-sm" onclick="showDialogConfirmationChangeStatusCredited(this, <?= $value->value_id ?>, <?= $task->id ?>, <?= $identifier->id ?>)">Работа принята</button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="showDialogConfirmationChangeStatusRejected(this, <?= $value->value_id ?>, <?= $task->id ?>, <?= $identifier->id ?>)">Работа не принята</button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="showDialogConfirmationChangeStatusImpossible(this, <?= $value->value_id ?>, <?= $task->id ?>, <?= $identifier->id ?>)">Невозможно проверить</button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php endif; ?>