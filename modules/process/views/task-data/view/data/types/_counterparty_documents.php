<?php

use app\modules\process\models\task_data\Req3TasksDataItemDocs;
use app\modules\process\models\task_data\Req3TasksDataItems;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$model = null;
?>

<?php if ($value->doc_link): ?>

    <div style="color: #ec7d00; font-weight: bold">
        <?= $value->doc_link->getTitleSimple() ?>
    </div>

    <?php if ($value->doc_link->doc_type == Req3TasksDataItemDocs::DOC_TYPE_RWP && $value->doc_link->doc_rwp): ?>
        <?= $this->render('@app/modules/counterparties/views/counterparties/info/doc-rwp', ['model' => $value->doc_link->doc_rwp]) ?>
        <?php $model = $value->doc_link->doc_rwp; ?>
    <?php endif; ?>

    <?php if ($value->doc_link->doc_type == Req3TasksDataItemDocs::DOC_TYPE_PASSPORT_RF && $value->doc_link->doc_passport_rf): ?>
        <?= $this->render('@app/modules/counterparties/views/counterparties/info/doc-passport-rf', ['model' => $value->doc_link->doc_passport_rf]) ?>
        <?php $model = $value->doc_link->doc_passport_rf; ?>
    <?php endif; ?>

    <?php if ($value->doc_link->doc_type == Req3TasksDataItemDocs::DOC_TYPE_RESIDENCE && $value->doc_link->doc_residence): ?>
        <?= $this->render('@app/modules/counterparties/views/counterparties/info/doc-residence', ['model' => $value->doc_link->doc_residence]) ?>
        <?php $model = $value->doc_link->doc_residence; ?>
    <?php endif; ?>


    <?php if ($model && $model->phys_face && $model->phys_face->counterparty): ?>
        <div style="color: #787878; font-size: small; margin-top: 10px;">Привязано к
            <a href="<?= Url::toRoute(['/counterparties/counterparties/view', 'id' => $model->phys_face->counterparty->id]) ?>" target="_blank">
                <?= $model->phys_face->counterparty->getTitle() ?>
            </a>
        </div>
    <?php endif; ?>

<?php else: ?>
    doc_link<?= $value->value_id ?>
<?php endif; ?>

