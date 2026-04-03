# Crypto Calculator API (Yii2 + PostgreSQL)

Мини-приложение для получения и кэширования курсов криптовалют в локальной БД и расчета стоимости указанного количества монет в фиатной валюте.

## Что реализовано

- `GET /cryptocurrencies` — список поддерживаемых криптовалют и их курсов (из локальной БД).
- `GET /cryptocurrencies/{symbol}` — данные по одной криптовалюте.
- `POST /cryptocurrencies/calculate` — расчет стоимости по количеству и валюте (`USD`, `EUR`, `GBP`).
- `PUT /cryptocurrencies/update` — обновление курсов из внешнего API (CoinGecko) и сохранение в PostgreSQL.

Поддерживаемые криптовалюты (по умолчанию): `BTC`, `ETH`, `USDT`, `BNB`, `SOL`.

## Требования

- PHP 7.4+
- Composer
- PostgreSQL 12+

## Быстрый запуск

1. Установить зависимости:

```bash
composer install
```

2. Создать БД в PostgreSQL (пример):

```sql
CREATE DATABASE crypto_calc;
CREATE USER crypto_user WITH PASSWORD 'crypto_pass';
GRANT ALL PRIVILEGES ON DATABASE crypto_calc TO crypto_user;
```

3. Проверить настройки подключения в `config/db.php`.

4. Применить миграции:

```bash
php yii migrate
```

5. Запустить проект:

```bash
php yii serve --port=8080
```

API будет доступен по адресу `http://localhost:8080`.

## Проверка API (curl)

Сначала обновите курсы в локальной БД:

```bash
curl -X PUT http://localhost:8080/cryptocurrencies/update
```

Получить весь список:

```bash
curl http://localhost:8080/cryptocurrencies
```

Получить конкретную монету:

```bash
curl http://localhost:8080/cryptocurrencies/BTC
```

Расчет (USD):

```bash
curl -X POST http://localhost:8080/cryptocurrencies/calculate \
  -H "Content-Type: application/json" \
  -d "{\"symbol\":\"BTC\",\"amount\":2,\"currency\":\"USD\"}"
```

Расчет (EUR):

```bash
curl -X POST http://localhost:8080/cryptocurrencies/calculate \
  -H "Content-Type: application/json" \
  -d "{\"symbol\":\"ETH\",\"amount\":10,\"currency\":\"EUR\"}"
```

## Пример ответа `POST /cryptocurrencies/calculate`

```json
{
  "amount": 2,
  "symbol": "BTC",
  "price_per_unit": 50000,
  "currency": "USD",
  "total_price": 100000
}
```
