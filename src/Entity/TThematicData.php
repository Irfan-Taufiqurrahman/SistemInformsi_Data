<?php

namespace App\Entity;

use App\Repository\TThematicDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity=TMainData::class, inversedBy="thematicData")
     */
    private $mainData;

    /**
     * @ORM\OneToMany(targetEntity=TTopic::class, mappedBy="thematicData", cascade={"persist", "remove"})
     */
    private $tTopics;

    public function __construct()
    {
        $this->tTopics = new ArrayCollection();
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

    public function getMainData(): ?TMainData
    {
        return $this->mainData;
    }

    public function setMainData(?TMainData $mainData): self
    {
        $this->mainData = $mainData;

        return $this;
    }

    /**
     * @return Collection<int, TTopic>
     */
    public function getTTopics(): Collection
    {
        return $this->tTopics;
    }

    public function addTTopic(TTopic $tTopic): self
    {
        if (!$this->tTopics->contains($tTopic)) {
            $this->tTopics[] = $tTopic;
            $tTopic->setThematicData($this);
        }

        return $this;
    }

    public function removeTTopic(TTopic $tTopic): self
    {
        if ($this->tTopics->removeElement($tTopic)) {
            // set the owning side to null (unless already changed)
            if ($tTopic->getThematicData() === $this) {
                $tTopic->setThematicData(null);
            }
        }

        return $this;
    }
}
