# Ремскол — ремонт сколов и трещин лобового стекла в Зеленограде

**Демо-версия сайта: https://falkinroman.github.io/remskol-site/**

Демо — статическая копия из папки [`docs/`](docs): все страницы, фото и навигация работают как на рабочем сайте,
но форма заявки ничего не отправляет, а страницы закрыты от индексации (`noindex`), чтобы не мешать SEO основного домена.

## Страницы

| Страница | Адрес |
|---|---|
| Главная | `/` |
| Ремонт сколов | `/uslugi/remont-skolov` |
| Ремонт трещин | `/uslugi/remont-treshhin` |
| Полировка стекла | `/uslugi/polirovka-stekla` |
| Примеры работ | `/primery-rabot` |
| Цены | `/ceny` |
| Контакты | `/kontakty` |
| Политика / Согласие (152-ФЗ) | `/privacy`, `/consent` |

## Стек

Laravel 13, PHP 8.3, Blade, чистые CSS/JS без сборки. Заявки с формы уходят в Telegram-бота.

## Запуск локально

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan serve
```

## Обновить демо

```bash
php artisan site:export
git add docs && git commit -m "Обновить демо" && git push
```

## Деплой на VPS

```bash
# .env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://домен.ru
TELEGRAM_BOT_TOKEN=...
TELEGRAM_CHAT_IDS=...

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
```

Пример конфига nginx — `deploy/nginx.conf`. Папку `docs/` на сервер выкладывать не нужно.
