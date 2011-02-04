/**
 * jQuery.FormNavigate.js
 * jQuery Form onChange Navigate Confirmation plugin
 *
 * Browser compatibility: IE 6-8, Firefox 2.0+, Safari 3+, Opera 9-11. Chrome 1+;
 *
 * @author Law Ding Yong
 * @author Егор Дубровский, <spirtz[at]gmail[dot]com>
 * @author Вячеслав Редькин, http://redk.in/
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * @copyright 2009, Law Ding Yong, http://code.google.com/p/jquery-plugin-form-navigation-confirmation/
 *
 * @license Licensed under the MIT license, http://www.opensource.org/licenses/mit-license.php
 *
 * $Id$
 */

if (history && history.navigationMode)
{
	history.navigationMode = 'compatible';
}

jQuery.fn.extend(
{
	FormNavigate: function(o)
	{
		var formdata_original = true;
		jQuery(window).bind('beforeunload',
			function ()
			{
				if (!formdata_original)
				{
					return settings.message;
				}
			}
		);

		var def = {
			message: '',
			aOutConfirm: 'a:not([target!=_blank])'
		};

		var settings = jQuery.extend(false, def, o);

		if (o.aOutConfirm && o.aOutConfirm != def.aOutConfirm)
		{
			jQuery('a').addClass('aOutConfirmPlugin');
			jQuery(settings.aOutConfirm).removeClass("aOutConfirmPlugin");
			jQuery(settings.aOutConfirm).click(
				function()
				{
					formdata_original = true;
					return true;
				}
			);
		}

		jQuery("a.aOutConfirmPlugin").click(
			function()
			{
				if (formdata_original == false)
				{
					if(confirm(settings.message))
					{
						formdata_original = true;
					}
				}
				return formdata_original;
			}
		);

		jQuery(this).find("input[type=text], textarea, input[type='password'], input[type='radio'], input[type='checkbox'], input[type='file'], select").live('change keypress',
			function(event)
			{
				formdata_original = false;
			}
		);

		jQuery(this).find(":submit, button[type='image'], input[type='image'], button[type='reset'], input[type='reset']").click(
			function()
			{
				formdata_original = true;
			}
		);
	}
});