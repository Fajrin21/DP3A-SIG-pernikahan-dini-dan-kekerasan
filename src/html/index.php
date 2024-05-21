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

  // Periksa koneksi
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }


  $fetchQuery = "SELECT kabkota_kua FROM datapernikahan";  // Assuming you want to group by kabkota_kua
  $fetchResultNikah = $conn->query($fetchQuery);

  if ($fetchResultNikah->num_rows > 0) {
    $groupedData = [];
    while ($row = $fetchResultNikah->fetch_assoc()) {
      $kabkota_kua = $row['kabkota_kua'];
      $jumlah_pernikahan = 1;

      if (isset($groupedData[$kabkota_kua])) {
        $jumlah_pernikahan = $groupedData[$kabkota_kua] + 1;
      }

      $groupedData[$kabkota_kua] = $jumlah_pernikahan;
    }
  }


  if (isset($_GET['delete']) && isset($_GET['id'])) {
    // Get the ID from the URL
    $id = $_GET['id'];

    // Prepare a SQL statement to delete the record with the given ID
    $deleteQuery = "DELETE FROM datapernikahan WHERE id = $id";

    // Execute the delete query
    if ($conn->query($deleteQuery) === TRUE) {
      // Deletion successful, redirect to the same page without 'delete' parameter
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    } else {
      // Handle the error if deletion fails
      echo "Error deleting data: " . $conn->error;
    }
  }

  if (isset($_GET['fetch_data']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $fetchQuery = "SELECT * FROM datapernikahan WHERE id = $id";
    $fetchResult = $conn->query($fetchQuery);

    if ($fetchResult->num_rows > 0) {
      $rowData = $fetchResult->fetch_assoc();
      echo json_encode($rowData);
      exit();
    } else {
      echo json_encode(['error' => 'Data not found']);
      exit();
    }
  }

  $recordsPerPage = 5; // Adjust as needed
  $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
  $offset = ($page - 1) * $recordsPerPage;

  // Fetch total number of records
  $totalQuery = "SELECT COUNT(*) AS total FROM datapernikahan";
  $totalResult = $conn->query($totalQuery);
  $totalRecords = $totalResult->fetch_assoc()['total'];

  // Calculate total pages
  $totalPages = ceil($totalRecords / $recordsPerPage);

  // Fetch data for the current page
  $query = "SELECT * FROM datapernikahan LIMIT $offset, $recordsPerPage";
  $result = $conn->query($query);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form was submitted
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    // Validate other fields as needed
    $id = $_POST['id'];
    $nikIstri = $_POST['nik_istri'];
    $nikSuami = $_POST['nik_suami'];
    $namaSuami = $_POST['nama_suami'];
    $namaIstri = $_POST['nama_istri'];
    $ttlSuami = $_POST['ttl_suami'];
    $ttlIstri = $_POST['ttl_istri'];
    $tanggalNikah = $_POST['tanggal_nikah'];
    $usiaSuami = $_POST['usia_suami'];
    $usiaIstri = $_POST['usia_istri'];
    $pendidikanSuami = $_POST['pendidikanterakhir_suami'];
    $pendidikanIstri = $_POST['pendidikanterakhir_istri'];
    $alamatPernikahan = $_POST['alamat_nikah'];
    $saksiNikah = $_POST['saksi_nikah'];
    $faktorPernikahan = $_POST['faktor_pernikahan'];

    if ($id) {
      $updateQuery = "UPDATE datapernikahan SET nik_istri = '$nikIstri', nik_suami = '$nikSuami', nama_suami = '$namaSuami', nama_istri = '$namaIstri', ttl_suami = '$ttlSuami', ttl_istri = '$ttlIstri', tanggal_nikah = '$tanggalNikah', usia_suami = '$usiaSuami', usia_istri = '$usiaIstri', pendidikanterakhir_suami = '$pendidikanSuami', pendidikanterakhir_istri = '$pendidikanIstri', alamat_nikah = '$alamatPernikahan', saksi_nikah = '$saksiNikah', faktor_pernikahan = '$faktorPernikahan' WHERE id = $id";
      if ($conn->query($updateQuery) === TRUE) {
      } else {
      }
    } else {
      $insertQuery = "INSERT INTO datapernikahan (nik_istri, nik_suami, nama_suami, nama_istri, ttl_suami, ttl_istri, tanggal_nikah, usia_suami, usia_istri, pendidikanterakhir_suami, pendidikanterakhir_istri, alamat_nikah, saksi_nikah, faktor_pernikahan) VALUES ('$nikIstri', '$nikSuami', '$namaSuami', '$namaIstri', '$ttlSuami', '$ttlIstri', '$tanggalNikah', '$usiaSuami', '$usiaIstri', '$pendidikanSuami', '$pendidikanIstri', '$alamatPernikahan', '$saksiNikah', '$faktorPernikahan')";
      if ($conn->query($insertQuery) === TRUE) {
      } else {
      }
    }
  }
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DP3A ADMIN DASHBOARD</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
      #map {
        width: 800px;
        height: 500px;
      }

      .info {
        padding: 6px 8px;
        font: 14px/16px Arial, Helvetica, sans-serif;
        background: white;
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
      }

      .info h4 {
        margin: 0 0 5px;
        color: #777;
      }

      .legend {
        text-align: left;
        line-height: 18px;
        color: #555;
      }

      .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
      }
    </style>
    <script>
      function submitForm() {
        // Fetch form data
        var formData = $('#updateForm').serialize();

        // Send an AJAX request to update data
        $.ajax({
          type: 'POST',
          url: window.location.href,
          data: formData,
          success: function(response) {
            try {
              var data = JSON.parse(response);
              if (data.status === 'success') {
                // If update is successful, reload the page
                location.reload();
              } else {
                // If there is an error, display the error message
                alert('Error updating data: ' + data.message);
                location.reload();
              }
            } catch (error) {
              console.log(error);
            } finally {
              // Regardless of success or error, hide the modal
              $('#updateModal').modal('hide');
              location.reload();
            }
          },
          error: function(error) {
            console.log(error);
            // In case of an error, hide the modal
            $('#updateModal').modal('hide');
            location.reload();
          }
        });
      }

      function deleteData(id) {
        var isConfirmed = confirm("Are you sure you want to delete this record?");
        if (isConfirmed) {
          window.location.href = '?delete=true&id=' + id;
        }
      }

      function updateData(id) {
        $('#updateModal').modal('show');
        $.ajax({
          type: 'GET',
          url: window.location.href,
          data: {
            fetch_data: true,
            id: id
          },
          success: function(response) {
            try {
              var data = JSON.parse(response);
              $('#user_id').val(data.id);
              $('#nik_istri').val(data.nik_istri);
              $('#nik_suami').val(data.nik_suami);
              $('#nama_suami').val(data.nama_suami);
              $('#nama_istri').val(data.nama_istri);
              $('#ttl_suami').val(data.ttl_suami);
              $('#ttl_istri').val(data.ttl_istri);
              $('#tanggal_nikah').val(data.tanggal_nikah);
              $('#usia_suami').val(data.usia_suami);
              $('#usia_istri').val(data.usia_istri);
              $('#pendidikanterakhir_suami').val(data.pendidikanterakhir_suami);
              $('#pendidikanterakhir_istri').val(data.pendidikanterakhir_istri);
              $('#alamat_nikah').val(data.alamat_nikah);
              $('#saksi_nikah').val(data.saksi_nikah);
              $('#faktor_pernikahan').val(data.faktor_pernikahan);
              $('#tanggal_penginputan').val(data.tanggal_penginputan);
              $('#kabkota_kua').val(data.kabkota_kua);
            } catch (error) {
              console.log(error);
            }
          },
          error: function(error) {
            console.log(error);
          }
        });
      }
    </script>
  </head>

  <body>

    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
      <!-- Sidebar Start -->
      <aside class="left-sidebar">
        <!-- Sidebar scroll-->
        <div>
          <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="./index.php" class="text-nowrap logo-img">
              <img src="../assets/images/logos/logoasli.png" alt="" style="margin: 10px 30px" width="150px">
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
              <i class="ti ti-x fs-8"></i>
            </div>
          </div>
          <!-- Sidebar navigation-->
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
          <!-- End Sidebar navigation -->
        </div>
        <!-- End Sidebar scroll-->
      </aside>
      <!-- Sidebar End -->
      <!-- Main wrapper -->
      <div class="body-wrapper">
        <div class="container-fluid">
          <div class="container-fluid" style="margin-top: 100px">
            <div class="container" style="box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset; padding: 10px">
              <div id="map" style="width: 100%; height: 600px">
              </div>
            </div>
          </div>
          <br>
          <!-- Row 1 -->
          <div class="col-lg-12 d-flex align-items-stretch">
            <div class="card w-300">
              <div class="card-body p-4">
                <h5 class="card-title fw-semibold mb-4">Data Pernikahan Anak</h5>
                <div class="table-responsive">
                  <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                      <tr>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">NIK Istri</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">NIK Suami</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Nama Suami</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Nama Istri</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Tanggal Nikah</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">TTL Suami</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">TTL Istri</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Usia Suami</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Usia Istri</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Pendidikan Suami</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Pendidikan Istri</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Alamat Pernikahan</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Saksi Nikah</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Faktor Pernikahan</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Tanggal Input</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Kabupaten</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Tombol Aksi</h6>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php

                      while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['nik_istri'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['nik_suami'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['nama_suami'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['nama_istri'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['tanggal_nikah'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['ttl_suami'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['ttl_istri'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['usia_suami'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['usia_istri'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['pendidikanterakhir_suami'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['pendidikanterakhir_istri'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['alamat_nikah'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['saksi_nikah'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['faktor_pernikahan'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . date('Y-m-d', strtotime($row['tanggal_penginputan'])) . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['kabkota_kua'] . "</h6></td>";
                        echo "<td class='border-bottom-0'>
              <button class='btn btn-sm btn-primary' onclick='updateData(" . $row['id'] . ")'>Update</button>
              <button class='btn btn-sm btn-danger' onclick='deleteData(" . $row['id'] . ")'>Delete</button>
            </td>";

                        echo "</tr>";
                        echo "</tr>";
                      }
                      echo '<ul class="pagination">';
                      for ($i = 1; $i <= $totalPages; $i++) {
                        echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                      }
                      echo '</ul>';
                      ?>
                    </tbody>
                  </table>
                  <button><a href="fpdfpernikahan.php" class="btn btn-success">PRINT</a></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="updateModalLabel">Update Data</h5>
              <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              <!-- Update Form -->
              <form id="updateForm" action="" method="post">
                <div class="mb-3">
                  <input type="hidden" class="form-control" id="user_id" name="id">
                </div>
                <div class="mb-3">
                  <label for="nik_istri" class="form-label">NIK Istri</label>
                  <input type="text" class="form-control" id="nik_istri" name="nik_istri">
                </div>
                <div class="mb-3">
                  <label for="nik_suami" class="form-label">NIK Suami</label>
                  <input type="text" class="form-control" id="nik_suami" name="nik_suami">
                </div>

                <div class="mb-3">
                  <label for="nama_suami" class="form-label">Nama Suami</label>
                  <input type="text" class="form-control" id="nama_suami" name="nama_suami">
                </div>

                <div class="mb-3">
                  <label for="nama_istri" class="form-label">Nama Istri</label>
                  <input type="text" class="form-control" id="nama_istri" name="nama_istri">
                </div>

                <div class="mb-3">
                  <label for="ttl_suami" class="form-label">Tempat, Tanggal Lahir Suami</label>
                  <input type="date" class="form-control" id="ttl_suami" name="ttl_suami">
                </div>

                <div class="mb-3">
                  <label for="ttl_istri" class="form-label">Tempat, Tanggal Lahir Istri</label>
                  <input type="date" class="form-control" id="ttl_istri" name="ttl_istri">
                </div>

                <div class="mb-3">
                  <label for="tanggal_nikah" class="form-label">Tanggal Nikah</label>
                  <input type="date" class="form-control" id="tanggal_nikah" name="tanggal_nikah">
                </div>

                <div class="mb-3">
                  <label for="usia_suami" class="form-label">Usia Suami</label>
                  <input type="text" class="form-control" id="usia_suami" name="usia_suami">
                </div>

                <div class="mb-3">
                  <label for="usia_istri" class="form-label">Usia Istri</label>
                  <input type="text" class="form-control" id="usia_istri" name="usia_istri">
                </div>

                <div class="mb-3">
                  <label for="pendidikanterakhir_suami" class="form-label">Pendidikan Terakhir Suami</label>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                      <?php echo isset($_POST['pendidikanterakhir_suami']) ? $_POST['pendidikanterakhir_suami'] : 'Pendidikan Terakhir'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <li><a class="dropdown-item" onclick="updateDropdown4('SD')">SD</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown4('SLTP/SMP')">SLTP/SMP</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown4('SLTA/SMA')">SLTA/SMA</a></li>
                    </ul>
                    <input type="hidden" name="pendidikanterakhir_suami" id="pendidikanterakhir_suami" value="<?php echo isset($_POST['pendidikanterakhir_suami']) ? $_POST['pendidikanterakhir_suami'] : ''; ?>">
                  </div>
                </div>
                <script>
                  function updateDropdown4(value) {
                    document.querySelector("#dropdownMenuButton").innerText = value;
                    document.querySelector("#pendidikanterakhir_suami").value = value;
                  }
                </script>

                <div class="mb-3">
                  <label for="pendidikanterakhir_istri" class="form-label">Pendidikan Terakhir Istri</label>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                      <?php echo isset($_POST['pendidikanterakhir_istri']) ? $_POST['pendidikanterakhir_istri'] : 'Pendidikan Terakhir'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton4">
                      <li><a class="dropdown-item" onclick="updateDropdown3('SD')">SD</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown3('SLTP/SMP')">SLTP/SMP</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown3('SLTA/SMA')">SLTA/SMA</a></li>
                    </ul>
                    <input type="hidden" name="pendidikanterakhir_istri" id="pendidikanterakhir_istri" value="<?php echo isset($_POST['pendidikanterakhir_istri']) ? $_POST['pendidikanterakhir_istri'] : ''; ?>">
                  </div>
                </div>
                <script>
                  function updateDropdown3(value) {
                    document.querySelector("#dropdownMenuButton3").innerText = value;
                    document.querySelector("#saksi_nikah").value = value;
                  }
                </script>

                <div class="mb-3">
                  <label for="alamat_nikah" class="form-label">Alamat Pernikahan</label>
                  <input type="text" class="form-control" id="alamat_nikah" name="alamat_nikah">
                </div>

                <div class="mb-3">
                  <label for="saksi_nikah" class="form-label">Saksi</label>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                      <?php echo isset($_POST['saksi_nikah']) ? $_POST['saksi_nikah'] : 'Pilih Saksi Pernikahan'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                      <li><a class="dropdown-item" onclick="updateDropdown('Aparat Desa')">Aparat Desa</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown('Lembaga Adat')">Lembaga Adat</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown('Imam Masjid')">Imam Masjid</a></li>
                    </ul>
                    <input type="hidden" name="saksi_nikah" id="saksi_nikah" value="<?php echo isset($_POST['saksi_nikah']) ? $_POST['saksi_nikah'] : ''; ?>">
                  </div>
                </div>

                <script>
                  function updateDropdown(value) {
                    document.querySelector("#dropdownMenuButton1").innerText = value;
                    document.querySelector("#saksi_nikah").value = value;
                  }
                </script>

                <div class="mb-3">
                  <label for="faktor_pernikahan" class="form-label">Faktor</label>
                  <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false" name="faktor_pernikahan">
                      <?php echo isset($_POST['faktor_pernikahan']) ? $_POST['faktor_pernikahan'] : 'Pilih Faktor Pernikahan'; ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                      <li><a class="dropdown-item" onclick="updateDropdown2('Pergaulan Bebas')">Pergaulan
                          Bebas</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown2('Perjodohan')">Perjodohan</a></li>
                      <li><a class="dropdown-item" onclick="updateDropdown2('Budaya')">Budaya</a></li>
                    </ul>
                    <input type="hidden" name="faktor_pernikahan" id="faktor_pernikahan" value="<?php echo isset($_POST['faktor_pernikahan']) ? $_POST['faktor_pernikahan'] : ''; ?>">
                  </div>
                </div>

                <script>
                  function updateDropdown2(value) {
                    document.querySelector("#dropdownMenuButton2").innerText = value;
                    document.querySelector("#faktor_pernikahan").value = value;
                  }
                </script>

                <div class="mb-3">
                  <label for="tanggal_input" class="form-label">Tanggal Input</label>
                  <input type="text" class="form-control" id="tanggal_input" name="tanggal_input" value="<?php echo date('Y-m-d'); ?>" disabled>
                </div>
              </form>
              <div class="modal-footer">
                <!-- Update button triggers form submission using JavaScript -->
                <button type="button" class="btn btn-primary" onclick="submitForm()">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
        <script src="../assets/js/sidebarmenu.js"></script>
        <script src="../assets/js/app.min.js"></script>
        <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
        <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
        <script src="../assets/js/dashboard.js"></script>
  </body>
  <script src="assets/js/main.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

  <script src="data/ajaxleaflet.js"></script>

  <script>
    var map = L.map("map").setView([-1.4409980545720098, 121.42291172678082], 8);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(map);

    const info = L.control();

    info.onAdd = function(map) {
      this._div = L.DomUtil.create('div', 'info');
      this.update();
      return this._div;
    };
    <?php echo 'const iniDataku =' . json_encode($groupedData) ?>

    console.log(iniDataku['BUOL']);

    info.update = function(props) {
      // const contents = props ? `<b>${props.KAB_KOTA}</b><br />${props.POPULASI} jumlah pernikahan dini` : 'Hover';
      const contents = props ? `<b>${props.KAB_KOTA}</b><br />${iniDataku[props.KAB_KOTA]} jumlah pernikahan dini` : 'Hover';
      this._div.innerHTML = `<h4>SULTENG Data Pernikahan Anak</h4>${contents}`;
    };

    info.addTo(map);


    // get color depending on population density value
    function getColor(d) {
      return d > 71 ? '#800026' :
        d > 61 ? '#BD0026' :
        d > 51 ? '#E31A1C' :
        d > 41 ? '#FC4E2A' :
        d > 31 ? '#FD8D3C' :
        d > 21 ? '#FEB24C' :
        d > 11 ? '#FED976' :
        '#FFEDA0';
    }

    function style(feature) {
      return {
        weight: 2,
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7,
        fillColor: getColor(iniDataku[feature.properties.KAB_KOTA])
      };
    }

    function highlightFeature(e) {
      const layer = e.target;

      layer.setStyle({
        weight: 5,
        color: '#666',
        dashArray: '',
        fillOpacity: 0.7
      });

      layer.bringToFront();

      info.update(layer.feature.properties);
    }

    function resetHighlight(e) {
      var layer = e.target;
      layer.setStyle({
        weight: 2,
        opacity: 1,
        color: 'white',
        dashArray: '3',
      });
      info.update();
    }

    function zoomToFeature(e) {
      map.fitBounds(e.target.getBounds());
    }

    function onEachFeature(feature, layer) {
      layer.on({
        mouseover: highlightFeature,
        mouseout: resetHighlight,
        click: zoomToFeature
      });
    }

    const legend = L.control({
      position: 'bottomright'
    });

    legend.onAdd = function(map) {

      const div = L.DomUtil.create('div', 'info legend');
      const grades = [0, 10, 20, 30, 40, 50, 60, 70];
      const labels = [];
      let from, to;

      for (let i = 0; i < grades.length; i++) {
        from = grades[i];
        to = grades[i + 1];

        labels.push(`<i style="background:${getColor(from + 1)}"></i> ${from}${to ? `&ndash;${to}` : '+'}`);
      }

      div.innerHTML = labels.join('<br>');
      return div;
    };

    legend.addTo(map);

    var jsonTest = new L.GeoJSON.AJAX(["data/tes.geojson"], {
      style: style,
      onEachFeature: onEachFeature
    }).addTo(map);
  </script>

  </html>