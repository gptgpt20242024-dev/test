<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\warehouse\models\SupReq;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<div>
	<span class="mr-1">Категория:</span>
	<b>
		<?php if ($value->syn_item): ?>
			<?= $value->syn_item->name ?>
		<?php else: ?>
			item<?= $value->value_id ?>
		<?php endif; ?>
	</b>
</div>
<?php if($value->value_id):?>
<div>
	<span class="mr-1">Средняя стоимость:</span>
	<b><?=round(SupReq::getLastAvgPriceBySynId($value->value_id),2)?></b>
</div>
<?php endif;?>

