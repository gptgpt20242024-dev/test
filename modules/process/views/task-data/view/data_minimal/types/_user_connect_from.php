<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\user\constants\UserConnectionStatusTypes;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$types = UserConnectionStatusTypes::NAMES;
?>

<?php if (isset($types[$value->value_id])): ?>
    <?= $types[$value->value_id] ?>

    <?php if (!empty($value->value_text)): ?>
        (<?= $value->value_text ?>)
    <?php endif; ?>
<?php else: ?>
    conn_type<?= $value->value_id ?>
<?php endif; ?>