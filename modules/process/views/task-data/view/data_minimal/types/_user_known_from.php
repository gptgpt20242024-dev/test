<?php

use app\models\Opers;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->known_from): ?>
    <?= $value->known_from->kf_text ?>

    <?php if ($value->value_id == 18): ?>
        (<?= Opers::getFio($value->value_text)?>)
    <?php else: ?>
        <?php if (!empty($value->value_text)): ?>
            (<?= $value->value_text ?>)
        <?php endif; ?>
    <?php endif; ?>

<?php else: ?>
    known_from<?= $value->value_id ?>
<?php endif; ?>

