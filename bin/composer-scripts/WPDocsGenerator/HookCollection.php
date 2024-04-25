<?php
/**
 * Collects all the WordPress Hooks
 *
 * @package WPDocsGenerator
 */

namespace Viget\ComposerScripts\WPDocsGenerator;

use PhpParser\Node\Expr\FuncCall;

/**
 * HookCollection Class
 */
class HookCollection extends CodeCollection
{
	/**
	 * @param FuncCall $node
	 * @return void
	 */
	public function collectFunctionCall( FuncCall $node ): void
	{
		$name = $node->name->name;
		$hooks = ['do_action', 'apply_filters'];

		if (!in_array($name, $hooks, true)) {
			return;
		}
		$nodeType = $name === 'do_action' ? 'action' : 'filter';

		$hookDocItem = new DocItem($this->path, $node->getStartLine());
//		$this->collectParameters($node->args);
//		$hookDocItem->name = $node->args[0]->value->value;
		$hookDocItem->node = $nodeType;
		$hookDocItem->description = $this->getDescription($node);

		$this->addObject($hookDocItem);
	}

}
