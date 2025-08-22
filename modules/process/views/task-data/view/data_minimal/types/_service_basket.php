<?php

use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemBaskets;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $value Req3TasksDataItems */

$items = [];
foreach ($value->basket->services ?? [] as $item) {
    $items[] = ($item->reward_service->name ?? "-");
}
?>
<?= implode(", ", $items); ?>

<?php if ($value->basket->installment_type ?? false): ?>
    (Рассрочка на: <?= $value->basket->installment_value ?>
    <?= $value->basket->installment_type == Req3TasksDataItemBaskets::INSTALLMENT_TYPE_MONTH_COUNT ? "месяц." : "" ?>
    <?= $value->basket->installment_type == Req3TasksDataItemBaskets::INSTALLMENT_TYPE_MONTHLY_AMOUNT ? "р. в месяц." : "" ?>
    )
<?php endif; ?>