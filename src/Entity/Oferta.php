<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Oferta
 *
 * @ORM\Table(name="oferta", indexes={@ORM\Index(name="id_osobyFirmy", columns={"id_osobyFirmy"})})
 * @ORM\Entity
 */
class Oferta
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
     * @ORM\Column(name="nazwsiskoLubNazwa", type="text", length=65535, nullable=false)
     */
    private $nazwsiskolubnazwa;

    /**
     * @var string
     *
     * @ORM\Column(name="cena", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $cena;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="termin_realizacji", type="date", nullable=false)
     */
    private $terminRealizacji;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="okres_gwarancji", type="date", nullable=true)
     */
    private $okresGwarancji;

    /**
     * @var string|null
     *
     * @ORM\Column(name="doswiadczenie", type="text", length=65535, nullable=true)
     */
    private $doswiadczenie;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ilosc_podobnych_projektow", type="integer", nullable=true)
     */
    private $iloscPodobnychProjektow;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_osobyFirmy", referencedColumnName="id")
     * })
     */
    private $idOsobyfirmy;

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
    public function getNazwsiskolubnazwa(): string
    {
        return $this->nazwsiskolubnazwa;
    }

    /**
     * @return string
     */
    public function getCena(): string
    {
        return $this->cena;
    }

    /**
     * @return \DateTime
     */
    public function getTerminRealizacji(): \DateTime
    {
        return $this->terminRealizacji;
    }

    /**
     * @return \DateTime|null
     */
    public function getOkresGwarancji(): ?\DateTime
    {
        return $this->okresGwarancji;
    }

    /**
     * @return string|null
     */
    public function getDoswiadczenie(): ?string
    {
        return $this->doswiadczenie;
    }

    /**
     * @return int|null
     */
    public function getIloscPodobnychProjektow(): ?int
    {
        return $this->iloscPodobnychProjektow;
    }

    /**
     * @return \User
     */
    public function getIdOsobyfirmy(): \User
    {
        return $this->idOsobyfirmy;
    }

    /**
     * @param string $nazwsiskolubnazwa
     */
    public function setNazwsiskolubnazwa(string $nazwsiskolubnazwa): void
    {
        $this->nazwsiskolubnazwa = $nazwsiskolubnazwa;
    }

    /**
     * @param string $cena
     */
    public function setCena(string $cena): void
    {
        $this->cena = $cena;
    }

    /**
     * @param \DateTime $terminRealizacji
     */
    public function setTerminRealizacji(\DateTime $terminRealizacji): void
    {
        $this->terminRealizacji = $terminRealizacji;
    }

    /**
     * @param \DateTime|null $okresGwarancji
     */
    public function setOkresGwarancji(?\DateTime $okresGwarancji): void
    {
        $this->okresGwarancji = $okresGwarancji;
    }

    /**
     * @param string|null $doswiadczenie
     */
    public function setDoswiadczenie(?string $doswiadczenie): void
    {
        $this->doswiadczenie = $doswiadczenie;
    }

    /**
     * @param int|null $iloscPodobnychProjektow
     */
    public function setIloscPodobnychProjektow(?int $iloscPodobnychProjektow): void
    {
        $this->iloscPodobnychProjektow = $iloscPodobnychProjektow;
    }

    /**
     * @param \User $idOsobyfirmy
     */
    public function setIdOsobyfirmy(\User $idOsobyfirmy): void
    {
        $this->idOsobyfirmy = $idOsobyfirmy;
    }
}
