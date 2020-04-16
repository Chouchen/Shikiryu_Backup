<?php

namespace Shikiryu\Backup\Backup;

trait IsDistantTrait
{
    public function isLocal()
    {
        return false;
    }

    public function isDistant()
    {
        return true;
    }

    abstract public function retrieve();
}