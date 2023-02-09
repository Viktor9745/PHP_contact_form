<?php
error_reporting(-1);
session_start();
require_once __DIR__.'/db.php';
require_once __DIR__.'/funcs.php';
if(isset($_POST['save'])){
    if(isset($_POST['id']) && !empty($_POST['message']) &&isset($_POST['status'])){
        $message = get_message($_POST['id']);
        if($message!=null){
            save_message($_POST['id'], $_POST['message'], $_POST['status']);
        }
    }else{
        $_SESSION['errors'] = 'Ошибка изменения!';
    }
    header("Location:admin.php");
    die;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Изменить отзыв</title>
</head>
<body>
<div class="container" style="min-height: 600px;">
    <div class="row mt-3">
        <div class="col-6 mx-auto">
        <?php
            if(isset($_GET['id'])&& is_numeric($_GET['id'])){
            $message=get_message($_GET['id']);
            if($message!=null){
        ?>
        <form action="details.php" method="post">
                            <input type="hidden" name="id" value="<?=$message['id']?>">
        <div class="form-floating">
                <textarea class="form-control" name="message"
                          id="floatingTextarea" style="height: 100px;"><?= $message['message'] ?></textarea>
                <label for="floatingTextarea">Отзыв</label>
            </div>
            <label class="form-check-label">
            Статус
            </label>
            <select class="form-select mt-2" aria-label="Default select example" name="status">
                    <option hidden="hidden" >Статус</option>
                    <option value="on" <?php if($message['status']=='on'){echo "selected";} ?>>принят</option>
                    <option value="off" <?php if($message['status']=='off'){echo "selected";} ?>>отклонен</option>
            </select>
        </div>
        </div>
        <div class="col-md-6 offset-md-3 mt-2">
                <button type="submit" class="btn btn-success" name="save">Сохранить </button>
                <a href="admin.php" type="button" class="btn btn-secondary">Отмена</a>
        </div>
            </form>
        <?php
            }
            }
        ?>
    </div>
</div>
</body>
</html>