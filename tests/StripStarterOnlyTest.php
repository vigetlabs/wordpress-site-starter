<?php
/**
 * Tests for PostCreateProjectScript::stripStarterOnly().
 *
 * @package ComposerScripts
 */

namespace Viget\ComposerScripts\Tests;

use PHPUnit\Framework\TestCase;
use Viget\ComposerScripts\ProjectEvents\PostCreateProjectScript;

/**
 * StripStarterOnlyTest
 */
class StripStarterOnlyTest extends TestCase {

	public function testRemovesMarkedBlocks(): void {
		$content = "- One\n<!-- starter-only -->\n- Starter note\n<!-- /starter-only -->\n- Two\n<!-- starter-only -->\n- Another\n<!-- /starter-only -->\n";

		$this->assertSame( "- One\n- Two\n", PostCreateProjectScript::stripStarterOnly( $content ) );
	}

	public function testLeavesUnmarkedContent(): void {
		$content = "- One\n- Two\n";

		$this->assertSame( $content, PostCreateProjectScript::stripStarterOnly( $content ) );
	}

	public function testStripsTheRealAgentsFile(): void {
		$stripped = PostCreateProjectScript::stripStarterOnly( file_get_contents( dirname( __DIR__ ) . '/AGENTS.md' ) );

		$this->assertStringNotContainsString( 'starter-only', $stripped );
		$this->assertStringNotContainsString( 'stays at `0.1.0`', $stripped );
	}
}
