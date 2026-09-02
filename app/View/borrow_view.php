<?php require ROOT . '/app/View/header_view.php'; ?>
<main class="content">
    <div class="flex justify-center">
        <div class="box">

            <form class="form" action="?action=borrow" method="post">
                <h2>Réserver du matériel</h2>

                <div class="form-group">
                    <label for="hardware">Matériel : </label><br>
                    <select name="hardware" id="hardware" required>
                        <option value="">-- Choisissez un matériel --</option>
                        <?php foreach ($hardwareList as $hardware): ?>
                            <option
                                value="<?= htmlspecialchars($hardware['id_hardware']) ?>"
                                <?= (isset($_POST['hardware']) && $_POST['hardware'] == $hardware['id_hardware']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($hardware['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php
                    if (isset($this->errors['hardware'])) {
                        echo ("<br><span class='error'>" . $this->errors['hardware'] . "</span>");
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label for="date_start">Date de début : </label><br>
                    <input type="date" name="date_start" id="date_start" value="<?= htmlspecialchars($_POST['date_start'] ?? '') ?>" required>
                    <?php
                    if (isset($this->errors['date_start'])) {
                        echo ("<br><span class='error'>" . $this->errors['date_start'] . "</span>");
                    }
                    ?>
                </div>

                <div class="form-group">
                    <label for="date_end">Date de fin : </label><br>
                    <input type="date" name="date_end" id="date_end" value="<?= htmlspecialchars($_POST['date_end'] ?? '') ?>" required
                    >
                    <?php
                    if (isset($this->errors['date_end'])) {
                        echo ("<br><span class='error'>" . $this->errors['date_end'] . "</span>");
                    }
                    ?>
                </div>

                <div class="submit">
                    <input type="submit" value="Réserver">
                </div>

            </form>
        </div>
    </div>

</main>

<?php require ROOT . '/app/View/footer_view.php'; ?>
