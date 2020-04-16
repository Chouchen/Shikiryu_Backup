<?php

namespace Shikiryu\Backup\Backup;

trait IsLocalTrait
{
    public function isLocal()
    {
        return true;
    }

    public function isDistant()
    {
        return false;
    }
}
