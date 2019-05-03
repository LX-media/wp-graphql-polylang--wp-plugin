<?php

// XXX: Can we autoload this somehow?
require_once __DIR__ . '/PolylangUnitTestCase.php';

class PostObjectMutationTest extends PolylangUnitTestCase
{
    public $admin_id = 1;

    static function wpSetUpBeforeClass()
    {
        parent::wpSetUpBeforeClass();

        self::set_default_language('en_US');
        self::create_language('en_US');
        self::create_language('fr_FR');
        self::create_language('fi');
        self::create_language('de_DE_formal');
        self::create_language('es_ES');
    }

    public function setUp() {
        parent::setUp();


        // XXX not enough permissions??
        // $this->admin = $this->factory->user->create( [
		// 	'role' => 'administrator',
		// ] );
    }

    public function testPostCreate() {
        wp_set_current_user( $this->admin_id );

        $query = '
        mutation InsertPost {
            createPost(input: {clientMutationId: "1", title: "test", language: FI}) {
              clientMutationId
              post {
                title
                postId
                language {
                  code
                }
              }
            }
          }
        ';

        $data = do_graphql_request($query);
        $this->assertArrayNotHasKey('errors', $data, print_r($data, true));
        $post_id = $data['data']['createPost']['post']['postId'];
        $lang = pll_get_post_language($post_id, 'slug');
        $this->assertEquals('fi', $lang);

    }
}