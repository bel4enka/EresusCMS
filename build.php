MBF/1.0
<?php
/**
 * Procreat Murash 1.0 Project
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

#SET('VERSION', '2.10');

#define('TARGET', '');

create_target('distrib');
copy_files_from('main');
