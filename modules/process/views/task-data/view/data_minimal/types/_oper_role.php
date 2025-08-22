<?php

use app\models\Opers;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$role = $value->getRole();
?>

<?php if ($value->oper): ?>
    <?= $value->oper->fio ?>
<?php else: ?>
    <span style="color: #98140c; text-decoration: line-through"><?= Opers::getFio($value->value_id) ?></span>
<?php endif; ?>

<?php if ($role): ?>
    (<?= $role->description ?>)
<?php else: ?>
    (role: <?= $value->value_text ?>)
<?php endif; ?>