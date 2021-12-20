<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\EnableAutoMapping()
 */
class Order
{
    const STATUS_PROCESSING = 'processing';
    const STATUS_DELIVERING = 'delivering';
    const STATUS_DELIVERED = 'delivered';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $deliveryAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_PROCESSING;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delivered;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     * @Assert\Count(min=1)
     */
    private $products;

    /**
     * @ORM\ManyToOne(targetEntity=Courier::class, inversedBy="orders")
     */
    private $courier;

    /**
     * @ORM\Column(type="integer")
     */
    private $productsTotal;

    /**
     * @ORM\Column(type="integer")
     */
    private $deliveryTotal;

    /**
     * @ORM\Column(type="integer")
     */
    private $grandTotal;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }


    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getDelivered(): ?\DateTimeInterface
    {
        return $this->delivered;
    }

    public function setDelivered(?\DateTimeInterface $delivered): self
    {
        $this->delivered = $delivered;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function onSave()
    {
        if (!$this->created) {
            $this->created = new \DateTime();
        }
        if ($this->status == self::STATUS_DELIVERED && !$this->delivered) {
            $this->delivered = new \DateTime();
        }
        if (!$this->getId()) {
            $this->setTotals();
        }
    }

    public function toArray(bool $includeProducts = false): array
    {
        $data = [
            'id' => $this->getId(),
            'deliveryAddress' => $this->getDeliveryAddress(),
            'status' => $this->getStatus(),
            'deliveryTotal' => $this->getDeliveryTotal(),
            'productsTotal' => $this->getProductsTotal(),
            'grandTotal' => $this->getGrandTotal(),
            'courier' => $this->getCourier()?->getName(),
            'created' => $this->getCreated()?->format(\DateTimeInterface::ATOM),
            'delivered' => $this->getDelivered()?->format(\DateTimeInterface::ATOM)
        ];
        if ($includeProducts) {
            $data['products'] = [];
            foreach ($this->getProducts() as $product) {
                $data['products'][] = $product->toArray();
            }
        }
        return $data;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }

    public function getCourier(): ?Courier
    {
        return $this->courier;
    }

    public function setCourier(?Courier $courier): self
    {
        $this->courier = $courier;

        return $this;
    }

    public function getProductsTotal(): ?int
    {
        return $this->productsTotal;
    }

    public function setProductsTotal(int $productsTotal): self
    {
        $this->productsTotal = $productsTotal;

        return $this;
    }

    public function getDeliveryTotal(): ?int
    {
        return $this->deliveryTotal;
    }

    public function setDeliveryTotal(int $deliveryTotal): self
    {
        $this->deliveryTotal = $deliveryTotal;

        return $this;
    }

    public function getGrandTotal(): ?int
    {
        return $this->grandTotal;
    }

    public function setGrandTotal(int $grandTotal): self
    {
        $this->grandTotal = $grandTotal;

        return $this;
    }

    public function setTotals()
    {
        $productTotal = 0;
        $deliveryTotal = 0;
        $length = mb_strlen($this->getDeliveryAddress());
        foreach ($this->getProducts() as $product) {
            $productTotal += $product->getPrice();
            $deliveryTotal += $length + mb_strlen($product->getSeller()->getAddress());
        }
        $this->setProductsTotal($productTotal);
        $this->setDeliveryTotal($deliveryTotal);
        $this->setGrandTotal($productTotal + $deliveryTotal);
    }
}
