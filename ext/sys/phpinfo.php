<?
  session_name('sid');
  session_start();
  session_register('user'); 
  if ($user['auth'] && ($user['access'] <= 2)) phpinfo(); else echo "YOU NOT AUTHORISED!";
?>