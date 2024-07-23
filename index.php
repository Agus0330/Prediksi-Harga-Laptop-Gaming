<!DOCTYPE html>
<html>
<head>
    <title>Prediksi Harga Laptop Gaming</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .result {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Prediksi Harga Laptop Gaming</h1>
        <form method="post" action="">
            <label for="vga">VGA:</label>
            <input type="text" id="vga" name="vga" required><br>

            <label for="processor">Processor:</label>
            <input type="text" id="processor" name="processor" required><br>

            <label for="penyimpanan">Penyimpanan:</label>
            <input type="text" id="penyimpanan" name="penyimpanan" required><br>

            <label for="ukuran_layar">Ukuran Layar (Inch):</label>
            <input type="text" id="ukuran_layar" name="ukuran_layar" required><br>
            
            <label for="ram">RAM (GB):</label>
            <input type="text" id="ram" name="ram" required><br>
            
            <input type="submit" name="submit" value="Prediksi">
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $vga = $_POST['vga'];
            $processor = $_POST['processor'];
            $penyimpanan = $_POST['penyimpanan'];
            $ukuran_layar = $_POST['ukuran_layar'];
            $ram = $_POST['ram'];
            
            // Data yang akan dikirim ke API Flask
            $data = array(
                'VGA' => $vga,
                'Processor' => $processor,
                'Penyimpanan' => $penyimpanan,
                'Ukuran Layar' => floatval($ukuran_layar),
                'RAM' => floatval($ram)
            );
            
            $url = 'http://127.0.0.1:5000/predict'; // URL API Flask
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ),
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            
            if ($result === FALSE) {
                echo "<div class='result'>Terjadi kesalahan dalam melakukan prediksi.</div>";
            } else {
                $response = json_decode($result, true);
                echo "<div class='result'><h2>Hasil Prediksi:</h2>";
                echo "Harga: Rp " . number_format($response['harga'], 0, ',', '.') . "</div>";
            }
        }
        ?>
    </div>
</body>
</html>
