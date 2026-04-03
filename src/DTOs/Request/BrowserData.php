<?php

declare(strict_types=1);

namespace LionTech\SDK\DTOs\Request;

use JsonSerializable;

final readonly class BrowserData implements JsonSerializable
{
    /**
     * @param string|null $acceptHeader HTTP Accept header
     * @param string|null $colorDepth Color depth
     * @param bool|null $javaEnabled Java enabled
     * @param string|null $language Browser language
     * @param int|null $screenHeight Screen height
     * @param int|null $screenWidth Screen width
     * @param string|null $timezone Timezone
     * @param string|null $userAgent User agent
     * @param int|null $windowHeight Window height
     * @param int|null $windowWidth Window width
     */
    public function __construct(
        public ?string $acceptHeader = null,
        public ?string $colorDepth = null,
        public ?bool $javaEnabled = null,
        public ?string $language = null,
        public ?int $screenHeight = null,
        public ?int $screenWidth = null,
        public ?string $timezone = null,
        public ?string $userAgent = null,
        public ?int $windowHeight = null,
        public ?int $windowWidth = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [];

        if ($this->acceptHeader !== null) {
            $data['acceptHeader'] = $this->acceptHeader;
        }

        if ($this->colorDepth !== null) {
            $data['colorDepth'] = $this->colorDepth;
        }

        if ($this->javaEnabled !== null) {
            $data['javaEnabled'] = $this->javaEnabled;
        }

        if ($this->language !== null) {
            $data['language'] = $this->language;
        }

        if ($this->screenHeight !== null) {
            $data['screenHeight'] = $this->screenHeight;
        }

        if ($this->screenWidth !== null) {
            $data['screenWidth'] = $this->screenWidth;
        }

        if ($this->timezone !== null) {
            $data['timezone'] = $this->timezone;
        }

        if ($this->userAgent !== null) {
            $data['userAgent'] = $this->userAgent;
        }

        if ($this->windowHeight !== null) {
            $data['windowHeight'] = $this->windowHeight;
        }

        if ($this->windowWidth !== null) {
            $data['windowWidth'] = $this->windowWidth;
        }

        return $data;
    }
}
