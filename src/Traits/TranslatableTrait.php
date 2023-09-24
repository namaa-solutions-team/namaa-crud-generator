<?php

namespace NamaaSolutions\CrudGenerator\Traits;


trait TranslatableTrait
{

    public function getLocaleName($locale)
    {
        return $this->translations->where('locale', $locale)->first()->name;
    }
}
