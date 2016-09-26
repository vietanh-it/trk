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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'a_thorakao');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

if (!defined('WP_SITEURL')) {
	define('WP_SITEURL', 'http://local.thorakao.vn');
}

if (!defined('WP_HOME')) {
	define('WP_HOME', 'http://local.thorakao.vn');
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'K/.1{Z1B9[*x17,XU hstI7_5Kf12.OU;+J|4f+^)/.b8mxm)l:3$o9-H(p$H#*q');
define('SECURE_AUTH_KEY',  'fJqGPhY:>e]?TS`68/vK1.1IVnf*G_<rT#-qqpn<ai9.ucu$J);1acYRDT.(^%dB');
define('LOGGED_IN_KEY',    '8dl%DB;%E)gndIYLjw7F/Sb[H|E.52:S(#ek)j#$4X#z,g~,q/KJWB+-=h88%3 =');
define('NONCE_KEY',        'b4X1j:Gm=f{?EhN{crV<]#G^Un,TmV/)tZUaqjDr0oIUJ zEff3%IKT_Pev!4b$&');
define('AUTH_SALT',        '{gO[,@[-XVR{@D|!nJv4xA:F~2{n2IcUTNWg.y^z^D!#-G}Q&9vd{qxaKsPY&f{%');
define('SECURE_AUTH_SALT', 's+t[:;y?Ro9rnl7f0yK=J1ekf?TV>(t[O6awo7kf4VKJMy#%`R:t.q]dZX`|0b+`');
define('LOGGED_IN_SALT',   'EE8nI7K^?#eUBI#wu2@PZiM_.b9L,!;/S*?D>EuRSr,9^x/8H5<C;9/Fke:r-V{!');
define('NONCE_SALT',       '2STt~9TX[P^m:d;Ot{dJ(Lif1Ql02#]TLzDR6>&M%tN+b&6a[@Y?UqJ%}?8ihIVC');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'trk_';

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
