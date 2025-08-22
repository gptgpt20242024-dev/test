<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */
/* @var $values Req3TasksDataItems[] */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$show_identifier = $identifier->identifier_link;
$values = $task ? $task->getDataIdentifier($identifier->type_info) : [];
?>

<?= $this->render('/task-data/view/data/content', [
    'task'              => $task,
    'access_identifier' => $identifier,
    'identifier'        => $show_identifier,
    'values'            => $values,

    'is_editable'        => $is_editable,
    'is_required'        => $is_required,
    'is_only_view'       => $is_only_view,
    'is_custom_editable' => true,

    'can_edit'          => $can_edit,
]) ?>