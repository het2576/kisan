// Initialize rich text editor
tinymce.init({
    selector: '.rich-editor',
    plugins: 'autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste help wordcount',
    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    height: 300
});

// Initialize Dropzone
Dropzone.autoDiscover = false;
const myDropzone = new Dropzone("#imageDropzone", {
    url: "api/upload-image.php",
    maxFiles: 5,
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    thumbnailWidth: 150,
    thumbnailHeight: 150,
    init: function() {
        this.on("success", function(file, response) {
            const data = JSON.parse(response);
            if (data.success) {
                file.serverId = data.imageId;
                updateImageOrder();
            }
        });
    }
});

// Form validation
const form = document.getElementById('auctionForm');
form.addEventListener('submit', function(event) {
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
});

// Dynamic pricing calculator
const startingPriceInput = document.querySelector('[name="starting_price"]');
const reservePriceInput = document.querySelector('[name="reserve_price"]');
const minIncrementInput = document.querySelector('[name="min_increment"]');

function updatePricingRecommendations() {
    const startingPrice = parseFloat(startingPriceInput.value) || 0;
    
    // Recommend minimum increment (2-5% of starting price)
    const recommendedIncrement = Math.max(1, Math.round(startingPrice * 0.02));
    minIncrementInput.setAttribute('placeholder', `Recommended: ₹${recommendedIncrement}`);
    
    // Recommend reserve price (120-150% of starting price)
    const recommendedReserve = Math.round(startingPrice * 1.2);
    reservePriceInput.setAttribute('placeholder', `Recommended: ₹${recommendedReserve}`);
}

startingPriceInput.addEventListener('input', updatePricingRecommendations);

// Smart date/time picker
const startTimeInput = document.querySelector('[name="start_time"]');
const endTimeInput = document.querySelector('[name="end_time"]');

function updateDateTimeConstraints() {
    const now = new Date();
    const minStart = new Date(now.getTime() + (30 * 60 * 1000)); // Minimum 30 minutes from now
    const maxStart = new Date(now.getTime() + (30 * 24 * 60 * 60 * 1000)); // Maximum 30 days from now
    
    startTimeInput.min = minStart.toISOString().slice(0, 16);
    startTimeInput.max = maxStart.toISOString().slice(0, 16);
    
    if (startTimeInput.value) {
        const startTime = new Date(startTimeInput.value);
        const minEnd = new Date(startTime.getTime() + (60 * 60 * 1000)); // Minimum 1 hour duration
        const maxEnd = new Date(startTime.getTime() + (14 * 24 * 60 * 60 * 1000)); // Maximum 14 days duration
        
        endTimeInput.min = minEnd.toISOString().slice(0, 16);
        endTimeInput.max = maxEnd.toISOString().slice(0, 16);
    }
}

startTimeInput.addEventListener('input', updateDateTimeConstraints);
updateDateTimeConstraints();

// Save as draft functionality
function saveDraft() {
    const formData = new FormData(form);
    formData.append('status', 'draft');
    
    fetch('api/save-draft.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Draft saved successfully!', 'success');
        } else {
            showNotification('Error saving draft', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving draft', 'error');
    });
}

// Category-specific template loader
const categorySelect = document.querySelector('[name="category_id"]');
categorySelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const template = selected.dataset.template;
    
    if (template) {
        tinymce.get('description').setContent(template);
    }
});

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.create-auction-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate start and end times
        const startTime = new Date(form.start_time.value);
        const endTime = new Date(form.end_time.value);
        const now = new Date();
        
        if (startTime < now) {
            alert('Start time must be in the future');
            return;
        }
        
        if (endTime <= startTime) {
            alert('End time must be after start time');
            return;
        }
        
        // Validate prices
        const startingPrice = parseFloat(form.starting_price.value);
        const minIncrement = parseFloat(form.min_increment.value);
        
        if (startingPrice <= 0) {
            alert('Starting price must be greater than 0');
            return;
        }
        
        if (minIncrement <= 0) {
            alert('Minimum increment must be greater than 0');
            return;
        }
        
        // If all validations pass, submit the form
        form.submit();
    });

    // Preview image before upload
    const imageInput = form.querySelector('input[type="file"]');
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You could add an image preview here if desired
                console.log('Image selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });

    // Auto-set minimum dates for datetime inputs
    const startTimeInput = form.querySelector('input[name="start_time"]');
    const endTimeInput = form.querySelector('input[name="end_time"]');
    
    const minDateTime = new Date();
    minDateTime.setMinutes(minDateTime.getMinutes() + 5); // Add 5 minutes buffer
    
    const formatDateTime = (date) => {
        return date.toISOString().slice(0, 16);
    };
    
    startTimeInput.min = formatDateTime(minDateTime);
    
    startTimeInput.addEventListener('change', function() {
        const selectedStart = new Date(this.value);
        selectedStart.setHours(selectedStart.getHours() + 1); // Minimum 1 hour auction duration
        endTimeInput.min = formatDateTime(selectedStart);
    });
}); 