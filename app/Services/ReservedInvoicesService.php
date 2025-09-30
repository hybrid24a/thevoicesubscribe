<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\ReservedInvoice;
use App\Repositories\ReservedInvoicesRepository;

class ReservedInvoicesService
{
    /** @var ReservedInvoicesRepository */
    private $reservedInvoicesRepository;

    public function __construct(
        ReservedInvoicesRepository $reservedInvoicesRepository
    ) {
        $this->reservedInvoicesRepository = $reservedInvoicesRepository;
    }

    public function getById(int $id): ?ReservedInvoice
    {
        return $this->reservedInvoicesRepository->getById($id);
    }

    /**
     * @return ReservedInvoice[]|Collection
     */
    public function getAll()
    {
        return $this->reservedInvoicesRepository->getAll();
    }

    public function create(array $data): ReservedInvoice
    {
        return $this->reservedInvoicesRepository->create($data);
    }

    public function delete(int $id): bool
    {
        return $this->reservedInvoicesRepository->delete($id);
    }
}
