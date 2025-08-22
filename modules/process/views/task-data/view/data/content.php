<?php

use app\modules\process\dto\RuleDataDto;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $ruleData ?RuleDataDto */
/* @var $access_identifier Req3Identifiers */
/* @var $identifier Req3Identifiers */
/* @var $values Req3TasksDataItems[] */

/* @var $is_required boolean */
/* @var $is_only_view boolean */
/* @var $is_editable boolean */
/* @var $is_custom_editable boolean */

/* @var $can_edit boolean */

if (!isset($access_identifier)) {
    $access_identifier = $identifier;
}

$class_container = "list-group list-group-flush";
$class_item = "list-group-item px-3 py-2";
$style_container = "";
$style_item = "";

if ($identifier->type == Req3Identifiers::TYPE_GROUP) {
    $class_container = "px-2 py-2";
    $style_container = "display: flex; flex-direction: column; gap: 10px; background-color: #eaeaea;";
    $class_item = "";
}

if ($identifier->type == Req3Identifiers::TYPE_SERVICE_BASKET) {
    $class_container = "";
    $class_item = "";
}

if ($identifier->type == Req3Identifiers::TYPE_PROJECT_TREE) {
    $class_container = "";
    $class_item = "";
}

if ($identifier->type == Req3Identifiers::TYPE_CRASH) {
    $class_container = "";
    $class_item = "";
}

if ($identifier->type == Req3Identifiers::TYPE_OPER_ROLE) {
    $class_item = "list-group-item p-0";
}

if ($identifier->type == Req3Identifiers::TYPE_GHOST) {
    $class_container = "";
    $class_item = "";
}

if (isset($force_class_container)) $class_container = $force_class_container;
if (isset($force_class_item)) $class_item = $force_class_item;
if (isset($force_style_item)) $class_item = $force_style_item;

$one_render = $identifier->isCustomView();
?>

<?php if (count($values) > 0): ?>
    <div class="<?= $class_container ?>" data-body="1" style="<?= $style_container ?>">
        <?php foreach ($values as $value): ?>
            <div class="<?= $class_item ?>" style="<?= $style_item ?>">
                <?= $this->render('/task-data/view/data/type', [
                    'task'              => $task,
                    'ruleData' => $ruleData ?? null,
                    'identifier'        => $identifier,
                    'access_identifier' => $access_identifier,
                    'value'             => $value,
                    'values'            => $values,

                    'is_editable'  => $is_editable,
                    'is_required'  => $is_required,
                    'is_only_view' => $is_only_view,

                    'can_edit'          => $can_edit,
                ]) ?>
            </div>
            <?php if ($one_render) break; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card-body px-2 py-2" data-body="1">
        <?= $this->render('/task-data/view/data/alert_fill', [
            'task'       => $task,
            'identifier' => $identifier,

            'is_required'        => $is_required,
            'is_editable'        => $is_editable,
            'is_only_view'       => $is_only_view,
            'is_custom_editable' => $is_custom_editable,
            'can_edit'           => $can_edit,
        ]); ?>
    </div>
<?php endif; ?>

<?php if ($task && $identifier->isHideSpoiler()): ?>
    <script>
        $(function () {
            let $identifier = $("#i_<?= $identifier->id ?>");
            let $body = $identifier.find("[data-body]");

            if ($body.height() > 150) {
                $body.css('max-height', '140px');
                $body.css('overflow', 'hidden');
                $body.css('position', 'relative');
                let $btn = $("<div>")
                    .css({
                        "position": "absolute",
                        "bottom": "0",
                        "left": "0",
                        "right": "0",
                        "padding": "7px 0 0 0",
                        "text-align": "center",
                        "background": "linear-gradient(0deg, rgba(247,247,247,1) 0%, rgba(247,247,247,1) 60%, rgba(255,255,255,0) 100%)",
                        "font-size": "small",
                        "color": "#1263f6",
                        "font-style": "italic",
                        "text-decoration": "underline",
                        "cursor": "pointer",
                        "z-index": 6,
                    })
                    .data('open', 0)
                    .html("Развернуть");
                $body.append($btn);

                $btn.click(function () {
                    let $btn = $(this);
                    let $body = $btn.closest("[data-body]");
                    let is_open = $btn.data('open');
                    if (is_open == 0) {
                        let $card = $body.closest(".card");

                        //клонируем что бы узнать развернутую высоту
                        let $clone = $body.clone().css({"height": "auto", "max-height": ""}).appendTo($card);
                        let height_real = $clone.css("height");
                        $clone.remove();

                        //назначаем высоту которая сейчас, что бы потом проанимировать
                        $body.css({"height": $body.css("height"), "max-height": ""});

                        $body.animate({"height": height_real}, 1000, function () {
                            $(this).css('height', 'auto');
                            $(this).css('overflow', 'visible');
                        });

                        $btn
                            .html("Свернуть")
                            .css({"position": "inherit"})
                            .data('open', 1);
                    } else {
                        $body.css('overflow', 'hidden');
                        $body.animate({"height": "140px"}, 1000);

                        $btn
                            .html("Развернуть")
                            .css({
                                "position": "absolute",
                                "z-index": 6,
                            })
                            .data('open', 0);
                    }
                });
            }
        });
    </script>
<?php endif; ?>