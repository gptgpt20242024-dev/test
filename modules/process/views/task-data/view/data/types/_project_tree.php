<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>


<?php if (!$value->node): ?>
    <div class="px-2 py-2">
        <div class="alert alert-default-warning mb-0">
            Дерево не создано
        </div>

        <?php if ($is_editable && !$is_only_view && $can_edit && $task): ?>
            <div class="mt-2" style="display: flex; flex-direction: column;align-items: center; gap: 5px;">
                <button type="button" class="btn btn-outline-success" style="width: 250px; max-width: 100%" onclick="showDialogProjectTreeAddNode(this, <?= $task->id ?>, <?= $identifier->id ?>)">Добавить корневой узел</button>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <?php $tree = $value->node->getTree(false) ?>
    <?php if ($tree): ?>
        <div class="pb-2" style="background-color: #e5e5e5">

            <?php if ($tree['parent']): ?>

                <div data-spoiler data-id="tree_node_parents_<?= $value->value_id ?>" data-save_time="60" data-container=".pb-2" data-content='[data-spoiler-content-parents=1]'>
                    <i class="far fa-plus-square mr-1" style="color: #9a9a9a;" data-close="1"></i>
                    <i class="far fa-minus-square mr-1" style="color: #9a9a9a; display: none" data-open="1"></i>
                    Структура проекта
                </div>

                <div style="display: none" data-spoiler-content-parents="1">
                    <div class="px-2 py-2" style="display: flex; gap: 5px; flex-direction: column">
                        <?= $this->render('project_tree/node_parents', [
                            'task'       => $task,
                            'identifier' => $identifier,
                            'item'       => $tree['parent'],

                            'is_editable'  => false,
                            'is_only_view' => true,
                            'can_edit'     => false,
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>


            <div class="list_tree pr-2">
                <ul>
                    <?= $this->render('project_tree/node_list', [
                        'task'       => $task,
                        'identifier' => $identifier,
                        'node'       => $tree['item'],
                        'children'   => $tree['children'],

                        'is_editable'  => $is_editable,
                        'is_only_view' => $is_only_view,
                        'can_edit'     => $can_edit,
                    ]); ?>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="px-2 py-2">
            <div class="alert alert-default-danger mb-0">
                Ошибка дерева
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>