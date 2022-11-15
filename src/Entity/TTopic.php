<?php

namespace App\Entity;

use App\Repository\TTopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TTopicRepository::class)
 */
class TTopic
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
     * @ORM\ManyToOne(targetEntity=TThematicData::class, inversedBy="tTopics")
     */
    private $thematicData;

    /**
     * @ORM\OneToMany(targetEntity=TSubTopic::class, mappedBy="Topic")
     */
    private $tSubTopics;

    public function __construct()
    {
        $this->tSubTopics = new ArrayCollection();
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

    public function getThematicData(): ?TThematicData
    {
        return $this->thematicData;
    }

    public function setThematicData(?TThematicData $thematicData): self
    {
        $this->thematicData = $thematicData;

        return $this;
    }

    /**
     * @return Collection<int, TSubTopic>
     */
    public function getTSubTopics(): Collection
    {
        return $this->tSubTopics;
    }

    public function addTSubTopic(TSubTopic $tSubTopic): self
    {
        if (!$this->tSubTopics->contains($tSubTopic)) {
            $this->tSubTopics[] = $tSubTopic;
            $tSubTopic->setTopic($this);
        }

        return $this;
    }

    public function removeTSubTopic(TSubTopic $tSubTopic): self
    {
        if ($this->tSubTopics->removeElement($tSubTopic)) {
            // set the owning side to null (unless already changed)
            if ($tSubTopic->getTopic() === $this) {
                $tSubTopic->setTopic(null);
            }
        }

        return $this;
    }
}
