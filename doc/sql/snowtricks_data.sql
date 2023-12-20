-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 09, 2023 at 08:14 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `snowtricks`
--

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `title`) VALUES
(1, 'Grabs'),
(2, 'Rotations'),
(3, 'Jibs and rails');

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `username`, `is_verified`, `reset_token`, `avatar`) VALUES
(1, 'aurelie.beninca@gmail.com', '[\"ROLE_ADMIN\", \"ROLE_USER\"]', '$2y$13$vlbn84.3fCipC0IKag7e/O3AAt9B1qH.uLklOf28.FGYfzys2TOBG', 'Shil', 1, '-jR1shTMhaCyLHaFKVX8Yn8ke18QXf8FO6DDdytFLc4', '5e41d2aec32623bd557e84f6349c1115.png'),
(2, 'donatello@tortue.geniale.fr', '[\"ROLE_USER\"]', '$2y$13$1AkuoPRm/2aNpMNkBcYav.nJaH72UGBI1YGEgsIyoVBXV86cZqQDi', 'Donatello', 1, NULL, '2961e921d7baa406f6359d70c287451f.png'),
(3, 'leonardo@tortue.geniale.fr', '[\"ROLE_USER\"]', '$2y$13$f26X7c7HfWS9LrWdujfNdeSCJyu/DWG93AkFAn/sAvIX4RdbukzZS', 'Leonardo', 1, NULL, '7cbea83269abd76b9de5610d3b363973.png'),
(4, 'raphael@tortue.geniale.fr', '[\"ROLE_USER\"]', '$2y$13$cpMPdaOErGZlX8xF6IpK4uUKtgN8DPcE87sZa3TJrUyA6cLHJh6im', 'Raphael', 1, NULL, '64697a388a8c4a6ac37ba7fa7453bfea.png'),
(5, 'michelangelo@tortue.geniale.fr', '[\"ROLE_USER\"]', '$2y$13$kEHY6nkOSpeWC2OYFK.YteoXB4HSVCYAvIBpbLfa1PePE/akuHZFm', 'Michelangelo', 1, NULL, '6e34bd97cac25180b3e8ed43f1f8eca9.png');
COMMIT;

--
-- Dumping data for table `trick`
--

INSERT INTO `trick` (`id`, `category_id`, `title`, `content`, `created_at`, `update_date`, `header_image`, `slug`) VALUES
(1, 1, 'Indy Grab', 'Ajouter du Style à vos Sauts.\r\n\r\nL\'Indy Grab, un classique du snowboard freestyle, incarne l\'alliance parfaite entre élégance aérienne et contrôle. Ce grab, caractérisé par la main arrière saisissant le côté de la carre frontside de la planche entre les fixations, offre une esthétique distincte et une sensation de vol inégalée.\r\n\r\nPour exécuter un Indy Grab avec style, choisissez une rampe ou un kicker approprié à votre niveau de compétence. Lors du saut, fléchissez vos genoux tout en étendant votre jambe arrière pour permettre à votre main arrière de saisir fermement la planche. La position de la main et la tenue du grab sont essentielles pour une esthétique optimale.\r\n\r\nMaintenez une position équilibrée en utilisant votre main avant et le mouvement du corps pour ajuster votre orientation en l\'air. Gardez votre regard sur la trajectoire pour assurer un atterrissage en douceur. La clé réside dans la synchronisation parfaite entre le grab et la rotation, ajoutant une fluidité distincte à votre descente.\r\n\r\nL\'Indy Grab offre une variété de variations, permettant aux riders de personnaliser leur style. Essayez de tordre légèrement la planche ou d\'ajouter une rotation pour varier l\'esthétique du grab. Les riders créatifs peuvent intégrer l\'Indy Grab dans des séquences plus complexes pour une performance freestyle complète.\r\n\r\nQue vous soyez sur un snowpark ou en hors-piste, l\'Indy Grab s\'adapte à divers terrains. Commencez par des sauts modérés pour maîtriser la technique, puis progressez vers des descentes plus complexes en ajustant l\'amplitude du grab.\r\n\r\nIntégrez l\'Indy Grab à votre répertoire de tricks et découvrez la magie de mêler élégance et contrôle en altitude. En adoptant cette figure emblématique, vous ajouterez une touche de style distinctif à chaque saut, impressionnant vos pairs sur les pistes enneigées..', '2023-11-21 16:59:36', '2023-12-09 19:47:49', '15f76bb9b8091054efeecccaf031ec78.png', 'indy-grab'),
(2, 1, 'Method Grab', 'Élégance dans les Airs.\r\n\r\nLe Method Grab, une figure emblématique du snowboard freestyle, incarne la fusion parfaite entre élégance et technique. Ce grab, caractérisé par la main arrière saisissant le talon de la planche tout en étirant la jambe arrière, offre une esthétique unique et une sensation de vol incomparable.\r\n\r\nPour exécuter un Method Grab avec brio, choisissez un lieu adapté à votre niveau de compétence. En approchant le saut, fléchissez vos genoux, et lors du moment crucial, engagez la rotation du haut du corps tout en étirant la jambe arrière pour saisir le talon. La clé réside dans la coordination entre le haut et le bas du corps.\r\n\r\nEn plein vol, utilisez vos bras pour maintenir l\'équilibre et accentuer la position du grab. Gardez votre regard fixé sur la trajectoire pour assurer un atterrissage en douceur. La position du corps est cruciale, avec une inclinaison légère vers l\'arrière pour éviter les atterrissages raides.\r\n\r\nLe Method Grab offre une variété de variations, permettant aux riders de personnaliser leur style. Ajoutez une torsion pour une touche artistique ou expérimentez avec différentes positions des jambes. C\'est un grab polyvalent qui s\'adapte à divers terrains, du snowpark à la poudreuse en backcountry.\r\n\r\nPartagez vos prouesses Method Grab avec la communauté! Que vous soyez un débutant enthousiaste ou un expert du snowboard, le Method Grab devient un symbole distinctif de votre style freestyle. Rejoignez la conversation sur notre plateforme communautaire pour échanger des astuces, des vidéos et des inspirations autour de ce grab emblématique. Faites équipe avec des riders partageant la même passion et explorez l\'art du Method Grab ensemble.', '2023-11-21 17:05:13', '2023-12-09 20:01:34', 'c2ceb07b647b1037718166330ea7d63d.png', 'method-grab'),
(3, 1, 'Mute Grab', 'Capturer l\'Instant de Grâce.\r\n\r\nLe Mute Grab, une icône du snowboard freestyle, incarne l\'élégance et la maîtrise aérienne. Ce grab classique, caractérisé par la main avant saisissant la carre frontside de la planche entre les fixations, offre une esthétique unique à chaque saut.\r\n\r\nPour exécuter un Mute Grab impressionnant, choisissez un lieu adapté à votre niveau de compétence. En approchant le saut, fléchissez vos genoux et tendez votre main avant vers la carre frontside, capturant ce moment de grâce. La clé réside dans l\'extension complète du bras et la tenue du grab pour une esthétique maximale.\r\n\r\nEn plein vol, utilisez votre bras arrière pour maintenir l\'équilibre et accentuer le style du grab. Gardez votre regard fixé sur la trajectoire pour un atterrissage en douceur. La position du corps est cruciale, avec une inclinaison légère vers l\'arrière pour prévenir les atterrissages raides.\r\n\r\nLe Mute Grab offre une toile d\'expression pour les riders créatifs. Expérimentez avec des variations de la position de la main ou ajoutez des rotations pour personnaliser votre style. C\'est un grab polyvalent qui s\'adapte à différents types de sauts, du snowpark à la poudreuse en backcountry.\r\n\r\nPartagez vos exploits Mute Grab avec la communauté! Que vous soyez un débutant enthousiaste ou un vétéran du snowboard, le Mute Grab devient une marque distinctive de votre style freestyle. Rejoignez la conversation sur notre plateforme communautaire pour échanger astuces, vidéos et inspirations autour de ce grab emblématique. Capturez l\'instant de grâce avec le Mute Grab et partagez-le avec la communauté qui partage votre passion pour la glisse.', '2023-11-21 17:06:00', '2023-12-09 19:59:11', NULL, 'mute-grab'),
(4, 1, 'Stalefish Grab', 'Étendre votre Style Aérien.\r\n\r\nLe Stalefish Grab, une figure emblématique du snowboard, apporte une touche d\'élégance aérienne à chaque descente. Ce grab, caractérisé par la main arrière qui saisit le talon de la planche entre les fixations, offre un équilibre parfait entre style et technique.\r\n\r\nPour exécuter un Stalefish Grab impeccable, choisissez une rampe ou un kicker adapté à votre niveau de compétence. Lors du saut, fléchissez vos genoux et tendez votre main arrière derrière vous pour saisir le talon de la planche. La clé réside dans l\'extension complète du bras pour créer une position distinctive.\r\n\r\nMaintenez une position équilibrée en utilisant votre main avant et votre corps pour ajuster votre position en l\'air. Gardez votre regard fixé sur la trajectoire pour assurer un atterrissage en douceur. La pratique régulière permettra d\'affiner la coordination entre le grab et la rotation.\r\n\r\nLe Stalefish Grab offre des possibilités de variations infinies. Essayez d\'incliner la planche ou d\'ajouter une rotation pour personnaliser votre style. Les riders créatifs peuvent intégrer ce grab dans des figures plus complexes, ajoutant une dimension artistique à leur répertoire freestyle.\r\n\r\nQue vous soyez sur un snowpark ou en hors-piste, le Stalefish Grab s\'adapte à divers terrains. Commencez par des sauts modérés et progressez vers des descentes plus complexes au fur et à mesure de votre confiance. La maîtrise du Stalefish Grab enrichira votre expérience de snowboard en ajoutant une élégance distincte à chaque saut.\r\n\r\nIntégrez le Stalefish Grab à votre répertoire de tricks et découvrez le plaisir de mêler la grâce à l\'adrénaline sur les pistes enneigées. En embrassant cette figure emblématique, vous ajouterez une touche de style unique à chacune de vos descentes.', '2023-11-21 17:06:54', '2023-11-25 14:14:59', '9d7ec5c90e47fb80901fc4dc76c532dc.png', 'stalefish-grab'),
(5, 1, 'Tail Grab 101', 'Saisir l\'Arrière avec Précision.\r\n\r\nLe tail grab est l\'un des tricks emblématiques du snowboard, apportant une dose de style et de créativité à votre ride. Dans ce guide \"Tail Grab 101\", nous explorerons les fondamentaux de cette prise classique et partagerons des conseils pour saisir l\'arrière de votre planche avec précision.\r\n\r\nPour exécuter un tail grab, commencez par choisir une rampe ou un kicker approprié. Lorsque vous êtes en l\'air, fléchissez vos genoux pour créer une position stable. Utilisez votre main arrière pour saisir fermement l\'arrière de votre planche, près du tail, en étendant votre bras vers l\'arrière.\r\n\r\nLa clé du tail grab réside dans le timing et la précision. Attendez le moment opportun pour saisir le tail de votre planche, généralement au pic de votre saut. Gardez votre regard fixé sur l\'endroit où vous souhaitez atterrir pour maintenir un bon équilibre.\r\n\r\nPratiquez d\'abord le tail grab sur des sauts de taille modérée avant de progresser vers des sauts plus complexes. En ajustant la force de votre grab et la position de votre corps, vous pouvez personnaliser cette prise pour ajouter une touche unique à votre style de snowboard.\r\n\r\nIntégrer le tail grab à votre répertoire de tricks offre non seulement une esthétique visuelle, mais ajoute également une dimension ludique à votre expérience de ride. Explorez différentes variations, comme le tail grab tweak, pour donner une touche personnelle à ce classique du snowboard. Que vous soyez débutant ou rider confirmé, le tail grab est une figure polyvalente qui peut être appréciée sur toutes les pistes et dans tous les snowparks. En maîtrisant le Tail Grab 101, vous enrichirez votre style et impressionnerez vos pairs sur les pistes enneigées.', '2023-11-22 21:41:51', '2023-11-25 13:07:43', NULL, 'tail-grab-101'),
(6, 2, 'Le 180', 'Les Bases de la Rotation.\r\n\r\nLe 180, ou One Eighty, est une rotation fondamentale en snowboard. Apprenez les bases de ce trick en pivotant votre corps de moitié pendant le saut. \r\n\r\nTravaillez sur le timing et l\'équilibre pour réaliser une rotation fluide à 180 degrés. Pratiquez d\'abord sur des pentes douces avant de vous aventurer sur des sauts plus techniques.\r\n\r\nLe 180, un trick de snowboard emblématique, incarne l\'essence même du freestyle avec son tour élégant dans l\'air. Connu pour sa polyvalence, le 180 peut être exécuté sur des sauts, des kicks, et même des modules dans les snowparks.\r\n\r\nPour réaliser un 180 avec style, choisissez un lieu approprié avec un dégagement suffisant. En approchant le saut, fléchissez vos genoux et engagez une rotation du haut du corps. La clé réside dans le mouvement du haut du corps, où les épaules et la tête initient la rotation tandis que les jambes suivent.\r\n\r\nLorsque vous êtes en l\'air, utilisez vos bras pour accentuer la rotation et maintenir l\'équilibre. Gardez votre regard fixé sur la trajectoire pour assurer un atterrissage en douceur. La position du corps est cruciale, avec une légère inclinaison vers l\'arrière pour éviter un atterrissage trop abrupt.\r\n\r\nPratiquez d\'abord sur des sauts de taille modérée avant de progresser vers des sauts plus conséquents. La maîtrise du 180 ouvre la porte à des variations infinies, telles que le 180 avec grabs ou tweaks, permettant aux riders de personnaliser leur style.\r\n\r\nQue vous soyez un débutant enthousiaste ou un rider confirmé, le 180 offre une introduction idéale au monde du freestyle. En l\'intégrant à votre répertoire de tricks, vous ajouterez une touche de grâce et de dynamisme à chaque descente, faisant du 180 une figure incontournable pour tous les amateurs de snowboard.', '2023-11-22 21:37:49', '2023-12-09 19:57:31', 'd5ddbe5237627c97388dd1aaf0adeb9d.png', 'le-180'),
(7, 2, 'Le 360', 'Tour Complet, Adrénaline Maximale.\r\n\r\nLe 360, un joyau du snowboard freestyle, est une figure qui incarne la fusion parfaite entre technique, style, et adrénaline. Cette rotation complète de 360 degrés ajoute une dimension spectaculaire à chaque descente, devenant un incontournable pour les riders passionnés.\r\n\r\nPour réaliser un 360 avec brio, choisissez un saut ou un kicker adapté à votre niveau. En approchant le moment crucial, fléchissez vos genoux et engagez la rotation en pivotant votre tête et vos épaules dans la direction souhaitée. La clé réside dans la coordination entre le haut et le bas du corps.\r\n\r\nEn plein vol, utilisez vos bras pour maintenir l\'équilibre et accentuer la rotation. Gardez votre regard fixé sur la trajectoire pour un atterrissage en douceur. La position du corps est cruciale, avec une légère inclinaison vers l\'arrière pour éviter les atterrissages raides.\r\n\r\nLe 360 offre un terrain fertile pour la créativité. Intégrez des grabs pour une touche personnelle ou expérimentez avec des variations de style. Cette figure, bien que demandant de la pratique, devient une toile vierge pour les riders souhaitant exprimer leur individualité.\r\n\r\nQue vous soyez un rider débutant ou chevronné, le 360 est une étape captivante dans le monde du freestyle. Ajouter cette rotation à votre répertoire ouvre la porte à un univers de possibilités, transformant chaque descente en une expérience exaltante et artistique sur les pistes enneigées.', '2023-11-22 21:38:27', '2023-12-09 19:56:07', NULL, 'le-360'),
(8, 2, 'Corkscrew', 'La Rotation Torsadée Démystifiée.\r\n\r\nLe corkscrew, figure emblématique du snowboard freestyle, offre une torsion audacieuse qui ajoute une touche spectaculaire à votre répertoire de tricks. Cette rotation torsadée, réalisée en l\'air, est un défi excitant pour les riders cherchant à repousser les limites de leur créativité sur les pistes enneigées.\r\n\r\nPour exécuter un corkscrew impressionnant, commencez par choisir une rampe ou un kicker approprié à votre niveau. La clé de cette figure réside dans la torsion du corps pendant le saut. Initiez la rotation en tordant votre épaule avant dans le sens opposé de vos jambes.\r\n\r\nEn plein vol, utilisez vos bras pour maintenir l\'équilibre et accentuer la torsion. La coordination entre le haut et le bas du corps est cruciale pour un corkscrew réussi. Gardez votre regard fixé sur votre destination pour assurer un atterrissage en douceur.\r\n\r\nLe corkscrew offre une variété de possibilités de variations. Les riders expérimentés peuvent explorer des corkscrews avec des grabs ou intégrer cette rotation torsadée dans des figures plus complexes. Les variations peuvent ajouter une dimension artistique et personnelle à votre style de snowboard.\r\n\r\nQue vous soyez dans le backcountry, le snowpark, ou même en milieu urbain, le corkscrew s\'adapte à différents terrains. N\'hésitez pas à ajuster l\'amplitude de la torsion en fonction de vos préférences et des caractéristiques du terrain.\r\n\r\nEn intégrant le corkscrew à votre répertoire, vous découvrirez une nouvelle dimension de plaisir et de créativité. Alors, lancez-vous dans cette aventure torsadée et imprégnez vos descentes de cette rotation audacieuse qui ne manquera pas de captiver les regards admiratifs sur les pistes.', '2023-11-22 21:40:50', '2023-12-09 19:42:51', '43efe062a7e648bf583d7b8ade8bafd9.png', 'corkscrew'),
(9, 2, 'Misty Flip', 'Les Secrets de ce Flip Stylisé.\n\nLe Misty Flip, une figure captivante dans le monde du snowboard freestyle, combine élégance et technique. Ce flip stylisé, caractérisé par une rotation en arrière, offre une manière unique de défier la gravité et d\'ajouter une touche artistique à votre répertoire de tricks.\n\nPour réaliser un Misty Flip impressionnant, commencez par choisir une rampe ou un kicker adapté à votre niveau de compétence. En approchant le saut, fléchissez vos genoux et engagez la rotation en basculant votre corps en arrière. La clé réside dans la coordination entre le mouvement de vos épaules et la rotation de vos jambes.\n\nEn plein vol, utilisez vos bras pour maintenir l\'équilibre et accentuer le mouvement de rotation. Pour un Misty Flip parfait, veillez à garder votre regard sur votre trajectoire pour un atterrissage en douceur. Pratiquez d\'abord sur des sauts de taille modérée pour ajuster votre technique, puis progressez vers des sauts plus complexes à mesure que vous gagnez en confiance.\n\nLe Misty Flip offre un moyen unique de personnaliser votre style de snowboard. Jouez avec l\'amplitude de la rotation et expérimentez des variations pour rendre ce trick encore plus spectaculaire. Que vous soyez un rider débutant ou expérimenté, l\'exploration du Misty Flip ajoutera une dimension artistique à votre répertoire freestyle, transformant chaque descente en une performance unique et élégante. En intégrant ce flip stylisé à votre arsenal, vous repousserez les limites de votre créativité sur les pistes enneigées.', '2023-11-22 21:40:15', '2023-12-09 19:53:09', 'fb00d58074c175ffe58f68d2ebd020a1.png', 'misty-flip'),
(10, 2, 'Rodeo Flip', 'Guide Étape par Étape pour cette Rotation en Arrière.\r\n\r\nLe Rodeo Flip, une figure spectaculaire du snowboard freestyle, est une combinaison parfaite entre la rotation et le flip. Ce trick emblématique, caractérisé par une rotation en arrière associée à un flip, offre une esthétique audacieuse et une dose d\'adrénaline à chaque descente.\r\n\r\nPour exécuter un Rodeo Flip impressionnant, commencez par choisir une rampe ou un kicker approprié à votre niveau de compétence. Lors du saut, engagez la rotation en tordant votre épaule arrière vers l\'avant, tout en initiant le flip avec une impulsion des jambes.\r\n\r\nLa coordination entre la rotation et le flip est essentielle. Utilisez vos bras pour maintenir l\'équilibre et accentuer le mouvement. Gardez votre regard fixé sur la trajectoire pour assurer un atterrissage en douceur. La position du corps est cruciale, avec une légère inclinaison vers l\'arrière pour maintenir la stabilité.\r\n\r\nLe Rodeo Flip offre une variété de variations, permettant aux riders de personnaliser leur style. Ajoutez un grab pour une touche de créativité ou expérimentez avec différentes amplitudes pour varier l\'intensité du flip.\r\n\r\nQue vous soyez un amateur de snowpark ou un rider aguerri en hors-piste, le Rodeo Flip s\'adapte à divers terrains. La maîtrise de cette figure demande de la pratique, alors commencez par des sauts modérés avant de progresser vers des descentes plus complexes.\r\n\r\nIntégrer le Rodeo Flip à votre répertoire de tricks enrichira votre expérience de snowboard en ajoutant une dimension acrobatique et artistique à vos descentes. Alors, lancez-vous dans cette rotation éblouissante et transformez chaque descente en une performance aérienne captivante.', '2023-11-22 21:39:16', '2023-11-25 14:11:42', '9afc4a7d05d8915a65233639c6f68e92.png', 'rodeo-flip'),
(11, 3, 'Boardslide', 'Techniques pour Glisser avec Élégance.\r\n\r\nLe boardslide est l\'un des tricks de snowboard les plus emblématiques dans la catégorie des jibs et rails. Connu pour son mélange de style et de technique, le boardslide est un incontournable pour les riders passionnés de freestyle. Voici un guide détaillé sur la maîtrise de cette figure emblématique.\r\n\r\nPour exécuter un boardslide avec élégance, choisissez un rail ou un box de taille appropriée. En approchant le rail, fléchissez vos genoux pour absorber les irrégularités de la surface et maintenez votre regard fixé sur le bout du rail. Lorsque vous montez sur le rail, orientez votre planche perpendiculairement à la direction du rail, en engageant légèrement le nose pour établir le contact.\r\n\r\nLa clé d\'un boardslide réussi réside dans la position du corps. Gardez vos épaules parallèles au rail et maintenez le poids centré sur votre planche. Utilisez vos bras pour maintenir l\'équilibre, et ajustez votre position si nécessaire en fonction des caractéristiques du rail.\r\n\r\nLorsque vous atteignez la fin du rail, engagez la rotation du corps pour sortir du trick en toute fluidité. La pratique régulière sur différents types de rails vous permettra d\'ajuster votre technique et de perfectionner votre boardslide.\r\n\r\nAjouter le boardslide à votre répertoire de tricks de jibs et rails apportera une touche d\'élégance à votre style de snowboard. Que vous soyez débutant ou expert, maîtriser cette figure emblématique ouvre la porte à une variété infinie de variations et de combinaisons pour élever votre niveau de ride freestyle.', '2023-11-22 21:29:46', NULL, '01bdc3194fb3d013198886e614a63a80.png', 'boardslide');


--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `trick_id`, `user_id`, `content`, `created_at`) VALUES
(18, 5, 2, 'Super article sur le Tail Grab 101! J\'adore ce trick, ça ajoute vraiment de la classe à mon ride.', '2023-11-25 13:17:52'),
(19, 5, 3, 'Carrément! J\'ai essayé hier, c\'est un bon guide pour les débutants aussi. Des conseils utiles pour perfectionner le grab. Thumbs up!', '2023-11-25 13:18:05'),
(26, 9, 3, 'Super article sur le Misty Flip, non ?', '2023-11-25 13:46:33'),
(27, 9, 4, 'Carrément! Les astuces pour les variations m\'ont inspiré.', '2023-11-25 13:46:40'),
(28, 9, 5, 'J\'ai tenté le Misty Flip, c\'est intense. Des conseils pour la land ?', '2023-11-25 13:46:49'),
(29, 9, 3, 'Assure-toi de garder les yeux sur la piste pour un atterrissage en douceur.', '2023-11-25 13:47:00'),
(30, 9, 4, 'J\'ai vu un pro faire un Misty Flip aux X Games. Magnifique!', '2023-11-25 13:47:10'),
(31, 9, 5, 'Ça devait être fou! Des conseils pour perfectionner le grab ?', '2023-11-25 13:47:19'),
(32, 9, 3, 'Maintiens une prise ferme sur l\'arrière de la planche, ajuste selon ta vitesse.', '2023-11-25 13:47:28'),
(33, 9, 4, 'Vraiment utile! Et pour filmer ça de manière cool ?', '2023-11-25 13:47:37'),
(34, 9, 5, 'J\'ai essayé le Misty Flip dans la poudreuse, sensationnel !', '2023-11-25 13:47:45'),
(35, 9, 3, 'Tu as filmé ça ? Partage le lien, je veux voir !', '2023-11-25 13:47:52'),
(36, 9, 4, 'Carrément! J\'ai aussi tenté quelques tweaks avec le Misty Flip.', '2023-11-25 13:48:04'),
(37, 9, 5, 'Tweaks avec le Misty Flip ? Ça sonne génial, des astuces ?', '2023-11-25 13:48:12'),
(38, 9, 3, 'Essayez de jouer avec la position de vos jambes pendant le grab. Ça donne un style unique', '2023-11-25 13:48:19'),
(39, 9, 4, 'Bon conseil, je vais tester ça demain !', '2023-11-25 13:48:29'),
(40, 9, 5, 'Merci pour les conseils, les gars. On se retrouve sur les pistes!', '2023-11-25 13:48:38'),
(41, 9, 3, 'Définitivement! Amusez-vous bien !', '2023-11-25 13:48:46'),
(42, 8, 3, 'L\'article sur le corkscrew est génial! Prêt à tenter ça ce week-end.', '2023-11-25 14:09:40'),
(43, 8, 2, 'Carrément! Les corkscrews sont incroyables. Des conseils pour la torsion parfaite ?', '2023-11-25 14:09:48'),
(44, 10, 4, 'Le Rodeo Flip, tellement stylé ! Prêt à le tester dans la poudreuse.', '2023-11-25 14:12:34'),
(45, 10, 5, 'Carrément! Rodeo Flip avec un grab, c\'est la clé du style.', '2023-11-25 14:12:40'),
(46, 10, 2, 'Vu un pro faire un Rodeo Flip hier, incroyable! Des conseils pour les débutants ?', '2023-11-25 14:12:49'),
(47, 4, 4, 'Le Stalefish Grab, une classe folle! Prêt à l\'essayer.', '2023-11-25 14:15:12'),
(48, 4, 2, 'Stalefish Grab tweaké, une tuerie. Conseils pour un tweak stylé ?', '2023-11-25 14:15:20'),
(49, 4, 3, 'Stalefish Grab en backcountry, un délice ! Des astuces pour ajuster en hors-piste ?', '2023-11-25 14:15:30'),
(50, 4, 5, 'Stalefish Grab en milieu urbain, challenge accepted ! Des recommandations pour les rails ?', '2023-11-25 14:15:41'),
(51, 1, 3, 'L\'article sur l\'Indy Grab est super informatif! J\'adore ce grab pour son style unique. Des conseils pour le perfectionner davantage ?', '2023-11-25 14:17:55'),
(52, 11, 2, 'Le boardslide, un classique indémodable! Les conseils sont top pour perfectionner l\'élégance. Prêt à le maîtriser.', '2023-11-25 14:19:58'),
(53, 11, 5, 'Boardslide, une base essentielle. Merci pour les astuces, ça va ajouter du style à mes rides.', '2023-11-25 14:20:05');

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `trick_id`, `video_url`) VALUES
(10, 11, 'https://youtube.com/embed/eyHiSn8vqpQ?si=_I6T0uUlfdRKhjv6'),
(11, 10, 'https://youtube.com/embed/pI-iykKk_z4?si=QPeZRGGYpUYrsLzT'),
(12, 10, 'https://youtube.com/embed/vf9Z05XY79A?si=BYhNXtlzkDjhB9xv'),
(13, 9, 'https://youtube.com/embed/wLyKxhzh1Sc?si=O_fx5IKo1n0c5st_'),
(14, 9, 'https://youtube.com/embed/CH60g_09lY0?si=_x07mV2C1vY2f9Zn'),
(16, 4, 'https://youtube.com/embed/f9FjhCt_w2U?si=HYtYrtCVEHd-Pz9f'),
(18, 1, 'https://youtube.com/embed/t9NC5BNQ4D8?si=W150JeB-NehcvaHx'),
(41, 3, 'https://youtube.com/embed/k6aOWf0LDcQ?si=EeGaWND6nZI1IlpU'),
(42, 6, 'https://youtube.com/embed/XyARvRQhGgk?si=N4FoyjhjhP-VDlBG');

--
-- Dumping data for table `picture`
--

INSERT INTO `picture` (`id`, `trick_id`, `name`) VALUES
(36, 11, '6b1c862128daac7470a94d6ad7ea884a.png'),
(37, 11, '9d9e33176aa5214feaeffcc03cfb0a25.png'),
(38, 11, '01bdc3194fb3d013198886e614a63a80.png'),
(39, 10, '8b173171090a379c761ee9e42b432f65.png'),
(40, 10, '9afc4a7d05d8915a65233639c6f68e92.png'),
(52, 9, '64ec5de6828ca95bad3da8902ba49adb.png'),
(53, 9, 'fb00d58074c175ffe58f68d2ebd020a1.png'),
(55, 4, '9d7ec5c90e47fb80901fc4dc76c532dc.png'),
(110, 8, '43efe062a7e648bf583d7b8ade8bafd9.png'),
(111, 1, '15f76bb9b8091054efeecccaf031ec78.png'),
(112, 6, 'd5ddbe5237627c97388dd1aaf0adeb9d.png'),
(113, 2, 'c2ceb07b647b1037718166330ea7d63d.png');




/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
