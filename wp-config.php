<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ecommerce-ai' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Jc1:G&x2y;@VSSzyKK`K8%Q5jIu?fAbFx2#o6i|7i> F,-&[XkLKIsy.SJ^&Ml!J' );
define( 'SECURE_AUTH_KEY',  'vtFK6u4`N9a*b6@;O%X6NQeN0I5fzAY+%&$e=7 jT*fVh:2cn^-k++2[}[6Ta7/,' );
define( 'LOGGED_IN_KEY',    '-7=Vf3/KqUjgB|!D+3OfhoMhNp*)gEM?joEp(zV>gP!QV]F,Th` quG^:lKL=kl$' );
define( 'NONCE_KEY',        'RG| *75fd:ZgtIQ{AdO*`sxd}o>l0C9JZ%?!knkTwyx74.0#?oMhgB;LsA#b6+95' );
define( 'AUTH_SALT',        'L:x1bqh?so-Gv{,3W-6#)VM#vWJ![H5y73XVwU[ts|9CsrEM)BvfnW}q#_zctD>Y' );
define( 'SECURE_AUTH_SALT', ']3wG/)S}SEn9C2BD<`4f=v`I0)cLACe-Q;gbDk# Q.@R,tKCRfohxffv61E=B5G;' );
define( 'LOGGED_IN_SALT',   'ogL~1#~}l^@1N|05+-edS+_(f5MDl`...Im]aM*V4S|c7Ii9?@rQ2QRZ?6L)SGls' );
define( 'NONCE_SALT',       '!uH^&,,L10A07Xp_.%f-zV#Lg(JY)sESy+v:r #B)6ew=(fJ4<-W~i-k[X3=^U s' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
