<?php
/**
 * Date: 18.07.14
 * Time: 8:47
 *
 * @category
 * @package  OracleMeta
 * @author   nightlinus <user@localhost>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version
 * @link
 */

namespace nightlinus\OracleMeta;

/**
 * Class Sequence
 * @package nightlinus\OracleMeta
 */
class Sequence
{

    /**
     * @type int
     */
    protected $cacheSize;

    /**
     * @type int
     */
    protected $increment;

    /**
     * @type bool
     */
    protected $isCyclic;

    /**
     * @type bool
     */
    protected $isOrdered;

    /**
     * @type int
     */
    protected $lastValue;

    /**
     * @type int
     */
    protected $max;

    /**
     * @type  int
     */
    protected $min;

    /**
     * @type string
     */
    protected $name;

    /**
     * @type  string
     */
    protected $owner;

    /**
     * @param $name
     * @param $owner
     * @param $min
     * @param $max
     * @param $increment
     * @param $isCyclic
     * @param $isOrdered
     * @param $cacheSize
     * @param $lastValue
     */
    public function __construct($name, $owner, $min, $max, $increment, $isCyclic, $isOrdered, $cacheSize, $lastValue)
    {
        $this->cacheSize = $cacheSize;
        $this->increment = $increment;
        $this->isCyclic = $isCyclic;
        $this->isOrdered = $isOrdered;
        $this->lastValue = $lastValue;
        $this->max = $max;
        $this->min = $min;
        $this->name = $name;
        $this->owner = $owner;
    }

    /**
     * @return int
     */
    public function getCacheSize()
    {
        return $this->cacheSize;
    }

    /**
     * @return int
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * @return bool
     */
    public function getIsCyclic()
    {
        return $this->isCyclic;
    }

    /**
     * @return bool
     */
    public function getIsOrdered()
    {
        return $this->isOrdered;
    }

    /**
     * @return int
     */
    public function getLastValue()
    {
        return $this->lastValue;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
