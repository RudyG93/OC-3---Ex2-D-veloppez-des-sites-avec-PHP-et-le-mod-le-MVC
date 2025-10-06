-- Migration pour ajouter le champ 'views' à la table article
-- Exécuter ce script sur votre base de données pour ajouter le comptage des vues

-- Ajouter la colonne views avec une valeur par défaut de 0
ALTER TABLE `article` ADD COLUMN `views` INT(11) NOT NULL DEFAULT 0 AFTER `date_update`;

-- Mettre à jour les articles existants avec des vues par défaut (optionnel)
-- UPDATE `article` SET `views` = 0 WHERE `views` IS NULL;