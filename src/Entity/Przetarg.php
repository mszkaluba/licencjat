<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Przetarg
 *
 * @ORM\Table(name="przetarg", indexes={@ORM\Index(name="wystawca_id", columns={"wystawca_id"})})
 * @ORM\Entity
 */
class Przetarg
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nazwa", type="text", length=65535, nullable=false)
     */
    private $nazwa;

    /**
     * @var string
     *
     * @ORM\Column(name="wystawca_nazwa", type="text", length=65535, nullable=false)
     */
    private $wystawcaNazwa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_rozpoczecia", type="date", nullable=false)
     */
    private $dataRozpoczecia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_zakonczenia", type="date", nullable=false)
     */
    private $dataZakonczenia;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="wystawca_id", referencedColumnName="id")
     * })
     */
    private $wystawca;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNazwa(): string
    {
        return $this->nazwa;
    }

    /**
     * @return string
     */
    public function getWystawcaNazwa(): string
    {
        return $this->wystawcaNazwa;
    }

    /**
     * @return \DateTime
     */
    public function getDataRozpoczecia(): \DateTime
    {
        return $this->dataRozpoczecia;
    }

    /**
     * @return \DateTime
     */
    public function getDataZakonczenia(): \DateTime
    {
        return $this->dataZakonczenia;
    }

    /**
     * @return \User
     */
    public function getWystawca(): \User
    {
        return $this->wystawca;
    }

    /**
     * @param string $nazwa
     */
    public function setNazwa(string $nazwa): void
    {
        $this->nazwa = $nazwa;
    }

    /**
     * @param string $wystawcaNazwa
     */
    public function setWystawcaNazwa(string $wystawcaNazwa): void
    {
        $this->wystawcaNazwa = $wystawcaNazwa;
    }

    /**
     * @param \DateTime $dataRozpoczecia
     */
    public function setDataRozpoczecia(\DateTime $dataRozpoczecia): void
    {
        $this->dataRozpoczecia = $dataRozpoczecia;
    }

    /**
     * @param \DateTime $dataZakonczenia
     */
    public function setDataZakonczenia(\DateTime $dataZakonczenia): void
    {
        $this->dataZakonczenia = $dataZakonczenia;
    }

    /**
     * @param \User $wystawca
     */
    public function setWystawca(\User $wystawca): void
    {
        $this->wystawca = $wystawca;
    }
}
