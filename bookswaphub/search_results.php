<?php
// Database connection
include 'db.php';

$query = $conn->real_escape_string($_GET['query']); // Sanitize user input
$sql = "SELECT * FROM books WHERE title LIKE '%$query%' OR author LIKE '%$query%' OR book_description LIKE '%$query%'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error: " . $conn->error); // Output any SQL errors
}

// Fetch some recommended books for the slider
$recommended_sql = "SELECT * FROM books ORDER BY RAND() LIMIT 10";
$recommended_result = $conn->query($recommended_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/favicon.png" sizes="16x16" type="image/png">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2rem;
            color: #333;
        }

        .search-results {
            margin-top: 20px;
            overflow-x: auto; /* For horizontal scrolling on smaller devices */
        }

        .search-results table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: fixed; /* Ensures table columns are evenly spaced */
        }

        .search-results table th, 
        .search-results table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            word-wrap: break-word; /* Ensures text wraps within the cell */
        }

        .search-results table th {
            background-color: #f8f8f8;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .search-results table td {
            font-size: 1.5rem;
        }

        .search-results table img {
            max-width: 120px;
            border-radius: 5px;
            cursor: pointer; /* Indicates that the image is clickable */
        }

        .price {
            color: #e67e22;
            font-weight: bold;
        }

        .no-results {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
        }

        .recommended-section {
            margin-top: 50px;
        }

        .recommended-section h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 20px;
        }

        .recommended-section .recommended-items {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .recommended-section .recommended-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: calc(25% - 20px);
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .recommended-section .recommended-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .recommended-section .recommended-item img {
            max-width: 100px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .recommended-section .recommended-item h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        .recommended-section .recommended-item .price {
            color: #e67e22;
            font-weight: bold;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .recommended-section .recommended-item {
                width: calc(50% - 20px);
            }

            .search-results table img {
                max-width: 80px;
            }
        }

        @media (max-width: 480px) {
            .recommended-section .recommended-item {
                width: 100%;
            }

            .search-results table th,
            .search-results table td {
                padding: 6px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .search-results table img {
                max-width: 45px; /* Allow images to scale better on small screens */
            }
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>
    <div class="container">
        <header class="header">
            <h1>Search Results for "<?php echo htmlspecialchars($query); ?>"</h1>
        </header>
        <main class="search-results">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <a href="book_details.php?id=<?php echo htmlspecialchars($row['book_ID']); ?>">
                                        <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Book Image">
                                    </a>
                                </td>
                                <td><a href="book_details.php?id=<?php echo htmlspecialchars($row['book_ID']); ?>"><?php echo htmlspecialchars($row['title']); ?></a></td>
                                <td><?php echo htmlspecialchars($row['author']); ?></td>
                                <td><?php echo htmlspecialchars($row['book_description']); ?></td>
                                <td class="price"><?php echo htmlspecialchars($row['price']); ?> INR</td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo htmlspecialchars($row['book_condition']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-results">No results found.</p>
            <?php endif; ?>
        </main>
        <section class="recommended-section">
            <h2>Recommended Books</h2>
            <div class="recommended-items">
                <?php if ($recommended_result->num_rows > 0): ?>
                    <?php while ($rec_row = $recommended_result->fetch_assoc()): ?>
                        <div class="recommended-item">
                            <a href="book_details.php?id=<?php echo htmlspecialchars($rec_row['book_ID']); ?>">
                                <img src="<?php echo htmlspecialchars($rec_row['photo']); ?>" alt="Book Image">
                                <h3><?php echo htmlspecialchars($rec_row['title']); ?></h3>
                                <p class="price"><?php echo htmlspecialchars($rec_row['price']); ?> INR</p>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>
