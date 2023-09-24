<?php

namespace NamaaSolutions\CrudGenerator\Base;

use NamaaSolutions\CrudGenerator\Traits\Scopes;
use NamaaSolutions\CrudGenerator\Traits\TranslatableTrait;
use Illuminate\Database\Eloquent\Model as ModelBase;

abstract class Model extends ModelBase
{
    use Scopes, TranslatableTrait;

    public function getIndexExtraData($params = [])
    {
        $data = [];

        return $data;
    }
}
