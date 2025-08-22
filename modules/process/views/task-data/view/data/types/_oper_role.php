<?php

use app\models\Opers;
use app\modules\lists\models\ListsItems;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $access_identifier Req3Identifiers */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

if (!isset($access_identifier)) {
    $access_identifier = $identifier;
}

$is_select_list = $identifier->type_info;
$is_select_only_me = $identifier->getSettingByKey(Req3Identifiers::SETTING_SELECT_LIST_ONLY_MY, 0);

$list_item = !empty($is_select_list) && !empty($value->value_number) ? ListsItems::find()->andWhere(['group_id' => $is_select_list, 'id' => $value->value_number])->one() : null;

$role = $value->getRole();
?>
<div class="px-3 py-2" data-item="<?= $value->id ?>" style="background-color: <?= $list_item ? $list_item->color : "" ?>;">

    <div style="display: flex; flex-wrap: wrap; align-items: center;">
        <div style="margin-right: auto; padding-right: 10px;">
            <?php if ($value->oper): ?>
                <?= $value->oper->fio ?>
            <?php else: ?>
                <span style="color: #98140c; text-decoration: line-through"><?= Opers::getFio($value->value_id) ?></span>
            <?php endif; ?>

            <?php if ($role): ?>
                (<?= $role->description ?>)
            <?php else: ?>
                (role: <?= $value->value_text ?>)
            <?php endif; ?>
        </div>

        <?php if ($is_select_list !== null): ?>
            <div>
                <?php if ($is_editable && !$is_only_view && $can_edit && (!$is_select_only_me || $value->value_id == Yii::$app->user->id)): ?>

                    <?php if ($list_item): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOperRoleSelectItem(this, <?= $task->id ?>, <?= $identifier->id ?>, <?= $value->id ?>, <?= $access_identifier->id ?>)">
                            <?= $list_item->value ?>
                        </button>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="addOperRoleSelectItem(this, <?= $task->id ?>, <?= $identifier->id ?>, <?= $value->id ?>, <?= $access_identifier->id ?>)">
                            Не выбрано
                        </button>
                    <?php endif; ?>

                <?php else: ?>
                    <?php if ($list_item): ?>
                        <span style="font-style: italic"><?= $list_item->value ?></span>
                    <?php else: ?>
                        <span style="color: #ff0800;">Не выбрано</span>
                    <?php endif; ?>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>

    <?php if ($is_select_list !== null): ?>
        <?php if ($value->oper_role_comment): ?>
            <span style="color: #575757; font-weight: bold; font-size: small">Комментарий: </span>
            <span style="color: #717171; font-style: italic; font-size: small"><?= $value->oper_role_comment->comment ?></span>
        <?php endif; ?>
    <?php endif; ?>


    <div data-list-item-container="<?= $value->id ?>">

    </div>
</div>