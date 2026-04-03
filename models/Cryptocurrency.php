<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%cryptocurrency}}".
 *
 * @property int $id
 * @property string $symbol
 * @property string $name
 * @property string $price_usd
 * @property string|null $market_cap_usd
 * @property string|null $volume_24h_usd
 * @property string|null $change_24h_percent
 * @property int $updated_at
 */
class Cryptocurrency extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cryptocurrency}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['symbol', 'name', 'price_usd', 'updated_at'], 'required'],
            [['market_cap_usd', 'volume_24h_usd', 'change_24h_percent'], 'default', 'value' => null],
            [['price_usd', 'market_cap_usd', 'volume_24h_usd', 'change_24h_percent'], 'number'],
            [['updated_at'], 'integer'],
            [['symbol'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 100],
            [['symbol'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'symbol' => 'Symbol',
            'name' => 'Name',
            'price_usd' => 'Price Usd',
            'market_cap_usd' => 'Market Cap Usd',
            'volume_24h_usd' => 'Volume 24h Usd',
            'change_24h_percent' => 'Change 24h Percent',
            'updated_at' => 'Updated At',
        ];
    }
}
