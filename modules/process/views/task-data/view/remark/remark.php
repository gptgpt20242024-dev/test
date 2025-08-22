<?php

use app\components\Date;
use app\models\Opers;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataRemarks;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\widgets\WorkRaterDocumentBtnWidget;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $remark Req3TasksDataRemarks */
/* @var $only_view boolean */

$date_send = new Date($remark->date_send);
$date_approved = new Date($remark->date_approved);

$style = "background-color: #ffe5e9;";
$class = "card-danger";

if ($remark->is_approved) {
    $style = "background-color: #eeffe5;";
    $class = "card-success";
}
?>

<div class="card <?= $class ?> card-outline mb-0" style="<?= $style ?>" id="remark_<?= $remark->id ?>" data-remark="<?= $remark->id ?>">
    <div class="card-header px-3 py-2" style="font-size: small">
        <span style="float: right; color: #595959"><?= Opers::getFioOrFioDeletedHtml($remark, 'oper_send', 'oper_send_id') ?></span>

        <?= $date_send->format(Date::FORMAT_DATE_TIME) ?> <span style="color: #6b6b6b">(<?= $date_send->toRemainingText(2, true) ?> назад)</span>

        <?php if ($remark->is_approved): ?>
            <span style="color: #168b24; font-style: italic">исправил через <?= Date::secondsToText($date_approved->subtractDateTime($date_send), 2) ?></span>
        <?php endif; ?>

        <div>
            <span style="color: #be5c5c">Улучшение на: <?= Opers::getFioOrFioDeletedHtml($remark, 'oper_executor', 'executor_id') ?></span>
        </div>
    </div>
    <div class="card-body px-3 py-2">
        <?= $remark->comment ?>
    </div>
    <?php if ($remark->canAccept(Yii::$app->user->identity, $task) && !$only_view): ?>
        <div class="card-footer px-3 py-2">
            <?php if (!empty($identifier->work_rated_id) && $identifier->work_rated): ?>
                <?= WorkRaterDocumentBtnWidget::widget([
                    'oper_id'       => Yii::$app->user->id,
                    'link_id'       => $remark->task_id,
                    'link_type'     => Req3WorkRaterOperConfirmation::LINK_TYPE_TASK,
                    'object_id'     => $remark->identifier_id,
                    'work_rated'    => $identifier->work_rated,
                    'type'          => Req3WorkRaterOperConfirmation::TYPE_STANDARD,
                    'min_date_open' => $remark->date_send,
                ]); ?>

                <?= WorkRaterDocumentBtnWidget::widget([
                    'oper_id'       => Yii::$app->user->id,
                    'link_id'       => $remark->task_id,
                    'link_type'     => Req3WorkRaterOperConfirmation::LINK_TYPE_TASK,
                    'object_id'     => $remark->identifier_id,
                    'work_rated'    => $identifier->work_rated,
                    'type'          => Req3WorkRaterOperConfirmation::TYPE_REGULATIONS,
                    'min_date_open' => $remark->date_send,
                ]); ?>
            <?php endif; ?>
            <button type="button" class="btn btn-xs btn-danger float-right" onclick="checkAcceptRemark(this, <?= $task->id ?>, <?= $identifier->id ?>, <?= $remark->id ?>)">Исправить</button>
        </div>
    <?php endif; ?>
    <div data-container-new-data="1">

    </div>
</div>
