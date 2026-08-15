<?php
require_once 'include/header.php';
?>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0 d-flex justify-content-between align-items-center">
                    <h2>Add Property</h2>
                    <a href="display_properties.php" class="btn btn-info btn-sm">View All Properties</a>
                </div>
            </div>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="full progress_bar_inner">
                <div class="row">
                    <div class="col-md-12">
                        <div class="full padding_infor_info">
                            <form class="form-horizontal" action="add_property.php" method="post" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <label class="control-label col-sm-3">Upload Image</label>
                                    <div class="col-sm-9">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="image" id="imageUpload" accept="image/*" required>
                                            <label class="custom-file-label" for="imageUpload">Choose file</label>
                                        </div>
                                        <div class="mt-3" id="imagePreview" style="display: none;">
                                            <img src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="control-label col-sm-3">Location</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="location" id="location" placeholder="Enter location" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="control-label col-sm-3" for="title">Title</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="title" placeholder="Enter title" required>
                                        </div>
                                        <!-- <textarea class="form-control" name="title" id="title" rows="4" placeholder="Enter property title" required></textarea> -->
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" name="submit" class="btn btn-info">Add Property</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-file-label::after {
    content: "Browse";
}

.form-group {
    margin-bottom: 1.5rem;
}

.control-label {
    font-weight: 500;
    padding-top: 7px;
}

.padding_infor_info {
    padding: 30px;
}

#imagePreview img {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
}

@media (max-width: 768px) {
    .control-label {
        margin-bottom: 10px;
    }
    
    .form-group {
        margin-bottom: 2rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .ml-2 {
        margin-left: 0;
    }
}
</style>
              
<?php
require_once 'include/footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    
    imageUpload.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.style.display = 'block';
                imagePreview.querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(file);            
            const fileName = file.name;
            const label = this.nextElementSibling;
            label.textContent = fileName;
        }
    });
});
</script>