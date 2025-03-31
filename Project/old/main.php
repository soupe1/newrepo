<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Home - OSP Electronics</title>
    </head>
    
    <?php include 'common_files/head-header.php'; include_once 'db/database-setup.php';?>
    
    <body>        
        <main>
            <h1 id="dept-header">Electronics</h1>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-2 col-md-4 offset-lg-5 offset-md-4">
                        <select class="form-select" id="dept-select">
                            <option value="furniture">Furniture</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="electronics" selected>Electronics</option>
                            <option value="appliances">Appliances</option>
                            <option value="beauty">Beauty</option>
                            <option value="clothes">Clothes</option>
                            <option value="shoes">Shoes</option>
                            <option value="food">Food</option>
                            <option value="art">Art</option>
                            <option value="decorations">Decorations</option>
                        </select>
                    </div>
                </div>
                <div class="row" id="display-parent"></div>
            </div>
        </main>
    </body>
</html>