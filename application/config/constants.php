<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
 * LOG_THRESHOLD VALUES:
 *   0 - OFF       No logging
 *   1 - ERRORS    PHP errors only
 *   2 - DEBUG     Errors + debug messages
 *   3 - INFO      Errors + debug + info
 *   4 - ALL       Everything
 */
define('LOG_THRESHOLD_OFF', 0);
define('LOG_THRESHOLD_ERROR', 1);
define('LOG_THRESHOLD_DEBUG', 2);
define('LOG_THRESHOLD_INFO', 3);
define('LOG_THRESHOLD_ALL', 4);

/*
|--------------------------------------------------------------------------
| Todo Constants
|--------------------------------------------------------------------------
|
| Constants for the Todo application - replaces all magic numbers/strings.
| Used across Model, Service, and Controller layers.
|
*/

// Todo Status (is_completed column)
define('TODO_STATUS_PENDING',    0);
define('TODO_STATUS_COMPLETED',  1);

// Todo Archive (is_archived column)
define('TODO_ACTIVE',    0);
define('TODO_ARCHIVED',  1);

// Todo Priority (ENUM values)
define('TODO_PRIORITY_LOW',    'low');
define('TODO_PRIORITY_MEDIUM', 'medium');
define('TODO_PRIORITY_HIGH',   'high');

// Todo Validation Limits
define('TODO_MIN_TITLE_LENGTH', 3);
define('TODO_MAX_TITLE_LENGTH', 255);

// Error indicators
define('TODO_TOGGLE_ERROR', -1);

// Pagination
define('TODO_DEFAULT_PER_PAGE', 10);

// Fetch columns
define('TODO_FETCH_COLUMNS', 'id, title, description, priority, due_date, is_completed, is_archived, created_at, updated_at');


/* End of file constants.php */
/* Location: ./application/config/constants.php */