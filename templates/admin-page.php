<div class="wrap">
    <h1>SSkin Email Sequence</h1>
    <p>Настройте последовательность писем, которые будут отправляться после заполнения формы Elementor.</p>
    
    <div id="sskin-es-settings-container">
        <form id="sskin-es-settings-form">
            <div class="sskin-es-settings-section">
                <h2>Основные настройки</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">Тестовый режим</th>
                        <td>
                            <label>
                                <input type="checkbox" name="test_mode" value="1" <?php checked(true, $this->options['test_mode']); ?>>
                                Включить тестовый режим (быстрая отправка писем для тестирования)
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Имя отправителя</th>
                        <td>
                            <input type="text" name="from_name" value="<?php echo esc_attr($this->options['from_name']); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Email отправителя</th>
                        <td>
                            <input type="email" name="from_email" value="<?php echo esc_attr($this->options['from_email']); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="sskin-es-settings-section">
                <h2>Настройка последовательности писем</h2>
                
                <div class="email-sequence-tabs">
                    <!-- Вкладки для переключения между режимами -->
                    <ul class="nav-tab-wrapper">
                        <li><a href="#tab-emails" class="nav-tab nav-tab-active">Содержание писем</a></li>
                        <li><a href="#tab-test-intervals" class="nav-tab">Тестовые интервалы</a></li>
                        <li><a href="#tab-normal-intervals" class="nav-tab">Обычные интервалы</a></li>
                    </ul>
                
                    <!-- Содержимое вкладки с письмами -->
                    <div id="tab-emails" class="tab-content active">
                        <?php for ($i = 1; $i <= SSKIN_ES_MAX_EMAILS; $i++) : 
                            $email_key = 'email' . $i;
                            $is_active = isset($this->options['emails'][$email_key]['active']) ? 
                                $this->options['emails'][$email_key]['active'] : 
                                ($i <= 3 ? true : false);
                        ?>
                        <div class="email-item">
                            <div class="email-header">
                                <h3>
                                    <label>
                                        <input type="checkbox" name="email_active[]" value="<?php echo $i; ?>" <?php checked(true, $is_active); ?>>
                                        Письмо <?php echo $i; ?>
                                    </label>
                                </h3>
                                <div class="email-toggle"></div>
                            </div>
                            <div class="email-content" <?php echo $is_active ? '' : 'style="display:none;"' ?>>
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Тема письма</th>
                                        <td>
                                            <input type="text" name="<?php echo $email_key; ?>_subject" 
                                                value="<?php echo esc_attr($this->options['emails'][$email_key]['subject']); ?>" 
                                                class="regular-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Содержание письма</th>
                                        <td>
                                            <textarea name="<?php echo $email_key; ?>_content" rows="8" 
                                                class="large-text"><?php echo esc_textarea($this->options['emails'][$email_key]['content']); ?></textarea>
                                            <p class="description">Переменные: {name} и {email} для персонализации</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Содержимое вкладки с тестовыми интервалами -->
                    <div id="tab-test-intervals" class="tab-content">
                        <h3>Интервалы отправки в тестовом режиме (в минутах)</h3>
                        <p class="description">Письмо 1 всегда отправляется сразу после заполнения формы.</p>
                        
                        <table class="widefat intervals-table">
                            <thead>
                                <tr>
                                    <th>Письмо</th>
                                    <th>Интервал (минуты)</th>
                                    <th>Активно</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < SSKIN_ES_MAX_EMAILS; $i++) : 
                                    $is_active = isset($this->options['emails']['email'.($i+1)]['active']) ? 
                                        $this->options['emails']['email'.($i+1)]['active'] : 
                                        ($i < 3 ? true : false);
                                    $value = isset($this->options['test_intervals'][$i]) ? 
                                        esc_attr($this->options['test_intervals'][$i]) : 
                                        ($i + 1);
                                ?>
                                <tr>
                                    <td>Письмо <?php echo $i+1; ?></td>
                                    <td>
                                        <input type="number" name="test_intervals[]" value="<?php echo $value; ?>" 
                                            min="<?php echo $i == 0 ? 0 : 1; ?>" max="60" class="small-text">
                                        <?php if ($i == 0) : ?>
                                            <span class="description">Отправляется сразу</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-indicator <?php echo $is_active ? 'active' : 'inactive'; ?>">
                                            <?php echo $is_active ? 'Активно' : 'Отключено'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Содержимое вкладки с обычными интервалами -->
                    <div id="tab-normal-intervals" class="tab-content">
                        <h3>Интервалы отправки в обычном режиме (в часах)</h3>
                        <p class="description">Письмо 1 всегда отправляется сразу после заполнения формы.</p>
                        
                        <table class="widefat intervals-table">
                            <thead>
                                <tr>
                                    <th>Письмо</th>
                                    <th>Интервал (часы)</th>
                                    <th>Активно</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < SSKIN_ES_MAX_EMAILS; $i++) : 
                                    $is_active = isset($this->options['emails']['email'.($i+1)]['active']) ? 
                                        $this->options['emails']['email'.($i+1)]['active'] : 
                                        ($i < 3 ? true : false);
                                    $value = isset($this->options['normal_intervals'][$i]) ? 
                                        esc_attr($this->options['normal_intervals'][$i]) : 
                                        ($i == 0 ? 24 : ($i == 1 ? 48 : ($i == 2 ? 72 : 24 * ($i + 1))));
                                ?>
                                <tr>
                                    <td>Письмо <?php echo $i+1; ?></td>
                                    <td>
                                        <input type="number" name="normal_intervals[]" value="<?php echo $value; ?>" 
                                            min="<?php echo $i == 0 ? 0 : 1; ?>" max="240" class="small-text">
                                        <?php if ($i == 0) : ?>
                                            <span class="description">Отправляется сразу</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-indicator <?php echo $is_active ? 'active' : 'inactive'; ?>">
                                            <?php echo $is_active ? 'Активно' : 'Отключено'; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <p class="submit">
                <button type="submit" class="button button-primary" id="sskin-es-save-settings">Сохранить настройки</button>
                <span id="sskin-es-save-result" style="margin-left: 15px; display: none;"></span>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Обработчик переключения вкладок
    $('.nav-tab-wrapper a').click(function(e) {
        e.preventDefault();
        
        // Активируем вкладку
        $('.nav-tab-wrapper a').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Показываем соответствующий контент
        var tabId = $(this).attr('href');
        $('.tab-content').hide();
        $(tabId).show();
    });
    
    // По умолчанию показываем первую вкладку
    $('.tab-content').hide();
    $('.tab-content:first').show();
    $('.nav-tab-wrapper a:first').addClass('nav-tab-active');
    
    // Обработчик раскрытия/скрытия настроек письма
    $('.email-header').on('click', function() {
        $(this).next('.email-content').slideToggle(200);
        $(this).find('.email-toggle').toggleClass('open');
    });
    
    // Обработчик изменения статуса письма
    $('input[name="email_active[]"]').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.email-item').find('.email-content').slideDown(200);
        } else {
            $(this).closest('.email-item').find('.email-content').slideUp(200);
        }
    });
});
</script>