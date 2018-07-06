<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Permission extends Pivot
{
    const Read = 'read';
    const Write = 'write';
}
