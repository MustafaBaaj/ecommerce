<?php
    require_once '../core/init.php';
    include 'includes/head.php';
    include 'includes/navigation.php';
    //get brands form database
    $sql = "SELECT * FROM  brand ORDER BY brand";
    $results  = $db->query($sql);
    $errors = array();


//Edit Brand
    if(isset($_GET['edit']) && !empty($_GET['edit'])){
        $edit_id = (int)$_GET['edit'];
        $edit_id = sanitize($edit_id);
        $sql2 = "SELECT * FROM brand WHERE id = '$edit_id'";
        $edit_result = $db->query($sql2);
        $eBrand = mysqli_fetch_assoc($edit_result);

    }

    //Delete Brand
    if(isset($_GET['delete']) && !empty($_GET['delete'])){
     $delete_id = (int)$_GET['delete'];
     $delete_id = sanitize($delete_id);
     $sql= "DELETE FROM brand WHERE id = '$delete_id'";
     $db->query($sql);
     header('Location: brands.php');
 }

    //of add fprm is submitted unde e formu? care form =))pasta
    if(isset($_POST['add_submit'])){
        $brand = sanitize($_POST ['brand']);
        //check if brand is bland
        if($_POST['brand'] == ''){
             //ar trebui sa inchida scritu die asta
            $errors[] .= 'you must enter a brand!';
        }//cheack if brand exist in data base
        $sql ="SELECT * FROM brand WHERE brand ='$brand'";
        if(isset($_GET['edit'])){
            $sql ="SELECT * FROM brand WHERE brand= '$brand' AND id !='$edit_id'";
        }
        $result = $db->query($sql);
        $count = mysqli_num_rows($result);
        if($count > 0){
            $errors[] .= $brand. 'that brand already exist. please sugi pula ';
        }
        //display errors
        if(!empty($errors)){
            echo display_errors($errors);

        } else{
            //ADD brand in database
            $sql = "INSERT INTO brand (brand) VALUES ('$brand')";
            if(isset($_GET['edit'])){
                $sql = "UPDATE brand SET brand = '$brand' WHERE id = '$edit_id'";
            }
            $db->query($sql);
            header('Location: brands.php');
        }

    }
?>
<style>
.table-auto{
    width: auto;

}
</style>

    <h2 class="text-center">Brands</h2><hr />
    <!-- brand form -->
    <Div class="text-center">
        <form class="form-inline" action="brands.php<?=((isset($_GET['edit']))?'?edit='.$_edit_id:'');?>" method="post">
            <Div class="form-group">

                <?php
                $brand_value = '';
                 if(isset($_GET['edit'])){
                    $brand_value = $eBrand['brand'];
                }else{
                    if(isset($_POST['brand'])){
                        $brand_value = sanitize($_POST['brand']);
                    }
                } ?>
                <label for="brand"><?=((isset($_GET['edit']))?'Edit':'Add A'); ?> a Brand:</label>
                <input type="text" name ="brand" class="form-contrl" value="<?=$brand_value;  ?>">
                <?php if(isset($_GET['edit'])):  ?>
                    <a href="brands.php" class="btn btn-default">Cancel</a>

                <?php endif; ?>
                <input type="submit" name="add_submit" value="<?=((isset($_GET['edit']))?'Edit':'Add'); ?> Brand" class="btn  btn-success" />
            </Div>

        </form>
    </Div>
    <hr />
    <table class=" table-auto table table-bordered table-striped table-condensed" >
<thead>
          <th></th><th>Brand</th><th></th>
</thead>
<tbody>
    <?php while($brand = mysqli_fetch_assoc($results)): ?>
<tr>
    <td><a href="brands.php?edit=<?=$brand['id']; ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a></td>
    <td><?= $brand['brand'];?></td>
    <td><a href="brands.php?delete=<?=$brand['id']; ?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove-sign"></span></a></td>
  </tr>
    <?php endwhile; ?>
      </tbody>
    </table>

<?php include 'includes/footer.php'; ?>
