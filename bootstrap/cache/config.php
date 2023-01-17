<?php return array (
  'app' => 
  array (
    'name' => 'Laravel',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost',
    'asset_url' => NULL,
    'timezone' => 'Asia/Tehran',
    'locale' => 'fa',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => 'base64:4MHwvSp6F9xNmDEMHnZR+kiQPmPpPZRYxkkKb9oLuBk=',
    'cipher' => 'AES-256-CBC',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'Spatie\\Permission\\PermissionServiceProvider',
      23 => 'App\\Providers\\AppServiceProvider',
      24 => 'App\\Providers\\AuthServiceProvider',
      25 => 'App\\Providers\\EventServiceProvider',
      26 => 'App\\Providers\\RouteServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Arr' => 'Illuminate\\Support\\Arr',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Http' => 'Illuminate\\Support\\Facades\\Http',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'Str' => 'Illuminate\\Support\\Str',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'web',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'token',
        'provider' => 'users',
        'hash' => false,
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'App\\Models\\Admin',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
        'throttle' => 60,
      ),
    ),
    'password_timeout' => 10800,
  ),
  'broadcasting' => 
  array (
    'default' => 'redis',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
          'cluster' => 'mt1',
          'useTLS' => true,
        ),
      ),
      'ably' => 
      array (
        'driver' => 'ably',
        'key' => NULL,
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'none',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
        'serialize' => false,
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
        'lock_connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/home/hasan/Documents/Projects/poolex/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
      ),
      'dynamodb' => 
      array (
        'driver' => 'dynamodb',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'table' => 'cache',
        'endpoint' => NULL,
      ),
      'none' => 
      array (
        'driver' => 'null',
      ),
    ),
    'prefix' => 'laravel_cache',
  ),
  'cors' => 
  array (
    'paths' => 
    array (
      0 => 'api/*',
      1 => 'sanctum/csrf-cookie',
    ),
    'allowed_methods' => 
    array (
      0 => '*',
    ),
    'allowed_origins' => 
    array (
      0 => '*',
    ),
    'allowed_origins_patterns' => 
    array (
    ),
    'allowed_headers' => 
    array (
      0 => '*',
    ),
    'exposed_headers' => 
    array (
    ),
    'max_age' => 0,
    'supports_credentials' => false,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'url' => NULL,
        'database' => 'polex',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'polex',
        'username' => 'root',
        'password' => '',
        'unix_socket' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
        'options' => 
        array (
        ),
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'polex',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'url' => NULL,
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'polex',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'options' => 
      array (
        'cluster' => 'redis',
        'prefix' => 'laravel_database_',
      ),
      'default' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '0',
      ),
      'cache' => 
      array (
        'url' => NULL,
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => '1',
      ),
    ),
  ),
  'dompdf' => 
  array (
    'show_warnings' => false,
    'orientation' => 'portrait',
    'defines' => 
    array (
      'font_dir' => '/home/hasan/Documents/Projects/poolex/storage/fonts/',
      'font_cache' => '/home/hasan/Documents/Projects/poolex/storage/fonts/',
      'temp_dir' => '/tmp',
      'chroot' => '/home/hasan/Documents/Projects/poolex',
      'enable_font_subsetting' => false,
      'pdf_backend' => 'CPDF',
      'default_media_type' => 'screen',
      'default_paper_size' => 'a4',
      'default_font' => 'serif',
      'dpi' => 96,
      'enable_php' => false,
      'enable_javascript' => true,
      'enable_remote' => true,
      'font_height_ratio' => 1.1,
      'enable_html5_parser' => false,
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/home/hasan/Documents/Projects/poolex/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/home/hasan/Documents/Projects/poolex/storage/app/public',
        'url' => 'http://localhost/storage',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
        'bucket' => '',
        'url' => NULL,
        'endpoint' => NULL,
      ),
      'ftp' => 
      array (
        'driver' => 'ftp',
        'host' => '171.22.24.62',
        'username' => 'pz13373',
        'password' => 'hI4hJI1f',
        'passive' => 'false',
        'ignorePassiveAddress' => true,
        'root' => '/public_html',
      ),
    ),
    'links' => 
    array (
      '/home/hasan/Documents/Projects/poolex/public/storage' => '/home/hasan/Documents/Projects/poolex/storage/app/public',
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'single',
        ),
        'ignore_exceptions' => false,
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/home/hasan/Documents/Projects/poolex/storage/logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/home/hasan/Documents/Projects/poolex/storage/logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'debug',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
      'null' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\NullHandler',
      ),
      'emergency' => 
      array (
        'path' => '/home/hasan/Documents/Projects/poolex/storage/logs/laravel.log',
      ),
    ),
  ),
  'mail' => 
  array (
    'default' => 'smtp',
    'mailers' => 
    array (
      'smtp' => 
      array (
        'transport' => 'smtp',
        'host' => 'mail.polexofficial.com',
        'port' => '587',
        'encryption' => NULL,
        'username' => '_mainaccount@polexofficial.com',
        'password' => 'FWA0lCd4z+4:2c',
        'timeout' => NULL,
        'auth_mode' => NULL,
      ),
      'ses' => 
      array (
        'transport' => 'ses',
      ),
      'mailgun' => 
      array (
        'transport' => 'mailgun',
      ),
      'postmark' => 
      array (
        'transport' => 'postmark',
      ),
      'sendmail' => 
      array (
        'transport' => 'sendmail',
        'path' => '/usr/sbin/sendmail -bs',
      ),
      'log' => 
      array (
        'transport' => 'log',
        'channel' => NULL,
      ),
      'array' => 
      array (
        'transport' => 'array',
      ),
    ),
    'from' => 
    array (
      'address' => '_mainaccount@polexofficial.com',
      'name' => 'Laravel',
    ),
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/home/hasan/Documents/Projects/poolex/resources/views/vendor/mail',
      ),
    ),
  ),
  'pdf' => 
  array (
    'mode' => 'utf-8',
    'format' => 'A4',
    'default_font_size' => '12',
    'default_font' => 'IRANSans',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_header' => 0,
    'margin_footer' => 0,
    'orientation' => 'P',
    'title' => 'Laravel mPDF',
    'author' => '',
    'watermark' => '',
    'show_watermark' => false,
    'watermark_font' => 'sans-serif',
    'display_mode' => 'fullpage',
    'watermark_text_alpha' => 0.1,
    'custom_font_dir' => '/home/hasan/Documents/Projects/poolex/resources/fonts/',
    'custom_font_data' => 
    array (
      'IRANSans' => 
      array (
        'R' => 'IRANSansWeb.ttf',
        'useOTL' => 255,
        'useKashida' => 75,
      ),
      'IranNastaliq' => 
      array (
        'R' => 'IranNastaliq.ttf',
      ),
    ),
    'auto_language_detection' => false,
    'temp_dir' => '/tmp',
    'pdfa' => false,
    'pdfaauto' => false,
  ),
  'permission' => 
  array (
    'models' => 
    array (
      'permission' => 'Spatie\\Permission\\Models\\Permission',
      'role' => 'Spatie\\Permission\\Models\\Role',
    ),
    'table_names' => 
    array (
      'roles' => 'roles',
      'permissions' => 'permissions',
      'model_has_permissions' => 'model_has_permissions',
      'model_has_roles' => 'model_has_roles',
      'role_has_permissions' => 'role_has_permissions',
    ),
    'column_names' => 
    array (
      'model_morph_key' => 'model_id',
    ),
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,
    'cache' => 
    array (
      'expiration_time' => 
      DateInterval::__set_state(array(
         'y' => 0,
         'm' => 0,
         'd' => 0,
         'h' => 24,
         'i' => 0,
         's' => 0,
         'f' => 0.0,
         'weekday' => 0,
         'weekday_behavior' => 0,
         'first_last_day_of' => 0,
         'invert' => 0,
         'days' => false,
         'special_type' => 0,
         'special_amount' => 0,
         'have_weekday_relative' => 0,
         'have_special_relative' => 0,
      )),
      'key' => 'spatie.permission.cache',
      'model_key' => 'name',
      'store' => 'default',
    ),
  ),
  'prsIcon' => 
  array (
    'persianIcon' => 
    array (
      'BTCUSDT' => 'بیت کوین',
      'ETHUSDT' => 'اتریوم',
      'BNBUSDT' => 'بایننس کوین',
      'BCCUSDT' => '',
      'NEOUSDT' => 'نئو',
      'LTCUSDT' => 'لایت کوین',
      'QTUMUSDT' => 'کوانتوم',
      'ADAUSDT' => 'کاردانو',
      'XRPUSDT' => 'ریپل',
      'EOSUSDT' => 'ایاس',
      'TUSDUSDT' => 'ترو یو اس دی',
      'IOTAUSDT' => 'آیوتا',
      'XLMUSDT' => 'استلار',
      'ONTUSDT' => 'آنتولوژی',
      'TRXUSDT' => 'ترون',
      'ETCUSDT' => 'اتریوم کلایک',
      'ICXUSDT' => 'آیکون',
      'VENUSDT' => '',
      'NULSUSDT' => 'نالز',
      'VETUSDT' => 'وی چین',
      'PAXUSDT' => 'پکسوز',
      'BCHABCUSDT' => '22',
      'BCHSVUSDT' => '23',
      'USDCUSDT' => 'یو اس دی کوین',
      'LINKUSDT' => 'چین لینک',
      'WAVESUSDT' => 'ویوز',
      'BTTUSDT' => 'بیت تورنت',
      'USDSUSDT' => 'استیبل',
      'ONGUSDT' => 'آنتولوژی گز',
      'HOTUSDT' => 'هولو',
      'ZILUSDT' => 'زیلیکا',
      'ZRXUSDT' => 'زیرو اکس',
      'FETUSDT' => 'فچ',
      'BATUSDT' => 'بیسیک اتنشن',
      'XMRUSDT' => 'مونرو',
      'ZECUSDT' => 'زی کش',
      'IOSTUSDT' => 'آی او اس تی',
      'CELRUSDT' => 'سلر نتورک',
      'DASHUSDT' => 'دش',
      'NANOUSDT' => 'نانو',
      'OMGUSDT' => 'اومیسی گو',
      'THETAUSDT' => 'تتا',
      'ENJUSDT' => 'انجین کوین',
      'MITHUSDT' => 'میتریل',
      'MATICUSDT' => 'متیک',
      'ATOMUSDT' => 'کاسموس',
      'TFUELUSDT' => 'تتا فیول',
      'ONEUSDT' => 'هارمونی',
      'FTMUSDT' => 'فانتوم',
      'ALGOUSDT' => 'الگوراند',
      'USDSBUSDT' => 'استیبل',
      'GTOUSDT' => 'گیفتو',
      'ERDUSDT' => 'الراند',
      'DOGEUSDT' => 'دوج کوین',
      'DUSKUSDT' => 'داسک',
      'ANKRUSDT' => 'انکر نتورک',
      'WINUSDT' => 'وینک',
      'COSUSDT' => 'کانتنت اواس',
      'NPXSUSDT' => '',
      'COCOSUSDT' => 'کوکوس',
      'MTLUSDT' => 'متال',
      'TOMOUSDT' => 'توموچین',
      'PERLUSDT' => 'پرلین',
      'DENTUSDT' => 'دنت',
      'MFTUSDT' => 'مین فریم',
      'KEYUSDT' => 'سلف کی',
      'STORMUSDT' => 'استورم',
      'DOCKUSDT' => 'داک',
      'WANUSDT' => 'ون چین',
      'FUNUSDT' => 'فان فیر',
      'CVCUSDT' => 'سیویک',
      'CHZUSDT' => 'چیلیر',
      'BANDUSDT' => 'باند پروتکل',
      'BUSDUSDT' => 'بایننس یو اس دی',
      'BEAMUSDT' => 'بیم',
      'XTZUSDT' => 'تزوس',
      'RENUSDT' => 'ریپابلبک توکن',
      'RVNUSDT' => 'ریون کوین',
      'HCUSDT' => 'هایپر کش',
      'HBARUSDT' => 'هدرا هشگراف',
      'NKNUSDT' => 'ان کی ان',
      'STXUSDT' => 'بلک استک',
      'KAVAUSDT' => 'کاوا',
      'ARPAUSDT' => 'آرپاچین',
      'IOTXUSDT' => 'آیوتکس',
      'RLCUSDT' => 'آی اکسز آر ال سی',
      'MCOUSDT' => 'ام سی او',
      'CTXCUSDT' => 'کورتکس',
      'BCHUSDT' => 'بیت کوین کش',
      'TROYUSDT' => 'تروی',
      'VITEUSDT' => 'وایت',
      'FTTUSDT' => 'اف تی ایکس',
      'EURUSDT' => '',
      'OGNUSDT' => 'اوریجین توکن',
      'DREPUSDT' => 'دی رپ',
      'BULLUSDT' => '',
      'BEARUSDT' => '',
      'ETHBULLUSDT' => '',
      'ETHBEARUSDT' => '',
      'TCTUSDT' => 'توکن کلاب',
      'WRXUSDT' => 'وزیر ایکس',
      'BTSUSDT' => 'بیت شرز',
      'LSKUSDT' => 'لیسک',
      'BNTUSDT' => 'بانکور',
      'LTOUSDT' => 'ال تی او نتورک',
      'EOSBULLUSDT' => '1',
      'EOSBEARUSDT' => '1',
      'XRPBULLUSDT' => '1',
      'XRPBEARUSDT' => '1',
      'STRATUSDT' => 'استراتیس',
      'AIONUSDT' => 'آئون',
      'MBLUSDT' => 'مووی بلاک',
      'COTIUSDT' => 'کوتی',
      'BNBBULLUSDT' => '1',
      'BNBBEARUSDT' => '1',
      'STPTUSDT' => 'اس تی پی نتورک',
      'WTCUSDT' => 'والتون',
      'DATAUSDT' => 'استریمر داتاکوین',
      'XZCUSDT' => 'فیرو',
      'SOLUSDT' => 'سولانا',
      'CTSIUSDT' => 'کارتزی',
      'HIVEUSDT' => 'هایو',
      'CHRUSDT' => 'کرومیا',
      'BTCUPUSDT' => '1',
      'BTCDOWNUSDT' => '1',
      'GXSUSDT' => 'جی ایکس چین',
      'ARDRUSDT' => 'آردور',
      'LENDUSDT' => 'آوی',
      'MDTUSDT' => 'مژربل دیتا توکن',
      'STMXUSDT' => 'استورم ایکس',
      'KNCUSDT' => 'کایبر نتورک',
      'REPUSDT' => 'آگور ورژن2',
      'LRCUSDT' => 'لوپرینک',
      'PNTUSDT' => 'پنتا',
      'COMPUSDT' => 'کامپوند',
      'BKRWUSDT' => '1',
      'SCUSDT' => 'سیا کوین',
      'ZENUSDT' => 'هایریزن',
      'SNXUSDT' => 'سینتیکس',
      'ETHUPUSDT' => '1',
      'ETHDOWNUSDT' => '1',
      'ADAUPUSDT' => '1',
      'ADADOWNUSDT' => '1',
      'LINKUPUSDT' => '1',
      'LINKDOWNUSDT' => '1',
      'VTHOUSDT' => 'وتور',
      'DGBUSDT' => 'دیجی بایت',
      'GBPUSDT' => '1',
      'SXPUSDT' => 'سوایپ',
      'MKRUSDT' => 'میکر',
      'DAIUSDT' => '1',
      'DCRUSDT' => 'دکرد',
      'STORJUSDT' => 'استورج',
      'BNBUPUSDT' => '1',
      'BNBDOWNUSDT' => '1',
      'XTZUPUSDT' => '1',
      'XTZDOWNUSDT' => '1',
      'MANAUSDT' => 'دی سنترلند',
      'AUDUSDT' => '1',
      'YFIUSDT' => 'یرن فایننس',
      'BALUSDT' => 'بالانسر',
      'BLZUSDT' => 'بلوزل',
      'IRISUSDT' => 'آی آر اس نت',
      'KMDUSDT' => 'کمودو',
      'JSTUSDT' => 'جاست',
      'ANTUSDT' => 'آراگون',
      'SRMUSDT' => 'سروم',
      'CRVUSDT' => 'کرو',
      'SANDUSDT' => 'سندباکس',
      'OCEANUSDT' => 'اوشن پروتکل',
      'NMRUSDT' => 'نوموریر',
      'DOTUSDT' => 'پولکادات',
      'LUNAUSDT' => 'ترا',
      'RSRUSDT' => 'ریزرو رایتس',
      'PAXGUSDT' => 'پکس گلد',
      'WNXMUSDT' => 'ریپد ان ایکس ام',
      'TRBUSDT' => 'تلور',
      'BZRXUSDT' => 'بی زد ایکس پروتکل',
      'SUSHIUSDT' => 'سوشی',
      'YFIIUSDT' => 'دی اف ای مانی',
      'KSMUSDT' => 'کوساما',
      'EGLDUSDT' => 'الراند گلد',
      'DIAUSDT' => 'دیا',
      'RUNEUSDT' => 'تورچین',
      'FIOUSDT' => 'اف آی او پروتکل',
      'UMAUSDT' => 'یو ام ای',
      'EOSUPUSDT' => '1',
      'EOSDOWNUSDT' => '1',
      'TRXUPUSDT' => '1',
      'TRXDOWNUSDT' => '1',
      'XRPUPUSDT' => '1',
      'XRPDOWNUSDT' => '1',
      'DOTUPUSDT' => '1',
      'DOTDOWNUSDT' => '1',
      'BELUSDT' => 'بلا پروتکل',
      'WINGUSDT' => 'وینگ پروتکل',
      'LTCUPUSDT' => '1',
      'LTCDOWNUSDT' => '1',
      'UNIUSDT' => 'یونی سواپ',
      'NBSUSDT' => 'نیو بیت شرز',
      'OXTUSDT' => 'ارکید',
      'SUNUSDT' => 'سان',
      'AVAXUSDT' => 'آولنج',
      'HNTUSDT' => 'هلیوم',
      'FLMUSDT' => 'فلامینگو',
      'UNIUPUSDT' => '1',
      'UNIDOWNUSDT' => '1',
      'ORNUSDT' => 'اوریون پروتکل',
      'UTKUSDT' => 'یوتراست',
      'XVSUSDT' => 'ونوس',
      'ALPHAUSDT' => 'آلفا فایننس لب',
      'AAVEUSDT' => 'آوی',
      'NEARUSDT' => 'نیر پروتکل',
      'SXPUPUSDT' => '1',
      'SXPDOWNUSDT' => '1',
      'FILUSDT' => 'فایل کوین',
      'FILUPUSDT' => '1',
      'FILDOWNUSDT' => '1',
      'YFIUPUSDT' => '1',
      'YFIDOWNUSDT' => '1',
      'INJUSDT' => 'اینجکتیو پروتکل',
      'AUDIOUSDT' => 'آدیوس',
      'CTKUSDT' => 'سرتیک',
      'BCHUPUSDT' => '1',
      'BCHDOWNUSDT' => '1',
      'AKROUSDT' => 'آکروپلیس',
      'AXSUSDT' => 'آکسی اینفینیتی',
      'HARDUSDT' => 'هارد پروتکل',
      'DNTUSDT' => 'دیستریکت زیرو ایکس',
      'STRAXUSDT' => 'استراتیس',
      'UNFIUSDT' => 'یونیفای پروتکل',
      'ROSEUSDT' => 'یو آیسیس نتورک',
      'AVAUSDT' => 'تراولا',
      'XEMUSDT' => 'نم',
      'AAVEUPUSDT' => '',
      'AAVEDOWNUSDT' => '',
      'SKLUSDT' => 'اسکیل نتورک',
      'SUSDUSDT' => 'اس یو اس دی',
      'SUSHIUPUSDT' => '',
      'SUSHIDOWNUSDT' => '',
      'XLMUPUSDT' => '',
      'XLMDOWNUSDT' => '',
      'GRTUSDT' => 'گراف توکن',
      'JUVUSDT' => 'یونتوی فن توکن',
      'PSGUSDT' => 'پاری سن ژرمن',
      '1INCHUSDT' => '1 اینج',
      'REEFUSDT' => 'ریف فایننس',
      'OGUSDT' => 'او جی فن توکن',
      'ATMUSDT' => 'اتلتیکو مادرید فن توکن',
      'ASRUSDT' => 'آ.اس روم فن توکن',
      'CELOUSDT' => 'سلو',
      'RIFUSDT' => 'آر اس کا اینفراستراکچر',
      'BTCSTUSDT' => 'بیت کوین هش ریت توکن',
      'TRUUSDT' => 'تروفای',
      'CKBUSDT' => '',
      'TWTUSDT' => 'تراست ولت توکن',
      'FIROUSDT' => 'فیرو',
      'LITUSDT' => 'لایت توکن',
      'SFPUSDT' => '',
      'DODOUSDT' => 'دودو',
      'CAKEUSDT' => 'پنکیک سواپ',
      'ACMUSDT' => '',
      'BADGERUSDT' => 'بجر دائو',
      'FISUSDT' => 'استفی',
      'OMUSDT' => '',
      'PONDUSDT' => '',
      'DEGOUSDT' => '',
      'ALICEUSDT' => 'مای نیبرآلیس',
      'LINAUSDT' => '',
      'PERPUSDT' => '',
      'RAMPUSDT' => '',
      'SUPERUSDT' => 'سوپرفارم',
      'CFXUSDT' => 'کانفلاکس نتورک',
      'EPSUSDT' => 'الیپسیس',
      'AUTOUSDT' => '',
      'TKOUSDT' => 'توکوکریپتو',
      'PUNDIXUSDT' => 'پاندی ایکس',
      'TLMUSDT' => '',
      '1INCHUPUSDT' => '',
      '1INCHDOWNUSDT' => '',
      'BTGUSDT' => '',
      'MIRUSDT' => '',
      'BARUSDT' => '',
      'FORTHUSDT' => '',
      'BAKEUSDT' => '',
      'BURGERUSDT' => '',
      'SLPUSDT' => '',
      'SHIBUSDT' => 'شیبا',
      'ICPUSDT' => '',
      'ARUSDT' => 'آرویو',
      'POLSUSDT' => 'پولکا استارتر',
      'MDXUSDT' => '',
      'MASKUSDT' => '',
      'LPTUSDT' => '',
    ),
    'unlist' => 
    array (
      0 => 'LPTUSDT',
      1 => 'BTGUSDT',
      2 => 'AUTOUSDT',
      3 => 'ETHBULLUSDT',
      4 => 'ETHBEARUSDT',
      5 => 'NPXSUSDT',
      6 => 'EURUSDT',
      7 => 'BULLUSDT',
      8 => 'EOSBULLUSDT',
      9 => 'BEARUSDT',
      10 => 'EOSBEARUSDT',
      11 => 'XRPBULLUSDT',
      12 => 'XRPBEARUSDT',
      13 => 'VENUSDT',
      14 => 'BCHABCUSDT',
      15 => 'BCHSVUSDT',
      16 => 'BCCUSDT',
      17 => 'EOSBEARUSDT',
      18 => 'XRPBULLUSDT',
      19 => 'XRPBEARUSDT',
      20 => 'BNBBULLUSDT',
      21 => 'BNBBEARUSDT',
      22 => 'BTCUPUSDT',
      23 => 'BTCDOWNUSDT',
      24 => 'BKRWUSDT',
      25 => 'ETHUPUSDT',
      26 => 'ETHDOWNUSDT',
      27 => 'ADAUPUSDT',
      28 => 'ADADOWNUSDT',
      29 => 'LINKUPUSDT',
      30 => 'LINKDOWNUSDT',
      31 => 'BNBUPUSDT',
      32 => 'BNBDOWNUSDT',
      33 => 'XTZUPUSDT',
      34 => 'XTZDOWNUSDT',
      35 => 'EOSUPUSDT',
      36 => 'EOSDOWNUSDT',
      37 => 'TRXUPUSDT',
      38 => 'TRXDOWNUSDT',
      39 => 'XRPUPUSDT',
      40 => 'XRPDOWNUSDT',
      41 => 'DOTUPUSDT',
      42 => 'DOTDOWNUSDT',
      43 => 'LTCUPUSDT',
      44 => 'LTCDOWNUSDT',
      45 => 'UNIUPUSDT',
      46 => 'UNIDOWNUSDT',
      47 => 'SXPUPUSDT',
      48 => 'SXPDOWNUSDT',
      49 => 'FILUPUSDT',
      50 => 'FILDOWNUSDT',
      51 => 'YFIUPUSDT',
      52 => 'YFIDOWNUSDT',
      53 => 'BCHUPUSDT',
      54 => 'BCHDOWNUSDT',
      55 => 'AAVEUPUSDT',
      56 => 'AAVEDOWNUSDT',
      57 => 'SUSHIUPUSDT',
      58 => 'SUSHIDOWNUSDT',
      59 => 'XLMUPUSDT',
      60 => 'XLMDOWNUSDT',
      61 => 'CKBUSDT',
      62 => 'SFPUSDT',
      63 => 'ACMUSDT',
      64 => 'OMUSDT',
      65 => 'PONDUSDT',
      66 => 'DEGOUSDT',
      67 => 'LINAUSDT',
      68 => 'PERPUSDT',
      69 => 'RAMPUSDT',
      70 => 'TLMUSDT',
      71 => '1INCHUPUSDT',
      72 => '1INCHDOWNUSDT',
      73 => 'MIRUSDT',
      74 => 'BARUSDT',
      75 => 'FORTHUSDT',
      76 => 'BAKEUSDT',
      77 => 'BURGERUSDT',
      78 => 'SLPUSDT',
      79 => 'ICPUSDT',
      80 => 'MDXUSDT',
      81 => 'MASKUSDT',
    ),
  ),
  'queue' => 
  array (
    'default' => 'sync',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => '',
        'secret' => '',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'suffix' => NULL,
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
      ),
    ),
    'failed' => 
    array (
      'driver' => 'database-uuids',
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => NULL,
      'secret' => NULL,
      'endpoint' => 'api.mailgun.net',
    ),
    'postmark' => 
    array (
      'token' => NULL,
    ),
    'ses' => 
    array (
      'key' => '',
      'secret' => '',
      'region' => 'us-east-1',
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '518400',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/home/hasan/Documents/Projects/poolex/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/home/hasan/Documents/Projects/poolex/resources/views',
    ),
    'compiled' => '/home/hasan/Documents/Projects/poolex/storage/framework/views',
  ),
  'flare' => 
  array (
    'key' => NULL,
    'reporting' => 
    array (
      'anonymize_ips' => true,
      'collect_git_information' => false,
      'report_queries' => true,
      'maximum_number_of_collected_queries' => 200,
      'report_query_bindings' => true,
      'report_view_data' => true,
      'grouping_type' => NULL,
      'report_logs' => true,
      'maximum_number_of_collected_logs' => 200,
      'censor_request_body_fields' => 
      array (
        0 => 'password',
      ),
    ),
    'send_logs_as_events' => true,
    'censor_request_body_fields' => 
    array (
      0 => 'password',
    ),
  ),
  'ignition' => 
  array (
    'editor' => 'phpstorm',
    'theme' => 'light',
    'enable_share_button' => true,
    'register_commands' => false,
    'ignored_solution_providers' => 
    array (
      0 => 'Facade\\Ignition\\SolutionProviders\\MissingPackageSolutionProvider',
    ),
    'enable_runnable_solutions' => NULL,
    'remote_sites_path' => '',
    'local_sites_path' => '',
    'housekeeping_endpoint_prefix' => '_ignition',
  ),
  'trustedproxy' => 
  array (
    'proxies' => NULL,
    'headers' => 30,
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'alias' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
