<?php

namespace GridCP\Net\Ip4\Domain\Model;

class Ip4
{
    private ?int $id = null;
    public ?string $uuid = null;
    public ?string $ip = null;
    public ?int $id_network = null;
    public ?bool $active = true;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     */
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     */
    public function setIp(?string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int|null
     */

    public function getIdNetwork(): ?int
    {
        return $this->id_network;
    }

    /**
     * @param int|null $id_network
     */

    public function setIdNetwork(?int $id_network): void
    {
        $this->id_network = $id_network;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     */
    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }


}