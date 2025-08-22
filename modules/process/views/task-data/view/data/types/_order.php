<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<?php if ($value->order): ?>
    <?= Html::a("Наряд №" . $value->value_id, ["/order/task/view", 'id' => $value->value_id], ['target' => '_blank']) ?>
<?php else: ?>
    Наряд id_<?= $value->value_id ?>
<?php endif; ?>