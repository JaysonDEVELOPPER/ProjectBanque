<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["transaction_read"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["transaction_read", "transaction_write"])]
    private ?\DateTimeInterface $trs_date = null;

    #[ORM\Column]
    #[Groups(["transaction_read", "transaction_write"])]
    private ?float $trs_amount = null;

    #[ORM\Column]
    #[Groups(["transaction_read", "transaction_write"])]
    private ?bool $trs_debit = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'transactions')]
    #[Groups(["transaction_write"])]
    #[MaxDepth(1)]
    private ?self $fk_trt_id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[Groups(["transaction_read", "transaction_write"])]
    
    private ?Category $fk_cat_id = null;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[Groups(["transaction_write"])]
    private ?BankAccount $fk_bnk_id = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrsDate(): ?\DateTimeInterface
    {
        return $this->trs_date;
    }

    public function setTrsDate(\DateTimeInterface $trs_date): static
    {
        $this->trs_date = $trs_date;

        return $this;
    }

    public function getTrsAmount(): ?float
    {
        return $this->trs_amount;
    }

    public function setTrsAmount(float $trs_amount): static
    {
        $this->trs_amount = $trs_amount;

        return $this;
    }

    public function isTrsDebit(): ?bool
    {
        return $this->trs_debit;
    }

    public function setTrsDebit(bool $trs_debit): static
    {
        $this->trs_debit = $trs_debit;

        return $this;
    }

    public function getFkTrtId(): ?self
    {
        return $this->fk_trt_id;
    }

    public function setFkTrtId(?self $fk_trt_id): static
    {
        $this->fk_trt_id = $fk_trt_id;

        return $this;
    }

   

    public function getFkCatId(): ?Category
    {
        return $this->fk_cat_id;
    }

    public function setFkCatId(?Category $fk_cat_id): static
    {
        $this->fk_cat_id = $fk_cat_id;

        return $this;
    }

    public function getFkBnkId(): ?BankAccount
    {
        return $this->fk_bnk_id;
    }

    public function setFkBnkId(?BankAccount $fk_bnk_id): static
    {
        $this->fk_bnk_id = $fk_bnk_id;

        return $this;
    }
}