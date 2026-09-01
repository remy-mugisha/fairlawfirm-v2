<?php
require_once 'include/header.php';
require_once 'propertyMgt/config.php';
require_once __DIR__ . '/csrf.php';
?>

<style>
.flf-upload-area {
    position: relative;
    width: 100%;
    border: 2px dashed #d9dee9;
    border-radius: var(--flf-radius);
    background: #fafbfe;
    text-align: center;
    padding: 40px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.flf-upload-area:hover,
.flf-upload-area.flf-dragover {
    border-color: var(--flf-royal);
    background: rgba(24, 53, 143, 0.04);
}
.flf-upload-area input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
.flf-upload-icon {
    font-size: 40px;
    color: var(--flf-blue);
    margin-bottom: 12px;
}
.flf-upload-area:hover .flf-upload-icon,
.flf-upload-area.flf-dragover .flf-upload-icon {
    color: var(--flf-royal);
}
.flf-upload-text {
    font-family: var(--flf-font-body);
    font-size: 15px;
    color: var(--flf-slate);
    margin: 0 0 4px;
}
.flf-upload-hint {
    font-family: var(--flf-font-body);
    font-size: 12.5px;
    color: var(--flf-muted);
    margin: 0;
}
.flf-upload-preview {
    display: none;
    margin-top: 16px;
    position: relative;
    width: 100%;
    max-width: 360px;
    margin-left: auto;
    margin-right: auto;
}
.flf-upload-preview img {
    width: 100%;
    max-height: 220px;
    object-fit: cover;
    border-radius: var(--flf-radius-sm);
    border: 2px solid var(--flf-blue);
}
.flf-upload-preview .flf-remove-img {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--flf-danger);
    color: var(--flf-white);
    border: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}
.flf-field {
    margin-bottom: 22px;
}
.flf-field label {
    display: block;
    font-family: var(--flf-font-body);
    font-weight: 600;
    font-size: 13.5px;
    color: var(--flf-slate);
    margin-bottom: 8px;
}
</style>

<div class="midde_cont">
    <div class="container-fluid">

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-check-circle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius:var(--flf-radius-sm);margin:0;">
                        <i class="fa fa-exclamation-triangle" style="margin-right:6px;"></i>
                        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                        <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                            <h2><i class="fa fa-plus-circle" style="color:var(--flf-gold);margin-right:10px;font-size:20px;"></i>Add Property</h2>
                            <a href="display_properties.php" class="btn btn-secondary btn-sm">
                                <i class="fa fa-th-list" style="margin-right:5px;"></i>View All Properties
                            </a>
                        </div>
                    </div>

                    <div class="full padding_infor_info">
                        <form action="add_property.php" method="post" enctype="multipart/form-data" style="max-width:680px;"><?php echo csrfHiddenField(); ?>

                            <div class="flf-field">
                                <label>Upload Image</label>
                                <div class="flf-upload-area" id="uploadArea">
                                    <input type="file" name="image" id="imageUpload" accept="image/*" required>
                                    <div class="flf-upload-icon"><i class="fa fa-cloud-upload"></i></div>
                                    <p class="flf-upload-text"><strong>Click to upload</strong> or drag and drop</p>
                                    <p class="flf-upload-hint">JPG, PNG, or GIF (Max 5MB)</p>
                                </div>
                                <div class="flf-upload-preview" id="imagePreview">
                                    <img src="" alt="Preview">
                                    <button type="button" class="flf-remove-img" id="removePreview" title="Remove image">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="flf-field">
                                <label for="location"><i class="fa fa-map-marker-alt" style="color:var(--flf-gold);margin-right:5px;"></i>Location</label>
                                <input type="text" class="form-control" name="location" id="location" placeholder="Enter property location" required>
                            </div>

                            <div class="flf-field">
                                <label for="title"><i class="fa fa-tag" style="color:var(--flf-gold);margin-right:5px;"></i>Title</label>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Enter property title" required>
                            </div>

                            <div style="padding-top:8px;">
                                <button type="submit" name="submit" class="btn btn-info">
                                    <i class="fa fa-plus" style="margin-right:5px;"></i>Add Property
                                </button>
                                <a href="display_properties.php" class="btn btn-secondary" style="margin-left:10px;">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var imageUpload = document.getElementById('imageUpload');
    var imagePreview = document.getElementById('imagePreview');
    var removePreview = document.getElementById('removePreview');
    var uploadArea = document.getElementById('uploadArea');

    imageUpload.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                imagePreview.style.display = 'block';
                imagePreview.querySelector('img').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    removePreview.addEventListener('click', function() {
        imageUpload.value = '';
        imagePreview.style.display = 'none';
        imagePreview.querySelector('img').src = '';
    });

    ['dragenter', 'dragover'].forEach(function(evt) {
        uploadArea.addEventListener(evt, function() {
            uploadArea.classList.add('flf-dragover');
        });
    });
    ['dragleave', 'drop'].forEach(function(evt) {
        uploadArea.addEventListener(evt, function() {
            uploadArea.classList.remove('flf-dragover');
        });
    });
});
</script>

<?php
require_once 'include/footer.php';
?>