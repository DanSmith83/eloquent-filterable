<?php

namespace DanSmith\Filterable;

use Illuminate\Database\Eloquent\Builder;

interface Filter {

    public function handle(Builder $query, $value);
}