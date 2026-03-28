<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PostgreSQL-17-4169E1?style=for-the-badge&logo=postgresql&logoColor=white" alt="PostgreSQL">
  <img src="https://img.shields.io/badge/OpenAI-API-412991?style=for-the-badge&logo=openai&logoColor=white" alt="OpenAI">
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
</p>

# Управувач со состаноци (Meeting Manager)

Систем за закажување состаноци, агенди и водење записници со автоматско генерирање на резиме, акциони точки и одлуки преку **OpenAI**. Генерирањето се одвива во позадина (Laravel Queue), а записникот се испраќа по е-пошта до сите учесници.

---

##  Функционалности

- Креирање, уредување и бришење на состаноци
- Додавање на агенда 
-  Додавање на учесници
-  Внесување на белешки по завршен состанок
- Автоматско генерирање на записник (резиме, акциони точки, одлуки) со **OpenAI**
-  Позадинска обработка (**Queue**) – корисникот не чека
- Испраќање на записник по е-пошта до **сите учесници**
- Автентикација (регистрација, најава, одјава)
- Авторизација – само креаторот може да уредува/брише, учесниците можат да гледаат

---

##  Технологии

| Компонента | Технологија                     |
|------------|---------------------------------|
| Backend | Laravel 12 (PHP 8.2)            |
| База на податоци | PostgreSQL                      |
| Frontend | Blade + Bootstrap 5             |
| Автентикација | Laravel Breeze                  |
| Вештачка интелигенција | OpenAI API (GPT-3.5-turbo)      |
| Е-пошта | Gmail SMTP           |
| Позадински задачи | Laravel Queue (database driver) |

---

##  Структура на базата

| Табела | Опис |
|--------|------|
| `users` | Регистрирани корисници |
| `meetings` | Состаноци (наслов, датум, време, локација, статус) |
| `agendas` | Агенда точки за секој состанок |
| `meeting_notes` | Белешки внесени од корисници |
| `meeting_minutes` | Генерирани записници (резиме, акциони точки, одлуки) |
| `participants` | Релација many-to-many помеѓу состаноци и корисници |

---

##  Инсталација и стартување

### 1. Клонирај го репозиториумот
```bash
git clone https://github.com/ana003m/meeting-management-app.git
cd meeting-management-app
```

### 2. Инсталирај ги PHP зависностите
```bash
composer install
```
### 3. Постави ја конфигурацијата
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Постави ја базата на податоци (PostgreSQL)
Во .env постави ги:
```text
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=meeting_db
DB_USERNAME=postgres
DB_PASSWORD=your_password
```
Креирај ја базата:
```bash
createdb -h 127.0.0.1 -p 5432 -U postgres meeting_db
```

### 5. Изврши ги миграциите
```bash
php artisan migrate
```

### 6. Постави го OpenAI API клучот
Во .env додај:
```text
OPENAI_API_KEY=your-openai-api-key-here
```

### 7. Постави ја е-поштата
Во .env додај:
```text
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```
### 8. Стартувај го серверот
```bash
php artisan serve
```

### 9. Стартувај го Queue worker (во посебен терминал)
```bash
php artisan queue:work
```

### 10. Отвори во прелистувач
```bash
http://127.0.0.1:8000
```
---
###  Авторизација

Правилата за пристап се дефинирани во `MeetingPolicy`:

- **view** – сите учесници може да ги гледаат состаноците
- **update / delete** – само креаторот може да направи измени или да избрише состанок

---

###  Како функционира OpenAI интеграцијата?

1. Корисникот внесува белешки по завршен состанок
2. Белешките се зачувуваат во база
3. Се испраќа `GenerateMeetingMinutes` Job во Queue
4. Queue worker го процесира Job-от:
    - Го повикува **OpenAI API** со детален промпт
    - Враќа **JSON** со `summary`, `action_items`, `decisions`
    - Го зачувува записникот во `meeting_minutes`
    - Испраќа е-пошта до **сите учесници**

---

###  Структура на проектот (клучни фајлови)
```text
app/
├── Http/Controllers/
│ ├── MeetingController.php # CRUD за состаноци
│ ├── MeetingNoteController.php # Внесување белешки, испраќање Job
│ └── Auth/ # Автентикација
├── Jobs/
│ └── GenerateMeetingMinutes.php # Позадинска задача (OpenAI, е-пошта)
├── Mail/
│ └── MeetingMinutesMail.php # Дефиниција на е-пошта
├── Models/
│ ├── Meeting.php
│ ├── Agenda.php
│ ├── MeetingNote.php
│ ├── MeetingMinute.php
│ ├── Participant.php
│ └── User.php
└── Policies/
└── MeetingPolicy.php # Правила за пристап
```
---
###  Тестирање
- Регистрирај се – /register
- Креирај состанок – додај агенда и учесници
- Внеси белешки по завршен состанок
- Провери дали записникот се генерирал на страницата на состанокот
- Провери дали е-поштата стигнала 
---
###  Рути
URL	Опис

| URL | Опис |
|-----|------|
| `/` | Пренасочување кон `/login` |
| `/login` | Страница за најава |
| `/register` | Страница за регистрација |
| `/dashboard` | Контролна табла |
| `/meetings` | Листа на сите состаноци |
| `/meetings/create` | Креирање нов состанок |
| `/meetings/{id}` | Преглед на состанок (агенда, белешки, записник) |
| `/meetings/{id}/minutes` | Листа на сите генерирани записници за состанокот |
| `/meetings/{id}/minutes/{minutes}` | Преглед на конкретен генериран записник |

---

### Изработено од:
- **Ана Манасиева** (221200)
- **Марија Прастова** (221093)

---

###  Лиценца

Проектот е наменет за образовни цели.

---
**Факултет:** Факултет за информатички науки и компјутерско инженерство - Скопје

**Курс:** Имплементација на системи со слободен и отворен код

