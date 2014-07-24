<?php
/**
 * Date: 23.05.14
 * Time: 16:53
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
 * Class Constraint
 * @package nightlinus\OracleMeta
 */
class Constraint
{
    const TYPE_CHECK = 'C';

    const TYPE_FOREIGN_KEY = 'R';

    const TYPE_PRIMARY_KEY = 'P';

    const TYPE_UNIQUE = 'U';

    const TYPE_WITH_CHECK = 'V';

    const TYPE_WITH_READ_ONLY = 'O';

    protected $columns = [ ];

    protected $name;

    protected $referenceConstraint;

    protected $referenceOwner;

    protected $status;

    protected $tableName;

    protected $type;

    protected $owner;

    /**
     * @param $name
     * @param $referenceConstraint
     * @param $referenceOwner
     * @param $status
     * @param $type
     * @param $table
     * @param $owner
     */
    public function __construct($name, $referenceConstraint, $referenceOwner, $status, $type, $table, $owner)
    {
        $this->name = $name;
        $this->referenceConstraint = $referenceConstraint;
        $this->referenceOwner = $referenceOwner;
        $this->status = $status;
        $this->type = $type;
        $this->tableName = $table;
        $this->owner = $owner;
    }

    /**
     * @param ConstraintColumn $constraintColumn
     *
     * @return $this
     */
    public function addColumn(ConstraintColumn $constraintColumn)
    {
        $name = $constraintColumn->getName();
        $this->columns[ $name ] = $constraintColumn;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getColumnNames()
    {
        $names = [ ];
        foreach ($this->getColumns() as $column) {
            $names[ ] = $column->getName();
        }

        return $names;
    }

    /**
     * @return ConstraintColumn[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getReferenceConstraint()
    {
        return $this->referenceConstraint;
    }

    /**
     * @return string
     */
    public function getReferenceOwner()
    {
        return $this->referenceOwner;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isCheck()
    {
        return $this->getType() === self::TYPE_CHECK ? true : false;
    }

    /**
     * @return bool
     */
    public function isForeignKey()
    {
        return $this->getType() === self::TYPE_FOREIGN_KEY ? true : false;
    }

    /**
     * @return bool
     */
    public function isPrimaryKey()
    {
        return $this->getType() === self::TYPE_PRIMARY_KEY ? true : false;
    }

    /**
     * @return bool
     */
    public function isUnique()
    {
        return $this->getType() === self::TYPE_UNIQUE ? true : false;
    }
}
