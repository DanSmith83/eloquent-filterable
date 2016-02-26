<?php

namespace DanSmith\Filterable;

trait Filterable
{

    /**
     * @param       $query
     * @param array $parameters
     * @return mixed
     */
    public function scopeFilter($query, $parameters = [])
    {
        if (!$this->filterable) {
            return $query;
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