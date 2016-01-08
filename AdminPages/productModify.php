<?php
if (!isset($_SESSION)) {
    session_start();
}


$fail = false;

if (!isset($_GET["token"]) || $_SESSION["IsLogin"] == 0) {
    $fail = true;
} else {
    require_once '../helper/crypter.php';
    $crypter = new Crypter("nhatanh");
    $decrypted = $crypter->Decrypt(str_replace(" ", "+", $_GET["token"]));
    //$data = explode("/", $decrypted);
    if (!isset($_SESSION["token"]) || $_SESSION["token"] != $decrypted) {
        $fail = true;
    } else {
        $fail = false;
    }
}
if ($fail) {
    require_once '../helper/Utils.php';
    $url = "adminLogin.php";
    Utils::Redirect($url);

} else {
    require_once '../helper/Page.php';
    require_once '../entities/Product.php';
    require_once '../entities/Category.php';
    require_once '../helper/File.php';
    require_once '../helper/Controls.php';

    $page = new Page();

    $page->addCSS("assets/js/summernote/summernote.css");
    $page->addCSS("assets/css/bootstrap-datepicker.min.css");
    $page->addCSS("assets/js/sweetalert/sweetalert.css");

    $page->addJavascript("assets/js/sweetalert/sweetalert.min.js");
    $page->addJavascript("assets/js/jquery.validate.min.js");
    $page->addJavascript("assets/js/summernote/summernote.min.js");
    $page->addJavascript("assets/js/bootstrap-datepicker.min.js");
    $page->addJavascript("assets/js/jquery.inputmask.bundle.min.js");
    $page->addJavascript("assets/js/fileinput.js");

    $page->startBody();
    function addImageURL(Product $Product)
    {
        if (isset($_FILES['fileImageURL']) && $_FILES['fileImageURL']['size'] > 0) {
            $errors = array();
            $fileName = $_FILES['fileImageURL']['name'];
            $tmpName = $_FILES['fileImageURL']['tmp_name'];
            $fileSize = $_FILES['fileImageURL']['size'];
            $fileType = $_FILES['fileImageURL']['type'];
            $File = new File($fileName, $tmpName, $fileSize, $fileType);
            if ($fileSize > 2097152) {
                $errors[] = 'File phải nhỏ hơn 2 MB';
            }
            if (!$File->isImageType()) {
                $errors[] = "";
            }
            if (empty($errors) == true) {
                $logo_Old = trim($Product->getImageURL(), '"');
                if (file_exists($logo_Old)) {
                    unlink($logo_Old);
                }
                $path = '../assets/images/productImages/' . $Product->getProID();
                if (!file_exists($path)) {// neu k ton tai duong dan thu muc cua id nay thi tạo mới
                    File::createDirectory($path);
                }
                $type = explode("/", $File->getFileType())[1];
                $find = array(" ", "\\", "/", ":", "*", "?", "\"", "<", ">", "|");
                $name = File::utf8convert(str_replace($find, '', $Product->getProName()));
                $pathNew = $path . '/' . $name . "_main." . $type;
                $File->moveFile($pathNew);
                $Product->setImageURL($pathNew);
                $Product->updateImageURL();
            } else {
                // print_r($errors);
            }
            if (empty($error)) {
                // echo "Success";
            }
        }
    }

    function addListImages(Product $Product)
    {
        $errors = array();
        $listImage_remove = array();

        $i = 1;
        $path = '../assets/images/productImages/' . $Product->getProID();
        if (isset($_POST["txtListImage_remove"]) && !empty($_POST["txtListImage_remove"])) {
            $listImage_remove = explode(',', $_POST["txtListImage_remove"]);
            foreach ($listImage_remove as $key => $fileName_remove) {
                $path_deleteFile = $path . '/' . $fileName_remove;
                if (file_exists($path_deleteFile)) {
                    unlink($path_deleteFile);
                    array_splice($listImage_remove, $key, 1);
                }
            }
        }
        if (isset($_FILES['listImageFiles']) && $_FILES['listImageFiles']['size'] > 0) {
            foreach ($_FILES['listImageFiles']['tmp_name'] as $key => $tmp_name) {
                $insertImage = true;
                $fileName = $_FILES['listImageFiles']['name'][$key];
                $fileSize = $_FILES['listImageFiles']['size'][$key];
                $tmpName = $_FILES['listImageFiles']['tmp_name'][$key];
                $fileType = $_FILES['listImageFiles']['type'][$key];
                foreach ($listImage_remove as $fileName_remove) {
                    if ($fileName == $fileName_remove) {
                        $insertImage = false;
                        break;
                    }
                }
                if ($insertImage) {
                    $File = new File($fileName, $tmpName, $fileSize, $fileType);
                    if ($fileSize > 2097152) {
                        $errors[] = 'File phải nhỏ hơn 2 MB';
                    }
                    if (!$File->isImageType()) {
                        $errors[] = ".";
                    }
                    if (empty($errors) == true) {

                        if (!file_exists($path)) {
                            File::createDirectory($path);
                        }
                        $type = explode("/", $File->getFileType())[1];
                        $find = array(" ", "\\", "/", ":", "*", "?", "\"", "<", ">", "|");
                        $name = File::utf8convert(str_replace($find, '', $Product->getProName()));
                        $name =
                        $pathNew = $path . '/' . $name . '_' . ($i++);
                        foreach (glob("{$path}/*") as $file) {
                            $file = substr($file, 0, strrpos($file, '.'));
                            if ($file == $pathNew) {
                                $pathNew = $path . '/' . $name . '_' . ($i++);
                            }
                        }
                        $pathNew .= '.' . $type;
                        $File->moveFile($pathNew);
                    } else {
                        //print_r($errors);
                    }
                }
            }
            if (empty($error)) {

            }

        }
    }

    $insert = null;
    $update = null;
    $delete = null;

    $control = new Controls(Controls::Insert);
    $ListCategories = Category::loadAll();
    $Product = new Product();

    if (isset($_POST["btnSave"])) {
        if (isset($_POST["txtControl"])) {
            $control->setValue($_POST["txtControl"]);
        }
        if (isset($_POST["txtProID"])) {
            $Product->setProID($_POST["txtProID"]);
        }
        if (isset($_POST["txtProName"])) {
            $Product->setProName($_POST["txtProName"]);
        }
        if (isset($_POST["txtImageURL_Old"])) {
            $Product->setImageURL($_POST["txtImageURL_Old"]);
        }
        if (isset($_POST["txtProPrice"])) {
            $fProPrice = (str_replace(',', '', $_POST["txtProPrice"]));
            $Product->setPrice($fProPrice);
        }
        if (isset($_POST["txtProQuantity"])) {
            $fProQuantity = (str_replace(',', '', $_POST["txtProQuantity"]));
            $Product->setInStock($fProQuantity);
        }
        if (isset($_POST["txtProCreated"])) {
            $dProCreate = strtotime(str_replace('/', '-', $_POST["txtProCreated"])); //d-m-Y
            $Product->setProCreated($dProCreate);
        }
        if (isset($_POST["cboCatPro"])) {
            $oCatPro = new Category($_POST["cboCatPro"]);
            $Product->setCatPro($oCatPro);
        }
        if (isset($_POST["txtTinyDes"])) {
            $Product->setTinyDes($_POST["txtTinyDes"]);
        }
        if (isset($_POST["txtFullDes"])) {
            $Product->setFullDes($_POST["txtFullDes"]);
        }
        if ($control == Controls::Insert) {
            $Product->insert();
            addImageURL($Product);
            addListImages($Product);
            $insert = true;

        } else if ($control == Controls::Update) {
            if (isset($_POST["chkStatus"])) {
                $Product->setStatus($_POST["chkStatus"]);
            } else {
                $Product->setStatus(0);
            }
            $Product->update();
            addImageURL($Product);
            addListImages($Product);
            $update = true;

        }
        $Product = new Product();
    } else if (isset($_GET["ProID"]) && isset($_GET["control"])) {
        $control->setValue($_GET["control"]);

        $Product = Product::loadProductByProID($_GET["ProID"]);
        if ($Product != null) {
            if ($control == Controls::Update) {

            } else if ($control == Controls::Delete) {
                $Product->delete();
                $delete = true;
                $path = '../assets/images/productImages/' . $Product->getProID();
                if (file_exists($path)) {
                    File::removeDirectoryAllFiles($path);
                }
                $Product = new Product();
            }
        } else {
            require_once '../helper/Utils.php';
            $url = "products.php";
            Utils::Redirect($url);
        }
    }




    ?>
    <form id="frmProductModify"  name="frmProductModify" enctype="multipart/form-data" class="form-horizontal"
          action="productModify.php?token=<?php echo $_GET["token"];?>"
          method="post">
        <div class="page-header">
            <div class="pull-right">
                <a class="btn btn-default pull-right" data-toggle="tooltip" name="btnCancel" href="products.php?token=<?php echo $_GET["token"] ?>"
                        data-placement="top" title="Hủy">
                    <strong><i class="entypo-reply"></i></strong>
                </a>
                <button type="submit" class="btn btn-primary pull-right" data-toggle="tooltip" name="btnSave"
                        id="btnSave"
                        data-placement="top" title="Lưu">
                    <strong><i class="entypo-floppy"></i></strong>
                </button>
            </div>
            <h1>Product</h1>
            <ol class="breadcrumb bc-3">
                <li>
                    <a href="index.php">Trang Chủ</a>
                </li>
                <li class="active">
                    <strong>Sản Phẩm</strong>
                </li>
            </ol>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <span><i class="entypo-list"></i>Thông Tin Sản Phẩm </span>
            </div>
            <div class="panel-body">

                <ul class="nav nav-tabs bordered"><!-- available classes "bordered", "right-aligned" -->
                    <li class="active">
                        <a href="#infors" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-info"></i></span>
                            <span class="hidden-xs">Thông tin chung</span>
                        </a>
                    </li>
                    <li>
                        <a href="#datas" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-picture"></i></span>
                            <span class="hidden-xs">Dữ liệu</span>
                        </a>
                    </li>
                    <li>
                        <a href="#images" data-toggle="tab">
                            <span class="visible-xs"><i class="entypo-picture"></i></span>
                            <span class="hidden-xs">Ảnh</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!--Thông tin chung-->
                    <div class="tab-pane active" id="infors">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="txtControl" id="txtControl"
                                   value=<?php echo $control ?>>

                            <div class="col-md-7">
                                <input type="hidden" class="form-control" name="txtProID" id="txtProID"
                                       value=<?php if ($control == Controls::Update) echo $Product->getProID(); ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="proName">
                                <label class="control-label col-md-2" for="txtProName">Sản phẩm:</label>

                                <div class="col-md-8">
                                    <input class="form-control" type="text" id="txtProName" name="txtProName"
                                           value="<?php if ($control == Controls::Update) echo $Product->getProName(); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="proTinyDes">
                                <label class="control-label col-md-2" for="txtTinyDes">Mô Tả:</label>

                                <div class="col-md-8">
                                    <input class="form-control" type="text" id="txtTinyDes" name="txtTinyDes"
                                           maxlength="65"  value="<?php if ($control == Controls::Update) echo $Product->getTinyDes(); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="proFullDes">
                                <label class="control-label col-md-2">Mô Tả Chi Tiết:</label>

                                <div class="col-md-8">
                                    <div id="editorFullDes"></div>
                                    <textarea class="input-block-level" id="txtFullDes" name="txtFullDes"
                                              hidden></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/Thông tin chung-->

                    <!--Dữ liệu-->
                    <div class="tab-pane" id="datas">
                        <div class="form-group">
                            <div class="proImageURL" id="proImageURL">
                                <label class="control-label col-md-2" for="fileImageURL">Ảnh:</label>

                                <div class="col-md-4 fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 128px;"
                                         data-trigger="fileinput">
                                        <img class="logo-brand" src="http://placehold.it/128x128" alt="...">
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail"
                                         style="max-width: 128px; ">
                                        <?php
                                        echo '<img class="logo-brand" src="' . trim($Product->getImageURL(), '"') . '" alt="' . $Product->getProName() . '">';
                                        ?>
                                    </div>
                                    <div class="btn-modify-group">
                                        <span class="btn btn-default btn-file">
                                            <span class="fileinput-new" data-placement="top" title="Chọn ảnh"
                                                  data-toggle="tooltip"><i class="entypo-upload"></i></span>
                                            <span class="fileinput-exists" data-placement="top" title="Thay đổi"
                                                  data-toggle="tooltip"><i class="entypo-pencil"></i></span>
                                            <input type="file" accept="image/*" name="fileImageURL" id="fileImageURL">
                                        </span>
                                        <a href="#" class="btn btn-delete fileinput-exists" data-dismiss="fileinput"
                                           data-placement="top" title="Xóa" data-toggle="tooltip">
                                            <i class="entypo-cancel"></i>
                                        </a>
                                    </div>
                                    <input type="hidden" name="txtImageURL_Old" id="txtImageURL_Old"
                                           value='"<?php echo $Product->getImageURL(); ?>"'>
                                    <input type="hidden" name="txtImageURL" id="txtImageURL">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="proPrice">
                                <label class="control-label col-md-2" for="txtProPrice">Đơn giá:</label>

                                <div class="col-md-8">
                                    <input class="form-control number" type="text" id="txtProPrice"
                                           name="txtProPrice"
                                           data-mask="fdecimal" data-dec="," maxlength="10"
                                           value="<?php if ($control == Controls::Update) {
                                               echo number_format($Product->getPrice(), 0);
                                           } ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="proQuantity">
                                <label class="control-label col-md-2" for="txtProQuantity">Số Lượng:</label>

                                <div class="col-md-8">
                                    <input class="form-control number" type="text" id="txtProQuantity"
                                           name="txtProQuantity"
                                           data-mask="fdecimal" data-dec="," maxlength="7"
                                           value="<?php if ($control == Controls::Update) {
                                               echo number_format($Product->getInStock(), 0);
                                           } ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="proCreated">
                                <label class="control-label col-md-2" for="txtProCreated">Ngày Tạo:</label>

                                <div class="col-md-8 input-group" id="datepicker">
                                    <input class="form-control datepicker date" type="text" id="txtProCreated"
                                           name="txtProCreated" data-mask="date"
                                           value="<?php if ($control == Controls::Update) echo date_format($Product->getProCreated(), 'd/m/Y'); ?>">

                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="proQuantity">
                                <label class="control-label col-md-2" for="cboCatPro">Loại Sản Phẩm:</label>

                                <div class="col-md-8">
                                    <select class="form-control" name="cboCatPro" id="cboCatPro">
                                        <option></option>
                                        <?php
                                        foreach ($ListCategories as $ItemCat) {
                                            $option = "<option ";
                                            if ($control == Controls::Update && $ItemCat->getCatID() == $Product->getCatPro()->getCatID()) {
                                                $option .= "selected ";
                                            }
                                            $option .= "value= " . $ItemCat->getCatID();
                                            $option .= " >";
                                            $option .= $ItemCat->getCatName();
                                            $option .= " </option> ";
                                            echo $option;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!--/Dữ liệu-->

                    <!--Ảnh-->
                    <div class="tab-pane" id="images">
                        <p class="text-right">
                            <span class="btn btn-default btn-file">
                                <span><i class="entypo-upload"></i></span>
                                <input type="file" id="listImageFiles" name="listImageFiles[]" accept="image/*"
                                       multiple/>
                            </span>
                            <input type="hidden" id="txtListImage_remove" name="txtListImage_remove">
                        </p>

                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Ảnh</th>
                                <!--<th>Số Thứ Tự</th> -->
                                <th class="action"></th>
                            </tr>
                            </thead>
                            <tbody id="listImages">
                            <?php
                            if ($control == Controls::Update) {
                                $directory = '../assets/images/productImages/' . $Product->getProID();
                                foreach (glob("{$directory}/*") as $file) {
                                    if ($Product->getImageURL() == $file) {
                                        continue;
                                    }
                                    $title = explode('/', $file);
                                    $title = array_pop($title);
                                    ?>
                                    <tr>
                                        <td><img class="thumbnail" style="height:auto; width: 200px;"
                                                 src="<?php echo $file ?>"
                                                 title="<?php echo $title ?>"></td>
                                        <td class="btn-modify-group text-center">
                                            <button onclick="removeImage(this);" type="button" class="btn btn-delete"
                                                    data-toggle="tooltip" data-placement="top" name="btnDelete"
                                                    title="Xóa">
                                                <i class="entypo-cancel"></i></button>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                    <!--/Ảnh-->
                </div>
            </div>
        </div>

    </form>
    <script>

        //Begin Image
        var listImages_Remove = [];// mang chua danh sach anh bi xoa
        function handleFileSelect(evt) {
            var files = evt.target.files; // FileList object

            for (var i = 0, f; f = files[i]; i++) {

                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }

                var reader = new FileReader();

                reader.onload = (function (theFile) {
                    return function (e) {
                        // Render thumbnail.
                        var tr = document.createElement('tr');
                        var td = [];
                        for (var i = 0; i < 2; i++) {
                            td[i] = document.createElement('td');
                        }
                        var span = document.createElement('span');

                        td[0].innerHTML = ['<img class="thumbnail" style="height:auto; width: 200px;" src="', e.target.result,
                            '" title="', escape(theFile.name), '" />'].join('');
                        //td[1].innerHTML = ['<input type="text" class="form-control" name="" >'].join('');
                        td[1].innerHTML = ['<button onclick="removeImage(this);" type="button" class="btn btn-delete" data-toggle="tooltip" data-placement="top" name="btnDelete" title="Xóa"><i class="entypo-cancel"></i></button>'].join('');
                        td[1].setAttribute("class", "btn-modify-group text-center");

                        for (var i = 0; i < 2; i++) {
                            tr.appendChild(td[i]);
                        }
                        document.getElementById('listImages').insertBefore(tr, null);
                    };
                })(f);

                // Read in the image file as a data URL.
                reader.readAsDataURL(f);
            }
        }

        function removeImage(element) {
            var td = element.parentNode;
            var tr = td.parentNode;
            var imageName = tr.getElementsByTagName('img')[0].title;
            listImages_Remove.push(imageName);
            tr.remove();
        }
        document.getElementById('listImageFiles').addEventListener('change', handleFileSelect, false);

        function removeImageFromListRemove(fileName){// loai bo phan tu cua mang bi xoa neu up lai anh tuong tu
            var pos =  listImages_Remove.indexOf(fileName);
            if (~pos) {
                listImages_Remove.slice(pos, 1);
            }
        }

        //End  Image

        $(document).ready(function () {
            //Begin Editor
            var summernote = $('#editorFullDes').summernote({
                height: 100,                 // set editor height
                minHeight: null,             // set minimum height of editor
                maxHeight: null,             // set maximum height of editor
                focus: true,                 // set focus to editable area after initializing summernote
                onChange: function () {
                    var content = $('#editorFullDes').code();
                    if (content === '<p><br></p>' || content === '<br>') {
                        content = '';
                    }
                    $('textarea[name="txtFullDes"]').html(content);
                    validator.element("#txtFullDes");
                }
            });
            $(window).load(function () {
                var control = parseInt("<?php echo $control; ?>");
                var controlUpdate = parseInt("<?php echo Controls::Update; ?>");
                var imageURL = "<?php echo trim($Product->getImageURL(), "\"");?>";
                if (control === controlUpdate) {
                    if (imageURL.trim().length > 0) {
                        var fileInput = $('#proImageURL .fileinput');
                        if (fileInput.hasClass("fileinput-new")) {
                            fileInput.removeClass("fileinput-new");
                            fileInput.addClass("fileinput-exists");
                        }
                        $('#txtImageURL').val(imageURL);
                    }
                    var fullDes = '<?php echo $Product->getFullDes();?>';
                    $('#editorFullDes').code(fullDes);
                    $('textarea[name="txtFullDes"]').html(fullDes);
                }
            });
            //kiem tra du lieu
            $.validator.addMethod(
                "regexp",
                function (value, element, regexp) {
                    var re = new RegExp(regexp);
                    return this.optional(element) || re.test(value.trim());
                },
                "Please check your input."
            );
            jQuery.validator.addMethod(
                "date",
                function (value, element) {
                    var check = false;
                    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                    if (re.test(value)) {
                        var adata = value.split('/');
                        var dd = parseInt(adata[0], 10);
                        var mm = parseInt(adata[1], 10);
                        var yyyy = parseInt(adata[2], 10);
                        var xdata = new Date(yyyy, mm - 1, dd);
                        var today = new Date();
                        if (( xdata.getFullYear() == yyyy ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == dd ))
                            check = true;
                        else
                            check = false;
                    } else
                        check = false;
                    return this.optional(element) || check;
                },
                "Vui lòng nhập ngày có dạng dd/mm/yyyy"
            );
            var validator = $("#frmProductModify").validate({
                ignore: [],
                rules: {
                    txtProName: "required",
                    txtProPrice: "required",
                    txtProQuantity: "required",
                    txtProCreated: {
                        required: true,
                        date : true
                    },
                    cboCatPro: "required",
                    txtTinyDes: "required",
                    txtFullDes: "required",
                    txtImageURL: {
                        required: true,
                        regexp: /^.+\.(jpg|JPG|png|PNG|jpeg|JPEG)$/
                    }
                },
                messages: {
                    txtProName: {
                        required: "Vui lòng nhập Sản Phẩm  !"
                    },
                    txtProPrice: {
                        required: "Vui lòng nhập Giá !"
                    },
                    txtProQuantity: {
                        required: "Vui lòng nhập Số Lượng !"
                    },
                    txtProCreated: {
                        required: "Vui lòng nhập Ngày Tạo  !",
                        date : "Ngày không hợp lệ !"
                    },
                    cboCatPro: {
                        required: "Vui lòng chọn Loại Sản Phẩm  !"
                    },
                    txtTinyDes: {
                        required: "Vui lòng nhập Mô Tả !"
                    },
                    txtFullDes: {
                        required: "Vui lòng nhập Mô Tả Chi Tiết !"
                    },
                    txtImageURL: {
                        required: "Vui lòng Chọn Ảnh !",
                        regexp: "Vui lòng chọn Ảnh có định dạng .jpg , .png hoặc .jpeg  !"
                    }
                }
            });
            $(".fileinput").on("change.bs.fileinput", function () {
                var e = $("#txtImageURL");
                var filename = $('#fileImageURL').val().split('\\').pop();
                e.val(filename);
                $('#txtListImage_remove').val(listImages_Remove);
                if ($("#proImageURL .fileinput").hasClass("fileinput-new")) {
                    e.val("");
                }
                validator.element("#txtImageURL");
            });
            //end kiem tra du lieu

            $('#datepicker>input').datepicker({
                format: "dd/mm/yyyy",
                endDate: "0d"
            });


            $('#frmProductModify').submit(function () {
                $('#txtListImage_remove').val(listImages_Remove);
            });
            //End Editor

            var _insert = "<?php echo $insert == null ? "" : $insert ; ?>";
            var _update = "<?php echo $update == null ? "" : $update ; ?>";
            var _delete = "<?php echo $delete == null ? "" : $delete ; ?>";
            if (_insert || _update || _delete) {
                swal({
                        title: "Thành công!",
                        type: "success",
                        timer: 1000,
                        showConfirmButton: false
                    },
                    function () {
                        var url = "products.php?token=" + "<?php echo $_GET["token"] ?>" ;
                        window.location.href = url;
                    }
                );
            }
        });


    </script>
    <?php
    $page->endBody();
    echo $page->render('./Teamplates/Template.php');
}