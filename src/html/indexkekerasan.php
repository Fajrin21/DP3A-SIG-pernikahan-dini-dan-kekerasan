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


  $fetchQuery = "SELECT kabkota FROM datakekerasan";  // Assuming you want to group by kabkota_kua
  $fetchResultNikah = $conn->query($fetchQuery);

  if ($fetchResultNikah->num_rows > 0) {
    $groupedData = [];
    while ($row = $fetchResultNikah->fetch_assoc()) {
      $kabkota = $row['kabkota'];
      $jumlah_kekerasan = 1;

      if (isset($groupedData[$kabkota])) {
        $jumlah_kekerasan = $groupedData[$kabkota] + 1;
      }

      $groupedData[$kabkota] = $jumlah_kekerasan;
    }
  }

  // Cek apakah formulir dikirim (melalui metode POST)
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai dari formulir
    $nama_korban = $_POST['nama_korban'];
    $nama_pelaku = $_POST['nama_pelaku'];

    // Query SQL untuk mencari data berdasarkan nama korban atau nama pelaku
    $query = "SELECT * FROM datakekerasan WHERE nama_korban LIKE '%$nama_korban%' OR nama_pelaku LIKE '%$nama_pelaku%'";

    // Eksekusi query
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
      // Tampilkan hasil pencarian
      while ($row = $result->fetch_assoc()) {
        echo "Nama Korban: " . $row['nama_korban'] . "<br>";
        echo "Nama Pelaku: " . $row['nama_pelaku'] . "<br>";
        // Tampilkan kolom lainnya sesuai kebutuhan
      }
    } else {
      echo "Data tidak ditemukan.";
    }
  }


  if (isset($_GET['delete']) && isset($_GET['id'])) {
    // Get the ID from the URL
    $id = $_GET['id'];

    // Prepare a SQL statement to delete the record with the given ID
    $deleteQuery = "DELETE FROM datakekerasan WHERE id = $id";

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
    $fetchQuery = "SELECT * FROM datakekerasan WHERE id = $id";
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
  $totalQuery = "SELECT COUNT(*) AS total FROM datakekerasan";
  $totalResult = $conn->query($totalQuery);
  $totalRecords = $totalResult->fetch_assoc()['total'];

  // Calculate total pages
  $totalPages = ceil($totalRecords / $recordsPerPage);

  // Fetch data for the current page
  $query = "SELECT * FROM datakekerasan LIMIT $offset, $recordsPerPage";
  $result = $conn->query($query);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the form was submitted
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    // Validate other fields as needed
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
    $kabkota = $_POST['kabkota'];
    $tanggalinput = date('Y-m-d');
    $kabkota = $_POST['kabkota'];


    if ($tempatkejadian === "LAINNYA") {
      $tempatkejadian = $_POST['tempat_kejadian_lainnya'];
    }
  

    if ($id) {
      $updateQuery = "UPDATE datakekerasan SET nama_korban = '$namakorban', nama_pelaku = '$namapelaku', jk_korban = '$jkkorban', jk_pelaku = '$jkpelaku', umur_korban = '$umurkorban', umur_pelaku = '$umurpelaku', status_korban = '$statuskorban', status_pelaku = '$statuspelaku', pendidikan_korban = '$pendidikankorban', tempat_kejadian = '$tempatkejadian', kronologi = '$kronologi', tanggal_penginputan = '$tanggalinput', kabkota = '$kabkota' WHERE id = $id";
      if ($conn->query($updateQuery) === TRUE) {
        echo "Record updated successfully";
      } else {
        echo "Error updating record: " . $conn->error;
      }
    } else {
      $insertQuery = "INSERT INTO datakekerasan (nama_korban, nama_pelaku, jk_korban, jk_pelaku, umur_korban, umur_pelaku, status_korban, status_pelaku, pendidikan_korban, tempat_kejadian, kronologi, tanggal_penginputan, kabkota, bukti) VALUES ('$namakorban', '$namapelaku', '$jkkorban', '$jkpelaku', '$umurkorban', '$umurpelaku', '$statuskorban', '$statuspelaku', '$pendidikankorban', '$tempatkejadian', '$kronologi', '$tanggalinput', '$kabkota', '$bukti')";
      if ($conn->query($insertQuery) === TRUE) {
        echo "New record created successfully";
      } else {
        echo "Error: " . $insertQuery . "<br>" . $conn->error;
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
              $('#nama_korban').val(data.nama_korban);
              $('#nama_pelaku').val(data.nama_pelaku);
              $('#jk_korban').val(data.jk_korban);
              $('#jk_pelaku').val(data.jk_pelaku);
              $('#umur_korban').val(data.umur_korban);
              $('#umur_pelaku').val(data.umur_pelaku);
              $('#status_korban').val(data.status_korban);
              $('#status_pelaku').val(data.status_pelaku);
              $('#pendidikan_korban').val(data.pendidikan_korban);
              $('#tempat_kejadian').val(data.tempat_kejadian);
              $('#bukti').val(data.bukti);
              $('#kronologi').val(data.kronologi);
              $('#tanggal_penginputan').val(data.tanggal_penginputan);
              $('#kabkota').val(data.kabkota);
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
                    <i class="ti ti-layout-dashboard  "></i>
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
                <h5 class="card-title fw-semibold mb-4">Data Kekerasan</h5>
                <div class="table-responsive">
                  <table class="table text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                      <tr>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Nama Korban</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Nama Pelaku</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Jenis Kelamin Korban</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Jenis Kelamin Pelaku</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Umur Korban</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Umur Pelaku</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Status Korban </h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Status Pelaku</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Pendidikan Korban</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Tempat Kejadian</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Kronologi</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Bukti</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Tanggal Input</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">kabupaten</h6>
                        </th>
                        <th class="border-bottom-0">
                          <h6 class="fw-semibold mb-0">Tombol Aksi</h6>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php

                      while ($row = $result->fetch_assoc()) {
                        $fileDirectory = 'bukti/';

                        $bukti = $fileDirectory . $row['bukti'];

                        echo "<tr>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['nama_korban'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['nama_pelaku'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['jk_korban'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['jk_pelaku'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['umur_korban'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['umur_pelaku'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['status_korban'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['status_pelaku'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['pendidikan_korban'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['tempat_kejadian'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['kronologi'] . "</h6></td>";
                        echo "<td class='border-bottom-0'><img src='" . $bukti . "' style='width:100px;height:100px;'></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . date('Y-m-d', strtotime($row['tanggal_penginputan'])) . "</h6></td>";
                        echo "<td class='border-bottom-0'><h6 class='fw-semibold mb-0'>" . $row['kabkota'] . "</h6></td>";
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
                  <div>
                    <button><a href="fpdfkekerasan.php" class="btn btn-success">PRINT</a></button>
                  </div>
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
                  <label for="nama_korban" class="form-label">Nama Korban</label>
                  <input type="text" class="form-control" id="nama_korban" name="nama_korban">
                </div>
                <div class="mb-3">
                  <label for="nama_pelaku" class="form-label">Nama Pelaku</label>
                  <input type="text" class="form-control" id="nama_pelaku" name="nama_pelaku">
                </div>

                <div class="mb-3">
                  <label for="jk_korban" class="form-label">Jenis Kelamin Korban</label>
                  <input type="text" class="form-control" id="jk_korban" name="jk_korban">
                </div>

                <div class="mb-3">
                  <label for="jk_pelaku" class="form-label">Jenis Kelamin Pelaku</label>
                  <input type="text" class="form-control" id="jk_pelaku" name="jk_pelaku">
                </div>

                <div class="mb-3">
                  <label for="umur_korban" class="form-label">Umur Korban</label>
                  <input type="text" class="form-control" id="umur_korban" name="umur_korban">
                </div>

                <div class="mb-3">
                  <label for="umur_pelaku" class="form-label">Umur Pelaku</label>
                  <input type="text" class="form-control" id="umur_pelaku" name="umur_pelaku">
                </div>

                <div class="mb-3">
                  <label for="status_korban" class="form-label">Status Korban</label>
                  <input type="text" class="form-control" id="status_korban" name="status_korban">
                </div>

                <div class="mb-3">
                  <label for="status_pelaku" class="form-label">Status Pelaku</label>
                  <input type="text" class="form-control" id="status_pelaku" name="status_pelaku">
                </div>

                <!-- <div class="mb-3">
                  <label for="pendidikan_korban" class="form-label">Pendidikan Korban</label>
                  <input type="text" class="form-control" id="pendidikan_korban" name="pendidikan_korban">
                </div> -->

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

                <!-- Inputan baru untuk tempat kejadian -->
                <div class="mb-3" id="tempat_kejadian_lainnya" style="display: none;">
                  <label for="tempat_kejadian_lainnya" class="form-label">Tempat Kejadian Lainnya</label>
                  <input type="text" class="form-control" id="tempat_kejadian_lainnya" name="tempat_kejadian_lainnya">
                </div>

                <div class="form-floating">
                  <textarea class="form-control" placeholder="Leave a comment here" id="kronologi" name="kronologi" style="height: 100px"></textarea>
                  <label for="kronologi">Kronologi Singkat</label>
                </div>

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

                <div class="mb-3">
                  <label for="tanggal_penginputan" class="form-label">Tanggal Input</label>
                  <input type="text" class="form-control" id="tanggal_penginputan" name="tanggal_penginputan" value="<?php echo date('Y-m-d'); ?>" disabled>
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

    // info.update = function(props) {
    //   const contents = props ? `<b>${props.KAB_KOTA}</b><br />${iniDataku[props.KAB_KOTA]} Jumlah Data Kekerasan` : 'Hover';
    //   this._div.innerHTML = `<h4>SULTENG Data Kekerasan</h4>${contents}`;
    // };

    // info.addTo(map);


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