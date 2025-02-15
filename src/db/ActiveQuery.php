<?php

namespace antonyz89\templates\db;

use yii\db\ActiveQuery as ActiveQueryBase;
use yii\db\Expression;
use yii\db\ExpressionInterface;
use yii\helpers\ArrayHelper;

/**
 * @property string $_alias
 */
class ActiveQuery extends ActiveQueryBase
{
    /**
     * @return string
     */
    public function get_alias()
    {
        return self::last($this->getTableNameAndAlias());
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param $array
     * @param $callback
     * @param null $default
     *
     * @return mixed
     */
    private static function last($array, $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? ArrayHelper::value($default) : end($array);
        }

        return ArrayHelper::first(array_reverse($array), $callback, $default);
    }
    
    /**
     * @return static
     */
    public function rand()
    {
        return $this->orderBy(new Expression('RAND()'));
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function select($columns, $options = null)
    {
        return parent::select($this->putAlias($columns), $options);
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function onCondition($condition, $params = [])
    {
        return parent::onCondition($this->putAlias($condition), $params);
    }

    /**
     * {@inheritdoc}
     * @return $this
     */
    public function andOnCondition($condition, $params = []) {
        return parent::andOnCondition($this->putAlias($condition), $params);
    }

    /**
     * {@inheritdoc}
     * @return $this
     */
    public function orOnCondition($condition, $params = []) {
        return parent::orOnCondition($this->putAlias($condition), $params);
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function andFilterWhere(array $params)
    {
        return parent::andFilterWhere($this->putAlias($params));
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function where($condition, $params = [])
    {
        return parent::where($this->putAlias($condition), $params);
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function andWhere($condition, $params = [])
    {
        return parent::andWhere($this->putAlias($condition), $params);
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function orWhere($condition, $params = [])
    {
        return parent::orWhere($this->putAlias($condition), $params);
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function filterWhere(array $condition)
    {
        return parent::filterWhere($this->putAlias($condition));
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function orFilterWhere(array $condition)
    {
        return parent::orFilterWhere($this->putAlias($condition));
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function leftJoin($table, $on = '', $params = [])
    {
        return parent::leftJoin($table, $this->putAlias($on), $params);
    }

    /**
     * {@inheritDoc}
     * @param string|array|false|ExpressionInterface $columns the columns to be grouped by. If `false` empty groupBy
     * @return $this
     */
    public function groupBy($columns)
    {
        if ($columns === false) {
            $this->groupBy = false;
            return $this;
        }

        return parent::groupBy($this->putAlias($columns));
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function addGroupBy($columns)
    {
        if ($this->groupBy === false) {
            return $this;
        }

        return parent::addGroupBy($columns);
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function orderBy($columns)
    {
        return parent::orderBy($this->putAlias($columns));
    }

    /**
     * @param string|Expression|array $params
     * @return array
     */
    protected function putAlias($params)
    {
        if (is_string($params)) {
            return str_replace('@alias', $this->_alias, $params);
        }

        $_params = [];

        if ($params instanceof Expression) {
            return new Expression(
                str_replace('@alias', $this->_alias, $params->expression),
                $params->params
            );
        }

        foreach ($params as $column => $value) {
            if (is_string($column)) {
                $column = str_replace('@alias', $this->_alias, $column);
            }

            if (is_array($value)) {
                $value = $this->putAlias($value);
            } else if (is_string($value)) {
                $value = str_replace('@alias', $this->_alias, $value);
            } else if($value instanceof Expression) {
                $value = new Expression(
                    str_replace('@alias', $this->_alias, $value->expression),
                    $value->params
                );
            }

            $_params[$column] = $value;
        }

        return $_params;
    }
}
