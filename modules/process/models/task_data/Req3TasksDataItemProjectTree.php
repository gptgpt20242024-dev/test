<?php

namespace app\modules\process\models\task_data;

use app\models\Opers;
use app\modules\process\models\_query\Req3TasksDataItemProjectTreeQuery;
use app\modules\process\models\identifiers\Req3Identifiers;
use app\modules\process\models\task\Req3Tasks;
use app\modules\scheduler\components\HelperThread;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "req3_tasks_data_item_project_tree".
 *
 * @property integer   $id
 * @property string    $project_goal
 * @property string    $description
 * @property double    $planned_work_costs
 * @property double    $actual_work_costs
 * @property integer   $planned_income_type
 * @property double    $planned_income
 * @property double    $actual_income
 * @property integer   $planned_expenses_type
 * @property double    $planned_expenses
 * @property double    $actual_expenses
 * @property string    $deadline
 * @property string    $role
 * @property integer   $oper_id
 * @property integer   $status
 * @property integer   $what_is_done
 * @property string    $date_add
 * @property integer   $creator_id
 * @property integer   $from_task_id
 * @property integer   $target_task_id
 * @property integer   $node_type
 * @property integer   $parent_id
 * @property integer   $tree_id
 *
 * @property Opers     $oper
 * @property Req3Tasks $target_task
 * @property Req3Tasks $from_task
 * @property Req3TasksDataItemProjectTree $root
 * @property Req3TasksDataItems $item
 */
class Req3TasksDataItemProjectTree extends ActiveRecord
{
    // ============================================================================
    // ============================== КОНСТАНТЫ ===================================
    // ============================================================================
    const PAY_TYPE_MONTHLY  = 1;
    const PAY_TYPE_ONE_TIME = 2;
    const PAY_TYPE_NAMES    = [
        self::PAY_TYPE_MONTHLY  => "Ежемес.",
        self::PAY_TYPE_ONE_TIME => "Разовый",
    ];

    const NODE_TYPE_CLASSIC    = 1;
    const NODE_TYPE_OTHER_TASK = 2;

    const TREE_ITEM     = 'item';
    const TREE_PARENT   = 'parent';
    const TREE_CHILDREN = 'children';

    const STATUS_PLAN                    = 1;
    const STATUS_PAUSE                   = 2;
    const STATUS_IN_WORK                 = 3;
    const STATUS_CLOSED_SUCCESSFULLY     = 4;
    const STATUS_CLOSED_NOT_SUCCESSFULLY = 5;
    const STATUS_DEVIATION               = 6;
    const STATUS_NAMES                   = [
        self::STATUS_PLAN                    => "План",
        self::STATUS_PAUSE                   => "Пауза",
        self::STATUS_IN_WORK                 => "В работе",
        self::STATUS_CLOSED_SUCCESSFULLY     => "Закрыто - успех",
        self::STATUS_CLOSED_NOT_SUCCESSFULLY => "Закрыто - не успех",
        self::STATUS_DEVIATION               => "Отклонение",
    ];

    // ============================================================================
    // ============================== ДОПОЛНИТЕЛЬНЫЕ ПОЛЯ =========================
    // ============================================================================
    public $tree = false;

    // ============================================================================
    // ============================== ИНИТ ========================================
    // ============================================================================
    public function init()
    {
        $this->status = self::STATUS_PLAN;
    }

    public static function tableName()
    {
        return 'req3_tasks_data_item_project_tree';
    }


    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'project_goal'          => 'Задача',
            'description'           => 'Подробное описание задачи',
            'planned_work_costs'    => 'Плановые трудозатраты',
            'actual_work_costs'     => 'Фактические трудозатраты',
            'planned_income_type'   => 'Периодичность планового дохода',
            'planned_income'        => 'Плановый доход',
            'actual_income'         => 'Фактический доход',
            'planned_expenses_type' => 'Периодичность планового расхода',
            'planned_expenses'      => 'Плановые расходы',
            'actual_expenses'       => 'Фактические расходы',
            'deadline'              => 'Дедлайн',
            'role'                  => 'Роль исполнителя',
            'oper_id'               => 'Исполнитель',
            'status'                => 'Состояние',
            'what_is_done'          => 'Что сделано',
            'date_add'              => 'Date Add',
            'creator_id'            => 'Creator ID',
            'from_task_id'          => 'From Task ID',
            'target_task_id'        => 'Target Task ID',
            'node_type'             => 'node_type',
            'parent_id'             => 'Parent ID',
            'tree_id'               => 'Tree ID',
        ];
    }


    public static function find()
    {
        return new Req3TasksDataItemProjectTreeQuery(get_called_class());
    }

    // ============================================================================
    // ============================== ПРАВИЛА =====================================
    // ============================================================================
    public function rules()
    {
        return [
            [['project_goal', 'status', 'date_add', 'creator_id', 'from_task_id', 'node_type', 'tree_id'], 'required'],

            [['planned_work_costs', 'planned_income_type', 'planned_income', 'planned_expenses_type', 'planned_expenses', 'deadline'],
                'required', 'when' => fn($model) => empty($model->parent_id)],

            [['what_is_done'],
                'required', 'when' => fn($model) => in_array($model->status, [self::STATUS_CLOSED_NOT_SUCCESSFULLY, self::STATUS_CLOSED_SUCCESSFULLY])],

            [['planned_work_costs', 'actual_work_costs', 'planned_income', 'actual_income', 'planned_expenses', 'actual_expenses'], 'number'],
            [['planned_income_type', 'planned_expenses_type', 'oper_id', 'status', 'creator_id', 'from_task_id', 'target_task_id', 'node_type', 'parent_id', 'tree_id'], 'integer'],
            [['deadline', 'date_add'], 'safe'],
            [['project_goal'], 'string', 'max' => 256],
            [['role'], 'string', 'max' => 64],
            [['what_is_done', 'description'], 'string'],

            [['deadline'], 'validateDeadline'],

            [['target_task_id'], 'validateTarget'],
        ];
    }

    public function validateDeadline($attribute)
    {
        if (!empty($this->deadline)) {

            if (time() > strtotime($this->deadline)) {
                $this->addError($attribute, "Дедлайн нельзя ставить в прошлом");
            }

            $node = $this->getNodeValueExist('deadline', true);
            if ($node) {
                if (strtotime($node->deadline) < strtotime($this->deadline)) {
                    $this->addError($attribute, "Дедлайн превышает родительский ({$node->deadline})");
                }
            }

            $tree = $this->getTree(false);
            if ($tree) {
                $fnct_check = function ($children) use ($attribute, &$fnct_check) {
                    foreach ($children as $child) {
                        /** @var self $item */
                        $item = $child[self::TREE_ITEM];
                        if (!empty($item->deadline)) {
                            if (strtotime($item->deadline) > strtotime($this->deadline)) {
                                $this->addError($attribute, "Дедлайн меньше чем у дочернего узла ({$item->deadline})");
                            }
                        } else {
                            $fnct_check($child[self::TREE_CHILDREN]);
                        }
                    }
                };
                $fnct_check($tree[self::TREE_CHILDREN]);
            }

        }
    }

    public function validateTarget($attribute)
    {
        if (!empty($this->target_task_id)) {
            $exists = Req3TasksDataItemProjectTree::find()
                ->andFilterWhere(['!=', 'id', $this->id])
                ->andWhere(['target_task_id' => $this->target_task_id])
                ->one();

            if ($exists) {
                $this->addError($attribute, "В задаче {$exists->from_task_id} в узле уже используется привязываемая задача");
            }
        }
    }

    public function load($data, $formName = null)
    {
        $load = parent::load($data, $formName);

        $value_id = explode("_", $this->oper_id, 2);
        if (count($value_id) == 2) {
            $this->oper_id = $value_id[0];
            $this->role = $value_id[1];
        }

        if ($this->node_type == self::NODE_TYPE_OTHER_TASK) {
            $this->project_goal = $this->target_task->name ?? "-";
        }

        return $load;
    }

    public function updateSubTasks()
    {
        $tasks = [];
        $tree = $this->getTree(false);
        if ($tree) {
            $fnct_check = function ($children) use (&$fnct_check, &$tasks) {
                foreach ($children as $child) {
                    /** @var self $item */
                    $item = $child[self::TREE_ITEM];
                    if (!empty($item->target_task_id)) {
                        foreach ($item->target_task->data ?? [] as $item) {
                            if ($item->type == Req3Identifiers::TYPE_PROJECT_TREE && $item->value_id == $item->id && $item->identifier) {
                                $item->target_task->initAfterUpdateData($item->identifier);
                                $tasks[$item->target_task->id] = $item->target_task;
                                break;
                            }
                        }
                        $fnct_check($child[self::TREE_CHILDREN]);
                    }
                }
            };
            $fnct_check($tree[self::TREE_CHILDREN]);
        }
        foreach ($tasks as $task) {
            HelperThread::startMethod(2, $task, "checkAutoConditions");
        }
    }

    // ============================================================================
    // ============================== СЦЕНАРИИ ====================================
    // ============================================================================

    // ============================================================================
    // ============================== ГЕТТЕРЫ =====================================
    // ============================================================================
    public function getOper()
    {
        return $this->hasOne(Opers::class, ['oper_id' => 'oper_id']);
    }

    public function getTarget_task()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'target_task_id']);
    }

    public function getFrom_task()
    {
        return $this->hasOne(Req3Tasks::class, ['id' => 'from_task_id']);
    }

    public function getItem()
    {
        return $this->hasOne(Req3TasksDataItems::class, ['value_id' => 'id']);
    }

    public function getRoot()
    {
        return $this->hasOne(self::class, ['id' => 'tree_id'])->alias('node_root');
    }

    // ============================================================================
    // ============================== СЕТТЕРЫ =====================================
    // ============================================================================

    // ============================================================================
    // ============================== СОБЫТИЯ СОХРАНЕНИЯ ==========================
    // ============================================================================
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($this->tree_id == -1) {
            $this->tree_id = $this->id;
            $this->save(true, ['tree_id']);
        }

        /** @see Req3TasksDataItemProjectTree::updateSubTasks() */
        HelperThread::startMethod(2, $this, "updateSubTasks");
    }

    // ============================================================================
    // ============================== СОБЫТИЯ УДАЛЕНИЯ ============================
    // ============================================================================

    // ============================================================================
    // ============================== ЧТО КАСАЕТСЯ OBJECT =========================
    // ============================================================================
    public function getRole()
    {
        return Yii::$app->authManager->getRole($this->role);
    }

    public function getTree($return_parent = true)
    {
        if ($this->tree === false) {
            $items = self::find()->andWhere(['tree_id' => $this->tree_id])->indexBy('id')->all();
            $items[$this->id] = $this;

            $data = [];
            foreach ($items as $item) {
                $data[$item->id] = [
                    self::TREE_ITEM     => $item,
                    self::TREE_CHILDREN => [],
                    self::TREE_PARENT   => null,
                ];
            }
            foreach ($items as $item) {
                if ($item->parent_id != null && isset($data[$item->parent_id])) {
                    $data[$item->parent_id][self::TREE_CHILDREN][] =& $data[$item->id];
                    $data[$item->id][self::TREE_PARENT] =& $data[$item->parent_id];
                }
            }

            $this->tree = &$data;
            foreach ($items as $item) {
                $item->tree = &$data;
            }
        }

        $item = $this->tree[$this->id] ?? null;
        if (!$return_parent) {
            return $item;
        } else {
            if ($item) {
                while ($item[self::TREE_PARENT] != null) {
                    $item = $item[self::TREE_PARENT];
                }
                return $item;
            }
        }
        return null;
    }

    public function isAccessDelete($task, $oper, &$errors = [])
    {
        $tree = $this->getTree(false);
        if ($tree[self::TREE_PARENT] == null) {
            $errors[] = "Корневой элемент";
            return false;
        }

        if (count($tree[self::TREE_CHILDREN]) > 0) {
            $errors[] = "Есть дочерние элементы";
            return false;
        }

        /** @var self $item */
        $item = $tree[self::TREE_ITEM];
        if (!empty($item->target_task_id) && $item->node_type == self::NODE_TYPE_CLASSIC) {
            $errors[] = "Есть привязанная задача";
            return false;//если прикреплена задача то уже никто не может редактировать узел
        }

        return $this->isAccessEdit($task, $oper, $errors);
    }

    public function isAccessEdit($task, $oper, &$errors = [])
    {
        $tree = $this->getTree(false);

        /** @var self $item */
        $item = $tree[self::TREE_ITEM];

        while (true) {
            if (!$tree[self::TREE_PARENT]) {//если нет родителя, значит это корень, значит можно редактировать в той задаче в которой создано
                if ($item->from_task_id == $task->id) {
                    return true;
                } else {
                    $errors[] = "Есть доступ только из корневой задачи ({$item->from_task_id})";
                    return false;
                }
            } else {
                $tree = $tree[self::TREE_PARENT];
                $item = $tree[self::TREE_ITEM];
                if (!empty($item->target_task_id)) {//если у родителя есть задача значит можно только в этой задаче, иначе цикл повторится
                    if ($item->target_task_id == $task->id) {
                        return true;
                    } else {
                        $errors[] = "Есть доступ только из подзадачи ({$item->target_task_id})";
                        return false;
                    }
                }
            }
        }
    }

    public function isAccessAddNode($task, $oper, &$errors = [])
    {
        if ($this->node_type == self::NODE_TYPE_OTHER_TASK) return false;

        $tree = $this->getTree(false);
        while (true) {
            /** @var self $item */
            $item = $tree[self::TREE_ITEM];
            if (!empty($item->target_task_id)) {//если это целевой узел задачи значит можно добавлять под узлы
                if ($item->target_task_id == $task->id) {
                    return true;
                } else {
                    $errors[] = "Есть доступ только из подзадачи ({$item->target_task_id})";
                    return false;
                }
            } else {
                if (!$tree[self::TREE_PARENT]) {//если нет родителя, значит это корень, значит можно редактировать в той задаче в которой создано
                    if ($item->from_task_id == $task->id) {
                        return true;
                    } else {
                        $errors[] = "Есть доступ только из корневой задачи ({$item->from_task_id})";
                        return false;
                    }
                } else {
                    $tree = $tree[self::TREE_PARENT];
                }
            }
        }
    }

    /**
     * @param       $task
     * @param       $oper
     * @param self  $parent_node
     * @param array $errors
     * @return bool
     */
    public function isAccessMoveNode($task, $oper, $parent_node, &$errors = [])
    {
        $_errors = [];
        if (!$this->isAccessEdit($task, $oper, $_errors)) {
            $errors[] = "Перемещаемый узел нет права редактировать а значит и перемещать (" . implode(", ", $_errors) . ")";
            return false;
        }

        $_errors = [];
        if (!$parent_node->isAccessAddNode($task, $oper, $errors)) {
            $errors[] = "В узел в который вы собрались перещеать у вас нет права добавлять узлы а значит и переместить в него нельзя (" . implode(", ", $_errors) . ")";
            return false;
        }

        return true;
    }

    public function isAccessCreateTask($task, $oper, &$errors = [])
    {
        if ($this->node_type == self::NODE_TYPE_OTHER_TASK) return false;

        $tree = $this->getTree(false);

        /** @var self $item */
        $item = $tree[self::TREE_ITEM];
        if (!empty($item->target_task_id)) {
            $errors[] = "Есть привязанная задача";
            return false;//если прикреплена задача то уже никто не может редактировать узел
        }

        return $this->isAccessEdit($task, $oper, $errors);
    }

    public function getNodeValueExist($attribute, $only_parent = false)
    {
        if (!$only_parent && !empty($this->{$attribute})) return $this;

        $tree = $this->getTree(false);
        if ($tree) {
            $tree = $tree[self::TREE_PARENT];
            while ($tree) {
                /** @var self $item */
                $item = $tree[self::TREE_ITEM];
                if (!empty($item->{$attribute})) return $item;
                $tree = $tree[self::TREE_PARENT];
            }
        }

        return null;
    }

    // ============================================================================
    // ============================== STATIC ======================================
    // ============================================================================
    public static function getTypeName($type)
    {
        return self::PAY_TYPE_NAMES[$type] ?? "Unknown$type";
    }

    public static function getStatusName($status)
    {
        return self::STATUS_NAMES[$status] ?? "Unknown$status";
    }

    public static function unlinkTask($task_id)
    {
        $nodes = Req3TasksDataItemProjectTree::find()
            ->andWhere(['target_task_id' => $task_id, 'node_type' => Req3TasksDataItemProjectTree::NODE_TYPE_CLASSIC])
            ->all();
        foreach ($nodes as $node) {
            $node->target_task_id = null;
            $node->save(true, ['target_task_id']);
        }
    }

}