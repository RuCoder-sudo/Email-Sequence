<?php
/**
 * Plugin Name: SSkin Email Sequence
 * Plugin URI: https://рукодер.рф/
 * Description: Отправка серии писем после отправки формы Elementor.
 * Version: 1.0.3
 * Author: RUCODER
 * Author URI: https://рукодер.рф/
 * Text Domain: email-sequence-wordpress
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 7.0.0
 * @package Email Sequence
 * 
 * ВАЖНО - АВТОРСКИЕ ПРАВА!
 * =========================
 * Этот плагин является интеллектуальной собственностью разработчика.
 * Распространение, копирование, модификация, продажа данного плагина
 * без письменного разрешения владельца строго запрещена.
 * Нарушение авторских прав преследуется по закону.
 * 
 * Разработчик: RUCODER - Разработка сайтов.
 * Сайт: https://рукодер.рф/
 * Телеграм: https://t.me/RussCoder
 * VK: https://vk.com/rucoderweb
 * Instagram: https://www.instagram.com/rucoder.web/
 * Email: rucoder.rf@yandex.ru
 * 
 * По всем вопросам и для заказа разработки сайтов
 * обращайтесь по контактным данным выше.
 */

// Защита от прямого доступа
if (!defined('ABSPATH')) {
    exit;
}

// Определение констант плагина
define('SSKIN_ES_VERSION', '1.0.3');
define('SSKIN_ES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SSKIN_ES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SSKIN_ES_MAX_EMAILS', 6); // Максимальное количество писем в последовательности

// Основной класс плагина
class SSkin_Email_Sequence {
    // Хранилище опций плагина
    private $options;
    
    // Конструктор класса
    public function __construct() {
        // Загружаем настройки по умолчанию
        $default_options = array(
            'test_mode' => true,
            'test_intervals' => array(1, 2, 3, 4, 5, 6), // в минутах
            'normal_intervals' => array(24, 48, 72, 96, 120, 144), // в часах
            'email_status' => array(true, true, true, false, false, false), // статус активности писем
            'from_name' => get_bloginfo('name'),
            'from_email' => 'info@' . parse_url(get_site_url(), PHP_URL_HOST),
            'emails' => array(
                'email1' => array(
                    'subject' => 'SSkin/ Доставка',
                    'content' => $this->get_default_email_content(1),
                    'active' => true
                ),
                'email2' => array(
                    'subject' => 'ДНК SSKIN',
                    'content' => $this->get_default_email_content(2),
                    'active' => true
                ),
                'email3' => array(
                    'subject' => 'Клуб лимитированных слотов',
                    'content' => $this->get_default_email_content(3),
                    'active' => true
                ),
                'email4' => array(
                    'subject' => 'Новинки SSkin',
                    'content' => $this->get_default_email_content(4),
                    'active' => false
                ),
                'email5' => array(
                    'subject' => 'Специальное предложение',
                    'content' => $this->get_default_email_content(5),
                    'active' => false
                ),
                'email6' => array(
                    'subject' => 'Благодарим за внимание к нашему бренду',
                    'content' => $this->get_default_email_content(6),
                    'active' => false
                )
            )
        );
        
        // Загружаем сохраненные настройки или используем значения по умолчанию
        $this->options = get_option('sskin_es_options', $default_options);
        
        // Регистрируем действия при активации и деактивации
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Добавляем меню администратора
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Регистрируем скрипты и стили
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));
        
        // Настраиваем AJAX обработчики
        add_action('wp_ajax_sskin_es_save_settings', array($this, 'ajax_save_settings'));
        add_action('wp_ajax_sskin_es_sequence', array($this, 'ajax_add_subscriber'));
        add_action('wp_ajax_nopriv_sskin_es_sequence', array($this, 'ajax_add_subscriber'));
        
        // Регистрируем действия для отправки писем
        add_action('sskin_es_send_email_1', array($this, 'send_email_1'), 10, 1);
        add_action('sskin_es_send_email_2', array($this, 'send_email_2'), 10, 1);
        add_action('sskin_es_send_email_3', array($this, 'send_email_3'), 10, 1);
        add_action('sskin_es_send_email_4', array($this, 'send_email_4'), 10, 1);
        add_action('sskin_es_send_email_5', array($this, 'send_email_5'), 10, 1);
        add_action('sskin_es_send_email_6', array($this, 'send_email_6'), 10, 1);
        
        // Добавляем JavaScript трекер на фронтенд
        add_action('wp_footer', array($this, 'add_form_tracker'));
    }
    
    // Функция активации плагина
    public function activate() {
        // Создаем таблицу подписчиков
        global $wpdb;
        $table_name = $wpdb->prefix . 'sskin_es_subscribers';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email varchar(100) NOT NULL,
            name varchar(100) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            email1_sent tinyint(1) DEFAULT 0,
            email2_sent tinyint(1) DEFAULT 0,
            email3_sent tinyint(1) DEFAULT 0,
            email4_sent tinyint(1) DEFAULT 0,
            email5_sent tinyint(1) DEFAULT 0,
            email6_sent tinyint(1) DEFAULT 0,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Обновляем версию плагина
        update_option('sskin_es_version', SSKIN_ES_VERSION);
    }
    
    // Функция деактивации плагина
    public function deactivate() {
        // Очищаем запланированные задачи
        wp_clear_scheduled_hook('sskin_es_send_email_1');
        wp_clear_scheduled_hook('sskin_es_send_email_2');
        wp_clear_scheduled_hook('sskin_es_send_email_3');
        wp_clear_scheduled_hook('sskin_es_send_email_4');
        wp_clear_scheduled_hook('sskin_es_send_email_5');
        wp_clear_scheduled_hook('sskin_es_send_email_6');
    }
    
    // Получение содержимого писем по умолчанию
    private function get_default_email_content($num) {
        switch ($num) {
            case 1:
                return 'Здравствуйте, уважаемый клиент!

Спасибо вам большое за регистрацию на нашем сайте и доверие нашему бренду. Мы искренне ценим ваш выбор и хотим сделать ваше пребывание среди наших клиентов приятным и комфортным.

Чтобы ваши покупки были ещё удобнее, мы подготовили подробную информацию о доставке товаров нашего бренда:

✅ Москва и Московская область:
Наши курьеры доставят выбранные вами товары прямо домой с удобной возможностью примерки. Оформили заказ до 17:00? Получайте вашу одежду уже завтра! Стоимость доставки: Москва — 700 ₽, Московская область — 1000 ₽.

✅ Вся Россия:
Покупатели из регионов могут воспользоваться услугами службы СДЭК. Доставка займёт всего 2–4 рабочих дня. Цена отправки зависит от вашего местоположения и стартует от 300 ₽.

✅ За пределами России:
Жители других стран смогут получать наши изделия благодаря международной службе доставки. Подробности сроков и стоимости уточните дополнительно, цена от 1000 ₽.

⭐️ Специальное предложение:
Закажите одежду на сумму от 10000₽ и получите бесплатную доставку по всей стране!

Будем рады видеть вас снова и помогать создавать уникальный образ каждый день. Если возникнут вопросы — напишите нам, мы всегда готовы поддержать вас в выборе лучших вещей.

До новых встреч,
Команда SSkin

---
Трендовые покупки на SSkin.org
Клуб лимитированных слотов SSkin - t.me/Sskinbot';

            case 2:
                return 'Привет!

🌟 Ты присоединилась к миру SSKIN — бренду для тех, кто создает тренды, вдохновляет окружающих и живет ярко. Наша миссия — поддерживать твою уверенность и желание выразить себя через уникальные, смелые и актуальные вещи, подчеркивающие твою индивидуальность.

Мы создаем яркие, привлекательные образы с дорогими деталями и премиальным качеством материалов. Уверены, твоя жизнь наполнена энергией лидерства, страстью и творчеством, ведь именно такие женщины выбирают нашу марку.

Готовься получать вдохновение от наших коллекций, участвовать в эксклюзивных акциях и быть частью сообщества ярких, успешных и независимых женщин.

Присоединяйся в наши соцсети и Клуб лимитированных слотов, будь в центре внимания вместе с нами и создавай свои правила стиля!

⭐️ Твой бренд SSKIN
Энергия лидерства. Смелость быть собой.

Трендовые покупки на SSkin.org
Клуб лимитированных слотов SSkin - t.me/Sskinbot';

            case 3:
                return 'Приветствуем тебя в мире уникальных возможностей! 🌟

Приглашаем стать резидентом закрытого клуба лимитированных слотов нашего бренда.

👉 Почему стоит присоединиться?

✅ Получи скидку 15% на любые онлайн-покупки каждый месяц! Мы регулярно публикуем новые промокоды исключительно для членов клуба.

✅ Получай суперскидки 50% на ограниченные слоты из любых коллекций, которые мы публикуем в Клубе 1-2 раза в месяц!

Зарегистрироваться в закрытый клуб легко и удобно прямо в нашем Telegram-боте - t.me/Sskinbot

Становись участником особого сообщества любителей трендовой моды и получай максимум преимуществ от покупок любимого бренда!

Подключайся к нашему сообществу и делай покупки ярче и выгоднее вместе с нами!

Ждем тебя в Клубе Лимитированных Слотов!

---
Трендовые покупки на SSkin.org
Клуб лимитированных слотов SSkin - t.me/Sskinbot';

            case 4:
                return 'Здравствуйте!

Спешим представить вам новинки нашего бренда SSkin, которые недавно пополнили наш ассортимент.

🔥 Новая коллекция уже доступна на нашем сайте - эксклюзивные модели, созданные с вниманием к каждой детали.

В этом сезоне мы сосредоточились на:
- Инновационных материалах, которые сочетают комфорт и стиль
- Уникальных силуэтах, подчеркивающих достоинства фигуры
- Ярких акцентах, которые делают образ запоминающимся

Первым 50 покупателям - особый бонус при заказе из новой коллекции!

С уважением,
Команда SSkin

---
Трендовые покупки на SSkin.org
Клуб лимитированных слотов SSkin - t.me/Sskinbot';

            case 5:
                return 'Специальное предложение только для вас!

Мы ценим ваше внимание к нашему бренду и подготовили эксклюзивное предложение:

🎁 Скидка 20% на всю новую коллекцию по промокоду: SSK20NEW

Промокод действует 3 дня с момента получения этого письма. Не упустите возможность приобрести стильные новинки по специальной цене!

Также напоминаем, что вы всегда можете получить персональную консультацию по подбору гардероба от наших стилистов - просто ответьте на это письмо.

С уважением,
Команда SSkin

---
Трендовые покупки на SSkin.org
Клуб лимитированных слотов SSkin - t.me/Sskinbot';

            case 6:
                return 'Благодарим за внимание к нашему бренду!

Ваше внимание к SSkin очень важно для нас. Мы стремимся создавать не просто одежду, а настоящие произведения искусства, которые подчеркнут вашу индивидуальность.

Надеемся, что информация, которую мы отправили вам, была полезной и интересной. Если у вас остались вопросы или предложения, не стесняйтесь обращаться к нам.

Мы всегда открыты для общения и с радостью поможем вам создать идеальный образ с SSkin.

До новых встреч,
Команда SSkin

---
Трендовые покупки на SSkin.org
Клуб лимитированных слотов SSkin - t.me/Sskinbot';

            default:
                return 'Письмо от SSkin

Благодарим за интерес к нашему бренду. Это автоматическое письмо из серии писем SSkin.

С уважением,
Команда SSkin

---
Трендовые покупки на SSkin.org
Клуб лимитированных слотов SSkin - t.me/Sskinbot';
        }
    }
    
    // Добавление меню в админку
    public function add_admin_menu() {
        add_menu_page(
            'SSkin Email Sequence', 
            'Email Sequence', 
            'manage_options', 
            'sskin-email-sequence', 
            array($this, 'admin_page'), 
            'dashicons-email-alt', 
            30
        );
    }
    
    // Регистрация стилей и скриптов для админки
    public function register_admin_assets($hook) {
        if ($hook != 'toplevel_page_sskin-email-sequence') {
            return;
        }
        
        wp_enqueue_style('sskin-es-admin', SSKIN_ES_PLUGIN_URL . 'assets/css/admin.css', array(), SSKIN_ES_VERSION);
        wp_enqueue_script('sskin-es-admin', SSKIN_ES_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), SSKIN_ES_VERSION, true);
        
        // Передаем данные в JS
        wp_localize_script('sskin-es-admin', 'sskin_es', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sskin_es_nonce'),
        ));
    }
    
    // Страница настроек в админке
    public function admin_page() {
        include SSKIN_ES_PLUGIN_DIR . 'templates/admin-page.php';
    }
    
    // AJAX обработчик сохранения настроек
    public function ajax_save_settings() {
        // Проверка безопасности
        check_ajax_referer('sskin_es_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'У вас нет прав для изменения настроек'));
        }
        
        // Получаем данные
        $settings = isset($_POST['settings']) ? $_POST['settings'] : array();
        
        // Проверка данных
        if (empty($settings)) {
            wp_send_json_error(array('message' => 'Нет данных для сохранения'));
        }
        
        // Получаем текущие настройки
        $emails_array = array();
        
        // Создаем новый массив с настройками писем
        for ($i = 1; $i <= SSKIN_ES_MAX_EMAILS; $i++) {
            $email_key = 'email' . $i;
            $is_active = isset($settings[$email_key . '_active']) && $settings[$email_key . '_active'] === 'true';
            
            $emails_array[$email_key] = array(
                'subject' => isset($settings[$email_key . '_subject']) ? 
                    sanitize_text_field($settings[$email_key . '_subject']) : 
                    (isset($this->options['emails'][$email_key]['subject']) ? $this->options['emails'][$email_key]['subject'] : ''),
                'content' => isset($settings[$email_key . '_content']) ? 
                    wp_kses_post($settings[$email_key . '_content']) : 
                    (isset($this->options['emails'][$email_key]['content']) ? $this->options['emails'][$email_key]['content'] : ''),
                'active' => $is_active
            );
        }
        
        // Создаем массив статусов активности писем
        $email_status = array();
        if (isset($settings['email_status']) && is_array($settings['email_status'])) {
            foreach ($settings['email_status'] as $status) {
                $email_status[] = ($status === 'true');
            }
        } else {
            // Если статусы не переданы, берем из активных писем
            for ($i = 1; $i <= SSKIN_ES_MAX_EMAILS; $i++) {
                $email_key = 'email' . $i;
                $email_status[] = $emails_array[$email_key]['active'];
            }
        }
        
        // Обновляем настройки
        $new_options = array(
            'test_mode' => (isset($settings['test_mode']) && $settings['test_mode'] == 'true') ? true : false,
            'test_intervals' => isset($settings['test_intervals']) ? array_map('sanitize_text_field', $settings['test_intervals']) : $this->options['test_intervals'],
            'normal_intervals' => isset($settings['normal_intervals']) ? array_map('sanitize_text_field', $settings['normal_intervals']) : $this->options['normal_intervals'],
            'email_status' => $email_status,
            'from_name' => isset($settings['from_name']) ? sanitize_text_field($settings['from_name']) : $this->options['from_name'],
            'from_email' => isset($settings['from_email']) ? sanitize_email($settings['from_email']) : $this->options['from_email'],
            'emails' => $emails_array
        );
        
        // Сохраняем настройки
        update_option('sskin_es_options', $new_options);
        $this->options = $new_options;
        
        // Отправляем ответ
        wp_send_json_success(array('message' => 'Настройки успешно сохранены'));
    }
    
    // AJAX обработчик добавления подписчика
    public function ajax_add_subscriber() {
        $this->debug_log('AJAX запрос получен');
        
        // Получаем данные
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        
        // Проверяем email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->debug_log('Невалидный email: ' . $email);
            wp_send_json_error(array('message' => 'Невалидный email'));
            return;
        }
        
        $this->debug_log('Валидный email получен: ' . $email);
        
        // Если имя пустое, используем часть email
        if (empty($name) && !empty($email)) {
            $name = substr($email, 0, strpos($email, '@'));
        }
        
        // Получаем ID подписчика или создаем нового
        $subscriber_id = $this->add_subscriber($email, $name);
        
        if (!$subscriber_id) {
            $this->debug_log('Ошибка при добавлении подписчика');
            wp_send_json_error(array('message' => 'Ошибка при добавлении подписчика'));
            return;
        }
        
        // Планируем отправку писем
        $this->schedule_emails($subscriber_id);
        
        // Отправляем успешный ответ
        wp_send_json_success(array('message' => 'Подписка успешно оформлена'));
    }
    
    // Добавление подписчика в базу данных
    private function add_subscriber($email, $name) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sskin_es_subscribers';
        
        // Проверяем существующего подписчика
        $subscriber_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE email = %s",
            $email
        ));
        
        // Если подписчик не существует, создаем его
        if (!$subscriber_id) {
            $wpdb->insert(
                $table_name,
                array(
                    'email' => $email,
                    'name' => $name
                ),
                array('%s', '%s')
            );
            $subscriber_id = $wpdb->insert_id;
        }
        
        return $subscriber_id;
    }
    
    // Планирование отправки писем
    private function schedule_emails($subscriber_id) {
        $intervals = $this->options['test_mode'] ? $this->options['test_intervals'] : $this->options['normal_intervals'];
        $email_status = isset($this->options['email_status']) ? $this->options['email_status'] : array(true, true, true, false, false, false);
        
        // Отправляем первое письмо сразу, если оно активно
        if ($email_status[0]) {
            $this->debug_log('Отправляем письмо 1 сразу');
            $this->send_email($subscriber_id, 'email1', 'email1_sent');
        }
        
        // Планируем остальные письма
        for ($i = 1; $i < min(count($intervals), SSKIN_ES_MAX_EMAILS); $i++) {
            // Проверяем, активно ли письмо
            if (isset($email_status[$i]) && $email_status[$i]) {
                $email_key = 'email' . ($i + 1);
                $event_name = 'sskin_es_send_email_' . ($i + 1);
                
                if ($this->options['test_mode']) {
                    // Тестовый режим - интервалы в минутах
                    $interval = intval($intervals[$i]) * MINUTE_IN_SECONDS;
                } else {
                    // Обычный режим - интервалы в часах
                    $interval = intval($intervals[$i]) * HOUR_IN_SECONDS;
                }
                
                wp_schedule_single_event(time() + $interval, $event_name, array($subscriber_id));
                $this->debug_log("Запланировано письмо $email_key через " . $intervals[$i] . ($this->options['test_mode'] ? " минут" : " часов"));
            }
        }
        
        $this->debug_log("Запланированы письма для подписчика ID: $subscriber_id");
    }
    
    // Отправка писем (для каждого хука)
    public function send_email_1($subscriber_id) {
        $this->send_email($subscriber_id, 'email1', 'email1_sent');
    }
    
    public function send_email_2($subscriber_id) {
        $this->send_email($subscriber_id, 'email2', 'email2_sent');
    }
    
    public function send_email_3($subscriber_id) {
        $this->send_email($subscriber_id, 'email3', 'email3_sent');
    }
    
    public function send_email_4($subscriber_id) {
        $this->send_email($subscriber_id, 'email4', 'email4_sent');
    }
    
    public function send_email_5($subscriber_id) {
        $this->send_email($subscriber_id, 'email5', 'email5_sent');
    }
    
    public function send_email_6($subscriber_id) {
        $this->send_email($subscriber_id, 'email6', 'email6_sent');
    }
    
    // Общая функция отправки письма
    private function send_email($subscriber_id, $email_key, $status_field) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sskin_es_subscribers';
        
        // Получаем данные подписчика
        $subscriber = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $subscriber_id
        ));
        
        if (!$subscriber) {
            $this->debug_log("Подписчик ID: $subscriber_id не найден");
            return;
        }
        
        // Получаем данные письма
        $subject = $this->options['emails'][$email_key]['subject'];
        $content = $this->options['emails'][$email_key]['content'];
        
        // Заменяем переменные в контенте
        $content = str_replace('{name}', $subscriber->name, $content);
        $content = str_replace('{email}', $subscriber->email, $content);
        
        // Настраиваем заголовки
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->options['from_name'] . ' <' . $this->options['from_email'] . '>'
        );
        
        // Отправляем письмо
        $result = wp_mail($subscriber->email, $subject, nl2br($content), $headers);
        
        if ($result) {
            // Обновляем статус отправки
            $wpdb->update(
                $table_name,
                array($status_field => 1),
                array('id' => $subscriber_id),
                array('%d'),
                array('%d')
            );
            
            $this->debug_log("Отправлено письмо $email_key на {$subscriber->email}");
        } else {
            $this->debug_log("Ошибка отправки письма $email_key на {$subscriber->email}");
        }
    }
    
    // Добавление JavaScript трекера для форм
    public function add_form_tracker() {
        if (is_admin()) return;
        
        include SSKIN_ES_PLUGIN_DIR . 'templates/form-tracker.php';
    }
    
    // Функция для логирования отладочной информации
    private function debug_log($message) {
        $log_file = WP_CONTENT_DIR . '/sskin-es-debug.log';
        file_put_contents($log_file, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
    }
}

// Создаем экземпляр плагина
function sskin_email_sequence() {
    global $sskin_email_sequence;
    
    if (!isset($sskin_email_sequence)) {
        $sskin_email_sequence = new SSkin_Email_Sequence();
    }
    
    return $sskin_email_sequence;
}

// Запускаем плагин
sskin_email_sequence();