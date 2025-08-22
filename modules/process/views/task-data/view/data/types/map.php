<?php

use app\modules\address\models\Locations;
use app\modules\address\models\MapAddresses;
use app\modules\address\models\MapHouses;
use app\modules\address\models\MapStreets;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */

$coord = $value->getAddressCoord();
$point = $coord ? $coord->getPoint() : null;
?>

<?php if ($point): ?>
    <a href="https://maps.yandex.ru/?text=<?= "{$point->x} {$point->y}" ?>" target="_blank" class="btn btn-light btn-sm" style="font-size: small;">
        <img src="/img/icons/Yandex_Maps_icon.svg">
    </a>
<?php endif; ?>