<?php
/**
 * Tests for building the project's AGENTS.md.
 *
 * @package ComposerScripts
 */

namespace Viget\ComposerScripts\Tests;

use PHPUnit\Framework\TestCase;
use Viget\ComposerScripts\ProjectEvents\PostCreateProjectScript;

/**
 * AgentsFileTest
 */
class AgentsFileTest extends TestCase {

	public function testRemovesMarkedBlocks(): void {
		$content = "- One\n<!-- starter-only -->\n- Starter note\n<!-- /starter-only -->\n- Two\n<!-- starter-only -->\n- Another\n<!-- /starter-only -->\n";

		$this->assertSame( "- One\n- Two\n", PostCreateProjectScript::stripStarterOnly( $content ) );
	}

	public function testLeavesUnmarkedContent(): void {
		$content = "- One\n- Two\n";

		$this->assertSame( $content, PostCreateProjectScript::stripStarterOnly( $content ) );
	}

	public function testAppendsTheDistFile(): void {
		$agents = "# Agents\n<!-- starter-only -->\n- Starter note\n<!-- /starter-only -->\n- One\n\n";

		$this->assertSame( "# Agents\n- One\n\n## Project notes\n", PostCreateProjectScript::combineAgents( $agents, "\n## Project notes\n" ) );
	}

	public function testBuildsTheRealAgentsFile(): void {
		$root   = dirname( __DIR__ );
		$agents = PostCreateProjectScript::combineAgents( file_get_contents( $root . '/AGENTS.md' ), file_get_contents( $root . '/AGENTS.dist.md' ) );

		$this->assertStringNotContainsString( 'starter-only', $agents );
		$this->assertStringNotContainsString( 'stays at `0.1.0`', $agents );
		$this->assertStringContainsString( '## Project notes', $agents );
		$this->assertStringNotContainsString( 'Never name a client in this repo', $agents );
		$this->assertStringContainsString( 'WP Site Starter is the only client named in this repo.', $agents );
	}
}
