<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var array $relations */
?>
<?php if (!empty($relations['crash'])): ?>
    <p class="mb-0"><strong>Авария:</strong> <a href="<?= Url::toRoute(['/crash/request/view', 'id' => $relations['crash']['crash_id']]) ?>" target="_blank">#<?= $relations['crash']['crash_id'] ?></a></p>
<?php endif; ?>
<?php if (!empty($relations['order'])): ?>
    <p class="mb-0"><strong>Наряд:</strong> <a href="<?= Url::toRoute(['/order/task/view', 'id' => $relations['order']['task_id']]) ?>" target="_blank">#<?= $relations['order']['task_id'] ?></a>
        <?php if (!empty($relations['order']['project_process_ids']) && count($relations['order']['project_process_ids']) > 1): ?>
            (задач по проекту: <?= count($relations['order']['project_process_ids']) ?>)
        <?php endif; ?>
    </p>
<?php endif; ?>
<?php if (!empty($relations['email'])): ?>
    <p class="mb-0"><strong>Письмо:</strong> <?= Html::encode($relations['email']['from']) ?>, дата: <?= Html::encode($relations['email']['date']) ?></p>
<?php endif; ?>
<?php if (!empty($relations['parent_task'])): ?>
    <p class="mb-0"><strong>Родительская задача:</strong> <a href="<?= Url::toRoute(['/process/task/view', 'id' => $relations['parent_task']['task_id']]) ?>" target="_blank">#<?= $relations['parent_task']['task_id'] ?></a> <?= Html::encode($relations['parent_task']['name']) ?></p>
<?php endif; ?>
<?php if (!empty($relations['parent_project_tasks'])): ?>
    <div class="mb-0"><strong>Родительские задачи проекта:</strong>
        <ul>
            <?php foreach ($relations['parent_project_tasks'] as $p): ?>
                <li><a href="<?= Url::toRoute(['/process/task/view', 'id' => $p['id']]) ?>" target="_blank">#<?= $p['id'] ?></a> <?= Html::encode($p['name']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if (!empty($relations['sub_tasks'])): ?>
    <div class="mb-0"><strong>Подзадачи:</strong>
        <ul>
            <?php foreach ($relations['sub_tasks'] as $group => $tasksGroup): ?>
                <?php foreach ($tasksGroup as $st): ?>
                    <li><a href="<?= Url::toRoute(['/process/task/view', 'id' => $st['id']]) ?>" target="_blank">#<?= $st['id'] ?></a> <?= Html::encode($st['name']) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php if (!empty($relations['sub_orders'])): ?>
    <div class="mb-0"><strong>Связанные наряды:</strong>
        <ul>
            <?php foreach ($relations['sub_orders'] as $order): ?>
                <li><a href="<?= Url::toRoute(['/order/task/view', 'id' => $order['order_id']]) ?>" target="_blank">#<?= $order['order_id'] ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
