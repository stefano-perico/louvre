<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Commande
 *
 * @ORM\Table(name="commandes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommandesRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide = 1;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(name="dateBillet", type="date")
     */
    private $dateBillet;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Utilisateur", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Billet", mappedBy="commande", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $billets;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

    /**
     * @var bool
     *
     * @ORM\Column(name="demi_journee", type="boolean")
     */
    private $demiJournee = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->billets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     *
     * @return Commande
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return boolean
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commande
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * set la date acctuel à la création de la commande
     *
     * @ORM\PrePersist()
     */
    public function createdAt()
    {
        $this->setDate(new \DateTime());
    }

    /**
     * Set dateBillet
     *
     * @param \DateTime $dateBillet
     *
     * @return Commande
     */
    public function setDateBillet($dateBillet)
    {
        $this->dateBillet = $dateBillet;

        return $this;
    }

    /**
     * Get dateBillet
     *
     * @return \DateTime
     */
    public function getDateBillet()
    {
        return $this->dateBillet;
    }

    /**
     * Set utilisateur
     *
     * @param \AppBundle\Entity\Utilisateur $utilisateur
     *
     * @return Commande
     */
    public function setUtilisateur(\AppBundle\Entity\Utilisateur $utilisateur)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \AppBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }

    /**
     * Add billet
     *
     * @param \AppBundle\Entity\Billet $billet
     *
     * @return Commande
     */
    public function addBillets(\AppBundle\Entity\Billet $billet)
    {
        $this->billets[] = $billet;
        $billet->setCommande($this);
        return $this;
    }

    /**
     * Remove billet
     *
     * @param \AppBundle\Entity\Billet $billet
     */
    public function removeBillets(\AppBundle\Entity\Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billet
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Commande
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return float
     */
    public function getPrix()
    {
        return $this->prix;
    }

    public function caluclerPrixCentimes()
    {
        return $this->getPrix() * 100;
    }



    /**
     * Set demiJournee
     *
     * @param boolean $demiJournee
     *
     * @return Commande
     */
    public function setDemiJournee($demiJournee)
    {
        $this->demiJournee = $demiJournee;

        return $this;
    }

    /**
     * Get demiJournee
     *
     * @return boolean
     */
    public function getDemiJournee()
    {
        return $this->demiJournee;
    }

    /**
     * @Assert\Callback()
     */
    public function isDateBilletValid(ExecutionContextInterface $context)
    {
        $year = $this->getDateBillet()->format('Y');
        $base = new \DateTime("$year-03-21");
        $days = easter_days($year);
        $base->add(new \DateInterval("P{$days}D"));

        $dimanche_paques = $base->format('d-m-Y');
        $lundi_paques = date("d-m-Y", strtotime("$dimanche_paques +1 day"));
        $jeudi_ascension = date("d-m-Y", strtotime("$dimanche_paques +39 day"));
        $lundi_pentecote = date("d-m-Y", strtotime("$dimanche_paques +50 day"));

        $jours_feries = array
        (
            $dimanche_paques,
            $lundi_paques
        ,   $jeudi_ascension
        ,   $lundi_pentecote

        ,    "01-01-$year"         //    Nouvel an
        ,    "01-05-$year"         //    Fête du travail
        ,    "08-05-$year"        //    Armistice 1945
        ,    "14-07-$year"         //    Fête nationale
        ,    "15-08-$year"         //    Assomption
        ,    "01-11-$year"         //    Toussaint
        ,    "11-11-$year"         //    Armistice 1918
        ,    "25-12-$year"         //    Noël
        );

        if (in_array($this->getDateBillet()->format('d-m-Y'), $jours_feries))
        {
            $context
                ->buildViolation('Ce jour est fériés, merci de choisir une autre date')
                ->atPath('dateBillet')
                ->addViolation()
            ;
        }
    }
}
