<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Seller::class, inversedBy="products")
     */
    private $seller;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    #[ArrayShape(['id' => "int|null", 'name' => "null|string", 'price' => "int|null", 'seller' => "null|string", 'address' => "null|string", 'description' => "null|string"])]
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'seller' => $this->getSeller()->getName(),
            'address' => $this->getSeller()->getAddress(),
            'description' => $this->getDescription()
        ];
    }

    public function getSeller(): Seller
    {
        return $this->seller;
    }

    public function setSeller(Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }
}
