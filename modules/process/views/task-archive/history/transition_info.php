<?php
use app\components\Date;
use app\modules\process\components\HelperOper;

/* @var $this yii\web\View */
/* @var $item array */

$date = new Date($item['start_date']);
?>

<div style="margin-left: 60px; font-size: small; color: #9b9b9b" title="Информация о переходе">
    <div>
        <i class="fas fa-clock"></i> <?= $date->format(Date::FORMAT_DATE_TIME) ?>
        <?php if (!empty($item['oper_id'])): ?>
            <span style="color: #9985af">(<?= HelperOper::getFioById($item['oper_id']) ?>)</span>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($item['from_task_name'])): ?>
<div>
    <i class="fas fa-directions" style="background-color: #458eb5; color: #fafafa;"></i>
    <div class="timeline-item clearfix">
        <div class="timeline-body">
            БП запущен из другого БП: <?= $item['from_task_name'] ?>
        </div>
    </div>
</div>
<?php endif; ?>
