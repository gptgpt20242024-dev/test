<?php

use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $leads array */
?>

<?php if (!empty($leads)): ?>
    <div class="btn-group">
        <?php $i = 0; ?>
        <?php foreach ($leads as $lead): ?>
            <a href="<?= Url::toRoute(['/process/report/tasks-lead', 'link_id' => $lead['id'], 'link_type' => $lead['type']]) ?>" target="_blank" class="btn btn-light btn-sm">
                <?php if ($i++ == 0) echo "Лидов "; ?>
                <?= $lead['name'] ?>: <span style="font-weight: bold"><?= $lead['count'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>