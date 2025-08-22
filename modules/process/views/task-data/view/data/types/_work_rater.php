<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\work_raters\Req3WorkRaterOperConfirmation;
use app\modules\process\widgets\WorkRaterDocumentBtnWidget;
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

<?php if ($value->workRater): ?>

    <?php if (Yii::$app->user->canMulti(['business.work_rater.index'])): ?>
        <a href="<?= Url::toRoute(['/process/work-rater/view', 'work_id' => $value->value_id]) ?>" target="_blank"><?= $value->workRater->name ?></a>
    <?php else: ?>
        <?= $value->workRater->name ?>
    <?php endif; ?>

    <?= WorkRaterDocumentBtnWidget::widget([
        'oper_id'    => Yii::$app->user->id,
        'type'       => Req3WorkRaterOperConfirmation::TYPE_STANDARD,
        'work_rated' => $value->workRater,
    ]); ?>
    <?= WorkRaterDocumentBtnWidget::widget([
        'oper_id'    => Yii::$app->user->id,
        'type'       => Req3WorkRaterOperConfirmation::TYPE_REGULATIONS,
        'work_rated' => $value->workRater,
    ]); ?>
    <?php if (!empty($value->workRater->control_text) || !empty($value->workRater->control_manual_id)): ?>
        <?= WorkRaterDocumentBtnWidget::widget([
            'oper_id'    => Yii::$app->user->id,
            'type'       => Req3WorkRaterOperConfirmation::TYPE_CONTROL,
            'work_rated' => $value->workRater,
        ]); ?>
    <?php endif; ?>

<?php else: ?>
    workRater<?= $value->value_id ?>
<?php endif; ?>

