<?php

use app\modules\address\models\Locations;
use app\modules\process\models\Req3TasksData;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $value Req3TasksDataItems */
?>

<?php if ($value->address_link): ?>
    <?= $value->address_link->getFullName(Locations::TYPE_COUNTRY, true, true) ?>
<?php else: ?>
    address_link<?= $value->value_id ?>
<?php endif; ?>

