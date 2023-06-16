<?php
require_once "admin_check.php";
require_once "../config.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="<?php echo LOGO ?>">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background-color: #404040;
            color: white;
        }

        .wrapper {
            width: 600px;
            margin: auto;
            padding: 20px;
        }

        .table {
            margin-top: 20px;
        }

        .table th {
            background-color: #333;
            color: white;
            text-align: center;
        }

        .table td {
            background-color: #555;
            text-align: center;
        }

        .btn-primary {
            background-color: #fdcb6e;
            color: black;
            border: none;
            border-radius: 0;
            padding: 10px 30px;
            transition: background-color 250ms;
        }

        .btn-primary:hover {
            background-color: #9c7631;
        }

        .form-control {
            background-color: #333;
            border: none;
            border-radius: 0;
            color: white;
        }

        .form-control:focus {
            box-shadow: none;
        }

        #banForm {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h2>Admin Panel - License Keys</h2>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="mb-4">
            <div class="form-group">
                <label for="license_key">New License Key:</label>
                <input type="text" name="license_key" id="license_key" class="form-control">
            </div>
            <input type="submit" value="Create" class="btn btn-primary">
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>License Key</th>
                    <th>Used</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once "../config.php";

                // Insert new license key into the database
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $new_license_key = trim($_POST["license_key"]);
                    if (!empty($new_license_key)) {
                        $sql = "INSERT INTO licensekeys (`key`, `used`) VALUES (?, FALSE)";
                        $stmt = mysqli_prepare($link, $sql);
                        mysqli_stmt_bind_param($stmt, "s", $new_license_key);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                    }
                }

                // Delete a license key from the database
                if (isset($_GET['delete'])) {
                    $delete_license_key = $_GET['delete'];
                    $sql = "DELETE FROM licensekeys WHERE `key` = ?";
                    $stmt = mysqli_prepare($link, $sql);
                    mysqli_stmt_bind_param($stmt, "s", $delete_license_key);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                // Retrieve all license keys from the database
                $sql = "SELECT `key`, `used` FROM licensekeys";
                $result = mysqli_query($link, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['key'] . "</td>";
                        echo "<td>" . ($row['used'] ? "Yes" : "No") . "</td>";
                        echo "<td><a href=\"?delete=" . $row['key'] . "\" class=\"btn btn-danger btn-sm\">Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Error retrieving data.</td></tr>";
                }

                ?>
            </tbody>
        </table>
        <div class="wrapper">
            <h2>Admin Panel - User List</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once "../config.php";

                    // Retrieve all users from the database
                    $sql2 = "SELECT id, username, is_banned FROM users";
                    $result2 = mysqli_query($link, $sql2);

                    if ($result2) {
                        while ($row = mysqli_fetch_assoc($result2)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['username'] . "</td>";
                            echo "<td>";
                            if ($row['id'] != $_SESSION['id']) {
                                if ($row['is_banned'] == 1) {
                                    echo "<a href=\"?unban=" . $row['id'] . "\" class=\"btn btn-warning btn-sm\">Unban</a>";
                                } else {
                                    echo "<a href=\"?ban=" . $row['id'] . "\" class=\"btn btn-danger btn-sm\">Ban</a>";
                                }
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Error retrieving data.</td></tr>";
                    }

                    // Ban a user
                    if (isset($_GET['ban'])) {
                        $user_id = $_GET['ban'];
                        $sql3 = "UPDATE users SET is_banned = 1 WHERE id = ?";
                        $stmt3 = mysqli_prepare($link, $sql3);
                        mysqli_stmt_bind_param($stmt3, "i", $user_id);
                        mysqli_stmt_execute($stmt3);
                        mysqli_stmt_close($stmt3);
                    }

                    // Unban a user
                    if (isset($_GET['unban'])) {
                        $user_id = $_GET['unban'];
                        $sql4 = "UPDATE users SET is_banned = 0 WHERE id = ?";
                        $stmt4 = mysqli_prepare($link, $sql4);
                        mysqli_stmt_bind_param($stmt4, "i", $user_id);
                        mysqli_stmt_execute($stmt4);
                        mysqli_stmt_close($stmt4);
                    }
                    ?>
                </tbody>
            </table>

        </div>
        <button onclick="goToDashboard()" class="btn btn-primary">Go to Dashboard</button>
    </div>


    <script>
        function goToDashboard() {
            window.location.href = "../dash";
        }
    </script>

</body>

</html>