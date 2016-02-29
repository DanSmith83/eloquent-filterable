<?php

namespace DanSmith\Filterable;

use DanSmith\Filterable\Exceptions\FilterableException;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{

    /**
     * @param       $query
     * @param array $parameters
     * @return mixed
     */
    public function scopeFilter(Builder $query, $parameters = [])
    {
        if (!$this->filterable) {

            throw new FilterableException('Filterable attributes must be set');
        }

        return $query->where($this->filterParameters($parameters));
    }

    /**
     * @param $parameters
     * @return array
     */
    private function filterParameters($parameters)
    {
        return array_filter(array_only($parameters, $this->filterable), function ($k) {

            if (strlen($k) == 0) {
                return false;
            }

            if ($k == 0) {
                return true;
            }

            return $k;
        });
    }
}