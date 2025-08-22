<?php

use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\Req3Corrections;
use app\modules\process\models\task\Req3Tasks;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */

?>
<div id="i_<?= $identifier->id ?>" data-identifier="<?= $identifier->id ?>" class="mb-3">
    <div class="card mb-0">

        <i class="fas fa-pen-fancy btn-correct left" onclick="showDialogCorrect(event, <?= $identifier->id ?>, <?= Req3Corrections::LINK_TYPE_IDENTIFIER ?>)"></i>

        <div class="card-header px-3 py-2">
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <div>
                    <?= $this->render('/identifiers/_icon_type', ['type' => $identifier->type, 'id' => $identifier->id]) ?>

                    <?= $identifier->name ?>

                    <span style="font-style: italic; color: #5d5d5d; font-size: small">(<?= $identifier->getTypeName() ?>)</span>
                </div>

                <button type="button" class="btn btn-light" onclick="viewIdentifier(this, <?= $task->id ?>, <?= $identifier->id ?>)" style="margin-left: auto">Посмотреть</button>
            </div>
        </div>
    </div>
</div>