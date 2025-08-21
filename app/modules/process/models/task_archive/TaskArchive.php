<?php

namespace app\modules\process\models\task_archive;

use Yii;
use yii\db\ActiveRecord;
use app\modules\process\models\task_archive\TaskArchiveEntity;

/**
 * Модель архива задач.
 *
 * @property int         $task_id          ID задачи (первичный ключ)
 * @property string      $task_name        Название задачи
 * @property int         $template_id      ID шаблона
 * @property string      $template_name    Название шаблона
 * @property string      $task_date_create Дата создания задачи
 * @property string      $task_date_start_step Дата начала шага
 * @property string      $date_add_to_archive Дата добавления в архив
 * @property int|null    $step_is_last     Признак последнего шага
 * @property int|null    $step_last_status Финальный статус шага
 * @property string|null $data_json        Дополнительные данные
 */
class TaskArchive extends ActiveRecord
{
    /**
     * Используем отдельное подключение к БД архива.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_req3_archive');
    }

    public static function tableName(): string
    {
        return '{{%req3_task_archive}}';
    }

    /**
     * Первичный ключ таблицы.
     */
    public static function primaryKey(): array
    {
        return ['task_id'];
    }

    /**
     * Правила валидации.
     */
    public function rules(): array
    {
        return [
            [['task_id', 'task_name', 'template_id', 'template_name', 'task_date_create', 'task_date_start_step', 'date_add_to_archive'], 'required'],
            [['task_id', 'template_id', 'step_is_last', 'step_last_status'], 'integer'],
            [['task_date_create', 'task_date_start_step', 'date_add_to_archive', 'data_json'], 'safe'],
            [['task_name', 'template_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * Связанные сущности архива.
     */
    public function getEntities()
    {
        return $this->hasMany(TaskArchiveEntity::class, ['task_id' => 'task_id']);
    }
}
