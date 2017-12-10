<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * billets
 *
 * @ORM\Table(name="billets")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BilletsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Billet
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=45)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=45)
     */
    private $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissance", type="datetime")
     */
    private $dateNaissance;

    /**
     * @var bool
     *
     * @ORM\Column(name="disponible", type="boolean")
     */
    private $disponible = true;

    /**
     * @var float
     *
     * @ORM\Column(name="prix", type="float")
     */
   private $prix;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Commande", inversedBy="billet", cascade={"persist"})
     */
   private $commande;

    /**
     * Constructor
     */
    public function __construct()
    {

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Billet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Billet
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set disponible
     *
     * @param boolean $disponible
     *
     * @return Billet
     */
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;

        return $this;
    }

    /**
     * Get disponible
     *
     * @return boolean
     */
    public function getDisponible()
    {
        return $this->disponible;
    }
    
    /**
     * Get commande
     *
     * @return \AppBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }

    public function getAge()
    {
        $age = $this->getDateNaissance()->diff(new \DateTime())->format('%Y');
        return $age;
    }

    /**
     * Set prix
     *
     * @param float $prix
     *
     * @return Billet
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


    /**
     * Calcule et set le prix du billet
     *
     */
    public function calculerPrix()
    {
        $age = (int) $this->getAge();
        if($age < 4)
        {
            $this->setPrix(0);
        }
        elseif ($age >= 4 && $age <= 12 OR $age >= 60)
        {
            $this->setPrix(12);
        }
        else
        {
            $this->setPrix(16);
        }
    }


    /**
     * Set commande
     *
     * @param \AppBundle\Entity\Commande $commande
     *
     * @return Billet
     */
    public function setCommande(\AppBundle\Entity\Commande $commande = null)
    {
        $this->commande = $commande;

        return $this;
    }
}
