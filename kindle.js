function emailArticleKindle(id) {
	try {
		var query = "?op=pluginhandler&plugin=kindle&method=sendEmail&id=" + param_escape(id);
		
    console.log(query);
		
    new Ajax.Request("backend.php",	{
			parameters: query,
			onComplete: function(transport) {
				console.log(transport.responseText);

				var reply = JSON.parse(transport.responseText);

				var error = reply['error'];

				if (error) {
					alert(__('Error sending email:') + ' ' + error);
				} else {
          var message = reply['message'];
					notify_info('The article has been sent to your kindle.' );
				}

			  } 
      });


	} catch (e) {
		exception_error("emailArticleKindle", e);
	}
}

hotkey_actions['send_to_kindle'] = function() {
  if (getActiveArticleId()) {
    emailArticleKindle(getActiveArticleId());
    return;
  }
};
