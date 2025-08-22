<?php

use app\assets\RippleCheckboxAsset;
use app\components\Str;
use app\modules\lists\models\ListsTreeItems;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */
/* @var $values Req3TasksDataItems[] */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

RippleCheckboxAsset::register($this);


$items = [];
/** @var ListsTreeItems[] $parents */
$parents = [];
if (!empty($value->value_id) && $value->list_tree_item) {
    $parent = $value->list_tree_item;
    while ($parent != null) {
        $parents[] = $parent;
        $parent = $parent->parent;
    }

    $items = $value->list_tree_item->getChildren()->andWhere(['OR', ['is_approved' => 1], ['is_approved' => 0, 'oper_added' => Yii::$app->user->id]])->all();
} else {
    $items = $identifier->list_tree_group ? $identifier->list_tree_group->getItems_main()->andWhere(['OR', ['is_approved' => 1], ['is_approved' => 0, 'oper_added' => Yii::$app->user->id]])->all() : [];
}

$items = ListsTreeItems::sortItems($items);
$parents = array_reverse($parents);
?>

<?php $key = "i_" . $identifier->id; ?>
<style>
    .cbx-label {
        -webkit-user-select: auto;
        user-select: auto;
    }
</style>


<?php if ($identifier->list_tree_group): ?>
    <?php ActiveForm::begin(['options' => ['id' => "form_{$identifier->id}"]]) ?>

    <?php if ($is_editable && !$is_only_view && $can_edit): ?>

        <?php
        $placeholder = "Введите для поиска";
        if ($identifier->list_tree_group->is_complemented) {
            $placeholder .= " или добавления недостающего пункта";
        }
        ?>

        <input class="form-control mb-2" type="text" id="search_<?= $identifier->id ?>" name="search[<?= $identifier->id ?>]" placeholder="<?= $placeholder ?>">
        <script>
            $('#search_<?= $identifier->id ?>').on('keyup', function () {
                let $e = $(this);
                let $form = $e.closest("form");
                let $container_search = $form.find("[data-search]");
                let $container_current = $form.find("[data-current]");
                let search = $e.val();
                if (search.length > 0) {
                    $container_search.show('fast');
                    $container_current.hide('fast');


                    let timer_id = $e.data("timer_id");
                    clearTimeout(timer_id);
                    timer_id = setTimeout(function () {

                        $.load2({
                            url: generateUrl('/process/task-data/ajax-find-list-tree', {
                                'task_id': <?=$task->id?>,
                                'identifier_id': <?=$identifier->id?>,
                                'group_id': <?=$identifier->list_tree_group->id?>,
                                'text': search,
                            }),
                            container: $container_search,
                            fragment: '.tree3',
                            fragmentContentOnly: false,
                        });

                    }, 2000);
                    $e.data("timer_id", timer_id);


                } else {
                    $container_search.hide('fast');
                    $container_current.show('fast');
                }
            });
        </script>
    <?php endif; ?>

    <input name="<?= Html::getInputName($value, '[' . $key . ']type') ?>" type="hidden" value="<?= $identifier->type ?>"/>
    <input name="<?= Html::getInputName($value, '[' . $key . ']identifier_id') ?>" type="hidden" value="<?= $identifier->id ?>"/>
    <?php if (!empty($value->value_id) && $value->list_tree_item)://если в поиске будет добавление это нам пригодится?>
        <input name="<?= Html::getInputName($value, '[' . $key . ']tree_parent_id') ?>" type="hidden" value="<?= $value->value_id ?>"/>
    <?php endif; ?>

    <div data-search="1" style="display: none">
        Поиск
    </div>

    <div data-current="1">


        <?php if (count($parents) > 0): ?>
            <div class="tree3">
                <ul class="tree3_item">
                    <?php foreach ($parents as $parent): ?>
                        <li class="tree3_item">
                            <?php if (!empty($parent->color)): ?>
                                <i class="fas fa-lightbulb mr-1" style="color: <?= $parent->color ?>; text-shadow: 0 0 5px black;"></i>
                            <?php endif; ?>

                            <?php if ($identifier->list_tree_group->is_hide_number): ?>
                                <?php $number = $parent->getNumber(); ?>
                                <?php if ($number !== null): ?>
                                    <i class="fas fa-info-circle" title="<?= $number ?>" data-toggle="tooltip" style="color: #cdcdcd"></i>
                                <?php endif; ?>
                                <?= Str::toLink($parent->getValueWithoutNumber()) ?>

                            <?php else: ?>
                                <?= Str::toLink($parent->value) ?>
                            <?php endif; ?>

                            <i class="fas fa-pen-fancy btn-correct" onclick="showDialogCorrect(event, <?= $parent->id ?>, <?= Req3Corrections::LINK_TYPE_LIST_TREE_ITEM ?>)"></i>

                            <?php if ($is_editable && !$is_only_view && $can_edit): ?>
                                <?php $back_id = "list_back_{$identifier->id}_{$parent->id}"; ?>
                                <a href="javascript:void (0);" id="<?= $back_id ?>" class="text-danger">
                                    <i class="far fa-times-circle"></i>
                                </a>
                                <script>
                                    $('#<?=$back_id?>').on('click', function (a1, a2, a3) {
                                        let e = this;
                                        let $container = $(e).closest("[data-identifier]");
                                        let $form = $container.find("form");

                                        <?php if (!empty($parent->parent_id)):?>
                                        $form.append($("<input>", {type: 'hidden', name: '<?= Html::getInputName($value, "[{$key}]value_id") ?>[]', value: '<?=$parent->parent_id?>'}));
                                        <?php endif;?>

                                        saveIdentifier(e, <?=$task->id?>, <?=$identifier->id?>, true);
                                    });
                                </script>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <?php if (!$is_only_view): ?>
            <?php /** @var Req3TasksDataItems[] $checked */
            $input_type = "radio";
            ?>
            <div class="option-input">
                <?php foreach ($items as $item): ?>
                    <?php $check_id = "list_{$identifier->id}_{$item->id}"; ?>

                    <label class="mt-3 <?= !($is_editable && !$is_only_view && $can_edit) ? "disabled" : "" ?>"
                           style="display: flex; position: relative; z-index: 5;">

                        <input
                                id="<?= $check_id ?>"
                                onchange="saveIdentifier(this, <?= $task->id ?>, <?= $identifier->id ?>)"
                                name="<?= Html::getInputName($value, "[{$key}]value_id") ?>[]" value="<?= $item->id ?>"
                                type="<?= $input_type ?>"
                            <?= !($is_editable && !$is_only_view && $can_edit) ? "disabled" : "" ?>
                                style="top: 0; flex: none;"
                        />
                        <div>
                            <?php if (!empty($item->color)): ?>
                                <i class="fas fa-lightbulb mr-1" style="color: <?= $item->color ?>; text-shadow: 0 0 5px black;"></i>
                            <?php endif; ?>

                            <?php if ($identifier->list_tree_group->is_hide_number): ?>
                                <?php $number = $item->getNumber(); ?>
                                <?php if ($number !== null): ?>
                                    <i class="fas fa-info-circle" title="<?= $number ?>" data-toggle="tooltip" style="color: #cdcdcd"></i>
                                <?php endif; ?>
                                <?= Str::toLink($item->getValueWithoutNumber()) ?>

                            <?php else: ?>
                                <?= Str::toLink($item->value) ?>
                            <?php endif; ?>

                            <i class="fas fa-pen-fancy btn-correct" onclick="showDialogCorrect(event, <?= $item->id ?>, <?= Req3Corrections::LINK_TYPE_LIST_TREE_ITEM ?>)"></i>

                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($task && !$value->isFill($task)): ?>
        <div style="margin: 0 -8px !important;">
            <?= $this->render('/task-data/view/data/alert_fill', [
                'task'       => $task,
                'identifier' => $identifier,

                'is_required'        => $is_required,
                'is_editable'        => $is_editable,
                'is_only_view'       => $is_only_view,
                'is_custom_editable' => true,
                'can_edit'           => $can_edit,
            ]); ?>
        </div>
    <?php endif; ?>

    <?php ActiveForm::end() ?>
<?php endif; ?>
