<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/init.php';
include  'includes/head.php';
include  'includes/navigation.php';

$sql="SELECT * FROM categories WHERE parent = 0";
$result = $db->query($sql);
$errors = array();
$category = '';
$post_parent = '';

//EDIT Category
if(isset($_GET['edit']) && !empty($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $edit_id= sanitize($edit_id);
    $edit_sql = "SELECT * FROM categories WHERE id = '$edit_id'";
    $edit_result = $db->query($edit_sql);
    $edit_category = mysqli_fetch_assoc($edit_result);
}

//DELETE category
if(isset($_GET['delete']) && !empty($_GET['delete'])){
    $delete_id = (int)$_GET['delete'];
    $delete_id = sanitize($delete_id);
    $sql = "SELECT * FROM categories WHERE id = '$delete_id'";
    $result =$db->query($sql);
    $category = mysqli_fetch_assoc($result);
    if($category['parent'] == 0 ){
        $sql = "DELETE FROM categories WHERE parent='$delete_id'";
        $db->query($sql);

    }
    $dsql ="DELETE FROM categories WHERE id ='$delete_id'";
    $db->query($dsql);
    header('Location: categories.php');

}

//Process form
if(isset($_POST) && !empty($_POST)){
    $post_parent = sanitize($_POST['parent']);
    $category = sanitize($_POST['category']);
    $sqlform ="SELECT * FROM categories WHERE category='$category' AND  parent='$post_parent'";
    if(isset($_GET['edit'])){
        $id= $edit_category['id'];
        $sqlform = "SELECT * FROM categories WHERE catergory = '$edit_value_of_cat' AND parent = '$post_parent' AND id !='$id'";
    }
    $fresult = $db->query($sqlform);
    $count = mysqli_num_rows($fresult);

    //if category is blank
    if($category == ''){
        $errors[] .= 'The cateogry cannot be left blank.';
    }
        //of exist in the database
        if($count != false){
            $errors[] .= $category. 'already exists. please choose a new category.' ;

        }

        //Display errors or update databse
       if(!empty($errors)){
            //display $errors
            $display = display_errors($errors); ?>
            <script>
                $('document').ready(function(){
                    $('#errors').html('<?php echo $display; ?>');
                });
                </script>

    <?php     } else {
            $updatesql ="INSERT INTO categories (category , parent) VALUES ('$category', '$post_parent')";
            if(isset($_GET['edit'])){
                $updatesql = "UPDATE categories set catergory = '$category', parent= '$post_parent' WHERE id='$edit_id'";
            }
            $db->query($updatesql);
            header('Location: categories.php');

        }

    }
    $category_value = '';
    $parent_value = 0;
    if(isset($_GET['edit'])){
        $category_value = $edit_category['category'];
        $parent_value = $edit_category['parent'];
    }else{
        if(isset($_POST)){
            $category_value = $category;
            $parent_value = $post_parent;

        }
    }
?>

<h2 class="text-center">Categories</h2> <hr />
<div class="row">
    <!-- Form -->
    <div class="col-md-6">
        <form class="form" action="categories.php<?php echo ((isset($_GET['edit']))?'?edit='.$edit_id:''); ?>" method="post">
            <legend><?php echo((isset($_GET['edit']))?'Edit':'Add A'); ?> category</legend>
            <div id="errors"></div>
            <div class="form-group">
                <label for="Parent">Parent</label>
                <select class="form-control" name="parent" id="parent">
                    <option value="0"<?php echo (($parent_value == 0)?' selected="selected"':'');?>>parent</option>
                    <?php while($parent = mysqli_fetch_assoc($result)) : ?>
                        <option value="<?=$parent['id']; ?>"<?php echo (($parent_value == $parent['id'])?'selected="selected"':'');?>><?=$parent['category'];?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div calss="form-group">
                <label for="category">Category</label>
                <input type="text" class="form-control" id="category" name="category" value="<?php echo $category_value; ?> " />
            </div>
            <div class="form-group">
                <input type="submit" value="<?php echo ((isset($_GET['edit']))? 'Edit' : 'Add'); ?> Category" class="btn btn-success" />
            </div>
        </form>
    </div>


    <!-- category table -->
    <div class="col-md-6">
        <table class="table table-bordered">
            <thead>
                <th>Categories</th><th>Parent</th><th></th>

            </thead>
            <tbody>
                <?php
                $sql="SELECT * FROM categories WHERE parent = 0";
                $result = $db->query($sql);
                while($parent = mysqli_fetch_assoc($result)):
                    $parent_id = (int)$parent['id'];
                    $sql2= "SELECT * FROM categories WHERE parent ='$parent_id'";
                    $cresult = $db->query($sql2);
                     ?>

                <tr class="bg-primary">
                    <td><?= $parent['category']; ?></td>
                    <td>Parent</td>
                    <td>
                        <a href="Categories.php?edit=<?=$parent['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a href="Categories.php?delete=<?=$parent['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>

                    </td>
                </tr>
                <?php while($child= mysqli_fetch_assoc($cresult)): ?>
                <tr class="bg-info">
                    <td><?= $child['category']; ?></td>
                    <td><?=$parent['category']; ?></td>
                    <td>
                        <a href="Categories.php?edit=<?=$child['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                        <a href="Categories.php?delete=<?=$child['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a>

                    </td>
                </tr>
            <?php endwhile; ?>
            <?php endwhile;?>
            </tbody>

        </table>
    </div>

</div>

<?php include 'includes/footer.php';
