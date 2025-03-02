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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $type = trim($_POST['type']);
    $localisation = trim($_POST['localisation']);
    
    $owner_id = $_SESSION['user_id'] ?? 1; // Assurez-vous que l'utilisateur est connecté et a un ID valide

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_fields = ['image_01', 'image_02', 'image_03', 'image_04'];
    $image_paths = [];
    
    foreach ($image_fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $file_name = time() . '_' . basename($_FILES[$field]['name']);
            $file_tmp = $_FILES[$field]['tmp_name'];
            $file_size = $_FILES[$field]['size'];
            $file_path = $upload_dir . $file_name;
            
            if ($file_size > 2000000) {
                $_SESSION['message'] = "L'image " . $field . " est trop volumineuse!";
            } else {
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $image_paths[$field] = $file_name;
                } else {
                    $image_paths[$field] = '';
                }
            }
        } else {
            $image_paths[$field] = '';
        }
    }

    // Préparer la requête SQL
    $stmt = $conn->prepare("INSERT INTO proprietes (titre, description, prix, type, localisation, proprietaire_id, image_01, image_02, image_03, image_04, cree_a) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param(
        'ssdsiissss',
        $titre, 
        $description, 
        $prix, 
        $type, 
        $localisation, 
        $owner_id, 
        $image_paths['image_01'], 
        $image_paths['image_02'], 
        $image_paths['image_03'], 
        $image_paths['image_04']
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Propriété ajoutée avec succès.";
    } else {
        $_SESSION['message'] = "Erreur : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirection pour éviter la réinsertion des données si l'utilisateur recharge la page
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="icon" type="image/png" href="logo2.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une propriété</title>
    <script>
        window.onload = function() {
            <?php if (!empty($_SESSION['message'])): ?>
                alert("<?php echo $_SESSION['message']; ?>");
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        };
    </script>
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
        input, textarea, select, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .hero {
           /* background: 
                linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.7) 70%, #000000 100%),
                url('amin.jpg') no-repeat center center/cover;*/
            height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 6px #000;
            padding: 0 20px;
        }
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-align: center;
        }
        .hero a {
            background-color: transparent;
            color: rgb(255, 255, 255);
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            border: 2px solid white;
            transition: background-color 0.3s, color 0.3s;
        }
        .hero a:hover {
            background-color: transparent;
            color: #ffffff;
            border-color: #f4a261;
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
        <a href="deconnexion1.php" class="connexion-btn">Déconnexion</a>
    </nav>

    <div class="hero">
        <img src="logo2.png" alt="Agence Immobilière Logo" style="max-width: 100px; margin-bottom: 20px;">
        <h1>Bienvenue sur notre Agence Immobilière</h1>
    </div>

    <?php if (!empty($message)) : ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <h1>Ajouter une Propriété</h1>
        <div class="form-group">
            <label for="titre">Titre de la propriété :</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        <div class="form-group">
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="prix">Prix (€) :</label>
            <input type="number" id="prix" name="prix" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="type">Type de propriété :</label>
            <select id="type" name="type" required>
                <option value="appartement">Appartement</option>
                <option value="maison">Maison</option>
                <option value="commercial">Commercial</option>
                <option value="terrain">Terrain</option>
            </select>
        </div>
        <div class="form-group">
            <label for="localisation">Localisation :</label>
            <input type="text" id="localisation" name="localisation" required>
        </div>
        <div class="box">
         <p>image 01 <span>*</span></p>
         <input type="file" name="image_01" class="input" accept="image/*" required>
      </div>
      <div class="flex"> 
         <div class="box">
            <p>image 02</p>
            <input type="file" name="image_02" class="input" accept="image/*">
         </div>
         <div class="box">
            <p>image 03</p>
            <input type="file" name="image_03" class="input" accept="image/*">
         </div>
         <div class="box">
            <p>image 04</p>
            <input type="file" name="image_04" class="input" accept="image/*">
         </div>
         <div class="box">
            <p>image 05</p>
            <input type="file" name="image_05" class="input" accept="image/*">
         </div>   
      </div>
        <button type="submit">Ajouter la propriété</button>
    </form>

    <footer>
        <p>&copy; 2025 Agence Immobilière. Tous droits réservés.</p>
        <p>
            Contactez-nous : mimwarimmo@gmail.com | +33 1 23 45 67 89
            <a href="connexion.php">Connexion</a>
        </p>
    </footer>

</body>
</html>