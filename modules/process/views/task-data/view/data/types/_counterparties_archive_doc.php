<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

?>

<?php if ($value->archive_doc->setting ?? null): ?>
    <div>
        <?= Html::encode($value->archive_doc->setting->number) ?>
        <span style="color: #22922e">(<?= Html::encode($value->archive_doc->setting->date) ?>)</span>
        - <span style="font-weight: bold"><?= Html::encode($value->archive_doc->setting->type->name ?? "-") ?></span>
    </div>
    <div style="font-size: small">
        Статус: <span style="font-weight: bold"><?= Html::encode($value->archive_doc->setting->getStatusLabel() ?? "-") ?></span>

        <?php $sub_status = $value->archive_doc->setting->getSubStatusLabel(); ?>
        <?php if ($sub_status): ?>
            <div>Под-статус: <span style="font-weight: bold"><?= Html::encode($sub_status) ?></span></div>
        <?php endif; ?>

        <?php $diadok_url = $value->archive_doc->setting->getDiadokUrl(); ?>
        <?php if ($diadok_url): ?>
            <div>Источник: <span style="font-weight: bold"><a target="_blank" href="<?= Html::encode($diadok_url) ?>">Диадок</a> </span></div>
        <?php endif; ?>
    </div>

    <div style="font-size: small; color: #646464">
        <?= Html::encode($value->archive_doc->setting->label_archive) ?>
    </div>

    <?php if (!empty($value->archive_doc->setting->comment)): ?>
        <div style="font-style: italic; color: #0c7651;">
            <?= Html::encode($value->archive_doc->setting->comment) ?>
        </div>
    <?php endif; ?>

    <button type="button" class="btn btn-xs btn-light px-2" onclick="openCounterpartiesArchiveDocument(<?= $value->value_id ?>, '<?= htmlspecialchars($value->archive_doc->file_name) ?>')">
        Открыть
    </button>

<?php else: ?>
    doc_<?= $value->value_id ?>
<?php endif; ?>