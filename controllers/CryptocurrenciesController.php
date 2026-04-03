<?php

namespace app\controllers;

use app\models\Cryptocurrency;
use Yii;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

class CryptocurrenciesController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return Cryptocurrency::find()
            ->select(['id', 'symbol', 'name', 'price_usd', 'updated_at'])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->all();
    }

    public function actionView($symbol)
    {
        $coin = Cryptocurrency::find()
            ->where(['symbol' => strtoupper($symbol)])
            ->asArray()
            ->one();

        if ($coin === null) {
            throw new NotFoundHttpException('Cryptocurrency not found.');
        }

        return $coin;
    }

    public function actionCalculate()
    {
        $body = Yii::$app->request->bodyParams;
        $symbol = strtoupper((string)($body['symbol'] ?? ''));
        $amount = (float)($body['amount'] ?? 0);
        $currency = strtoupper((string)($body['currency'] ?? 'USD'));

        if ($symbol === '' || $amount <= 0) {
            throw new UnprocessableEntityHttpException('symbol and amount (> 0) are required.');
        }

        $coin = Cryptocurrency::findOne(['symbol' => $symbol]);
        if ($coin === null) {
            throw new NotFoundHttpException('Cryptocurrency not found.');
        }

        $usdPrice = (float)$coin->price_usd;
        $fiatRate = $this->getUsdToFiatRate($currency);
        $pricePerUnit = round($usdPrice * $fiatRate, 8);
        $total = round($pricePerUnit * $amount, 8);

        return [
            'amount' => $amount,
            'symbol' => $coin->symbol,
            'price_per_unit' => $pricePerUnit,
            'currency' => $currency,
            'total_price' => $total,
        ];
    }

    public function actionUpdate()
    {
        if (!Yii::$app->request->isPut) {
            throw new MethodNotAllowedHttpException('Only PUT is allowed.');
        }

        $coinMap = Yii::$app->params['supportedCoins'] ?? [];
        if (empty($coinMap)) {
            throw new UnprocessableEntityHttpException('supportedCoins is not configured.');
        }

        $ids = implode(',', array_values($coinMap));
        $url = 'https://api.coingecko.com/api/v3/simple/price'
            . '?ids=' . urlencode($ids)
            . '&vs_currencies=usd';

        $raw = $this->fetchUrl($url);
        if ($raw === false) {
            Yii::$app->response->statusCode = 502;
            return ['error' => 'Failed to fetch data from external API.'];
        }

        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            Yii::$app->response->statusCode = 502;
            return ['error' => 'External API returned invalid JSON.'];
        }

        $byId = [];
        $now = time();
        $updated = [];
        $coinNames = [
            'bitcoin' => 'Bitcoin',
            'ethereum' => 'Ethereum',
            'tether' => 'Tether',
            'binancecoin' => 'BNB',
            'solana' => 'Solana',
        ];

        foreach ($coinMap as $symbol => $id) {
            if (!isset($payload[$id]['usd'])) {
                continue;
            }

            $coin = Cryptocurrency::findOne(['symbol' => $symbol]);
            if ($coin === null) {
                $coin = new Cryptocurrency();
                $coin->symbol = $symbol;
            }

            $coin->name = (string)($coinNames[$id] ?? $symbol);
            $coin->price_usd = (float)$payload[$id]['usd'];
            $coin->market_cap_usd = null;
            $coin->volume_24h_usd = null;
            $coin->change_24h_percent = null;
            $coin->updated_at = $now;

            if (!$coin->save()) {
                Yii::$app->response->statusCode = 422;
                return ['errors' => $coin->errors];
            }

            $updated[] = $coin->toArray(['id', 'symbol', 'name', 'price_usd', 'updated_at']);
        }

        return [
            'updated_count' => count($updated),
            'items' => $updated,
        ];
    }

    private function getUsdToFiatRate(string $currency): float
    {
        if ($currency === 'USD') {
            return 1.0;
        }

        $allowed = Yii::$app->params['supportedFiatCurrencies'] ?? ['USD'];
        if (!in_array($currency, $allowed, true)) {
            throw new UnprocessableEntityHttpException('Unsupported fiat currency.');
        }

        $url = 'https://api.frankfurter.app/latest?from=USD&to=' . urlencode($currency);
        $raw = $this->fetchUrl($url);
        if ($raw === false) {
            throw new UnprocessableEntityHttpException('Unable to get fiat exchange rate.');
        }

        $payload = json_decode($raw, true);
        $rate = (float)($payload['rates'][$currency] ?? 0);
        if ($rate <= 0) {
            throw new UnprocessableEntityHttpException('Invalid fiat exchange rate.');
        }

        return $rate;
    }

    private function fetchUrl(string $url)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'crypto-calculator/1.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($response === false || $statusCode < 200 || $statusCode >= 300) {
                return false;
            }

            return $response;
        }

        return @file_get_contents($url);
    }
}
