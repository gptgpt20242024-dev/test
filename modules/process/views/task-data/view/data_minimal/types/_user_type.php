<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\user\models\Users;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$types = Users::USER_TYPE_NAMES;
?>

<?php if (isset($types[$value->value_id])): ?>
    <?= $types[$value->value_id] ?>
<?php else: ?>
    user_type<?= $value->value_id ?>
<?php endif; ?>

