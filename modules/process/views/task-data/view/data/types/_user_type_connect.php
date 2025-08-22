<?php

use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\user\models\Users;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$types = Users::CONN_TYPE_NAMES;
?>

<?php if (isset($types[$value->value_id])): ?>
    <?= $types[$value->value_id] ?>
<?php else: ?>
    conn_type<?= $value->value_id ?>
<?php endif; ?>

