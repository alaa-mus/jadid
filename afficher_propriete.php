<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "immobilier";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les propriétés depuis la base de données
$sql = "SELECT * FROM proprietes ORDER BY cree_a DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="logo2.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Propriétés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
            background: 
                linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.7) 70%, #000000 100%),
                url('amin.jpg') no-repeat center center/cover;
        }
        form {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        nav {
    display: flex;
    justify-content: center;
    background-color: #000000;
    padding: 15px;
    gap: 15px;
}

nav a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    font-size: 16px;
    padding: 8px 12px;
    transition: transform 0.3s, color 0.3s, box-shadow 0.3s;
}

nav a:hover {
    color: #f4a261;
    transform: scale(1.1); /* Zoom-in effect */
    text-shadow: 0 0 10px rgba(244, 162, 97, 0.8); /* Glow effect */
}

nav .connexion-btn {
    margin-left: auto;
    background-color: #f8f9fa;
    color: #000;
    padding: 8px 15px;
    border-radius: 5px;
    font-weight: 700;
    text-decoration: none;
    transition: background-color 0.3s, color 0.3s, transform 0.3s, box-shadow 0.3s;
}

nav .connexion-btn:hover {
    background-color: #f4a261;
    color: white;
    transform: scale(1.1); /* Zoom-in effect */
    box-shadow: 0 0 10px rgba(244, 162, 97, 0.8); /* Glow effect */
}


        /**/ 
        .property {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .property h2 {
            color:rgb(0, 0, 0);
            margin-bottom: 10px;
            
        }
        .property p {
            color: #555;
            margin: 5px 0;
            font-size: 30px;
            
        }
        .photos-container {
            overflow-x: auto;
            white-space: nowrap;
            padding: 10px 0;
        }
        .photos img {
            width: 500px;
            height: 450px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
            display: inline-block;
            transition: transform 0.3s;
        }
        .photos img:hover {
            transform: scale(1.1);
        }
        footer {
            background-color: #000000;
            color: white;
            text-align: center;
            padding: 20px 10px;
            font-size: 0.9rem;
        }
        footer a {
            color: #f4a261;
            text-decoration: none;
            font-weight: 500;
        }
        footer a:hover {
            text-decoration: underline;
        }
        .filters form .flex .box .input{
   width: 100%;
   margin: 1rem 0;
   font-size: 1.8rem;
   color: var(--black);
   border: var(--border);
   padding: 1.4rem;
}
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="afficher_propriete.php">Propriétés</a>
        <a href="favoris.php">Favoris</a>
        <a href="contact.php">Contact</a>
        <a href="connexion1.php" class="connexion-btn">Déconnexion</a>
    </nav>

    <div class="container">
        <h1>Liste des Propriétés</h1>

        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="property">
                    <h2><?= htmlspecialchars($row['titre']) ?> </h2>
                    <p><strong>Type :</strong> <?= htmlspecialchars($row['type']) ?></p>
                    <p><strong>Prix :</strong> <?= number_format($row['prix'], 2) ?> €</p>
                    <p><strong>Localisation :</strong> <?= htmlspecialchars($row['localisation']) ?></p>
                    <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($row['description'])) ?></p>
                    
                    <div class="photos-container">
                        <div class="photos">
                            <?php 
                            $images = [];
                            if (!empty($row['image_01'])) $images[] = "uploads/" . $row['image_01'];
                            if (!empty($row['image_02'])) $images[] = "uploads/" . $row['image_02'];
                            if (!empty($row['image_03'])) $images[] = "uploads/" . $row['image_03'];
                            if (!empty($row['image_04'])) $images[] = "uploads/" . $row['image_04'];
                            if (!empty($row['photos'])) {
                                $more_images = explode(',', $row['photos']);
                                foreach ($more_images as $img) {
                                    $images[] = htmlspecialchars($img);
                                }
                            }
                            foreach ($images as $img) : ?>
                                <img src="<?= htmlspecialchars($img) ?>" alt="Photo de la propriété">
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Aucune propriété disponible.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 Agence Immobilière. Tous droits réservés.</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>
