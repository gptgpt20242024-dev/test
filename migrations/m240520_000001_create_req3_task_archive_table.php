<?php

use yii\db\Migration;

class m240520_000001_create_req3_task_archive_table extends Migration
{
    public $db = 'db_req3_archive';

    public function safeUp()
    {
        $this->createTable('{{%req3_task_archive}}', [
            // первичный ключ совпадает с ID задачи
            'task_id' => $this->primaryKey(),
            'task_name' => $this->string()->notNull(),
            'template_id' => $this->integer()->notNull(),
            'template_name' => $this->string()->notNull(),
            'task_date_create' => $this->dateTime()->notNull(),
            'task_date_start_step' => $this->dateTime()->notNull(),
            'date_add_to_archive' => $this->dateTime()->notNull(),
            'step_is_last' => $this->tinyInteger(),
            'step_last_status' => $this->integer(),
            'data_json' => $this->text(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%req3_task_archive}}');
    }
}
