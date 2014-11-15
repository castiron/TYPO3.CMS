<?php
namespace TYPO3\CMS\Fluid\Tests\Unit\Core\Parser\SyntaxTree;

/*                                                                        *
 * This script is backported from the TYPO3 Flow package "TYPO3.Fluid".   *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\BooleanNode;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\RootNode;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\TextNode;

/**
 * Test case
 */
class BooleanNodeTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode
	 */
	protected $viewHelperNode;

	/**
	 * @var \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface
	 */
	protected $renderingContext;

	/**
	 * Setup fixture
	 */
	public function setUp() {
		$this->renderingContext = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\Rendering\\RenderingContextInterface');
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Fluid\Core\Parser\Exception
	 */
	public function havingMoreThanThreeElementsInTheSyntaxTreeThrowsException() {
		$rootNode = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\RootNode');
		$rootNode->expects($this->once())->method('getChildNodes')->will($this->returnValue(array(1, 2, 3, 4)));

		new BooleanNode($rootNode);
	}

	/**
	 * @test
	 */
	public function comparingEqualNumbersReturnsTrue() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('5'));
		$rootNode->addChildNode(new TextNode('=='));
		$rootNode->addChildNode(new TextNode('5'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function comparingUnequalNumbersReturnsFalse() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('5'));
		$rootNode->addChildNode(new TextNode('=='));
		$rootNode->addChildNode(new TextNode('3'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function notEqualReturnsFalseIfNumbersAreEqual() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('5'));
		$rootNode->addChildNode(new TextNode('!='));
		$rootNode->addChildNode(new TextNode('5'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function notEqualReturnsTrueIfNumbersAreNotEqual() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('5'));
		$rootNode->addChildNode(new TextNode('!='));
		$rootNode->addChildNode(new TextNode('3'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function oddNumberModulo2ReturnsTrue() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('43'));
		$rootNode->addChildNode(new TextNode('%'));
		$rootNode->addChildNode(new TextNode('2'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function evenNumberModulo2ReturnsFalse() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('42'));
		$rootNode->addChildNode(new TextNode('%'));
		$rootNode->addChildNode(new TextNode('2'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function greaterThanReturnsTrueIfNumberIsReallyGreater() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('10'));
		$rootNode->addChildNode(new TextNode('>'));
		$rootNode->addChildNode(new TextNode('9'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function greaterThanReturnsFalseIfNumberIsEqual() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('10'));
		$rootNode->addChildNode(new TextNode('>'));
		$rootNode->addChildNode(new TextNode('10'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function greaterOrEqualsReturnsTrueIfNumberIsReallyGreater() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('10'));
		$rootNode->addChildNode(new TextNode('>='));
		$rootNode->addChildNode(new TextNode('9'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function greaterOrEqualsReturnsTrueIfNumberIsEqual() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('10'));
		$rootNode->addChildNode(new TextNode('>='));
		$rootNode->addChildNode(new TextNode('10'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function greaterOrEqualsReturnFalseIfNumberIsSmaller() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('10'));
		$rootNode->addChildNode(new TextNode('>='));
		$rootNode->addChildNode(new TextNode('11'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function lessThanReturnsTrueIfNumberIsReallyless() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('9'));
		$rootNode->addChildNode(new TextNode('<'));
		$rootNode->addChildNode(new TextNode('10'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function lessThanReturnsFalseIfNumberIsEqual() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('10'));
		$rootNode->addChildNode(new TextNode('<'));
		$rootNode->addChildNode(new TextNode('10'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function lessOrEqualsReturnsTrueIfNumberIsReallyLess() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('9'));
		$rootNode->addChildNode(new TextNode('<='));
		$rootNode->addChildNode(new TextNode('10'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function lessOrEqualsReturnsTrueIfNumberIsEqual() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('10'));
		$rootNode->addChildNode(new TextNode('<='));
		$rootNode->addChildNode(new TextNode('10'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function lessOrEqualsReturnFalseIfNumberIsBigger() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('11'));
		$rootNode->addChildNode(new TextNode('<='));
		$rootNode->addChildNode(new TextNode('10'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function lessOrEqualsReturnFalseIfComparingWithANegativeNumber() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('11 <= -2.1'));
		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function notEqualReturnsFalseIfComparingMatchingStrings() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('\'stringA\' != "stringA"'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function notEqualReturnsTrueIfComparingNonMatchingStrings() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('\'stringA\' != \'stringB\''));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function equalsReturnsFalseIfComparingNonMatchingStrings() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('\'stringA\' == \'stringB\''));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function equalsReturnsTrueIfComparingMatchingStrings() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('\'stringA\' == "stringA"'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function equalsReturnsTrueIfComparingMatchingStringsWithEscapedQuotes() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('\'\\\'stringA\\\'\' == \'\\\'stringA\\\'\''));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function equalsReturnsFalseIfComparingStringWithNonZero() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('\'stringA\' == 42'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function equalsReturnsTrueIfComparingStringWithZero() {
		$rootNode = new RootNode();
		$rootNode->addChildNode(new TextNode('\'stringA\' == 0'));

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function objectsAreComparedStrictly() {
		$object1 = new \stdClass();
		$object2 = new \stdClass();

		$rootNode = new RootNode();

		$object1Node = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ObjectAccessorNode', array('evaluate'), array('foo'));
		$object1Node->expects($this->any())->method('evaluate')->will($this->returnValue($object1));

		$object2Node = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ObjectAccessorNode', array('evaluate'), array('foo'));
		$object2Node->expects($this->any())->method('evaluate')->will($this->returnValue($object2));

		$rootNode->addChildNode($object1Node);
		$rootNode->addChildNode(new TextNode('=='));
		$rootNode->addChildNode($object2Node);

		$booleanNode = new BooleanNode($rootNode);
		$this->assertFalse($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function objectsAreComparedStrictlyInUnequalComparison() {
		$object1 = new \stdClass();
		$object2 = new \stdClass();

		$rootNode = new RootNode();

		$object1Node = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ObjectAccessorNode', array('evaluate'), array('foo'));
		$object1Node->expects($this->any())->method('evaluate')->will($this->returnValue($object1));

		$object2Node = $this->getMock('TYPO3\\CMS\\Fluid\\Core\\Parser\\SyntaxTree\\ObjectAccessorNode', array('evaluate'), array('foo'));
		$object2Node->expects($this->any())->method('evaluate')->will($this->returnValue($object2));

		$rootNode->addChildNode($object1Node);
		$rootNode->addChildNode(new TextNode('!='));
		$rootNode->addChildNode($object2Node);

		$booleanNode = new BooleanNode($rootNode);
		$this->assertTrue($booleanNode->evaluate($this->renderingContext));
	}

	/**
	 * @test
	 */
	public function convertToBooleanProperlyConvertsValuesOfTypeBoolean() {
		$this->assertFalse(BooleanNode::convertToBoolean(FALSE));
		$this->assertTrue(BooleanNode::convertToBoolean(TRUE));
	}

	/**
	 * @test
	 */
	public function convertToBooleanProperlyConvertsValuesOfTypeString() {
		$this->assertFalse(BooleanNode::convertToBoolean(''));
		$this->assertFalse(BooleanNode::convertToBoolean('false'));
		$this->assertFalse(BooleanNode::convertToBoolean('FALSE'));

		$this->assertTrue(BooleanNode::convertToBoolean('true'));
		$this->assertTrue(BooleanNode::convertToBoolean('TRUE'));
	}

	/**
	 * @test
	 */
	public function convertToBooleanProperlyConvertsNumericValues() {
		$this->assertFalse(BooleanNode::convertToBoolean(FALSE));
		$this->assertTrue(BooleanNode::convertToBoolean(TRUE));

		$this->assertFalse(BooleanNode::convertToBoolean(0));
		$this->assertFalse(BooleanNode::convertToBoolean('0'));

		$this->assertFalse(BooleanNode::convertToBoolean(0.0));
		$this->assertFalse(BooleanNode::convertToBoolean('0.0'));

		$this->assertFalse(BooleanNode::convertToBoolean(0x0));
		$this->assertFalse(BooleanNode::convertToBoolean('0x0'));

		$this->assertTrue(BooleanNode::convertToBoolean(0x1));
		$this->assertTrue(BooleanNode::convertToBoolean('0x1'));

		$this->assertFalse(BooleanNode::convertToBoolean(0e0));
		$this->assertFalse(BooleanNode::convertToBoolean('0e0'));

		$this->assertTrue(BooleanNode::convertToBoolean(1e0));
		$this->assertTrue(BooleanNode::convertToBoolean('1e0'));

		$this->assertTrue(BooleanNode::convertToBoolean(-1));
		$this->assertTrue(BooleanNode::convertToBoolean('-1'));

		$this->assertTrue(BooleanNode::convertToBoolean(-0.5));
		$this->assertTrue(BooleanNode::convertToBoolean('-0.5'));

		$this->assertTrue(BooleanNode::convertToBoolean(1));
		$this->assertTrue(BooleanNode::convertToBoolean('1'));

		$this->assertTrue(BooleanNode::convertToBoolean(0.5));
		$this->assertTrue(BooleanNode::convertToBoolean('0.5'));

		$this->assertTrue(BooleanNode::convertToBoolean(0x1));
		$this->assertTrue(BooleanNode::convertToBoolean('0x10'));
	}

	/**
	 * @test
	 */
	public function convertToBooleanProperlyConvertsValuesOfTypeArray() {
		$this->assertFalse(BooleanNode::convertToBoolean(array()));

		$this->assertTrue(BooleanNode::convertToBoolean(array('foo')));
		$this->assertTrue(BooleanNode::convertToBoolean(array('foo' => 'bar')));
	}

	/**
	 * @test
	 */
	public function convertToBooleanProperlyConvertsObjects() {
		$this->assertFalse(BooleanNode::convertToBoolean(NULL));

		$this->assertTrue(BooleanNode::convertToBoolean(new \stdClass()));
	}
}
