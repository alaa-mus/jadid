<?php
session_start(); // Démarrer la session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "immobilier";
$error = ""; // Variable pour stocker un éventuel message d'erreur

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $role_saisi = $_POST['role']; // Récupérer le rôle sélectionné

    // Requête sécurisée pour vérifier l'utilisateur
    $stmt = $conn->prepare('SELECT * FROM utilisateurs WHERE nom_utilisateur = ?');
    $stmt->bind_param('s', $nom_utilisateur);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Vérifier le mot de passe et le rôle
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        if ($user['role'] === $role_saisi) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['nom_utilisateur'];

            // Redirection selon le rôle
            if ($user['role'] === 'proprietaire') {
                header('Location: ajouter_proprties.php');
            } else {
                header('Location: dashboard.php');
            }
            exit;
        } else {
            $error = "Rôle incorrect. Veuillez vérifier votre sélection.";
        }
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="icon" type="image/png" href="logo2.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: url('ch.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: rgba(242, 239, 231, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }
        .login-container img {
            max-width: 80px;
            margin-bottom: 15px;
        }
        .login-container h1 {
            font-size: 1.5rem;
            color: #000000;
            margin-bottom: 15px;
        }
        .login-container label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
            text-align: left;
            font-size: 0.9rem;
        }
        .login-container input,
        .login-container select,
        .login-container button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .login-container button {
            background-color: #000000;
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-container button:hover {
            background-color: #012b55;
        }
        .login-container p {
            text-align: center;
            margin-top: 10px;
            font-size: 0.8rem;
        }
        .login-container p a {
            color: #00509E;
            text-decoration: none;
        }
        .login-container p a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="logo2.png" alt="Logo">
        <h1>Connexion</h1>

        <!-- Affichage du message d'erreur -->
        <?php if (!empty($error)) : ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="role">Je suis :</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="proprietaire">Propriétaire</option>
                <option value="visiteur">Visiteur</option>
            </select>
            
            <label for="nom_utilisateur">Nom d'utilisateur :</label>
            <input type="text" id="nom_utilisateur" name="nom_utilisateur" placeholder="Entrez votre nom d'utilisateur" required>
            
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Entrez votre mot de passe" required>
            
            <button type="submit">Se Connecter</button>
        </form>
        
        <p>Pas encore inscrit ? <a href="creer_compte.html">Créer un compte</a></p>
    </div>
</body>
</html>
