<?php
/**
 * Collects all the instance of classes being created
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts\WPDocsGenerator;

use PhpParser\Node;

/**
 * ReferenceCollection Class
 */
class ReferenceCollection extends CodeCollection
{
	public array $references = [];

	/**
	 * @param Node $node
	 * @return void
	 */
	public function enterNode(Node $node): void
	{
		if ($node instanceof Node\Stmt\Namespace_) {
			$this->resetCurrentClass();
			$this->resetCurrentFunction();
			$this->currentNamespace = $node->name->toString();
		} elseif ($node instanceof Node\Stmt\Use_) {
			$this->collectUseStatement($node);
		} elseif ($node instanceof Node\Stmt\Class_) {
			$this->resetCurrentClass();
			$this->resetCurrentFunction();
			$this->collectClass($node);
		} elseif ($node instanceof Node\Expr\New_) {
			$this->collectNew($node);
		}
	}

	public function collectNew(Node\Expr\New_ $node): void
	{
		$reference = $this->getReference($node);

		if ( ! array_key_exists($reference, $this->references) ) {
			$this->references[$reference] = new ReferenceItem( $reference );
		}

		$use = new ReferenceUse( $this->path, $node->getStartLine() );

		$parentNode = $node->getAttribute('parent');

		if ($parentNode instanceof Node\Stmt\Return_) {
			$use->returned = true;
		} elseif ($parentNode instanceof Node\Expr\Assign) {
			$use->assigned = true;
		}

		$this->references[$reference]->uses[] = $use;
	}

	private function getReference(Node\Expr\New_ $node): string
	{
		$reference = $node->class->toString();

		if ($node->class instanceof Node\Name\FullyQualified) {
			$reference = '\\' . $reference;
		} elseif (array_key_exists($reference, $this->useStatements)) {
			$reference = $this->useStatements[$reference];
		} elseif ($this->currentNamespace && $reference[0] === strtoupper($reference[0]) ) {
			$reference = $this->currentNamespace . '\\' . $reference;
		}

		return $reference;
	}
}
