<?php

namespace App\Models;

use Homeful\Properties\Models\Project as BaseProject;

class Project extends BaseProject
{
    protected $connection = 'properties-pgsql';
    protected $table = 'projects';

    protected static function newFactory()
    {
        return \Database\Factories\ProjectFactory::new();
    }
}
