<?php
require_once __DIR__.'/db.php';
require_once __DIR__.'/funcs.php';
session_start();
$messages = [];

if(!empty($_POST['mySelect'])){
    switch($_POST['mySelect']){
        case '0':
            $messages = get_messages();
            break;
        case '1':
            $messages = get_messages_by_email();
            break;
        case '2':
            $messages = get_messages_asc();
            break;
        case '3':
            $messages = get_messages_by_name();
            break;
    }
}else{
    $messages = get_messages();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if(!empty($_SESSION['user']['name']) && $_SESSION['user']['id']==1): ?>
    <title>Admin page</title>
    </head>
    <body>
    <div class="container">
    <div class="row my-3">
                <div class="col">
                	<?php if(!empty($_SESSION['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                            echo $_SESSION['errors'];
                            unset($_SESSION['errors']);
                         ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if(!empty($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                         ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                </div>
            </div>
            <a href="index.php" class="btn btn-primary mt-3">Main Page</a>
        <form action="admin.php" method="POST" >
            <div class="input-group mb-3">
                <select class="form-select mt-5" aria-label="Default select example" name="mySelect">
                    <option hidden="hidden">Сортировка</option>
                    <option value="0" <?php if(!empty($_POST['mySelect']) && $_POST['mySelect']=='0'){echo "selected";}?>>По умолчанию</option>
                    <option value="1" <?php if(!empty($_POST['mySelect']) && $_POST['mySelect']=='1'){echo "selected";}?>>По email</option>
                    <option value="2" <?php if(!empty($_POST['mySelect']) && $_POST['mySelect']=='2'){echo "selected";}?>>По дате создания</option>
                    <option value="3" <?php if(!empty($_POST['mySelect']) && $_POST['mySelect']=='3'){echo "selected";}?>>По именам пользователей</option>
                </select>
                <button type="submit" class="btn btn-primary mt-5">Сортировать</button>
            </div>
        </form>
          <table class="table table-striped mt-3">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Имя</th>
            <th scope="col">Email</th>
            <th scope="col">Отзыв</th>
            <th scope="col">Изображение</th>
            <th scope="col">Создан</th>
            <th scope="col">Статус</th>
            <th scope="col">Изменен администратором?</th>
            <th scope="col">Изменить</th>
          </tr>
        </thead>
        <tbody>
            <?php for($i=0;$i<count($messages); $i++){ ?>
          <tr>
            <th scope="row"><?= $i+1; ?></th>
            <td><?= htmlspecialchars($messages[$i]['name']); ?></td>
            <td><?= htmlspecialchars($messages[$i]['email']); ?></td>
            <td><?= nl2br(htmlspecialchars($messages[$i]['message'])); ?></td>
            <td><?php if(!empty($messages[$i]['image'])): ?><img src="images/<?php echo $messages[$i]['image']; ?>" alt="" width="100px"><?php endif; ?></td>
            <td><?= htmlspecialchars($messages[$i]['created_at']); ?></td>
            <td><input type="checkbox" disabled name="status" value="<?= $messages[$i]['status'] ?>" <?php if($messages[$i]['status']=='on'): ?>checked<?php endif; ?>></td>
            <td><?= htmlspecialchars($messages[$i]['admin_changed']); ?></td>
            <th scope="col"><a href="details.php?id=<?php echo $messages[$i]['id'];?>" type="button" class="btn btn-success">Изменить</a></th>
          </tr>
          <?php }?>
        </tbody>
</table>
    </div>
    
</body>
<?php else: ?>
    <h1>403 page</h1>
<?php endif; ?>
</html>