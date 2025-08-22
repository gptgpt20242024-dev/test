<?php

use app\modules\lists\models\ListsTreeGroups;
use app\modules\lists\models\ListsTreeItems;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $group ListsTreeGroups */
/* @var $data_item array */
/* @var $value Req3TasksDataItems */
/* @var $key string */

/** @var ListsTreeItems $item */
$item = $data_item['item'];
?>

<li class="tree3_item">
    <?php if (isset($data_item['find'])): ?>
        <div class="option-input">
            <?php $check_id = "list_{$identifier->id}_{$item->id}"; ?>
            <?php $input_type = "radio"; ?>
            <label class="" style="display: flex;">
                <input
                        id="<?= $check_id ?>"
                        onchange="saveIdentifier(this, <?= $task->id ?>, <?= $identifier->id ?>)"
                        name="<?= Html::getInputName($value, "[{$key}]value_id") ?>[]"
                        value="<?= $item->id ?>"
                        type="<?= $input_type ?>"
                        style="top: 0; flex: none;"/>

                <div>
                    <?php if (!empty($item->color)): ?>
                        <i class="fas fa-lightbulb mr-1" style="color: <?= $item->color ?>; text-shadow: 0 0 5px black;"></i>
                    <?php endif; ?>
                    <?= $data_item['find'] ?>
                    <?php if (!empty($data_item['hint'])): ?>
                        <div style="font-size: small; color: #6b6b6b"><?= $data_item['hint'] ?></div>
                    <?php endif; ?>
                </div>
            </label>
        </div>
    <?php else: ?>
        <?php if (!empty($item->color)): ?>
            <i class="fas fa-lightbulb mr-1" style="color: <?= $item->color ?>; text-shadow: 0 0 5px black;"></i>
        <?php endif; ?>
        <?= $item->value ?>
    <?php endif; ?>

    <?php if (count($data_item['children']) > 0): ?>
        <ul class="tree3_item">
            <?php foreach ($data_item['children'] as $child_data_item): ?>
                <?= $this->render('_find_item', ['task' => $task, 'identifier' => $identifier, 'group' => $group, 'data_item' => $child_data_item, 'value' => $value, 'key' => $key]); ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</li>