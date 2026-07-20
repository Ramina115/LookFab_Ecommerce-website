<?php include 'includes/header.php'; ?>

<section class="shop">
    <div class="container">
        <h2 class="section-title">Our Shop</h2>
        <p class="shop-description" style="text-align:center;">Browse our collections and find the perfect outfit for any occasion.</p>

        <!-- New Arrivals Section -->
        <h2 class="section-title" style="text-align:center; margin-top: 32px;">New Arrivals</h2>
        <div class="shop-sections luxury-shop-sections">
            <div class="shop-section">
                <div class="shop-section-controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <select class="category-dropdown" style="min-width: 180px;">
                        <option value="">Category</option>
                        <option value="women">Women</option>
                        <option value="men">Men</option>
                        <option value="kids">Kids</option>
                        <option value="accessories">Accessories</option>
                        <option value="home">Home Decor</option>
                    </select>
                    <select id="sort-new" class="filter-select" style="min-width: 180px;" onchange="window.location.href=this.value">
                        <option value="?sort=price_asc">Price: Low to High</option>
                        <option value="?sort=price_desc">Price: High to Low</option>
                    </select>
                </div>
                <div class="products-grid">
                    <?php
                    include 'config/database.php';
                    // Pagination for New Arrivals
                    $limitn = 9;
                    $pagen = isset($_GET['pagen']) ? max(1, intval($_GET['pagen'])) : 1;
                    $offsetn = ($pagen - 1) * $limitn;
                    $orderBy = "ORDER BY created_at DESC";
                    if (isset($_GET['sort'])) {
                        switch ($_GET['sort']) {
                            case 'price_asc': $orderBy = "ORDER BY price ASC"; break;
                            case 'price_desc': $orderBy = "ORDER BY price DESC"; break;
                        }
                    }
                    $countQueryN = "SELECT COUNT(*) as total FROM products";
                    $countResultN = mysqli_query($conn, $countQueryN);
                    $totalN = mysqli_fetch_assoc($countResultN)['total'];
                    $totalPagesN = ceil($totalN / $limitn);
                    $query = "SELECT * FROM products $orderBy LIMIT $limitn OFFSET $offsetn";
                    $result = mysqli_query($conn, $query);
                    $shown_ids = [];
                    while ($product = mysqli_fetch_assoc($result)):
                        $shown_ids[] = $product['id'];
                    ?>
                        <div class="product-card">
                            <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <div class="price">₹<?php echo htmlspecialchars($product['price']); ?></div>
                            <button onclick="addToCart(<?php echo htmlspecialchars($product['id']); ?>)">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php if ($totalPagesN > 1): ?>
                <div style="display: flex; justify-content: center; margin: 24px 0;">
                    <nav class="pagination">
                        <?php if ($pagen > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['pagen' => $pagen - 1])); ?>" class="page-btn">Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= min(3, $totalPagesN); $i++): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['pagen' => $i])); ?>" class="page-btn<?php if ($i == $pagen) echo ' active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <?php if ($pagen < $totalPagesN): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['pagen' => $pagen + 1])); ?>" class="page-btn">Next</a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Best Sellers Section -->
        <h2 class="section-title" style="text-align:center; margin-top: 32px;">Best Sellers</h2>
        <div class="shop-sections luxury-shop-sections">
            <div class="shop-section">
                <div class="shop-section-controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <select class="category-dropdown" style="min-width: 180px;">
                        <option value="">Category</option>
                        <option value="women">Women</option>
                        <option value="men">Men</option>
                        <option value="kids">Kids</option>
                        <option value="accessories">Accessories</option>
                        <option value="home">Home Decor</option>
                    </select>
                    <select id="sort-best" class="filter-select" style="min-width: 180px;" onchange="window.location.href=this.value">
                        <option value="?sort=price_asc">Price: Low to High</option>
                        <option value="?sort=price_desc">Price: High to Low</option>
                    </select>
                </div>
                <div class="products-grid">
                    <?php
                    // Pagination for Best Sellers
                    $limitb = 6;
                    $pageb = isset($_GET['pageb']) ? max(1, intval($_GET['pageb'])) : 1;
                    $offsetb = ($pageb - 1) * $limitb;
                    $orderBy = "ORDER BY RAND()";
                    if (isset($_GET['sort'])) {
                        switch ($_GET['sort']) {
                            case 'price_asc': $orderBy = "ORDER BY price ASC"; break;
                            case 'price_desc': $orderBy = "ORDER BY price DESC"; break;
                        }
                    }
                    $exclude = !empty($shown_ids) ? ('WHERE id NOT IN (' . implode(',', array_map('intval', $shown_ids)) . ')') : '';
                    $countQueryB = "SELECT COUNT(*) as total FROM products $exclude";
                    $countResultB = mysqli_query($conn, $countQueryB);
                    $totalB = mysqli_fetch_assoc($countResultB)['total'];
                    $totalPagesB = ceil($totalB / $limitb);
                    $query = "SELECT * FROM products $exclude $orderBy LIMIT $limitb OFFSET $offsetb";
                    $result = mysqli_query($conn, $query);
                    while ($product = mysqli_fetch_assoc($result)):
                    ?>
                        <div class="product-card">
                            <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                            <div class="price">₹<?php echo htmlspecialchars($product['price']); ?></div>
                            <button onclick="addToCart(<?php echo htmlspecialchars($product['id']); ?>)">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php if ($totalPagesB > 1): ?>
                <div style="display: flex; justify-content: center; margin: 24px 0;">
                    <nav class="pagination">
                        <?php if ($pageb > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['pageb' => $pageb - 1])); ?>" class="page-btn">Previous</a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= min(3, $totalPagesB); $i++): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['pageb' => $i])); ?>" class="page-btn<?php if ($i == $pageb) echo ' active'; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>
                        <?php if ($pageb < $totalPagesB): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['pageb' => $pageb + 1])); ?>" class="page-btn">Next</a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/cart.js"></script>