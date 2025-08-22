<?php

use app\modules\address\models\Locations;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->cap): ?>
    <?= $value->cap->getFullName(Locations::TYPE_COUNTRY, true, true) ?>
<?php else: ?>
    cap<?= $value->value_id ?>
<?php endif; ?>

