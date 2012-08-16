Отправка почты
==============

Начиная с версии 2.12 в Eresus CMS доступен компонент Mail из состава eZ Components (`Официальная документация, eng <http://ezcomponents.org/docs/tutorials/Mail>`_).

Пример отправки письма:

.. code-block:: php

   <?php
   // Создаём объект-составитель письма
   $mail = new ezcMailComposer();
   // Задаём отправителя письма
   $mail->from = new ezcMailAddress(option('mailFromAddr'), option('mailFromName'), 'utf-8');
   // Добавляем получателя
   $mail->addTo(new ezcMailAddress('pupkin@example.org', 'Васе Пупкину', 'utf-8'));
   // Указываем тему
   $mail->subject = 'Извещение';
   // ... и кодировку темы
   $mail->subjectCharset = 'utf-8';
   // Указываем кодировку тела письма
   $mail->charset = 'utf-8';
   // Пишем письмо
   $mail->htmlText = '<html> ... текст письма  ... </html>';
   // Собираем письмо
   $mail->build();

   // Выбираем транспорт - стандартный: mail
   $transport = new ezcMailMtaTransport();
   // Отправляем письмо
   $transport->send($mail);


Статьи
------

* `Apache Zeta Components: Doing mail right <http://qafoo.com/blog/011_apache_zeta_components_doing_mail_right.html>`_
