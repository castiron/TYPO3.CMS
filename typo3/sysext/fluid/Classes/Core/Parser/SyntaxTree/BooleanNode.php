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

		$units = new RootNode();
		foreach ($childNodes as $childNode) {
			$units->addChildNode($childNode);
		}
		$this->expressions->addChildNode(new BooleanUnitNode($units));
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
		$a = 1;
	}



	/**
	 * Convert argument strings to their equivalents. Needed to handle strings with a boolean meaning.
	 *
	 * Must be public and static as it is used from inside cached templates.
	 *
	 * @param mixed $value Value to be converted to boolean
	 * @return boolean
	 */
	static public function convertToBoolean($value) {
		if (is_bool($value)) {
			return $value;
		}

		if (is_integer($value) || is_float($value)) {
			return !empty($value);
		}

		if (is_numeric($value)) {
			return ($value != 0);
		}

		if (is_string($value)) {
			return (!empty($value) && strtolower($value) !== 'false');
		}
		if (is_array($value) || (is_object($value) && $value instanceof \Countable)) {
			return (bool) count($value);
		}
		if (is_object($value)) {
			return TRUE;
		}

		return FALSE;
	}
}
