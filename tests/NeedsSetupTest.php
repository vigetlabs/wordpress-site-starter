<?php
/**
 * Tests for PostInstallScript::needsSetup().
 *
 * @package ComposerScripts
 */

namespace Viget\ComposerScripts\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Viget\ComposerScripts\ProjectEvents\PostInstallScript;

/**
 * Stubs the two things needsSetup() reads so the decision can be tested directly.
 */
class SetupStateStub extends PostInstallScript {

	public static bool $coreFiles = false;

	public static bool $dbInstalled = false;

	protected static function hasCoreFiles(): bool {
		return static::$coreFiles;
	}

	protected static function isWordPressDbInstalled(): bool {
		return static::$dbInstalled;
	}
}

/**
 * NeedsSetupTest
 */
class NeedsSetupTest extends TestCase {

	#[DataProvider( 'setupStates' )]
	public function testNeedsSetup( bool $coreFiles, bool $dbInstalled, bool $expected, string $scenario ): void {
		SetupStateStub::$coreFiles   = $coreFiles;
		SetupStateStub::$dbInstalled = $dbInstalled;

		$this->assertSame( $expected, SetupStateStub::needsSetup(), $scenario );
	}

	/**
	 * @return array<string, array{bool, bool, bool, string}>
	 */
	public static function setupStates(): array {
		return [
			'fresh create-project' => [ false, false, true, 'No core files and no database: setup must run.' ],
			'core deleted'         => [ false, true, true, 'Core files missing: setup must run.' ],
			'interrupted install'  => [ true, false, true, 'Core files on disk with an empty database must still run setup, or the project is stuck on install.php.' ],
			'already set up'       => [ true, true, false, 'A working install must not be set up again.' ],
		];
	}
}
