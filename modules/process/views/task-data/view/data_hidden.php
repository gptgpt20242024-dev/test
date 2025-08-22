<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $hidden Req3Identifiers[] */
/* @var $hidden_empty Req3Identifiers[] */

?>

<div data-block="data-hidden">

    <?php if (count($hidden) > 0): ?>
        <div data-block="data-hidden-fill">
            <div style="color: #9a9a9a; font-size: small; font-style: italic" class="mb-3">
                Скрытые, заполненные идентификаторы
            </div>

            <div class="grid" data-grid="hidden-fill">
                <div class="grid-sizer"></div>
                <?php foreach ($hidden as $identifier): ?>
                    <div class="grid-item">
                        <?= $this->render('data/identifier_hidden', ['task' => $task, 'identifier' => $identifier]) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
                $(function () {
                    let $grid = $('[data-grid="hidden-fill"]');
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
                    $(document).ajaxComplete(masonryUpdates('[data-grid="hidden-fill"]'));
                });
            </script>
        </div>
    <?php endif; ?>


    <?php if (count($hidden_empty) > 0): ?>
        <div data-block="data-hidden-empty">
            <div style="color: #9a9a9a; font-size: small; font-style: italic" class="mb-3">
                Скрытые, пустые, но заполняемые ранее
            </div>

            <div class="grid" data-grid="hidden-empty">
                <div class="grid-sizer"></div>
                <?php foreach ($hidden_empty as $identifier): ?>
                    <div class="grid-item">
                        <?= $this->render('data/identifier_hidden', ['task' => $task, 'identifier' => $identifier]) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <script>
                $(function () {
                    let $grid = $('[data-grid="hidden-empty"]');
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
                    $(document).ajaxComplete(masonryUpdates('[data-grid="hidden-empty"]'));
                });
            </script>
        </div>
    <?php endif; ?>



    <?php if (count($hidden) == 0 && count($hidden_empty) == 0): ?>
        <div class="alert alert-default-primary">
            Скрытых идентификаторов нет.
        </div>
    <?php endif; ?>

</div>
