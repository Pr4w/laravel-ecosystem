# Changelog

## v1.0.0
- Catalogue initial : LaRédac, NotionScheduler, Abrège, Cherche Mission.
- Trois vues prêtes à l'emploi : `<x-ecosystem::inline />`, `<x-ecosystem::grid />` et `<x-ecosystem::columns />`, plus le composant Vue équivalent pour les sites Inertia. Publiables pour être réécrites au design du site.
- Libellé commun traduisible dans la config, pour que les quatre sites disent la même phrase.
- Chaque produit peut déclarer son format d'affichage avec `logo.prefer` (`svg`, `url` ou `text`) : une marque qui est un emoji s'affiche en emoji. Un ordre passé explicitement par le site reste prioritaire.
