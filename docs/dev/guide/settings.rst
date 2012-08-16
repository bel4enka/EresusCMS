Диалог настроек
===============

Для создания диалога, позволяющего изменять настройки модуля, надо определить метод ``settings``, который должен иметь следующий вид:

.. code-block:: php

   <?php
   class MyPlugin extends Plugin
   {
     /**
      * Возвращает диалог настроек
      *
      * @return string  HTML
      */
     public function settings()
     {
       $form = array(
         'name' => 'SettingsForm',
         'caption' => $this->title . ' ' . $this->version,
         'width' => '500px',
         'fields' => array (
           array('type' => 'hidden', 'name' => 'update', 'value' => $this->name),
           /* Здесь надо определить нужные вам поля формы */
         ),
        'buttons' => array('ok', 'apply', 'cancel'),
       );
       $result = Eresus_Kernel::app()->getPage()->renderForm($form, $this->settings);
       return $result;
     }


Имена полей формы должны совпадать с ключами массива в свойстве `Plugin::$settings <../../api/Eresus/Plugin.html#$settings>`_ вашего плагина. Тогда система автоматически изменит значения настроек после того, как пользователь нажмёт в диалоге настроек кнопку "Сохранить".

Дополнительные действия при сохранении
--------------------------------------

Если при сохранении пользователем настроек вам надо выполнить какие-либо дополнительные действия, вы можете переопределить метод `Plugin::onSettingsUpdate() <../../api/Eresus/Plugin.html#onSettingsUpdate>`_, например так:

.. code-block:: php

   <?php
   public function onSettingsUpdate()
   {
     if (is_uploaded_file($_FILES['logo']['tmp_name']))
     {
       // загружаем логотип
     }
   }


