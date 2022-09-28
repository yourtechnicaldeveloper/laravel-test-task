<?php

define("DATABASE_DATE_FORMAT", "Y-m-d");
define("DATABASE_TIME_FORMAT", "H:i:s");
define("DATABASE_DATETIME_FORMAT", DATABASE_DATE_FORMAT . " " . DATABASE_TIME_FORMAT);
define("DEFAULT_DATE_FORMAT", "d/m/Y");
define("DEFAULT_TIME_FORMAT", "h:i A");
define("DEFAULT_DATETIME_FORMAT", DEFAULT_DATE_FORMAT . " " . DEFAULT_TIME_FORMAT);
define("DEFAULT_MOMENTJS_DATE_FORMAT", "DD/MM/YYYY");
define("DEFAULT_MOMENTJS_TIME_FORMAT", "hh:mm a");
define("DEFAULT_MOMENTJS_DATETIME_FORMAT", DEFAULT_MOMENTJS_DATE_FORMAT . " " . DEFAULT_MOMENTJS_TIME_FORMAT);
define("DEFAULT_CURRENCY_SYMBOL", '$');
define("DEFAULT_URL_PARAMETER", '!~');

$UploadDirectories = [
    "user_photos"
];

foreach ($UploadDirectories AS $UploadDirectory) {
    define("DIRECTORY_" . strtoupper($UploadDirectory), $UploadDirectory);
}
?>