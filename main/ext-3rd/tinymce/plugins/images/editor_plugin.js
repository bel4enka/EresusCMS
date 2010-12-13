/**
 *
 * @copyright 2008-2009, Antonov A Andrey, All rights reserved.
 * @author Antonov Andrey http://dustweb.ru/
 * @author Mikhail Krasilnikov <mk@3wstyle.ru
 */

/**
 *
 */
(function()
{
	tinymce.PluginManager.requireLangPack('images');

	tinymce.create('tinymce.plugins.ImagesPlugin', {

		/**
		 * Инициализирует плагин
		 *
		 * @param {Object} ed  Редактор
		 * @param {String} url
		 * @returns ???
		 */
		init : function(ed, url)
		{
			// Register commands
			ed.addCommand('mceImages', function()
			{
				ed.windowManager.open(
					{
						file : url + '/images.htm',
						width : 700 + parseInt(ed.getLang('images.delta_width', 0)),
						height : 500 + parseInt(ed.getLang('images.delta_height', 0)),
						inline: true
					},
					{
						plugin_url : url
					}
				);
			});

			// Register buttons
			ed.addButton('images',
			{
				title : 'images.images_desc',
				cmd : 'mceImages',
				image : url + '/img/icon.gif'
			});
		},

		/**
		 *
		 * @returns Служебная информация
		 */
		getInfo : function()
		{
			return {
				longname : 'Images Manager',
				author : 'Antonov Andrey',
				authorurl : 'http://dustweb.ru',
				infourl : 'http://dustweb.ru/log/projects/tinymce_images/',
				version : '1.1 beta 2'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('images', tinymce.plugins.ImagesPlugin);
})();