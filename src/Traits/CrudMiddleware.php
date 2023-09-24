<?php

namespace NamaaSolutions\CrudGenerator\Traits;

trait CrudMiddleware
{
    public function __construct()
    {
        // $this->middleware(['check_permission:read_'.$this->premissionSuffix])->only([...$this->readFunctions ?? [], ...['index', 'show']]);
        // $this->middleware(['check_permission:write_'.$this->premissionSuffix])->only([...$this->writeFunctions ?? [], ...['store', 'update', 'import']]);
        // $this->middleware(['check_permission:delete_'.$this->premissionSuffix])->only(['destroy']);
    }
}
