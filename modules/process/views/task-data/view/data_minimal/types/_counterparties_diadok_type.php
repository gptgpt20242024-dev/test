<?php

use app\modules\counterparties\models\CounterpartiesFileSetting;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$types = CounterpartiesFileSetting::DIADOK_DOCUMENT_TYPES;
?>

<?php if (isset($types[$value->value_text])): ?>
    <?= $types[$value->value_text] ?>
<?php else: ?>
    user_type<?= $value->value_text ?>
<?php endif; ?>

