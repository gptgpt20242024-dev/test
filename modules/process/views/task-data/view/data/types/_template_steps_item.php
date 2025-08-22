<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $item array */

?>

<li>
    <?php if ($item['type'] == 'category'): ?>
        <?= $item['name'] ?>
    <?php elseif ($item['type'] == 'version'): ?>
        <?= Html::a($item['name'], ['/process/version/view', 'id' => $item['id']]) ?>
    <?php elseif ($item['type'] == 'step'): ?>
        <?= Html::a($item['name'], ['/process/step/view', 'id' => $item['id']]) ?>
    <?php endif; ?>


    <div style="font-size: 11px">
        <?php if (!empty($item['deviation_tasks'] ?? [])): ?>
            <div style="color: #5d251b"><span style="font-weight: bold">Отклонения (<?= count($item['deviation_tasks']) ?>):</span>
                <ul>
                    <?php foreach ($item['deviation_tasks'] as $taskId => $name): ?>
                        <li>
                            - <a href="<?= Url::toRoute(['/process/task/view', 'id' => $taskId]) ?>"><?= $name ?></a>
                            <button type="button" class="btn btn-xs btn-light" data-spoiler data-container="li" data-content="[data-dop-info]" onclick="loadMinimalDataTask(this, <?= $taskId ?>, '[data-dop-info]', 'li')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div data-dop-info="1" style="display: none"></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!empty($item['improvements_tasks'] ?? [])): ?>
            <div style="color: #295d1b"><span style="font-weight: bold">Улучшения (<?= count($item['improvements_tasks']) ?>):</span>
                <ul>
                    <?php foreach ($item['improvements_tasks'] as $taskId => $name): ?>
                        <li>
                            - <a href="<?= Url::toRoute(['/process/task/view', 'id' => $taskId]) ?>"><?= $name ?></a>
                            <button type="button" class="btn btn-xs btn-light" data-spoiler data-container="li" data-content="[data-dop-info]" onclick="loadMinimalDataTask(this, <?= $taskId ?>, '[data-dop-info]', 'li')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div data-dop-info="1" style="display: none"></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>


    <?php if (count($item['children']) > 0): ?>
        <ul>
            <?php foreach ($item['children'] as $childItem): ?>
                <?= $this->render('_template_steps_item', ['item' => $childItem]) ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</li>