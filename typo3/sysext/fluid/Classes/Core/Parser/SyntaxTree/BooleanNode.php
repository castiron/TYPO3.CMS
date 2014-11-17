<?php
namespace TYPO3\CMS\Fluid\Core\Parser\SyntaxTree;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * A node which is used inside boolean arguments
 */
class BooleanNode extends AbstractNode {

	const JUNCTION_AND = 'AND';
	const JUNCTION_OR  = 'OR';

	/**
	 * List of junctions for complex boolean expressions
	 *
	 * @var array
	 */
	static protected $junctions = array('&&','||','AND','OR','and','or');


	/**
	 * BooleanNodes or BooleanUnitNodes bound to the current junction
	 *
	 * @var AbstractNode
	 */
	protected $expressions;

	/**
	 * The junction holding together BooleanUnitNodes or BooleanNodes.
	 *
	 * There can be only one for each BooleanNode:
	 * a && b && c   => AND(a,b,c)
	 * a && b || c   => OR(AND(a,b), c)
	 * a && (b || c) => AND(a,OR(b,c))
	 * a             => a
	 *
	 * @var string
	 */
	protected $junction;

	/**
	 * Constructor. Parses the syntax tree node and fills $this->leftSide, $this->rightSide,
	 * $this->comparator and $this->syntaxTreeNode.
	 *
	 * @param AbstractNode $syntaxTreeNode
	 * @throws \TYPO3\CMS\Fluid\Core\Parser\Exception
	 */
	public function __construct(AbstractNode $syntaxTreeNode) {
		$childNodes = $syntaxTreeNode->getChildNodes();

		if (count($childNodes) === 0) {
			// In this case, we do not have child nodes; i.e. the current SyntaxTreeNode
			// is a text node with a literal comparison like "1 == 1"
			$childNodes = array($syntaxTreeNode);
		}

		$this->expressions = new RootNode();

		$regex = '/' . implode('|',array_map('preg_quote', self::$junctions)) . '/x';


		$units = new RootNode();

		$clauses = array();
		$currentClause = new \stdClass();
		$currentClause->nodes = array();
		$currentClause->junction = NULL;

		foreach ($childNodes as $childNode) {
			$matches = NULL;
			if ($childNode instanceof TextNode && preg_match($regex, $childNode->getText(), $matches)) {

				$currentJunction = self::reduceJunction($matches[0]);
//				if ($currentClause->junction) {
//					if ($currentClause->junction != $currentJunction) {
//
//					}
//				} else {
//					$currentClause->junction = $currentJunction;
//				}

				$currentClause->junction = $currentJunction;
				$clauses[] = $currentClause;
				$currentClause = new \stdClass();
				$currentClause->nodes = array();


				$remainingText = trim(str_replace($matches[0], '', $childNode->getText()));
				if ($remainingText) {
					$currentClause->nodes[] = new TextNode($remainingText);
				}

			} else {
				$currentClause->nodes[] = $childNode;
			}
		}
		$clauses[] = $currentClause;

		$a = 1;
//		$this->expressions->addChildNode(new BooleanUnitNode($units));
	}


	/**
	 * @return AbstractNode|RootNode
	 * @internal
	 */
	public function getExpressions() {
		return $this->expressions->getChildNodes();
	}

	/**
	 * @return string
	 * @internal
	 */
	public function getJunction() {
		return $this->junction;
	}

	/**
	 * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return boolean the boolean value
	 */
	public function evaluate(\TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
		if (!$this->junction) {
			$booleanUnit = current($this->expressions->getChildNodes());
			return $booleanUnit->evaluate($renderingContext);
		}

		if ($this->junction == self::JUNCTION_OR) {
			foreach ($this->expressions->getChildNodes() as $booleanUnit) {
				if ($booleanUnit->evaluate($renderingContext)) {
					return TRUE;
				}
			}
			return FALSE;
		}

		foreach ($this->expressions->getChildNodes() as $booleanUnit) {
			if (!$booleanUnit->evaluate($renderingContext)) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * @param $str
	 * @return string
	 */
	protected static function reduceJunction($str) {
		switch ($str) {
			case '&&':
			case 'AND':
			case 'and':
				return self::JUNCTION_AND;
			case '||':
			case 'OR':
			case 'or':
				return self::JUNCTION_OR;
		}
	}
}
