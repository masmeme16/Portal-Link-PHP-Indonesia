<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Anda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Gaya tambahan opsional */
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 400px;
            margin-top: 50px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .05);
            background-color: white;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <?php
    require 'db_connect.php'; // Sertakan file koneksi

    $message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // 1. Validasi Input
        if (empty($username) || empty($password)) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Username dan Password harus diisi!</div>';
        } else {
            // 2. Hash Password
            // password_hash() adalah fungsi yang sangat aman dan direkomendasikan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 3. Prepared Statement untuk INSERT (Anti SQL Injection)
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

            // Inisialisasi prepared statement
            if ($stmt = $conn->prepare($sql)) {
                // Bind parameter ke statement
                // "ss" berarti ada dua string (s) yang akan diikat
                $stmt->bind_param("ss", $username, $hashed_password);

                // Eksekusi statement
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Registrasi berhasil! Silakan <a href="login.php">Login</a>.</div>';
                } else {
                    // Biasanya terjadi karena username sudah ada (jika di-set UNIQUE)
                    $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Registrasi gagal. Mungkin username sudah terdaftar.</div>';
                }

                // Tutup statement
                $stmt->close();
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-bug"></i> Error prepare statement: ' . $conn->error . '</div>';
            }
        }
    }

    // Tutup koneksi setelah selesai menggunakan database
    $conn->close();
    ?>
    <?php /* Sertakan kode HTML, Bootstrap, dan Font Awesome CDN di sini (dari poin 2) */ ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 form-container">
                <h3 class="text-center mb-4"><i class="fas fa-user-plus"></i> Daftar Akun Baru</h3>
                <?php echo $message; ?>
                <form action="register.php" method="post">
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Register</button>
                    </div>
                </form>
                <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>
    <?php /* Tutup tag </body> dan </html> di sini */ ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>