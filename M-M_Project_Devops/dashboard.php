<?php
session_start(); // Start the session to access user data

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit;
}

// Database connection (replace with your actual database credentials)
$db_server = "localhost";
$db_username = "admin";
$db_password = "admin";
$db_name = "Web App";

$conn = new mysqli($db_server, $db_username, $db_password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the "orders" table exists; if not, you can create it with appropriate columns
// For simplicity, let's assume the table has columns: id, user_id, product_name, and quantity

// Function to add a product to the order
function addToOrder($product_name, $quantity, $username, $conn)
{
    $sql = "INSERT INTO orders (username, product_name, quantity) VALUES ('$username', '$product_name', '$quantity')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to fetch and display user's orders
/*function displayOrders($username, $conn) {
    $sql = "SELECT product_name, quantity FROM orders WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['quantity']} x {$row['product_name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No orders yet.</p>";
    }
}
*/
// Handle the form submission to add a product to the order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['product_name']) && isset($_POST['quantity'])) {
        $product_name = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $username = $_SESSION['username'];

        if (addToOrder($product_name, $quantity, $username, $conn)) {
            echo "<p style='color:lightgreen'>Product added to your order.</p>";
        } else {
            echo "<pstyle='color:red'>Error adding product to your order.</pstyle=>";
        }
    }
}

// Function to remove a product from the order by name
function removeFromOrderByName($product_name, $username, $conn)
{
    $sql = "DELETE FROM orders WHERE username='$username' AND product_name='$product_name'";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Handle the remove button click
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove'])) {
    $product_name = $_POST['product_name'];
    $username = $_SESSION['username'];

    if (removeFromOrderByName($product_name, $username, $conn)) {
        echo "<p style='color: lightcoral;'>Product removed from your order.</p>";
    } else {
        echo "<p style='color: red;'>Error removing product from your order.</p>";
    }
}

// Function to update the quantity of a product in the order
function updateOrderQuantity($product_name, $new_quantity, $username, $conn)
{
    $sql = "UPDATE orders SET quantity='$new_quantity' WHERE username='$username' AND product_name='$product_name'";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Handle the edit button click
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $product_name = $_POST['product_name'];
    $new_quantity = $_POST['new_quantity'];
    $username = $_SESSION['username'];

    if (updateOrderQuantity($product_name, $new_quantity, $username, $conn)) {
        echo "<p style='color: lightblue;'>Quantity updated.</p>";
    } else {
        echo "<p style='color: red;'>Error updating quantity.</p>";
    }
}
?>

<script>
    function toggleEditForm(itemIndex) {
        var editForm = document.getElementById('edit-form-' + itemIndex);
        var editButton = document.getElementById('edit-button-' + itemIndex);

        if (editForm.style.display === 'none' || editForm.style.display === '') {
            editForm.style.display = 'block';
            editButton.innerHTML = 'Cancel';
        } else {
            editForm.style.display = 'none';
            editButton.innerHTML = 'Edit';
        }
    }
</script>


<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        a:hover {
            cursor: pointer;
            color: purple;
            font-size: 30px;
            transition: all .2s ease-in;

        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav li {
            display: inline-block;
            margin: 0 10px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
        }


        h1 {
            text-align: center;
        }

        main {
            padding: 20px;
            text-align: left;
        }

        section {
            margin-bottom: 20px;

        }

        input[id="addToCart"] {
            background-color: green;
            width: 10%;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        input[id="addToCart"]:hover {
            background-color: #45a049;
        }

        input[value="Remove"] {
            background-color: darkred;
            width: 5%;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        input[value="Remove"]:hover {
            background-color: black;
        }

        button {
            background-color: blue;
            width: 5%;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: black;
        }

        a[id="logout"]:hover {
            cursor: pointer;
            color: red;
            font-size: 20px;
            transition: all .2s ease-in;
        }

        button[id="contactUsButton"] {
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;

        }


        footer {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }


        /* Add your CSS styles for other elements here */
    </style>
</head>

<body>
    <header>
        <img src="H&H.png" alt="M&M logo" width="90px">
        <h1>Welcome to M&M Store</h1>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#Our Products">Products</a></li>
                <li><a href="#Cart">Add to Cart</a></li>
                <li><a href="#Your Orders">Your Orders</a></li>
                <li><a href="#Our Services">Services</a></li>
                <li><a href="#About Us">About Us</a></li>
            </ul>
        </nav>
    </header>
    <h4 style="color:lightgreen">Welcome,
        <?php echo $_SESSION['username']; ?>!
    </h4>
    <main>
        <!-- Display products -->
        <section id="Our Products">
            <u>
                <h2>Our Products</h2>
            </u>
            <ul>
                <li><b><i>LAPTOP</i></b></li><br>
                <img src="https://www.paklap.pk/pub/media/catalog/product/cache/7e76858baa02afd4bb6d466a87d0383e/h/p/hp-elitex2-1012-g2-7300u-7thgen-ci5-12.3inch-touch-tablet-pc-laptop-price-in-pakistan.jpg"
                    alt="Hp Elite x2" width=180px>
                <i>
                    <p>Hp Elite x2 - $800</p>
                </i><br>

                <br>

                <li><b><i>CAMERA</i></b></li><br>
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRdl48iYj39niLl4j-ynzDbgepCKptKTOn_8A&usqp=CAU"
                    alt="Sony A7 IV" width=180px>
                <i>
                    <p>Sony A7 IV - $600</p>
                </i><br>

                <br>

                <li><b><i>WATCHES</i></b></li><br>
                <i>
                    <p>Available Soon...</p>
                </i><br>

                <!-- Add more products as needed -->
            </ul>

            <!-- Add to Cart Form -->
            <section id="Cart">
                <u>
                    <h2>Add to Cart</h2>
                </u>
                <form action="dashboard.php" method="post">
                    <label for="product_name">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" required><br><br>

                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" required min="1"><br><br>

                    <input id="addToCart" type="submit" value="Add to Cart">
                </form>
                <br><br>

                <!-- Display user's orders with remove and edit buttons -->
                <section id="Your Orders">
                    <u>
                        <h2>Your Orders</h2>
                    </u>
                    <?php
                    $username = $_SESSION['username'];
                    $sql = "SELECT product_name, quantity FROM orders WHERE username='$username'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $itemIndex = 0;
                        echo "<ul>";
                        while ($row = $result->fetch_assoc()) {
                            $itemIndex++;
                            echo "<li>{$row['quantity']} x {$row['product_name']} 
                      <form method='post' style='display: inline;'>
                          <input type='hidden' name='product_name' value='{$row['product_name']}'>
                          <input type='submit' name='remove' value='Remove'>
                      </form>
                      <button id='edit-button-$itemIndex' onclick='toggleEditForm($itemIndex)'>Edit</button>
                      <form method='post' id='edit-form-$itemIndex' style='display: none;'>
                          <input type='hidden' name='product_name' value='{$row['product_name']}'>
                          <label for='new_quantity'>New Quantity:</label>
                          <input type='number' id='new_quantity' name='new_quantity' required min='1'>
                          <input type='submit' name='edit' value='Edit'>
                      </form>
                      </li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No orders yet.</p>";
                    }

                    ?>
                    <br><br>

                    <section id="Our Services">
                        <u>
                            <h2>Our Services</h2>
                        </u>
                        <ul>
                            <li>Door-to-Door Repair Services</li>
                            <li>Free Product Maintenance for 1 year</li>
                            <li>Replace old products with new</li>
                        </ul>
                        <p><a href="#">View All Services</a></p>

                    </section>

                    <br>
                    <br>
                    <section id="About Us">
                        <u>
                            <h2>About Us</h2>
                        </u>
                        <i>
                            <p>We are a leading provider of high-quality products and services.
                                Pakistan's Leading Store & Repair Service Provider M&M store Pakistan is the only
                                reliable, trustworthy store in Pakistan that provides
                                authentic products with guarantee. We have a huge range of elctronics products be it the
                                latest iPhone, iPad, or Macbook. Because of our finest team of professionals and the
                                fastest and reliable services we have created a huge trust
                                among our thousands of customers nationwide. Our store is a solely reliable Store in
                                Pakistan.
                            </p>
                        </i>

                        <br><br>

                        <section id="Contact Us">
                            <u>
                                <h2>Contact Us</h2>
                            </u>
                            <form action="">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email">
                                <label for="message">Message</label>
                                <textarea id="message"></textarea>
                                <button id="contactUsButton">Submit</button>
                            </form>
                        </section>
</body>

</main>
<a id="logout" href="logout.php">Logout</a> <!-- Add a logout link to log out the user -->
<br>

<footer>
    <p>Copyright © 2023 M&M Store</p>
    <a href="https://www.instagram.com/mohid_anwar/" target="_main">Follow on IG </a><br>
    <a href="https://www.facebook.com/MohidAnwar.06" target="_main">Follow on Facebook</a><br>
</footer>
</body>

</html>