<?php

session_start();

  // Fungsi untuk logout
  function logout()
  {
    // Hapus semua data sesi
    $_SESSION = array();

    // Hapus cookie sesi jika ada
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    // Hancurkan sesi
    session_destroy();

    // Redirect ke halaman login atau halaman lain yang sesuai
    header("Location:  ../../../form login/login.php");
    exit();
  }

  // Periksa apakah tombol logout ditekan
  if (isset($_POST['logout'])) {
    logout();
  }


// Lakukan koneksi ke database (gunakan informasi koneksi Anda)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datapernikahananak";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$key = "kuncisaya";


// Cek apakah formulir dikirim (melalui metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Ambil nilai dari formulir
  // Ganti ini dengan nama-nama kolom di tabel Anda
  $namakorban = $_POST['nama_korban'];
  $namapelaku = $_POST['nama_pelaku'];
  $jkkorban = $_POST['jk_korban'];
  $jkpelaku = $_POST['jk_pelaku'];
  $umurkorban = $_POST['umur_korban'];
  $umurpelaku = $_POST['umur_pelaku'];
  $statuskorban = $_POST['status_korban'];
  $statuspelaku = $_POST['status_pelaku'];
  $pendidikankorban = $_POST['pendidikan_korban'];
  $tempatkejadian = $_POST['tempat_kejadian'];
  $kronologi = $_POST['kronologi'];
  $tanggalinput = date('Y-m-d'); // Tanggal penginputan
  $kabkota = $_POST['kabkota'];
  $subject = $_POST['subject'];

  if ($tempatkejadian === "LAINNYA") {
    $tempatkejadian = $_POST['tempat_kejadian_lainnya'];
  }


  $bukti = $_FILES['bukti']['name'];
  $bukti_tmp = $_FILES['bukti']['tmp_name'];
  move_uploaded_file($bukti_tmp, 'bukti/' . $bukti);


  $sql = "INSERT INTO datakekerasan (nama_korban, nama_pelaku, jk_korban, jk_pelaku, umur_korban, umur_pelaku, status_korban, status_pelaku, pendidikan_korban, tempat_kejadian, kronologi, tanggal_penginputan, kabkota,  subject, bukti)
    VALUES ('$namakorban', '$namapelaku', '$jkkorban', '$jkpelaku', '$umurkorban', '$umurpelaku', '$statuskorban', '$statuspelaku', '$pendidikankorban', '$tempatkejadian', '$kronologi', '$tanggalinput', '$kabkota', '$subject', '$bukti')";

  if ($conn->query($sql) === TRUE) {
  } else {
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.php" class="text-nowrap logo-img">
            <h1>DP3A</h1>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>

        <aside class="left-sidebar">
          <div>
            <div class="brand-logo d-flex align-items-center justify-content-between">
              <a href="./index.php" class="text-nowrap logo-img">
                <img src="../assets/images/logos/logoasli.png" alt="" style="margin: 10px 30px" width="150px">
              </a>
              <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
              </div>
            </div>
            <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
              <ul id="sidebarnav">
                <li class="nav-small-cap">
                  <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                  <span class="hide-menu">Dashboard</span>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link" href="./index.php" aria-expanded="false">
                    <span>
                      <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Data Pernikahan</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link" href="./datapernikahan.php" aria-expanded="false">
                    <span>
                      <i class="ti ti-file-description"></i>
                    </span>
                    <span class="hide-menu">Forms Pernikahan</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link" href="./indexkekerasan.php" aria-expanded="false">
                    <span>
                      <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Data Kekerasan</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link" href="./datakekerasan.php" aria-expanded="false">
                    <span>
                      <i class="ti ti-file-description"></i>
                    </span>
                    <span class="hide-menu">Forms Kekerasan</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a class="sidebar-link" href="./complaint.php" aria-expanded="false">
                    <span>
                      <i class="ti ti-layout-dashboard"></i>
                    </span>
                    <span class="hide-menu">Complaint Kekerasan</span>
                  </a>
                </li>
                <li class="sidebar-item">
                <a class="sidebar-link" href="./complaintpernikahan.php" aria-expanded="false">
                  <span>
                    <i class="ti ti-layout-dashboard"></i>
                  </span>
                  <span class="hide-menu">Complaint Pernikahan</span>
                </a>
              </li>
                <li class="sidebar-item">
                  <form method="post" action="">
                    <input class='btn btn-danger' type="submit" name="logout" value="Logout">
                  </form>
                </li>
              </ul>
            </nav>
          </div>
      </div>
    </aside>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-4">Forms</h5>
        <div class="card">
          <div class="card-body">
            <form method="post" action="" enctype="multipart/form-data">

              <div class="body-wrapper">

                <label for="kabkota" class="form-label">Kabupaten</label>
                <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton5" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo isset($_POST['kabkota']) ? $_POST['kabkota'] : 'Kabupaten'; ?>
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton5">
                    <li><a class="dropdown-item" onclick="updateDropdown5('KOTA PALU')">Kota Palu</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('SIGI')">Kabupaten Sigi</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('DONGGALA')">Kabupaten Donggala</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('PARIGI MOUTONG')">Kabupaten Parigi Moutong</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('POSO')">Kabupaten Poso</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('TOJO UNA-UNA')">Kabupaten Tojo Una-Una</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('MOROWALI')">Kabupaten Morowali</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('BANGGAI')">Kabupaten Banggai</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('BANGGAI LAUT')">Kabupaten Banggai Laut</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('BANGGAI KEPULAUAN')">Kabupaten Banggai Kepulauan</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('TOLITOLI')">Kabupaten Toli-Toli</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('BUOL')">Kabupaten Buol</a></li>
                    <li><a class="dropdown-item" onclick="updateDropdown5('MOROWALI UTARA')">Kabupaten Morowali Utara</a></li>
                  </ul>
                  <input type="hidden" name="kabkota" id="kabkota" value="<?php echo isset($_POST['kabkota']) ? $_POST['kabkota'] : ''; ?>" required>
                </div>
                <script>
                  function updateDropdown5(value) {
                    document.querySelector("#dropdownMenuButton5").innerText = value;
                    document.querySelector("#kabkota").value = value;
                  }
                </script>
                <br>

                <div class="mb-3">
                  <label for="subject" class="form-label">Jenis Kekerasan</label>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton6" data-bs-toggle="dropdown" aria-expanded="false">
                      <?php echo isset($_POST['subject']) ? $_POST['subject'] : 'Jenis Kekerasan'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton6">
                      <li><a class="dropdown-item" onclick="updateDropdown8('Kekerasan Fisik')">Kekerasan Fisik</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown8('Kekerasan Psikis')">Kekerasan Psikis</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown8('Kekerasan Seksual')">Kekerasan Seksual</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown8('Kekerasan Verbal')">Vekerasan Verbal</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown8('Perundungan')">Perundungan</a></li>
                    </ul>
                    <input type="hidden" name="subject" id="subject" value="<?php echo isset($_POST['subject']) ? $_POST['subject'] : ''; ?>" required>
                  </div>
                </div>
                <script>
                  function updateDropdown8(value) {
                    document.querySelector("#dropdownMenuButton6").innerText = value;
                    document.querySelector("#subject").value = value;
                  }
                </script>

                <div class="mb-3">
                  <label for="nama_korban" class="form-label">Nama Korban</label>
                  <input type="text" class="form-control" id="nama_korban" name="nama_korban" required>
                </div>
                <div class="mb-3">
                  <label for="nama_pelaku" class="form-label">Nama Pelaku</label>
                  <input type="text" class="form-control" id="nama_pelaku" name="nama_pelaku" required>
                </div>

                <div class="mb-3">
                  <label for="jk_korban" class="form-label">Jenis Kelamin Korban</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="jk_korban" id="jk_laki" value="Laki-laki" required>
                    <label class="form-check-label" for="jk_laki">
                      Laki-laki
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="jk_korban" id="jk_perempuan" value="Perempuan" required>
                    <label class="form-check-label" for="jk_perempuan">
                      Perempuan
                    </label>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="jk_pelaku" class="form-label">Jenis Kelamin Pelaku</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="jk_pelaku" id="jk_laki" value="Laki-laki" required>
                    <label class="form-check-label" for="jk_laki">
                      Laki-laki
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="jk_pelaku" id="jk_perempuan" value="Perempuan" required>
                    <label class="form-check-label" for="jk_perempuan">
                      Perempuan
                    </label>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="umur_korban" class="form-label">Umur Korban</label>
                  <input type="text" class="form-control" id="umur_korban" name="umur_korban" required>
                </div>

                <div class="mb-3">
                  <label for="umur_pelaku" class="form-label">Umur Pelaku</label>
                  <input type="text" class="form-control" id="umur_pelaku" name="umur_pelaku" required>
                </div>

                <div class="mb-3">
                  <label for="status_korban" class="form-label">Status Korban</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status_korban" id="korban_anak" value="Anak" required>
                    <label class="form-check-label" for="korban_anak">
                      Anak
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status_korban" id="korban_dewasa" value="Dewasa" required>
                    <label class="form-check-label" for="korban_dewasa">
                      Dewasa
                    </label>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="status_pelaku" class="form-label">Status Pelaku</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status_pelaku" id="pelaku_anak" value="Anak" required>
                    <label class="form-check-label" for="pelaku_anak">
                      Anak
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status_pelaku" id="pelaku_dewasa" value="Dewasa" required>
                    <label class="form-check-label" for="pelaku_dewasa">
                      Dewasa
                    </label>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="pendidikan_korban" class="form-label">Pendidikan Korban</label>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                      <?php echo isset($_POST['pendidikan_korban']) ? $_POST['pendidikan_korban'] : 'Pendidikan Terakhir'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <li><a class="dropdown-item" onclick="updateDropdown4('TK/PAUD')">TK/PAUD</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown4('SD')">SD</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown4('SLTP/SMP')">SLTP/SMP</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown4('SLTA/SMA')">SLTA/SMA</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown4('PERGURUAN TINGGI')">PERGURUAN TINGGI</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown4('BELUM PERNAH BERSEKOLAH')">BELUM PERNAH BERSEKOLAH</a></li>


                    </ul>
                    <input type="hidden" name="pendidikan_korban" id="pendidikan_korban" value="<?php echo isset($_POST['pendidikan_korban']) ? $_POST['pendidikan_korban'] : ''; ?>" required>
                  </div>
                </div>
                <script>
                  function updateDropdown4(value) {
                    document.querySelector("#dropdownMenuButton").innerText = value;
                    document.querySelector("#pendidikan_korban").value = value;
                  }
                </script>

                <div class="mb-3">
                  <label for="tempat_kejadian" class="form-label">Tempat Kejadian</label>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                      <?php echo isset($_POST['tempat_kejadian']) ? $_POST['tempat_kejadian'] : 'Tempat Kejadian'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                      <li><a class="dropdown-item" onclick="updateDropdown3('RUMAH TANGGA')">RUMAH TANGGA</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown3('TEMPAT KERJA')">TEMPAT KERJA</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown3('SEKOLAH/PERGURUAN TINGGI')">SEKOLAH/PERGURUAN TINGGI</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown3('FASILITAS UMUM')">FASILITAS UMUM</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown3('LEMBAGA PENDIDIKAN KILAT')">LEMBAGA PENDIDIKAN KILAT</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown3('LAINNYA')">Lainnya</a></li>
                    </ul>
                    <input type="hidden" name="tempat_kejadian" id="tempat_kejadian" value="<?php echo isset($_POST['tempat_kejadian']) ? $_POST['tempat_kejadian'] : ''; ?>" required>
                  </div>
                </div>

                <div class="mb-3" id="tempat_kejadian_lainnya" style="display: none;">
                  <label for="tempat_kejadian_lainnya" class="form-label">Tempat Kejadian Lainnya</label>
                  <input type="text" class="form-control" id="tempat_kejadian_lainnya" name="tempat_kejadian_lainnya">
                </div>

                <div class="form-floating">
                  <textarea class="form-control" placeholder="Leave a comment here" id="kronologi" name="kronologi" style="height: 100px"></textarea>
                  <label for="kronologi">Kronologi Singkat</label>
                </div>

                <div class="form-group">
                  <label for="bukti" class="form-text">Proof Image</label>
                  <input type="file" class="form-control" name="bukti" id="bukti" required accept=".jpg, .jpeg, .png">
                </div>

                <div class="mb-3">
                  <label for="tanggal_penginputan" class="form-label">Tanggal Input</label>
                  <input type="text" class="form-control" id="tanggal_penginputan" name="tanggal_penginputan" value="<?php echo date('Y-m-d'); ?>" disabled>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
</body>

<script>
  function updateDropdown3(value) {
    if (value === 'LAINNYA') {
      // Tampilkan inputan baru
      document.getElementById('tempat_kejadian_lainnya').style.display = 'block';
    } else {
      // Sembunyikan inputan baru
      document.getElementById('tempat_kejadian_lainnya').style.display = 'none';
    }

    // Perbarui teks pada tombol dropdown
    document.querySelector("#dropdownMenuButton3").innerText = value;
    document.querySelector("#tempat_kejadian").value = value;
  }
</script>

<script>
  // Mendapatkan elemen umur korban dan pelaku
  var umurKorbanInput = document.getElementById('umur_korban');
  var umurPelakuInput = document.getElementById('umur_pelaku');

  // Mendapatkan elemen radio button untuk status korban dan pelaku
  var statusKorbanAnak = document.getElementById('korban_anak');
  var statusKorbanDewasa = document.getElementById('korban_dewasa');
  var statusPelakuAnak = document.getElementById('pelaku_anak');
  var statusPelakuDewasa = document.getElementById('pelaku_dewasa');

  // Mendengarkan perubahan pada input umur korban
  umurKorbanInput.addEventListener('change', function() {
    if (parseInt(umurKorbanInput.value) < 18) {
      statusKorbanAnak.checked = true;
    } else {
      statusKorbanDewasa.checked = true;
    }
  });

  // Mendengarkan perubahan pada input umur pelaku
  umurPelakuInput.addEventListener('change', function() {
    if (parseInt(umurPelakuInput.value) < 18) {
      statusPelakuAnak.checked = true;
    } else {
      statusPelakuDewasa.checked = true;
    }
  });
</script>

</html>