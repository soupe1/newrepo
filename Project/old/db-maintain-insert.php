<!DOCTYPE html>
<html>
    <head>
        <title>Database Maintain - Insert - OSP Electronics</title>
    </head>

    <?php include 'common_files/head-header.php'; include 'php/db-accessor.php';?>

    <body>
        <main>
            <div class="container-fluid">
                <h1>Insert</h1>
                <div class="row">
                    <div class="col-lg-2 col-md-4 offset-lg-5 offset-md-4" id="form-container">
                        <form id="insert-form" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="table-select">Table</label>
                                <select class="form-select" name="table-select" id="table-select">
                                    <option selected disabled>Select a Table</option>
                                    <option value="item/insert">Item</option>
                                    <option value="orders/insert">Orders</option>
                                    <option value="shopping/insert">Shopping</option>
                                    <option value="trip/insert">Trip</option>
                                    <option value="truck/insert">Truck</option>
                                    <option value="user/insert">User</option>
                                </select>
                                <div id="item-form">
                                    <label for="item_name">Item Name</label>
                                    <input type="text" class="form-control" name="item_name" id="item_name" placeholder="Item Name" pattern="[A-z0-9]{1,255}" required>
                                    <label for="price">Price</label>
                                    <input type="text" class="form-control" name="price" id="price" placeholder="Price" pattern="[0-9]*(\.[0-9][0-9]?)?" required>
                                    <label for="item_name">Made In</label>
                                    <input type="text" class="form-control" name="made_in" id="made_in" placeholder="Made In" pattern="[A-z0-9]{1,255}" required>
                                    <label for="image">Image</label>
                                    <input type="file" class="form-control-file" name="image" id="image" placeholder="Image" accept="image/png, image/jpeg" required>
                                    <label for="image">Department Code</label>
                                    <select class="form-select" name="department_code" id="department_code" required>
                                        <option value="" selected disabled>Select a Department</option>
                                        <option value="furniture">Furniture</option>
                                        <option value="kitchen">Kitchen</option>
                                        <option value="electronics">Electronics</option>
                                        <option value="appliances">Appliances</option>
                                        <option value="beauty">Beauty</option>
                                        <option value="clothes">Clothes</option>
                                        <option value="shoes">Shoes</option>
                                        <option value="food">Food</option>
                                        <option value="art">Art</option>
                                        <option value="decorations">Decorations</option>
                                    </select>
                                    <button type="submit" name="form-submit" class="btn btn-primary">Insert</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-md-4 offset-lg-5 offset-md-4 alert alert-success" id="form-success">Submission successful!</div>
                    <div class="col-lg-2 col-md-4 offset-lg-5 offset-md-4 alert alert-danger" id="form-fail">There was an issue submitting the data. Please try again.</div>
                </div>
            </div>
        </main>
    </body>
</html>