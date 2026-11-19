<?php
// includes/footer.php
?>
</div> <!-- Cierre del contenedor principal -->

<footer class="footer-main">
    &copy; <?= date('Y') ?> ML Industries - Departamento de Sistemas
</footer>

<!-- Librería SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['login_exitoso'])): ?>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        Toast.fire({
            icon: "success",
            title: "Sesión iniciada correctamente"
        });
    </script>
    <?php unset($_SESSION['login_exitoso']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['alerta'])): ?>
    <script>
        // AQUÍ PUEDES PONER TU ALERTA PERSONALIZADA DE SWEETALERT2
        Swal.fire({
            icon: "<?= $_SESSION['alerta']['icono'] ?>",
            title: "<?= $_SESSION['alerta']['titulo'] ?>",
            text: "<?= $_SESSION['alerta']['texto'] ?? '' ?>",
            confirmButtonColor: "#2563eb"
        });
    </script>
    <?php unset($_SESSION['alerta']); ?>
<?php endif; ?>

</body>
</html>