<?php
/**
 * MIT License
 * Copyright (c) 2018 Dogan Ucar, <dogan@dogan-ucar.de>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace doganoo\DPS\Handler;

use doganoo\DPS\Common\IDatabase;
use doganoo\DPS\Common\ITable;
use doganoo\PHPAlgorithms\Algorithm\Sorting\TopologicalSort;
use doganoo\PHPAlgorithms\Datastructure\Graph\Graph\DirectedGraph;
use doganoo\PHPAlgorithms\Datastructure\Graph\Graph\Node;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class DependencyHandler
 *
 * @package Handler
 */
class DependencySolver{
	/** @var IDatabase|null $database */
	private $database = null;
	/** @var null|DirectedGraph $graph */
	private $graph = null;

	/**
	 * DependencyHandler constructor.
	 *
	 * @param IDatabase $database
	 *
	 * @throws \doganoo\PHPAlgorithms\common\Exception\InvalidGraphTypeException
	 * @throws \doganoo\PHPAlgorithms\Common\Exception\IndexOutOfBoundsException
	 */
	public function __construct(IDatabase $database){
		$this->database = $database;
		$this->toGraph();
		$this->addEdges();
	}

	/**
	 * transforms a list of tables to a directed graph
	 *
	 * @throws \doganoo\PHPAlgorithms\common\Exception\InvalidGraphTypeException
	 * @return void
	 */
	private function toGraph(): void{
		$graph = new DirectedGraph();
		/** @var ITable $table */
		foreach($this->database->getTables() as $table){
			$graph->createNode($table);
		}
		$this->graph = $graph;
	}

	/**
	 * adds edges to the graph. The edges are the dependencies (foreign key constraints)
	 * between tables.
	 * Note that a dependency is not added to the graph if a inverse dependency is already present
	 *
	 * @return void
	 * @throws \doganoo\PHPAlgorithms\Common\Exception\IndexOutOfBoundsException
	 */
	private function addEdges(): void{
		/** @var ArrayList $dependency */
		foreach($this->database->getDependencies() as $dependency){
			$first      = $dependency->get(0);
			$second     = $dependency->get(1);
			$firstNode  = new Node($first);
			$secondNode = new Node($second);
			$this->graph->addEdge($firstNode, $secondNode);
		}
	}

	/**
	 * solves database dependencies and returns it as a list.
	 * Note that a dependency is not added to the graph if a inverse dependency is already present.
	 * If a dependency is missing, make sure that you do not have a circular dependency.
	 *
	 * @throws \doganoo\PHPAlgorithms\Common\Exception\InvalidGraphTypeException
	 * @throws \doganoo\PHPAlgorithms\common\Exception\InvalidKeyTypeException
	 * @throws \doganoo\PHPAlgorithms\common\Exception\UnsupportedKeyTypeException
	 * @return ArrayList
	 */
	public function perform(): ArrayList{
		$sort  = new TopologicalSort();
		$stack = $sort->sort($this->graph);
		$list  = new ArrayList();
		while(!$stack->isEmpty()){
			/** @var Node $value */
			$value = $stack->pop();
			$list->add($value->getValue());
		}
		return $list;
	}
}