<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mystore' );

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
define( 'AUTH_KEY',         'jm2t}rl63a%IZ,>4Ob0`Wl_zC/AU<P%?MyOQJpcI>i.xZLa4h?.z[XO]BEYQf}q^' );
define( 'SECURE_AUTH_KEY',  '4}91(,aX~D|WUO28E9^qv:K`{@%y-}?v.}Og6i-; ]2R3KmPk5baXzxGq;lg;9$T' );
define( 'LOGGED_IN_KEY',    '#T]=`L_NSHvWy)H.S0CL+G2/e?1>Ad{_`ht1`NpM1+z_ kNepK|}J MP)B^67}Ie' );
define( 'NONCE_KEY',        'hzuw59=dsEehM:Wa_u_R`-rsD~{u)hs8LoRu^vGp$m~~mMU}9{:FV+H:_JugwL~^' );
define( 'AUTH_SALT',        '~IjhPO>loW{`YQ!nn5o36+X5Yi;#F*5{-[]:|okZhJ,Zco?&kZjxvY +xVJi)9W{' );
define( 'SECURE_AUTH_SALT', '@!.Y8YZ1H2}RGSQ,}q+<i1n@WW^=yKI&T4l|]1]tEFT<P6?eq|*&S1^Gw8lPHq`b' );
define( 'LOGGED_IN_SALT',   'x^,wMu#NQ2;dEV];{96&;+}hj2QopDaSj(q|-Ws<(QVv1}i?b_nIx$sal&I3jiOd' );
define( 'NONCE_SALT',       '%ObuN!/fM_Y%}pSv17;n3mw>wVCp)IU_ViC,.=%f.5mF%Kh/8| %.|64dxKt.jX5' );

/**#@-*/

/**
 * WordPress database table prefix.
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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
