<?php
include 'config.php'; // Database connection

$message = [];

// Handle category and subcategory actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category'])) {
        // Add Category
        $c_name = mysqli_real_escape_string($conn, $_POST['c_name']);
        $query = "INSERT INTO category (c_name) VALUES ('$c_name')";
        if (mysqli_query($conn, $query)) {
            $message[] = "Category added successfully.";
        } else {
            $message[] = "Error adding category: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_category'])) {
        // Edit Category
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $c_name = mysqli_real_escape_string($conn, $_POST['c_name']);
        $query = "UPDATE category SET c_name = '$c_name' WHERE id = '$category_id'";
        if (mysqli_query($conn, $query)) {
            $message[] = "Category updated successfully.";
        } else {
            $message[] = "Error updating category: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_category'])) {
        // Delete Category
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $query = "DELETE FROM category WHERE id = '$category_id'";
        if (mysqli_query($conn, $query)) {
            $message[] = "Category deleted successfully.";
        } else {
            $message[] = "Error deleting category: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['add_subcategory'])) {
        // Add Subcategory
        $subcategory_name = mysqli_real_escape_string($conn, $_POST['subcategory_name']);
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $query = "INSERT INTO subcategory (name, category_id) VALUES ('$subcategory_name', '$category_id')";
        if (mysqli_query($conn, $query)) {
            $message[] = "Subcategory added successfully.";
        } else {
            $message[] = "Error adding subcategory: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['edit_subcategory'])) {
        // Edit Subcategory
        $subcategory_id = mysqli_real_escape_string($conn, $_POST['subcategory_id']);
        $subcategory_name = mysqli_real_escape_string($conn, $_POST['subcategory_name']);
        $query = "UPDATE subcategory SET name = '$subcategory_name' WHERE id = '$subcategory_id'";
        if (mysqli_query($conn, $query)) {
            $message[] = "Subcategory updated successfully.";
        } else {
            $message[] = "Error updating subcategory: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_subcategory'])) {
        // Delete Subcategory
        $subcategory_id = mysqli_real_escape_string($conn, $_POST['subcategory_id']);
        $query = "DELETE FROM subcategory WHERE id = '$subcategory_id'";
        if (mysqli_query($conn, $query)) {
            $message[] = "Subcategory deleted successfully.";
        } else {
            $message[] = "Error deleting subcategory: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Categories Management </title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">
    <style>
        :root {
            --pink: #d33cf2;
            --red: #c0392b;
            --black: black;
            --white: #fff;
            --light-gray: #f5f5f5;
            --border: .2rem solid var(--black);
            --box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1);
        }

        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--light-gray);
        
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        h1, h2 {
            color: var(--black);
            text-transform: uppercase;
            margin-bottom: 20px;
            font-size: 34px;
            font-weight: 600;
            padding: 20px 0px;
        }

        .title {
    text-align: center;
    margin-bottom: 2rem;
    color: var(--black);
    text-transform: uppercase;
    font-size: 4rem;
    padding: 20px 0;
}

        form {
            background-color: var(--white);
            padding: 20px;
            border: var(--border);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            max-width: 600px;
            margin: auto;
            margin-bottom: 40px !important;
            box-shadow: 5px 5px 20px 2px #0000005c;
        }

        form h2 {
            margin-bottom: 10px;
            color: #000;
            font-size: 20px;
            font-weight: 600;

        }

        input[type="text"], select {
            width: 100%;
            padding: 16px 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #000;
        
        }

        button[type="submit"] {
            margin-top: 10px;
            padding: 12px 15px;
            background-color: var(--black);
            color: var(--white);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 400;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: var(--black);
        }

        /* Message styling */
        .message {
            background-color: var(--light-gray);
            color: var(--black);
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message i {
            cursor: pointer;
            font-size: 1.2rem;
            color: var(--red);
        }

        .message i:hover {
            transform: rotate(90deg);
        }
    </style>
</head>
<body>
<?php include 'admin_header.php';?>
<br>
<div class="container">

    <?php
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo "<div class='message'><span>$msg</span><i class='fas fa-times'></i></div>";
        }
    }
    ?>

    <h1 class="title">Manage Category</h1>

    <!-- Form to add category -->
    <form action="" method="post">
        <h2>Add Category</h2>
        <input type="text" name="c_name" placeholder="Category Name" required>
        <button type="submit" name="add_category">Add Category</button>
    </form>

    <!-- Form to edit category -->
    <form action="" method="post">
        <h2>Edit Category</h2>
        <select name="category_id" required>
            <?php
            $categories = mysqli_query($conn, "SELECT * FROM category");
            while ($category = mysqli_fetch_assoc($categories)) {
                echo '<option value="'.$category['id'].'">'.$category['c_name'].'</option>';
            }
            ?>
        </select>
        <input type="text" name="c_name" placeholder="New Category Name" required>
        <button type="submit" name="edit_category">Edit Category</button>
    </form>

    <!-- Form to delete category -->
    <form action="" method="post">
        <h2>Delete Category</h2>
        <select name="category_id" required>
            <?php
            $categories = mysqli_query($conn, "SELECT * FROM category");
            while ($category = mysqli_fetch_assoc($categories)) {
                echo '<option value="'.$category['id'].'">'.$category['c_name'].'</option>';
            }
            ?>
        </select>
        <button type="submit" name="delete_category">Delete Category</button>
    </form>
    <br>

    <h2><center>Manage Subcategories</center></h2>

    <!-- Form to add subcategory -->
    <form action="" method="post">
        <input type="text" name="subcategory_name" placeholder="Subcategory Name" required>
        <select name="category_id" required>
            <?php
            $categories = mysqli_query($conn, "SELECT * FROM category");
            while ($category = mysqli_fetch_assoc($categories)) {
                echo '<option value="'.$category['id'].'">'.$category['c_name'].'</option>';
            }
            ?>
        </select>
        <button type="submit" name="add_subcategory">Add Subcategory</button>
    </form>

    <!-- Form to edit subcategory -->
    <form action="" method="post">
        <h2>Edit Subcategory</h2>
        <select name="subcategory_id" required>
            <?php
            $subcategories = mysqli_query($conn, "SELECT * FROM subcategory");
            while ($subcategory = mysqli_fetch_assoc($subcategories)) {
                echo '<option value="'.$subcategory['id'].'">'.$subcategory['name'].'</option>';
            }
            ?>
        </select>
        <input type="text" name="subcategory_name" placeholder="New Subcategory Name" required>
        <button type="submit" name="edit_subcategory">Edit Subcategory</button>
    </form>

    <!-- Form to delete subcategory -->
    <form action="" method="post">
        <h2>Delete Subcategory</h2>
        <select name="subcategory_id" required>
            <?php
            $subcategories = mysqli_query($conn, "SELECT * FROM subcategory");
            while ($subcategory = mysqli_fetch_assoc($subcategories)) {
                echo '<option value="'.$subcategory['id'].'">'.$subcategory['name'].'</option>';
            }
            ?>
        </select>
        <button type="submit" name="delete_subcategory">Delete Subcategory</button>
    </form>

</div>

</body>
</html>
