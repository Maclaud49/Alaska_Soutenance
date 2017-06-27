insert into t_article values
(1, 'Traite des fourrures', 'À partir de 1784, les trappeurs russes établissent des comptoirs de traite permanents sur les îles Aléoutiennes et sur la côte américaine du Pacifique, jusqu''à la Californie (fort Ross, à moins de 160 kilomètres au nord de San Francisco). Pour commencer, des postes côtiers sont établis à Attu, Agattu (en) et Unalaska, dans les îles Aléoutiennes, ainsi que dans l''île de Kodiak, au large de l''embouchure du golfe de Cook. Dix-huit mois plus tard, une colonie est établie sur le continent, en face de l''anse Cook. L''objectif est de chasser la loutre de mer, dont la fourrure se vend à prix d''or sur les marchés chinois. Comme en Sibérie, les Russes embauchent, alcoolisent et cherchent à convertir à l''orthodoxie les populations locales : la communauté orthodoxe alaskane est aléoute ou kodiak. On comptait environ 25 000 Aléoutes à l''arrivée des Russes, mais seulement 3 892 en 1885, après 122 ans de domination russe (au pied du volcan Mont Redoubt, haut de 3 100 mètres, la présence de l''église russe orthodoxe de Ninilchik rappelle que l''Alaska fut une colonie russe), puis américaine… vodka, bourbon et grippe ont eu ici les mêmes effets qu''ailleurs6. Dès la fin du xviiie siècle, des marchands et des missionnaires américains et anglais viennent concurrencer les activités russes.');
insert into t_article values
(2, 'Un territoire américain', 'Territoire d''origine russe, après son achat par les Américains en 1867 pour 7 millions de dollars. Cet achat fut effectué lors de la création d''une ligne télégraphique devant traverser la Russie et le détroit de Béring, reliant ainsi le territoire des États-Unis à l’Europe. Le transfert de ce territoire de la Russie aux États-Unis entraîna pour les Alaskains le passage du calendrier julien au calendrier grégorien ainsi que le décalage vers l''ouest de la ligne de changement de date ; pour cette double raison, ils virent le vendredi 18 octobre succéder au vendredi 6 octobre. La région fut d''abord dénommée : département de l''Alaska (en) et placé sous la juridiction de l''armée jusqu''en 1877, du Trésor jusqu''en 1879 et de la Marine jusqu''en 1884.

En cette fin du xixe siècle, les chercheurs d''or tentèrent par milliers leur chance et y laissèrent parfois leur vie. Mais cette fièvre de l''or, appelée « ruée vers l''or du Klondike », retombe très vite. Les autres activités économiques sont données par la pêche et la conserverie.

En 1884, l''Alaska fut organisé en tant que District de l''Alaska (en). En 1890, l''Alaska compte environ 30 000 habitants, dont les ¾ sont indigènes.

Le 24 août 1912, il devint le Territoire de l''Alaska. Une seule voie de chemin de fer relie alors la côte à Fairbanks au centre du territoire, elle a été construite par le biais de l''État fédéral entre 1915 et 1923.

Ce territoire entra dans l''Union en tant que 49e État le 3 janvier 1959. Durant la deuxième moitié du xxe siècle, l''Alaska devint une position stratégique dans la guerre froide qui opposait les États-Unis à l''Union soviétique. Vers 1975, la découverte de champs pétrolifères entraîna un afflux massif de travailleurs. Aujourd''hui, l''Alaska attire les touristes à la belle saison, venus admirer les ours et les fjords et pratiquer la pêche sportive (saumon et truite).');
insert into t_article values
(3, 'Le destin des populations autochtones', "En 1971, on recensait 40 000 Inuits et Yupiks, 22 000 Amérindiens et 7 000 Aléoutes. Ils obtinrent un statut privilégié et reçurent légalement 200 000 km2 de réserve ainsi qu'un milliard de dollars d'indemnités. Le mode de vie traditionnel des autochtones a été profondément bouleversé par l'arrivée des Blancs : désormais, les déplacements se font sur des motoneiges, les jeunes profitent du confort moderne, mais s'éloignent des traditions et ils vivent des revenus du pétrole.");

/* raw password is 'oursblanc' */
insert into t_user values
(2, 'oursBlanc','oursblanc@banquise.com', '$2y$13$IFwilM1/FOqna3uWXCpg8ej4.V4BeJ8iDCSY1619.BIA9Uu9daR6W', 'e4d7c3cc31f551b4ef4a578', 'ROLE_USER');
/* raw password is 'saumonorange' */
insert into t_user values
(3, 'saumonOrange','saumonorange@banquise.com', '$2y$13$N8yk6aJtfdol0qjo/3LmS.jKoBV2kNnra6alqEiwodL717BHjTYvm', '4142fbde0f5961728ba4bfd', 'ROLE_USER');
/* raw password is '@dm1n' */
insert into t_user values
(1, 'admin', '$2y$13$A8MQM2ZNOi99EW.ML7srhOJsCaybSbexAj/0yXrJs4gQ/2BqMMW2K', 'EDDsl&fBCJB|a5XUtAlnQN8', 'ROLE_ADMIN');
/* raw password is 'pauline' */
insert into t_user values
(5, 'pauline','pauline@hfd.com', '$2y$13$vHNelI/YHgqyXgrLRTQMN.pkHB4LOcTWxR6vCuwBPc7eeCMinJc.u', '5cfa5da345e791abeff52c5', 'ROLE_USER');


insert into t_comment values
(1, 'Great! Keep up the good work', 1, 2);
insert into t_comment values
(2, "Thank you, I'll try my best.", 1, 3);
