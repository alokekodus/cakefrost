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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cakefrost' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '|8[>VQ, ^gH|whE<`S$WN7IQOs}:ZI)f=n8LwuMe`u?jwDPsyt!!*_HI)do;ooC_' );
define( 'SECURE_AUTH_KEY',  'j||zGmcjiPII-Ew|QXa]QM-f<twu8`VaPU*I}NLjGHf]TPX~h~q{YZF!z(Bk.wcG' );
define( 'LOGGED_IN_KEY',    'qpZk/7W=;^DaN4*Bq-;6.VQjyno~&!rYAw2-Q@UYyqA7hK8N3RWw&&M,r3A}^yg$' );
define( 'NONCE_KEY',        '2[S&jB,5s>7`th>M==HZ=axu%+]EfoO*l<1EK%m~*@2;,k<FW@!76w[j0012bTab' );
define( 'AUTH_SALT',        '.T=F-=^9SOdtKfs/:SuGvRqPKL%+BbqUeqUIlyT+@_euT)S[.:,Vm$fs[]Ak^HY5' );
define( 'SECURE_AUTH_SALT', 'E kgSHh.qGI$]OUvj(kAXb|Z}YIA# > i%oT0IIot`>6F= Dt`|P}^B+4P[:P=KA' );
define( 'LOGGED_IN_SALT',   'OLO!*ryOz/SD`aAH.AxI=0423Onnx>9(cQn5EvyV?8ZWNGzS$Gqs>|1,oDN>bMq6' );
define( 'NONCE_SALT',       '$=}2KRW[eD$=p^Xv;l)Xv,h:H7NZ[X(|+K]v}O/+zKFheXIZFYI@R<<TBn7yr+lo' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
