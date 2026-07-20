<?php include 'includes/header.php'; ?>

<section class="products">
    <div class="container">
        <h2 class="section-title">Our Products</h2>
        <div class="product-filters" style="margin-bottom: 32px;">
            <div class="filter-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <select id="category-select" class="filter-select" style="min-width: 180px;" onchange="window.location.href=this.value">
                    <option value="?category=all" <?php echo (!isset($_GET['category']) || $_GET['category'] == 'all' ? 'selected' : ''); ?>>All Categories</option>
                    <option value="?<?php echo buildQueryString(['category' => 'men']); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == 'men' ? 'selected' : ''); ?>>Men</option>
                    <option value="?<?php echo buildQueryString(['category' => 'women']); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == 'women' ? 'selected' : ''); ?>>Women</option>
                    <option value="?<?php echo buildQueryString(['category' => 'kids']); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == 'kids' ? 'selected' : ''); ?>>Kids</option>
                    <option value="?<?php echo buildQueryString(['category' => 'formal']); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == 'formal' ? 'selected' : ''); ?>>Formal</option>
                    <option value="?<?php echo buildQueryString(['category' => 'casual']); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == 'casual' ? 'selected' : ''); ?>>Casual</option>
                </select>
                <select id="sort-select" class="filter-select" style="min-width: 180px;" onchange="window.location.href=this.value">
                    <option value="?<?php echo buildQueryString(['sort' => 'price_asc']); ?>" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'price_asc' ? 'selected' : ''); ?>>Price: Low to High</option>
                    <option value="?<?php echo buildQueryString(['sort' => 'price_desc']); ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc' ? 'selected' : ''); ?>>Price: High to Low</option>
                    <option value="?<?php echo buildQueryString(['sort' => 'name_asc']); ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc' ? 'selected' : ''); ?>>Name: A-Z</option>
                    <option value="?<?php echo buildQueryString(['sort' => 'name_desc']); ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc' ? 'selected' : ''); ?>>Name: Z-A</option>
                </select>
            </div>
            <?php if (isset($_GET['search'])): ?>
                <p>Search results for: <strong><?php echo htmlspecialchars($_GET['search']); ?></strong></p>
            <?php elseif (isset($_GET['category'])): ?>
                <p>Showing category: <strong><?php echo ucfirst($_GET['category']); ?></strong></p>
            <?php endif; ?>
        </div>
        <div class="products-grid">
            <?php
            include 'config/database.php';
            $where = [];
            $params = [];
            $orderBy = "ORDER BY ";
            // Pagination setup
            $limit = 9;
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $offset = ($page - 1) * $limit;
            // Category filter
            if (isset($_GET['category']) && $_GET['category'] != 'all') {
                $where[] = "category = ?";
                $params[] = $_GET['category'];
            }
            // Search filter
            if (isset($_GET['search'])) {
                $where[] = "(name LIKE ? OR description LIKE ?)";
                $searchTerm = "%" . $_GET['search'] . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            // Sorting
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'price_asc':
                        $orderBy .= "price ASC";
                        break;
                    case 'price_desc':
                        $orderBy .= "price DESC";
                        break;
                    case 'name_asc':
                        $orderBy .= "name ASC";
                        break;
                    case 'name_desc':
                        $orderBy .= "name DESC";
                        break;
                    default:
                        $orderBy .= "price ASC";
                }
            } else {
                $orderBy .= "price ASC";
            }
            // Count total products for pagination
            $countQuery = "SELECT COUNT(*) as total FROM products";
            if (!empty($where)) {
                $countQuery .= " WHERE " . implode(" AND ", $where);
            }
            $countStmt = mysqli_prepare($conn, $countQuery);
            if (!empty($params)) {
                mysqli_stmt_bind_param($countStmt, str_repeat('s', count($params)), ...$params);
            }
            mysqli_stmt_execute($countStmt);
            $countResult = mysqli_stmt_get_result($countStmt);
            $totalProducts = mysqli_fetch_assoc($countResult)['total'];
            $totalPages = ceil($totalProducts / $limit);
            // Fetch products for current page
            $query = "SELECT * FROM products";
            if (!empty($where)) {
                $query .= " WHERE " . implode(" AND ", $where);
            }
            $query .= " " . $orderBy . " LIMIT $limit OFFSET $offset";
            $stmt = mysqli_prepare($conn, $query);
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                while ($product = mysqli_fetch_assoc($result)):
                    $img_path = 'assets/images/products/' . htmlspecialchars($product['image']);
            ?>
                    <div class="product-card">
                        <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.onerror=null;this.src='assets/images/products/model2.jpg';">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <div class="price">₹<?php echo htmlspecialchars($product['price']); ?></div>
                        <button onclick="addToCart(<?php echo htmlspecialchars($product['id']); ?>)">
                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart
                        </button>
                    </div>
            <?php endwhile;
            } else {
                echo '<p class="no-products">No products found. Try a different search or category.</p>';
            }
            ?>
        </div>
        <?php if ($totalPages > 1): ?>
        <div style="display: flex; justify-content: center; margin: 32px 0;">
            <nav class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo buildQueryString(['page' => $page - 1]); ?>" class="page-btn">Previous</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= min(3, $totalPages); $i++): ?>
                    <a href="?<?php echo buildQueryString(['page' => $i]); ?>" class="page-btn<?php if ($i == $page) echo ' active'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo buildQueryString(['page' => $page + 1]); ?>" class="page-btn">Next</a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php 
// Helper function to build query string while preserving existing parameters
function buildQueryString($newParams = []) {
    $params = $_GET;
    foreach ($newParams as $key => $value) {
        $params[$key] = $value;
    }
    return http_build_query($params);
}

include 'includes/footer.php'; 
?>