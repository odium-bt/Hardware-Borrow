<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Hardware Borrow</title>
    <meta name="description" content="Site d'emprunt de matériel informatique pour le Greta-CFA de Vannes">
    <link rel="stylesheet" href="./public/css/style.css">
</head>

<body>

    <header>
        <div class="logo">
            <a href="?action=home">
                <h1>Hardware Borrow</h1>
            </a>
        </div>

        <nav id="header-nav">
            <ul>
                <?php if (!isset($_SESSION['user_id'])) { ?>
                    <li id="header-nav-login" class="<?= $_GET['action'] === 'login' ? 'active' : '' ?>"><a href="?action=login"><i class="fa-regular fa-circle-user"></i>&nbsp;Connexion</a></li>
                <?php } else { ?>
                    <li id="header-nav-hardware" class="<?= $_GET['action'] === 'hardware' ? 'active' : '' ?>"><a href="?action=hardware"><i class="fa-solid fa-magnifying-glass"></i>&nbsp;Matériel</a></li>
                    <li id="header-nav-borrow" class="<?= $_GET['action'] === 'borrow' ? 'active' : '' ?>"><a href="?action=borrow"><i class="fa-solid fa-magnifying-glass"></i>&nbsp;Emprunter</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>