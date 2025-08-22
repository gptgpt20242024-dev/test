<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->counterparty): ?>
    <a href="<?= Url::toRoute(['/counterparties/counterparties/view', 'id' => $value->counterparty->id]) ?>" target="_blank">
        <?= $value->counterparty->getTitle() ?>
    </a>
<?php else: ?>
    counterparty<?= $value->value_id ?>
<?php endif; ?>

