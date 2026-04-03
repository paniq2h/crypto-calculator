<?php

use yii\db\Migration;

class m260403_125551_add_fields_to_cryptocurrency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%cryptocurrency}}', 'symbol', $this->string(20)->notNull()->unique());
        $this->addColumn('{{%cryptocurrency}}', 'name', $this->string(100)->notNull());
        $this->addColumn('{{%cryptocurrency}}', 'price_usd', $this->decimal(20, 8)->notNull()->defaultValue(0));
        $this->addColumn('{{%cryptocurrency}}', 'market_cap_usd', $this->decimal(20, 2)->null());
        $this->addColumn('{{%cryptocurrency}}', 'volume_24h_usd', $this->decimal(20, 2)->null());
        $this->addColumn('{{%cryptocurrency}}', 'change_24h_percent', $this->decimal(7, 2)->null());
        $this->addColumn('{{%cryptocurrency}}', 'updated_at', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%cryptocurrency}}', 'updated_at');
        $this->dropColumn('{{%cryptocurrency}}', 'change_24h_percent');
        $this->dropColumn('{{%cryptocurrency}}', 'volume_24h_usd');
        $this->dropColumn('{{%cryptocurrency}}', 'market_cap_usd');
        $this->dropColumn('{{%cryptocurrency}}', 'price_usd');
        $this->dropColumn('{{%cryptocurrency}}', 'name');
        $this->dropColumn('{{%cryptocurrency}}', 'symbol');
    }
}
