<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm" action="process_product.php" method="POST" enctype="multipart/form-data">
                    <!-- Image Upload with Preview -->
                    <div class="mb-4">
                        <label class="form-label">Product Images</label>
                        <div class="image-upload-container">
                            <div class="preview-container mb-2 d-flex gap-2"></div>
                            <input type="file" class="form-control" name="product_images[]" multiple accept="image/*" 
                                   onchange="previewImages(this)">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category_id" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Rest of the form fields... -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="addProductForm" class="btn btn-primary">Add Product</button>
            </div>
        </div>
    </div>
</div>

<script>
function previewImages(input) {
    const previewContainer = input.previousElementSibling;
    previewContainer.innerHTML = '';
    
    if (input.files) {
        [...input.files].forEach(file => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-image position-relative';
                div.innerHTML = `
                    <img src="${e.target.result}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                    <button type="button" class="btn-close position-absolute top-0 end-0" 
                            style="background-color: white; margin: 5px;"
                            onclick="this.parentElement.remove()"></button>
                `;
                previewContainer.appendChild(div);
            }
            
            reader.readAsDataURL(file);
        });
    }
}
</script> 