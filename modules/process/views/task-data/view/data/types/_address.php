<?php

use app\modules\address\models\Locations;
use app\modules\order\models\FormTaskSearch;
use app\modules\process\components\HelperIdentifier;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_reports\Req3AddressTasksLead;
use app\modules\setting\constants\SettingLinkType;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $identifier Req3Identifiers */
/* @var $value Req3TasksDataItems */

/* @var $is_editable boolean */
/* @var $is_required boolean */
/* @var $is_only_view boolean */

/* @var $can_edit boolean */

$dopData = HelperIdentifier::getAddressData($value, $task, [HelperIdentifier::INFO_LEAD]);
?>

<?php if ($value->address): ?>
    <?= $this->render('map', ['value' => $value]) ?>
    <?= $this->render('state_zone', ['value' => $value]) ?>

    <?= $value->address->getFullName(Locations::TYPE_COUNTRY, true, true) ?>

    <?php if ($task): ?>
        <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap">
            <?= $this->render('dop_info', ['task' => $task, 'value' => $value]) ?>

            <a href="<?= Url::toRoute(['/order/task/find', 'link_id' => $value->value_id, 'link_type' => FormTaskSearch::LINK_TYPE_ADDRESS]) ?>" target="_blank" class="btn btn-light btn-sm">Найти наряды</a>

            <?= $this->render('leads', ['leads' => $dopData['leads']]) ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    address<?= $value->value_id ?>
<?php endif; ?>

