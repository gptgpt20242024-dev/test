<?php

use app\components\Phone;
use app\components\Str;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\widgets\oktelldial\OktellDial;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<?php if (Str::isHtmlDocument($value->value_text)): ?>
    <button class="btn btn-light text-primary" type="button" onclick="showDialogHTML(<?= $value->id ?>)">Просмотреть содержимое</button>
<?php else: ?>
    <i><?= Str::toLink(nl2br(Html::encode($value->value_text))) ?></i>

    <?php if (Phone::isPhone($value->value_text)): ?>

        <?= OktellDial::widget([
            'phone'     => $value->value_text,
            'linkClass' => "btn btn-xs btn-link",
            'text'      => "(<i class=\"fas fa-phone\"></i> Позвонить)",
			'fm_id' => $task->fm_id
        ]); ?>
    <?php endif; ?>
<?php endif; ?>
