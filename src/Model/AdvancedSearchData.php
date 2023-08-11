<?php

namespace App\Model;

use App\Entity\Category;
use Doctrine\Common\Collections\Collection;

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

    public ?Collection $varieties = null;

    public ?Collection $colors = null;

    public ?Collection $lustres = null;
}