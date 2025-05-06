<script type="text/javascript">
jQuery(document).ready(function($) {
    console.log('SSkin Email Sequence: Tracker initialized');
    
    // Функция для обработки данных формы
    function processFormData(form) {
        var email = '';
        var name = '';
        
        // Ищем email и имя в форме
        form.find('input').each(function() {
            var input = $(this);
            if (input.attr('type') === 'email' || (input.attr('name') && input.attr('name').indexOf('email') !== -1)) {
                email = input.val();
            }
            if (input.attr('name') && input.attr('name').indexOf('name') !== -1) {
                name = input.val();
            }
        });
        
        if (!email) {
            console.log('SSkin Email Sequence: Email не найден в форме');
            return;
        }
        
        console.log('SSkin Email Sequence: Найден email ' + email);
        
        // Отправляем данные через AJAX для регистрации подписчика
        setTimeout(function() {
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'sskin_es_sequence',
                email: email,
                name: name
            }, function(response) {
                console.log('SSkin Email Sequence: Подписка обработана');
            });
        }, 1500);
    }
    
    // Метод 1: Наблюдаем за успешной отправкой форм (предпочтительный метод)
    $(document).on('submit_success', '.elementor-form', function(e, response) {
        console.log('SSkin Email Sequence: Форма успешно отправлена через событие success');
        processFormData($(this));
    });
    
    // Метод 2: Используем другой метод отслеживания отправки (без блокировки отправки)
    $('.elementor-form .elementor-button').on('click', function() {
        try {
            var form = $(this).closest('form');
            if (!form.length) return true;
            
            // Используем setTimeout чтобы не мешать штатной отправке формы
            setTimeout(function() {
                processFormData(form);
            }, 500);
            
            // Не блокируем отправку формы
            return true;
        } catch(e) {
            console.error('SSkin Email Sequence Error: ' + e.message);
            // Не блокируем отправку формы даже при ошибке
            return true;
        }
    });
    
    // Метод 3: Дополнительный метод для нестандартных форм - отслеживание через элементы формы
    $('.elementor-field-type-submit .elementor-button').on('click', function() {
        try {
            var form = $(this).closest('form');
            if (!form.length) return true;
            
            setTimeout(function() {
                processFormData(form);
            }, 700);
            
            return true;
        } catch(e) {
            console.error('SSkin Email Sequence: Ошибка в методе 3 - ' + e.message);
            return true;
        }
    });
});
</script>