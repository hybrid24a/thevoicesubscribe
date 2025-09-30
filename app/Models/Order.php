<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use App\Traits\CartOrderCommonTrait;

class Order extends Model
{
    use SoftDeletes, CartOrderCommonTrait;

    protected $guarded = [];

    const NUMBER_COLUMN = 'number';
    const CART_ID_COLUMN = 'cart_id';
    const INVOICE_PATH_COLUMN = 'invoice_path';
    const INVOICE_NUMBER_COLUMN = 'invoice_number';

    const OPEN_STATUS = 'open';
    const FULFILLED_STATUS = 'fulfilled';

    const STATUSES = [
        self::OPEN_STATUS,
        self::FULFILLED_STATUS,
    ];

    protected $casts = [
        'item_details' => 'array',
    ];

    /** @var Collection|PaymentDetails[] */
    private $paymentsDetails;

    /** @var Cart */
    private $cart;

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCartId(): int
    {
        return $this->cart_id;
    }

    public function getInvoicePath(): ?string
    {
        return $this->invoice_path;
    }

    public function getPaymentsDetails(): Collection|array
    {
        return $this->paymentsDetails;
    }

    public function getLastPaymentDetails(): PaymentDetails
    {
        return $this->paymentsDetails->first();
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function isFulfilled(): bool
    {
        return $this->status === self::FULFILLED_STATUS;
    }

    public function hasBeenPaid(): bool
    {
        // if one of the payment details has been paid
        return $this->paymentsDetails->contains(fn($payment) => $payment->isPaid());
    }

    public function getInvoiceItems(): array
    {
        $items = [];

        if ($this->getTip() > 0) {
            $items[] = [
                'thumbnail' => '/build/images/tips.jpg',
                'title'     => self::TIP_TITLE['fr'],
                'price'     => number_format($this->getTip(), 2),
            ];
        }

        $formatedPrice = number_format($this->price, 2);

        if ($this->isSubscriptionArchiveItem()) {
            $items[] = [
                'thumbnail' => '/build/images/sub-yearly-archive.jpg',
                'title'     => self::YEARLY_SUBSCRIPTION_ARCHIVE_TITLE['fr'],
                'price'     => $formatedPrice,
            ];

            $items = array_reverse($items);

            return $items;
        }

        $details = $this->getItemDetails();

        if ($this->isMagazineItem()) {
            $magazineNumberText = self::MAGAZINE_NUMBER_TITLE['fr'] . ' ' . $details['number'];
            $items[] = [
                'thumbnail' => $details['thumbnail'] ?? null,
                'title'     => $magazineNumberText,
                'price'     => $formatedPrice,
            ];

            $items = array_reverse($items);

            return $items;
        }

        $items[] = [
            'thumbnail' => '/build/images/sub-yearly.jpg',
            'title'     => self::YEARLY_SUBSCRIPTION_TITLE['fr'],
            'price'     => $formatedPrice,
        ];

        $items = array_reverse($items);

        return $items;
    }

    public function getInvoiceNumber(): ?int
    {
        return $this->invoice_number;
    }

    public function getInvoiceDisplayNumber(): ?string
    {
        if (!$this->invoice_number) {
            return null;
        }

        return date('Y') . '/' . str_pad($this->invoice_number, 4, '0', STR_PAD_LEFT);
    }

    public function setInvoicePath(string $invoicePath): void
    {
        $this->invoice_path = $invoicePath;
    }

    public function setInvoiceNumber(int $invoiceNumber): void
    {
        $this->invoice_number = $invoiceNumber;
    }

    /**
     * @param Collection|PaymentDetails[] $paymentsDetails
     */
    public function setPaymentDetails($paymentsDetails): void
    {
        $this->paymentsDetails = $paymentsDetails;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }
}
