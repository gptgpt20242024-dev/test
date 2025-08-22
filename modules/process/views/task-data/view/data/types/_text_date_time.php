<?php

use app\components\Date;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<?= $value->value_text ?>
<div style="color: #535353; font-size: small">
    <?= (new Date($value->value_text))->toRemainingText(2) ?>
</div>