<?php

use app\modules\counterparties\models\CounterpartiesFileSetting;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$types = CounterpartiesFileSetting::DIADOK_DOCUMENT_STATUSES;
?>

<?php if (isset($types[$value->value_text])): ?>
    <?= $types[$value->value_text] ?>
<?php else: ?>
    diadok_status_<?= $value->value_text ?>
<?php endif; ?>

