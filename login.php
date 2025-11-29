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
    session_start(); // Mulai session untuk menyimpan status login
    require 'db_connect.php';

    $message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Username dan Password harus diisi!</div>';
        } else {
            // 1. Prepared Statement untuk SELECT (Anti SQL Injection)
            $sql = "SELECT id, username, password FROM users WHERE username = ?";

            if ($stmt = $conn->prepare($sql)) {
                // Bind parameter
                $stmt->bind_param("s", $username);

                // Eksekusi statement
                $stmt->execute();

                // Ambil hasil
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    // Username ditemukan, ambil data baris
                    $row = $result->fetch_assoc();
                    $hashed_password = $row['password'];

                    // 2. Verifikasi Password
                    // password_verify() adalah cara aman untuk mencocokkan password dengan hash
                    if (password_verify($password, $hashed_password)) {
                        // Password cocok, buat session
                        $_SESSION['loggedin'] = true;
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['username'] = $row['username'];

                        // Redirect ke halaman dashboard atau home
                        header("location: dashboard.php"); // Ganti dengan halaman tujuan Anda
                        exit;
                    } else {
                        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Password salah.</div>';
                    }
                } else {
                    $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Username tidak ditemukan.</div>';
                }

                // Tutup statement
                $stmt->close();
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-bug"></i> Error prepare statement: ' . $conn->error . '</div>';
            }
        }
    }

    // Tutup koneksi
    $conn->close();
    ?>
    <?php /* Sertakan kode HTML, Bootstrap, dan Font Awesome CDN di sini (dari poin 2) */ ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 form-container">
                <h3 class="text-center mb-4"><i class="fas fa-sign-in-alt"></i> Login Akun</h3>
                <?php echo $message; ?>
                <form action="login.php" method="post">
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success"><i class="fas fa-key"></i> Login</button>
                    </div>
                </form>
                <p class="mt-3 text-center">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </div>
    </div>
    <?php /* Tutup tag </body> dan </html> di sini */ ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>