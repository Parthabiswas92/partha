<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$Name = $address = $Code = "";
$Name_err = $address_err = $Code_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_name = trim($_POST["Name"]);
    if(empty($input_Name)){
        $name_err = "Please enter a Name.";
    } elseif(!filter_var($input_Name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $Name_err = "Please enter a valid Name.";
    } else{
        $Name = $input_Name;
    }
    
    // Validate address Course
    $input_Course = trim($_POST["Course"]);
    if(empty($input_Course)){
        $Course_err = "Please enter an Course.";     
    } else{
        $Course = $input_Course;
    }
    
    // Validate Course id
    $input_Course_id = trim($_POST["Course id"]);
    if(empty($input_Course_id)){
        $Course_id_err = "Please enter the Course id.";     
    } elseif(!ctype_digit($input_Course_id)){
        $Course_id_err = "Please enter a positive integer Course id.";
    } else{
        $Course_id = $input_Course_id;
    }
    
    // Check input errors before inserting in database
    if(empty($Name_err) && empty($Course_err) && empty($Course_id_err)){
        // Prepare an update statement
        $sql = "UPDATE Name SET Name=?, Course=?, Course_id=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_Name, $param_Course, $param_Course_id, $param_id);
            
            // Set parameters
            $param_Name = $Name;
            $param_address = $Course;
            $param_Code = $Course_id;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM Students WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $Name = $row["Name"];
                    $Course = $row["Course"];
                    $Course_id= $row["Course id"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the Students record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="Name" class="form-control <?php echo (!empty($Name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $Name; ?>">
                            <span class="invalid-feedback"><?php echo $Name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <textarea name="Course" class="form-control <?php echo (!empty($Course_err)) ? 'is-invalid' : ''; ?>"><?php echo $Course; ?></textarea>
                            <span class="invalid-feedback"><?php echo $Course_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Course id</label>
                            <input type="text" name="Course id" class="form-control <?php echo (!empty($Course_id)) ? 'is-invalid' : ''; ?>" value="<?php echo $Course_id; ?>">
                            <span class="invalid-feedback"><?php echo $Course_id_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>