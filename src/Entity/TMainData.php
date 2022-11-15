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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity=TThematicData::class, mappedBy="mainData")
     */
    private $thematicData;

    public function __construct()
    {
        $this->thematicData = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, TThematicData>
     */
    public function getThematicData(): Collection
    {
        return $this->thematicData;
    }

    public function addThematicData(TThematicData $thematicData): self
    {
        if (!$this->thematicData->contains($thematicData)) {
            $this->thematicData[] = $thematicData;
            $thematicData->setMainData($this);
        }

        return $this;
    }

    public function removeThematicData(TThematicData $thematicData): self
    {
        if ($this->thematicData->removeElement($thematicData)) {
            // set the owning side to null (unless already changed)
            if ($thematicData->getMainData() === $this) {
                $thematicData->setMainData(null);
            }
        }

        return $this;
    }
}
