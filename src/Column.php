<?php
/**
 * Date: 23.05.14
 * Time: 16:52
 *
 * @category
 * @package  OracleDb
 * @author   nightlinus <user@localhost>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version
 * @link
 */

namespace nightlinus\OracleMeta;

/**
 * Class Column
 *
 * @package nightlinus\OracleMeta
 */
class Column
{

    const YES = 'Y';

    protected $charLength;

    protected $comment;

    protected $default;

    protected $id;

    protected $length;

    protected $name;

    protected $nullable;

    protected $owner;

    protected $precision;

    protected $scale;

    protected $tableName;

    protected $type;

    /**
     * @param $id
     * @param $owner
     * @param $tableName
     * @param $length
     * @param $charLength
     * @param $name
     * @param $nullable
     * @param $precision
     * @param $scale
     * @param $type
     * @param $default
     * @param $comment
     */
    public function __construct(
        $id,
        $owner,
        $tableName,
        $length,
        $charLength,
        $name,
        $nullable,
        $precision,
        $scale,
        $type,
        $default,
        $comment
    ) {
        $this->id = $id;
        $this->length = $length;
        $this->name = $name;
        $this->nullable = $nullable;
        $this->precision = $precision;
        $this->scale = $scale;
        $this->type = $type;
        $this->comment = $comment;
        $this->owner = $owner;
        $this->tableName = $tableName;
        $this->default = $default;
        $this->charLength = $charLength;
    }

    /**
     * @return mixed
     */
    public function getCharLength()
    {
        return $this->charLength;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return mixed
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return mixed
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @return string
     */
    public function getShortComment()
    {
        $comment = explode("\n", $this->getComment());

        return $comment[ 0 ];
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        $value = strtoupper($this->nullable);

        return $value === self::YES ? true : false;
    }
}
