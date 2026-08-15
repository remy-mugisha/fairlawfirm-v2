<?php
ob_start();

require_once 'include/header.php';
require_once 'propertyMgt/config.php';

if ($_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("SELECT u.*, r.role_name 
                                FROM users u 
                                JOIN roles r ON u.role_id = r.role_id 
                                WHERE u.id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $_SESSION['error_message'] = "User not found!";
            header("Location: manage_users.php");
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error fetching user: " . $e->getMessage();
        header("Location: manage_users.php");
        exit();
    }
} else {
    header("Location: manage_users.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
    $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $gender = $_POST['gender'];
    $role_id = filter_var($_POST['role_id'], FILTER_SANITIZE_NUMBER_INT);
    $status = $_POST['status'];
    
    try {
        $conn->beginTransaction();

        $conn->exec("SET FOREIGN_KEY_CHECKS=0");

        if ($email !== $user['email']) {
            $check_email = $conn->prepare("SELECT email FROM login WHERE email = :email AND email != :current_email");
            $check_email->bindParam(':email', $email);
            $check_email->bindParam(':current_email', $user['email']);
            $check_email->execute();
            
            if ($check_email->rowCount() > 0) {
                throw new Exception("Email already exists");
            }
            
            $update_login = $conn->prepare("UPDATE login SET email = :new_email WHERE email = :old_email");
            $update_login->bindParam(':new_email', $email);
            $update_login->bindParam(':old_email', $user['email']);
            $update_login->execute();
        }
        
        $new_usertype = ($role_id == 1) ? 'admin' : 'user';
        $update_usertype = $conn->prepare("UPDATE login SET usertype = :usertype WHERE email = :email");
        $update_usertype->bindParam(':usertype', $new_usertype);
        $update_usertype->bindParam(':email', $email);
        $update_usertype->execute();
        
        $stmt = $conn->prepare("UPDATE users 
                                SET first_name = :first_name, 
                                    last_name = :last_name, 
                                    email = :email, 
                                    phone = :phone, 
                                    gender = :gender, 
                                    role_id = :role_id, 
                                    status = :status 
                                WHERE id = :id");
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'propertyMgt/userImg/';
            
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $filename;
            
            $check = getimagesize($_FILES['profile_image']['tmp_name']);
            if ($check === false) {
                throw new Exception("File is not an image");
            }
            
            if ($_FILES['profile_image']['size'] > 5000000) {
                throw new Exception("File is too large (max 5MB)");
            }
            
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed");
            }
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                if (!empty($user['profile_image']) && file_exists($user['profile_image'])) {
                    unlink($user['profile_image']);
                }
                
                $update_image = $conn->prepare("UPDATE users SET profile_image = :profile_image WHERE id = :id");
                $update_image->bindParam(':profile_image', $target_file);
                $update_image->bindParam(':id', $id);
                $update_image->execute();
            } else {
                throw new Exception("Error uploading file");
            }
        }
        
        $conn->exec("SET FOREIGN_KEY_CHECKS=1");

        $conn->commit();
        
        $_SESSION['success_message'] = "User updated successfully!";
        header("Location: manage_users.php");
        exit();
    } catch(Exception $e) {
        $conn->rollback();
        
        $conn->exec("SET FOREIGN_KEY_CHECKS=1");

        $_SESSION['error_message'] = "Error updating user: " . $e->getMessage();
        header("Location: edit_user.php?id=" . $id);
        exit();
    }
}

try {
    $stmt = $conn->query("SELECT * FROM roles");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching roles: " . $e->getMessage();
}
?>

<div class="row column1">
    <div class="col-md-12">
        <div class="white_shd full margin_bottom_30">
            <div class="full graph_head">
                <div class="heading1 margin_0">
                    <h2>Edit User</h2>
                </div>
            </div>
            <div class="full padding_infor_info">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        echo $_SESSION['error_message']; 
                        unset($_SESSION['error_message']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field">
                                <label class="label_field">First Name</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field">
                                <label class="label_field">Last Name</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label_field">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="form-control">
                    </div>
                    <div class="field">
                        <label class="label_field">Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required class="form-control">
                    </div>
                    <div class="field">
                        <label class="label_field">Gender</label>
                        <div class="radio_group">
                            <div class="radio_option">
                                <input type="radio" name="gender" id="male" value="Male" <?php echo ($user['gender'] == 'Male') ? 'checked' : ''; ?> class="radio_input">
                                <label for="male" class="radio_label">
                                    <span class="radio_custom"></span>
                                    Male
                                </label>
                            </div>
                            <div class="radio_option">
                                <input type="radio" name="gender" id="female" value="Female" <?php echo ($user['gender'] == 'Female') ? 'checked' : ''; ?> class="radio_input">
                                <label for="female" class="radio_label">
                                    <span class="radio_custom"></span>
                                    Female
                                </label>
                            </div>
                            <div class="radio_option">
                                <input type="radio" name="gender" id="other" value="Other" <?php echo ($user['gender'] == 'Other') ? 'checked' : ''; ?> class="radio_input">
                                <label for="other" class="radio_label">
                                    <span class="radio_custom"></span>
                                    Other
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label_field">Role</label>
                        <select name="role_id" class="form-control" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_id']; ?>" <?php echo ($role['role_id'] == $user['role_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($role['role_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="label_field">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Active" <?php echo ($user['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo ($user['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                            <option value="Pending" <?php echo ($user['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>
                    <div class="field">
                        <label class="label_field">Profile Image</label>
                        <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                            <div class="mb-2">
                                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Current Profile Image" class="img-thumbnail" style="max-height: 100px;">
                                <p class="text-muted">Current profile image</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="profile_image" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep the current image</small>
                    </div>
                    <div class="field margin_0">
                        <button type="submit" class="btn btn-info btn-block">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
ob_end_flush();
?>

<style>
.white_shd {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.field {
    margin-bottom: 20px;
}

.label_field {
    font-weight: bold;
    color: #555;
    display: block;
    margin-bottom: 8px;
}

/* Radio button styles */
.radio_group {
    display: flex;
    gap: 20px;
    margin-top: 10px;
}

.radio_option {
    display: flex;
    align-items: center;
}

.radio_input {
    position: absolute;
    opacity: 0;
}

.radio_label {
    position: relative;
    padding-left: 30px;
    cursor: pointer;
    font-weight: normal;
    color: #555;
    display: flex;
    align-items: center;
}

.radio_custom {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 50%;
    transition: all 0.3s;
}

.radio_input:checked ~ .radio_label .radio_custom {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.radio_input:checked ~ .radio_label .radio_custom::after {
    content: "";
    position: absolute;
    top: 4px;
    left: 4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: white;
}

.radio_input:focus ~ .radio_label .radio_custom {
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.2);
}

.btn-block {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-block:hover {
    background-color: #138496;
    border-color: #117a8b;
}

@media (max-width: 768px) {
    .white_shd {
        padding: 15px;
    }

    .radio_group {
        flex-direction: column;
        gap: 10px;
    }

    .field {
        margin-bottom: 15px;
    }
}

@media (max-width: 480px) {
    .white_shd {
        padding: 10px;
    }

    .btn-block {
        font-size: 14px;
    }
}
</style>