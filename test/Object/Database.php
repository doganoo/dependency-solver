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

namespace Object;

use doganoo\DPS\Common\IDatabase;
use doganoo\DPS\Common\ITable;
use doganoo\PHPAlgorithms\Datastructure\Lists\ArrayLists\ArrayList;

/**
 * Class Database
 *
 * @package Object
 */
class Database implements IDatabase{
	/**
	 * schema name
	 *
	 * @return string
	 */
	public function getName(): string{
		return "DPS";
	}

	/**
	 * returns all tables that are part of this schema
	 *
	 * @return ArrayList
	 */
	public function getTables(): ArrayList{
		$arrayList = new ArrayList();
		$arrayList->add($this->getTable("a"));
		$arrayList->add($this->getTable("b"));
		$arrayList->add($this->getTable("c"));
		$arrayList->add($this->getTable("d"));
		$arrayList->add($this->getTable("e"));
		$arrayList->add($this->getTable("f"));
		return $arrayList;
	}

	/**
	 * returns a single table instance
	 *
	 * @param string $name
	 *
	 * @return ITable
	 */
	private function getTable(string $name): ITable{
		$table = new Table($name);
		return $table;
	}

	/**
	 * returns a list of lists containing all
	 * databases that depends on each other.
	 * The second element in the list depends on
	 * the first element.
	 *
	 * @return ArrayList
	 */
	public function getDependencies(): ArrayList{
		$dependencies = new ArrayList();
		$dependencies->add($this->getDependency("a", "d"));
		$dependencies->add($this->getDependency("f", "b"));
		$dependencies->add($this->getDependency("b", "d"));
		$dependencies->add($this->getDependency("f", "a"));
		$dependencies->add($this->getDependency("d", "c"));
		return $dependencies;
	}

	/**
	 * creates a dependency from $second to $first, meaning that $second
	 * depends on $first.
	 *
	 * @param string $first
	 * @param string $second
	 *
	 * @return ArrayList
	 */
	private function getDependency(string $first, string $second): ArrayList{
		$dependency = new ArrayList();
		$dependency->add($this->getTable($first));
		$dependency->add($this->getTable($second));
		return $dependency;
	}
}