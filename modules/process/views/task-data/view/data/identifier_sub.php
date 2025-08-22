<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $group_data Req3TasksDataItems[] */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */


$is_multi = $identifier->is_multi;
$is_compact = !$is_multi && $identifier->type != Req3Identifiers::TYPE_GROUP;

$values = [];
foreach ($group_data as $value) {
    if ($value->identifier_id == $identifier->id && $value->type == $identifier->type) {
        $values[] = $value;
        if (!$is_multi) break;
    }
}

$one_render = $identifier->isCustomView();
?>
<div class="px-3 py-2">
    <span style="font-style: italic; color: #5d5d5d; font-size: small; float: right; margin-left: 10px;">(<?= $identifier->getTypeName() ?>)</span>
    <b><?= $identifier->name ?></b>

    <?php if (count($values) == 0): ?>
        : <span style="color: #b51700">Данные не заполнены</span>
    <?php else: ?>
        <?php if ($is_compact): ?>
            : <?= $this->render('type', [
                'task'       => $task,
                'identifier' => $identifier,
                'value'      => $values[0],
                'values'     => $values,

                'is_editable'  => $is_editable,
                'is_required'  => $is_required,
                'is_only_view' => $is_only_view,

                'can_edit'          => $can_edit,
            ]) ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if (count($values) > 0 && !$is_compact): ?>
    <div class="list-group list-group-flush">
        <?php $background = ($identifier->type == Req3Identifiers::TYPE_GROUP) ? "background-color: #eaeaea;" : "" ?>
        <?php $class = ($identifier->type == Req3Identifiers::TYPE_GROUP) ? "px-2 py-2" : "px-3 py-2" ?>

        <?php foreach ($values as $value): ?>
            <div class="list-group-item <?= $class ?>" style="<?= $background ?>">
                <?= $this->render('type', [
                    'task'       => $task,
                    'identifier' => $identifier,
                    'value'      => $value,
                    'values'     => $values,

                    'is_editable'  => $is_editable,
                    'is_required'  => $is_required,
                    'is_only_view' => $is_only_view,

                    'can_edit' => $can_edit,
                ]) ?>
            </div>
            <?php if ($one_render) break; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
