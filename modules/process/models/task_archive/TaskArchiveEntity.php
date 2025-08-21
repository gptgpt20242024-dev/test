<?php

namespace app\modules\process\models\task_archive;

use Yii;
use yii\db\ActiveRecord;

/**
 * Связь архивной задачи с сущностями.
 *
 * @property int $id
 * @property int $task_id
 * @property int $value_id
 * @property int $identifier_type
 */
class TaskArchiveEntity extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('db_req3_archive');
    }

    public static function tableName(): string
    {
        return 'req3_task_archive_entity';
    }

    public function rules(): array
    {
        return [
            [['task_id', 'value_id', 'identifier_type'], 'required'],
            [['task_id', 'value_id', 'identifier_type'], 'integer'],
        ];
    }
}
