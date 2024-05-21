<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datapernikahananak";


$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//jumlah pernikahan perkab/kota
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

$fetchQuery = "SELECT kabkota_kua, faktor_pernikahan, COUNT(*) AS jumlah FROM datapernikahan GROUP BY kabkota_kua, faktor_pernikahan";
$fetchResult = $conn->query($fetchQuery);

$most_common_factor = []; // Menyimpan faktor terbanyak untuk setiap kabupaten/kota

if ($fetchResult->num_rows > 0) {
    // Loop untuk mendapatkan faktor terbanyak pada setiap kabupaten/kota
    while ($row = $fetchResult->fetch_assoc()) {
        $kabkota_kua = $row['kabkota_kua'];
        $faktor_pernikahan = $row['faktor_pernikahan'];
        $jumlah = $row['jumlah'];

        // Periksa apakah sudah ada faktor sebelumnya untuk kabupaten/kota ini
        if (isset($most_common_factor[$kabkota_kua])) {
            // Jika ada, periksa apakah jumlah faktor saat ini lebih besar dari yang sebelumnya
            if ($jumlah > $most_common_factor[$kabkota_kua]['jumlah']) {
                // Jika iya, update faktor terbanyak
                $most_common_factor[$kabkota_kua] = [
                    'faktor' => $faktor_pernikahan,
                    'jumlah' => $jumlah
                ];
            }
        } else {
            // Jika belum ada faktor sebelumnya untuk kabupaten/kota ini, tambahkan
            $most_common_factor[$kabkota_kua] = [
                'faktor' => $faktor_pernikahan,
                'jumlah' => $jumlah
            ];
        }
    }
}


$conn->close();
?>

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TUGAS AKHIR</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Jost:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet" />

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet" />
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet" />
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet" />
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
</head>

<body>
    <header id="header" class="fixed-top" style="background-color: rgba(40, 58, 90, 0.9)">
        <div class="container d-flex align-items-center">
            <h1 class="logo me-auto"><a href="index.php">DP3A</a></h1>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto" href="index.php">Home</a></li>
                    <li class="dropdown scrollto">
                        <a href="#" style="cursor: hand"><span>Profil</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="informasidp3a.html">Informasi Kelembagaan</a></li>
                            <li><a href="daftardp3a.html">Daftar Dinas P3A</a></li>
                            <li><a href="#">Daftar Program Unggulan</a></li>
                        </ul>
                    </li>
                    <li class="dropdown active"><a href="#" style="color: white; cursor: hand;"><span>Pendataan</span> <i class="bi bi-chevron-down"></i></a>
                        <ul>
                            <li><a href="datapernikahan.php">Data Pernikahan</a></li>
                            <li><a href="datakekerasan.php">Data Kekerasan</a></li>
                        </ul>
                    </li>
                    <!-- <li><a class="nav-link scrollto" href="berita.html">Berita</a></li> -->
                    <li class="dropdown active"><a href="#" style="color: white; cursor: hand;"><span>Pengaduan</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              <li><a href="pengaduanpernikahan.php">Pengaduan Pernikahan Dini</a></li>
              <li><a href="pengaduankekerasan.php">Pengaduan Kekerasan</a></li>
            </ul>
          </li>                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>
    <br><br><br>

    <div class="container-fluid" style="margin-top: 100px">
        <div class="container-fluid" style="margin-top: 100px">
            <div class="container" style="box-shadow: rgba(6, 24, 44, 0.4) 0px 0px 0px 2px, rgba(6, 24, 44, 0.65) 0px 4px 6px -1px, rgba(255, 255, 255, 0.08) 0px 1px 0px inset; padding: 10px">
                <div id="map" style="width: 100%; height: 600px">
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="container">
                <section id="datapernikahan">
                    <table id="data-table" class="table table-striped table-hover">
                        <thead class="table-primary fw-bold">
                            <tr>
                                <td>No</td>
                                <td>Kabupaten/Kota</td>
                                <td>Jumlah kasus</td>
                                <td>Faktor Terbanyak</td>
                            </tr>
                        </thead>

                        <?php
                        // Membuat nomor urut untuk setiap baris
                        $nomor_urut = 1;

                        // Loop melalui data faktor terbanyak untuk setiap kabupaten/kota yang telah diurutkan
                        foreach ($most_common_factor as $kabkota_kua => $data) {
                            echo "<tr>";
                            echo "<td>" . $nomor_urut . "</td>"; // Menampilkan nomor urut
                            echo "<td>" . $kabkota_kua . "</td>"; // Menampilkan nama kabupaten/kota
                            echo "<td>" . $groupedData[$kabkota_kua] . "</td>"; // Menampilkan jumlah kasus pernikahan
                            echo "<td>" . $data['faktor'] . "</td>"; // Menampilkan faktor terbanyak
                            echo "</tr>";

                            $nomor_urut++; // Meningkatkan nomor urut setiap kali looping
                        }
                        ?>
                    </table>
                </section>
            </div>
        </div>
    </div>

    <script>
        // Wait for the DOM content to be fully loaded
        document.addEventListener("DOMContentLoaded", function() {
            //fungsi untuk menyorting jumlah kasus (jumlah kasus)
            function sortTable() {
                var table, rows, switching, i, x, y, shouldSwitch;
                table = document.getElementById("data-table");
                switching = true;
                while (switching) {
                    switching = false;
                    rows = table.getElementsByTagName("tr");
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        x = rows[i].getElementsByTagName("td")[2]; // Change index to match your column
                        y = rows[i + 1].getElementsByTagName("td")[2]; // Change index to match your column
                        if (parseInt(x.innerHTML) < parseInt(y.innerHTML)) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                    if (shouldSwitch) {
                        // Swap rows
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                    }
                }

                // Reassigning row numbers
                var rowCount = 1;
                for (var j = 1; j < rows.length; j++) {
                    var row = rows[j].getElementsByTagName("td")[0];
                    if (row) {
                        row.innerHTML = rowCount;
                        rowCount++;
                    }
                }
            }

            // Call the sortTable function when the page is loaded
            sortTable();
        });
    </script>

</body>
<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 footer-contact">
                    <h3>DINAS PEMBERDAYAAN PEREMPUAN DAN PERLINDUNGAN ANAK PROVINSI SULAWESI TENGAH</h3>
                    <p>
                        Jl. Mangunsarkoro No.31<br />
                        Besusu Timur <br />
                        Kec. Palu Timur Kota Palu<br />
                        Sulawesi Tengah 94111<br /><br />
                        <strong>Phone:</strong> +62 822 3122 8860<br />
                        <strong>Email:</strong> info@example.com<br />
                    </p>
                </div>

                <div class="col">
                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4>Ikuti Kami</h4>
                        <div class="social-links mt-3">
                            <a href="#" class="facebook"><i class="bx bxl-facebook"></i></a>
                            <a href="https://www.instagram.com/dp3asulteng_official/" class="instagram"><i class="bx bxl-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container footer-bottom clearfix">
        <div class="copyright">
            &copy; Copyright <strong><span>Dinas P3A Sulawesi Tengah</span></strong>.
        </div>
    </div>
</footer>
<!-- End Footer -->

<div id="preloader"></div>
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/waypoints/noframework.waypoints.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>
<script src="assets/js/ajaxleaflet.js"></script>

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


    info.update = function(props) {
        // const contents = props ? `<b>${props.KAB_KOTA}</b><br />${props.POPULASI} jumlah pernikahan dini` : 'Hover';
        const contents = props ? `<b>${props.KAB_KOTA}</b><br />${iniDataku[props.KAB_KOTA]} jumlah pernikahan dini` : 'Hover';
        this._div.innerHTML = `<h4>SULTENG Data Pernikahan Anak</h4>${contents}`;
    };

    info.addTo(map);


    // warna peta sesuai jumlah angka pernikahan
    function getColor(d) {
        return d > 71 ? '#800026' :
            d > 61 ? '#BD0026' :
            d > 51 ? '#E31A1C' :
            d > 41 ? '#FC4E2A' :
            d > 31 ? '#FD8D3C' :
            d > 21 ? '#FEB24C' :
            d > 11 ? '#FFD000' :
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

    var jsonTest = new L.GeoJSON.AJAX(["assets/js/tes.geojson"], {
        style: style,
        onEachFeature: onEachFeature
    }).addTo(map);
</script>

</html>