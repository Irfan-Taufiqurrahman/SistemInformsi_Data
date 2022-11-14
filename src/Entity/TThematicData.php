<?php

namespace App\Entity;

use App\Repository\TThematicDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TThematicDataRepository::class)
 */
class TThematicData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TMainData::class, inversedBy="name")
     */
    private $Thematic;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThematic(): ?TMainData
    {
        return $this->Thematic;
    }

    public function setThematic(?TMainData $Thematic): self
    {
        $this->Thematic = $Thematic;

        return $this;
    }
}
