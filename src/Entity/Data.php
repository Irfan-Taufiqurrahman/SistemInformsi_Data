<?php

namespace App\Entity;

use App\Repository\DataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DataRepository::class)
 */
class Data
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Dataset::class, inversedBy="data")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dataset;

    /**
     * @ORM\ManyToOne(targetEntity=Variable::class, inversedBy="data")
     * @ORM\JoinColumn(nullable=false)
     */
    private $var;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $row_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataset(): ?Dataset
    {
        return $this->dataset;
    }

    public function setDataset(?Dataset $dataset): self
    {
        $this->dataset = $dataset;

        return $this;
    }

    public function getVar(): ?Variable
    {
        return $this->var;
    }

    public function setVar(?Variable $var): self
    {
        $this->var = $var;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRowId(): ?int
    {
        return $this->row_id;
    }

    public function setRowId(int $row_id): self
    {
        $this->row_id = $row_id;

        return $this;
    }
}
