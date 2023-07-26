<?php

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// IMPORTANT: this file needs to stay in-sync with https://github.com/WordPress/WordPress/blob/master/wp-config-sample.php
// (it gets parsed by the upstream wizard in https://github.com/WordPress/WordPress/blob/f27cb65e1ef25d11b535695a660e7282b98eb742/wp-admin/setup-config.php#L356-L392)

// a helper function to lookup "env_FILE", "env", then fallback
if (!function_exists('getenv_docker')) {
    function getenv_docker($env, $default) {
        if ($fileEnv = getenv($env . '_FILE')) {
            return rtrim(file_get_contents($fileEnv), "\r\n");
        }

        if (($val = getenv($env)) !== false) {
            return $val;
        }

        return $default;
    }
}

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', getenv_docker('WORDPRESS_DB_NAME', 'wordpress'));

/** MySQL database username */
define('DB_USER', getenv_docker('WORDPRESS_DB_USER', 'user'));

/** MySQL database password */
define('DB_PASSWORD', getenv_docker('WORDPRESS_DB_PASSWORD', 'password'));

/** MySQL hostname */
define('DB_HOST', getenv_docker('WORDPRESS_DB_HOST', 'mysql'));

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', getenv_docker('WORDPRESS_DB_CHARSET', 'utf8mb4'));

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', getenv_docker('WORDPRESS_DB_COLLATE', ''));

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         getenv_docker('WORDPRESS_AUTH_KEY',         '-3{ZGi /i2u9Sfi2I?6z/4*>qEz?<;dqgKIodM@Tp(aO.%?Y;[X(Si{e{6??lhHI') );
define( 'SECURE_AUTH_KEY',  getenv_docker('WORDPRESS_SECURE_AUTH_KEY',  'CgT Q9P13xC+96TQ?}kt|X&?+i6lg8hxyceDQd=+<[*?_>`jE[mfOD ?eEhYB(2v') );
define( 'LOGGED_IN_KEY',    getenv_docker('WORDPRESS_LOGGED_IN_KEY',    '-;7}G6-h-HcyvR.Gq-X91pVW{u|_gAUE]yTR0g Ayf.[.Kj+&pFtb=DOy$;#gk-%') );
define( 'NONCE_KEY',        getenv_docker('WORDPRESS_NONCE_KEY',        'd+tg52@Q#@HyUodDL8U}+%HU|lIy|O>%-i!wBmTEVFve9y#)t2k}k#D-~i+cd4:g') );
define( 'AUTH_SALT',        getenv_docker('WORDPRESS_AUTH_SALT',        '(zG&d7g0.qgC3ttU/*=~k|8{]%w1O68LW=j+tkJ81YtikE#GW))G[|Z9/ <ctCKp') );
define( 'SECURE_AUTH_SALT', getenv_docker('WORDPRESS_SECURE_AUTH_SALT', '8u|{2q=F+m:{UwT8$Z|igmtqa*L)A9|i[{>d!F~)kc&dp>+WoLQrV9s?D|?CgMcz') );
define( 'LOGGED_IN_SALT',   getenv_docker('WORDPRESS_LOGGED_IN_SALT',   'yq7w~y03EL|ctX-|kR}v$=[E?P+[zNf8IhP@}?3-FtNY^-t|;8-8R0@K6HR!sm7o') );
define( 'NONCE_SALT',       getenv_docker('WORDPRESS_NONCE_SALT',       '1{1G%>#~Va8TRt-TU;pi.v{@;P-+M%g}Ud!- 4eWl](=pw?|-Rym:jGtU9|/#@4t') );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = getenv_docker('WORDPRESS_TABLE_PREFIX', 'wp_');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', filter_var(getenv_docker('WORDPRESS_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

/* Add any custom values between this line and the "stop editing" line. */

define('WP_DEBUG_LOG', filter_var(getenv_docker('WORDPRESS_DEBUG_LOG', 'false'), FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_DISPLAY', filter_var(getenv_docker('WORDPRESS_DEBUG_DISPLAY', 'false'), FILTER_VALIDATE_BOOLEAN));
define('WP_DISABLE_FATAL_ERROR_HANDLER', filter_var(getenv_docker('WORDPRESS_DISABLE_FATAL_ERROR_HANDLER', 'true'), FILTER_VALIDATE_BOOLEAN));

define('WP_REDIS_HOST', getenv_docker('WORDPRESS_REDIS_HOST', 'redis'));
define('WP_REDIS_PASSWORD', getenv_docker('WORDPRESS_REDIS_PASSWORD', 'password'));
define('WP_CACHE_KEY_SALT', getenv_docker('WORDPRESS_CACHE_KEY_SALT', 'R!0uA_0:DmiDAX|18owsU[{9f-]+}p`,;lGaU:}}f#T}f-K%#I>:?DvpPuv|_8Bl'));

define('DISABLE_WP_CRON', filter_var(getenv_docker('WORDPRESS_DISABLE_WP_CRON', 'true'), FILTER_VALIDATE_BOOLEAN));

/** WP-Optimize Cache */
define('WP_CACHE', filter_var(getenv_docker('WORDPRESS_CACHE', 'true'), FILTER_VALIDATE_BOOLEAN));

/** Automatic updates */
define('WP_HTTP_BLOCK_EXTERNAL', filter_var(getenv_docker('WORDPRESS_HTTP_BLOCK_EXTERNAL', 'false'), FILTER_VALIDATE_BOOLEAN));
define('WP_AUTO_UPDATE_CORE', filter_var(getenv_docker('WORDPRESS_AUTO_UPDATE_CORE', 'false'), FILTER_VALIDATE_BOOLEAN));
define('AUTOMATIC_UPDATER_DISABLED', filter_var(getenv_docker('WORDPRESS_AUTOMATIC_UPDATER_DISABLED', 'true'), FILTER_VALIDATE_BOOLEAN));

/** Move directories */
define('WP_CONTENT_DIR', __DIR__ . '/content');
define('WP_CONTENT_URL', getenv_docker('WORDPRESS_SITE_URL', 'https://localhost') . '/wp-content');

define('WP_PLUGIN_DIR', __DIR__ . '/content/plugins');
define('WP_PLUGIN_URL', getenv_docker('WORDPRESS_SITE_URL', 'https://localhost') . '/wp-content/plugins');
define('PLUGINDIR', __DIR__ . '/content/plugins');


/**
 * Handle SSL reverse proxy
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && str_contains($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https')) {
    $_SERVER['HTTPS'] = 'on';
}

if ($configExtra = getenv_docker('WORDPRESS_CONFIG_EXTRA', '')) {
    eval($configExtra);
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/core/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/** Handles additional stuff in global scope. */
require_once __DIR__ . '/../vendor/phpsword/sword-bundle/src/Loader/wp-load.php';
