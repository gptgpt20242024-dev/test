<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$role = $value->getRole();
?>

<?php if ($role): ?>
    <?= $role->description ?>
<?php else: ?>
    role: <?= $value->value_text ?>
<?php endif; ?>

