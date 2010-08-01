<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ko" class="<?= $this->controller ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?= h($this->title) ?></title>
<link rel="stylesheet" type="text/css" media="screen" href="<?= u(css, master) ?>.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?= u(css, $this->controller, $this->action) ?>.css">
<?= $this->meta() ?>
</head>
<body class="<?= $this->action ?>">
<?= $_ ?>
</body>
</html>
