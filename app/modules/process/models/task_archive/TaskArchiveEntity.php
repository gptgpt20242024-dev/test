<?php

namespace app\modules\process\models\task_archive;

use Yii;
use yii\db\ActiveRecord;

/**
 * Связь архивной задачи с сущностями.
 *
 * @property int $id               Первичный ключ
 * @property int $task_id          ID задачи из архива
 * @property int $value_id         ID связанной сущности
 * @property int $identifier_type  Тип идентификатора сущности
 */
class TaskArchiveEntity extends ActiveRecord
{
    /**
     * Используем подключение к БД архива.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_req3_archive');
    }

    public static function tableName(): string
    {
        return '{{%req3_task_archive_entity}}';
    }

    public static function primaryKey(): array
    {
        return ['id'];
    }

    public function rules(): array
    {
        return [
            [['task_id', 'value_id', 'identifier_type'], 'required'],
            [['task_id', 'value_id', 'identifier_type'], 'integer'],
        ];
    }
}
