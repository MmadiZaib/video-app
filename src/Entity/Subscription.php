<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    private static array $planDataNames = ['free', 'pro', 'enterprise'];

    private static array $planDataPrices = [
        'free' => 0,
        'pro' => 15,
        'enterprise' => 29,
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plan;

    /**
     * @ORM\Column(type="datetime")
     */
    private $validTo;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $paymentStatus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $freePlanUsed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getFreePlanUsed(): ?bool
    {
        return $this->freePlanUsed;
    }

    public function setFreePlanUsed(bool $freePlanUsed): self
    {
        $this->freePlanUsed = $freePlanUsed;

        return $this;
    }

    public static function getPlanDataNameByIndex(int $index): string
    {
        return self::$planDataNames[$index];
    }

    public static function getPlanDataPriceByIndex(int $index): int
    {
        return self::$planDataPrices[$index];
    }

    public static function getPlanDataNames(): array
    {
        return self::$planDataNames;
    }

    public static function getPlanDataPrices(): array
    {
        return self::$planDataPrices;
    }
}
