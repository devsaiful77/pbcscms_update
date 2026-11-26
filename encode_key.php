<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AES Encryption/Decryption with Base64</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        h2 {
            margin-top: 0;
            color: #333;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            color: #555;
        }

        input, textarea, select, input[type="submit"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            resize: none;
            height: 100px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .result-container {
            margin-top: 20px;
        }

        .result-container h3 {
            margin: 0 0 10px;
            color: #333;
        }

        .result-container textarea {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>AES Encryption/Decryption with Base64</h2>
        
        <form method="post">
            <label for="action">Choose Action:</label>
            <select name="action" id="action">
                <option value="encrypt">Encrypt</option>
                <option value="decrypt">Decrypt</option>
            </select>

            <label for="key">Enter Secret Key:</label>
            <input type="text" name="key" id="key" required>

            <label for="inputString">Enter Text:</label>
            <textarea name="inputString" id="inputString"></textarea>

            <input type="submit" value="Submit">
        </form>

        <?php
        function aes_encrypt($plaintext, $key) {
            $cipher = "AES-256-CBC";
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
            $encrypted = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);
            return base64_encode($encrypted . '::' . $iv);
        }

        function aes_decrypt($ciphertext, $key) {
            $cipher = "AES-256-CBC";
            list($encrypted_data, $iv) = explode('::', base64_decode($ciphertext), 2);
            return openssl_decrypt($encrypted_data, $cipher, $key, 0, $iv);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $action = $_POST['action'];
            $inputString = $_POST['inputString'];
            $key = $_POST['key'];
            $outputString = '';

            if ($action == 'encrypt') {
                $outputString = aes_encrypt($inputString, $key);
                echo "<div class='result-container'><h3>Encrypted Result:</h3>";
            } elseif ($action == 'decrypt') {
                $outputString = aes_decrypt($inputString, $key);
                echo "<div class='result-container'><h3>Decrypted Result:</h3>";
            }

            echo "<textarea readonly>" . htmlspecialchars($outputString) . "</textarea></div>";
        }
        ?>
    </div>
</body>
</html>
