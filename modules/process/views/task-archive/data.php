<?php

use app\assets\MasonryAsset;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\widgets\IdentifierViewWidget;

/* @var $this yii\web\View */
/* @var $identifierIds int[] */
/* @var $identifiers app\modules\process\models\identifiers\Req3Identifiers[] */
/* @var $dataItems app\modules\process\models\task_data\Req3TasksDataItems[][] */

MasonryAsset::register($this);
?>

<div data-block="data">
    <div class="grid" data-grid="1">
        <div class="grid-sizer"></div>

        <?php foreach ($identifierIds as $identifierId): ?>
            <?php
            $data = $dataItems[$identifierId] ?? [];
            $identifier = $identifiers[$identifierId] ?? null;
            if (!$identifier) {
                /** @var Req3TasksDataItems $firstItem */
                $firstItem = reset($data);
                if ($firstItem) {
                    $identifier = new Req3Identifiers(['id' => $identifierId, 'type' => $firstItem->type]);
                }
            }
            ?>
            <div class="grid-item">
                <?= IdentifierViewWidget::widget([
                    'identification' => Yii::$app->user->identity,
                    'identifier'     => $identifier,
                    'forced_data'    => $data,
                    'is_only_view'   => true,
                ]) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        $(function () {
            let $grid = $('[data-grid="1"]');
            $grid.masonry({
                // set itemSelector so .grid-sizer is not used in layout
                itemSelector: '.grid-item',
                columnWidth: ".grid-sizer",
                gutter: 10,
                percentPosition: true,
                horizontalOrder: true
            });
            $grid.children("div:not(.grid-sizer)").each(function () {
                observePosition($(this), function ($item) {
                    $item.closest('.grid').masonry();
                });
            });
        });
    </script>
</div>












