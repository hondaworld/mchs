<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyRepository")
 */
class Currency
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $valute_id;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $num_code;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $char_code;

    /**
     * @ORM\Column(type="integer")
     */
    private $nominal;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=4)
     */
    private $value;

    /**
     * @ORM\Column(type="date")
     */
    private $dateofadded;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValuteId(): ?string
    {
        return $this->valute_id;
    }

    public function setValuteId(string $valute_id): self
    {
        $this->valute_id = $valute_id;

        return $this;
    }

    public function getNumCode(): ?string
    {
        return $this->num_code;
    }

    public function setNumCode(string $num_code): self
    {
        $this->num_code = $num_code;

        return $this;
    }

    public function getCharCode(): ?string
    {
        return $this->char_code;
    }

    public function setCharCode(string $char_code): self
    {
        $this->char_code = $char_code;

        return $this;
    }

    public function getNominal(): ?int
    {
        return $this->nominal;
    }

    public function setNominal(int $nominal): self
    {
        $this->nominal = $nominal;

        return $this;
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getDateofadded(): ?\DateTimeInterface
    {
        return $this->dateofadded;
    }

    public function setDateofadded(\DateTimeInterface $dateofadded): self
    {
        $this->dateofadded = $dateofadded;

        return $this;
    }
}
