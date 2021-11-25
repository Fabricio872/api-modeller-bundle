<?php

declare(strict_types=1);

namespace Fabricio872\ApiModeller\Services;

use Doctrine\Common\Collections\ArrayCollection;

interface ModellerInterface
{
    /**
     * @return ArrayCollection|mixed
     */
    public function getData(Repo $repo);
}
