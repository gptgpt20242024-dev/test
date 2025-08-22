<?php

use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->doc_link): ?>
    <?= $value->doc_link->getTitle() ?>
<?php else: ?>
    doc_link<?= $value->value_id ?>
<?php endif; ?>

