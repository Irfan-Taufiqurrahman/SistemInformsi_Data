<?php

namespace App\Entity;

use App\Repository\TMainDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TMainDataRepository::class)
 */
class TMainData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=TThematicData::class, mappedBy="Thematic")
     */
    private $name;

    public function __construct()
    {
        $this->name = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, TThematicData>
     */
    public function getName(): Collection
    {
        return $this->name;
    }

    public function addName(TThematicData $name): self
    {
        if (!$this->name->contains($name)) {
            $this->name[] = $name;
            $name->setThematic($this);
        }

        return $this;
    }

    public function removeName(TThematicData $name): self
    {
        if ($this->name->removeElement($name)) {
            // set the owning side to null (unless already changed)
            if ($name->getThematic() === $this) {
                $name->setThematic(null);
            }
        }

        return $this;
    }
}
