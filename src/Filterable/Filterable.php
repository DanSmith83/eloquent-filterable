<?php

namespace DanSmith\Filterable;

use DanSmith\Filterable\Exceptions\FilterableException;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * @return mixed
     */
    public function getFilterable()
    {
        return $this->filterable;
    }

    /**
     * @param Builder $query
     * @param array   $parameters
     * @return array|Builder
     * @throws FilterableException
     */
    public function scopeFilter(Builder $query, $parameters = [])
    {
        if (!$this->getFilterable()) {
            throw new FilterableException('Filterable attributes must be set');
        }

        if (empty($parameters)) {
            return $query;
        }

        return $this->filterParameters($query, $parameters);
    }

    /**
     * @param $parameters
     * @return array
     */
    private function getWhereFilters($parameters)
    {
        return array_only($parameters, array_filter($this->getFilterable(), function($v, $k) use ($parameters) {
            return is_numeric($k) && isset($parameters[$v]) && strlen($parameters[$v]) > 0;
        }, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param $query
     * @param $parameters
     * @return mixed
     */
    private function applyWhereFilters(Builder $query, $parameters)
    {
        $query->where($this->getWhereFilters($parameters));

        return $query;
    }

    /**
     * @param $parameters
     * @return array
     */
    private function getCallableFilters($parameters)
    {
        return array_filter($this->getFilterable(), function($v, $k) use ($parameters) {
            return is_callable($v) && isset($parameters[$k]) && strlen($parameters[$k]) > 0;
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param $query
     * @param $parameters
     * @return mixed
     */
    private function applyCallableFilters(Builder $query, $parameters)
    {
        foreach ($this->getCallableFilters($parameters) as $k => $callback) {
            $query = $callback($query, $parameters[$k]);
        }

        return $query;
    }

    /**
     * @param $parameters
     * @return array
     */
    private function getClassFilters($parameters)
    {
        return array_filter($this->getFilterable(), function($v, $k) use ($parameters) {
            return  is_string($v) &&
            class_exists($v) &&
            isset($parameters[$k]) &&
            strlen($parameters[$k]) > 0;
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param $query
     * @param $parameters
     * @return mixed
     */
    private function applyClassFilters(Builder $query, $parameters)
    {
        foreach ($this->getClassFilters($parameters) as $k => $class) {
            $query = $this->applyClassFilter($query, new $class, $parameters[$k]);
        }

        return $query;
    }

    /**
     * @param        $query
     * @param Filter $filter
     * @param        $value
     * @return mixed
     */
    private function applyClassFilter(Builder $query, Filter $filter, $value)
    {
        $query = $filter->handle($query, $value);

        return $query;
    }

    /**
     * @param $query
     * @param $parameters
     * @return mixed
     */
    private function filterParameters(Builder $query, $parameters)
    {
        $query = $this->applyWhereFilters($query, $parameters);
        $query = $this->applyCallableFilters($query, $parameters);
        $query = $this->applyClassFilters($query, $parameters);

        return $query;
    }
}