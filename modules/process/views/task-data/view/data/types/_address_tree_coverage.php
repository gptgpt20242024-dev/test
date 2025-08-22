<?php

use app\modules\address\models\Locations;
use app\modules\order\models\FormTaskSearch;
use app\modules\process\components\HelperIdentifier;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;
use app\modules\process\models\task_reports\Req3AddressTasksLead;
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

<?php if ($value->address_link): ?>

    <?= $this->render('state_zone', ['value' => $value]) ?>

    <?= $value->address_link->getFullName(Locations::TYPE_COUNTRY, true, true) ?>

    <?php
    $current = null;
    if ($value->address_link->street_id != null && $value->address_link->street && $value->address_link->street->coverage) {
        $current = $value->address_link->street->coverage->coverage;
    } elseif ($value->address_link->location_id != null && $value->address_link->location && $value->address_link->location->coverage) {
        $current = $value->address_link->location->coverage->coverage;
    }
    ?>
    <?php if ($value->address_link->coverage !== null || $current != null): ?>
        <div style="font-size: small; color: #606060">
            <?php if ($value->address_link->coverage === null): ?>
                На момент фиксации данных об охвате не было, на данный момент от составляет: <?= round($current) ?>
            <?php endif; ?>
            <?php if ($current === null): ?>
                На момент фиксации охват составлял: <?= round($value->address_link->coverage) ?>, на текущий момент данных нет.
            <?php endif; ?>

            <?php if (round($current) == round($value->address_link->coverage)): ?>
                Охват: <?= round($value->address_link->coverage) ?>.
            <?php else: ?>
                Охват составлял <?= round($value->address_link->coverage) ?>, на данный момент:
                <?php if ($current < $value->address_link->coverage): ?>
                    <span style="color: #b31e0d"><?= round($current) ?>.</span>
                <?php else: ?>
                    <span style="color: #199f0a"><?= round($current) ?>.</span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($task): ?>
        <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap">

            <?= $this->render('dop_info', ['task' => $task, 'value' => $value]) ?>

            <?php if ($value->address_link->street_id != null && $value->address_link->street) : ?>
                <a href="<?= Url::toRoute(['/order/task/find', 'link_id' => $value->address_link->street_id, 'link_type' => FormTaskSearch::LINK_TYPE_STREET]) ?>" target="_blank" class="btn btn-light btn-sm">Найти наряды</a>
            <?php elseif ($value->address_link->location_id != null && $value->address_link->location): ?>
                <a href="<?= Url::toRoute(['/order/task/find', 'link_id' => $value->address_link->location_id, 'link_type' => FormTaskSearch::LINK_TYPE_LOCATIONS]) ?>" target="_blank" class="btn btn-light btn-sm">Найти наряды</a>
            <?php endif; ?>

            <?= $this->render('leads', ['leads' => $dopData['leads']]) ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    address_link<?= $value->value_id ?>
<?php endif; ?>

