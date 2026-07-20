<?php include 'includes/header.php'; ?>

<div class="hero">
  <h1>Welcome to LookFab Boutique</h1>
  <p>Discover timeless fashion curated for Men, Women, and Kids. Experience elevated style, premium quality, and a touch of luxury in every piece.</p>
  <a href="shop.php" class="btn" style="background:var(--secondary);color:var(--primary);padding:14px 32px;border-radius:var(--radius);font-weight:bold;text-decoration:none;">Shop Now</a>
</div>

<!-- Category Cards -->
<div class="categories">
  <div class="category-card category-men" onclick="location.href='shop.php?category=men'">
    <div class="category-img">
      <img src="assets/images/banners/men.jpg" alt="Men's Fashion">
    </div>
                    <h3>Men</h3>
    <p>Elegant & Modern Styles</p>
                </div>
  <div class="category-card category-women" onclick="location.href='shop.php?category=women'">
    <div class="category-img">
      <img src="assets/images/banners/womens.jpg" alt="Women's Fashion">
            </div>
                    <h3>Women</h3>
    <p>Chic & Timeless Fashion</p>
                </div>
  <div class="category-card category-kids" onclick="location.href='shop.php?category=kids'">
    <div class="category-img">
      <img src="assets/images/banners/kids.jpg" alt="Kids' Fashion">
            </div>
    <h3>Kids</h3>
    <p>Vibrant & Playful Styles</p>
        </div>
    </div>

        <h2 class="section-title">Featured Products</h2>
<div style="display: flex; align-items: center; justify-content: flex-start; margin: 0 0 24px 32px; gap: 16px;">
  <label for="sort-featured" style="font-weight: 600; color: var(--primary);">Sort by Price:</label>
  <select id="sort-featured" class="filter-select" style="min-width: 180px;" onchange="window.location.href=this.value">
    <option value="?sort=price_asc" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'price_asc' ? 'selected' : ''); ?>>Low to High</option>
    <option value="?sort=price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc' ? 'selected' : ''); ?>>High to Low</option>
  </select>
</div>
<div class="products-grid">
            <?php
            include 'config/database.php';
    $orderBy = 'ORDER BY RAND()';
    if (isset($_GET['sort'])) {
      switch ($_GET['sort']) {
        case 'price_asc': $orderBy = 'ORDER BY price ASC'; break;
        case 'price_desc': $orderBy = 'ORDER BY price DESC'; break;
      }
    }
    $shown_ids = [];
    $query = "SELECT * FROM products $orderBy LIMIT 9";
            $result = mysqli_query($conn, $query);
    while ($product = mysqli_fetch_assoc($result)):
        $shown_ids[] = $product['id'];
  ?>
                <div class="product-card">
      <div class="product-img">
        <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
      <h4><?php echo htmlspecialchars($product['name']); ?></h4>
      <div class="price">₹<?php echo htmlspecialchars($product['price']); ?></div>
      <button class="btn add-to-cart" onclick="addToCart(<?php echo htmlspecialchars($product['id']); ?>)">
        <i class="fas fa-shopping-cart"></i> Add to Cart
      </button>
                </div>
            <?php endwhile; ?>
        </div>

<!-- Best Sellers Section -->
<h2 class="section-title" style="margin-top: 48px;">Best Sellers</h2>
<div style="display: flex; align-items: center; justify-content: flex-start; margin: 0 0 24px 32px; gap: 16px;">
  <label for="sort-bestsellers" style="font-weight: 600; color: var(--primary);">Sort by Price:</label>
  <select id="sort-bestsellers" class="filter-select" style="min-width: 180px;" onchange="window.location.href=this.value">
    <option value="?sortb=price_asc" <?php echo (!isset($_GET['sortb']) || $_GET['sortb'] == 'price_asc' ? 'selected' : ''); ?>>Low to High</option>
    <option value="?sortb=price_desc" <?php echo (isset($_GET['sortb']) && $_GET['sortb'] == 'price_desc' ? 'selected' : ''); ?>>High to Low</option>
  </select>
</div>
<div class="products-grid">
  <?php
    $orderByB = 'ORDER BY RAND()';
    if (isset($_GET['sortb'])) {
      switch ($_GET['sortb']) {
        case 'price_asc': $orderByB = 'ORDER BY price ASC'; break;
        case 'price_desc': $orderByB = 'ORDER BY price DESC'; break;
      }
    }
    $exclude = !empty($shown_ids) ? ('WHERE id NOT IN (' . implode(',', array_map('intval', $shown_ids)) . ')') : '';
    $queryB = "SELECT * FROM products $exclude $orderByB LIMIT 6";
    $resultB = mysqli_query($conn, $queryB);
    while ($product = mysqli_fetch_assoc($resultB)):
  ?>
    <div class="product-card">
      <div class="product-img">
        <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
      <h4><?php echo htmlspecialchars($product['name']); ?></h4>
      <div class="price">₹<?php echo htmlspecialchars($product['price']); ?></div>
      <button class="btn add-to-cart" onclick="addToCart(<?php echo htmlspecialchars($product['id']); ?>)">
        <i class="fas fa-shopping-cart"></i> Add to Cart
      </button>
    </div>
  <?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>
<script src="assets/js/cart.js"></script>