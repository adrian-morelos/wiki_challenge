<?php

namespace Drupal\wiki_challenge\Tests\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that searching for a parameter gets the correct result.
 *
 * @group wiki_challenge
 */
class WikiChallengeSearchPageTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['node', 'wiki_challenge'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    // Log in with sufficient privileges.
    $user = $this->drupalCreateUser(['access content', 'search content']);
    $this->drupalLogin($user);
  }

  /**
   * Tests that the search path '/wiki/{parameter}' works.
   */
  public function testPathParameters() {
    // Test search path with parameters.
    $path = 'wiki/love+pizza';
    $this->drupalGet($path);
    $this->assertText(t('Search term(s): @terms.', ['@terms' => 'love pizza']));
    // Test search path with no parameters.
    $path = 'wiki';
    $this->drupalGet($path);
    $this->assertText(t('Wiki Search'));
    $this->assertText(t('Enter the terms you wish to search for.'));
  }

  /**
   * Tests Wiki page Search result with exact phrase.
   */
  public function testSearchParameters() {
    // Create a node of type 'wikipedia_article' with exact phrase.
    $settings = [
      'type' => 'wikipedia_article',
      'title' => 'love pizza',
      'body' => [['value' => 'love pizza body content']],
      'status' => TRUE,
    ];
    $this->drupalCreateNode($settings);
    // Test if the form gets the correct result.
    $edit = [
      'keys' => 'love pizza',
      'render_mode' => 'render_mode_render_as_snippets',
      'query_mode' => 'query_mode_database_api',
    ];
    $this->drupalCreateNode($settings);
    $this->drupalPostForm('wiki/love+pizza', $edit, t('Search'));
    // Test search result title.
    $this->assertText(t('Search term(s): @terms.', ['@terms' => 'love pizza']));
    // Test if the item(By the phrase that contains) is present on the result.
    $this->assertText(t('love pizza body content'));
  }

}
