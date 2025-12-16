// Product Form JavaScript
document.addEventListener('DOMContentLoaded', function () {
    const imageFilesInput = document.getElementById('image_files');
    const dropZone = document.getElementById('drop-zone');
    const addImageLinkBtn = document.getElementById('add-image-link-btn');

    if (imageFilesInput && dropZone) {
        // Handle file input change
        imageFilesInput.addEventListener('change', handleFileSelect);

        // Handle drag and drop
        dropZone.addEventListener('drop', handleDrop);
        dropZone.addEventListener('dragover', handleDragOver);
        dropZone.addEventListener('dragleave', handleDragLeave);
        dropZone.addEventListener('click', function () {
            imageFilesInput.click();
        });
    }

    if (addImageLinkBtn) {
        addImageLinkBtn.addEventListener('click', addImageLink);
    }

    const uploadDeviceBtn = document.getElementById('upload-device-btn');
    if (uploadDeviceBtn && imageFilesInput) {
        uploadDeviceBtn.addEventListener('click', function () {
            imageFilesInput.click();
        });
    }

    // Close and cancel buttons
    document.querySelectorAll('.close-btn, .btn-cancel').forEach(btn => {
        if (!btn.id || btn.id !== 'reset-password-form-btn') {
            btn.addEventListener('click', function () {
                window.history.back();
            });
        }
    });
});

// Functions defined outside DOMContentLoaded for reuse
function handleFileSelect(event) {
    const files = event.target.files || (event.dataTransfer ? event.dataTransfer.files : []);
    if (files.length > 0) {
        displaySelectedFiles(files);
    }
}

function handleDrop(event) {
    event.preventDefault();
    event.stopPropagation();
    const dropZone = event.currentTarget;
    dropZone.classList.remove('drag-over');

    const files = event.dataTransfer.files;
    if (files.length > 0) {
        const imageFilesInput = document.getElementById('image_files');
        if (imageFilesInput) {
            // Create a new FileList-like object
            const dataTransfer = new DataTransfer();
            for (let i = 0; i < files.length; i++) {
                dataTransfer.items.add(files[i]);
            }
            imageFilesInput.files = dataTransfer.files;
            handleFileSelect({ target: imageFilesInput, dataTransfer: dataTransfer });
        }
    }
}

function handleDragOver(event) {
    event.preventDefault();
    event.stopPropagation();
    event.currentTarget.classList.add('drag-over');
}

function handleDragLeave(event) {
    event.preventDefault();
    event.stopPropagation();
    event.currentTarget.classList.remove('drag-over');
}

function displaySelectedFiles(files) {
    const container = document.getElementById('image-preview-container');
    if (!container) return;

    container.innerHTML = '';
    Array.from(files).forEach((file) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '4px';
                img.style.margin = '5px';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
}

// Add image link function
function addImageLink() {
    const link = prompt('Enter image URL:');
    if (link && link.trim()) {
        const container = document.getElementById('image-preview-container');
        const imageUrlsInput = document.getElementById('image-urls');
        if (container && imageUrlsInput) {
            const img = document.createElement('img');
            img.src = link.trim();
            img.style.maxWidth = '100px';
            img.style.maxHeight = '100px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '4px';
            img.style.margin = '5px';
            container.appendChild(img);

            // Update hidden input with image URLs
            const currentUrls = imageUrlsInput.value ? imageUrlsInput.value.split(',') : [];
            currentUrls.push(link.trim());
            imageUrlsInput.value = currentUrls.join(',');
        }
    }
}

// Initialize existing product images
function initializeProductImages(imageUrls) {
    const imageUrlsInput = document.getElementById('image-urls');
    const previewContainer = document.getElementById('image-preview-container');

    if (imageUrls && imageUrls.length > 0 && previewContainer && imageUrlsInput) {
        // Set the hidden input value
        imageUrlsInput.value = JSON.stringify(imageUrls);

        // Display existing images
        imageUrls.forEach((url, index) => {
            const imageWrapper = document.createElement('div');
            imageWrapper.className = 'image-preview-item';
            imageWrapper.dataset.index = index;

            const img = document.createElement('img');
            img.src = url;
            img.alt = 'Product Image';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-image-btn';
            removeBtn.innerHTML = '<i class="fa-solid fa-times"></i>';
            removeBtn.addEventListener('click', function() {
                imageWrapper.remove();
                updateImageUrls();
            });

            imageWrapper.appendChild(img);
            imageWrapper.appendChild(removeBtn);
            previewContainer.appendChild(imageWrapper);
        });
    }
}

function updateImageUrls() {
    const previewContainer = document.getElementById('image-preview-container');
    const imageUrlsInput = document.getElementById('image-urls');
    if (previewContainer && imageUrlsInput) {
        const images = Array.from(previewContainer.querySelectorAll('img')).map(img => img.src);
        imageUrlsInput.value = JSON.stringify(images);
    }
}

// Initialize if product images data is available
if (typeof window.productImagesData !== 'undefined' && window.productImagesData) {
    document.addEventListener('DOMContentLoaded', function() {
        initializeProductImages(window.productImagesData);
    });
}
