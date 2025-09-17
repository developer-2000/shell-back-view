## Инструкция по запуску проекта Laravel с использованием Sail

### Требования

-   Docker: Скачайте Docker с официального сайта: https://docs.docker.com/get-docker/
-   Docker Compose: Docker Compose обычно устанавливается вместе с Docker. Дополнительные инструкции: https://docs.docker.com/compose/install/
-   Windows с WSL 2: Рекомендуется использовать WSL 2. Узнайте, как его настроить: https://docs.microsoft.com/en-us/windows/wsl/install

### Пакеты

- [laravel-excel](https://laravel-excel.com/)


### Шаги по установке и запуску проекта

-   Клонируйте репозиторий:
    Откройте PowerShell или терминал WSL и выполните команду:

```sh
git clone https://github.com/involveno/shell2-backend.git
на текущий момент ветка - ryslan_back
```

-   Установите зависимости:
    Убедитесь, что у вас установлен Composer. Затем выполните:
```sh
composer require laravel/sail --dev

В LINUS КОНСОЛЕ
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

-   Скопируйте файл .env:
    Создайте файл .env, используя шаблон:

```sh
cp .env.example .env
```

-   Настройте .env:
    Измените параметры подключения к базе данных в файле .env. Например:

```sh
APP_PORT=1111
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=sail
DB_PASSWORD=password
```

-   Запустите Sail:
    Запустите контейнеры в фоновом режиме:
```sh
./vendor/bin/sail up -d
```

-   Запустите миграции и сидеры:
    Выполните миграции и сидеры:

```sh
./vendor/bin/sail artisan migrate --seed
```

-   Откройте страницу Front проекта в браузере:

```sh
http://localhost:1111
При смене на другой домен - убрать в .env - APP_PORT=1111
```

-   Откройте phpMyAdmin в браузере:
    Перейдите по адресу в браузере:

```sh
http://localhost:2222
login: sail
pass: password
```

- Дать разрешение домену обращаться к серверу с запросом API:
  В .env:
```sh
добавить или заменить разрешенные домены через запятую
ALLOWED_DOMAINS=localhost,example.com
```

- Адрес Telescope
```sh
http://localhost:1111/telescope/requests
```

## Laravel Telescope - Мониторинг и отладка

Laravel Telescope - это инструмент для мониторинга и отладки Laravel приложений. Доступен по адресу `http://localhost:1111/telescope/requests`

### Основные возможности:

**Requests** - отслеживание всех HTTP запросов:
- Время выполнения запросов
- Используемые маршруты и контроллеры
- Параметры запросов и ответы
- SQL запросы, выполненные во время запроса
- Использование памяти

**Commands** - мониторинг Artisan команд:
- Время выполнения команд
- Аргументы и опции команд
- Вывод команд

**Jobs** - отслеживание фоновых задач:
- Статус выполнения задач
- Время выполнения
- Ошибки в задачах

**Queries** - мониторинг SQL запросов:
- Все выполненные SQL запросы
- Время выполнения каждого запроса
- Стек вызовов

**Models** - отслеживание работы с моделями:
- Создание, обновление, удаление записей
- Связи между моделями

**Events** - мониторинг событий:
- Отправленные события
- Слушатели событий

**Logs** - просмотр логов приложения
**Mail** - отслеживание отправленных писем
**Notifications** - мониторинг уведомлений
**Cache** - операции с кэшем
**Scheduled** - выполнение запланированных задач

### Использование для отладки:

1. **Анализ медленных запросов** - в разделе Requests можно найти запросы с большим временем выполнения
2. **Отладка SQL запросов** - в разделе Queries видно все SQL запросы с их временем выполнения
3. **Мониторинг фоновых задач** - в разделе Jobs можно отследить выполнение очередей
4. **Отслеживание ошибок** - в разделе Logs видны все ошибки приложения
5. **Анализ производительности** - использование памяти и времени выполнения для каждого запроса

### Настройка Telescope:

Telescope автоматически настроен в проекте. Для продакшена рекомендуется ограничить доступ через middleware или отключить полностью.

## Авторизация пользователей
- Администратор
```sh
email: admin@admin.com
password: password1
```
- Менеджер 1
```sh
email: cm1@cm.com
password: password1
```
- Менеджер 2
```sh
email: cm2@cm.com
password: password1
```
- Дизайнер 1
```sh
email: designer1@designer.com
password: password1
```
- Дизайнер 2
```sh
email: designer2@designer.com
password: password1
```
- CM-Admin
```sh
email: cmadmin@cmadmin.com
password: password1
```
- Принтер
```sh
email: printer@printer.com
password: password1
```
- Принтер 2
```sh
email: printer2@printer.com
password: password1
```
- Дистрибьютор
```sh
email: distributor@distributor.com
password: password1
```
- Regular User
```sh
email: user@user.com
password: password1
```
- Regular User 2
```sh
email: user2@user.com
password: password1
```
- Regular User 3
```sh
email: user3@user.com
password: password1
```

## Все случаи отправки Email
- cm_and_admin_about_distributor.blade.php
```sh
  Кому отправка - CM создавшему Promotion и Admin отвечающему за Promotion.
  В событии отправки Дистрибьютером всех дизайнов, всем Станциям (Users)
  вызов в:
  •	Установить Трекер номера Дистрибьютера
  •	public function setDistributorTracker(array $validated)
  •	dispatch(new SentCmAndAdminAboutDistributorJob
```
- new_promotion.blade.php
```sh
Кому отправка – Админу (Ответственный за все Promotion)
В событии нажатия кнопки Notify Admin на странице promotion-settings
вызов в:
•	public function notifyAdminAboutPromotion
•	dispatch(new SendAboutNewPromotionJob($email, $promotion->id));
```
- new_design.blade.php
```sh
Кому отправка – Админу (Ответственный за все Promotion)
В событии - Обновление данных brief дизайна если статус дизайна меняется с Created на Brief
вызов в:
•	public function updateBriefSurfaceDesign 
•	public function updateDesignStatus
•	dispatch(new SendAboutNewDesignJob($email, $promotion_link, $design_link));
```
- \resources\views\emails\new_promotion_user.blade.php
```sh
Кому отправка – User (Компани)
В событии нажатия кнопки Notify Admin на странице promotion-settings
вызов в:
•	public function updateBriefSurfaceDesign 
•	dispatch(new SendUserAboutNewPromotionJob($userData, $validated['promotion_id']));
```


