<?php

use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3Tasks;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */

?>

<h3 class="m-0" style="color: #9c3c00; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" data-many-line="1">
    <span title="Название задачи" data-toggle="tooltip"><?= $task->name ?></span>
</h3>

<div style="font-size: small; color: #767676; font-style: italic;">
    <a href="<?= Url::toRoute(['/process/version/view', 'id' => $task->version_id, 'highlight_step_id' => $task->step_id]) ?>" target="_blank" title="Шаблон + версия" data-toggle="tooltip">
        <?= $task->template->name ?? "-" ?> v<?= $task->version->version ?? "-" ?>
    </a>
    <i class="fas fa-pen-fancy btn-correct text" onclick="showDialogCorrect(event, <?= $task->template->id ?? -1 ?>, <?= Req3Corrections::LINK_TYPE_TEMPLATE ?>)"></i>

    <?php if (!empty($task->template->vfp_id ?? null)): ?>
        - <span title="ЦКП: <?= Html::encode($task->template->vfp->final_product ?? "-") ?>" data-toggle="tooltip"><?= $task->template->vfp->short_product ?? "цкп удалена" ?></span>
    <?php endif; ?>
</div>

<span style="color: #00761a;" title="Фин. менеджер" data-toggle="tooltip"><?= $task->fm->fio ?? "Фирма не определена" ?></span>