<?php

use app\components\Date;

/* @var $this yii\web\View */
/* @var $last_date string */
/* @var $date_check string */
?>

<?php $date = new Date($date_check); ?>
<?php if ($last_date != $date->format(Date::FORMAT_DATE_DB)): ?>
    <?php $last_date = $date->format(Date::FORMAT_DATE_DB) ?>
    <!-- timeline time label -->
    <div class="time-label">
        <span class="bg-cyan"><?= $date->format("d") . " " . $date->getMonthName() . " " . $date->format("Y") ?></span>
    </div>
<?php endif; ?>