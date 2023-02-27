<?php

namespace WpUtm\Test;

class ScriptsEnqueueingTest extends \WP_UnitTestCase {
	public static $post_id;
	public static function wpSetUpBeforeClass( \WP_UnitTest_Factory $factory ) {
		self::$post_id = $factory->post->create(
			array(
				'post_content' => <<<HTML
<!-- wp:paragraph -->
<p>This is an example page. It's different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:</p>
<!-- /wp:paragraph -->

<!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>Hi there! I'm a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like piña coladas. (And gettin' caught in the rain.)</p>
<!-- /wp:paragraph --></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph -->
<p>...or something like this:</p>
<!-- /wp:paragraph -->

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</p>
<!-- /wp:paragraph --></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph -->
<p>As a new WordPress user, you should go to <a href="http://localhost:8888/:8888/wp-admin/">your dashboard</a> to delete this page and create new pages for your content. Have fun!</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
HTML,
			)
		);
	}
	public function test_footer_scripts_enqueued_in_footer() {
		\ob_start();
		\wp_footer();
		\ob_end_clean();
		$this->assertEquals( 1, $GLOBALS['wp_scripts']->registered['wputm-footer-script']->extra['group'] );
	}

	public function test_header_scripts_enqueued_in_header() {
		\ob_start();
		\wp_footer();
		\ob_end_clean();
		$this->assertEmpty( $GLOBALS['wp_scripts']->registered['wputm-header-script']->extra );
	}

	public function test_enqueueing_block_scripts() {
		global $wputm;
		$sut = $wputm->get( \WpUtm\AssetsRegistration::class );
		$sut->enqueue_block_assets( self::$post_id );
		$this->assertContains( 'wputm-core-paragraph', $GLOBALS['wp_scripts']->queue );
	}
}
