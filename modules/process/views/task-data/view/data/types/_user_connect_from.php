<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\user\constants\UserConnectionStatusTypes;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */


$types = UserConnectionStatusTypes::NAMES;
?>

<?php if (isset($types[$value->value_id])): ?>
    <?= $types[$value->value_id] ?>

    <?php if (!empty($value->value_text)): ?>
        (<?= $value->value_text ?>)
    <?php endif; ?>
<?php else: ?>
    conn_type<?= $value->value_id ?>
<?php endif; ?>