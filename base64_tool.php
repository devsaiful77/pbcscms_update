<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base64 Encoder/Decoder</title>
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

        select, textarea, input[type="submit"] {
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
        <h2>Base64 Encoder/Decoder</h2>
        
        <form method="post">
            <label for="action">Choose Action:</label>
            <select name="action" id="action">
                <option value="encode">Encode</option>
                <option value="decode">Decode</option>
            </select>

            <label for="inputString">Enter String:</label>
            <textarea name="inputString" id="inputString"></textarea>

            <input type="submit" value="Submit">
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $action = $_POST['action'];
            $inputString = $_POST['inputString'];

            if ($action == 'encode') {
                $outputString = base64_encode($inputString);
                echo "<div class='result-container'><h3>Encoded Result:</h3>";
            } else {
                $outputString = base64_decode($inputString);
                echo "<div class='result-container'><h3>Decoded Result:</h3>";
            }

            echo "<textarea readonly>" . htmlspecialchars($outputString) . "</textarea></div>";
        }
        ?>
    </div>
</body>
</html>
