<?php
// Tidak memerlukan session_start() karena halaman ini diakses publik
require 'db_connect.php';

$menus = [];
$links_by_menu = [];

// 1. Ambil Semua Data Menu
$sql_menu = "SELECT menu_id, menu_name FROM menu ORDER BY menu_id ASC";
if ($stmt_menu = $conn->prepare($sql_menu)) {
    $stmt_menu->execute();
    $result_menu = $stmt_menu->get_result();
    while ($row = $result_menu->fetch_assoc()) {
        $menus[] = $row;
    }
    $stmt_menu->close();
}

// 2. Ambil Semua Data Link, dikelompokkan berdasarkan menu_id
$sql_link = "SELECT link_id, link_menu_id, link_name, link_redirect FROM link ORDER BY link_menu_id ASC, link_name ASC";
if ($stmt_link = $conn->prepare($sql_link)) {
    $stmt_link->execute();
    $result_link = $stmt_link->get_result();

    // Kelompokkan link berdasarkan link_menu_id
    while ($row = $result_link->fetch_assoc()) {
        $links_by_menu[$row['link_menu_id']][] = $row;
    }
    $stmt_link->close();
}

// Data links_by_menu akan dikirim ke JavaScript untuk diolah
$json_links = json_encode($links_by_menu);

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Akses Cepat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .menu-button {
            height: 120px;
            font-size: 1.5rem;
            margin: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s, box-shadow 0.2s;
            background-color: #007bff;
            /* Warna Primer */
            border: none;
        }

        .menu-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            background-color: #0056b3;
        }

        .links-container {
            display: none;
            /* Sembunyikan secara default */
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .link-item {
            display: block;
            margin-bottom: 10px;
            padding: 10px 15px;
            background-color: #28a745;
            /* Warna Sukses */
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .link-item:hover {
            background-color: #1e7e34;
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="text-center mb-5">!!!GANTI!!!</h1>
        <h3 class="text-center mb-5"><i class="fas fa-cubes"></i> Pilih Kategori Akses</h3>

        <div id="menu-area" class="row justify-content-center">
            <?php if (empty($menus)): ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> Belum ada Menu utama yang ditambahkan.
                </div>
            <?php else: ?>
                <?php foreach ($menus as $menu): ?>
                    <div class="col-md-4 col-sm-6 col-12">
                        <button
                            class="btn btn-primary menu-button w-100"
                            data-menu-id="<?= htmlspecialchars($menu['menu_id']) ?>">
                            <?= htmlspecialchars($menu['menu_name']) ?>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="links-container" class="links-container">
            <h3 id="links-title" class="text-center mb-3"></h3>
            <div id="links-list">
            </div>
            <button id="back-button" class="btn btn-warning mt-3 w-100"><i class="fas fa-arrow-left"></i> Kembali ke Menu Utama</button>
        </div>

        <hr class="mt-5">
        <div class="text-center text-secondary">
            <small>&copy; <?php echo date("Y"); ?> Sistem Akses Cepat</small>
            <div>Created by (GANTI INI)</div>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Data link dari PHP, siap diolah JavaScript
        const LINKS_DATA = <?= $json_links ?>;

        $(document).ready(function() {

            // --- 1. Event Listener untuk Tombol Menu ---
            $('.menu-button').on('click', function() {
                const menuId = $(this).data('menu-id');
                const menuName = $(this).text().trim(); // Ambil nama menu dari teks tombol

                showLinks(menuId, menuName);
            });

            // --- 2. Fungsi Tampilkan Link ---
            function showLinks(menuId, menuName) {
                const links = LINKS_DATA[menuId];
                const $linksList = $('#links-list');

                // Bersihkan daftar link lama
                $linksList.empty();

                // Set judul
                $('#links-title').text(`Pilihan Link untuk Kategori: ${menuName}`);

                if (links && links.length > 0) {
                    // Iterasi dan cetak link
                    links.forEach(link => {
                        const linkHtml = `
                        <a href="${link.link_redirect}" target="_blank" class="link-item">
                            <i class="fas fa-external-link-alt me-2"></i> ${link.link_name}
                        </a>
                    `;
                        $linksList.append(linkHtml);
                    });
                } else {
                    // Jika tidak ada link terkait
                    $linksList.append('<div class="alert alert-danger text-center"><i class="fas fa-info-circle"></i> Tidak ada link yang terdaftar pada menu ini.</div>');
                }

                // Ganti tampilan: Sembunyikan menu, tampilkan link
                $('#menu-area').fadeOut(400, function() {
                    $('#links-container').fadeIn(400);
                });
            }

            // --- 3. Event Listener Tombol Kembali ---
            $('#back-button').on('click', function() {
                // Ganti tampilan: Sembunyikan link, tampilkan menu
                $('#links-container').fadeOut(400, function() {
                    $('#menu-area').fadeIn(400);
                });
            });

        });
    </script>
</body>

</html>