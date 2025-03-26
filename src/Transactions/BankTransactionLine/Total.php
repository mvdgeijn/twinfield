<?php

namespace PhpTwinfield\Transactions\BankTransactionLine;

use Money\Money;
use PhpTwinfield\Enums\DebitCredit;
use PhpTwinfield\Enums\LineType;

class Total extends Base
{
    /**
     * The total VAT amount in the currency of the bank transaction.
     *
     * @var Money|null
     */
    private $vatTotal;

    /**
     * The total VAT amount in base currency.
     *
     * @var Money|null
     */
    private $vatBaseTotal;

    /**
     * The total VAT amount in reporting currency.
     *
     * @var Money|null
     */
    private $vatRepTotal;

    public function __construct()
    {
        $this->setLineType(LineType::TOTAL());
    }

    public function setBankBalanceAccount(string $dim1): self
    {
        return $this->setDim1($dim1);
    }

    /**
     * Based on the sum of the individual bank transaction lines. In case of a bank addition debit. In case of a bank
     * withdrawal credit.
     *
     * @param DebitCredit $debitCredit
     * @return $this
     */
    public function setDebitCredit(DebitCredit $debitCredit): self
    {
        return parent::setDebitCredit($debitCredit);
    }

    /**
     * Amount including VAT.
     *
     * @param Money $money
     */
    public function setValue(Money $money): self
    {
        return parent::setValue($money);
    }

    /**
     * @return Money|null
     */
    public function getVatTotal(): ?Money
    {
        return $this->vatTotal;
    }

    /**
     * The total VAT amount in the currency of the bank transaction.
     *
     * @param Money $vatTotal
     */
    public function setVatTotal(Money $vatTotal): self
    {
        $this->vatTotal = $vatTotal;

        return $this;
    }

    /**
     * @return Money|null
     */
    public function getVatBaseTotal(): ?Money
    {
        return $this->vatBaseTotal;
    }

    /**
     * The total VAT amount in base currency.
     *
     * @param Money $vatBaseTotal
     * @return Total
     */
    public function setVatBaseTotal(Money $vatBaseTotal): self
    {
        $this->vatBaseTotal = $vatBaseTotal;

        return $this;
    }

    /**
     * @return Money|null
     */
    public function getVatRepTotal(): ?Money
    {
        return $this->vatRepTotal;
    }

    /**
     * @param Money $vatRepTotal
     * @return Total
     */
    public function setVatRepTotal(Money $vatRepTotal): self
    {
        $this->vatRepTotal = $vatRepTotal;

        return $this;
    }
}
