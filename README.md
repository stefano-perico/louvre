louvre
======
**Note de cadrage projet Louvre**

Rappel des enjeux et objectifs
Le musée du Louvre veux créer un système de réservation et de gestion des tickets en ligne pour diminuer les longes files d’attente et tirer parti de l’usage croissant des smartphones.

Objectifs

    • Vendre des billets pour le musée du Louvre en ligne
    • Diminuer les longues files d’attente
    
Préconisations fonctionnelles

    • Paiement en ligne via Stripe
    • Site responsive, accessible sur smartphone, ordinateur et tablette
    • Billetterie en ligne
    • Interface fonctionnelle, claire et rapide
    
Futures utilisateurs

    • Les touristes
    • Les familles
    • Les particulier des plus âgés aux ados qui souhaite s’instruire
    • Les amoureux de l’art
    
solutions technique

    • MVC
    • Symfony
    • MySql
    • Jquery
    • Stripe
    
Préconisations d’architecture

    Accueil du Louvre
    
        -> Billetterie
        
            -> Informations utilisateur
            
                -> Informations de la visite
                
                    -> Récapitulatif de la commande et paiement
                    
                        -> Validation du paiement

