<?php
if (session_status() === PHP_SESSION_NONE) {
}
require_once 'include/header.php';
require_once 'propertyMgt/config.php';

function getCount($conn, $table, $condition = null) {
    try {
        $sql = "SELECT COUNT(*) as count FROM $table";
        if ($condition) {
            $sql .= " WHERE $condition";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (PDOException $e) {
        return 0;
    }
}

$userCount = getCount($conn, "users", "status = 'Active'");
$totalProperties = getCount($conn, "add_property");
$rentalProperties = getCount($conn, "properties", "property_status = 'For Rent'");
?>

<div class="row column1">
    <div class="col-md-12">     
            <div class="full padding_infor_info">
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header text-white" style="background: #fff;">
                                <h4 class="mb-0">Statistics</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="stat-card text-white">
                                            <div class="stat-card-inner">
                                                <h5 style="color: #17a2b8;">Total Employers</h5>
                                                <h2 style="color: #17a2b8;"><?php echo $userCount; ?></h2>
                                                <p>Registered active Employers</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-card text-white">
                                            <div class="stat-card-inner">
                                                <h5 style="color: #ffc107;">Total Properties</h5>
                                                <h2 style="color: #ffc107;"><?php echo $totalProperties; ?></h2>
                                                <p>All properties in system</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-card text-dark">
                                            <div class="stat-card-inner">
                                                <h5 style="color: #065f46;">Rental Properties</h5>
                                                <h2 style="color: #065f46;"><?php echo $rentalProperties; ?></h2>
                                                <p>Properties for rent</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<style>
.dashboard_card {
    border-radius: 10px;
    overflow: hidden;
    height: 130px;
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.dashboard_card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.15);
}

.inner_card {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    width: 100%;
    padding: 20px;
    text-align: center;
}

.icon_holder {
    margin-bottom: 10px;
}

.icon_holder i {
    font-size: 30px;
}

.stat-card {
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.15);
}

.stat-card-inner {
    text-align: center;
}

.stat-card h5 {
    margin-bottom: 15px;
    font-weight: 600;
}

.stat-card h2 {
    font-size: 36px;
    margin-bottom: 10px;
    font-weight: 700;
}

@media (max-width: 768px) {
    .dashboard_card {
        height: 120px;
    }
    
    .icon_holder i {
        font-size: 24px;
    }
    
    .stat-card h2 {
        font-size: 28px;
    }
}
</style>

<?php
require_once 'include/footer.php';
?>