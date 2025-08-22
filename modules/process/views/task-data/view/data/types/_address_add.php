<?php

use app\modules\address\models\Locations;
use app\modules\order\models\FormTaskSearch;
use app\modules\process\components\HelperIdentifier;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
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


$setting = [HelperIdentifier::INFO_USERS, HelperIdentifier::INFO_LEAD, HelperIdentifier::INFO_CADASTRAL, HelperIdentifier::INFO_COORDINATES];
$isNeedProcess = $identifier->getSettingByKey(Req3Identifiers::SETTING_ADDRESS_PROCESS, true);
if ($isNeedProcess) $setting[] = HelperIdentifier::INFO_PROCESS;
$dopData = HelperIdentifier::getAddressData($value, $task, $setting);
?>

<?php if ($value->address): ?>
    <?= $this->render('state_zone', ['value' => $value]) ?>

    <?= $value->address->getFullName(Locations::TYPE_COUNTRY, true, true) ?>

    <?php if (!empty($dopData['cadastral_number'])): ?>
        <div style="color: #006163; font-size: 12px">Кадастровый номер: <span style="color: #525252"><?= $dopData['cadastral_number'] ?></span></div>
    <?php endif; ?>

    <?php if (!empty($dopData['coordinates'])): ?>
        <div style="color: #006163; font-size: 12px">Координаты: <span style="color: #525252"><?= $dopData['coordinates'] ?></span></div>
    <?php endif; ?>

    <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap">

        <?= $this->render('map', ['value' => $value]) ?>

        <?php if ($task): ?>
            <?= $this->render('dop_info', ['task' => $task, 'value' => $value]) ?>
        <?php endif; ?>

        <div class="btn-group">
            <a href="<?= Url::toRoute(['/user1/profile/index', 'address' => $dopData['address']['id']]) ?>" target="_blank" class="btn btn-secondary btn-sm">Абонов: <?= $dopData['address']['users'] ?></a>
            <?php if (!empty($dopData['house']['id'])): ?>
                <a href="<?= Url::toRoute(['/user1/profile/index', 'address' => $dopData['house']['id']]) ?>" target="_blank" class="btn btn-secondary btn-sm" title="Абонов в доме">В доме: <?= $dopData['house']['users'] ?></a>
            <?php endif; ?>

            <?php if ($isNeedProcess): ?>
            <a href="<?= Url::toRoute(array_merge(['/process/task/index'], $dopData['process']['search'])) ?>" target="_blank" class="btn btn-secondary btn-sm">БП: <?= $dopData['process']['count'] ?></a>
            <?php endif; ?>
        </div>

        <a href="<?= Url::toRoute(['/order/task/find', 'link_id' => $value->value_id, 'link_type' => FormTaskSearch::LINK_TYPE_ADDRESS]) ?>" target="_blank" class="btn btn-light btn-sm">Найти наряды</a>

        <?= $this->render('leads', ['leads' => $dopData['leads']]) ?>
    </div>
<?php else: ?>
    address<?= $value->value_id ?>
<?php endif; ?>

