<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cryptocurrency}}`.
 */
class m260403_113822_create_cryptocurrency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cryptocurrency}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%cryptocurrency}}');
    }
}
