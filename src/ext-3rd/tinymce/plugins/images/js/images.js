/**
 * Диалог вставки изображения
 *
 */
var ImagesDialog =
{
	/**
	 * URL коннектора
	 */
	connector: "connector/php/",

	/**
	 * Разрешает (true) или запрещает (false) отмену создания директории при потери фокуса полем
	 * ввода имени.
	 */
	folderCreateAlowCancelOnBlur: true,

	/**
	 * Запрет на переключение
	 */
	folderLoadFlag: false,

	/**
	 * Session ID для Flash-загрузчика
	 */
	SID: null,

	/**
	 * Инициализация диалога
	 *
	 * @param {Object} ed
	 * @type void
	 */
	init : function(ed)
	{
		tinyMCEPopup.resizeToInnerSize();

		$('.imageBlock0').live('click', ImagesDialog.selectImage);
		$('.imageBlock0').live('dblclick', ImagesDialog.insert);
		$('.folderClosed,.folderOpened,.folderS,.folderImages,.folderFiles').live('click', ImagesDialog.selectFolder);

		$('#menuCreateFolder').click(ImagesDialog.createFolder);
		$('#menuSaveFolder').click(ImagesDialog.saveNewFolder).hover(
			function(){ ImagesDialog.folderCreateAlowCancelOnBlur = false; },
			function(){ ImagesDialog.folderCreateAlowCancelOnBlur = true; }
		);
		$('#menuCancelFolder').click(ImagesDialog.cancelNewFolder);
		$('#menuDelFolder').click(ImagesDialog.deleteFolder);
		$('#menuDelFiles').click(ImagesDialog.deleteFile);
		$('#menuUploadFiles').click(ImagesDialog.uploadFiles);
		$('#uploadClose').click(ImagesDialog.uploadClose);


		$('.folderClosed,.folderOpened,.folderS,.folderImages,.folderFiles').live('mouseover',function(){
			if(!$(this).hasClass('folderAct')) {
				$(this).addClass('folderHover');
			} else {
				$(this).addClass('folderActHover');
			}
		});
		$('.folderClosed,.folderOpened,.folderS,.folderImages,.folderFiles').live('mouseout',function(){
			if(!$(this).hasClass('folderAct')) {
				$(this).removeClass('folderHover');
			} else {
				$(this).removeClass('folderActHover');
			}
		});


		$('.folderImages,.folderFiles').live('dblclick',function(){
			$(this).next().slideToggle('normal');
		});
		$('.folderOpened,.folderS').live('dblclick',function(){
			if(!$(this).next().hasClass('folderOpenSection')) return false;
			if($(this).hasClass('folderS')) {
				$(this).removeClass('folderS').addClass('folderOpened');
			} else {
				$(this).removeClass('folderOpened').addClass('folderS');
			}
			$(this).next().slideToggle('normal');
		});

		//Нормальная загрузка
		$('#fileOpen').MultiFile({
			STRING: {
				remove:'<img src="img/cross_small.png" width="16" height="16" alt="Убрать" />',
				denied:'Нельзя загружать файлы с расширением $ext!',
				duplicate:'Вы уже добавили файл $file'
			},
			max: 5,
			afterFileSelect: ImagesDialog.checkUploadingFiles,
			afterFileRemove: ImagesDialog.checkUploadingFiles
		});

		/* Каталог папок */
		$.ajax({
			type: "POST",
			url: ImagesDialog.connector,
			data: "action=showtree&default=1",
			success: function(data)
			{
				$('#tree').html(data);
			}
		});

		/* Список файлов */
		$.ajax({
			type: "POST",
			url: ImagesDialog.connector,
			data: "action=showdir&pathtype=images&path=&default=1",
			success: function(data)
			{
				$('#loader').hide();
				$('#mainFiles').html('<div id="files">'+data+'</div>');
				//showFootInfo();
			}
		});

		/* Session ID для Flash-загрузчика */
		$.ajax({
			type: "POST",
			url: ImagesDialog.connector,
			data: "action=SID",
			success: function(data)
			{
				ImagesDialog.SID = data;
			}
		});

		$('#normalSubmit').click(function()
		{
			$('#normalLoader').show();
			$('#filesForm').ajaxSubmit(
			{
				success: function()
				{
					$('#fileOpen_wrap_labels').slideUp(function()
					{
						$('#normalLoader').hide();
						$('#normalSubmit').hide();
						$('#normalResult').show();
						$('#filesHolder').html('<input type="file" id="fileOpen" class="fileOpen" />');
						$('#fileOpen').MultiFile(
						{
							STRING: {
								remove:'<img src="img/cross_small.png" width="16" height="16" alt="Убрать" />',
								denied:'Нельзя загружать файлы с расширением $ext!',
								duplicate:'Вы уже добавили файл $file'
							},
							max: 5,
							afterFileSelect: ImagesDialog.checkUploadingFiles,
							afterFileRemove: ImagesDialog.checkUploadingFiles
						});
					});
				}
			});

			return false;
		});

	},
	//-----------------------------------------------------------------------------

	/**
	 * Вставляет выбранное изображение в документ
	 *
	 * @type void
	 */
	insert : function()
	{
		var selectedImage = $('.imageBlockAct');
		var code = '<img src="$(httpRoot)' + selectedImage.attr('linkto').substr(1) +
			'" width="' + selectedImage.attr('fwidth') +
			'" height="' + selectedImage.attr('fheight') +
			'" alt="' + selectedImage.attr('fname') + '" /> ';
		tinyMCEPopup.execCommand('mceInsertContent', false, code);
		tinyMCEPopup.close();
	},
	//-----------------------------------------------------------------------------

	/**
	 * Получить текущую директорию и ее тип
	 *
	 * @type Array
	 */
	getCurrentPath: function ()
	{
		var path = $('#tree .folderAct').attr('path');

		if (!path) path = '/';

		return {'type': 'images', 'path':path};
	},
	//-----------------------------------------------------------------------------

	/**
	 * Открыть указанную папку
	 *
	 * @param {String}  type
	 * @param {String}  path
	 * @param {Fuction} callback
	 */
	openFolder: function(type, path, callback)
	{
		$.ajax({
			type: "POST",
			url: "connector/php/index.php",
			data: "action=showpath&type="+type+"&path="+path,
			success: function(data)
			{
				$('#addr').html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "connector/php/index.php",
			data: "action=showdir&pathtype="+type+"&path="+path,
			success: function(data)
			{
				$('#loader').hide();
				//$('#files').html(data);
				$('#mainFiles').html('<div id="files">'+data+'</div>');
				//showFootInfo();
				callback();
			}
		});
	},
	//-----------------------------------------------------------------------------

	/**
	 * Обработка одиночного клика по изображению
	 */
	selectImage: function ()
	{
		$('#files table.imageBlockAct').removeClass('imageBlockAct');
		$(this).removeClass('imageBlockHover').addClass('imageBlockAct');
		$('#menuDelFiles').addClass('enabled');

		//showFootInfo();
	},
	//-----------------------------------------------------------------------------

	/**
	 * Выбор директории
	 */
	selectFolder: function ()
	{
		//Запрет на переключение
		if (ImagesDialog.folderLoadFlag)
		{
			return;
		}
		ImagesDialog.folderLoadFlag = true;

		$('#loader').show();
		$('.folderAct').removeClass('folderAct');
		$(this).removeClass('folderHover').addClass('folderAct');
		$('#menuDelFiles').removeClass('enabled');

		ImagesDialog.openFolder($(this).attr('pathtype'), $(this).attr('path'), function()
		{
			ImagesDialog.folderLoadFlag = false;
		});
	},
	//-----------------------------------------------------------------------------

	/**
	 * Создать папку
	 */
	createFolder: function ()
	{
		$('#toolBar div.toolbar-main').hide();
		$('#toolBar div.toolbar-folder').show();

		$('.folderAct').after('<div id="newFolderBlock"><input type="text" name="newfolder" id="newFolder" /></div>');
		$('#newFolderBlock').slideDown('fast', function(){
			$('#newFolderBlock input').focus().blur(ImagesDialog.cancelNewFolder).keypress(function(e){
				if(e.which == 13)
				{
					ImagesDialog.saveNewFolder();
				}
				else if (e.which == 27)
				{
					ImagesDialog.cancelNewFolder();
				}
				else if ((e.which >= 97 && e.which <= 122) || (e.which >= 65 && e.which <= 90) || (e.which >= 48 && e.which <= 57) || e.which == 8 || e.which == 95 || e.which == 45 || e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 16)
				{
					//Значит все верно: a-Z0-9-_ и управление вводом
				}
				else
				{
					return false;
				}

			});
		});

	},
	//-----------------------------------------------------------------------------

	/**
	 * Подтвердить создание папки
	 *
	 */
	saveNewFolder: function ()
	{
		ImagesDialog.folderCreateAlowCancelOnBlur = false;

		if($('#newFolderBlock input').val() == '')
		{
			alert('Введите имя новой папки');
			$('#newFolderBlock input').focus();
			return;
		}

		$('#toolBar div.toolbar-folder').hide();
		$('#toolBar div.toolbar-main').show();
		$('#loader').show();

		//Запрос на создание папки + сервер должен отдать новую структуру каталогов
		var pathtype = $('#tree .folderAct').attr('pathtype');
		var path = $('#tree .folderAct').attr('path');
		var path_new = $('#newFolderBlock input').val();
		var path_will = path + '/' + path_new;
		$.ajax({
			type: "POST",
			url: ImagesDialog.connector,
			data: "action=newfolder&type="+ pathtype +"&path="+ path +"&name=" + path_new,
			success: function(data)
			{
				$('#loader').hide();
				var blocks = eval('('+data+')');
				if (blocks.error != '')
				{
					alert(blocks.error);
					$('#newFolderBlock input').focus();
				}
				else
				{
					$('#tree').html(blocks.tree);
					//$('#addr').html(blocks.addr);
					ImagesDialog.folderCreateAlowCancelOnBlur = true;

					//Открываем созданную папку
					$.ajax({
						type: "POST",
						url: "connector/php/index.php",
						data: "action=showdir&pathtype="+pathtype+"&path="+$('.folderAct').attr('path'),
						success: function(data){
							$('#loader').hide();
							//$('#files').html(data);
							$('#mainFiles').html('<div id="files">'+data+'</div>');
						}
					});
				}
			}
		});
	},
	//-----------------------------------------------------------------------------

	/**
	 * Отменить создание папки
	 *
	 */
	cancelNewFolder: function ()
	{
		if (!ImagesDialog.folderCreateAlowCancelOnBlur)
		{
			ImagesDialog.folderCreateAlowCancelOnBlur = true;
			return;
		}

		$('#toolBar div.toolbar-folder').hide();
		$('#toolBar div.toolbar-main').show();

		$('#newFolderBlock').slideUp('fast', function()
		{
			$(this).remove();
		});
	},
	//-----------------------------------------------------------------------------

	/**
	 * Удаление директории
	 */
	deleteFolder: function()
	{
		var path = ImagesDialog.getCurrentPath();
		if (confirm('Удалить папку "' + path.path + '" ?'))
		{
			$('#loader').show();
			$.ajax({
				type: "POST",
				url: "connector/php/index.php",
				data: "action=delfolder&pathtype="+path.type+"&path="+path.path,
				success: function(data)
				{
					var result = eval('('+data+')');
					if (typeof(result.error) != 'undefined')
					{
						$('#loader').hide();
						alert(result.error);
					}
					else
					{
						//$('#mainFiles').html('<div id="files">'+result.ok+'</div>');
						//showFootInfo();
						$.ajax({
							type: "POST",
							url: "connector/php/index.php",
							data: "action=showtree&path=&type="+path.type,
							success: function(data)
							{
								//$('#loader').hide();
								$('#tree').html(data);
							}
						});
						ImagesDialog.openFolder(path.type, '', function(){ $('#loader').hide(); });
					}
				}
			});
		}
	},
	//-----------------------------------------------------------------------------

	deleteFile: function()
	{
		var file = $('.imageBlockAct');

		if(file.length == 0)
		{
			alert('Выберите файл для удаления.');
		}
		else
		{
			if (confirm('Удалить файл '+file.attr('fname')+'.'+file.attr('ext')+'?'))
			{
				$('#loader').show();
				var path = ImagesDialog.getCurrentPath();
				$.ajax({
					type: "POST",
					url: "connector/php/index.php",
					data: "action=delfile&pathtype="+path.type+"&path="+path.path+"&md5="+file.attr('md5')+"&filename="+file.attr('filename'),
					success: function(data)
					{
						$('#loader').hide();
						//$('#files').html(data);
						if (data != 'error')
						{
							$('#mainFiles').html('<div id="files">'+data+'</div>');
							//showFootInfo();
						}
						else
						{
							alert(data);
						}
					}
				});
			}
		}
	},
	//-----------------------------------------------------------------------------

	/**
	 * Открыть загрузчик файлов
	 */
	uploadFiles: function()
	{
		var path = ImagesDialog.getCurrentPath();
		var str = '';
		if(path.type=='images')
		{
			str = '<span>Изображения:</span>';
		}
		else if(path.type=='files')
		{
			str = '<span>Файлы:</span>';
		}
		str += path.path;
		$('#uploadTarget').html(str);

		$('#normalPathVal').val(path.path);
		$('#normalPathtypeVal').val(path.type);

		$('#upload').show();
	},
	//-----------------------------------------------------------------------------

	/**
	 * Закрыть загрузчик
	 */
	uploadClose: function ()
	{
		$('#loader').show();
		var path = ImagesDialog.getCurrentPath();
		$.ajax({
			type: "POST",
			url: ImagesDialog.connector,
			data: "action=showtree&path="+path.path+"&type="+path.type,
			success: function(data)
			{
				$('#loader').hide();
				$('#tree').html(data);
			}
		});
		ImagesDialog.openFolder(path.type, path.path, function(){ $('#loader').hide(); });

		$('#upload').hide();
		$('#divStatus').html('');
	},
	//-----------------------------------------------------------------------------

	checkUploadingFiles: function ()
	{
		if ($('.fileOpen').length > 1) $('#normalSubmit').show();
		else $('#normalSubmit').hide();

		$('#normalResult').hide();
	}
	//-----------------------------------------------------------------------------

};

tinyMCEPopup.onInit.add(ImagesDialog.init, ImagesDialog);


$(function(){

	//ЗАГРУЗКА
	$('#loader').show();
	//Строка адреса

	/*$.ajax({
		type: "POST",
		url: "connector/php/index.php",
		data: "action=showpath&type=images&path=&default=1",
		success: function(data){
			$('#addr').html(data);
		}
	});*/


/*
	//Адресная строка
	$('.addrItem div,.addrItem img').live('mouseover', function(){
		$(this).parent().animate({backgroundColor:'#b1d3fa'}, 100, 'swing', function(){

		});
	});
	$('.addrItem div,.addrItem img').live('mouseout', function(){
		$(this).parent().animate({backgroundColor:'#e4eaf1'}, 200, 'linear', function(){
			//alert('ck');
			$(this).css({'background-color':'transparent'});
			//alert('ck');
		});
	});
	$('.addrItem div,.addrItem img').live('mousedown', function(){
		$(this).parent().css({'background-color':'#679ad3'});
	});
	$('.addrItem div,.addrItem img').live('mouseup', function(){
		$(this).parent().css({'background-color':'#b1d3fa'});
		$.ajax({
			type: "POST",
			url: "connector/php/index.php",
			data: "action=showtree&path="+$(this).parent().attr('path')+"&type="+$(this).parent().attr('pathtype'),
			success: function(data){
				//$('#loader').hide();
				$('#tree').html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "connector/php/index.php",
			data: "action=showpath&type="+$(this).parent().attr('pathtype')+"&path="+$(this).parent().attr('path'),
			success: function(data){
				$('#addr').html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "connector/php/index.php",
			data: "action=showdir&pathtype="+$(this).parent().attr('pathtype')+"&path="+$(this).parent().attr('path'),
			success: function(data){
				$('#loader').hide();
				//$('#files').html(data);
				$('#mainFiles').html('<div id="files">'+data+'</div>');
				showFootInfo();
			}
		});
	});

	//Кнопка "В начало"
	$('#toBeginBtn').mouseover(function(){
		$(this).children(0).attr('src','img/backActive.gif');
	});
	$('#toBeginBtn').mouseout(function(){
		$(this).children(0).attr('src','img/backEnabled.gif');
	});
*/

	/*
	 * ДЕЙСТВИЯ МЕНЮ
	 */

	//Файлы
	var ctrlState = false;
	$('.imageBlock0').live('mouseover', function(){
		if(!$(this).hasClass('imageBlockAct')) {
			$(this).addClass('imageBlockHover');
		} else {
			$(this).addClass('imageBlockActHover');
		}
	});
	$('.imageBlock0').live('mouseout', function(){
		if(!$(this).hasClass('imageBlockAct')) {
			$(this).removeClass('imageBlockHover');
		} else {
			$(this).removeClass('imageBlockActHover');
		}
	});


	function selectAllFiles() {
		$('.imageBlock0').addClass('imageBlockAct');
		showFootInfo();
	}

	$(this).keydown(function(event){
		if(ctrlState && event.keyCode==65) selectAllFiles();
		if(event.keyCode==17) ctrlState = true;
	});
	$(this).keyup(function(event){
		if(event.keyCode==17) ctrlState = false;
	});
	$(this).blur(function(event){
		ctrlState = false;
	});



	//НИЖНЯЯ ПАНЕЛЬ
	//Показать текущую информацию
	function showFootInfo() {
		$('#fileNameEdit').show();
		$('#fileNameSave').hide();
		var file = $('.imageBlockAct');
		if(file.length > 1) {
			$('#footTableName, #footDateLabel, #footLinkLabel, #footDimLabel, #footDate, #footLink, #footDim').css('visibility','hidden');
			$('#footExt').text('Выбрано файлов: '+file.length);
			var tmpSizeCount = 0;
			$.each(file, function(i, item) {
				tmpSizeCount += parseInt($(this).attr('fsize'));
			});
			$('#footSize').text(intToMb(tmpSizeCount));
		} else if(file.length == 0) {
			$('#footTableName, #footDateLabel, #footLinkLabel, #footDimLabel, #footDate, #footLink, #footDim').css('visibility','hidden');
			var allFiles = $('.imageBlock0');

			$('#footExt').text('Всего файлов: '+allFiles.length);
			var tmpSizeCount = 0;
			$.each(allFiles, function(i, item) {
				tmpSizeCount += parseInt($(this).attr('fsize'));
			});
			$('#footSize').text(intToMb(tmpSizeCount));
		} else {

			$('#fileName').text(file.attr('fname'));
			$('#footExt').text(file.attr('ext'));
			$('#footDate').text(file.attr('date'));
			$('#footLink a').text(file.attr('fname').substr(0,16)).attr('href',file.attr('linkto'));
			$('#footSize').text(intToMb(file.attr('fsize')));
			$('#footDim').text(file.attr('fwidth')+'x'+file.attr('fheight'));

			$('#footTableName, #footDateLabel, #footLinkLabel, #footDimLabel, #footDate, #footLink, #footDim').css('visibility','visible');
		}
	}

	//Очистить поля информации

	//Байты в Мб и Кб
	function intToMb(i) {
		if(i < 1024) {
			return i + ' Б';
		} else if(i < 1048576) {
			var v = i/1024;
			v = parseInt(v*10)/10;
			return v + ' КБ';
		} else {
			var v = i/1048576;
			v = parseInt(v*10)/10;
			return v + ' МБ';
		}
	}
/*
	//Редактировать имя
	$('#fileNameEdit').click(function(){
		$('#fileName').html('<input type="text" name="fileName" id="fileNameValue" value="'+$('#fileName').html()+'" />');
		$('#fileNameValue').focus();
		$('#fileNameEdit').hide();
		$('#fileNameSave').show();
	});
	//Сохранить имя
	$('#fileNameSave').click(function(){
		$('#loader').show();

		//Запрос
		//$('.imageBlockAct').attr('filename')
		var path = getCurrentPath();
		var newname = $('#fileNameValue').val();
		$.ajax({
			type: "POST",
			url: "connector/php/index.php",
			data: 'action=renamefile&path='+path.path+'&pathtype='+path.type+'&filename='+$('.imageBlockAct').attr('filename')+'&newname='+newname,
			success: function(data){
				$('#loader').hide();
				if(data != 'error') {
					$('#fileName').html(newname);
					$('.imageBlockAct').attr('fname', newname);
					$('.imageBlockAct .imageName').text(newname);
				} else {
					alert(data);
				}
			}
		});

		$('#fileNameSave').hide();
		$('#fileNameEdit').show();
	});

*/
	//Меню загрузчика
	$('#uploadMenu a').click(function(){
		$('#uploadMenu a').removeClass('act');
		$(this).addClass('act');

		if($(this).attr('id') == 'uploadAreaNormalControl') {
			$('#uploadAreaNormal').show();
			$('#uploadAreaMulti').hide();
		} else if($(this).attr('id') == 'uploadAreaMultiControl') {
			$('#uploadAreaNormal').hide();
			$('#uploadAreaMulti').show();
		}

		return false;
	});


/*
	//SWFUpload загрузка
	swfu = new SWFUpload({
		flash_url : "js/swfupload/swfupload.swf",
		upload_url: "connector/php/index.php",	// Relative to the SWF file
		post_params: {
			//"PHPSESSID" : "NONE",
			"action" : "uploadfile"
		},
		file_size_limit : "100 MB",
		file_types : "*.*",
		file_types_description : "Все файлы",
		file_upload_limit : 20,
		file_queue_limit : 0,
		custom_settings : {
			progressTarget : "fsUploadProgress",
			cancelButtonId : "btnCancel"
		},
		debug: false,

		// Button Settings
		button_placeholder_id : "spanButtonPlaceholder",
		button_width: 70,
		button_height: 24,
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,

		// The event handler functions are defined in handlers.js
		swfupload_loaded_handler : function() {
			var self = this;
			clearTimeout(this.customSettings.loadingTimeout);
			document.getElementById("divLoadingContent").style.display = "none";
			document.getElementById("divLongLoading").style.display = "none";
			document.getElementById("divAlternateContent").style.display = "none";
			document.getElementById("btnCancel").onclick = function () { self.cancelQueue(); };

			var path = getCurrentPath();
			this.addPostParam('path', path.path);
			this.addPostParam('pathtype', path.type);
			this.addPostParam('SID', SID);
			//alert(SID);
		},
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		queue_complete_handler : queueComplete,	// Queue plugin event

		// SWFObject settings
		minimum_flash_version : "9.0.28",
		swfupload_pre_load_handler : swfUploadPreLoad,
		swfupload_load_failed_handler : swfUploadLoadFailed
	});
	*/
});
