<?php

/* @var $this yii\web\View */

/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $is_required boolean */
/* @var $is_editable boolean */
/* @var $is_only_view boolean */
/* @var $is_custom_editable boolean */

/* @var $can_edit boolean */

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;

$click = "";
$style = "";
if (!$is_only_view && $is_editable && !$is_custom_editable && $can_edit) {
    $click = "editIdentifier(this, {$task->id}, {$identifier->id})";
    $style = "cursor: pointer;";
}

?>

<?php if ($is_required): ?>
    <div class="alert alert-default-danger m-0" onclick="<?= $click ?>" style="<?= $style ?>">
        <?php if ($is_editable): ?>
            Необходимо заполнить для перехода на следующий шаг
        <?php else: ?>
            Данные заполняются <b><u>внешними</u></b> источниками
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php if ($is_editable): ?>
        <div class="alert alert-default-warning m-0" onclick="<?= $click ?>" style="<?= $style ?>">
            Данные не заполнены
        </div>
    <?php else: ?>
        <div class="alert alert-default-secondary m-0">
            Данные не заполнены
        </div>
    <?php endif; ?>
<?php endif; ?>