<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Request;

use JsonSerializable;

final readonly class CustomerData implements JsonSerializable
{
    /**
     * @param string|null $accountId Customer account ID
     * @param string|null $email Customer email
     * @param string|null $fullName Customer full name
     * @param string|null $phone Customer phone
     * @param string|null $ip Customer IP address
     * @param string|null $fingerprint Browser fingerprint
     * @param string|null $address Customer address
     * @param string|null $city Customer city
     * @param string|null $state Customer state
     * @param string|null $postalCode Customer postal code
     * @param string|null $country Customer country code
     * @param string|null $neighborhood Customer neighborhood
     * @param \DateTimeImmutable|null $birthdate Customer birthdate
     * @param BrowserData|null $browserData Browser data
     */
    public function __construct(
        public ?string $accountId = null,
        public ?string $email = null,
        public ?string $fullName = null,
        public ?string $phone = null,
        public ?string $ip = null,
        public ?string $fingerprint = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?string $neighborhood = null,
        public ?\DateTimeImmutable $birthdate = null,
        public ?BrowserData $browserData = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [];

        if ($this->accountId !== null) {
            $data['accountId'] = $this->accountId;
        }

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->fullName !== null) {
            $data['fullName'] = $this->fullName;
        }

        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
        }

        if ($this->ip !== null) {
            $data['ip'] = $this->ip;
        }

        if ($this->fingerprint !== null) {
            $data['fingerprint'] = $this->fingerprint;
        }

        if ($this->address !== null) {
            $data['address'] = $this->address;
        }

        if ($this->city !== null) {
            $data['city'] = $this->city;
        }

        if ($this->state !== null) {
            $data['state'] = $this->state;
        }

        if ($this->postalCode !== null) {
            $data['postalCode'] = $this->postalCode;
        }

        if ($this->country !== null) {
            $data['country'] = $this->country;
        }

        if ($this->neighborhood !== null) {
            $data['neighborhood'] = $this->neighborhood;
        }

        if ($this->birthdate instanceof \DateTimeImmutable) {
            $data['birthdate'] = $this->birthdate->format('Y-m-d');
        }

        if ($this->browserData instanceof \LionTech\SDK\DTOs\Request\BrowserData) {
            $data['browserData'] = $this->browserData->jsonSerialize();
        }

        return $data;
    }
}
