<?php

use app\modules\process\models\task\Req3Tasks;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

?>

<?php
$map_executors_main = [];

foreach ($task->observers as $link_oper) {
    if ($link_oper->oper) {
        $map_executors_main[$link_oper->oper_id] = $link_oper->oper;
    }
}
?>

<?= implode(", ", ArrayHelper::getColumn($map_executors_main, 'fio')) ?>
<?php if (count($map_executors_main) == 0): ?>
    В задаче нет наблюдателей
<?php endif; ?>
