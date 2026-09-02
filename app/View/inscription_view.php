<?php require ROOT . '/app/View/header_view.php'; ?>
<main class="content">
    <div class="flex justify-center">
        <!-- Formulaire d'inscription' -->
        <div class="box">
            <form class="form" action="?action=register" method="post">
                <h1>Inscription</h1>

                <div class="form-group">
                    <label for="first_name">Prénom : </label><br>
                    <input type="text" name="first_name" id="first_name" placeholder="Choisissez votre prénom"
                        value="<?php // Garde la valeur si pas d'erreurs, efface si il y en a
                                if (!isset($this->errors["first_name"])) {
                                    echo $_POST["first_name"] ?? '';
                                } else {
                                    echo '';
                                }; ?>" required>
                    <?php if (isset($this->errors['first_name'])) echo ("<br><span class='error'>" . $this->errors['first_name'] . "</span>"); ?>
                </div>

                <div class="form-group">
                    <label for="last_name">Nom : </label><br>
                    <input type="text" name="last_name" id="last_name" placeholder="Choisissez votre nom"
                        value="<?php // Garde la valeur si pas d'erreurs, efface si il y en a
                                if (!isset($this->errors["last_name"])) {
                                    echo $_POST["last_name"] ?? '';
                                } else {
                                    echo '';
                                }; ?>" required>
                    <?php if (isset($this->errors['last_name'])) echo ("<br><span class='error'>" . $this->errors['last_name'] . "</span>"); ?>
                </div>

                <div class="form-group">
                    <label for="email">Email : </label><br>
                    <input type="text" name="email" id="email" placeholder="Entrez votre email"
                        value="<?php // Garde la valeur si pas d'erreurs, efface si il y en a
                                if (!isset($this->errors["email"])) {
                                    echo $_POST["email"] ?? '';
                                } else {
                                    echo '';
                                }; ?>" required>
                    <?php if (isset($this->errors['email'])) echo ("<br><span class='error'>" . $this->errors['email'] . "</span>"); ?>
                </div>

                <div class="form-group">
                    <label for="email-confirm">Confirmez votre email : </label><br>
                    <input type="text" name="email-confirm" id="email-confirm" placeholder="Ré-entrez votre email"
                        value="<?php // Garde la valeur si pas d'erreurs, efface si il y en a
                                if (!isset($this->errors["email-confirm"])) {
                                    echo $_POST["email-confirm"] ?? '';
                                } else {
                                    echo '';
                                }; ?>" required>
                    <?php if (isset($this->errors['email-confirm'])) echo ("<br><span class='error'>" . $this->errors['email-confirm'] . "</span>"); ?>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe : </label><br>
                    <input type="password" name="password" id="password" placeholder="Choisissez un mot de passe" required>
                    <?php if (isset($this->errors["password"])) echo ("<br><span class='error'>" . $this->errors["password"] . "</span>"); ?>
                </div>

                <div class="form-group">
                    <label for="password-confirm">Confirmez votre mot de passe : </label><br>
                    <input type="password" name="password-confirm" id="password-confirm" placeholder="Ré-entrez le mot de passe" required>
                    <?php if (isset($this->errors["password-confirm"])) echo ("<br><span class='error'>" . $this->errors["password-confirm"] . "</span>"); ?>
                </div>

                <p class="login-switch"><a class=" blue-link" href="?action=login">Vous avez déjà un compte&nbsp;?</a></p>

                <div class="submit">
                    <input type="submit" class="button" value="Enregistrer">
                </div>
            </form>
        </div>
        <!---->
    </div>
</main>
<?php require ROOT . '/app/View/footer_view.php'; ?>