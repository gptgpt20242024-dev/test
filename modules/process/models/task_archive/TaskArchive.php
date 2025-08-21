<?php

namespace app\modules\process\models\task_archive;

use Yii;
use yii\db\ActiveRecord;

/**
 * Модель архива задач.
 *
 * @property int         $task_id
 * @property string      $task_name
 * @property int         $template_id
 * @property string      $template_name
 * @property string      $task_date_create
 * @property string      $task_date_start_step
 * @property string      $date_add_to_archive
 * @property int|null    $step_is_last
 * @property int|null    $step_last_status
 * @property string|null $data_json
 */
class TaskArchive extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('db_req3_archive');
    }

    public static function tableName(): string
    {
        return 'req3_task_archive';
    }

    public function rules(): array
    {
        return [
            [['task_id', 'task_name', 'template_id', 'template_name', 'task_date_create', 'task_date_start_step', 'date_add_to_archive'], 'required'],
            [['task_id', 'template_id', 'step_is_last', 'step_last_status'], 'integer'],
            [['task_date_create', 'task_date_start_step', 'date_add_to_archive', 'data_json'], 'safe'],
            [['task_name', 'template_name'], 'string', 'max' => 255],
        ];
    }

    public function getId()
    {
        return $this->task_id;
    }
}
