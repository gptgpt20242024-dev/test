<?php

use app\components\ModelHelper;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task_data\Req3TasksDataItems;

/* @var $this yii\web\View */
/* @var $task Req3Tasks */
/* @var $value Req3TasksDataItems */

$_models=[];
foreach ($value->template_steps as $template_step) {
    if (!empty($template_step->step_id))$_models[]= $template_step;
}
if (count($_models)>0)ModelHelper::loadWith($_models, [
    'step.version.template',
]);

$_models=[];
foreach ($value->template_steps as $template_step) {
    if (!empty($template_step->version_id))$_models[]= $template_step;
}
if (count($_models)>0)ModelHelper::loadWith($_models, [
    'version.template',
]);

$templates = [];
$version_ids = [];
$step_ids = [];


foreach ($value->template_steps as $template_step) {
    if (!empty($template_step->step_id) && $template_step->step && $template_step->step->version && $template_step->step->version->template) {
        $template_id = $template_step->step->version->template->id;
        if (!isset($templates[$template_id])) {
            $templates[$template_id] = [
                'name'      => $template_step->step->version->template->name . " v" . $template_step->step->version->version,
                'all_steps' => false,
                'steps'     => [],
            ];
        }

        $step_id = $template_step->step_id;
        $step_ids[$step_id] = $step_id;
        if (!isset($templates[$template_id]['steps'][$step_id])) {
            $templates[$template_id]['steps'][$step_id] = [
                'name' => $template_step->step->name,
            ];
        }
    }

    if (!empty($template_step->version_id) && $template_step->version && $template_step->version->template) {
        $template_id = $template_step->version->template_id;
        $version_ids[$template_step->version_id] = $template_step->version_id;
        if (!isset($templates[$template_id])) {
            $templates[$template_id] = [
                'name'  => $template_step->version->template->name . " v" . $template_step->version->version,
                'steps' => [],
            ];
        }
        $templates[$template_id]['all_steps'] = true;
    }
}
?>
    <ul class="mb-0">
        <?php foreach ($templates as $template): ?>
            <li>
                <?= $template['name'] ?>
                <?php if (count($template['steps']) > 0): ?>
                    <ul>
                        <?php foreach ($template['steps'] as $step): ?>
                            <li>
                                <?= $step['name'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>