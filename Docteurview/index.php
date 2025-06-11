<?php include 'header.php'; ?>

<div class="content">
    <?php
        $page = $_GET['page'] ?? 'dashboard';

        switch ($page) {
            case 'emploi_temps':
                include 'content/emploi_temps.php';
                break;
            case 'prescriptions':
                include 'content/prescriptions.php';
                break;
            case 'patients':
                include 'content/patients.php';
                break;
            default:
                include 'content/dashboard.php';
        }
    ?>
</div>

<?php include 'footer.php'; ?>