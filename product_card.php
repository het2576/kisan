<div class="product-card">
    <div class="product-image-container">
        <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'assets/default-product.jpg'); ?>" 
             class="product-image" 
             alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php if($product['is_organic']): ?>
            <div class="organic-badge">
                <i class="fas fa-leaf me-1"></i>
                <?php echo $translations[$lang]['organic'] ?? 'Organic'; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="product-details p-3">
        <h5 class="card-title mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
        <p class="text-muted mb-2">
            <?php echo $translations[$lang]['category'] ?? 'Category'; ?>: 
            <?php echo htmlspecialchars($product['category_name']); ?>
        </p>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="price-tag">₹<?php echo number_format($product['price_per_kg'], 2); ?>/<?php echo htmlspecialchars($product['unit'] ?? 'kg'); ?></div>
            <span class="status-badge <?php echo $product['status'] === 'available' ? 'available' : 'sold-out'; ?>">
                <?php echo $translations[$lang][$product['status']] ?? ucfirst($product['status']); ?>
            </span>
        </div>
        <div class="product-meta mb-3">
            <div class="mb-1">
                <i class="fas fa-box me-2"></i>
                <?php echo $translations[$lang]['quantity'] ?? 'Quantity'; ?>: 
                <?php echo number_format($product['quantity_available'], 2); ?> 
                <?php echo htmlspecialchars($product['unit'] ?? 'kg'); ?>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary flex-grow-1 edit-button" 
                    data-product-id="<?php echo $product['product_id']; ?>"
                    data-price="<?php echo number_format($product['price_per_kg'], 2); ?>"
                    data-quantity="<?php echo number_format($product['quantity_available'], 2); ?>"
                    data-status="<?php echo htmlspecialchars($product['status']); ?>"
                    data-category="<?php echo htmlspecialchars($product['category_id']); ?>"
                    data-bs-toggle="modal" 
                    data-bs-target="#editProductModal">
                <i class="fas fa-edit me-1"></i> <?php echo $translations[$lang]['edit'] ?? 'Edit'; ?>
            </button>
            <button class="btn btn-danger delete-button" data-product-id="<?php echo $product['product_id']; ?>">
                <i class="fas fa-trash"></i> <?php echo $translations[$lang]['delete'] ?? 'Delete'; ?>
            </button>
        </div>
    </div>
</div> 