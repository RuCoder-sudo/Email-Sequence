jQuery(document).ready(function($) {
    
    // Обработка отправки формы настроек
    $('#sskin-es-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitButton = $('#sskin-es-save-settings');
        var $result = $('#sskin-es-save-result');
        
        // Блокируем кнопку во время отправки
        $submitButton.prop('disabled', true);
        $result.text('Сохранение...').removeClass('success error').show();
        
        // Собираем данные формы
        var settings = {
            test_mode: $('input[name="test_mode"]').is(':checked') ? 'true' : 'false',
            from_name: $('input[name="from_name"]').val(),
            from_email: $('input[name="from_email"]').val(),
            test_intervals: [],
            normal_intervals: [],
            email_status: []
        };
        
        // Собираем статусы активности писем
        $('input[name="email_active[]"]').each(function() {
            settings.email_status.push($(this).is(':checked') ? 'true' : 'false');
        });
        
        // Собираем данные писем (6 писем)
        for (var i = 1; i <= 6; i++) {
            var emailKey = 'email' + i;
            settings[emailKey + '_subject'] = $('input[name="' + emailKey + '_subject"]').val();
            settings[emailKey + '_content'] = $('textarea[name="' + emailKey + '_content"]').val();
            settings[emailKey + '_active'] = $('input[name="email_active[]"][value="' + i + '"]').is(':checked') ? 'true' : 'false';
        }
        
        // Собираем интервалы для тестового режима
        $('input[name="test_intervals[]"]').each(function() {
            settings.test_intervals.push($(this).val());
        });
        
        // Собираем интервалы для обычного режима
        $('input[name="normal_intervals[]"]').each(function() {
            settings.normal_intervals.push($(this).val());
        });
        
        // Отправляем данные на сервер
        $.ajax({
            url: sskin_es.ajax_url,
            type: 'POST',
            data: {
                action: 'sskin_es_save_settings',
                settings: settings,
                nonce: sskin_es.nonce
            },
            success: function(response) {
                if (response.success) {
                    $result.text(response.data.message).addClass('success');
                    setTimeout(function() {
                        $result.fadeOut(500);
                    }, 3000);
                } else {
                    $result.text(response.data.message).addClass('error');
                }
            },
            error: function() {
                $result.text('Ошибка соединения с сервером').addClass('error');
            },
            complete: function() {
                $submitButton.prop('disabled', false);
            }
        });
    });
});