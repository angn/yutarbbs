<?php
if (requirelogin())
    return;

$users = fetchall('year, name, email, phone, remark FROM users ORDER BY year DESC, name');
?>
<h2>회원명부</h2>

<table class=users>
<thead>
<tr>
<td>학번
<td>이름
<td>전화번호 · 이메일
<td>남김말
<tbody>

<?php foreach ($users as $i => $u): ?>
<tr class="<?= $i & 1 ? 'a' : '' ?>">
<td><?= $u->year ?>
<td><?= h($u->name) ?>
<td><?= h(formatphone($u->phone)) ?><br>
    <a class=email href="mailto:<?= h($u->email) ?>"><?= h($u->email) ?></a>
<td><?= formattext($u->remark) ?></td>
<?php endforeach ?>

</table>
