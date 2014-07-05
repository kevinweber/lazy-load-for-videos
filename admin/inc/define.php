<?php
/**
 * Define signup
 */
define( 'LL_NEWS_TEXT', 'To suggest and vote for new features: Let the developer come into contact with you.' );
define( 'LL_NEWS_BUTTON', 'Get contacted' );
define( 'LL_NEWS_GROUP', 'Signup via Plugin' );
define( 'LL_NEWS_NAME', 'b_f65d804ad274b9c8812b59b4d_b900d3be48' );
define( 'LL_NEWS_ACTION_URL', 'http://kevinw.us2.list-manage1.com/subscribe/post?u=f65d804ad274b9c8812b59b4d&amp;id=b900d3be48' );
/**
 * Definitions for admin page
 */
if ( !defined( 'LL_NOTICE' ) )
	define( 'LL_ADMIN_URL', 'lazyload.php' );
if ( !defined( 'LL_NOTICE' ) )
	define( 'LL_NOTICE', '<p class="notice"><span style="color:#f60;">Important:</span> Updates on <u>underlined options</u> will only affect new posts and posts you update afterwards. To apply changes on all existing posts, save your changes and then <a href="options-general.php?page='. LL_ADMIN_URL .'&update_posts=with_oembed">update all posts by calling this link</a> once. <span style="color:#f60;">Attention:</span> On slow sites or blogs with many posts this link might cause a "500 Internal Server Error" and not update all posts.</p>' );

?>