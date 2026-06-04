<?php
$mysqli = new mysqli("localhost", "root", "", "agrimapgis");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$data = [
    ['Poktan Mekar Jaya', 'Maju Sejahtera', 'Padi, Jagung', 'Bapak Suryanto'],
    ['Poktan Maju Bersama', 'Maju Sejahtera', 'Padi, Jagung', 'Bapak Wahyudi'],
    ['Poktan Sukamaju I', 'Maju Sejahtera', 'Padi, Jagung', 'Bapak Hartono'],
    ['Poktan Jaya Bersama', 'Maju Sejahtera', 'Padi, Jagung', 'Bapak Slamet Riyadi'],
    ['Poktan Tani Mandiri', 'Maju Sejahtera', 'Padi, Jagung', 'Bapak Mulyono'],
    ['Poktan Harapan Jaya', 'Harapan Makmur', 'Padi, Jagung', 'Bapak Agus Santoso'],
    ['Poktan Sumber Rejeki', 'Harapan Makmur', 'Padi, Jagung', 'Bapak Supriadi'],
    ['Poktan Sido Makmur', 'Harapan Makmur', 'Padi', 'Ibu Suminah'],
    ['Poktan Karya Tani', 'Harapan Makmur', 'Padi', 'Bapak Bambang Eko'],
    ['Poktan Tunas Harapan', 'Harapan Makmur', 'Padi', 'Bapak Sudirman']
];

foreach ($data as $row) {
    $nama_poktan = $mysqli->real_escape_string($row[0]);
    $gapoktan = $mysqli->real_escape_string($row[1]);
    $komoditas = $mysqli->real_escape_string($row[2]);
    $ketua = $mysqli->real_escape_string($row[3]);

    // Insert into farmer_groups
    $sql_group = "INSERT INTO farmer_groups (nama_kelompok, gapoktan, komoditas, ketua, kecamatan, created_at) 
                  VALUES ('$nama_poktan', '$gapoktan', '$komoditas', '$ketua', 'Rajabasa', NOW())";
    
    if ($mysqli->query($sql_group)) {
        $id_kelompok = $mysqli->insert_id;
        
        // Buat nama user tanpa title "Bapak"/"Ibu"
        $clean_name = str_replace(['Bapak ', 'Ibu '], '', $row[3]);
        $clean_name_esc = $mysqli->real_escape_string($clean_name);
        
        // Buat email (hilangkan spasi, jadikan lowercase)
        $email_prefix = strtolower(str_replace(' ', '', $clean_name));
        $email = $mysqli->real_escape_string($email_prefix . '@agrimapgis.test');
        
        // Password default: petani123
        $password = password_hash('petani123', PASSWORD_DEFAULT);
        
        // Insert user
        $sql_user = "INSERT INTO users (nama, email, password, role, id_kelompok, created_at) 
                     VALUES ('$clean_name_esc', '$email', '$password', 'petani', $id_kelompok, NOW())";
        
        if ($mysqli->query($sql_user)) {
            echo "Berhasil menambah Poktan '$nama_poktan' beserta Ketua '$clean_name' ($email)\n";
        } else {
            echo "Error menambah User '$clean_name': " . $mysqli->error . "\n";
        }
    } else {
        echo "Error menambah Poktan '$nama_poktan': " . $mysqli->error . "\n";
    }
}

$mysqli->close();
