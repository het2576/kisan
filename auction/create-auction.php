<?php
require_once '../includes/init.php';
require_once '../models/Auction.php';
require_once '../includes/validators/AuctionValidator.php';
require_once '../includes/handlers/ImageHandler.php';
require_once '../includes/services/NotificationService.php';

// Advanced security check
if (!isAuthenticated() || (!isFarmer() && !isAdmin())) {
    $_SESSION['error'] = "Unauthorized access. Only farmers and admins can create auctions.";
    header('Location: index.php');
    exit();
}

// Initialize objects
$auction = new Auction();
$categories = $auction->getCategories();

// Handle form submission with advanced validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // CSRF protection
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid security token");
        }

        // Advanced input validation
        $validator = new AuctionValidator($_POST);
        $errors = $validator->validate();

        if (empty($errors)) {
            // Handle multiple image uploads with optimization
            $imageHandler = new ImageHandler();
            $images = $imageHandler->processImages($_FILES['images']);

            // Create auction with transaction
            $db->beginTransaction();
            try {
                // Create main auction
                $auctionData = [
                    'seller_id' => $_SESSION['user_id'],
                    'category_id' => filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT),
                    'title' => htmlspecialchars(trim($_POST['title'])),
                    'description' => htmlspecialchars(trim($_POST['description'])),
                    'starting_price' => filter_var($_POST['starting_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    'min_increment' => filter_var($_POST['min_increment'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    'start_time' => date('Y-m-d H:i:s', strtotime($_POST['start_time'])),
                    'end_time' => date('Y-m-d H:i:s', strtotime($_POST['end_time'])),
                    'reserve_price' => filter_var($_POST['reserve_price'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    'quantity' => filter_var($_POST['quantity'] ?? 1, FILTER_SANITIZE_NUMBER_INT),
                    'status' => 'draft'
                ];

                $auction_id = $auction->createWithDetails($auctionData);

                // Save images
                foreach ($images as $index => $image) {
                    $imageHandler->saveAuctionImage($auction_id, $image, $index === 0);
                }

                // Save additional details
                $auction->saveAuctionMetadata($auction_id, [
                    'shipping_info' => $_POST['shipping_info'] ?? '',
                    'payment_terms' => $_POST['payment_terms'] ?? '',
                    'quality_grade' => $_POST['quality_grade'] ?? '',
                    'certification' => $_POST['certification'] ?? '',
                    'harvest_date' => $_POST['harvest_date'] ?? '',
                    'storage_conditions' => $_POST['storage_conditions'] ?? ''
                ]);

                $db->commit();

                // Send notifications
                NotificationService::notifyFollowers($auction_id, 'new_auction');
                
                // Success redirect with message
                $_SESSION['success'] = "Auction created successfully! It will be reviewed before going live.";
                header('Location: view.php?id=' . $auction_id);
                exit();

            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
        }
    } catch (Exception $e) {
        error_log("Auction creation error: " . $e->getMessage());
        $errors[] = "An error occurred while creating the auction. Please try again.";
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Load required assets
$pageTitle = "Create New Auction";
require_once 'includes/header.php';
?>

<div class="create-auction-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="create-auction-card">
                    <div class="card-header">
                        <h2><i class="fas fa-gavel me-2"></i>Create New Auction</h2>
                        <p class="text-muted">Fill in the details to create your auction</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form id="auctionForm" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <!-- Smart Category Selection -->
                        <div class="form-section">
                            <h3>Basic Information</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                        data-template="<?php echo htmlspecialchars($category['template'] ?? ''); ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a category</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Quality Grade</label>
                                        <select name="quality_grade" class="form-select">
                                            <option value="premium">Premium Grade</option>
                                            <option value="standard">Standard Grade</option>
                                            <option value="economy">Economy Grade</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="form-section">
                            <h3>Product Details</h3>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" name="title" class="form-control" required
                                               minlength="10" maxlength="100"
                                               pattern="^[a-zA-Z0-9\s\-_.,!?()]+$">
                                        <div class="invalid-feedback">
                                            Please enter a valid title (10-100 characters)
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control rich-editor" rows="5" required
                                                  minlength="50" maxlength="5000"></textarea>
                                        <div class="invalid-feedback">
                                            Please provide a detailed description (50-5000 characters)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing and Duration -->
                        <div class="form-section">
                            <h3>Pricing & Duration</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Starting Price (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="starting_price" class="form-control" 
                                                   required min="1" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Reserve Price (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="reserve_price" class="form-control" 
                                                   min="0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Minimum Bid Increment (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" name="min_increment" class="form-control" 
                                                   required min="1" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="datetime-local" name="start_time" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="datetime-local" name="end_time" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Images Upload -->
                        <div class="form-section">
                            <h3>Product Images</h3>
                            <div class="image-upload-container">
                                <div class="dropzone" id="imageDropzone">
                                    <div class="dz-message">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p>Drag & drop images here or click to upload</p>
                                        <span class="note">(Maximum 5 images, first image will be the main image)</span>
                                    </div>
                                </div>
                                <div class="image-preview" id="imagePreview"></div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="form-section">
                            <h3>Additional Details</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Shipping Information</label>
                                        <textarea name="shipping_info" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Terms</label>
                                        <textarea name="payment_terms" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="form-section text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-gavel me-2"></i>Create Auction
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="saveDraft()">
                                <i class="fas fa-save me-2"></i>Save as Draft
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="assets/js/create-auction.js"></script>
<script src="https://cdn.tiny.cloud/1/your-api-key/tinymce/5/tinymce.min.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script> 