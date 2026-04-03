<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cryptocurrency}}`.
 */
class m260403_124923_create_cryptocurrency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->getTableSchema('{{%cryptocurrency}}', true) !== null) {
            return;
        }

        $this->createTable('{{%cryptocurrency}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->getTableSchema('{{%cryptocurrency}}', true) === null) {
            return;
        }

        $this->dropTable('{{%cryptocurrency}}');
    }
}
