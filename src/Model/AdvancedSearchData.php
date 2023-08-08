<?php

namespace App\Model;

use App\Entity\Category;

class AdvancedSearchData {

    public int $page = 1;

    public ?string $name = null;

    public ?string $formula = null;

    public ?string $crystal_system = null;

    public ?string $density = null;

    public ?int $hardness = null;

    public ?string $fracture = null;

    public ?string $streak = null;

    public ?Category $category = null;
}