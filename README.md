#👨‍💻 Email-Sequence | 🆁🆄🅲🅾🅳🅴🆁
</br>
Описание плагина
Плагин "SSkin Email Sequence" предназначен для автоматизации email-маркетинга на сайтах WordPress с Elementor. Он отслеживает отправку форм Elementor и автоматически отправляет серию последующих писем подписчикам с настраиваемыми интервалами.
</br>
Основные возможности:

Отслеживание заполнения форм Elementor и сбор данных (имя и email)
Настройка серии до 6 писем с возможностью включения/отключения каждого
Тестовый режим с короткими интервалами (в минутах) для проверки работы
Рабочий режим с более длинными интервалами (в часах) для реального использования
Полностью настраиваемое содержимое каждого письма
Удобный интерфейс администратора для управления всеми параметрами
Использование на других сайтах
Да, вы можете использовать этот плагин на любом вашем сайте на WordPress с установленным Elementor. Плагин имеет гибкие настройки, которые позволяют адаптировать его под разные формы.

# Плагин SSkin Email Sequence для WordPress

## Описание
Плагин SSkin Email Sequence предназначен для отправки серии писем после заполнения формы Elementor. Это решение позволяет настроить автоматическую email-последовательность для нового подписчика с заданными интервалами.

## История создания плагина

### Задача
Клиент обратился с потребностью автоматизировать отправку серии писем после заполнения формы Elementor на сайте. Требовалось решение, которое позволило бы отправлять welcome-письмо сразу после регистрации и последующие письма через определенные интервалы времени.

Основные требования:
- Работа с конкретной формой Elementor (ID: 6478, идентификатор: rasilka_web)
- Отправка серии из 3-6 писем с настраиваемыми интервалами
- Тестовый режим для быстрой проверки работы (интервалы в минутах)
- Обычный режим для реального использования (интервалы в днях)
- Возможность редактирования содержания писем

### Процесс разработки

Разработка плагина шла в несколько этапов с постоянным улучшением функционала:

1. **Первый прототип** — создали базовый скрипт для отслеживания отправки формы Elementor и сохранения данных подписчика.

2. **Улучшение отслеживания форм** — внедрили три разных метода отслеживания для повышения надежности:
   - Отслеживание через событие `submit_success`
   - Отслеживание через нажатие кнопки отправки
   - Отслеживание через элементы формы

3. **Проблемы с отправкой писем** — столкнулись с трудностями работы WordPress Cron. Первоначально письма не отправлялись по расписанию.

4. **Решение с прямой отправкой** — реализовали механизм, где первое письмо отправляется сразу, а последующие планируются через короткие интервалы.

5. **Разработка плагина** — преобразовали скрипт в полноценный плагин с административным интерфейсом и настройками.

6. **Проблемы сохранения настроек** — столкнулись с ошибкой, когда плагин не сохранял ID формы и идентификатор в настройках.

7. **Финальное решение** — жестко закодировали ID формы и шаблона в коде плагина для гарантированной работы, убрали зависимость от сохранения настроек.

### Интересные моменты
В процессе разработки мы исследовали различные аспекты работы Elementor Pro и его систему форм, что позволило создать надежный механизм отслеживания отправки форм и извлечения данных пользователя. Пришлось изрядно покопаться в JavaScript API Elementor, чтобы найти самый надежный способ отслеживания событий формы.

При решении проблемы с сохранением настроек мы пришли к выводу, что использование констант в PHP-коде вместо опций в базе данных обеспечивает гораздо более стабильную работу в некоторых сценариях, особенно когда речь идет о критических параметрах.

## Функциональность

- Автоматическое отслеживание отправки формы Elementor
- Отправка приветственного письма сразу после регистрации
- Планирование и отправка серии последующих писем
- Тестовый режим для быстрой проверки (интервалы в минутах)
- Обычный режим для реального использования (интервалы в днях)
- Настройка содержания писем через административный интерфейс
- Подробное логирование для отладки

## Установка и настройка

1. Загрузите плагин на ваш сайт WordPress
2. Активируйте плагин через панель управления
3. Перейдите в раздел "Email Sequence" для настройки интервалов и содержания писем
4. Включите тестовый режим для быстрой проверки работы
5. После проверки переключитесь на обычный режим для реальной работы

## Адаптация для своих форм

Если вы хотите использовать плагин со своей формой Elementor, необходимо внести изменения в код плагина:

1. Откройте файл `sskin-email-sequence.php`
2. Найдите константы `FORM_ID` и `TEMPLATE_ID`
3. Замените их значения на идентификаторы вашей формы:
   ```php
   const FORM_ID = 'ваш_идентификатор_формы';
   const TEMPLATE_ID = 'ваш_id_шаблона';
   ```
4. Сохраните изменения

## Технические детали

- Плагин создает таблицу в базе данных для хранения подписчиков и статуса отправки писем
- Использует WordPress AJAX API для обработки отправки формы
- Для отправки писем используется стандартная функция WordPress `wp_mail()`
- Логи работы плагина сохраняются в файл `/wp-content/sskin-es-debug.log`

---

# SSkin Email Sequence WordPress Plugin

## Description
The SSkin Email Sequence plugin is designed to send a series of emails after an Elementor form submission. This solution allows you to set up an automatic email sequence for new subscribers with specified intervals.

## Plugin Creation History

### Task
The client approached us with the need to automate a series of emails after an Elementor form submission on their website. A solution was required that would allow sending a welcome email immediately after registration and subsequent emails at certain time intervals.

Main requirements:
- Integration with a specific Elementor form (ID: 6478, identifier: rasilka_web)
- Sending a series of 3-6 emails with customizable intervals
- Test mode for quick verification (intervals in minutes)
- Normal mode for real use (intervals in days)
- Ability to edit email content

### Development Process

The plugin development went through several stages with constant functionality improvements:

1. **First Prototype** — created a basic script to track Elementor form submissions and save subscriber data.

2. **Improved Form Tracking** — implemented three different tracking methods to increase reliability:
   - Tracking through the `submit_success` event
   - Tracking through the submit button click
   - Tracking through form elements

3. **Email Sending Issues** — encountered difficulties with WordPress Cron operation. Initially, emails were not sent on schedule.

4. **Direct Sending Solution** — implemented a mechanism where the first email is sent immediately, and subsequent ones are scheduled at short intervals.

5. **Plugin Development** — transformed the script into a full-fledged plugin with an administrative interface and settings.

6. **Settings Saving Issues** — encountered an error where the plugin did not save the form ID and identifier in the settings.

7. **Final Solution** — hardcoded the form ID and template in the plugin code for guaranteed operation, removed dependency on saving settings.

### Interesting Moments
During development, we explored various aspects of Elementor Pro's operation and its form system, which allowed us to create a reliable mechanism for tracking form submissions and extracting user data. We had to dig extensively into Elementor's JavaScript API to find the most reliable way to track form events.

When solving the settings saving problem, we concluded that using constants in PHP code instead of options in the database provides much more stable operation in some scenarios, especially when it comes to critical parameters.

## Functionality

- Automatic tracking of Elementor form submissions
- Sending a welcome email immediately after registration
- Planning and sending a series of subsequent emails
- Test mode for quick verification (intervals in minutes)
- Normal mode for real use (intervals in days)
- Email content configuration through the administrative interface
- Detailed logging for debugging

## Installation and Setup

1. Upload the plugin to your WordPress site
2. Activate the plugin through the control panel
3. Go to the "Email Sequence" section to configure intervals and email content
4. Enable test mode for quick verification
5. After verification, switch to normal mode for real operation

## Adapting for Your Forms

If you want to use the plugin with your own Elementor form, you need to make changes to the plugin code:

1. Open the `sskin-email-sequence.php` file
2. Find the `FORM_ID` and `TEMPLATE_ID` constants
3. Replace their values with your form identifiers:
   ```php
   const FORM_ID = 'your_form_identifier';
   const TEMPLATE_ID = 'your_template_id';
   ```
4. Save the changes

## Technical Details

- The plugin creates a database table to store subscribers and email sending status
- Uses WordPress AJAX API to process form submissions
- Uses the standard WordPress `wp_mail()` function to send emails
- Plugin logs are saved to the file `/wp-content/sskin-es-debug.log`

## Developer

👨‍💻 **Сергей Солошенко (@RussCoder)**
- Website Development since 2018 | WordPress / Full Stack
- "A site as if for myself" - this is how I would define my main principle

🌐 [Portfolio](https://рукодер.рф)
📩 [Email](mailto:support@рукодер.рф)
⚡ [Telegram](https://t.me/RussCoder)
📱 [Phone / WhatsApp](tel:+79859855397): +7 (985) 985-53-97
