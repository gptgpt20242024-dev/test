<?php

use yii\db\Migration;

class m240520_000002_create_req3_task_archive_entity_table extends Migration
{
    public $db = 'db_req3_archive';

    public function safeUp()
    {
        $this->createTable('{{%req3_task_archive_entity}}', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'value_id' => $this->integer()->notNull(),
            'identifier_type' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'uq_req3_task_archive_entity',
            '{{%req3_task_archive_entity}}',
            ['task_id', 'value_id', 'identifier_type'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%req3_task_archive_entity}}');
    }
}
