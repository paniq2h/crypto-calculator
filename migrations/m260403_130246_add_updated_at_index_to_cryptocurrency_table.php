<?php

use yii\db\Migration;

class m260403_130246_add_updated_at_index_to_cryptocurrency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-cryptocurrency-updated_at',
            '{{%cryptocurrency}}',
            'updated_at'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-cryptocurrency-updated_at',
            '{{%cryptocurrency}}'
        );
    }
}
