<?php
/**
 * Date: 26.05.14
 * Time: 9:05
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
 * Class Schema
 * @package nightlinus\OracleMeta
 */
class Schema
{

    /**
     * @var \nightlinus\OracleDb\Database
     */
    protected $db;

    /**
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * @param Relation $relation
     * @param string $name
     * @param string $type
     * @param int $size
     *
     * @throws Exception
     * @return $this
     */
    public function addColumn(&$relation, $name, $type, $size = null)
    {
        $owner = $relation->getOwner();
        $table = $relation->getName();
        if ($size) {
            $size = "($size)";
        }
        $sql = "ALTER TABLE $owner.$table
                ADD ($name $type $size)";
        $this->db->query($sql);
        $relation = $this->getRelation($table, $owner);

        return $this;
    }

    /**
     * @param Column $column
     *
     * @return $this
     */
    public function deleteColumn($column)
    {
        $owner = $column->getOwner();
        $table = $column->getTableName();
        $name = $column->getName();
        $sql = "ALTER TABLE $owner.$table
                DROP COLUMN $name";

        $this->db->query($sql);

        return $this;
    }

    /**
     * @param Relation $relation
     *
     * @return Column[]
     */
    public function getColumns(Relation $relation)
    {
        $owner = $relation->getOwner();
        $name = $relation->getName();
        $sql = "SELECT t_atc.COLUMN_NAME,
                       t_atc.DATA_DEFAULT,
                       t_atc.DATA_TYPE,
                       t_atc.DATA_LENGTH,
                       t_atc.DATA_PRECISION,
                       t_atc.DATA_SCALE,
                       t_atc.CHAR_LENGTH,
                       t_atc.NULLABLE,
                       t_atc.COLUMN_ID,
                       t_acc.COMMENTS,
                       t_atc.OWNER,
                       t_atc.TABLE_NAME
                FROM ALL_TAB_COLUMNS t_atc
                     JOIN ALL_COL_COMMENTS t_acc
                     ON t_acc.COLUMN_NAME = t_atc.COLUMN_NAME
                      AND t_acc.OWNER = t_atc.OWNER
                      AND t_acc.TABLE_NAME = t_atc.TABLE_NAME
                WHERE t_atc.OWNER = :b_owner
                AND t_atc.TABLE_NAME = :b_name";
        $statement = $this->db->query($sql, [ 'b_name' => $name, 'b_owner' => $owner ]);
        $columns = [ ];
        foreach ($statement as $row) {
            $column = new Column(
                $row[ 'COLUMN_ID' ],
                $row[ 'OWNER' ],
                $row[ 'TABLE_NAME' ],
                $row[ 'DATA_LENGTH' ],
                $row[ 'CHAR_LENGTH' ],
                $row[ 'COLUMN_NAME' ],
                $row[ 'NULLABLE' ],
                $row[ 'DATA_PRECISION' ],
                $row[ 'DATA_SCALE' ],
                $row[ 'DATA_TYPE' ],
                $row[ 'DATA_DEFAULT' ],
                $row[ 'COMMENTS' ]
            );
            $relation->addColumn($column);
            $columns[ $column->getName() ] = $column;
        }

        return $columns;
    }

    /**
     * @param string $name
     * @param string $owner
     *
     * @throws \Exception
     * @return Constraint
     */
    public function getConstraint($name, $owner)
    {
        $sql = "SELECT CONSTRAINT_NAME,
                       CONSTRAINT_TYPE,
                       R_OWNER,
                       R_CONSTRAINT_NAME,
                       STATUS,
                       TABLE_NAME,
                       OWNER
                FROM ALL_CONSTRAINTS
                WHERE OWNER = :b_owner
                  AND CONSTRAINT_NAME = :b_name";
        $statement = $this->db->query($sql, [ 'b_name' => $name, 'b_owner' => $owner ]);
        $row = $statement->fetchOne();
        if ($row === null) {
            throw new \Exception("No such constraint or you dont have enough permissions: $owner.$name");
        }
        $constraint = new Constraint(
            $row[ 'CONSTRAINT_NAME' ],
            $row[ 'R_CONSTRAINT_NAME' ],
            $row[ 'R_OWNER' ],
            $row[ 'STATUS' ],
            $row[ 'CONSTRAINT_TYPE' ],
            $row[ 'TABLE_NAME' ],
            $row[ 'OWNER' ]
        );
        $this->getConstraintColumns($constraint);

        return $constraint;
    }

    /**
     * @param Constraint $constraint
     *
     * @return ConstraintColumn[]
     */
    public function getConstraintColumns(Constraint $constraint)
    {
        $name = $constraint->getName();
        $owner = $constraint->getOwner();
        $sql = "SELECT OWNER,
                       CONSTRAINT_NAME,
                       TABLE_NAME,
                       COLUMN_NAME
                FROM ALL_CONS_COLUMNS
                WHERE CONSTRAINT_NAME = :b_name
                  AND OwNER = :b_owner";
        $statement = $this->db->query($sql, [ 'b_name' => $name , 'b_owner' => $owner]);
        $columns = [ ];
        foreach ($statement as $row) {
            $constraintColumn = new ConstraintColumn(
                $row[ 'COLUMN_NAME' ],
                $row[ 'OWNER' ],
                $row[ 'TABLE_NAME' ],
                $row[ 'CONSTRAINT_NAME' ]
            );
            $constraint->addColumn($constraintColumn);
            $columns[ $constraintColumn->getName() ] = $constraintColumn;
        }

        return $columns;
    }

    /**
     * @param Relation $relation
     *
     * @return Constraint[]
     */
    public function getConstraints(Relation $relation)
    {
        $owner = $relation->getOwner();
        $name = $relation->getName();
        $sql = "SELECT CONSTRAINT_NAME,
                       CONSTRAINT_TYPE,
                       R_OWNER,
                       R_CONSTRAINT_NAME,
                       STATUS,
                       TABLE_NAME,
                       OWNER
                FROM ALL_CONSTRAINTS
                WHERE OWNER = :b_owner
                  AND TABLE_NAME = :b_name";
        $statement = $this->db->query($sql, [ 'b_name' => $name, 'b_owner' => $owner ]);
        $constraints = [ ];
        foreach ($statement as $row) {
            $constraint = new Constraint(
                $row[ 'CONSTRAINT_NAME' ],
                $row[ 'R_CONSTRAINT_NAME' ],
                $row[ 'R_OWNER' ],
                $row[ 'STATUS' ],
                $row[ 'CONSTRAINT_TYPE' ],
                $row[ 'TABLE_NAME' ],
                $row[ 'OWNER' ]
            );
            $relation->addConstraint($constraint);
            $this->getConstraintColumns($constraint);
            $constraints[ $constraint->getName() ] = $constraint;
        }

        return $constraints;
    }

    /**
     * @param Constraint $constraint
     *
     * @return Constraint
     * @throws \Exception
     */
    public function getReferenceConstraint(Constraint $constraint)
    {
        $owner = $constraint->getReferenceOwner();
        $name = $constraint->getReferenceConstraint();

        return $this->getConstraint($name, $owner);
    }

    /**
     * @param string $name
     * @param string $owner
     *
     * @throws Exception
     * @return Relation
     */
    public function getRelation($name, $owner)
    {
        $sql = "SELECT t_at.OWNER,
                       t_at.TABLE_NAME,
                       t_at.TABLESPACE_NAME,
                       t_at.STATUS,
                       t_at.NUM_ROWS,
                       t_atc.COMMENTS
                FROM ALL_TABLES t_at
                     JOIN ALL_TAB_COMMENTS t_atc
                     ON t_atc.OWNER = t_at.OWNER
                      AND t_atc.TABLE_NAME = t_at.TABLE_NAME
                WHERE t_at.OWNER = :b_owner
                AND t_at.TABLE_NAME = :b_name";
        $statement = $this->db->query($sql, [ 'b_name' => $name, 'b_owner' => $owner ]);
        $row = $statement->fetchOne();
        if ($row === null) {
            throw new Exception("No table $owner.$name or you dont have enough permissions");
        }
        $relation = new Relation(
            $row[ 'TABLE_NAME' ],
            $row[ 'OWNER' ],
            $row[ 'TABLESPACE_NAME' ],
            $row[ 'STATUS' ],
            $row[ 'NUM_ROWS' ],
            $row[ 'COMMENTS' ]
        );
        $this->getColumns($relation);
        $this->getConstraints($relation);

        return $relation;
    }

    /**
     * @return array|\Generator
     */
    public function getSchemes()
    {
        $sql = "SELECT USERNAME FROM ALL_USERS";
        $st = $this->db->query($sql);

        return $st->fetchColumn();
    }

    /**
     * @param $name
     * @param $owner
     * @return \array[]
     */
    public function getSequence($name, $owner)
    {
        $sql = "SELECT
                  SEQUENCE_OWNER,
                  SEQUENCE_NAME,
                  MIN_VALUE,
                  MAX_VALUE,
                  INCREMENT_BY,
                  CYCLE_FLAG,
                  ORDER_FLAG,
                  CACHE_SIZE,
                  LAST_NUMBER
                FROM ALL_SEQUENCES
                WHERE SEQUENCE_OWNER = :b_owner
                  AND SEQUENCE_NAME = :b_name";
        $statement = $this->db->query($sql, [ 'b_name' => $name, 'b_owner' => $owner ]);
        $row = $statement->fetchOne();
        $sequence = new Sequence(
            $row[ 'SEQUENCE_NAME' ],
            $row[ 'SEQUENCE_OWNER' ],
            $row[ 'MIN_VALUE' ],
            $row[ 'MAX_VALUE' ],
            $row[ 'INCREMENT_BY' ],
            $row[ 'CYCLE_FLAG' ],
            $row[ 'ORDER_FLAG' ],
            $row[ 'CACHE_SIZE' ],
            $row[ 'LAST_NUMBER' ]
        );

        return $sequence;
    }

    /**
     * @param $owner
     * @return \array[]|\Generator
     */
    public function getSequences($owner)
    {
        $sql = "SELECT
                  SEQUENCE_OWNER,
                  SEQUENCE_NAME
                FROM ALL_SEQUENCES
                WHERE SEQUENCE_OWNER = :b_owner";
        $statement = $this->db->query($sql, [ 'b_owner' => $owner ]);
        $row = $statement->fetchAssoc();

        return $row;
    }

    /**
     * @param $owner
     * @return array|\Generator
     */
    public function getTables($owner)
    {
        $sql = "SELECT TABLE_NAME FROM ALL_TABLES WHERE OWNER = :b_owner";
        $st = $this->db->query($sql, [ 'b_owner' => $owner ]);

        return $st->fetchColumn();
    }

    /**
     * @param Column $column
     * @param string $comment
     * @return $this
     */
    public function setColumnComment($column, $comment)
    {
        $owner = $column->getOwner();
        $name = $column->getName();
        $table = $column->getTableName();
        $sql = "COMMENT ON COLUMN $owner.$table.$name IS '$comment'";
        $this->db->query($sql);

        return $this;
    }

    /**
     * @param Relation $relation
     * @param string $comment
     * @return $this
     */
    public function setTableComment($relation, $comment)
    {
        $owner = $relation->getOwner();
        $name = $relation->getName();
        $sql = "COMMENT ON TABLE $owner.$name IS '$comment'";
        $this->db->query($sql);

        return $this;
    }
}
