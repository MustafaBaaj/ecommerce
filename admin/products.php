<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/tutorial/core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
if(isset($_GET['add']) || isset($_Get['edit'])){
$brandQuery = $db->query("SELECT * FROM brand ORDER BY brand");
$parentQuery = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");
if(isset($_POST['submit'])){
    $title = sanitize($_POST['title']);
    $brand = sanitize($_POST['brand']);
    $categories = sanitize($_POST['child']);
    $price = sanitize($_POST['price']);
    $list_price = sanitize($_POST['list_price']);
    $sizes = sanitize($_POST['sizes']);
    $description = sanitize($_POST['description']);
    $dbpath = '';
    $errors = array();

    if(!empty($_POST['sizes'])) {
        $sizestring = sanitize($_POST['sizes']);
        $sizestring = rtrim($sizestring,',');
        $sizesarray = explode(',',$sizestring);
        $saray = array();
        $qarray = array();
        foreach ($sizesarray as $ss) {
            $s = explode(':', $ss);
            $sarray[] = $s[0];
            $qarray[] = $s[1];
        }
    }
    else{$sizesarray = array();}
    $requierd = array('title','brand','price','parent','child','sizes');
    foreach($requierd as $field){
        if($_POST[$field] == ''){
            $errors[] = 'ALL fields with and astrisk are requierd.';
            break;
        }
    }

    if(!isset($_FILES)){
        $photo = $_FILES['photo'];
        $name = $photo['name'];
        $namearray = explode('.',$name);
        $filename = $namearray[0];
        $fileext  = $namearray[1];
        $mime =  explode('/',$photo['type']);
        $mimetype = $mime[0];
        $mimeext = $mime[1];
        $tmploc = $photo['tmp_name'];
        $filesize = $photo['size'];
        $allowed = array('png','jpg','jpeg','gif');
        $uploadname = md5(microtime()).'.'.$fileext;
        $uploadpath = BASEURL.'images/products/'.$uploadname;
        $dbpath = 'tutorial/images/products/'.$uploadname;
        if($mimetype != 'image'){
            $errors[] = 'the file must be and image';
        }
        if(!in_array($fileext, $allowed)){
            $errors[] = 'The file extension must be  a png, jpg, jpeg, or gif';
        }
        if ($filesize > 1000000) {
            $errors[] = 'the files size must be unde 25mb';
        }
        if ($fileext != $mimeext && ($mimeext == 'jepg' && $fileext != 'jpg')) {

            $errors[] = ' file extenos dose not match';
        }
    }
    if(!empty($errors)){
        echo display_errors($errors);

    }else{
        //uploud file and insert into database
        move_uploaded_file($tmpLoc,$uploadPath);
   $insertSql = $db -> query("INSERT INTO `products` (`title`, `price`, `list_price`, `brand`, `categories`, `image`, `description`, `featured`, `sizes`, `deleted`) VALUES ('$title', '$price', '$list_price', '$brand', '$categories', '$dbpath', '$description', '0', '$sizes', '0')");
   header('Location: products.php');


    }
}



?>
<h2 class="text-center"><?=((isset($_GET['edit']))?'Edit':'Add A new'); ?> products</h2><hr />
<form action="products.php?add=1" method="POST" enctype="multipart/form-data">
    <div class="form-group col-md-3">
        <label for="title">Title*:</label>
        <input type="text" name="title" class="form-control" id="title" value="<?=((isset($_POST['title']))?sanitize($_POST['title']):'');?>" />
    </div>
    <div class="form-group col-md-3">
        <label for="brand">Brand*:</label>
        <select class="form-control" id="brand" name="brand">
            <option value=""<?=((isset($_POST['brand']) && $_POST['brand'] == '``')?'selected':''); ?>></option>
                    <?php while($brand= mysqli_fetch_assoc($brandQuery)): ?>
            <option value="<?=$brand['id'];?>"<?=((isset($_POST['brand']) && $_POST['brand'] == $brand['id'])?'selected':'');?>><?= $brand['brand']; ?></option>
                    <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="parent">Parent Category*:</label>
        <select class="form-control" id="parent" name="parent">
            <option value=""<?=((isset($_POST['parent']) && $_POST['parent'] == '')?'selected':'');?>></option>
            <?php while($parent = mysqli_fetch_assoc($parentQuery)): ?>
                <option value="<?= $parent['id'];?>"<?=((isset($_POST['parent']) && $_POST['parent'] == $parent['id'])?'select': '');?>><?=$parent['category'];?></option>

            <?php endwhile; ?>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="child">Child Category*:</label>
        <select class="form-control" id="child" name="child">
        </select>
    </div>

    <div class="form-group col-md-3">
        <label for="price">Price*:</label>
        <input type="text" class="form-control" id="price" name="price" value="<?=((isset($_POST['price']))?sanitize($_POST['price']):'');?>">
    </div>
    <div class="form-group col-md-3">
        <label for="price">list_price:</label>
        <input type="text" class="form-control" id="list_price" name="list_price" value="<?=((isset($_POST['list_price']))?sanitize($_POST['list_price']):'');?>">
    </div>
    <div class="form-group col-md-3">
        <label>Quantity & Sizes*:</label>
        <button type="button"  class="btn btn-default form-control" data-toggle="modal" data-target="#sizesModal" click="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>﻿
    </div>
    <div class="form-group col-md-3">
        <label for="sizes">Sizes & Qty Priveiw</label>
        <input type="text" class="form-control" name="sizes" id="sizes" value="<?=((isset($_POST['sizes']))?$_POST['sizes']:'');?>" readonly>
    </div>
    <div class="form-group col-md-6">
        <label for="photo">Product Photo</label>
        <input type="file" name="photo" id="photo" class="form-control" />
    </div>
    <div class="form-group col-md-6">
        <label for="description">Description:</label>
        <textarea id="description" name="description" class="form-control" rows="6"><?=((isset($_POST['description']))?sanitize($_POST['description']):'');?></textarea>
<div class="form-group pull-right">

        <a href="products.php" class="btn btn-default">cancel</a>
        <input type="submit" value="<?=((isset($_GET['edit']))?'Edit':'Add A new'); ?>" class=" btn btn-success pull-right" />
    </div><div class="clearfix">
    </div>
     </div>
</form>
<!-- Modal -->
<div class="modal fade " id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="sizesModal">Size & Quantity</h4>
      </div>
      <div class="modal-body">
          <div class="container-fluid">
        <?php for($i=1;$i <= 12;$i++): ?>
            <div class="form-group col-md-4">
                <label for="size<?=$i;?>">Size:</label>
                <input type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=((!empty($sarray[$i-1]))?$sarray[$i-1]:'');?>" class="form-control">
            </div>
            <div class="form-group col-md-2">
                <label for="qty<?=$i;?>">Quantity:</label>
                <input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>" value="<?=((!empty($qarray[$i-1]))?$qarray[$i-1]:'');?>" min="0" class="form-control">
            </div>

    <?php endfor; ?>
    </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="updatesizes();jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
      </div>
    </div>
  </div>
</div>

<!-- modal -->
<?php } else{
$sql = "SELECT * FROM products WHERE deleted = 0";
$presults = $db->query($sql);
if (isset($_GET['featured'])){
    $id = (int)$_GET['id'];
    $featured = (int)$_GET['featured'];
    $featuredsql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
    $db->query($featuredsql);
    header('Location: products.php');
}
 ?>
<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn" >Add product</a><div class="clearfix">
</div>
<hr />
<table class="table table-bordered table-condensed table-striped">
    <thead><th></th><th>Product</th><th>Price</th><th>Category</th><th>Featured</th><th>Sold</th></thead>
    <tbody>
        <?php while($product = mysqli_fetch_assoc($presults)):
                $childID = $product['categories'];
                $catsql = "SELECT * FROM categories WHERE id = '$childID'";
                $result = $db->query($catsql);
                $child = mysqli_fetch_assoc($result);
                $parentid = $child['parent'];
                $psql = "SELECT * FROM categories WHERE id = '$parentid'";
                $presult = $db->query($psql);
                $parent = mysqli_fetch_assoc($presult);
                $category = $parent['category'].'~'.$child['category'];
            ?>
            <tr>
                <td>
                    <a href="products.php?add=<?php echo $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="products.php?delete=<?php echo $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
                </td>
                <td><?php echo $product ['title']; ?> </td>
                <td><?php echo money($product['price']);?></td>
                <td><?= $category;?></td>
                <td><a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?php echo $product['id'];?>" class="btn btn-xs btn-default">
                   <span class="glyphicon glyphicon-<?php echo(($product['featured']==1)?'minus':'plus');?>"></span>
                </a>&nbsp <?php echo (($product['featured'] == 1)?'Featured product':''); ?></td>
                <td>0</td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php } include 'includes/footer.php';
