<?php

/**
 * The PHP Database Library.
 *
 * @package dionchaika/database
 * @version 1.0.0
 * @license MIT
 * @author Dion Chaika <dionchaika@gmail.com>
 */

namespace Dionchaika\Database\Query;

class Migration
{
    const TYPE_DROP_TABLE = 0;
    const TYPE_CREATE_TABLE = 1;
    const TYPE_DROP_DATABASE = 2;
    const TYPE_CREATE_DATABASE = 3;

    /**
     * @var int
     */
    protected $type = self::TYPE_DROP_TABLE;

    /**
     * @var mixed[]
     */
    protected $parts = [

        'drop_table'    => null,
        'if_exists'     => false,
        'create_table'  => null,
        'if_not_exists' => false

    ];

    /**
     * @var mixed[]
     */
    protected $columns = [];

    /**
     * @param mixed $tableName
     * @return self
     */
    public function dropTable($tableName): self
    {
        $this->setType(self::TYPE_DROP_TABLE);

        $this->parts['drop_table']
            = $this->compileName($tableName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function dropTableRaw(string $expression): self
    {
        $this->setType(self::TYPE_DROP_TABLE);
        $this->parts['drop_table'] = $expression;

        return $this;
    }

    /**
     * @return self
     */
    public function ifExists(): self
    {
        $this->parts['if_exists'] = true;
        return $this;
    }

    /**
     * @param mixed $tableName
     * @return self
     */
    public function createTable($tableName): self
    {
        $this->setType(self::TYPE_CREATE_TABLE);

        $this->parts['create_table']
            = $this->compileName($tableName);

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function createTableRaw(string $expression): self
    {
        $this->setType(self::TYPE_CREATE_TABLE);
        $this->parts['create_table'] = $expression;

        return $this;
    }

    /**
     * @return self
     */
    public function ifNotExists(): self
    {
        $this->parts['if_not_exists'] = true;
        return $this;
    }

    /**
     * @param mixed $columnName
     * @return self
     */
    public function column($columnName): self
    {
        $this->columns[] = [

            'name'       => $this->compileName($columnName),
            'data_type'  => null,
            'constraint' => null

        ];

        return $this;
    }

    /**
     * @param string $expression
     * @return self
     */
    public function columnRaw(string $expression): self
    {
        $this->columns[] = [

            'name'       => $expression,
            'data_type'  => '',
            'constraint' => ''

        ];

        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function int(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileIntegerDataType('INT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function bigInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileIntegerDataType('UNSIGNED', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function tinyInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileIntegerDataType('TINYINT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function smallInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileIntegerDataType('SMALLINT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param bool     $unsigned
     * @return self
     */
    public function mediumInt(?int $size = null, bool $unsigned = false): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileIntegerDataType('MEDIUMINT', $size, $unsigned);
        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function float(?int $size = null, ?int $digits  = null): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileFloatDataType('FLOAT', $size, $digits);
        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function double(?int $size = null, ?int $digits  = null): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileFloatDataType('DOUBLE', $size, $digits);
        return $this;
    }

    /**
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function decimal(?int $size = null, ?int $digits  = null): self
    {
        $this->columns[count($this->columns) - 1] = $this->compileFloatDataType('DECIMAL', $size, $digits);
        return $this;
    }

    /**
     * @return self
     */
    public function text(): self
    {
        $this->columns[count($this->columns) - 1] = 'TEXT';
        return $this;
    }

    /**
     * @return self
     */
    public function tinyText(): self
    {
        $this->columns[count($this->columns) - 1] = 'TINYTEXT';
        return $this;
    }

    /**
     * @param int $size
     * @return self
     */
    public function char(int $size): self
    {
        $this->columns[count($this->columns) - 1] = 'CHAR('.$size.')';
        return $this;
    }

    /**
     * @param int $size
     * @return self
     */
    public function varchar(int $size): self
    {
        $this->columns[count($this->columns) - 1] = 'VARCHAR('.$size.')';
        return $this;
    }

    /**
     * @param mixed $values
     * @return self
     */
    public function set($values): self
    {
        $values = is_array($values)
            ? $values
            : func_get_args();

        return $this->enum($values);
    }

    /**
     * @param mixed $values
     * @return self
     */
    public function enum($values): self
    {
        $values = is_array($values)
            ? $values
            : func_get_args();

            $this->columns[count($this->columns) - 1]
                = 'ENUM('.implode(', ', array_map(['static', 'compileValue'], $values)).')';

        return $this;
    }

    /**
     * @return self
     */
    public function time(): self
    {
        $this->columns[count($this->columns) - 1] = 'TIME()';
        return $this;
    }

    /**
     * @return self
     */
    public function year(): self
    {
        $this->columns[count($this->columns) - 1] = 'YEAR()';
        return $this;
    }

    /**
     * @return self
     */
    public function date(): self
    {
        $this->columns[count($this->columns) - 1] = 'DATE()';
        return $this;
    }

    /**
     * @return self
     */
    public function datetime(): self
    {
        $this->columns[count($this->columns) - 1] = 'DATETIME()';
        return $this;
    }

    /**
     * @return self
     */
    public function timestamp(): self
    {
        $this->columns[count($this->columns) - 1] = 'TIMESTAMP()';
        return $this;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        switch ($this->type) {
            case self::TYPE_DROP_TABLE:
                return $this->getSqlForDropTable();
            case self::TYPE_CREATE_TABLE:
                return $this->getSqlForCreateTable();
            case self::TYPE_DROP_DATABASE:
                return $this->getSqlForDropDatabase();
            case self::TYPE_CREATE_DATABASE:
                return $this->getSqlForCreateDatabase();
            default:
                return '';
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getSql();
    }

    /**
     * @param int $type
     * @return void
     */
    protected function setType(int $type): void
    {
        $this->type = $type;
        $this->columns = [];

        $this->migrationParts['drop_table']    = null;
        $this->migrationParts['if_exists']     = false;
        $this->migrationParts['create_table']  = null;
        $this->migrationParts['if_not_exists'] = false;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function quoteName(string $name): string
    {
        return ('*' === $name)
            ? $name
            : '`'.str_replace('`', '\\`', $name).'`';
    }

    /**
     * @param string $string
     * @return string
     */
    protected function quoteString(string $string): string
    {
        return '\''.str_replace('\'', '\\\'', $string).'\'';
    }

    /**
     * @param mixed $name
     * @return string
     */
    protected function compileName($name): string
    {
        $name = (string)$name;

        if (preg_match('/\s+as\s+/i', $name)) {
            [$name, $alias] = array_filter(preg_split('/\s+as\s+/i', $name, 2));
        } else {
            $alias = null;
        }

        $name = implode('.', array_map(['static', 'quoteName'], preg_split('/\s*\.\s*/', $name, 3)));
        if (!empty($alias)) {
            $name .= ' AS '.$this->quoteName($alias);
        }

        return $name;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function compileValue($value): string
    {
        if (null === $value) {
            return 'NULL';
        }

        if (true === $value) {
            return 'TRUE';
        }

        if (false === $value) {
            return 'FALSE';
        }

        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }

        $value = (string)$value;

        if ('?' === $value || 0 === strpos($value, ':')) {
            return $value;
        }

        return $this->quoteString($value);
    }

    /**
     * @param string   $name
     * @param int|null $size
     * @param bool     $unsigned
     * @return string
     */
    protected function compileIntegerDataType(string $name, ?int $size = null, bool $unsigned = false): string
    {
        $integerDataType = $name.'(';
        if (null !== $size) {
            $integerDataType .= $size;
            if ($unsigned) {
                $integerDataType .= ' UNSIGNED';
            }
        }

        return $integerDataType.')';
    }

    /**
     * @param string   $name
     * @param int|null $size
     * @param int|null $digits
     * @return self
     */
    public function compileFloatDataType(string $name, ?int $size = null, ?int $digits  = null): self
    {
        $floatDataType = $name.'(';
        if (null !== $size) {
            $floatDataType .= $size;
            if (null !== $digits) {
                $floatDataType .= ', '.$digits;
            }
        }

        return $floatDataType.')';
    }

    /**
     * @return string
     */
    protected function getSqlForDropTable(): string
    {
        $sql = ($this->parts['if_exists'] ? 'DROP TABLE IF EXISTS ' : 'DROP TABLE ')
            .$this->parts['drop_table'];

        return $sql.';';
    }
}