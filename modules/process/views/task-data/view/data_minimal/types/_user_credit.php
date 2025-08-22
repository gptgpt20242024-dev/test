<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\user\constants\UserCreditTypes;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$types = UserCreditTypes::NAMES;
?>

<?php if (isset($types[$value->value_id])): ?>
    <?= $types[$value->value_id] ?>
<?php else: ?>
    credit<?= $value->value_id ?>
<?php endif; ?>

