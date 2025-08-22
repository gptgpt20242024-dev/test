<?php

namespace app\modules\process\models\task_data;

use app\components\ModelPreload;
use app\modules\process\models\_query;
use app\modules\process\models\task\Req3Tasks;
use app\modules\process\models\task\Req3TasksStepPriority;
use app\modules\process\models\template\Req3TemplateCategory;
use app\modules\process\models\template\Req3TemplateVersions;
use app\modules\process\models\template_steps\Req3TemplateSteps;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "req3_tasks_data_item_steps".
 *
 * @property integer              $id
 * @property integer              $item_id
 * @property integer $category_id
 * @property integer              $version_id
 * @property integer              $step_id
 * @property double               $priority_value
 *
 * @property Req3TemplateCategory $category
 * @property Req3TemplateVersions $version
 * @property Req3TemplateSteps    $step
 */
class Req3TasksDataItemSteps extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================

    // ============================================================================
    // ============================== ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ =========================
    // ============================================================================

    // ============================================================================
    // ============================== ИНИТ ========================================
    // ============================================================================
    public function init()
    {
    }

    public static function tableName()
    {
        return 'req3_tasks_data_item_steps';
    }


    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'item_id'        => 'Item ID',
            'category_id' => 'category_id',
            'version_id'     => 'Version ID',
            'step_id'        => 'Step ID',
            'priority_value' => 'Priority Value',
        ];
    }


    public static function find()
    {
        return new _query\Req3TasksDataItemStepsQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['item_id'], 'required'],
            [['item_id', 'category_id', 'version_id', 'step_id'], 'integer'],
            [['priority_value'], 'number'],
        ];
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getCategory()
    {
        return $this->hasOne(Req3TemplateCategory::class, ['id' => 'category_id']);
    }

    public function getVersion()
    {
        return $this->hasOne(Req3TemplateVersions::class, ['id' => 'version_id']);
    }

    public function getStep()
    {
        return $this->hasOne(Req3TemplateSteps::class, ['id' => 'step_id']);
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
    /**
     * @param self[] $items
     * @return array
     */
    public static function createTree(array $items, Req3Tasks $task)
    {
        $map = [];
        ModelPreload::preload(array_filter($items, fn(self $it) => !empty($it->step_id)), ['step.version.template']);
        ModelPreload::preload(array_filter($items, fn(self $it) => !empty($it->version_id)), ['version.template']);

        $fnctAddToMap = function ($item, $selected = false) use (&$map) {
            if ($item instanceof Req3TemplateCategory) {
                $key = "c{$item->id}";
                $map[$key] = [
                    'id'        => $item->id,
                    'type'      => "category",
                    'name'      => $item->name,
                    'children'  => [],
                    'parent_id' => $item->parent_id ? "c{$item->parent_id}" : null,
                    'sort'      => $item->sort_order,
                    'selected'  => $selected,
                    'is_child'  => false,
                ];
            }
            if ($item instanceof Req3TemplateVersions && $item->template) {
                $key = "v{$item->id}";
                $map[$key] = [
                    'id'        => $item->id,
                    'type'      => "version",
                    'name'      => $item->template->name . " v" . $item->version,
                    'children'  => [],
                    'parent_id' => "c{$item->template->category_id}",
                    'sort'      => $item->template->sort_order,
                    'selected'  => $selected,
                    'is_child'  => false,
                ];
            }
            if ($item instanceof Req3TemplateSteps && ($item->version->template ?? false)) {
                $key = "s{$item->id}";
                $map[$key] = [
                    'id'                 => $item->id,
                    'type'               => "step",
                    'name'               => $item->name,
                    'children'           => [],
                    'parent_id'          => "v{$item->version_id}",
                    'sort'               => $item->sort_order,
                    'selected'           => $selected,
                    'is_child'           => false,
                    'deviation_tasks'    => [],
                    'improvements_tasks' => [],
                ];
            }
        };

        foreach (Req3TemplateCategory::find()->all() as $category) {
            $fnctAddToMap($category);
        }

        $stepIds = [];
        foreach ($items as $item) {
            if (!empty($item->step_id)) {
                $fnctAddToMap($item->step, true);
                $fnctAddToMap($item->step->version ?? null);
                $stepIds[$item->step_id] = $item->step_id;
            }

            if (!empty($item->version_id)) {
                $fnctAddToMap($item->version, true);
            }
            if (!empty($item->category_id)) {
                $key = "c{$item->category_id}";
                if (isset($map[$key]))
                    $map[$key]['selected'] = true;
            }
        }

        if (!empty($stepIds)) {
            $dataItems = Req3TasksDataItemSteps::find()
                ->andWhere([Req3TasksDataItemSteps::tableName() . ".step_id" => $stepIds])
                ->select(['step_id', 'item_id'])
                ->asArray()->all();
            $mapDataItems = ArrayHelper::map($dataItems, 'step_id', 'step_id', 'item_id');
            $dataItemIds = array_keys($mapDataItems);

            if (count($dataItemIds) > 0) {
                $deviationTasks = Req3TasksDataItems::find()->id($dataItemIds)
                    ->linkType(Req3TasksDataItems::LINK_TYPE_TASK)
                    ->joinWith('task.step', false)
                    ->andWhere([Req3Tasks::tableName() . ".template_id" => $task->template_id])
                    ->andWhere([
                        'OR',
                        [Req3TemplateSteps::tableName() . ".is_deviation" => 1],
                        [Req3TemplateSteps::tableName() . ".is_deviation_architect" => 1],
                    ])
                    ->select([Req3Tasks::tableName() . ".id", Req3Tasks::tableName() . ".name", Req3TasksDataItems::tableName() . ".id as data_id"])
                    ->asArray()->all();
                foreach ($deviationTasks as $taskData) {
                    $taskId = $taskData['id'];
                    $name = $taskData['name'];
                    $dataId = $taskData['data_id'];
                    $stepIds = $mapDataItems[$dataId] ?? [];
                    foreach ($stepIds as $stepId) {
                        $key = "s{$stepId}";
                        if (isset($map[$key]))
                            $map[$key]['deviation_tasks'][$taskId] = $name;
                    }
                }

                $improvementsTemplateId = Yii::$app->controller->module->params['improvements_template_id'];
                $improvementsTasks = Req3TasksDataItems::find()->id($dataItemIds)
                    ->linkType(Req3TasksDataItems::LINK_TYPE_TASK)
                    ->joinWith('task.step', false)
                    ->andWhere([Req3Tasks::tableName() . ".template_id" => $improvementsTemplateId])
                    ->andWhere([Req3TemplateSteps::tableName() . ".is_last" => 0])
                    ->select([Req3Tasks::tableName() . ".id", Req3Tasks::tableName() . ".name", Req3TasksDataItems::tableName() . ".id as data_id"])
                    ->asArray()->all();
                foreach ($improvementsTasks as $taskData) {
                    $taskId = $taskData['id'];
                    $name = $taskData['name'];
                    $dataId = $taskData['data_id'];
                    $stepIds = $mapDataItems[$dataId] ?? [];
                    foreach ($stepIds as $stepId) {
                        $key = "s{$stepId}";
                        if (isset($map[$key]))
                            $map[$key]['improvements_tasks'][$taskId] = $name;
                    }
                }
            }
        }

        foreach ($map as $k => $item) {
            $parent = $item['parent_id'];
            if (isset($map[$parent])) {
                $map[$parent]['children'][] = &$map[$k];
                $map[$k]['is_child'] = true;
            }
        }
        $tree = [];
        foreach ($map as $k => $item) {
            if (!$item['is_child']) {
                $tree[] = $item;
            }
        }
        $fnctUnsetUnselected = function (&$items) use (&$fnctUnsetUnselected) {
            $hasSelected = false;
            foreach ($items as $k => $item) {
                $isSelected = $item['selected'];
                $childSelected = $fnctUnsetUnselected($items[$k]['children']);
                if (!$isSelected && !$childSelected) {
                    unset($items[$k]);
                }
                $hasSelected = $hasSelected || $isSelected || $childSelected;
            }
            return $hasSelected;
        };
        $fnctUnsetUnselected($tree);
        return $tree;
    }


    /**
     * @param self[] $items
     * @return integer
     */
    public static function calculatePriority($items)
    {
        $categoryIds = [];
        $versionIds = [];
        $stepIds = [];

        foreach ($items as $template_step) {
            if (!empty($template_step->step_id)) {
                $stepIds[$template_step->step_id] = $template_step->step_id;
            }

            if (!empty($template_step->version_id)) {
                $versionIds[$template_step->version_id] = $template_step->version_id;
            }

            if (!empty($template_step->category_id)) {
                $categoryIds[$template_step->category_id] = $template_step->category_id;
            }
        }

        if (!empty($categoryIds)) {
            $child = $categoryIds;
            while (!empty($child)) {
                $child = Req3TemplateCategory::find()->andWhere(['parent_id' => $child])->select('id')->column();
                foreach ($child as $key => $id) {
                    if (!isset($categoryIds[$id]))
                        $categoryIds[$id] = $id;
                    else
                        unset($child[$key]);
                }
            }

            $versionIdsFromCategory = Req3TemplateVersions::find()->joinWith('template', false)->andWhere(['category_id' => $categoryIds])->select(Req3TemplateVersions::tableName() . '.id')->column();
            foreach ($versionIdsFromCategory as $id)
                $versionIds[$id] = $id;
        }

        if (!empty($versionIds)) {
            $stepIdsFromVersions = Req3TemplateSteps::find()->andWhere(['version_id' => $versionIds])->select('id')->column();
            foreach ($stepIdsFromVersions as $id)
                $stepIds[$id] = $id;
        }

        if (empty($stepIds)) return 0;

        $query = Req3TasksStepPriority::find();
        $query->andWhere(['step_id' => $stepIds]);
        $query->andWhere(['>=', 'month', new Expression("DATE_SUB(NOW(), INTERVAL 12 MONTH)")]);
        return doubleval($query->sum('priority_sum'));
    }
}
