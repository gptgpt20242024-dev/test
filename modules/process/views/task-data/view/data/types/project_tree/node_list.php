<?php

use app\assets\SortableJSAsset;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItemProjectTree;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $node Req3TasksDataItemProjectTree */
/* @var $children array */

/* @var $is_editable boolean */
/* @var $is_only_view boolean */
/* @var $can_edit boolean */
/* @var $parentIds array */

$key = bin2hex(random_bytes(10));

if (!isset($parentIds)) $parentIds = [];
$isLoop = isset($parentIds[$node->id]);
$parentIds[$node->id] = $node->id;

SortableJSAsset::register($this);
?>

<li data-container-node="<?= $node->id ?>" data-container-parent="<?= $node->parent_id ?>">
    <?php if ($node->node_type == Req3TasksDataItemProjectTree::NODE_TYPE_CLASSIC): ?>
        <?= $this->render('node_item', [
            'task'       => $task,
            'identifier' => $identifier,
            'node'       => $node,
            'children'   => $children,
            'key'        => $key,

            'is_editable'  => $is_editable,
            'is_only_view' => $is_only_view,
            'can_edit'     => $can_edit,
        ]); ?>


        <ul data-spoiler-content="<?= $key ?>">
            <?php if (!$isLoop): ?>
                <?php foreach ($children as $child): ?>
                    <?= $this->render('node_list', [
                        'task'       => $task,
                        'identifier' => $identifier,
                        'node'       => $child['item'],
                        'children'   => $child['children'],

                        'is_editable'  => $is_editable,
                        'is_only_view' => $is_only_view,
                        'can_edit'     => $can_edit,

                        'parentIds' => $parentIds,
                    ]); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-default-danger">Зацикливание</div>
            <?php endif; ?>
        </ul>

    <?php if ($is_editable && !$is_only_view && $can_edit): ?>
        <script>
            $(function () {
                let container = $("[data-spoiler-content='<?= $key ?>']")[0];
                Sortable.create(container, {
                    handle: ".js_drag_item",
                    group: 'tree_<?= $identifier->id ?>',
                    animation: 300,
                    easing: "cubic-bezier(1, 0, 0, 1)",
                    fallbackOnBody: true, // Appends the cloned DOM Element into the Document's Body
                    forceFallback: true, // ignore the HTML5 DnD behaviour and force the fallback to kick in
                    onEnd: function (/**Event*/ evt) {
                        let $item = $(evt.item);
                        let $container = $item.parent().closest("[data-container-node]");

                        let item_id = $item.data("container-node")
                        let current_parent_id = $item.data("container-parent")
                        let parent_id = $container.data("container-node")
                        if (current_parent_id == parent_id) {
                            PNotify.info('Вы не изменили родителя, изменение сортировки не сохраняется');
                        } else {
                            updateProjectTreeNode($item, <?= $task->id ?>, <?= $identifier->id ?>, item_id, parent_id);
                        }
                    },
                });
            });
        </script>
    <?php endif; ?>


    <?php else: ?>
        <?= $this->render('node_link', [
            'task'        => $task,
            'identifier'  => $identifier,
            'node'        => $node,
            'target_task' => $node->target_task,
            'key'         => $key,

            'is_editable'  => $is_editable,
            'is_only_view' => $is_only_view,
            'can_edit'     => $can_edit,
        ]); ?>
    <?php endif; ?>

</li>